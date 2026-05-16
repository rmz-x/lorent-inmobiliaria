@extends('layouts.panel')
@section('titulo','Visitas')
@section('titulo_pagina','Agenda de visitas')

@push('styles')
<style>
    .btn-confirmar {
        background: linear-gradient(135deg, #10b981, #059669) !important;
        color: #fff !important;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2) !important;
        border: none !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13px;
    }
    .btn-confirmar:hover { transform: translateY(-1px); box-shadow: 0 6px 14px rgba(16, 185, 129, 0.3) !important; }

    .btn-cancelar {
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        color: #fff !important;
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2) !important;
        border: none !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13px;
    }
    .btn-cancelar:hover { transform: translateY(-1px); box-shadow: 0 6px 14px rgba(239, 68, 68, 0.3) !important; }

/* Mobile card design */
@media (max-width: 640px) {
    .table-container, .w-full.overflow-x-auto {
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
        background: #fff !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 12px !important;
        margin-bottom: 12px !important;
        padding: 16px 14px !important;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02) !important;
    }
    td {
        padding: 6px 0 !important;
        border: none !important;
        display: flex !important;
        flex-direction: row !important;
        justify-content: flex-start !important;
        align-items: flex-start !important;
        font-size: 13.5px !important;
        line-height: 1.4 !important;
    }
    td::before {
        content: attr(data-label);
        font-weight: 700 !important;
        color: #94a3b8 !important;
        font-size: 11px !important;
        width: 95px !important;
        flex-shrink: 0 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px;
    }
    .td-value {
        flex: 1 !important;
        min-width: 0 !important;
        text-align: left !important;
        word-break: break-word !important;
        color: #1e293b !important;
        font-weight: 600 !important;
    }
    
    td[data-label="Acción"] {
        margin-top: 12px !important;
        padding-top: 14px !important;
        border-top: 1px solid #f1f5f9 !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
    }
    td[data-label="Acción"]::before {
        display: none !important;
    }

    .action-btns {
        width: 100% !important;
        display: flex !important;
        gap: 12px !important;
        justify-content: center !important;
    }
    .action-btns form { flex: 1 !important; max-width: 140px !important; }
    .action-btns button {
        width: 100% !important;
        height: 38px !important;
        border-radius: 10px !important;
    }

    td .badge {
        font-size: 11px !important;
        padding: 4px 12px !important;
        border-radius: 20px !important;
        display: inline-block !important;
        font-weight: 600 !important;
    }
}
</style>
@endpush

@section('contenido')
<div class="card">
    <div class="card-header">
        <span class="card-title">
            Todas las solicitudes de visita
            <span style="font-size:12px;color:#8a94a6;font-weight:400;margin-left:6px">
                ({{ $solicitudes->count() }} registros)
            </span>
        </span>
    </div>
    <div class="table-container">
<div class="w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
<table class="min-w-[600px] w-full text-sm text-left">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Propiedad</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
            @forelse($solicitudes as $s)
            @php
                $cls = match($s->estado) {
                    'Aceptada'  => 'badge-disponible',
                    'Rechazada' => 'badge-vendido',
                    default     => 'badge-reservado'
                };
            @endphp
            <tr>
                <td data-label="Cliente">
                    <span class="td-value">{{ $s->cliente->nombre ?? '—' }}</span>
                </td>
                <td data-label="Propiedad">
                    <span class="td-value">{{ $s->propiedad->titulo ?? '—' }}</span>
                </td>
                <td data-label="Fecha">
                    <span class="td-value">{{ \Carbon\Carbon::parse($s->fecha_solicitada)->format('d/m/Y') }}</span>
                </td>
                <td data-label="Estado">
                    <span class="td-value">
                        <span class="badge {{ $cls }}">{{ ucfirst($s->estado) }}</span>
                    </span>
                </td>
                <td data-label="Acción">
                    @if($s->estado === 'Pendiente')
                    <div class="action-btns">
                        <form method="POST" action="{{ route('asistente.visitas.estado', $s) }}">
                            @csrf @method('PUT')
                            <input type="hidden" name="estado" value="Aceptada">
                            <button type="submit" class="btn-confirmar">Confirmar</button>
                        </form>
                        <form method="POST" action="{{ route('asistente.visitas.estado', $s) }}">
                            @csrf @method('PUT')
                            <input type="hidden" name="estado" value="Rechazada">
                            <button type="submit" class="btn-cancelar">Cancelar</button>
                        </form>
                    </div>
                    @else
                    <span class="td-value" style="font-size:12px;color:#8a94a6">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;color:#8a94a6;padding:28px">
                    No hay solicitudes de visita.
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
</div>
 </div>
</div>
@endsection