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
.floating-voice-btn { display:inline-flex; align-items:center; gap:8px; background:linear-gradient(90deg,#4f46e5,#6366f1); color:#fff; border:none; padding:6px 10px; border-radius:10px; font-weight:700; box-shadow:0 8px 20px rgba(79,70,229,0.16); cursor:pointer; }
.floating-voice-btn .fv-icon { display:inline-flex; align-items:center; justify-content:center; width:20px; height:20px; }
.floating-voice-btn .fv-label { font-size:13px; color:#fff; }
.voice-panel { position:fixed; top:80px; right:28px; width:320px; max-width:calc(100% - 48px); background:#fff; color:#0b1220; border-radius:12px; box-shadow:0 20px 50px rgba(2,6,23,0.24); z-index:1000; overflow:hidden; animation:fadeIn .18s ease; }
.voice-panel.dark { background:#0b1220; color:#fff; }
.voice-panel .vp-header { display:flex; align-items:center; justify-content:space-between; padding:12px 14px; border-bottom:1px solid rgba(15,23,42,0.04); }
.voice-panel.dark .vp-header { border-bottom-color: rgba(255,255,255,0.04); }
.voice-panel .vp-title { font-weight:800; }
.voice-panel .vp-status { font-size:13px; color:#6b7280; }
.voice-panel .vp-body { padding:12px 14px; display:grid; gap:10px; }
.mic-visual { display:flex; align-items:center; gap:10px; }
.mic-circle { width:44px; height:44px; border-radius:50%; background:rgba(99,102,241,0.12); display:flex; align-items:center; justify-content:center; }
.mic-waves { width:48px; height:44px; position:relative; }
.mic-waves::after, .mic-waves::before { content:''; position:absolute; left:50%; top:50%; transform:translate(-50%,-50%); border-radius:50%; }
.mic-waves::before { width:48px; height:44px; box-shadow:0 0 0 0 rgba(99,102,241,0.12); }
.mic-waves::after { width:28px; height:26px; box-shadow:0 0 0 0 rgba(99,102,241,0.10); }
.voice-panel.listening .mic-waves::before { animation:wave 1.2s infinite; }
.voice-panel.listening .mic-waves::after { animation:wave 1.2s infinite .2s; }
.vp-transcript { font-size:14px; color:#0f1724; min-height:38px; }
.vp-feedback { min-height:32px; }
.vp-feedback .ok { background:#ecfdf5; color:#065f46; padding:8px 10px; border-radius:8px; display:inline-block; }
.vp-feedback .err { background:#fff1f2; color:#9f1239; padding:8px 10px; border-radius:8px; display:inline-block; }
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
        <button id="voice-float-btn" class="floating-voice-btn" type="button" aria-haspopup="dialog" aria-expanded="false" aria-label="Abrir Reporte IA Voz" title="Abrir Reporte IA Voz">
            <span class="fv-icon" aria-hidden="true">
                <!-- Google-style search-by-voice mic icon (white) -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="none" aria-hidden="true">
                    <path d="M12 14a3.5 3.5 0 0 0 3.5-3.5V6.5A3.5 3.5 0 0 0 12 3a3.5 3.5 0 0 0-3.5 3.5v4A3.5 3.5 0 0 0 12 14z" fill="#fff"/>
                    <path d="M19 11a7 7 0 0 1-14 0" stroke="#fff" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 17v3" stroke="#fff" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
            </span>
            <span class="fv-label">Reporte IA Voz</span>
        </button>
    </div>

    <div id="voice-panel" class="voice-panel" style="display:none;">
        <div class="vp-header">
            <div class="vp-title">IA Voz</div>
            <div class="vp-status" id="voice-panel-status">Inactivo</div>
        </div>
        <div class="vp-body">
            <div class="mic-visual">
                <div class="mic-circle" id="mic-circle">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="9" y="3" width="6" height="8" rx="3" fill="#2563eb" />
                    </svg>
                </div>
                <div class="mic-waves" id="mic-waves"></div>
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
    const micWaves = document.getElementById('mic-waves');
    if(!form || !voiceBtn || !voicePanel) return;

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

    function showPanel(){ voicePanel.style.display = 'block'; voiceBtn.setAttribute('aria-expanded','true'); }
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
        stopDotsAnimation();
    }

    voiceBtn.addEventListener('click', ()=>{
        if(voicePanel.style.display === 'block'){
            if(recognizing && recognition) recognition.stop(); else startListening();
        } else { showPanel(); startListening(); }
    });

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
        recognition.onstart = ()=>{ recognizing = true; panelStatus.innerHTML = 'Escuchando<span class="dots">...</span>'; voicePanel.classList.add('listening'); };
        recognition.onresult = (event)=>{ let interim=''; for(let i=event.resultIndex;i<event.results.length;i++){ const res=event.results[i]; if(res.isFinal){ finalTranscript += res[0].transcript + ' '; } else { interim += res[0].transcript; } } transcriptEl.textContent = (finalTranscript + interim).trim() || '...'; clearTimeout(inactivityTimer); inactivityTimer = setTimeout(()=>{ stopListening(); }, 5000); };
        recognition.onerror = (e)=>{ recognizing=false; voicePanel.classList.remove('listening'); stopDotsAnimation(); panelStatus.textContent='Error: '+(e.error||'desconocido'); feedbackEl.innerHTML='<div class="err">No pude entender la voz. Intenta de nuevo.</div>'; setTimeout(()=>{ panelStatus.textContent='Inactivo'; },2000); };
        recognition.onend = ()=>{ recognizing=false; voicePanel.classList.remove('listening'); stopDotsAnimation(); const text = finalTranscript.trim(); if(text.length){ panelStatus.textContent='Procesando...'; handleCommand(text); } else { panelStatus.textContent='Inactivo'; inactivityTimer = setTimeout(()=> hidePanel(),2500); } };
    }

    function handleCommand(text){
        const trimmed = text.trim();
        feedbackEl.innerHTML = '<div class="ok">Transcripción: ' + escapeHtml(trimmed) + '</div>';

        if (isGreeting(trimmed.toLowerCase())) {
            const greetingReply = '¡Hola! Estoy lista para ayudarte con reportes y datos de propiedades.';
            feedbackEl.innerHTML = '<div class="ok">' + escapeHtml(greetingReply) + '</div>';
            speak(greetingReply);
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
            speak(shortReply);
            inactivityTimer = setTimeout(()=> hidePanel(), 3000);
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

    async function fetchAndSpeakReport(){ panelStatus.textContent='Obteniendo reporte...'; try{ const res = await fetch('/voice/report/propiedades',{ headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}, credentials:'same-origin' }); if(!res.ok) throw new Error('HTTP '+res.status); const data = await res.json(); const speech = buildSpeechFromReport(data); speak(speech); feedbackEl.innerHTML = '<div class="ok">Reporte leído por voz.</div>'; panelStatus.textContent = 'Listo'; }catch(e){ feedbackEl.innerHTML = '<div class="err">Error obteniendo reporte.</div>'; panelStatus.textContent = 'Error'; } }

    function getCsrfToken(){ const tokenMeta = document.querySelector('meta[name="csrf-token"]'); if(tokenMeta){ return tokenMeta.getAttribute('content'); } const match = document.cookie.match(/(^|;)\s*XSRF-TOKEN=([^;]+)/); return match ? decodeURIComponent(match[2]) : null; }

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
            if(reply){
                speak(reply);
                feedbackEl.innerHTML = '<div class="ok">' + escapeHtml(reply) + '</div>';
            } else {
                const errorMsg = data && data.error ? data.error : 'Sin respuesta de Gemini.';
                feedbackEl.innerHTML = '<div class="err">' + escapeHtml(errorMsg) + '</div>';
                console.error('Gemini returned no speech', data);
            }
            panelStatus.textContent = 'Listo';
            inactivityTimer = setTimeout(()=> hidePanel(), 3000);
        }catch(e){
            console.error(e);
            feedbackEl.innerHTML = '<div class="err">Error consultando Gemini: ' + escapeHtml(e.message || String(e)) + '</div>';
            panelStatus.textContent = 'Error';
            inactivityTimer = setTimeout(()=> hidePanel(), 3000);
        }
    }

    let selectedVoice = null;
    if (window.speechSynthesis) {
        window.speechSynthesis.onvoiceschanged = loadVoices;
        loadVoices();
        setTimeout(loadVoices, 500);
    }

    function loadVoices() {
        const voices = window.speechSynthesis.getVoices() || [];
        selectedVoice = findFemaleSpanishVoice(voices) || findAnySpanishVoice(voices);
    }

    function findFemaleSpanishVoice(voices) {
        const normalized = (text) => (text || '').toLowerCase();
        const femaleHint = ['female', 'woman', 'mujer', 'feminine', 'maria', 'sofia', 'lucia', 'silvia', 'ines', 'laura', 'emilia', 'valentina', 'alejandra', 'carmen', 'angela', 'marina', 'ana', 'paola', 'adriana', 'helena', 'carla', 'riana', 'laura', 'isabel', 'natalia', 'sofia'];

        return voices.find(voice => {
            const name = normalized(voice.name);
            const uri = normalized(voice.voiceURI || '');
            const lang = normalized(voice.lang);
            const isSpanish = lang.startsWith('es');
            return isSpanish && femaleHint.some(hint => name.includes(hint) || uri.includes(hint));
        });
    }

    function findAnySpanishVoice(voices) {
        return voices.find(voice => (voice.lang || '').toLowerCase().startsWith('es'));
    }

    function buildSpeechFromReport(data){ const total = data.totalProps ?? 0; const hoy = data.totalHoy ?? 0; let text = `Hay ${total} propiedades registradas.`; if(hoy>0) text += ` Hoy se registraron ${hoy} propiedades.`; if(Array.isArray(data.latest) && data.latest.length>0){ text += ' Últimas propiedades registradas: '; const items = data.latest.map((r,idx)=>{ const desc = r.descripcion ? r.descripcion.replace(/\s+/g,' ').slice(0,80) : ''; return `${idx+1}: ${desc}`; }); text += items.join('; '); } return text; }

    function speak(text){ if(!window.speechSynthesis){ feedbackEl.innerHTML='<div class="err">SpeechSynthesis no disponible.</div>'; return; } const u = new SpeechSynthesisUtterance(text); if(selectedVoice){ u.voice = selectedVoice; u.lang = selectedVoice.lang || 'es-ES'; } else { u.lang='es-ES'; } u.rate=1.15; u.pitch=1.1; u.volume=1.0; u.onend = ()=>{ inactivityTimer = setTimeout(()=> hidePanel(), 2000); }; window.speechSynthesis.cancel(); window.speechSynthesis.speak(u); }

    function startDotsAnimation(){ const dotEl = panelStatus.querySelector('.dots'); if(!dotEl) return; let n=0; dotEl.textContent=''; dotsTimer = setInterval(()=>{ n=(n+1)%4; dotEl.textContent='.'.repeat(n); },400); }
    function stopDotsAnimation(){ if(dotsTimer){ clearInterval(dotsTimer); dotsTimer=null; const dotEl=panelStatus.querySelector('.dots'); if(dotEl) dotEl.textContent=''; } }
    function setSelectValueByText(select,text){ if(!select) return; for(const opt of select.options){ if(opt.value===text){ select.value=opt.value; return;} if(opt.text.toLowerCase().includes(text)){ select.value=opt.value; return;} } }
    function setSelectLikeText(select,fragment){ if(!select) return; const frag=fragment.toLowerCase(); for(const opt of select.options){ if(opt.text.toLowerCase().includes(frag) || opt.value.toLowerCase().includes(frag)){ select.value=opt.value; return; } } }
    function formatDate(d){ const mm=('0'+(d.getMonth()+1)).slice(-2); const dd=('0'+d.getDate()).slice(-2); return d.getFullYear()+'-'+mm+'-'+dd; }
    function escapeHtml(s){ return s.replace(/[&<>"']/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }); }

})();
</script>
@endpush