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

/* Mic button (glossy red) */
.btn-voice { background: transparent; color: #fff; padding: 4px 8px; display:inline-flex; align-items:center; gap:8px; border-radius:10px; }
.mic-thumb { width:36px; height:36px; border-radius:50%; background: linear-gradient(180deg,#6366f1 0%, #4f46e5 60%); display:flex; align-items:center; justify-content:center; color:#fff; box-shadow: 0 6px 16px rgba(79,70,229,0.16), inset 0 4px 10px rgba(255,255,255,0.06); transition:transform 160ms, box-shadow 160ms; }
.btn-voice .label { font-weight:700; color:#fff; font-size:13px; }
.btn-voice.listening { transform: translateY(-1px); }
.btn-voice.listening .mic-thumb { transform: scale(1.08); box-shadow: 0 14px 32px rgba(79,70,229,0.36), 0 0 0 8px rgba(99,102,241,0.12); }
@keyframes mic-pulse { 0%{ box-shadow: 0 12px 28px rgba(79,70,229,0.36), 0 0 0 0 rgba(99,102,241,0.06);} 50%{ box-shadow: 0 16px 34px rgba(79,70,229,0.44), 0 0 0 10px rgba(99,102,241,0.10);} 100%{ box-shadow: 0 12px 28px rgba(79,70,229,0.36), 0 0 0 0 rgba(99,102,241,0.06);} }
.btn-voice.listening .mic-thumb { animation: mic-pulse 1.2s infinite; }
/* Floating voice button and panel */
.floating-voice-btn { display:inline-flex; align-items:center; gap:6px; background:linear-gradient(135deg,#1e40af,#2563eb); color:#fff; border:none; padding:8px 12px; border-radius:16px; font-weight:700; box-shadow:0 12px 28px rgba(37,99,235,0.30), 0 0 0 1px rgba(59,130,246,0.15); cursor:pointer; transition:transform .18s ease, box-shadow .18s ease, background .18s ease, filter .18s ease; }
.floating-voice-btn:hover { transform:translateY(-1px); box-shadow:0 18px 40px rgba(37,99,235,0.42), 0 0 0 2px rgba(59,130,246,0.18); background:linear-gradient(135deg,#1d4ed8,#1e40af); filter:brightness(1.08); }
.floating-voice-btn .fv-icon { display:inline-flex; align-items:center; justify-content:center; width:20px; height:20px; border-radius:50%; background:rgba(255,255,255,0.22); }
.floating-voice-btn .fv-label { font-size:13px; color:#fff; }
.voice-panel { position:fixed; top:64px; right:20px; width:340px; max-width:calc(100% - 40px); background:linear-gradient(180deg, rgba(10,20,45,0.97), rgba(16,42,77,0.96)); color:#f8fbff; border-radius:26px; box-shadow:0 34px 98px rgba(7,18,54,0.30); z-index:1000; overflow:hidden; animation:fadeIn .18s ease; border:1px solid rgba(96,165,250,0.18); backdrop-filter: blur(20px); }
.voice-panel.dark { background:linear-gradient(180deg, rgba(8,14,34,0.96), rgba(15,27,55,0.94)); color:#f8fcff; }
.voice-panel .vp-header { display:flex; align-items:flex-start; justify-content:space-between; padding:16px 18px; background:linear-gradient(135deg, rgba(59,130,246,0.92), rgba(168,85,247,0.92)); color:#fff; box-shadow: inset 0 -1px 0 rgba(255,255,255,0.08); }
.voice-panel.dark .vp-header { background:linear-gradient(135deg, rgba(67,56,202,0.94), rgba(37,99,235,0.92)); }
.voice-panel .vp-title { font-weight:600; font-size:14px; letter-spacing:.01em; }
.voice-panel .vp-subtitle { font-size:12px; color:rgba(255,255,255,0.88); margin-top:3px; }
.voice-panel .vp-status { font-size:11px; color:rgba(255,255,255,0.92); font-weight:500; background:rgba(255,255,255,0.10); padding:7px 11px; border-radius:999px; white-space:nowrap; }
.voice-panel .vp-body { padding:18px 20px 20px; display:grid; gap:14px; text-align:center; }
.assistant-sphere-container { display:flex; justify-content:center; padding:8px 0 0; }
.assistant-sphere { position: relative; width:132px; height:132px; display:flex; align-items:center; justify-content:center; margin:0 auto; }
.sphere-core { width:86px; height:86px; border-radius:50%; background: radial-gradient(circle at 30% 22%, rgba(255,255,255,0.95) 0%, rgba(147,197,253,0.30) 17%, rgba(129,140,248,0.55) 38%, rgba(168,85,247,0.78) 62%, rgba(49,46,129,1) 100%); box-shadow: 0 0 0 20px rgba(148,187,255,0.08), 0 26px 60px rgba(59,130,246,0.30), inset 0 0 24px rgba(255,255,255,0.32); position:relative; z-index:2; transition:transform .28s ease, box-shadow .28s ease, background .28s ease; }
.sphere-core::before { content:''; position:absolute; top:10%; left:14%; width:28px; height:28px; border-radius:50%; background: rgba(255,255,255,0.85); filter: blur(2px); opacity:0.9; }
.sphere-core::after { content:''; position:absolute; bottom:14%; right:14%; width:20px; height:20px; border-radius:50%; background: rgba(255,255,255,0.32); filter: blur(3px); opacity:0.75; }
.sphere-ring { position:absolute; border-radius:50%; opacity:0.75; pointer-events:none; }
.ring-outer { width:148px; height:148px; border:1px solid rgba(96,165,250,0.32); top:-8px; left:-8px; animation: ringSpin 9s linear infinite; }
.ring-inner { width:102px; height:102px; border:1px solid rgba(168,85,247,0.60); top:14px; left:14px; animation: ringGlow 2.6s ease-in-out infinite; }
.assistant-sphere::before { content:''; position:absolute; inset:0; border-radius:50%; box-shadow: 0 0 0 26px rgba(96,165,250,0.10), 0 0 0 42px rgba(168,85,247,0.08); }
.assistant-sphere::after { content:''; position:absolute; inset:10px; border-radius:50%; box-shadow: 0 0 0 20px rgba(59,130,246,0.10); opacity:0.45; filter: blur(8px); }
.assistant-sphere.idle .sphere-core { animation: idleGlow 4s ease-in-out infinite; }
.assistant-sphere.listening .sphere-core { animation: listeningPulse 1.2s ease-in-out infinite; }
.assistant-sphere.processing .sphere-core { animation: thinkingPulse 1.8s ease-in-out infinite; }
.assistant-sphere.speaking .sphere-core { animation: speakingPulse 1.2s ease-in-out infinite; background: radial-gradient(circle at 30% 22%, rgba(255,255,255,0.95) 0%, rgba(237,233,254,0.20) 15%, rgba(167,139,250,0.50) 38%, rgba(99,102,241,0.82) 68%, rgba(49,46,129,1) 100%); box-shadow: 0 0 0 26px rgba(167,139,250,0.22), 0 30px 64px rgba(59,130,246,0.30), inset 0 0 56px rgba(255,255,255,0.32); transform: scale(1.04); }
.vp-transcript { font-size:14px; color:#f8fbff; min-height:50px; line-height:1.5; text-align:center; font-weight:700; }
.vp-feedback { display:none !important; }
.vp-feedback .ok { background:#ecfdf5; color:#065f46; padding:8px 10px; border-radius:10px; display:inline-block; }
.vp-feedback .err { background:#fef2f2; color:#b91c1c; padding:8px 10px; border-radius:10px; display:inline-block; }
.source-note { display:none; }
@keyframes idleGlow { 0%,100%{ transform: scale(1); box-shadow: 0 0 0 16px rgba(96,165,250,0.12), 0 18px 44px rgba(37,99,235,0.20); } 50%{ transform: scale(1.03); box-shadow: 0 0 0 22px rgba(96,165,250,0.14), 0 22px 52px rgba(37,99,235,0.24); } }
@keyframes listeningPulse { 0%,100%{ transform: scale(1); } 50%{ transform: scale(1.07); } }
@keyframes thinkingPulse { 0%,100%{ transform: scale(1); } 40%{ transform: scale(1.06); } 80%{ transform: scale(1.02); } }
@keyframes speakingPulse { 0%,100%{ transform: scale(1); } 30%{ transform: scale(1.08); } 60%{ transform: scale(1.03); } }
@keyframes ringSpin { 0%{ transform: rotate(0deg); opacity:0.6; } 100%{ transform: rotate(360deg); opacity:0.6; } }
@keyframes ringGlow { 0%,100%{ opacity:0.45; } 50%{ opacity:0.75; } }
@keyframes wave { 0%{ box-shadow:0 0 0 0 rgba(99,102,241,0.12);} 50%{ box-shadow:0 0 0 10px rgba(99,102,241,0.06);} 100%{ box-shadow:0 0 0 0 rgba(99,102,241,0.00);} }
@keyframes fadeIn { from{opacity:0; transform:translateY(-6px);} to{opacity:1; transform:translateY(0);} }
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
    .voice-panel {
        left: 50%;
        right: auto;
        transform: translateX(-50%);
        width: calc(100% - 32px);
        max-width: 100%;
    }
    .voice-panel .vp-body {
        text-align: center;
    }
    .vp-transcript {
        text-align: center;
        font-weight: 700;
    }
    .voice-panel .vp-header {
        justify-content: center;
        text-align: center;
    }
    .voice-panel .vp-status {
        margin: 8px auto 0;
    }
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
<div class="card" style="margin-bottom:20px; position:relative">
    <p class="card-title" style="margin-bottom:16px">Filtrar registros</p>
    <form method="GET" id="filterForm">
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

    <!-- Floating IA Voz button (top-right of this card) and panel -->
    <div style="position:absolute;top:12px;right:16px;z-index:40;">
        <button id="voice-float-btn" class="floating-voice-btn" type="button" aria-haspopup="dialog" aria-expanded="false" aria-label="Abrir Asistente IA" title="Abrir Asistente IA">
            <span class="fv-icon" aria-hidden="true">
                <!-- Google-style search-by-voice mic icon (white) -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="none" aria-hidden="true">
                    <path d="M12 14a3.5 3.5 0 0 0 3.5-3.5V6.5A3.5 3.5 0 0 0 12 3a3.5 3.5 0 0 0-3.5 3.5v4A3.5 3.5 0 0 0 12 14z" fill="#fff"/>
                    <path d="M19 11a7 7 0 0 1-14 0" stroke="#fff" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 17v3" stroke="#fff" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
            </span>
            <span class="fv-label">Asistente IA</span>
        </button>
    </div>

    <div id="voice-panel" class="voice-panel" style="display:none;">
        <div class="vp-header">
            <div>
                <div class="vp-title">Asistente IA</div>
            </div>
            <div class="vp-status" id="voice-panel-status">Inactivo</div>
        </div>
        <div class="vp-body">
            <div class="assistant-sphere-container">
                <div id="assistant-sphere" class="assistant-sphere idle" aria-hidden="true">
                    <div class="sphere-ring ring-outer"></div>
                    <div class="sphere-ring ring-inner"></div>
                    <div class="sphere-core"></div>
                </div>
            </div>
            <div class="vp-transcript" id="voice-transcript">Habla ahora...</div>
            <div id="voice-feedback" class="vp-feedback" aria-live="polite"></div>
        </div>
    </div>

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

@push('scripts')
<script>
(function(){
    const form = document.getElementById('filterForm');
    const voiceBtn = document.getElementById('voice-float-btn');
    const voicePanel = document.getElementById('voice-panel');
    const panelStatus = document.getElementById('voice-panel-status');
    const transcriptEl = document.getElementById('voice-transcript');
    const feedbackEl = document.getElementById('voice-feedback');
    const sphereEl = document.getElementById('assistant-sphere');
    if(!form || !voiceBtn || !voicePanel) return;

    function setSphereState(state){
        if(!sphereEl) return;
        sphereEl.classList.remove('idle','listening','processing','speaking');
        sphereEl.classList.add(state || 'idle');
    }

    const accionSelect = form.querySelector('select[name="accion"]');
    const rolSelect = form.querySelector('select[name="rol"]');
    const fechaInput = form.querySelector('input[name="fecha"]');

    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    const supportsSR = !!SpeechRecognition;
    if(!supportsSR){
        panelStatus.textContent = 'Reconocimiento no soportado';
        voiceBtn.title = 'Reconocimiento de voz no disponible en este navegador';
    }

    let recognizing = false;
    let inactivityTimer = null;
    let dotsTimer = null;
    let recognition = null;
    let finalTranscript = '';
    if(supportsSR){
        recognition = new SpeechRecognition();
        recognition.lang = 'es-ES';
        recognition.interimResults = true;
        recognition.maxAlternatives = 1;
    }

    function showPanel(){ voicePanel.style.display = 'block'; voiceBtn.setAttribute('aria-expanded','true'); setSphereState('idle'); }
    function hidePanel(){
        clearTimeout(inactivityTimer);
        inactivityTimer = null;
        if(recognition){
            try{ recognition.stop(); } catch(e){}
        }
        voicePanel.style.display = 'none';
        voiceBtn.setAttribute('aria-expanded','false');
        transcriptEl.textContent = 'Habla ahora...';
        panelStatus.textContent = 'Inactivo';
        feedbackEl.innerHTML = '';
        voicePanel.classList.remove('listening');
        setSphereState('idle');
        stopDotsAnimation();
    }

    voiceBtn.addEventListener('click', ()=>{
        if(voicePanel.style.display === 'block'){
            if(recognizing && recognition) recognition.stop(); else startListening();
        } else { showPanel(); startListening(); }
    });

    function updateSourceIndicator(src){
        // ahora solo usamos el estado visual del panel y no el punto en el botón.
        // Esto permite mantener el botón compacto sin elementos extra.
    }

    async function startListening(){
        finalTranscript = '';
        transcriptEl.textContent = '';
        if(!recognition){
            panelStatus.textContent = 'Reconocimiento no disponible en este navegador.';
            feedbackEl.innerHTML = '<div class="err">Tu navegador no soporta reconocimiento de voz. Prueba Chrome o Edge.</div>';
            inactivityTimer = setTimeout(()=> hidePanel(), 3000);
            return;
        }
        panelStatus.innerHTML = 'Escuchando<span class="dots">...</span>';
        setSphereState('listening');
        startDotsAnimation();
        voicePanel.classList.add('listening');
        try{ recognition.start(); }
        catch(err){
            console.warn('recognition.start() failed:', err);
            try{ await navigator.mediaDevices.getUserMedia({ audio: true }); recognition.start(); }
            catch(e2){ console.error('getUserMedia fallback failed:', e2); panelStatus.textContent = 'Permiso de micrófono denegado'; feedbackEl.innerHTML = '<div class="err">No se pudo acceder al micrófono. Verifica permisos del navegador.</div>'; stopDotsAnimation(); voicePanel.classList.remove('listening'); inactivityTimer = setTimeout(()=> hidePanel(), 3000); return; }
        }
        clearTimeout(inactivityTimer);
    }

    function stopListening(){ try{ if(recognition) recognition.stop(); }catch(e){} }

    if(recognition){
        recognition.onstart = ()=>{ recognizing = true; panelStatus.innerHTML = 'Escuchando<span class="dots">...</span>'; voicePanel.classList.add('listening'); setSphereState('listening'); };
        recognition.onresult = (event)=>{ let interim=''; for(let i=event.resultIndex;i<event.results.length;i++){ const res=event.results[i]; if(res.isFinal){ finalTranscript += res[0].transcript + ' '; } else { interim += res[0].transcript; } } const textSoFar = (finalTranscript + interim).trim(); transcriptEl.textContent = textSoFar || '...'; clearTimeout(inactivityTimer); inactivityTimer = setTimeout(()=>{ stopListening(); }, 5000); };
        recognition.onerror = (e)=>{ recognizing=false; voicePanel.classList.remove('listening'); stopDotsAnimation(); panelStatus.textContent='Error: '+(e.error||'desconocido'); setSphereState('idle'); setTimeout(()=>{ panelStatus.textContent='Inactivo'; },2000); };
        recognition.onend = ()=>{ recognizing=false; voicePanel.classList.remove('listening'); stopDotsAnimation(); const text = finalTranscript.trim(); if(text.length){ panelStatus.textContent='Procesando...'; setSphereState('processing'); handleCommand(text); } else { panelStatus.textContent='Inactivo'; setSphereState('idle'); inactivityTimer = setTimeout(()=> hidePanel(),2500); } };
    }

    async function handleCommand(text){
        const trimmed = text.trim();

        if (isGreeting(trimmed.toLowerCase())) {
            const greetingReply = '¡Hola! Estoy lista para ayudarte con reportes y datos de propiedades.';
            panelStatus.textContent = 'Procesando...';
            await playPollySpeech(greetingReply);
            return;
        }

        if (trimmed.length < 3) {
            const shortReplies = [
                'No capté bien tu consulta. ¿Puedes decirme algo sobre propiedades o actividad?',
                'No entendí claramente. Intenta preguntarme por un reporte de propiedades o actividad.',
                'Lo siento, no escuché bien. Cuéntame qué necesitas saber de los reportes.'
            ];
            const shortReply = shortReplies[Math.floor(Math.random() * shortReplies.length)];
            feedbackEl.innerHTML = '<div class="err">' + escapeHtml(shortReply) + '</div>';
            await playPollySpeech(shortReply);
            return;
        }

        fetchVoiceQuery(trimmed);
    }

    function isGreeting(text){
        const normalized = text
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[¿?!.]/g, '')
            .trim()
            .toLowerCase();

        const greetings = [
            'hola',
            'hola como estas',
            'hola como estas',
            'hola como estas',
            'hola como estas',
            'hola como estas',
            'hola que tal',
            'hola que tal',
            'hola que tal',
            'como estas',
            'como estas',
            'como estas',
            'que tal',
            'buenos dias',
            'buenas tardes',
            'buenas noches',
            'buenos dias',
            'buenas tardes',
            'buenas noches'
        ];

        return greetings.includes(normalized);
    }

    async function fetchAndSpeakReport(){ panelStatus.textContent='Obteniendo reporte...'; try{ const res = await fetch('/voice/report/propiedades',{ headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}, credentials:'same-origin' }); if(!res.ok) throw new Error('HTTP '+res.status); const data = await res.json(); const speech = buildSpeechFromReport(data); updateSourceIndicator('polly'); await playPollySpeech(speech); }catch(e){ panelStatus.textContent = 'Error'; } }

    function getCsrfToken(){ const tokenMeta = document.querySelector('meta[name="csrf-token"]'); if(tokenMeta){ return tokenMeta.getAttribute('content'); } const match = document.cookie.match(/(^|;)\s*XSRF-TOKEN=([^;]+)/); return match ? decodeURIComponent(match[2]) : null; }

    async function playPollySpeech(text){
        if(!text) return;
        const token = getCsrfToken();
        if(!token){
            console.error('No CSRF token available for Polly');
            panelStatus.textContent = 'Error';
            return;
        }

        try{
            const res = await fetch('/voice/polly', {
                method:'POST',
                headers:{
                    'X-Requested-With':'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                    'Accept':'application/json',
                    'Content-Type':'application/json'
                },
                body: JSON.stringify({ text }),
                credentials:'same-origin'
            });
            const data = await res.json();
            if(!res.ok || !data.audio){
                console.error('Polly response failed', res.status, data);
                panelStatus.textContent = 'Error';
                throw new Error(data.error || 'Fallo Polly');
            }
            const audio = new Audio('data:audio/mpeg;base64,' + data.audio);
            audio.onended = ()=>{ panelStatus.textContent = 'Listo'; setSphereState('idle'); inactivityTimer = setTimeout(()=> hidePanel(), 1200); };
            audio.onerror = (err)=>{ console.error('Audio playback error', err); panelStatus.textContent = 'Error'; setSphereState('idle'); inactivityTimer = setTimeout(()=> hidePanel(), 1200); };
            await audio.play();
            panelStatus.textContent = 'Hablando...';
            setSphereState('speaking');
        }catch(e){
            console.error('Polly playback failed', e);
            panelStatus.textContent = 'Error';
        }
    }

    async function fetchVoiceQuery(q){
        clearTimeout(inactivityTimer);
        panelStatus.textContent = 'Consultando Gemini...';
        try{
            const token = getCsrfToken();
            if(!token){
                feedbackEl.innerHTML = '<div class="err">Error interno: faltó token CSRF.</div>';
                panelStatus.textContent = 'Error';
                console.error('No CSRF token available');
                inactivityTimer = setTimeout(()=> hidePanel(), 3000);
                return;
            }
            panelStatus.textContent = 'Procesando...';
            const res = await fetch('/voice/gemini', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ q }),
                credentials: 'same-origin'
            });
            let data = null;
            try { data = await res.json(); } catch(e){ data = null; }
            if(!res.ok){
                const detail = data ? JSON.stringify(data) : (res.status + ' ' + res.statusText);
                feedbackEl.innerHTML = '<div class="err">Error consultando Gemini: ' + escapeHtml(detail) + '</div>';
                panelStatus.textContent = 'Error';
                console.error('Gemini voice query error', res.status, data);
                inactivityTimer = setTimeout(()=> hidePanel(), 3000);
                return;
            }
            const reply = data && data.speech ? data.speech : '';
            const source = data && data.source ? data.source : 'gemini';
            console.debug('Gemini voice response', { source, payload: data });
            updateSourceIndicator(source);
            if(reply){
                await playPollySpeech(reply);
            } else {
                const errorMsg = data && data.error ? data.error : 'Sin respuesta válida.';
                console.error('Gemini returned no speech', data);
                panelStatus.textContent = 'Error';
                inactivityTimer = setTimeout(()=> hidePanel(), 3000);
            }
        }catch(e){
            console.error(e);
            feedbackEl.innerHTML = '<div class="err">Error consultando Gemini: ' + escapeHtml(e.message || String(e)) + '</div>';
            panelStatus.textContent = 'Error';
            inactivityTimer = setTimeout(()=> hidePanel(), 3000);
        }
    }

    function buildSpeechFromReport(data){ const total = data.totalProps ?? 0; const hoy = data.totalHoy ?? 0; let text = `Hay ${total} propiedades registradas.`; if(hoy>0) text += ` Hoy se registraron ${hoy} propiedades.`; if(Array.isArray(data.latest) && data.latest.length>0){ text += ' Últimas propiedades registradas: '; const items = data.latest.map((r,idx)=>{ const desc = r.descripcion ? r.descripcion.replace(/\s+/g,' ').slice(0,80) : ''; return `${idx+1}: ${desc}`; }); text += items.join('; '); } return text; }

    async function speak(text){ await playPollySpeech(text); }

    function startDotsAnimation(){ const dotEl = panelStatus.querySelector('.dots'); if(!dotEl) return; let n=0; dotEl.textContent=''; dotsTimer = setInterval(()=>{ n=(n+1)%4; dotEl.textContent='.'.repeat(n); },400); }
    function stopDotsAnimation(){ if(dotsTimer){ clearInterval(dotsTimer); dotsTimer=null; const dotEl=panelStatus.querySelector('.dots'); if(dotEl) dotEl.textContent=''; } }
    function setSelectValueByText(select,text){ if(!select) return; for(const opt of select.options){ if(opt.value===text){ select.value=opt.value; return;} if(opt.text.toLowerCase().includes(text)){ select.value=opt.value; return;} } }
    function setSelectLikeText(select,fragment){ if(!select) return; const frag=fragment.toLowerCase(); for(const opt of select.options){ if(opt.text.toLowerCase().includes(frag) || opt.value.toLowerCase().includes(frag)){ select.value=opt.value; return; } } }
    function formatDate(d){ const mm=('0'+(d.getMonth()+1)).slice(-2); const dd=('0'+d.getDate()).slice(-2); return d.getFullYear()+'-'+mm+'-'+dd; }
    function escapeHtml(s){ return s.replace(/[&<>"']/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }); }

})();
</script>
@endpush