@extends('layouts.panel')
@section('titulo', 'Dashboard Administrador')
@section('titulo_pagina', 'Centro de mando')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
.dash-pro {
    --bg: #f4f7fb;
    --card: rgba(255,255,255,.86);
    --ink: #0f172a;
    --muted: #64748b;
    --line: rgba(148,163,184,.28);
    --brand: #0f766e;
    --brand2: #f59e0b;
    color: var(--ink);
}
.dash-pro.dark {
    --bg: #07111f;
    --card: rgba(15,23,42,.82);
    --ink: #e5edf7;
    --muted: #9fb0c6;
    --line: rgba(148,163,184,.18);
}
body.lorent-admin-dark {
    background: #07111f !important;
}
body.lorent-admin-dark .main,
body.lorent-admin-dark .content {
    background: #07111f !important;
}
body.lorent-admin-dark .topbar {
    background: #0f172a !important;
    border-color: rgba(148,163,184,.18) !important;
    color: #e5edf7 !important;
}
body.lorent-admin-dark .topbar-title,
body.lorent-admin-dark .user-name,
body.lorent-admin-dark .user-role {
    color: #e5edf7 !important;
}
body.lorent-admin-dark #sidebar-mobile {
    background: #020617 !important;
    border-right: 1px solid rgba(148,163,184,.16);
}
body.lorent-admin-dark #sidebar-mobile .nav-item,
body.lorent-admin-dark #sidebar-mobile .nav-section,
body.lorent-admin-dark #sidebar-mobile .logo-title,
body.lorent-admin-dark #sidebar-mobile .logo-sub {
    color: #dbeafe !important;
}
.dash-shell {
    background:
        radial-gradient(circle at 0% 0%, rgba(20,184,166,.20), transparent 30%),
        radial-gradient(circle at 90% 12%, rgba(245,158,11,.18), transparent 28%),
        var(--bg);
    border-radius: 24px;
    margin: -8px;
    padding: 14px;
}
.dash-hero {
    align-items: center;
    background: linear-gradient(135deg, #0f2d4f, #0f766e 58%, #f59e0b 145%);
    border-radius: 22px;
    color: #fff;
    display: flex;
    gap: 18px;
    justify-content: space-between;
    margin-bottom: 14px;
    overflow: hidden;
    padding: 18px 20px;
    position: relative;
}
.dash-hero::after {
    background: radial-gradient(circle, rgba(255,255,255,.26), transparent 62%);
    content: "";
    height: 220px;
    position: absolute;
    right: -80px;
    top: -80px;
    width: 220px;
}
.dash-hero h1 { font-size: clamp(22px, 3vw, 32px); font-weight: 950; letter-spacing: -.04em; margin: 0 0 4px; }
.dash-hero p { color: rgba(255,255,255,.78); font-size: 13px; line-height: 1.45; margin: 0; max-width: 620px; }
.dash-toggle {
    align-items: center;
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.26);
    border-radius: 999px;
    color: #fff;
    cursor: pointer;
    display: inline-flex;
    gap: 9px;
    font-weight: 800;
    min-width: 132px;
    padding: 12px 18px;
    position: relative;
    z-index: 1;
}
.dash-toggle svg { height: 18px; width: 18px; }
.kpi-grid { display: grid; gap: 14px; grid-template-columns: repeat(4, minmax(0, 1fr)); margin-bottom: 18px; }
.kpi {
    background: var(--card);
    border: 1px solid var(--line);
    border-radius: 22px;
    box-shadow: 0 20px 45px rgba(15,23,42,.08);
    padding: 18px;
}
.kpi small { color: var(--muted); font-size: 11px; font-weight: 900; letter-spacing: .09em; text-transform: uppercase; }
.kpi strong { display: block; font-size: 32px; font-weight: 950; margin-top: 8px; }
.kpi span { color: var(--muted); font-size: 12px; }
.dash-grid { display: grid; gap: 16px; grid-template-columns: 1.2fr .8fr; }
.dash-card {
    background: var(--card);
    border: 1px solid var(--line);
    border-radius: 24px;
    box-shadow: 0 20px 45px rgba(15,23,42,.08);
    padding: 20px;
}
.dash-card h3 { color: var(--ink); font-size: 17px; font-weight: 950; margin: 0 0 14px; }
.chart-wrap { height: 290px; }
#adminMap { border-radius: 20px; height: 380px; overflow: hidden; }
.mini-list { display: grid; gap: 10px; }
.mini-item {
    border: 1px solid var(--line);
    border-radius: 16px;
    color: var(--ink);
    padding: 12px;
}
.mini-item p { color: var(--muted); font-size: 12px; margin: 3px 0 0; }
.admin-popup { width: 220px; }
.admin-popup-img { background:#e2e8f0; border-radius:12px; height:105px; margin-bottom:9px; overflow:hidden; }
.admin-popup-img img { height:100%; object-fit:cover; width:100%; }
@media (max-width: 1100px) {
    .kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .dash-grid { grid-template-columns: 1fr; }
}
@media (max-width: 620px) {
    .dash-hero { align-items: flex-start; flex-direction: column; }
    .kpi-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('contenido')
<div class="dash-pro" id="adminDash">
    <div class="dash-shell">
        <section class="dash-hero">
            <div style="position:relative;z-index:1">
                <h1>Lorent Inmobiliaria</h1>
                <p>Panel ejecutivo con propiedades, usuarios, visitas, alertas y mapa global en una sola vista.</p>
            </div>
            <button class="dash-toggle" id="adminThemeBtn" type="button" aria-label="Cambiar tema">
                <span id="adminThemeIcon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12.8A8.5 8.5 0 1 1 11.2 3a6.5 6.5 0 0 0 9.8 9.8Z"/>
                    </svg>
                </span>
                <span id="adminThemeLabel">Oscuro</span>
            </button>
        </section>

        <section class="kpi-grid">
            <article class="kpi"><small>Propiedades totales</small><strong class="count-up" data-value="{{ $totalProps }}">0</strong><span>{{ $disponibles }} disponibles</span></article>
            <article class="kpi"><small>Usuarios registrados</small><strong class="count-up" data-value="{{ $totalUsuarios }}">0</strong><span>Todos los roles del sistema</span></article>
            <article class="kpi"><small>Visitas programadas</small><strong class="count-up" data-value="{{ $totalVisitas }}">0</strong><span>Solicitudes y agenda global</span></article>
            <article class="kpi"><small>Vendidas</small><strong class="count-up" data-value="{{ $totalVentas }}">0</strong><span>{{ $reservadas }} reservadas</span></article>
        </section>

        <section class="dash-grid" style="margin-bottom:16px">
            <div class="dash-card">
                <h3>Propiedades por mes</h3>
                <div class="chart-wrap"><canvas id="propsMonthChart"></canvas></div>
            </div>
            <div class="dash-card">
                <h3>Distribucion por estado</h3>
                <div class="chart-wrap"><canvas id="statusChart"></canvas></div>
            </div>
        </section>

        <section class="dash-grid" style="margin-bottom:16px">
            <div class="dash-card">
                <h3>Mapa global de propiedades</h3>
                <div id="adminMap"></div>
            </div>
            <div class="dash-card">
                <h3>Proximas visitas</h3>
                <div class="mini-list">
                    @forelse($visitas as $v)
                        <div class="mini-item">
                            <strong>{{ $v->propiedad->titulo ?? 'Propiedad' }}</strong>
                            <p>{{ $v->cliente->nombre ?? 'Cliente' }} · {{ optional($v->fecha_solicitada)->format('d/m/Y H:i') ?? $v->fecha_solicitada }}</p>
                        </div>
                    @empty
                        <div class="mini-item"><strong>Sin visitas próximas</strong><p>No hay solicitudes programadas.</p></div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="dash-grid">
            <div class="dash-card">
                <h3>Actividad reciente del sistema</h3>
                <div class="mini-list">
                    @forelse($actividad as $a)
                        <div class="mini-item">
                            <strong>{{ $a->accion }}</strong>
                            <p>{{ $a->nombre ?? 'Sistema' }} · {{ optional($a->fecha_hora)->format('d/m/Y H:i') ?? $a->fecha_hora }}</p>
                        </div>
                    @empty
                        <div class="mini-item"><strong>Sin actividad</strong><p>Aun no existen eventos recientes.</p></div>
                    @endforelse
                </div>
            </div>
            <div class="dash-card">
                <h3>Notificaciones importantes</h3>
                <div class="mini-list">
                    @forelse($notificaciones as $n)
                        <div class="mini-item">
                            <strong>{{ ucfirst(str_replace('_', ' ', $n->tipo)) }}</strong>
                            <p>{{ $n->mensaje }}</p>
                        </div>
                    @empty
                        <div class="mini-item"><strong>Todo tranquilo</strong><p>No hay alertas recientes.</p></div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('adminDash');
    const themeBtn = document.getElementById('adminThemeBtn');
    const themeLabel = document.getElementById('adminThemeLabel');
    const themeIcon = document.getElementById('adminThemeIcon');
    const monthLabels = @json($propiedadesPorMes->keys()->values());
    const monthData = @json($propiedadesPorMes->values());
    const statusData = @json(array_values($porEstado));
    const statusLabels = @json(array_keys($porEstado));
    const mapData = @json($mapaPropiedades);
    let charts = [];

    document.querySelectorAll('.count-up').forEach((el) => {
        const target = Number(el.dataset.value || 0);
        const start = performance.now();
        const duration = 900;
        const tick = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            el.textContent = Math.round(target * progress);
            if (progress < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
    });

    function chartColors() {
        const dark = root.classList.contains('dark');
        return {
            text: dark ? '#dbeafe' : '#0f172a',
            grid: dark ? 'rgba(148,163,184,.18)' : 'rgba(148,163,184,.26)',
        };
    }

    function renderCharts() {
        charts.forEach(chart => chart.destroy());
        const colors = chartColors();
        charts = [
            new Chart(document.getElementById('propsMonthChart'), {
                type: 'bar',
                data: { labels: monthLabels, datasets: [{ label: 'Propiedades', data: monthData, backgroundColor: '#0f766e', borderRadius: 14 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { labels: { color: colors.text } } }, scales: { x: { ticks: { color: colors.text }, grid: { color: colors.grid } }, y: { ticks: { color: colors.text }, grid: { color: colors.grid } } } }
            }),
            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: { labels: statusLabels, datasets: [{ data: statusData, backgroundColor: ['#22c55e', '#f59e0b', '#ef4444'], borderWidth: 0 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { color: colors.text } } } }
            })
        ];
    }

    themeBtn.addEventListener('click', () => {
        root.classList.toggle('dark');
        document.body.classList.toggle('lorent-admin-dark', root.classList.contains('dark'));
        const dark = root.classList.contains('dark');
        themeLabel.textContent = dark ? 'Claro' : 'Oscuro';
        themeIcon.innerHTML = dark
            ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>'
            : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.8A8.5 8.5 0 1 1 11.2 3a6.5 6.5 0 0 0 9.8 9.8Z"/></svg>';
        renderCharts();
    });
    renderCharts();

    const map = L.map('adminMap').setView([-17.7833, -63.1822], 11);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
    const bounds = [];
    mapData.forEach((p) => {
        const position = [p.latitud, p.longitud];
        bounds.push(position);
        const image = p.imagen_url ? `<img src="${p.imagen_url}" alt="${p.titulo}">` : '';
        L.marker(position).addTo(map).bindPopup(`
            <div class="admin-popup">
                <div class="admin-popup-img">${image || 'Sin imagen'}</div>
                <strong>${p.titulo}</strong><br>
                ${p.tipo} · ${p.estado} · ${p.zona}<br>
                <strong>$${p.precio}</strong><br>
                <small>Agente: ${p.agente}</small>
            </div>
        `);
    });
    if (bounds.length > 1) map.fitBounds(bounds, { padding: [60, 60], maxZoom: 12 });
    if (bounds.length === 1) map.setView(bounds[0], 11);
    setTimeout(() => map.invalidateSize(), 250);
});
</script>
@endpush
