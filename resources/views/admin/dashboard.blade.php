@extends('layouts.panel')
@section('titulo', 'Dashboard — Administrador')
@section('titulo_pagina', 'Dashboard')

@push('styles')
<style>
/* Mobile card design */
@media (max-width: 640px) {
    .table-container {
        display: block !important;
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
        overflow: hidden !important;
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
        box-sizing: border-box !important;
    }
    table, thead, tbody, th, td, tr {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        box-sizing: border-box !important;
    }
    thead {
        display: none !important;
    }
    tr {
        background: #fff !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 10px !important;
        margin-bottom: 12px !important;
        padding: 14px 12px !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important;
    }
    td {
        padding: 6px 0 !important;
        border: none !important;
        display: flex !important;
        flex-direction: row !important;
        justify-content: flex-start !important;
        align-items: flex-start !important;
        font-size: 13px !important;
        line-height: 1.4 !important;
    }
    td::before {
        content: attr(data-label);
        font-weight: 600 !important;
        color: #6b7280 !important;
        font-size: 12px !important;
        width: 90px !important;
        flex-shrink: 0 !important;
        margin-right: 0 !important;
        text-align: left !important;
    }
    .td-value {
        flex: 1 !important;
        min-width: 0 !important;
        text-align: left !important;
        word-break: break-word !important;
        overflow-wrap: anywhere !important;
        color: #1f2937 !important;
        font-weight: 500 !important;
    }
    td .badge {
        font-size: 11px !important;
        padding: 4px 10px !important;
        display: inline-block !important;
    }
}
</style>
@endpush

@section('contenido')

<div class="stats grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

    {{-- Card 1: Total propiedades (destacada) --}}
    <div class="stat-card">
        <span class="badge-available">● Available</span>
        <p class="stat-label">Total propiedades</p>
        <p class="stat-value">{{ $totalProps }}</p>
        <span class="badge badge-green" style="margin-top:10px;display:inline-flex">
            {{ $disponibles }} disponibles
        </span>
        <span class="stat-icon">🏠</span>
    </div>

    {{-- Card 2: Propiedades vendidas --}}
    <div class="stat-card">
        <p class="stat-label">Propiedades vendidas</p>
        <p class="stat-value">{{ $totalVentas }}</p>
        <span class="badge" style="margin-top:10px;background:#fff3e0;color:#e65100;display:inline-flex">
            {{ $totalVentas > 0 ? $totalVentas : '0' }} en proceso
        </span>
        <span class="stat-icon">💰</span>
    </div>

    {{-- Card 3: Usuarios del sistema --}}
    <div class="stat-card">
        <p class="stat-label">Usuarios del sistema</p>
        <p class="stat-value">{{ $totalUsuarios }}</p>
        <span class="stat-icon">👥</span>
    </div>

</div>

<div class="activity-card">
    <p class="section-title">Actividad reciente</p>

    <div class="activity-list">

        {{-- Propiedades registradas --}}
        <div class="activity-item">
            <div class="activity-icon blue">🏡</div>
            <div class="activity-body">
                <p class="activity-title">Propiedades registradas</p>
                @if($totalProps > 0)
                    <p class="activity-desc">
                        {{ $totalProps }} propiedad(es) registrada(s) en el sistema.
                    </p>
                @else
                    <p class="activity-desc">Sin propiedades registradas aún.</p>
                @endif
            </div>
        </div>

        {{-- Ventas registradas --}}

        <div class="activity-item">
            <div class="activity-icon gold">💲</div>
            <div class="activity-body">
                <p class="activity-title">Ventas registradas</p>
                <p class="activity-desc">{{ $totalVentas }} ventas registradas este mes.</p>
            </div>
        </div>

        {{-- Usuarios activos --}}
        <div class="activity-item">
            <div class="activity-icon purple">👤</div>
            <div class="activity-body">
                <p class="activity-title">Usuarios activos</p>
                <p class="activity-desc">{{ $totalUsuarios }} usuarios actualmente en el sistema.</p>
            </div>
        </div>

    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Últimas propiedades registradas</span>
        <a href="{{ route('admin.propiedades') }}" class="btn-primary">Ver todas</a>
    </div>

    <div class="table-responsive">
    <div class="table-container w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
<table class="min-w-[600px] w-full text-sm text-left">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Zona</th>
                    <th>Tipo</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Agente</th>
                </tr>
            </thead>
            <tbody>
            @forelse($ultimas as $p)
            <tr>
                <td data-label="Título"><span class="td-value">{{ $p->titulo }}</span></td>
                <td data-label="Zona"><span class="td-value">{{ $p->zona }}</span></td>
                <td data-label="Tipo"><span class="td-value">
                    <span class="badge badge-{{ strtolower($p->tipo) === 'alquiler' ? 'alquiler' : 'venta' }}">
                        {{ $p->tipo }}
                    </span>
                </span></td>
                <td data-label="Precio"><span class="td-value">${{ number_format($p->precio, 0, ',', '.') }}</span></td>
                <td data-label="Estado"><span class="td-value">
                    <span class="badge badge-{{ strtolower($p->estado) }}">
                        {{ $p->estado }}
                    </span>
                </span></td>
                <td data-label="Agente"><span class="td-value">
                    <div class="agent-cell">
                        <div class="agent-avatar">
                            {{ strtoupper(substr($p->agente->nombre ?? 'SA', 0, 2)) }}
                        </div>
                        {{ $p->agente->nombre ?? 'Sin asignar' }}
                    </div>
                </span></td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;color:#8a94a6;padding:28px">
                    No hay propiedades registradas aún.
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
</div>
 </div>
</div>

@endsection