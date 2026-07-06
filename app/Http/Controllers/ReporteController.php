<?php
// app/Http/Controllers/ReporteController.php
namespace App\Http\Controllers;

use App\Models\RegistroActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Aws\Polly\PollyClient;
use App\Exports\ReporteExport;
use App\Models\Propiedad;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $filtroAccion = $request->query('accion','todas');
        $filtroRol    = $request->query('rol','todos');
        $filtroFecha  = $request->query('fecha','');

        $query = RegistroActividad::query()->orderBy('id','desc');

        if ($filtroAccion !== 'todas') $query->where('accion', $filtroAccion);
        if ($filtroRol    !== 'todos') $query->where('rol', $filtroRol);
        if ($filtroFecha)              $query->whereRaw("DATE(fecha_hora) = ?", [$filtroFecha]);

        $actividad = $query->limit(200)->get();
        $acciones  = RegistroActividad::select('accion')->distinct()->pluck('accion');

        $totalLogins   = RegistroActividad::where('accion','Inicio de sesión')->count();
        $totalFallidos = RegistroActividad::where('accion','Intento de sesión fallido')->count();
        $totalProps    = RegistroActividad::where('accion','Propiedad registrada')->count();
        $totalHoy      = RegistroActividad::whereRaw("DATE(fecha_hora) = CURRENT_DATE")->count();

        // Guardar/actualizar exportes (PDF y Excel) para los filtros actuales
        try {
            $this->storeReports($actividad, $filtroAccion, $filtroRol, $filtroFecha);
        } catch (\Throwable $e) {
            // No detener la vista si falla la generación de archivos; solo registrar si hay logger
            if (function_exists('logger')) logger('Error generando reportes: ' . $e->getMessage());
        }

        return view('compartido.reportes', compact(
            'actividad','acciones',
            'totalLogins','totalFallidos','totalProps','totalHoy',
            'filtroAccion','filtroRol','filtroFecha'
        ));
    }

    public function tendencias(Request $request)
    {
        $filtroTipo = $request->query('tipo', 'todos');
        $filtroZona = trim((string) $request->query('zona', ''));

        $predicciones = collect($this->buildMarketTrendRows($filtroTipo, $filtroZona));
        $zonas = Propiedad::query()
            ->whereNotNull('zona')
            ->pluck('zona')
            ->filter()
            ->map(fn ($zona) => trim((string) $zona))
            ->unique()
            ->sort()
            ->values();

        $resumen = [
            'segmentos' => $predicciones->count(),
            'alta' => $predicciones->where('tendencia', 'Alta')->count(),
            'media' => $predicciones->where('tendencia', 'Media')->count(),
            'baja' => $predicciones->where('tendencia', 'Baja')->count(),
            'probabilidad_promedio' => round($predicciones->avg('probabilidad_venta') ?? 0),
            'solicitudes' => $predicciones->sum('solicitudes'),
        ];

        return view('compartido.tendencias', compact(
            'predicciones',
            'zonas',
            'resumen',
            'filtroTipo',
            'filtroZona'
        ));
    }

    /**
     * Endpoint JSON para ser consumido por la interfaz de voz.
     * Devuelve conteos y últimos registros de "Propiedad registrada".
     */
    public function voiceReportProperties(Request $request)
    {
        $totalProps = RegistroActividad::where('accion', 'Propiedad registrada')->count();
        $totalHoy   = RegistroActividad::where('accion', 'Propiedad registrada')
            ->whereRaw("DATE(fecha_hora) = CURRENT_DATE")->count();

        $latest = RegistroActividad::where('accion', 'Propiedad registrada')
            ->orderBy('fecha_hora', 'desc')
            ->limit(5)
            ->get(['id', 'fecha_hora', 'nombre', 'descripcion']);

        return response()->json([
            'totalProps' => $totalProps,
            'totalHoy' => $totalHoy,
            'latest' => $latest,
        ]);
    }

    /**
     * Generic voice query powered by Gemini AI Studio.
     * Laravel queries the database, sends only the necessary data to Gemini,
     * and returns a natural Spanish response text.
     */
    public function voiceQuery(Request $request)
    {
        $query = trim((string) $request->input('q', ''));
        if ($query === '') {
            return response()->json(['error' => 'Missing q parameter'], 400);
        }

        $context = $this->collectVoiceContext($request);
        $localReply = $this->localVoiceFallback($query, $context, false);
        if ($localReply !== null) {
            return response()->json([
                'speech' => $localReply,
                'source' => 'local',
                'reason' => 'local_fast_match',
            ]);
        }

        $apiKey = env('GEMINI_API_KEY');
        $apiUrl = rtrim((string) env('GEMINI_API_URL', ''), '/');
        $model = trim((string) env('GEMINI_MODEL', ''));

        if (!$apiKey) {
            return response()->json(['error' => 'GEMINI_API_KEY not configured in .env'], 500);
        }
        if (!$apiUrl) {
            return response()->json(['error' => 'GEMINI_API_URL not configured in .env'], 500);
        }
        if (!$model) {
            return response()->json(['error' => 'GEMINI_MODEL not configured in .env'], 500);
        }

        $systemPrompt = "Eres un asistente de voz para un dashboard inmobiliario. Responde siempre en español y nunca contestes en otro idioma.
Usa únicamente los datos que te entrega el servidor. No inventes cifras ni supongas información adicional.
Responde con frases completas, terminadas en punto. Si no tienes información suficiente, di que no puedes responder con seguridad y termina con un punto.";

        $userPrompt = "Pregunta: {$query}\n\nDatos disponibles:\n" . $this->formatVoiceContext($context) . "\n\nResponde con un texto natural en español, usando oraciones completas y terminando siempre con un punto. No cambies de idioma.";

        $endpoint = "{$apiUrl}/{$model}:generateContent";
        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $userPrompt],
                    ],
                ],
            ],
            'systemInstruction' => [
                'role' => 'system',
                'parts' => [
                    ['text' => $systemPrompt],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.25,
                'maxOutputTokens' => 180,
            ],
        ];

        try {
            $response = Http::connectTimeout(5)
                ->timeout(12)
                ->acceptJson()
                ->asJson()
                ->post($endpoint . '?key=' . urlencode($apiKey), $payload);
        } catch (\Throwable $e) {
            if (function_exists('logger')) {
                logger('Gemini request exception', [
                    'url' => $endpoint,
                    'message' => $e->getMessage(),
                ]);
            }
            $fallbackReply = $this->localVoiceFallback($query, $context);
            if ($fallbackReply !== null) {
                return response()->json([
                    'speech' => $fallbackReply,
                    'source' => 'fallback',
                    'reason' => 'request_exception',
                    'reason_detail' => $e->getMessage(),
                ]);
            }
            return response()->json([
                'error' => 'Gemini request exception',
                'source' => 'gemini',
                'reason' => 'request_exception',
                'reason_detail' => $e->getMessage(),
            ], 502);
        }

        if (!$response->successful()) {
            $responseBody = null;
            try {
                $responseBody = $response->json();
            } catch (\Throwable $e) {
                $responseBody = $response->body();
            }

            if (function_exists('logger')) {
                logger('Gemini request error', [
                    'url' => $endpoint,
                    'status' => $response->status(),
                    'body' => $responseBody,
                ]);
            }

            $fallbackReply = $this->localVoiceFallback($query, $context);
            if ($fallbackReply !== null) {
                return response()->json([
                    'speech' => $fallbackReply,
                    'source' => 'fallback',
                    'reason' => 'gemini_http_error',
                    'reason_detail' => $responseBody,
                ]);
            }

            $statusCode = $response->status();
            if ($statusCode === 429) {
                return response()->json([
                    'error' => 'Gemini API request failed',
                    'status' => 429,
                    'body' => $responseBody,
                    'message' => 'Demasiadas solicitudes a Gemini. Revisa tu cuota o espera unos segundos.',
                    'source' => 'gemini',
                    'reason' => 'gemini_rate_limit',
                ], 429);
            }

            return response()->json([
                'error' => 'Gemini API request failed',
                'status' => $statusCode,
                'body' => $responseBody,
                'source' => 'gemini',
                'reason' => 'gemini_http_error',
            ], 502);
        }

        $replyText = $this->parseGeminiResponseText($response->json());
        $replyText = $this->ensureCompleteSentence($replyText);
        if ($replyText === '') {
            if (function_exists('logger')) {
                logger('Gemini response missing text', [
                    'url' => $endpoint,
                    'response' => $response->json(),
                ]);
            }
            $fallbackReply = $this->localVoiceFallback($query, $context);
            if ($fallbackReply !== null) {
                return response()->json([
                    'speech' => $fallbackReply,
                    'source' => 'fallback',
                    'reason' => 'gemini_parse_failure',
                    'reason_detail' => $response->json(),
                ]);
            }
            return response()->json([
                'error' => 'Gemini response contained no valid text',
                'source' => 'gemini',
                'reason' => 'gemini_parse_failure',
                'reason_detail' => $response->json(),
            ], 502);
        }

        return response()->json(['speech' => $replyText, 'source' => 'gemini', 'reason' => 'gemini_success']);
    }

    public function voicePolly(Request $request)
    {
        $text = trim((string) $request->input('text', ''));
        if ($text === '') {
            return response()->json(['error' => 'Missing text parameter'], 400);
        }

        $apiKey = env('AWS_ACCESS_KEY_ID');
        $apiSecret = env('AWS_SECRET_ACCESS_KEY');
        $region = trim((string) env('AWS_POLLY_REGION', env('AWS_DEFAULT_REGION', 'us-west-2')));
        $voice = trim((string) env('AWS_POLLY_VOICE', 'Lucia'));
        $engine = trim((string) env('AWS_POLLY_ENGINE', 'neural')) ?: 'neural';
        $cacheKey = 'voice_polly_' . sha1($region . '|' . $voice . '|' . $engine . '|' . $text);

        if (!$apiKey || !$apiSecret) {
            return response()->json(['error' => 'AWS credentials not configured'], 500);
        }

        if ($cachedAudio = Cache::get($cacheKey)) {
            return response()->json([
                'audio' => $cachedAudio,
                'voice' => $voice,
                'region' => $region,
                'cached' => true,
            ]);
        }

        try {
            $client = new PollyClient([
                'version' => 'latest',
                'region' => $region,
                'credentials' => [
                    'key' => $apiKey,
                    'secret' => $apiSecret,
                ],
            ]);

            $result = $client->synthesizeSpeech([
                'OutputFormat' => 'mp3',
                'Text' => $text,
                'VoiceId' => $voice,
                'Engine' => $engine,
                'TextType' => 'text',
            ]);

            $audioStream = $result->get('AudioStream');
            if (!$audioStream) {
                throw new \RuntimeException('No audio data returned by Polly');
            }

            $audio = $audioStream->getContents();
            $encodedAudio = base64_encode($audio);
            Cache::put($cacheKey, $encodedAudio, now()->addDay());

            return response()->json([
                'audio' => $encodedAudio,
                'voice' => $voice,
                'region' => $region,
                'cached' => false,
            ]);
        } catch (\Throwable $e) {
            if (function_exists('logger')) {
                logger('Polly synthesis failed', [
                    'message' => $e->getMessage(),
                    'text' => $text,
                    'region' => $region,
                    'voice' => $voice,
                ]);
            }
            return response()->json([
                'error' => 'Polly synthesis failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function collectVoiceContext(?Request $request = null): array
    {
        $filtroAccion = $request?->input('accion', $request?->query('accion', 'todas')) ?? 'todas';
        $filtroRol = $request?->input('rol', $request?->query('rol', 'todos')) ?? 'todos';
        $filtroFecha = $request?->input('fecha', $request?->query('fecha', '')) ?? '';

        $totalLogins = RegistroActividad::where('accion', 'Inicio de sesión')->count();
        $totalLoginsToday = RegistroActividad::where('accion', 'Inicio de sesión')
            ->whereRaw('DATE(fecha_hora) = CURRENT_DATE')->count();

        $totalFailed = RegistroActividad::where('accion', 'Intento de sesión fallido')->count();
        $totalFailedToday = RegistroActividad::where('accion', 'Intento de sesión fallido')
            ->whereRaw('DATE(fecha_hora) = CURRENT_DATE')->count();

        $totalProps = RegistroActividad::where('accion', 'Propiedad registrada')->count();
        $totalPropsToday = RegistroActividad::where('accion', 'Propiedad registrada')
            ->whereRaw('DATE(fecha_hora) = CURRENT_DATE')->count();

        $latestEvents = RegistroActividad::whereRaw('DATE(fecha_hora) = CURRENT_DATE')
            ->orderBy('fecha_hora', 'desc')
            ->limit(5)
            ->get(['fecha_hora', 'accion', 'nombre', 'descripcion'])
            ->toArray();

        $latestProps = RegistroActividad::where('accion', 'Propiedad registrada')
            ->orderBy('fecha_hora', 'desc')
            ->limit(5)
            ->get(['fecha_hora', 'nombre', 'descripcion'])
            ->toArray();
        $topTrend = $this->buildMarketTrendRows()[0] ?? null;
        $filteredQuery = RegistroActividad::query()->orderBy('id', 'desc');
        if ($filtroAccion !== 'todas') $filteredQuery->where('accion', $filtroAccion);
        if ($filtroRol !== 'todos') $filteredQuery->where('rol', $filtroRol);
        if ($filtroFecha) $filteredQuery->whereRaw("DATE(fecha_hora) = ?", [$filtroFecha]);

        $filteredTotal = (clone $filteredQuery)->count();
        $filteredLatest = (clone $filteredQuery)
            ->limit(5)
            ->get(['fecha_hora', 'accion', 'rol', 'nombre', 'descripcion'])
            ->toArray();
        $accionesResumen = RegistroActividad::selectRaw('accion, COUNT(*) as total')
            ->groupBy('accion')
            ->orderByDesc('total')
            ->limit(8)
            ->pluck('total', 'accion')
            ->toArray();
        $rolesResumen = RegistroActividad::selectRaw('rol, COUNT(*) as total')
            ->whereNotNull('rol')
            ->groupBy('rol')
            ->pluck('total', 'rol')
            ->toArray();
        $filters = [
            'accion' => $filtroAccion,
            'rol' => $filtroRol,
            'fecha' => $filtroFecha,
            'total' => $filteredTotal,
            'latest' => $filteredLatest,
        ];

        return compact(
            'totalLogins',
            'totalLoginsToday',
            'totalFailed',
            'totalFailedToday',
            'totalProps',
            'totalPropsToday',
            'latestEvents',
            'latestProps',
            'topTrend',
            'filters',
            'accionesResumen',
            'rolesResumen'
        );
    }

    private function formatVoiceContext(array $context): string
    {
        $lines = [
            "Total inicios de sesión: {$context['totalLogins']}",
            "Inicios de sesión hoy: {$context['totalLoginsToday']}",
            "Total intentos fallidos: {$context['totalFailed']}",
            "Intentos fallidos hoy: {$context['totalFailedToday']}",
            "Total propiedades registradas: {$context['totalProps']}",
            "Propiedades registradas hoy: {$context['totalPropsToday']}",
        ];

        if (!empty($context['topTrend'])) {
            $trend = $context['topTrend'];
            $lines[] = "Tendencia destacada: {$trend['zona']} - {$trend['tipo_propiedad']}, {$trend['tendencia']}, {$trend['probabilidad_venta']}% de probabilidad estimada.";
        }

        if (!empty($context['filters'])) {
            $filters = $context['filters'];
            $lines[] = "Filtros actuales del reporte: accion {$filters['accion']}, rol {$filters['rol']}, fecha " . ($filters['fecha'] ?: 'sin fecha') . ". Registros encontrados: {$filters['total']}.";
        }

        if (!empty($context['accionesResumen'])) {
            $lines[] = 'Resumen por accion:';
            foreach ($context['accionesResumen'] as $accion => $total) {
                $lines[] = "- {$accion}: {$total}";
            }
        }

        if (!empty($context['rolesResumen'])) {
            $lines[] = 'Resumen por rol:';
            foreach ($context['rolesResumen'] as $rol => $total) {
                $lines[] = "- {$rol}: {$total}";
            }
        }

        if (!empty($context['latestProps'])) {
            $lines[] = 'Últimas propiedades registradas:';
            foreach ($context['latestProps'] as $item) {
                $description = trim(preg_replace('/\s+/', ' ', $item['descripcion'] ?? ''));
                $lines[] = '- ' . ($item['nombre'] ?? 'Sin nombre') . ($description ? ': ' . mb_substr($description, 0, 80) : '');
            }
        }

        if (!empty($context['latestEvents'])) {
            $lines[] = 'Últimos eventos de hoy:';
            foreach ($context['latestEvents'] as $item) {
                $description = trim(preg_replace('/\s+/', ' ', $item['descripcion'] ?? ''));
                $lines[] = '- ' . ($item['accion'] ?? 'Evento') . ' (' . date('H:i', strtotime($item['fecha_hora'])) . ')' . ($description ? ': ' . mb_substr($description, 0, 80) : '');
            }
        }

        return implode("\n", $lines);
    }

    private function parseGeminiResponseText(array $json): string
    {
        if (isset($json['candidates']) && is_array($json['candidates'])) {
            $collected = [];
            foreach ($json['candidates'] as $candidate) {
                if (!is_array($candidate) || !isset($candidate['content'])) {
                    continue;
                }
                $collected[] = $this->extractGeminiText($candidate['content']);
            }
            $text = trim(preg_replace('/\s+/u', ' ', implode(' ', array_filter($collected))));
            return $text;
        }

        if (isset($json['content'])) {
            return trim(preg_replace('/\s+/u', ' ', $this->extractGeminiText($json['content'])));
        }

        return '';
    }

    private function extractGeminiText(mixed $content): string
    {
        $parts = [];

        if (is_string($content)) {
            return $content;
        }

        if (!is_array($content)) {
            return '';
        }

        if (isset($content['text']) && is_string($content['text'])) {
            $parts[] = $content['text'];
        }

        if (isset($content['output_text']) && is_string($content['output_text'])) {
            $parts[] = $content['output_text'];
        }

        if (isset($content['parts']) && is_array($content['parts'])) {
            foreach ($content['parts'] as $part) {
                $partText = $this->extractGeminiText($part);
                if ($partText !== '') {
                    $parts[] = $partText;
                }
            }
        }

        if ($this->isSequentialArray($content)) {
            foreach ($content as $item) {
                $itemText = $this->extractGeminiText($item);
                if ($itemText !== '') {
                    $parts[] = $itemText;
                }
            }
        }

        return trim(preg_replace('/\s+/u', ' ', implode(' ', array_filter($parts))));
    }

    private function isSequentialArray(array $array): bool
    {
        return array_keys($array) === range(0, count($array) - 1);
    }

    private function ensureCompleteSentence(string $text): string
    {
        $trimmed = trim($text);
        if ($trimmed === '') {
            return '';
        }
        $lastChar = mb_substr($trimmed, -1);
        if (!in_array($lastChar, ['.', '!', '?'], true)) {
            $trimmed .= '.';
        }
        return $trimmed;
    }

    private function localVoiceFallback(string $query, array $context, bool $allowGeneric = true): ?string
    {
        $normalized = $this->normalizeText($query);

        $isSession = $this->containsAny($normalized, ['inicio de sesión', 'inicios de sesión', 'sesion', 'sesiones', 'login', 'logins']);
        $isFailed = $this->containsAny($normalized, ['fallido', 'fallidos', 'intento fallido', 'intentos fallidos', 'error de acceso']);
        $isProperty = $this->containsAny($normalized, ['propiedad', 'propiedades', 'registradas', 'inmueble', 'vivienda']);
        $isActivity = $this->containsAny($normalized, ['actividad', 'reporte', 'reportes', 'evento', 'eventos', 'registro', 'registros']);
        $isTrend = $this->containsAny($normalized, ['tendencia', 'tendencias', 'mercado', 'prediccion', 'predicciones', 'proyeccion']);
        $isFilter = $this->containsAny($normalized, ['filtro', 'filtros', 'filtrado', 'aplicado', 'aplicados', 'accion', 'acciones', 'rol', 'roles', 'fecha']);
        $isToday = $this->containsAny($normalized, ['hoy', 'solo hoy', 'de hoy', 'este dia', 'esta mañana', 'esta tarde', 'esta noche']);
        $isTotal = $this->containsAny($normalized, ['total', 'en total', 'hasta ahora', 'todos', 'todo', 'sumados']);

        if ($isFilter && !empty($context['filters'])) {
            $filters = $context['filters'];
            $fecha = $filters['fecha'] ?: 'sin fecha especifica';
            return "Los filtros actuales son accion {$filters['accion']}, rol {$filters['rol']} y fecha {$fecha}. Con esos filtros hay {$filters['total']} registros.";
        }

        if ($isTrend && !empty($context['topTrend'])) {
            $trend = $context['topTrend'];
            return "La tendencia mas fuerte es {$trend['zona']} para {$trend['tipo_propiedad']}, con tendencia {$trend['tendencia']} y una probabilidad estimada de venta del {$trend['probabilidad_venta']} por ciento.";
        }

        if ($isSession) {
            if ($isToday && !$isTotal) {
                return "Hoy se registraron {$context['totalLoginsToday']} inicios de sesión.";
            }
            if ($isTotal && !$isToday) {
                return "Hay {$context['totalLogins']} inicios de sesión registrados en total.";
            }
            return "Hay {$context['totalLogins']} inicios de sesión registrados, de los cuales {$context['totalLoginsToday']} son de hoy.";
        }

        if ($isFailed) {
            if ($isToday && !$isTotal) {
                return "Hoy se registraron {$context['totalFailedToday']} intentos fallidos.";
            }
            if ($isTotal && !$isToday) {
                return "Hay {$context['totalFailed']} intentos fallidos registrados en total.";
            }
            return "Hay {$context['totalFailed']} intentos fallidos registrados, y {$context['totalFailedToday']} de ellos fueron hoy.";
        }

        if ($isProperty) {
            if ($isToday && !$isTotal) {
                return "Hoy se registraron {$context['totalPropsToday']} propiedades.";
            }
            if ($isTotal && !$isToday) {
                return "Hay {$context['totalProps']} propiedades registradas en total.";
            }
            return "Se han registrado {$context['totalProps']} propiedades en total, y {$context['totalPropsToday']} de ellas hoy.";
        }

        if ($isActivity) {
            if ($isToday) {
                return "Hoy hay {$context['totalLoginsToday']} inicios de sesión y {$context['totalPropsToday']} propiedades registradas.";
            }
            return "En el panel hay datos recientes sobre {$context['totalLogins']} inicios de sesión y {$context['totalProps']} propiedades registradas.";
        }

        return $allowGeneric ? 'No tengo una respuesta exacta para eso ahora. Intenta preguntar de nuevo.' : null;
    }

    private function normalizeText(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü', '¿', '?', '¡', '!'], ['a', 'e', 'i', 'o', 'u', 'n', 'u', '', '', '', ''], $text);
        return preg_replace('/\s+/u', ' ', trim($text));
    }

    private function containsAny(string $text, array $words): bool
    {
        foreach ($words as $word) {
            if (str_contains($text, $word)) {
                return true;
            }
        }
        return false;
    }

    private function buildMarketTrendRows(string $tipo = 'todos', string $zona = ''): array
    {
        $propiedades = Propiedad::query()
            ->withCount('solicitudes')
            ->get()
            ->filter(function ($propiedad) use ($tipo, $zona) {
                if ($tipo !== 'todos' && $propiedad->tipo !== $tipo) {
                    return false;
                }

                if ($zona !== '' && trim((string) $propiedad->zona) !== $zona) {
                    return false;
                }

                return trim((string) $propiedad->zona) !== '' && trim((string) $propiedad->tipo) !== '';
            });

        return $propiedades
            ->groupBy(fn ($propiedad) => trim((string) $propiedad->zona) . '|' . trim((string) $propiedad->tipo))
            ->map(function ($items, $key) {
                [$zona, $tipoPropiedad] = explode('|', $key, 2);
                $total = $items->count();
                $solicitudes = (int) $items->sum('solicitudes_count');
                $disponibles = $items->where('estado', 'Disponible')->count();
                $noDisponibles = max(0, $total - $disponibles);
                $conMapa = $items->filter(fn ($item) => $item->latitud !== null && $item->longitud !== null)->count();
                $precioPromedio = round((float) $items->avg('precio'), 2);

                $demandaScore = min(35, $solicitudes * 7);
                $conversionScore = $total > 0 ? min(25, round(($noDisponibles / $total) * 25)) : 0;
                $disponibilidadScore = $disponibles > 0 ? 10 : 0;
                $mapaScore = $total > 0 ? round(($conMapa / $total) * 10) : 0;
                $volumenScore = min(15, $total * 3);
                $probabilidad = min(95, 15 + $demandaScore + $conversionScore + $disponibilidadScore + $mapaScore + $volumenScore);

                if ($probabilidad >= 75) {
                    $tendencia = 'Alta';
                } elseif ($probabilidad >= 55) {
                    $tendencia = 'Media';
                } elseif ($solicitudes === 0 && $disponibles > 0) {
                    $tendencia = 'Estable';
                } else {
                    $tendencia = 'Baja';
                }

                return [
                    'zona' => $zona,
                    'tipo_propiedad' => $tipoPropiedad,
                    'propiedades' => $total,
                    'disponibles' => $disponibles,
                    'solicitudes' => $solicitudes,
                    'precio_promedio' => $precioPromedio,
                    'probabilidad_venta' => $probabilidad,
                    'dias_estimados_venta' => max(15, 125 - $probabilidad),
                    'tendencia' => $tendencia,
                ];
            })
            ->sortByDesc('probabilidad_venta')
            ->values()
            ->all();
    }

    /**
     * Endpoint para descargar el reporte generado (pdf|xlsx)
     */
    public function export(Request $request, $type)
    {
        $filtroAccion = $request->query('accion','todas');
        $filtroRol    = $request->query('rol','todos');
        $filtroFecha  = $request->query('fecha','');

        // Soporte CSV inmediato sin paquetes externos
        if ($type === 'csv') {
            $query = RegistroActividad::query()->orderBy('id','desc');
            if ($filtroAccion !== 'todas') $query->where('accion', $filtroAccion);
            if ($filtroRol    !== 'todos') $query->where('rol', $filtroRol);
            if ($filtroFecha)              $query->whereRaw("DATE(fecha_hora) = ?", [$filtroFecha]);
            $actividad = $query->limit(200)->get();

            $filename = 'Reportes_LORENT-INMOBILIARIA_' . now()->format('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($actividad) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['ID','FechaHora','Nombre','Correo','Rol','Accion','Descripcion','IP']);
                foreach ($actividad as $r) {
                    fputcsv($out, [
                        $r->id,
                        optional($r->fecha_hora)->format('Y-m-d H:i:s'),
                        $r->nombre,
                        $r->correo,
                        $r->rol,
                        $r->accion,
                        $r->descripcion,
                        $r->ip,
                    ]);
                }
                fclose($out);
            };

            return response()->stream($callback, 200, $headers);
        }

        $hash = md5(implode('|', [$filtroAccion, $filtroRol, $filtroFecha]));
        $folder = 'reports';

        // Si el usuario pidió PDF/XLSX pero faltan las dependencias, entregar CSV como fallback
        if ($type === 'pdf' && !class_exists('Barryvdh\\DomPDF\\Facade\\Pdf')) {
            // Regenerar actividad y servir CSV
            $query = RegistroActividad::query()->orderBy('id','desc');
            if ($filtroAccion !== 'todas') $query->where('accion', $filtroAccion);
            if ($filtroRol    !== 'todos') $query->where('rol', $filtroRol);
            if ($filtroFecha)              $query->whereRaw("DATE(fecha_hora) = ?", [$filtroFecha]);
            $actividad = $query->limit(200)->get();
            // Reuse CSV stream
            $filename = 'Reportes_LORENT-INMOBILIARIA_fallback_' . now()->format('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];
            $callback = function() use ($actividad) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['ID','FechaHora','Nombre','Correo','Rol','Accion','Descripcion','IP']);
                foreach ($actividad as $r) {
                    fputcsv($out, [
                        $r->id,
                        optional($r->fecha_hora)->format('Y-m-d H:i:s'),
                        $r->nombre,
                        $r->correo,
                        $r->rol,
                        $r->accion,
                        $r->descripcion,
                        $r->ip,
                    ]);
                }
                fclose($out);
            };
            return response()->stream($callback, 200, $headers);
        }

        if ($type === 'xlsx' && !class_exists('Maatwebsite\\Excel\\Facades\\Excel')) {
            // Mismo fallback a CSV
            $query = RegistroActividad::query()->orderBy('id','desc');
            if ($filtroAccion !== 'todas') $query->where('accion', $filtroAccion);
            if ($filtroRol    !== 'todos') $query->where('rol', $filtroRol);
            if ($filtroFecha)              $query->whereRaw("DATE(fecha_hora) = ?", [$filtroFecha]);
            $actividad = $query->limit(200)->get();
            $filename = 'Reportes_LORENT-INMOBILIARIA_fallback_' . now()->format('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];
            $callback = function() use ($actividad) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['ID','FechaHora','Nombre','Correo','Rol','Accion','Descripcion','IP']);
                foreach ($actividad as $r) {
                    fputcsv($out, [
                        $r->id,
                        optional($r->fecha_hora)->format('Y-m-d H:i:s'),
                        $r->nombre,
                        $r->correo,
                        $r->rol,
                        $r->accion,
                        $r->descripcion,
                        $r->ip,
                    ]);
                }
                fclose($out);
            };
            return response()->stream($callback, 200, $headers);
        }

        if ($type === 'pdf') {
            $path = "$folder/Reportes_LORENT-INMOBILIARIA_{$hash}.pdf";
        } else {
            $path = "$folder/Reportes_LORENT-INMOBILIARIA_{$hash}.xlsx";
        }

        if (!Storage::disk('public')->exists($path)) {
            // Si no existe, regenerar y luego devolver
            $query = RegistroActividad::query()->orderBy('id','desc');
            if ($filtroAccion !== 'todas') $query->where('accion', $filtroAccion);
            if ($filtroRol    !== 'todos') $query->where('rol', $filtroRol);
            if ($filtroFecha)              $query->whereRaw("DATE(fecha_hora) = ?", [$filtroFecha]);
            $actividad = $query->limit(200)->get();
            $this->storeReports($actividad, $filtroAccion, $filtroRol, $filtroFecha);
        }

        return Storage::disk('public')->download($path);
    }

    /**
     * Genera y guarda PDF y Excel en storage/app/public/reports
     */
    private function storeReports($actividad, $filtroAccion, $filtroRol, $filtroFecha): void
    {
        $hash = md5(implode('|', [$filtroAccion, $filtroRol, $filtroFecha]));
        $folder = 'reports';
        // Asegurar carpeta
        if (!Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder);
        }

        // PDF (si está disponible)
        if (class_exists('Barryvdh\\DomPDF\\Facade\\Pdf')) {
            $pdfView = view('compartido.reportes_pdf', compact('actividad'));
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHtml($pdfView->render())->setPaper('a4', 'portrait');
            $pdfPath = "$folder/Reportes_LORENT-INMOBILIARIA_{$hash}.pdf";
            Storage::disk('public')->put($pdfPath, $pdf->output());
        }

        // Excel (si está disponible)
        if (class_exists('Maatwebsite\\Excel\\Facades\\Excel')) {
            $excelPath = "$folder/Reportes_LORENT-INMOBILIARIA_{$hash}.xlsx";
            \Maatwebsite\Excel\Facades\Excel::store(new ReporteExport($actividad), $excelPath, 'public');
        }
    }
}
