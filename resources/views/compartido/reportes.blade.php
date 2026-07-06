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
.floating-voice-btn { display:inline-flex; align-items:center; gap:6px; background:linear-gradient(135deg,#5b21b6,#4338ca); color:#fff; border:none; padding:10px 14px; border-radius:22px; font-weight:700; box-shadow:0 18px 48px rgba(88,70,204,0.30), 0 0 0 1px rgba(167,139,250,0.16); cursor:pointer; transition:transform .18s ease, box-shadow .18s ease, background .18s ease, filter .18s ease; }
.floating-voice-btn:hover { transform:translateY(-2px); box-shadow:0 22px 52px rgba(88,70,204,0.38), 0 0 0 2px rgba(167,139,250,0.20); background:linear-gradient(135deg,#4f46e5,#4338ca); filter:brightness(1.05); }
.floating-voice-btn .fv-icon { display:inline-flex; align-items:center; justify-content:center; width:22px; height:22px; border-radius:50%; background:rgba(255,255,255,0.20); box-shadow: inset 0 2px 6px rgba(255,255,255,0.35); }
.floating-voice-btn .fv-label { font-size:13px; color:#fff; }
.voice-panel { position:fixed; top:84px; right:28px; width:min(410px, calc(100vw - 36px)); background:#07143c; color:#eef2ff; border-radius:26px; box-shadow:0 24px 70px rgba(20,42,104,0.28), 0 8px 22px rgba(14,28,78,0.16); z-index:1000; overflow:hidden; animation:panelReveal .24s cubic-bezier(.2,.8,.2,1); border:1px solid rgba(88,112,218,0.42); backdrop-filter: blur(22px); transform-origin:88% 0; }
.voice-panel::before { content:''; position:absolute; inset:0; background:linear-gradient(90deg, rgba(255,255,255,0.22), transparent 18%, transparent 82%, rgba(255,255,255,0.10)); opacity:.22; pointer-events:none; }
.voice-panel::after { content:''; position:absolute; inset:auto 0 0; height:54%; background:linear-gradient(180deg, transparent, rgba(41,89,194,0.32)); pointer-events:none; }
.voice-panel.dark { background:#07143c; color:#eef2ff; }
.voice-panel .vp-header { position:relative; z-index:2; min-height:72px; display:flex; align-items:center; justify-content:space-between; gap:14px; padding:18px 22px; background:linear-gradient(135deg, #426ee8 0%, #5360dc 54%, #6545c4 100%); color:#fff; box-shadow: inset 0 -1px 0 rgba(0,0,0,0.16); border-bottom:1px solid rgba(5,15,55,0.38); }
.voice-panel .vp-header::before { content:''; position:absolute; inset:0; background:radial-gradient(circle at 18% 0%, rgba(255,255,255,0.30), transparent 31%), radial-gradient(circle at 92% 28%, rgba(183,163,255,0.28), transparent 28%); pointer-events:none; }
.voice-panel .vp-header::after { content:''; position:absolute; left:0; right:0; top:0; height:2px; background:linear-gradient(90deg, transparent, rgba(255,255,255,0.82), transparent); opacity:.55; pointer-events:none; }
.voice-panel.dark .vp-header { background:linear-gradient(135deg, rgba(19,83,184,0.94), rgba(54,103,212,0.90)); }
.voice-panel .vp-title { position:relative; z-index:1; font-weight:800; font-size:20px; line-height:1; letter-spacing:0; }
.voice-panel .vp-subtitle { font-size:12px; color:rgba(255,255,255,0.92); margin-top:3px; }
.voice-panel .vp-status { position:relative; z-index:1; font-size:14px; line-height:1; color:#fff; font-weight:800; background:rgba(255,255,255,0.16); border:1px solid rgba(255,255,255,0.10); padding:12px 18px; border-radius:999px; white-space:nowrap; backdrop-filter: blur(12px); box-shadow:inset 0 1px 0 rgba(255,255,255,0.16); }
.voice-panel .vp-body { position:relative; z-index:1; min-height:310px; padding:0 22px 24px; display:grid; grid-template-rows:1fr auto; gap:4px; text-align:center; background:linear-gradient(180deg, #081236 0%, #142861 100%); overflow:hidden; }
.voice-panel .vp-body::before { content:''; position:absolute; inset:0; background:radial-gradient(circle at 50% 40%, rgba(78,91,245,0.30), transparent 34%), radial-gradient(circle at 50% 56%, rgba(32,169,255,0.18), transparent 46%), radial-gradient(circle at 88% 24%, rgba(151,64,255,0.24), transparent 18%), radial-gradient(circle at 12% 36%, rgba(41,213,255,0.14), transparent 22%); pointer-events:none; }
.voice-panel .vp-body::after { content:''; position:absolute; inset:16px 16px 72px; background-image:radial-gradient(circle, rgba(255,255,255,0.86) 0 1px, transparent 1.5px), radial-gradient(circle, rgba(97,145,255,0.72) 0 1.2px, transparent 1.8px); background-size:58px 64px, 82px 70px; background-position:5px 12px, 31px 4px; opacity:.20; animation:starDrift 12s linear infinite; pointer-events:none; }
.assistant-sphere-container { position:relative; display:flex; justify-content:center; align-items:center; min-height:232px; padding-top:8px; isolation:isolate; }
.assistant-sphere-container::before { content:''; position:absolute; left:50%; top:52%; width:118%; height:54%; transform:translate(-50%, -50%); background:radial-gradient(ellipse at center, rgba(88,99,255,0.42), rgba(42,75,181,0.16) 42%, transparent 71%); border-radius:50%; filter:blur(2px); pointer-events:none; }
.assistant-sphere { position:relative; width:282px; height:218px; display:flex; align-items:center; justify-content:center; margin:0 auto; }
.assistant-sphere::before { content:''; position:absolute; width:226px; height:226px; border-radius:50%; background:repeating-radial-gradient(circle, transparent 0 22px, rgba(96,129,255,0.24) 23px 24px, transparent 25px 34px); opacity:.66; animation:ringSpin 22s linear infinite; pointer-events:none; }
.assistant-sphere::after { content:''; position:absolute; width:292px; height:164px; border-radius:50%; border:1px solid rgba(113,88,255,0.24); box-shadow:0 0 24px rgba(80,106,255,0.28), inset 0 0 22px rgba(28,219,255,0.08); transform:rotate(-12deg); animation:orbitTilt 8s ease-in-out infinite; pointer-events:none; }
.sphere-core { width:124px; height:124px; position:relative; z-index:4; border-radius:50%; filter:drop-shadow(0 0 18px rgba(117,113,255,0.50)); }
.sphere-blob { position:absolute; inset:0; border-radius:50%; background:radial-gradient(circle at 28% 20%, #ffffff 0 10%, #e5f0ff 11% 18%, rgba(126,188,255,0.92) 29%, rgba(121,111,246,0.95) 48%, rgba(242,98,255,0.78) 72%, rgba(103,51,230,0.86) 100%); box-shadow:0 0 0 2px rgba(255,255,255,0.56), 0 0 34px rgba(255,255,255,0.44), 0 0 70px rgba(94,88,255,0.56), inset -22px -24px 42px rgba(78,33,175,0.36), inset 16px 18px 36px rgba(255,255,255,0.36); animation:blobMorph 6s ease-in-out infinite, orbGlow 2.6s ease-in-out infinite; }
.sphere-blob::before { content:''; position:absolute; width:40px; height:30px; top:15px; left:20px; border-radius:50%; background:rgba(255,255,255,0.92); filter:blur(5px); transform:rotate(-30deg); }
.sphere-blob::after { content:''; position:absolute; width:34px; height:24px; right:20px; bottom:29px; border-radius:50%; background:rgba(255,199,255,0.52); filter:blur(7px); }
.sphere-ring { position:absolute; z-index:2; border-radius:50%; opacity:.72; pointer-events:none; }
.ring-outer { width:226px; height:226px; border:1px solid rgba(92,127,255,0.32); box-shadow:0 0 24px rgba(73,106,255,0.22); animation:ringSpin 15s linear infinite; }
.ring-inner { width:168px; height:168px; border:1px solid rgba(80,216,255,0.24); box-shadow:inset 0 0 18px rgba(75,107,255,0.22); animation:ringGlow 3.2s ease-in-out infinite; }
.sphere-particle { position:absolute; z-index:3; border-radius:50%; background:rgba(255,255,255,0.92); box-shadow:0 0 14px rgba(255,255,255,0.76), 0 0 32px rgba(58,207,255,0.50); opacity:.95; pointer-events:none; }
.particle-1 { width:7px; height:7px; top:18%; left:14%; animation:particleFloat 5.5s ease-in-out infinite; }
.particle-2 { width:5px; height:5px; top:12%; right:17%; animation:particleFloat 4.8s ease-in-out infinite .8s; }
.particle-3 { width:6px; height:6px; bottom:21%; left:12%; animation:particleFloat 6.4s ease-in-out infinite .5s; }
.particle-4 { width:7px; height:7px; bottom:22%; right:13%; animation:particleFloat 5.9s ease-in-out infinite .3s; }
.particle-5 { width:5px; height:5px; top:47%; left:3%; animation:particleFloat 4.2s ease-in-out infinite .6s; }
.sphere-wave { position:absolute; z-index:3; left:50%; top:50%; height:3px; border-radius:999px; background:linear-gradient(90deg, transparent 0%, rgba(62,219,255,0.14) 7%, rgba(60,231,255,0.98) 24%, rgba(255,255,255,0.98) 48%, rgba(255,86,255,0.98) 74%, rgba(103,88,255,0.22) 93%, transparent 100%); box-shadow:0 0 16px rgba(61,226,255,0.78), 0 0 28px rgba(235,83,255,0.44); opacity:.96; filter:blur(.2px); pointer-events:none; }
.wave-1 { width:330px; transform:translateX(-50%); animation:wavePulse 2.2s ease-in-out infinite; }
.wave-2 { width:296px; transform:translateX(-50%) translateY(-7px) rotate(-2deg); opacity:.52; animation:wavePulseAlt 2.7s ease-in-out infinite .22s; }
.wave-3 { width:310px; transform:translateX(-50%) translateY(8px) rotate(2deg); opacity:.44; animation:wavePulseAlt 2.5s ease-in-out infinite .45s; }
.assistant-sphere.idle .sphere-blob { animation: blobMorph 6s ease-in-out infinite; }
.assistant-sphere.listening .sphere-blob { animation: blobMorph 5s ease-in-out infinite; }
.assistant-sphere.processing .sphere-blob { animation: blobMorph 4.2s ease-in-out infinite; }
.assistant-sphere.speaking .sphere-blob { animation: blobMorph 3.8s ease-in-out infinite; }
.assistant-sphere.listening .sphere-core { animation: listeningPulse 1.2s ease-in-out infinite; }
.assistant-sphere.processing .sphere-core { animation: thinkingPulse 1.8s ease-in-out infinite; }
.assistant-sphere.speaking .sphere-core { animation: speakingPulse 1.2s ease-in-out infinite; background: radial-gradient(circle at 35% 30%, #ffffff 0%, #ede9fe 16%, #c4b5fd 44%, #7c3aed 72%, #312e81 100%); box-shadow: 0 0 0 24px rgba(192,132,252,0.24), 0 28px 64px rgba(79,70,229,0.32), inset 0 0 48px rgba(255,255,255,0.52); transform: scale(1.04); }
.vp-transcript { position:relative; z-index:2; display:flex; align-items:center; justify-content:center; min-height:50px; color:#fff; font-size:21px; line-height:1.2; text-align:center; font-weight:500; text-shadow:0 8px 24px rgba(0,0,0,0.20); overflow:hidden; }
.vp-feedback { display:none !important; }
.vp-feedback .ok { background:rgba(16,185,129,0.14); color:#d1fae5; padding:8px 10px; border-radius:12px; display:inline-block; }
.vp-feedback .err { background:rgba(248,113,113,0.14); color:#fee2e2; padding:8px 10px; border-radius:12px; display:inline-block; }
.source-note { display:none; }
@keyframes idleGlow { 0%,100%{ transform: scale(1); box-shadow: 0 0 0 16px rgba(99,102,241,0.10), 0 18px 48px rgba(79,70,229,0.22); } 50%{ transform: scale(1.03); box-shadow: 0 0 0 24px rgba(99,102,241,0.12), 0 24px 56px rgba(79,70,229,0.28); } }
@keyframes listeningPulse { 0%,100%{ transform: scale(1); } 50%{ transform: scale(1.08); } }
@keyframes thinkingPulse { 0%,100%{ transform: scale(1); } 40%{ transform: scale(1.07); } 80%{ transform: scale(1.03); } }
@keyframes speakingPulse { 0%,100%{ transform: scale(1); } 30%{ transform: scale(1.09); } 60%{ transform: scale(1.05); } }
@keyframes ringSpin { 0%{ transform: rotate(0deg); opacity:0.7; } 100%{ transform: rotate(360deg); opacity:0.7; } }
@keyframes ringGlow { 0%,100%{ opacity:0.45; } 50%{ opacity:0.82; } }
@keyframes orbitTilt { 0%,100%{ transform:rotate(-12deg) scale(1); opacity:.58; } 50%{ transform:rotate(12deg) scale(1.04); opacity:.82; } }
@keyframes wavePulse { 0%,100%{ opacity:.78; transform:translateX(-50%) scaleX(.94); } 50%{ opacity:1; transform:translateX(-50%) scaleX(1.08); } }
@keyframes wavePulseAlt { 0%,100%{ filter:blur(.6px); opacity:.38; } 50%{ filter:blur(.1px); opacity:.82; } }
@keyframes orbGlow { 0%,100%{ filter:saturate(1) brightness(1); } 50%{ filter:saturate(1.2) brightness(1.08); } }
@keyframes blobMorph { 0%,100%{ transform:scale(1) rotate(0deg); }
    25%{ transform:scale(1.025) rotate(2deg); }
    50%{ transform:scale(.985) rotate(-2deg); }
    75%{ transform:scale(1.018) rotate(1deg); }
}
@keyframes particleFloat { 0%,100%{ transform: translateY(0) translateX(0); opacity:0.9; }
    50%{ transform: translateY(-14px) translateX(8px); opacity:1; }
}
@keyframes starDrift { from{ background-position:5px 12px, 31px 4px; } to{ background-position:67px 80px, 119px 78px; } }
@keyframes panelReveal { from{ opacity:0; transform:translateY(-12px) scale(.96); } to{ opacity:1; transform:translateY(0) scale(1); } }
@keyframes panelRevealMobile { from{ opacity:0; transform:translateX(-50%) translateY(-12px) scale(.96); } to{ opacity:1; transform:translateX(-50%) translateY(0) scale(1); } }
@keyframes fadeIn { from{opacity:0; transform:translateY(-8px);} to{opacity:1; transform:translateY(0);} }
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
        top: 76px;
        left: 50%;
        right: auto;
        transform: translateX(-50%);
        animation: panelRevealMobile .28s cubic-bezier(.2,.8,.2,1);
        width: calc(100% - 32px);
        max-width: 100%;
        border-radius: 28px;
    }
    .voice-panel .vp-header {
        min-height: 86px;
        padding: 20px;
        flex-direction: column;
        gap: 12px;
    }
    .voice-panel .vp-title {
        font-size: 23px;
    }
    .voice-panel .vp-status {
        padding: 13px 22px;
        font-size: 17px;
    }
    .voice-panel .vp-body {
        min-height: 330px;
        padding: 0 16px 26px;
        text-align: center;
    }
    .assistant-sphere-container {
        min-height: 230px;
    }
    .assistant-sphere {
        width: min(292px, 100%);
        height: 218px;
    }
    .sphere-core {
        width: 116px;
        height: 116px;
    }
    .ring-outer {
        width: 218px;
        height: 218px;
    }
    .ring-inner {
        width: 160px;
        height: 160px;
    }
    .wave-1,
    .wave-2,
    .wave-3 {
        width: 360px;
    }
    .vp-transcript {
        min-height: 50px;
        font-size: 21px;
        text-align: center;
        font-weight: 400;
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
            $trendRoute = "{$rolePrefix}.reportes.tendencias";
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
                <a href="{{ route($trendRoute) }}" class="btn-export btn-export-gray">
                    Ver tendencias
                </a>
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
                    <div class="sphere-particle particle-1"></div>
                    <div class="sphere-particle particle-2"></div>
                    <div class="sphere-particle particle-3"></div>
                    <div class="sphere-particle particle-4"></div>
                    <div class="sphere-particle particle-5"></div>
                    <div class="sphere-wave wave-1"></div>
                    <div class="sphere-wave wave-2"></div>
                    <div class="sphere-wave wave-3"></div>
                    <div class="sphere-ring ring-outer"></div>
                    <div class="sphere-ring ring-inner"></div>
                    <div class="sphere-core">
                        <div class="sphere-blob"></div>
                    </div>
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
    const audioCache = new Map();
    const voiceExportUrls = {
        pdf: @json(route($exportRoute, array_merge(request()->query(), ['type' => 'pdf']))),
        excel: @json(route($exportRoute, array_merge(request()->query(), ['type' => 'xlsx']))),
        csv: @json(route($exportRoute, array_merge(request()->query(), ['type' => 'csv']))),
    };
    if(supportsSR){
        recognition = new SpeechRecognition();
        recognition.lang = 'es-ES';
        recognition.interimResults = true;
        recognition.maxAlternatives = 1;
    }

    function showPanel(){
        voicePanel.style.display = 'block';
        document.body.classList.add('voice-panel-open');
        voiceBtn.setAttribute('aria-expanded','true');
        setSphereState('idle');
    }
    function hidePanel(){
        clearTimeout(inactivityTimer);
        inactivityTimer = null;
        if(recognition){
            try{ recognition.stop(); } catch(e){}
        }
        voicePanel.style.display = 'none';
        document.body.classList.remove('voice-panel-open');
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
        recognition.onresult = (event)=>{ let interim=''; for(let i=event.resultIndex;i<event.results.length;i++){ const res=event.results[i]; if(res.isFinal){ finalTranscript += res[0].transcript + ' '; } else { interim += res[0].transcript; } } const textSoFar = (finalTranscript + interim).trim(); transcriptEl.textContent = textSoFar || '...'; clearTimeout(inactivityTimer); inactivityTimer = setTimeout(()=>{ stopListening(); }, 1200); };
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

        const requestedExport = detectExportCommand(trimmed);
        if (requestedExport) {
            await downloadReportByVoice(requestedExport);
            return;
        }

        fetchVoiceQuery(trimmed);
    }

    function normalizeVoiceText(text){
        return text
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[¿?!.]/g, '')
            .trim()
            .toLowerCase();
    }

    function detectExportCommand(text){
        const normalized = normalizeVoiceText(text);
        const wantsDownload = ['descarga', 'descargar', 'exporta', 'exportar', 'baja', 'generar', 'genera'].some(word => normalized.includes(word));
        const wantsReport = ['reporte', 'reportes', 'informe', 'actividad'].some(word => normalized.includes(word));
        if (!wantsDownload || !wantsReport) return null;
        if (normalized.includes('excel') || normalized.includes('xlsx')) return 'excel';
        if (normalized.includes('csv')) return 'csv';
        if (normalized.includes('pdf')) return 'pdf';
        return 'pdf';
    }

    async function downloadReportByVoice(type){
        const url = voiceExportUrls[type] || voiceExportUrls.pdf;
        const label = type === 'excel' ? 'Excel' : type.toUpperCase();
        panelStatus.textContent = 'Preparando descarga...';
        window.open(url, '_blank');
        await playPollySpeech('Listo. Estoy descargando el reporte en ' + label + '.');
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
        const cachedAudio = audioCache.get(text);
        if(cachedAudio){
            playAudioSource(cachedAudio);
            return;
        }
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
            const audioSource = 'data:audio/mpeg;base64,' + data.audio;
            audioCache.set(text, audioSource);
            playAudioSource(audioSource);
        }catch(e){
            console.error('Polly playback failed', e);
            panelStatus.textContent = 'Error';
        }
    }

    async function playAudioSource(audioSource){
        const audio = new Audio(audioSource);
        audio.onended = ()=>{ panelStatus.textContent = 'Listo'; setSphereState('idle'); inactivityTimer = setTimeout(()=> hidePanel(), 1200); };
        audio.onerror = (err)=>{ console.error('Audio playback error', err); panelStatus.textContent = 'Error'; setSphereState('idle'); inactivityTimer = setTimeout(()=> hidePanel(), 1200); };
        panelStatus.textContent = 'Hablando...';
        setSphereState('speaking');
        try { await audio.play(); }
        catch(e){ console.error('Audio play failed', e); panelStatus.textContent = 'Error'; setSphereState('idle'); }
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
                body: JSON.stringify({
                    q,
                    accion: accionSelect ? accionSelect.value : 'todas',
                    rol: rolSelect ? rolSelect.value : 'todos',
                    fecha: fechaInput ? fechaInput.value : ''
                }),
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
