@extends('layouts.panel')
@section('titulo', 'Tendencias del mercado')
@section('titulo_pagina', 'Prediccion de Tendencias del Mercado')

@push('styles')
<style>
.trend-hero {
    background: linear-gradient(135deg, #0f2d4f 0%, #14532d 55%, #f59e0b 140%);
    border-radius: 24px;
    color: #fff;
    padding: 28px;
    margin-bottom: 22px;
    box-shadow: 0 22px 52px rgba(15, 45, 79, .22);
}
.trend-hero h2 { margin: 0 0 8px; font-size: 28px; letter-spacing: -.03em; }
.trend-hero p { max-width: 780px; margin: 0; color: rgba(255,255,255,.82); line-height: 1.6; }
.trend-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 20px; }
.trend-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    padding: 18px;
    box-shadow: 0 14px 32px rgba(15, 23, 42, .06);
}
.trend-label { color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: .08em; font-weight: 800; }
.trend-value { margin-top: 8px; color: #0f172a; font-size: 28px; font-weight: 900; }
.trend-filters {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    padding: 18px;
    margin-bottom: 20px;
    display: flex;
    gap: 14px;
    align-items: end;
    flex-wrap: wrap;
}
.trend-group { display: flex; flex-direction: column; gap: 6px; min-width: 190px; }
.trend-group label { font-size: 12px; color: #64748b; font-weight: 800; text-transform: uppercase; letter-spacing: .06em; }
.trend-group select {
    border: 1.5px solid #dbe3ee;
    border-radius: 12px;
    padding: 10px 12px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
}
.trend-btn {
    border: none;
    border-radius: 12px;
    padding: 11px 18px;
    background: #0f2d4f;
    color: #fff;
    font-weight: 800;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.trend-btn.secondary { background: #f1f5f9; color: #0f172a; }
.trend-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 14px 32px rgba(15, 23, 42, .06);
}
.trend-table th, .trend-table td { padding: 14px 16px; border-bottom: 1px solid #eef2f7; text-align: left; }
.trend-table th { background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase; letter-spacing: .06em; }
.trend-badge { border-radius: 999px; padding: 6px 10px; font-size: 12px; font-weight: 900; display: inline-flex; }
.trend-alta { background: #dcfce7; color: #166534; }
.trend-media { background: #fef3c7; color: #92400e; }
.trend-baja { background: #fee2e2; color: #991b1b; }
.trend-estable { background: #e0f2fe; color: #075985; }
.prob-bar { min-width: 120px; height: 10px; background: #e2e8f0; border-radius: 999px; overflow: hidden; }
.prob-bar span { display: block; height: 100%; background: linear-gradient(90deg, #22c55e, #f59e0b); border-radius: 999px; }
.empty-state { background: #fff; border: 1px dashed #cbd5e1; border-radius: 18px; padding: 34px; color: #64748b; text-align: center; }
@media (max-width: 900px) {
    .trend-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .trend-table { min-width: 760px; }
    .trend-scroll { overflow-x: auto; }
}
@media (max-width: 560px) {
    .trend-grid { grid-template-columns: 1fr; }
    .trend-hero { padding: 22px; }
}
</style>
@endpush

@section('contenido')
@php
    $rolePrefix = explode('.', request()->route()->getName())[0] ?? 'admin';
    $reportesRoute = "{$rolePrefix}.reportes";
@endphp

<div class="trend-hero">
    <h2>Predecir Tendencias del Mercado</h2>
    <p>
        El sistema analiza propiedades registradas, disponibilidad, solicitudes de visita y datos de ubicacion para estimar que zonas y tipos de inmueble tienen mayor probabilidad de venta o alquiler.
    </p>
</div>

<div class="trend-grid">
    <div class="trend-card">
        <div class="trend-label">Segmentos analizados</div>
        <div class="trend-value">{{ $resumen['segmentos'] }}</div>
    </div>
    <div class="trend-card">
        <div class="trend-label">Tendencia alta</div>
        <div class="trend-value">{{ $resumen['alta'] }}</div>
    </div>
    <div class="trend-card">
        <div class="trend-label">Solicitudes usadas</div>
        <div class="trend-value">{{ $resumen['solicitudes'] }}</div>
    </div>
    <div class="trend-card">
        <div class="trend-label">Probabilidad promedio</div>
        <div class="trend-value">{{ $resumen['probabilidad_promedio'] }}%</div>
    </div>
</div>

<form method="GET" class="trend-filters">
    <div class="trend-group">
        <label>Tipo de propiedad</label>
        <select name="tipo">
            <option value="todos">Todos</option>
            @foreach(['Venta', 'Alquiler', 'Anticretico'] as $tipo)
                <option value="{{ $tipo }}" {{ $filtroTipo === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
            @endforeach
        </select>
    </div>
    <div class="trend-group">
        <label>Zona</label>
        <select name="zona">
            <option value="">Todas las zonas</option>
            @foreach($zonas as $zona)
                <option value="{{ $zona }}" {{ $filtroZona === $zona ? 'selected' : '' }}>{{ $zona }}</option>
            @endforeach
        </select>
    </div>
    <button class="trend-btn" type="submit">Analizar</button>
    <a class="trend-btn secondary" href="{{ route($reportesRoute) }}">Volver a reportes</a>
</form>

@if($predicciones->isEmpty())
    <div class="empty-state">
        No hay datos suficientes para generar tendencias con los filtros actuales.
    </div>
@else
    <div class="trend-scroll">
        <table class="trend-table">
            <thead>
                <tr>
                    <th>Zona</th>
                    <th>Tipo</th>
                    <th>Propiedades</th>
                    <th>Solicitudes</th>
                    <th>Precio promedio</th>
                    <th>Probabilidad</th>
                    <th>Dias estimados</th>
                    <th>Tendencia</th>
                </tr>
            </thead>
            <tbody>
                @foreach($predicciones as $item)
                    @php
                        $badgeClass = match($item['tendencia']) {
                            'Alta' => 'trend-alta',
                            'Media' => 'trend-media',
                            'Baja' => 'trend-baja',
                            default => 'trend-estable',
                        };
                    @endphp
                    <tr>
                        <td><strong>{{ $item['zona'] }}</strong></td>
                        <td>{{ $item['tipo_propiedad'] }}</td>
                        <td>{{ $item['propiedades'] }} total / {{ $item['disponibles'] }} disponibles</td>
                        <td>{{ $item['solicitudes'] }}</td>
                        <td>Bs {{ number_format($item['precio_promedio'], 2) }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px">
                                <div class="prob-bar"><span style="width: {{ $item['probabilidad_venta'] }}%"></span></div>
                                <strong>{{ $item['probabilidad_venta'] }}%</strong>
                            </div>
                        </td>
                        <td>{{ $item['dias_estimados_venta'] }} dias</td>
                        <td><span class="trend-badge {{ $badgeClass }}">{{ $item['tendencia'] }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
