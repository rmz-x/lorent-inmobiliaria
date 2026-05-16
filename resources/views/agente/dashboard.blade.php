@extends('layouts.panel')
@section('titulo', 'Panel Agente')
@section('titulo_pagina', 'Panel del Agente')

@push('styles')
<style>
/* Mobile card design */
@media (max-width: 640px) {
    .overflow-x-auto {
        overflow: visible !important;
        border: none !important;
        box-shadow: none !important;
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
        background: #fff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 14px !important;
        margin-bottom: 12px !important;
        padding: 14px !important;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02) !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 6px !important;
    }
    td {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        padding: 4px 0 !important;
        border: none !important;
        font-size: 13px !important;
    }
    td::before {
        content: attr(data-label);
        font-weight: 700 !important;
        color: #94a3b8 !important;
        font-size: 10px !important;
        text-transform: uppercase !important;
    }
    
    /* Headers de tarjeta */
    .prop-row td[data-label="Título"],
    .visit-row td[data-label="Propiedad"] {
        border-bottom: 1px solid #f1f5f9 !important;
        padding-bottom: 8px !important;
        margin-bottom: 4px !important;
        font-size: 15px !important;
        font-weight: 800 !important;
        color: #0f172a !important;
    }
    .prop-row td[data-label="Título"]::before,
    .visit-row td[data-label="Propiedad"]::before { display: none !important; }

    /* Botones de acción */
    .visit-row td[data-label="Acción"] {
        flex-direction: column !important;
        border-top: 1px solid #f1f5f9 !important;
        padding-top: 10px !important;
        margin-top: 4px !important;
    }
    .visit-row td[data-label="Acción"]::before { margin-bottom: 8px !important; align-self: flex-start; }

    .action-btns {
        width: 100% !important;
        display: flex !important;
        gap: 10px !important;
    }
    .action-btns form { flex: 1 !important; }
}

.btn-confirmar {
    background: linear-gradient(135deg, #10b981, #059669) !important;
    color: #fff !important;
    box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2) !important;
    border: none !important; padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 12px; cursor: pointer; width: 100%;
}
.btn-cancelar {
    background: linear-gradient(135deg, #ef4444, #dc2626) !important;
    color: #fff !important;
    box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2) !important;
    border: none !important; padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 12px; cursor: pointer; width: 100%;
}
</style>
@endpush

@section('contenido')

<div class="stats grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="stat-card">
        <p class="stat-label">Mis propiedades</p>
        <p class="stat-value">{{ $misProps }}</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Disponibles</p>
        <p class="stat-value">{{ $disponibles }}</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Vendidas</p>
        <p class="stat-value">{{ $vendidas }}</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Visitas pendientes</p>
        <p class="stat-value">{{ $visitasPend }}</p>
    </div>
</div>

{{-- Mis propiedades recientes --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-header">
        <span class="card-title">Mis propiedades recientes</span>
        <a href="{{ route('agente.propiedades') }}" class="btn-primary">Ver todas</a>
    </div>
<div class="w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
<table class="min-w-[600px] w-full text-sm text-left">
        <thead>
            <tr>
                <th>Título</th>
                <th>Zona</th>
                <th>Tipo</th>
                <th>Precio</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
        @forelse($ultimas as $p)
        <tr class="prop-row">
            <td data-label="Título">{{ $p->titulo }}</td>
            <td data-label="Zona">{{ $p->zona }}</td>
            <td data-label="Tipo">{{ $p->tipo }}</td>
            <td data-label="Precio">${{ number_format($p->precio, 0, ',', '.') }}</td>
            <td data-label="Estado">
                <span class="badge badge-{{ strtolower($p->estado) }}">
                    {{ $p->estado }}
                </span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align:center;color:#6c757d;padding:20px">
                No tienes propiedades asignadas aún.
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>
</div>

</div>

{{-- Visitas pendientes --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Solicitudes de visita pendientes</span>
        <a href="{{ route('agente.visitas') }}" class="btn-primary">Ver todas</a>
    </div>
<div class="w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
<table class="min-w-[600px] w-full text-sm text-left">
        <thead>
            <tr>
                <th>Propiedad</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
        @forelse($visitas as $v)
        <tr class="visit-row">
            <td data-label="Propiedad">{{ $v->propiedad->titulo ?? '—' }}</td>
            <td data-label="Cliente">{{ $v->cliente->nombre ?? '—' }}</td>
            <td data-label="Fecha">{{ $v->fecha_solicitada }}</td>
            <td data-label="Estado"><span class="badge badge-reservado">Pendiente</span></td>
            <td data-label="Acción">
                <div class="action-btns">
                    <form method="POST"
                          action="{{ route('agente.visitas.estado', $v->id) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="estado" value="confirmada">
                        <button type="submit" class="btn-confirmar">Confirmar</button>
                    </form>
                    <form method="POST"
                          action="{{ route('agente.visitas.estado', $v->id) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="estado" value="cancelada">
                        <button type="submit" class="btn-cancelar">Cancelar</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align:center;color:#6c757d;padding:20px">
                No hay visitas pendientes.
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>
</div>

</div>

@endsection
