@extends('layouts.panel')
@section('titulo','Mis clientes')
@section('titulo_pagina','Mis clientes')

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
        width: 110px !important;
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
        flex-direction: row !important;
        gap: 12px !important;
        justify-content: center !important;
        align-items: center !important;
        flex-wrap: wrap !important;
    }
    .action-btns a, .action-btns button {
        width: auto !important;
        padding: 8px 20px !important;
        border-radius: 8px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        text-align: center !important;
        white-space: nowrap !important;
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
<div class="card">
    <div class="card-header">
        <span class="card-title">Clientes que solicitaron visitas <span style="font-size:12px;color:#6c757d;font-weight:400">({{ $clientes->count() }} clientes)</span></span>
    </div>
<div class="table-container w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
<table class="min-w-[600px] w-full text-sm text-left">
        <thead><tr><th>Nombre</th><th>Correo</th><th>Total visitas</th><th>Última solicitud</th><th>Acción</th></tr></thead>
        <tbody>
        @forelse($clientes as $c)
        <tr>
            <td data-label="Nombre"><span class="td-value">{{ $c->cliente->nombre ?? '—' }}</span></td>
            <td data-label="Correo"><span class="td-value">{{ $c->cliente->correo ?? '—' }}</span></td>
            <td data-label="Total visitas"><span class="td-value"><span class="badge badge-blue">{{ $c->total_visitas }}</span></span></td>
            <td data-label="Última sol."><span class="td-value">{{ $c->ultima_visita }}</span></td>
            <td data-label="Acciones">
                <div class="action-btns">
                    <a href="{{ route('agente.clientes.seguimientos', $c->cliente->id) }}" class="btn-edit">Ver cliente</a>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;color:#6c757d;padding:20px">No tienes clientes aún.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

</div>
@endsection
