@extends('layouts.panel')
@section('titulo','Visitas')
@section('titulo_pagina','Solicitudes de visita')

@push('styles')
<style>
.filter-tag{display:inline-block;font-size:12px;padding:5px 14px;border:1px solid #dee2e6;border-radius:20px;color:#6c757d;margin-right:6px;transition:all 200ms}
.filter-tag:hover{border-color:#46A2FD;color:#185FA5}
.filter-tag.active{background:#E6F1FB;color:#185FA5;border-color:#85B7EB}

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
    display: inline-flex;
    align-items: center;
    justify-content: center;
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
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.btn-cancelar:hover { transform: translateY(-1px); box-shadow: 0 6px 14px rgba(239, 68, 68, 0.3) !important; }

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
    td[data-label="Acciones"] {
        margin-top: 12px !important;
        padding-top: 12px !important;
        border-top: 1px solid #f3f4f6 !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
    }
    td[data-label="Acciones"]::before {
        display: none !important;
    }
    .action-btns {
        width: 100% !important;
        display: flex !important;
        gap: 12px !important;
        justify-content: center !important;
        margin-top: 10px !important;
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
<div class="card" style="margin-bottom:16px;padding:12px 18px">
    <div style="display:flex;gap:4px;align-items:center;flex-wrap:wrap">
        <span style="font-size:13px;color:#6c757d;margin-right:6px">Filtrar:</span>
        <a href="{{ route('agente.visitas') }}" class="filter-tag {{ $filtro=='todas'?'active':'' }}">Todas</a>
        <a href="{{ route('agente.visitas') }}?estado=Pendiente" class="filter-tag {{ $filtro=='Pendiente'?'active':'' }}">Pendientes</a>
        <a href="{{ route('agente.visitas') }}?estado=Aceptada"  class="filter-tag {{ $filtro=='Aceptada'?'active':'' }}">Confirmadas</a>
        <a href="{{ route('agente.visitas') }}?estado=Rechazada" class="filter-tag {{ $filtro=='Rechazada'?'active':'' }}">Canceladas</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Visitas <span style="font-size:12px;color:#6c757d;font-weight:400">({{ $solicitudes->count() }} registros)</span></span>
    </div>
<div class="table-container w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
<table class="min-w-[600px] w-full text-sm text-left">
        <thead><tr><th>Propiedad</th><th>Cliente</th><th>Correo</th><th>Fecha</th><th>Estado</th><th>Acción</th></tr></thead>
        <tbody>
        @forelse($solicitudes as $v)
        @php $cls = match($v->estado){'Aceptada'=>'badge-disponible','Rechazada'=>'badge-vendido',default=>'badge-reservado'}; @endphp
        <tr>
            <td data-label="Propiedad"><span class="td-value">{{ $v->propiedad->titulo ?? '—' }}</span></td>
            <td data-label="Cliente"><span class="td-value">{{ $v->cliente->nombre ?? '—' }}</span></td>
            <td data-label="Correo" style="font-size:12px;color:#6c757d"><span class="td-value">{{ $v->cliente->correo ?? '—' }}</span></td>
            <td data-label="Fecha"><span class="td-value">{{ $v->fecha_solicitada }}</span></td>
            <td data-label="Estado"><span class="td-value"><span class="badge {{ $cls }}">{{ ucfirst($v->estado) }}</span></span></td>
            <td data-label="Acciones">
                @if($v->estado === 'Pendiente')
                <div class="action-btns">
                    <form method="POST" action="{{ route('agente.visitas.estado',$v) }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="estado" value="Aceptada">
                        <button type="submit" class="btn-confirmar">Confirmar</button>
                    </form>
                    <form method="POST" action="{{ route('agente.visitas.estado',$v) }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="estado" value="Rechazada">
                        <button type="submit" class="btn-cancelar">Cancelar</button>
                    </form>
                </div>
                @else
                <span class="td-value" style="font-size:12px;color:#6c757d">—</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;color:#6c757d;padding:20px">No hay solicitudes.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

</div>
@endsection
