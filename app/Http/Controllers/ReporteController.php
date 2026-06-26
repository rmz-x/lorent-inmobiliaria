<?php
// app/Http/Controllers/ReporteController.php
namespace App\Http\Controllers;

use App\Models\RegistroActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteExport;

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

        $context = $this->collectVoiceContext();

        $systemPrompt = "Eres un asistente de voz para un dashboard inmobiliario. Responde siempre en español.
Usa únicamente los datos que te entrega el servidor. No inventes cifras ni supongas información adicional.
Responde de forma natural, clara y breve.";

        $userPrompt = "Pregunta: {$query}\n\nDatos disponibles:\n" . $this->formatVoiceContext($context) . "\n\nResponde en un texto natural en español.";

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
                'temperature' => 0.2,
                'maxOutputTokens' => 350,
            ],
        ];

        try {
            $response = Http::timeout(30)
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
            return response()->json(['error' => 'Gemini API request failed'], 502);
        }

        if (!$response->successful()) {
            if (function_exists('logger')) {
                logger('Gemini request error', [
                    'url' => $endpoint,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
            return response()->json([
                'error' => 'Gemini API request failed',
                'status' => $response->status(),
            ], 502);
        }

        $replyText = $this->parseGeminiResponseText($response->json());
        if ($replyText === '') {
            if (function_exists('logger')) {
                logger('Gemini response missing text', [
                    'url' => $endpoint,
                    'response' => $response->json(),
                ]);
            }
            return response()->json([
                'error' => 'Gemini API response did not include text',
                'body' => $response->json(),
            ], 502);
        }

        return response()->json(['speech' => $replyText]);
    }

    private function collectVoiceContext(): array
    {
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

        return compact(
            'totalLogins',
            'totalLoginsToday',
            'totalFailed',
            'totalFailedToday',
            'totalProps',
            'totalPropsToday',
            'latestEvents',
            'latestProps'
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
        if (!isset($json['candidates'][0]['content'])) {
            return '';
        }

        $content = $json['candidates'][0]['content'];
        if (is_string($content)) {
            return trim($content);
        }

        if (is_array($content)) {
            if (isset($content['text']) && is_string($content['text'])) {
                return trim($content['text']);
            }

            if (isset($content['parts']) && is_array($content['parts'])) {
                foreach ($content['parts'] as $part) {
                    if (is_string($part)) {
                        return trim($part);
                    }
                    if (is_array($part)) {
                        if (isset($part['text']) && is_string($part['text'])) {
                            return trim($part['text']);
                        }
                        if (isset($part['output_text']) && is_string($part['output_text'])) {
                            return trim($part['output_text']);
                        }
                    }
                }
            }

            if (isset($content[0]) && is_array($content[0])) {
                foreach ($content as $contentItem) {
                    if (!is_array($contentItem)) {
                        continue;
                    }

                    if (isset($contentItem['text']) && is_string($contentItem['text'])) {
                        return trim($contentItem['text']);
                    }

                    if (isset($contentItem['output_text']) && is_string($contentItem['output_text'])) {
                        return trim($contentItem['output_text']);
                    }

                    if (isset($contentItem['parts']) && is_array($contentItem['parts'])) {
                        foreach ($contentItem['parts'] as $part) {
                            if (is_string($part)) {
                                return trim($part);
                            }
                            if (is_array($part)) {
                                if (isset($part['text']) && is_string($part['text'])) {
                                    return trim($part['text']);
                                }
                                if (isset($part['output_text']) && is_string($part['output_text'])) {
                                    return trim($part['output_text']);
                                }
                            }
                        }
                    }
                }
            }
        }

        return '';
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