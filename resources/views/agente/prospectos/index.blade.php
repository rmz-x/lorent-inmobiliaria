@extends('layouts.panel')
@section('titulo', 'Mis Prospectos')
@section('titulo_pagina', 'Mis Prospectos')

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
    <div class="card-header flex justify-between items-center">
        <span class="card-title">Listado de Prospectos CRM</span>
        <a href="{{ route('agente.prospectos.create') }}" class="btn-primary" style="padding: 5px 15px; text-decoration: none; border-radius: 5px;">+ Nuevo Prospecto</a>
    </div>

    @if(session('success'))
        <div class="alert success" style="padding: 10px; background-color: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-container w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
        <table class="min-w-[600px] w-full text-sm text-left">
            <thead>
                <tr style="background-color: #f8f9fa;">
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Nombre</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Contacto</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Etapa (Estado)</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Propiedad de Interés</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($prospectos as $p)
                <tr>
                    <td data-label="Nombre" style="padding: 10px; border-bottom: 1px solid #dee2e6;"><span class="td-value">{{ $p->nombre }}</span></td>
                    <td data-label="Contacto" style="padding: 10px; border-bottom: 1px solid #dee2e6;">
                        <span class="td-value">
                            {{ $p->telefono ?? '—' }} <br>
                            <span style="font-size: 0.85em; color: #6c757d;">{{ $p->email ?? '' }}</span>
                        </span>
                    </td>
                    <td data-label="Etapa" style="padding: 10px; border-bottom: 1px solid #dee2e6;">
                        <span class="td-value"><span class="badge" style="background-color: #e9ecef; color: #495057; padding: 3px 8px; border-radius: 10px;">{{ $p->etapa }}</span></span>
                    </td>
                    <td data-label="Interés" style="padding: 10px; border-bottom: 1px solid #dee2e6;"><span class="td-value">{{ $p->propiedad->titulo ?? 'Ninguna' }}</span></td>
                    <td data-label="Acciones" style="padding: 10px; border-bottom: 1px solid #dee2e6;">
                        <div class="action-btns">
                            <a href="{{ route('agente.prospectos.edit', $p) }}" class="btn-edit" style="color: #007bff; text-decoration: none;">Actualizar</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;color:#6c757d;padding:20px">No tienes prospectos registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
