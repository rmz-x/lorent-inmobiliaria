<?php

namespace App\Http\Controllers;

use App\Models\HistorialCliente;
use App\Models\Propiedad;
use App\Models\Recomendacion;
use App\Models\SolicitudVisita;
use Illuminate\Support\Facades\Auth;

class RecomendacionController extends Controller
{
    public function index()
    {
        $clienteId = Auth::id();
        $preferencias = $this->obtenerPreferencias($clienteId);

        $propiedades = Propiedad::where('estado', 'Disponible')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function (Propiedad $propiedad) use ($preferencias, $clienteId) {
                $puntaje = $this->calcularPuntaje($propiedad, $preferencias);

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

                return $propiedad;
            })
            ->sortByDesc('puntaje_recomendacion')
            ->take(8)
            ->values();

        return view('cliente.recomendaciones', [
            'propiedades' => $propiedades,
            'preferencias' => $preferencias,
        ]);
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

        $base = $visitadas->merge($solicitadas);

        return [
            'tipos' => $base->pluck('tipo')->filter()->countBy()->sortDesc()->keys()->take(2)->all(),
            'zonas' => $base->pluck('zona')->filter()->countBy()->sortDesc()->keys()->take(3)->all(),
        ];
    }

    private function calcularPuntaje(Propiedad $propiedad, array $preferencias): float
    {
        $puntaje = 45;

        if (in_array($propiedad->tipo, $preferencias['tipos'], true)) {
            $puntaje += 25;
        }

        if (in_array($propiedad->zona, $preferencias['zonas'], true)) {
            $puntaje += 20;
        }

        if ($propiedad->latitud && $propiedad->longitud) {
            $puntaje += 5;
        }

        if ($propiedad->imagen) {
            $puntaje += 5;
        }

        return min($puntaje, 100);
    }
}
