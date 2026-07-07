<?php

namespace App\Http\Controllers;

use App\Models\HistorialCliente;
use App\Models\Propiedad;
use App\Models\Recomendacion;
use App\Models\SolicitudVisita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecomendacionController extends Controller
{
    public function index()
    {
        $clienteId = Auth::id();
        $preferencias = $this->obtenerPreferencias($clienteId);

        // Detectar si hay historial suficiente para personalizar recomendaciones
        $tieneHistorial = (!empty($preferencias['tipos']) || !empty($preferencias['zonas']));

        // Base: incluir conteo de solicitudes para usar como criterio de popularidad
        $query = Propiedad::where('estado', 'Disponible')->withCount('solicitudes');

        if (!$tieneHistorial) {
            // Fallback: si no hay historial, recomendar por popularidad (más solicitudes)
            $propiedades = $query->orderByDesc('solicitudes_count')
                ->orderByDesc('id')
                ->take(8)
                ->get()
                ->map(function (Propiedad $propiedad) {
                    // Normalizar un puntaje básico basado en solicitudes
                    $puntaje = 45 + min($propiedad->solicitudes_count * 8, 40); // max +40
                    $propiedad->puntaje_recomendacion = min($puntaje, 100);
                    $propiedad->recomendacion_id = null;
                    return $propiedad;
                })
                ->values();
        } else {
            $propiedades = $query->orderBy('id', 'desc')
                ->get()
                ->map(function (Propiedad $propiedad) use ($preferencias, $clienteId) {
                    $existing = Recomendacion::where('cliente_id', $clienteId)
                        ->where('propiedad_id', $propiedad->id)
                        ->first();

                    $feedback = $existing->feedback ?? null;

                    $puntaje = $this->calcularPuntaje($propiedad, $preferencias, $feedback);

                    $recomendacion = Recomendacion::updateOrCreate(
                        [
                            'cliente_id' => $clienteId,
                            'propiedad_id' => $propiedad->id,
                        ],
                        [
                            'puntuacion_recomendacion' => $puntaje,
                            'fecha_recomendacion' => now(),
                        ]
                    );

                    $propiedad->puntaje_recomendacion = $puntaje;
                    $propiedad->recomendacion_id = $recomendacion->id;
                    $propiedad->feedback = $recomendacion->feedback;

                    return $propiedad;
                })
                ->sortByDesc('puntaje_recomendacion')
                ->take(8)
                ->values();
        }

        return view('cliente.recomendaciones', [
            'propiedades' => $propiedades,
            'preferencias' => $preferencias,
        ]);
    }

    public function feedback(Request $request, Propiedad $propiedad)
    {
        $request->validate(['feedback' => 'nullable|in:like,dislike,none']);

        $clienteId = Auth::id();
        $value = $request->input('feedback');
        $feedback = $value === 'none' ? null : $value;

        $rec = Recomendacion::updateOrCreate(
            ['cliente_id' => $clienteId, 'propiedad_id' => $propiedad->id],
            ['feedback' => $feedback, 'fecha_recomendacion' => now()]
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'feedback' => $feedback,
                'recomendacion_id' => $rec->id,
            ]);
        }

        return back()->with('success', 'Tu feedback fue registrado.');
    }

    private function obtenerPreferencias(int $clienteId): array
    {
        $visitadas = HistorialCliente::with('propiedad')
            ->where('cliente_id', $clienteId)
            ->latest('fecha_accion')
            ->limit(12)
            ->get()
            ->pluck('propiedad')
            ->filter();

        $solicitadas = SolicitudVisita::with('propiedad')
            ->where('cliente_id', $clienteId)
            ->latest('fecha_solicitada')
            ->limit(8)
            ->get()
            ->pluck('propiedad')
            ->filter();

        $base = $visitadas->merge($solicitadas)->filter();

        $precios = $base->pluck('precio')->filter();
        $precioPromedio = $precios->count() ? (float) $precios->avg() : 0.0;

        return [
            'tipos' => $base->pluck('tipo')->filter()->countBy()->sortDesc()->keys()->take(2)->all(),
            'zonas' => $base->pluck('zona')->filter()->countBy()->sortDesc()->keys()->take(3)->all(),
            'precio_promedio' => $precioPromedio,
        ];
    }

    private function calcularPuntaje(Propiedad $propiedad, array $preferencias, ?string $feedback = null): float
    {
        $puntaje = 45; // Base

        // Coincidencia de tipo (+25)
        if (in_array($propiedad->tipo, $preferencias['tipos'], true)) {
            $puntaje += 25;
        }

        // Coincidencia de zona (+20)
        if (in_array($propiedad->zona, $preferencias['zonas'], true)) {
            $puntaje += 20;
        }

        // Tiene coordenadas (+10)
        if ($propiedad->latitud && $propiedad->longitud) {
            $puntaje += 10;
        }

        // Tiene imagen (+10)
        if ($propiedad->imagen) {
            $puntaje += 10;
        }

        // Precio cercano (+5 o +10)
        $precioRef = $preferencias['precio_promedio'] ?? 0;
        if ($precioRef > 0 && $propiedad->precio) {
            $diff = abs($propiedad->precio - $precioRef) / max($precioRef, 1);
            if ($diff <= 0.10) {
                $puntaje += 10;
            } elseif ($diff <= 0.25) {
                $puntaje += 5;
            }
        }

        // Popularidad (+4 a +12)
        $solicitudes = $propiedad->solicitudes_count ?? 0;
        if ($solicitudes > 0) {
            $puntaje += min($solicitudes * 4, 12);
        }

        // Feedback (+20 o -40)
        if ($feedback === 'like') {
            $puntaje += 20;
        } elseif ($feedback === 'dislike') {
            $puntaje -= 40;
        }

        return min(max($puntaje, 0), 100); // Limitar entre 0 y 100
    }
}
