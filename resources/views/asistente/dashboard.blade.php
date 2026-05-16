@extends('layouts.panel')
@section('titulo', 'Panel Asistente')
@section('titulo_pagina', 'Dashboard')

@push('styles')
<style>
.rol-badge        { font-size:11px; padding:3px 9px; border-radius:20px; font-weight:600; display:inline-block; }
.rol-administrador{ background:#e3f0ff; color:#1565c0; }
.rol-agente       { background:#e8f5e9; color:#2e7d32; }
.rol-asistente    { background:#ede7f6; color:#512da8; }
.rol-cliente      { background:#fff8e1; color:#e65100; }

.visit-status-pendiente  { background:#fff8e1; color:#e65100; }
.visit-status-confirmada { background:#e8f5e9; color:#2e7d32; }
.visit-status-cancelada  { background:#ffebee; color:#c62828; }

/* Mobile card design */
@media (max-width: 640px) {
    .table-responsive, .w-full.overflow-x-auto {
        overflow: visible !important;
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
    }
    table {
        min-width: 0 !important;
        display: block !important;
        width: 100% !important;
    }
    thead {
        display: none !important;
    }
    tbody, tr, td {
        display: block !important;
        width: 100% !important;
    }
    tr {
        display: grid !important;
        grid-template-columns: 1fr 1fr;
        background: #fff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 14px !important;
        margin-bottom: 12px !important;
        padding: 16px !important;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02) !important;
        gap: 8px !important;
        position: relative;
    }
    td {
        display: flex !important;
        flex-direction: column !important;
        align-items: flex-start !important;
        padding: 4px 0 !important;
        border: none !important;
        min-width: 0 !important;
    }
    td::before {
        content: attr(data-label);
        font-weight: 700 !important;
        color: #94a3b8 !important;
        font-size: 10px !important;
        text-transform: uppercase !important;
        margin-bottom: 2px !important;
    }
    
    /* Visitas */
    .visitas-row td[data-label="Cliente"] {
        grid-column: span 2 !important;
        border-bottom: 1px solid #f1f5f9 !important;
        padding-bottom: 10px !important;
        margin-bottom: 4px !important;
    }
    .visitas-row td[data-label="Propiedad"] {
        grid-column: span 2 !important;
        background: #f8fafc !important;
        padding: 8px 12px !important;
        border-radius: 10px !important;
    }

    /* Clientes */
    .clientes-row td[data-label="Nombre"] {
        grid-column: span 2 !important;
        border-bottom: 1px solid #f1f5f9 !important;
        padding-bottom: 10px !important;
        margin-bottom: 6px !important;
    }
    .clientes-row td[data-label="Nombre"] .agent-cell {
        font-size: 16px !important;
        font-weight: 800 !important;
    }
    .clientes-row td[data-label="Correo"],
    .clientes-row td[data-label="Usuario"] {
        grid-column: span 2 !important;
        flex-direction: row !important;
        justify-content: flex-start !important;
        gap: 8px !important;
        align-items: center !important;
        font-size: 13px !important;
    }
    .clientes-row td[data-label="Correo"]::before,
    .clientes-row td[data-label="Usuario"]::before {
        margin-bottom: 0 !important;
        width: 70px !important;
        flex-shrink: 0 !important;
    }

    .clientes-row td[data-label="#"] {
        position: absolute !important;
        top: 16px !important;
        right: 16px !important;
        width: auto !important;
        background: #f1f5f9 !important;
        padding: 4px 10px !important;
        border-radius: 8px !important;
        font-size: 11px !important;
        color: #64748b !important;
    }
    .clientes-row td[data-label="#"]::before { display: none !important; }
}
</style>
@endpush

@section('contenido')

<div class="stats grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

    <div class="stat-card">
        <p class="stat-label">Clientes registrados</p>
        <p class="stat-value">{{ $totalClientes }}</p>
        <span class="stat-icon">👥</span>
    </div>

    <div class="stat-card">
        <p class="stat-label">Visitas pendientes</p>
        <p class="stat-value">{{ $visitasPend }}</p>
        <span class="stat-icon">📋</span>
    </div>

    <div class="stat-card">
        <p class="stat-label">Visitas hoy</p>
        <p class="stat-value">{{ $visitasHoy }}</p>
        <span class="stat-icon">📅</span>
    </div>

    <div class="stat-card">
        <p class="stat-label">Propiedades disponibles</p>
        <p class="stat-value">{{ $totalProps }}</p>
        <span class="stat-icon">🏠</span>
    </div>

</div>

<div class="card" style="margin-bottom:20px">
    <div class="card-header">
        <span class="card-title">Próximas visitas</span>
        <a href="{{ route('asistente.visitas') }}" class="btn-primary">Ver agenda completa</a>
    </div>
    <div class="table-responsive">
<div class="w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
<table class="min-w-[600px] w-full text-sm text-left">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Propiedad</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
            @forelse($visitas as $v)
            <tr class="visitas-row">
                <td data-label="Cliente">
                    <div class="agent-cell">
                        <div class="agent-avatar">
                            {{ strtoupper(substr($v->cliente->nombre ?? 'CL', 0, 2)) }}
                        </div>
                        {{ $v->cliente->nombre ?? '—' }}
                    </div>
                </td>
                <td data-label="Propiedad">{{ $v->propiedad->titulo ?? '—' }}</td>
                <td data-label="Fecha" style="white-space:nowrap;font-size:12px">
                    <strong>{{ \Carbon\Carbon::parse($v->fecha_solicitada)->format('d/m/Y') }}</strong>
                </td>
                <td data-label="Estado">
                    <span class="badge visit-status-{{ $v->estado }}">
                        {{ ucfirst($v->estado) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center;color:#8a94a6;padding:28px">
                    No hay visitas próximas pendientes.
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
</div>
 </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Últimos clientes registrados</span>
    </div>
    <div class="table-responsive">
<div class="w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
<table class="min-w-[600px] w-full text-sm text-left">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
            @forelse($clientes as $c)
            <tr class="clientes-row">
                <td data-label="#" style="color:#8a94a6;font-size:12px">{{ $c->id }}</td>
                <td data-label="Nombre">
                    <div class="agent-cell">
                        <div class="agent-avatar">
                            {{ strtoupper(substr($c->nombre, 0, 2)) }}
                        </div>
                        {{ $c->nombre }}
                    </div>
                </td>
                <td data-label="Correo" style="color:#8a94a6;font-size:12px">{{ $c->correo }}</td>
                <td data-label="Usuario" style="font-size:12px">{{ $c->usuario }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center;color:#8a94a6;padding:28px">
                    No hay clientes registrados aún.
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
</div>
 </div>
</div>

@endsection