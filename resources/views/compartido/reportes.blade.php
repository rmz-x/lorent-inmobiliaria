@extends('layouts.panel')
@section('titulo', 'Reportes')
@section('titulo_pagina', 'Reportes de actividad')

@push('styles')
<style>
/* Badges de acción */
.badge-accion-login    { background:#e8f5e9; color:#2e7d32; }
.badge-accion-logout   { background:#fff8e1; color:#e65100; }
.badge-accion-fallido  { background:#ffebee; color:#c62828; }
.badge-accion-propiedad{ background:#e3f2fd; color:#1565c0; }
.badge-accion-visita   { background:#f3e5f5; color:#6a1b9a; }
.badge-accion-usuario  { background:#e0f7fa; color:#00695c; }
.badge-accion-default  { background:#f5f5f5; color:#424242; }

/* Badges de rol */
.rol-badge         { font-size:11px; padding:3px 9px; border-radius:20px; font-weight:600; display:inline-block; }
.rol-administrador { background:#e3f0ff; color:#1565c0; }
.rol-agente        { background:#e8f5e9; color:#2e7d32; }
.rol-asistente     { background:#ede7f6; color:#512da8; }
.rol-cliente       { background:#fff8e1; color:#e65100; }

/* Filtros */
.filter-bar {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
    align-items: flex-end;
    justify-content: flex-start;
}
.filter-groups-container {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
    align-items: flex-end;
    flex: 1;
}
.filter-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
    min-width: 160px;
}
.filter-actions {
    display: flex;
    gap: 8px;
    align-items: flex-end;
    flex-wrap: wrap;
}
.export-actions {
    display: flex;
    gap: 10px;
    align-items: flex-end;
    flex-wrap: wrap;
    margin-left: auto;
}

@media (max-width: 1200px) {
    .filter-bar {
        flex-direction: column;
        align-items: stretch;
    }
    .filter-groups-container {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .filter-group {
        width: 100%;
    }
    .filter-actions {
        width: 100%;
        justify-content: center;
    }
    .export-actions {
        width: 100%;
        justify-content: center;
        margin-left: 0;
    }
}

@media (max-width: 640px) {
    .filter-bar {
        flex-direction: column;
        align-items: stretch;
    }
    .filter-groups-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        align-items: flex-end;
    }
    .filter-group:nth-child(3) {
        grid-column: 1;
    }
    .filter-actions {
        grid-column: 2;
        justify-content: flex-end;
        gap: 8px;
    }
    .export-actions {
        grid-column: 1 / -1;
        justify-content: center;
        gap: 8px;
    }
}
.filter-group label {
    font-size: 11px;
    color: #8a94a6;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.filter-group select,
.filter-group input[type="date"] {
    padding: 8px 11px;
    border: 1.5px solid #e8eaed;
    border-radius: 8px;
    font-size: 13px;
    font-family: inherit;
    outline: none;
    background: #fafbfc;
    color: #1a1a2e;
    transition: border-color 200ms;
    min-width: 160px;
}
.filter-group select:focus,
.filter-group input[type="date"]:focus {
    border-color: #1a3d8f;
    background: #fff;
}
.btn-filter {
    background: #1a3d8f; color: #fff; border: none;
    padding: 8px 20px; border-radius: 8px; font-size: 13px;
    cursor: pointer; font-family: inherit; font-weight: 500;
    transition: background 200ms; white-space: nowrap;
}
.btn-filter:hover { background: #163273; }

.btn-limpiar {
    padding: 8px 16px; border-radius: 8px;
    border: 1.5px solid #e8eaed; color: #8a94a6;
    font-size: 13px; text-decoration: none;
    background: #fff; transition: border-color 200ms, color 200ms;
    white-space: nowrap;
}
.btn-limpiar:hover { border-color:#1a3d8f; color:#1a3d8f; }

.btn-export {
    border: none;
    border-radius: 8px;
    color: #fff;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: filter 200ms ease;
    white-space: nowrap;
}

.btn-export:hover { filter: brightness(0.92); }

.btn-export-blue { background: #1d4ed8; }
.btn-export-green { background: #16a34a; }
.btn-export-gray  { background: #374151; }

@media (max-width: 1200px) {
    .filter-bar {
        flex-direction: column;
        align-items: stretch;
    }
    .filter-groups-container,
    .filter-actions,
    .export-actions {
        width: 100%;
        justify-content: center;
        margin-left: 0;
    }
}

/* Acción badge inline */
.accion-badge {
    display: inline-flex;
    align-items: center;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;
    white-space: nowrap;
}

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
}
</style>
@endpush

@section('contenido')

{{-- ═══════════════════════════════════════
     TARJETAS DE RESUMEN
════════════════════════════════════════ --}}
<div class="stats grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" style="margin-bottom:20px">

    <div class="stat-card">
        <p class="stat-label">Inicios de sesión</p>
        <p class="stat-value">{{ $totalLogins }}</p>
        <span class="stat-icon">🔐</span>
    </div>

    <div class="stat-card">
        <p class="stat-label">Intentos fallidos</p>
        <p class="stat-value" style="color:#e53935">{{ $totalFallidos }}</p>
        <span class="stat-icon">⚠️</span>
    </div>

    <div class="stat-card">
        <p class="stat-label">Propiedades registradas</p>
        <p class="stat-value">{{ $totalProps }}</p>
        <span class="stat-icon">🏠</span>
    </div>

    <div class="stat-card">
        <p class="stat-label">Actividad hoy</p>
        <p class="stat-value">{{ $totalHoy }}</p>
        <span class="stat-icon">📊</span>
    </div>

</div>

{{-- ═══════════════════════════════════════
     FILTROS
════════════════════════════════════════ --}}
<div class="card" style="margin-bottom:20px">
    <p class="card-title" style="margin-bottom:16px">Filtrar registros</p>
    <form method="GET">
        @php
            $rolePrefix = explode('.', request()->route()->getName())[0] ?? 'admin';
            $exportRoute = "{$rolePrefix}.reportes.export";
        @endphp
        <div class="filter-bar">
            <div class="filter-groups-container">
                <div class="filter-group">
                    <label>Tipo de acción</label>
                    <select name="accion">
                        <option value="todas">Todas las acciones</option>
                        @foreach($acciones as $a)
                        <option value="{{ $a }}" {{ $filtroAccion===$a ? 'selected' : '' }}>{{ $a }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label>Rol</label>
                    <select name="rol">
                        <option value="todos">Todos los roles</option>
                        @foreach(['administrador','agente','asistente','cliente'] as $r)
                        <option value="{{ $r }}" {{ $filtroRol===$r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label>Fecha</label>
                    <input type="date" name="fecha" value="{{ $filtroFecha }}">
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn-filter">Filtrar</button>
                    <a href="{{ request()->url() }}" class="btn-limpiar">Limpiar</a>
                </div>
            </div>

            <div class="export-actions">
                <a href="{{ route($exportRoute, array_merge(request()->query(), ['type' => 'pdf'])) }}" class="btn-export btn-export-blue">
                    Descargar PDF
                </a>
                <a href="{{ route($exportRoute, array_merge(request()->query(), ['type' => 'xlsx'])) }}" class="btn-export btn-export-green">
                    Descargar Excel
                </a>
                <a href="{{ route($exportRoute, array_merge(request()->query(), ['type' => 'csv'])) }}" class="btn-export btn-export-gray">
                    Descargar CSV
                </a>
            </div>
        </div>
    </form>
</div>

{{-- ═══════════════════════════════════════
     TABLA DE ACTIVIDAD
════════════════════════════════════════ --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">
            Registro de actividad
            <span style="font-size:12px;color:#8a94a6;font-weight:400;margin-left:6px">
                ({{ $actividad->count() }} registros)
            </span>
        </span>
    </div>

    <div class="table-responsive">
    <div class="table-container w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
<table class="min-w-[600px] w-full text-sm text-left">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha y hora</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Acción</th>
                    <th>Descripción</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
            @forelse($actividad as $r)
            @php
                $al = strtolower($r->accion);
                if      (str_contains($al,'inicio'))        $cls = 'badge-accion-login';
                elseif  (str_contains($al,'cierre'))        $cls = 'badge-accion-logout';
                elseif  (str_contains($al,'fallido'))       $cls = 'badge-accion-fallido';
                elseif  (str_contains($al,'propiedad'))     $cls = 'badge-accion-propiedad';
                elseif  (str_contains($al,'visita'))        $cls = 'badge-accion-visita';
                elseif  (str_contains($al,'usuario'))       $cls = 'badge-accion-usuario';
                else                                        $cls = 'badge-accion-default';
            @endphp
            <tr>
                <td data-label="ID" style="color:#8a94a6;font-size:12px"><span class="td-value">{{ $r->id }}</span></td>

                <td data-label="Fecha" style="white-space:nowrap;font-size:12px"><span class="td-value">
                    <strong>{{ \Carbon\Carbon::parse($r->fecha_hora)->format('d/m/Y') }}</strong><br>
                    <span style="color:#8a94a6">{{ \Carbon\Carbon::parse($r->fecha_hora)->format('H:i:s') }}</span>
                </span></td>

                <td data-label="Usuario"><span class="td-value">
                    <div style="font-size:13px;font-weight:500;color:#0f1e3c">{{ $r->nombre ?? '—' }}</div>
                    <div style="font-size:11px;color:#8a94a6">{{ $r->correo ?? '' }}</div>
                </span></td>

                <td data-label="Rol"><span class="td-value">
                    @if($r->rol)
                        <span class="rol-badge rol-{{ $r->rol }}">{{ ucfirst($r->rol) }}</span>
                    @else
                        <span style="color:#8a94a6">—</span>
                    @endif
                </span></td>

                <td data-label="Acción"><span class="td-value">
                    <span class="accion-badge {{ $cls }}">{{ $r->accion }}</span>
                </span></td>

                <td data-label="Detalle" style="font-size:12px;color:#6c757d;max-width:260px"><span class="td-value">
                    {{ $r->descripcion }}
                </span></td>

                <td data-label="IP" style="font-size:11px;color:#8a94a6;font-family:monospace"><span class="td-value">
                    {{ $r->ip ?? '—' }}
                </span></td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;color:#8a94a6;padding:36px">
                    No hay registros con los filtros seleccionados.
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
</div>
 </div>
</div>

@endsection