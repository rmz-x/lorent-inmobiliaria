@extends('layouts.panel')
@section('titulo', 'Recomendaciones')
@section('titulo_pagina', 'Propiedades Recomendadas')

@push('styles')
<style>
.rec-hero {
    background: linear-gradient(135deg, #2f235f 0%, #1b5674 100%);
    border-radius: 18px;
    color: #fff;
    margin-bottom: 18px;
    padding: 24px;
}
.rec-grid {
    display: grid;
    gap: 16px;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
}
.rec-card {
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    overflow: hidden;
}
.rec-img {
    align-items: center;
    background: #e2e8f0;
    color: #64748b;
    display: flex;
    height: 150px;
    justify-content: center;
}
.rec-body {
    padding: 15px;
}
.rec-score {
    background: #e0f2fe;
    border-radius: 999px;
    color: #0369a1;
    font-size: 12px;
    font-weight: 800;
    padding: 5px 10px;
}
</style>
@endpush

@section('contenido')
<div class="rec-hero">
    <h2 style="font-size:22px;font-weight:800;margin-bottom:6px">Sugerencias personalizadas para ti</h2>
    <p style="color:rgba(255,255,255,.76);font-size:13px;max-width:720px">
        El sistema prioriza propiedades disponibles según tus visitas, solicitudes y zonas/tipos consultados recientemente.
    </p>
</div>

<div class="card" style="margin-bottom:16px">
    <span style="font-size:13px;color:#64748b">
        Preferencias detectadas:
        <strong>Tipos:</strong> {{ count($preferencias['tipos']) ? implode(', ', $preferencias['tipos']) : 'Sin historial suficiente' }}
        ·
        <strong>Zonas:</strong> {{ count($preferencias['zonas']) ? implode(', ', $preferencias['zonas']) : 'Sin historial suficiente' }}
    </span>
</div>

<div class="rec-grid">
    @forelse($propiedades as $p)
        <div class="rec-card">
            <div class="rec-img">
                @if($p->imagen)
                    <img src="{{ $p->imagen_url }}" alt="{{ $p->titulo }}" style="width:100%;height:100%;object-fit:cover">
                @else
                    Sin foto
                @endif
            </div>
            <div class="rec-body">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:10px">
                    <span class="rec-score">{{ number_format($p->puntaje_recomendacion, 0) }}% afinidad</span>
                    <span style="font-size:12px;color:#64748b">{{ $p->tipo }}</span>
                </div>
                <h3 style="color:#0f172a;font-size:15px;font-weight:800;margin-bottom:5px">{{ $p->titulo }}</h3>
                <p style="color:#64748b;font-size:13px;margin-bottom:10px">{{ $p->zona }} · {{ $p->area }} m²</p>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:8px">
                    <strong style="color:#0f172a">${{ number_format($p->precio, 0, ',', '.') }}</strong>
                    <a href="{{ route('cliente.propiedades.detalle', $p) }}" style="color:#2563eb;font-size:12px;font-weight:800;text-decoration:none">Ver detalle</a>
                </div>
            </div>
        </div>
    @empty
        <div class="card" style="grid-column:1/-1;text-align:center;color:#64748b">
            No hay propiedades disponibles para recomendar por ahora.
        </div>
    @endforelse
</div>
@endsection
