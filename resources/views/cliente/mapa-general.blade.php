@extends('layouts.panel')
@section('titulo', 'Mapa general')
@section('titulo_pagina', 'Mapa general de propiedades')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
.map-hero {
    background: linear-gradient(135deg, #0f2a4a 0%, #173b63 58%, #245a7f 100%);
    border-radius: 18px;
    color: #fff;
    margin-bottom: 18px;
    overflow: hidden;
    padding: 26px;
    position: relative;
}
.map-hero::after {
    background: radial-gradient(circle, rgba(255,255,255,.18), transparent 62%);
    content: "";
    height: 260px;
    position: absolute;
    right: -80px;
    top: -90px;
    width: 260px;
}
.map-layout {
    display: grid;
    gap: 16px;
    grid-template-columns: minmax(0, 1fr) 320px;
}
#mapaGeneral {
    border: 1px solid #dbe3ef;
    border-radius: 18px;
    height: 620px;
    overflow: hidden;
}
.map-list {
    max-height: 620px;
    overflow: auto;
}
.map-item {
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    margin-bottom: 10px;
    padding: 13px;
}
.map-item-title {
    color: #0f172a;
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 4px;
}
.map-item-meta {
    color: #64748b;
    font-size: 12px;
    margin-bottom: 8px;
}
.map-popup {
    width: 220px;
}
.map-popup-img {
    background: #e2e8f0;
    border-radius: 12px;
    height: 112px;
    margin-bottom: 10px;
    overflow: hidden;
    width: 100%;
}
.map-popup-img img {
    display: block;
    height: 100%;
    object-fit: cover;
    width: 100%;
}
.map-popup-empty {
    align-items: center;
    color: #64748b;
    display: flex;
    font-size: 12px;
    height: 100%;
    justify-content: center;
}
.map-popup-title {
    color: #0f172a;
    font-weight: 700;
    margin-bottom: 4px;
}
.map-popup-link {
    color: #2563eb;
    font-weight: 700;
    text-decoration: none;
}
@media (max-width: 980px) {
    .map-layout {
        grid-template-columns: 1fr;
    }
    #mapaGeneral,
    .map-list {
        max-height: none;
    }
}
</style>
@endpush

@section('contenido')
<div class="map-hero">
    <h2 style="font-size:22px;font-weight:800;margin-bottom:6px;position:relative;z-index:1">Explora propiedades disponibles en el mapa</h2>
    <p style="color:rgba(255,255,255,.74);font-size:13px;max-width:680px;position:relative;z-index:1">
        Consulta la ubicación de las propiedades registradas con coordenadas, filtra por tipo y entra al detalle desde cada marcador.
    </p>
</div>

<div class="card" style="margin-bottom:16px;padding:12px 18px">
    <form method="GET" action="{{ route('cliente.mapa') }}" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
        <span style="font-size:13px;color:#6c757d;margin-right:4px">Tipo:</span>
        @foreach(['Todas','Venta','Alquiler','Anticretico'] as $t)
            <button
                type="submit"
                name="tipo"
                value="{{ $t }}"
                style="
                    border:1px solid {{ $tipo === $t ? '#2563eb' : '#dee2e6' }};
                    background:{{ $tipo === $t ? '#eaf1ff' : '#fff' }};
                    border-radius:999px;
                    color:{{ $tipo === $t ? '#1d4ed8' : '#64748b' }};
                    cursor:pointer;
                    font-size:12px;
                    padding:6px 14px;
                "
            >
                {{ $t === 'Todas' ? 'Todas' : $t }}
            </button>
        @endforeach
    </form>
</div>

<div class="map-layout">
    <div class="card" style="padding:0;overflow:hidden">
        <div id="mapaGeneral"></div>
    </div>

    <div class="card map-list">
        <div class="card-header" style="padding-left:0;padding-right:0">
            <span class="card-title">
                Propiedades ubicadas
                <span style="color:#64748b;font-size:12px;font-weight:400;margin-left:4px">({{ $propiedades->count() }})</span>
            </span>
        </div>

        @forelse($propiedades as $p)
            <div class="map-item">
                <div class="map-item-title">{{ $p->titulo }}</div>
                <div class="map-item-meta">{{ $p->tipo }} · {{ $p->zona }} · {{ $p->area }} m²</div>
                <div style="display:flex;justify-content:space-between;gap:8px;align-items:center">
                    <strong style="color:#0f172a;font-size:13px">${{ number_format($p->precio, 0, ',', '.') }}</strong>
                    <a href="{{ route('cliente.propiedades.detalle', $p) }}" style="color:#2563eb;font-size:12px;font-weight:700;text-decoration:none">Ver detalle</a>
                </div>
            </div>
        @empty
            <p style="color:#64748b;font-size:13px;padding:14px 0">No hay propiedades disponibles con coordenadas registradas.</p>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const propiedades = @json($propiedadesMapa);
    const centroSantaCruz = [-17.7833, -63.1822];
    const mapa = L.map('mapaGeneral').setView(centroSantaCruz, 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(mapa);

    const bounds = [];

    propiedades.forEach((propiedad) => {
        const posicion = [propiedad.latitud, propiedad.longitud];
        bounds.push(posicion);

        const imagen = propiedad.imagen_url
            ? `<img src="${propiedad.imagen_url}" alt="${propiedad.titulo}">`
            : `<div class="map-popup-empty">Sin imagen</div>`;
        const marker = L.marker(posicion).addTo(mapa).bindPopup(`
            <div class="map-popup">
            <div class="map-popup-img">${imagen}</div>
            <div class="map-popup-title">${propiedad.titulo}</div>
            <div>${propiedad.tipo} · ${propiedad.zona}</div>
            <div><strong>$${propiedad.precio}</strong></div>
            <a class="map-popup-link" href="${propiedad.detalle_url}">Ver detalle</a>
            </div>
        `);
        marker.on('mouseover', () => marker.openPopup());
    });

    if (bounds.length > 0) {
        if (bounds.length === 1) {
            mapa.setView(bounds[0], 11);
        } else {
            mapa.fitBounds(bounds, { padding: [70, 70], maxZoom: 12 });
        }
    }

    setTimeout(() => mapa.invalidateSize(), 250);
});
</script>
@endpush
