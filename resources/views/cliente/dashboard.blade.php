@extends('layouts.panel')
@section('titulo', 'Inicio Cliente')
@section('titulo_pagina', 'Mi espacio')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
.client-dash {
    --bg: #f6efe5;
    --card: rgba(255,255,255,.88);
    --ink: #172033;
    --muted: #6b7280;
    --line: rgba(120,113,108,.22);
    color: var(--ink);
}
.client-dash.dark {
    --bg: #0e1624;
    --card: rgba(15,23,42,.86);
    --ink: #edf4ff;
    --muted: #a7b4c8;
    --line: rgba(148,163,184,.18);
}
.client-shell {
    background:
        radial-gradient(circle at 8% 0%, rgba(245,158,11,.22), transparent 30%),
        radial-gradient(circle at 90% 8%, rgba(14,165,233,.16), transparent 30%),
        var(--bg);
    border-radius: 30px;
    margin: -8px;
    padding: 18px;
}
.client-hero {
    background: linear-gradient(135deg, #7c2d12, #0f766e 58%, #0f2d4f 132%);
    border-radius: 30px;
    color: #fff;
    display: grid;
    gap: 18px;
    grid-template-columns: 1.2fr .8fr;
    margin-bottom: 18px;
    overflow: hidden;
    padding: 28px;
    position: relative;
}
.client-hero h1 { font-size: clamp(28px, 5vw, 46px); font-weight: 950; letter-spacing: -.05em; margin: 0 0 8px; }
.client-hero p { color: rgba(255,255,255,.78); margin: 0; max-width: 620px; }
.client-actions { align-items: end; display: flex; flex-wrap: wrap; gap: 10px; justify-content: flex-end; position: relative; z-index: 1; }
.client-btn {
    background: rgba(255,255,255,.16);
    border: 1px solid rgba(255,255,255,.28);
    border-radius: 999px;
    color: #fff;
    font-weight: 850;
    padding: 11px 16px;
    text-decoration: none;
}
.client-btn.solid { background: #fff; color: #0f2d4f; }
.client-kpis { display:grid; gap:14px; grid-template-columns:repeat(4,minmax(0,1fr)); margin-bottom:18px; }
.client-kpi, .client-card {
    background: var(--card);
    border: 1px solid var(--line);
    border-radius: 24px;
    box-shadow: 0 18px 42px rgba(15,23,42,.07);
    padding: 18px;
}
.client-kpi small { color: var(--muted); font-size: 11px; font-weight: 900; letter-spacing: .08em; text-transform: uppercase; }
.client-kpi strong { display:block; font-size: 31px; font-weight: 950; margin-top: 8px; }
.client-grid { display:grid; gap:16px; grid-template-columns:1.1fr .9fr; margin-bottom:16px; }
.client-card h3 { color: var(--ink); font-size:17px; font-weight:950; margin:0 0 14px; }
.property-row {
    align-items:center;
    border:1px solid var(--line);
    border-radius:18px;
    display:grid;
    gap:12px;
    grid-template-columns:82px 1fr auto;
    margin-bottom:10px;
    padding:10px;
}
.property-img { background:#e2e8f0; border-radius:15px; height:70px; overflow:hidden; }
.property-img img { height:100%; object-fit:cover; width:100%; }
.property-row strong { color:var(--ink); display:block; }
.property-row p { color:var(--muted); font-size:12px; margin:3px 0 0; }
.pill { background:#e0f2fe; border-radius:999px; color:#0369a1; font-size:12px; font-weight:850; padding:6px 10px; white-space:nowrap; }
#clientMap { border-radius:20px; height:360px; overflow:hidden; }
.timeline { display:grid; gap:10px; }
.timeline-item { border-left:3px solid #0f766e; color:var(--ink); padding:8px 0 8px 12px; }
.timeline-item p { color:var(--muted); font-size:12px; margin:3px 0 0; }
.client-popup { width:210px; }
.client-popup-img { background:#e2e8f0; border-radius:12px; height:105px; margin-bottom:8px; overflow:hidden; }
.client-popup-img img { height:100%; object-fit:cover; width:100%; }
@media (max-width: 1050px) {
    .client-hero, .client-grid { grid-template-columns:1fr; }
    .client-actions { justify-content:flex-start; }
    .client-kpis { grid-template-columns:repeat(2,minmax(0,1fr)); }
}
@media (max-width: 620px) {
    .client-kpis { grid-template-columns:1fr; }
    .property-row { grid-template-columns:1fr; }
}
</style>
@endpush

@section('contenido')
<div class="client-dash" id="clientDash">
    <div class="client-shell">
        <section class="client-hero">
            <div style="position:relative;z-index:1">
                <h1>Hola, {{ explode(' ', auth()->user()->nombre)[0] }}</h1>
                <p>Tu espacio para descubrir propiedades, seguir tus visitas y recibir sugerencias segun tu actividad reciente.</p>
            </div>
            <div class="client-actions">
                <a class="client-btn solid" href="{{ route('cliente.recomendaciones') }}">Ver recomendaciones</a>
                <a class="client-btn" href="{{ route('cliente.mapa') }}">Explorar mapa</a>
                <button class="client-btn" id="clientThemeBtn" type="button">Modo oscuro</button>
            </div>
        </section>

        <section class="client-kpis">
            <article class="client-kpi"><small>Disponibles</small><strong class="count-up" data-value="{{ $totalDisp }}">0</strong></article>
            <article class="client-kpi"><small>En venta</small><strong class="count-up" data-value="{{ $totalVenta }}">0</strong></article>
            <article class="client-kpi"><small>En alquiler</small><strong class="count-up" data-value="{{ $totalAlquiler }}">0</strong></article>
            <article class="client-kpi"><small>Mis visitas</small><strong class="count-up" data-value="{{ $visitas->count() }}">0</strong></article>
        </section>

        <section class="client-grid">
            <div class="client-card">
                <h3>Recomendadas para ti</h3>
                @forelse($recomendaciones as $rec)
                    @php($p = $rec->propiedad)
                    @if($p)
                    <div class="property-row">
                        <div class="property-img">@if($p->imagen)<img src="{{ $p->imagen_url }}" alt="{{ $p->titulo }}">@endif</div>
                        <div>
                            <strong>{{ $p->titulo }}</strong>
                            <p>{{ $p->zona }} · {{ $p->tipo }} · Bs {{ number_format($p->precio, 0, ',', '.') }}</p>
                        </div>
                        <a class="pill" href="{{ route('cliente.propiedades.detalle', $p) }}">Ver</a>
                    </div>
                    @endif
                @empty
                    @foreach($propiedades as $p)
                    <div class="property-row">
                        <div class="property-img">@if($p->imagen)<img src="{{ $p->imagen_url }}" alt="{{ $p->titulo }}">@endif</div>
                        <div>
                            <strong>{{ $p->titulo }}</strong>
                            <p>{{ $p->zona }} · {{ $p->tipo }} · Bs {{ number_format($p->precio, 0, ',', '.') }}</p>
                        </div>
                        <a class="pill" href="{{ route('cliente.propiedades.detalle', $p) }}">Ver</a>
                    </div>
                    @endforeach
                @endforelse
            </div>

            <div class="client-card">
                <h3>Estado de mis visitas</h3>
                <div class="timeline">
                    @forelse($visitas as $v)
                        <div class="timeline-item">
                            <strong>{{ $v->propiedad->titulo ?? 'Propiedad' }} · {{ ucfirst($v->estado) }}</strong>
                            <p>{{ optional($v->fecha_solicitada)->format('d/m/Y H:i') ?? $v->fecha_solicitada }}</p>
                        </div>
                    @empty
                        <div class="timeline-item"><strong>Aun no tienes visitas</strong><p>Cuando solicites una visita, aparecera aqui.</p></div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="client-grid">
            <div class="client-card">
                <h3>Mapa de propiedades sugeridas</h3>
                <div id="clientMap"></div>
            </div>
            <div class="client-card">
                <h3>Historial reciente</h3>
                <div class="timeline">
                    @forelse($historial as $h)
                        <div class="timeline-item">
                            <strong>{{ str_replace('_', ' ', ucfirst($h->accion)) }}</strong>
                            <p>{{ $h->propiedad->titulo ?? 'Propiedad' }} · {{ optional($h->fecha_accion)->format('d/m/Y H:i') }}</p>
                        </div>
                    @empty
                        <div class="timeline-item"><strong>Sin historial todavia</strong><p>Explora propiedades para personalizar tus sugerencias.</p></div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('clientDash');
    const themeBtn = document.getElementById('clientThemeBtn');
    const mapData = @json($mapaPropiedades);

    document.querySelectorAll('.count-up').forEach((el) => {
        const target = Number(el.dataset.value || 0);
        const start = performance.now();
        const tick = (now) => {
            const progress = Math.min((now - start) / 850, 1);
            el.textContent = Math.round(target * progress);
            if (progress < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
    });

    themeBtn.addEventListener('click', () => {
        root.classList.toggle('dark');
        themeBtn.textContent = root.classList.contains('dark') ? 'Modo claro' : 'Modo oscuro';
    });

    const map = L.map('clientMap').setView([-17.7833, -63.1822], 11);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
    const bounds = [];
    mapData.forEach((p) => {
        const position = [p.latitud, p.longitud];
        bounds.push(position);
        const image = p.imagen_url ? `<img src="${p.imagen_url}" alt="${p.titulo}">` : 'Sin imagen';
        const marker = L.marker(position).addTo(map).bindPopup(`
            <div class="client-popup">
                <div class="client-popup-img">${image}</div>
                <strong>${p.titulo}</strong><br>
                ${p.tipo} · ${p.zona}<br>
                <strong>Bs ${p.precio}</strong><br>
                <a href="${p.detalle_url}">Ver detalle</a>
            </div>
        `);
        marker.on('mouseover', () => marker.openPopup());
    });
    if (bounds.length > 1) map.fitBounds(bounds, { padding: [60, 60], maxZoom: 12 });
    if (bounds.length === 1) map.setView(bounds[0], 11);
    setTimeout(() => map.invalidateSize(), 250);
});
</script>
@endpush
