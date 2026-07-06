@extends('layouts.panel')
@section('titulo', 'Notificaciones')
@section('titulo_pagina', 'Notificaciones Automáticas')

@push('styles')
<style>
.notif-hero {
    background: linear-gradient(135deg, #10263f 0%, #1e4f73 100%);
    border-radius: 18px;
    color: #fff;
    margin-bottom: 18px;
    padding: 24px;
}
.notif-item {
    align-items: flex-start;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    display: flex;
    gap: 14px;
    margin-bottom: 12px;
    padding: 16px;
}
.notif-item.unread {
    background: #f0f7ff;
    border-color: #bfdbfe;
}
.notif-dot {
    border-radius: 999px;
    flex: 0 0 12px;
    height: 12px;
    margin-top: 5px;
    width: 12px;
}
.notif-title {
    color: #0f172a;
    font-size: 14px;
    font-weight: 800;
    margin-bottom: 4px;
}
.notif-message {
    color: #475569;
    font-size: 13px;
    line-height: 1.55;
}
.notif-date {
    color: #94a3b8;
    font-size: 12px;
    margin-top: 7px;
}
</style>
@endpush

@section('contenido')
<div class="notif-hero">
    <p style="color:rgba(255,255,255,.7);font-size:12px;margin-bottom:5px">Alertas y avisos del sistema</p>
    <h2 style="font-size:22px;font-weight:800;margin-bottom:6px">Centro de Notificaciones</h2>
    <p style="color:rgba(255,255,255,.76);font-size:13px;max-width:720px">
        Aquí aparecen avisos generados automáticamente por el sistema: nuevas propiedades, solicitudes de visita y cambios de estado.
    </p>
</div>

<div class="card">
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;gap:12px">
        <span class="card-title">Mis notificaciones</span>
        <form method="POST" action="{{ route('notificaciones.leer-todas') }}">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn-detalle" style="border:none;cursor:pointer">Marcar todas como leídas</button>
        </form>
    </div>

    @forelse($notificaciones as $notificacion)
        @php
            $tipoLabel = match($notificacion->tipo) {
                'cambio_estado' => 'Cambio de estado',
                'nueva_propiedad' => 'Nueva propiedad',
                'recordatorio' => 'Recordatorio',
                'recomendacion' => 'Recomendación',
                default => ucfirst(str_replace('_', ' ', $notificacion->tipo)),
            };
        @endphp
        <div class="notif-item {{ $notificacion->leida ? '' : 'unread' }}">
            <span class="notif-dot" style="background:{{ $notificacion->leida ? '#cbd5e1' : '#2563eb' }}"></span>
            <div style="flex:1">
                <div class="notif-title">
                    {{ $tipoLabel }}
                </div>
                <div class="notif-message">{{ $notificacion->mensaje }}</div>
                <div class="notif-date">{{ optional($notificacion->fecha_envio)->format('d/m/Y H:i') }}</div>
                <div style="display:flex;gap:10px;align-items:center;margin-top:10px;flex-wrap:wrap">
                    @if($notificacion->propiedad && auth()->user()->rol === 'cliente')
                        <a href="{{ route('cliente.propiedades.detalle', $notificacion->propiedad) }}" style="color:#2563eb;font-size:12px;font-weight:700;text-decoration:none">Ver propiedad</a>
                    @endif
                    @unless($notificacion->leida)
                        <form method="POST" action="{{ route('notificaciones.leer', $notificacion) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" style="background:none;border:none;color:#64748b;cursor:pointer;font-size:12px;font-weight:700;padding:0">Marcar como leída</button>
                        </form>
                    @endunless
                </div>
            </div>
        </div>
    @empty
        <p style="color:#64748b;font-size:13px;padding:20px 0;text-align:center">Todavía no tienes notificaciones.</p>
    @endforelse
</div>
@endsection
