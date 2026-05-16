@extends('layouts.panel')
@section('titulo','Mis solicitudes')
@section('titulo_pagina','Mis solicitudes de visita')

@push('styles')
<style>
table{width:100%;border-collapse:collapse;font-size:13px}
th{text-align:left;color:#6c757d;font-weight:500;padding:10px 14px;border-bottom:2px solid #e2e6ea;background:#f8f9fa;white-space:nowrap}
td{padding:11px 14px;border-bottom:1px solid #f0f2f5;vertical-align:middle}
tr:last-child td{border-bottom:none}
tr:hover td{background:#f8f9fa}

/* Badge colors */
.badge-pendiente {
    background: #fef3c7;
    color: #92400e;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}
.badge-confirmada {
    background: #d1fae5;
    color: #065f46;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}
.badge-cancelada {
    background: #fee2e2;
    color: #991b1b;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

/* Button actions */
.btn-reagendar-solicitud {
    background: #f0f7ff;
    color: #0ea5e9;
    border: 1px solid transparent;
    padding: 7px 16px;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.btn-reagendar-solicitud:hover {
    background: #e0f2fe;
    border-color: #7dd3fc;
    box-shadow: 0 4px 8px rgba(14,165,233,0.15);
    transform: translateY(-1px);
}

.btn-cancelar-solicitud {
    background: #fff5f5;
    color: #ef4444;
    border: 1px solid transparent;
    padding: 7px 16px;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.btn-cancelar-solicitud:hover {
    background: #fee2e2;
    border-color: #fca5a5;
    box-shadow: 0 4px 8px rgba(239,68,68,0.15);
    transform: translateY(-1px);
}

.acciones-wrapper {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.form-cancelar {
    display: inline-flex;
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
    td[data-label="Mensaje"] {
        flex-direction: column !important;
        margin-top: 8px !important;
        padding-top: 8px !important;
        border-top: 1px solid #f3f4f6 !important;
    }
    td[data-label="Mensaje"]::before {
        margin-bottom: 6px !important;
        width: 100% !important;
    }
    .mensaje-container {
        background: #f9fafb !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 8px !important;
        padding: 10px !important;
        width: 100% !important;
        min-width: 0 !important;
        font-size: 13px !important;
        color: #4b5563 !important;
        line-height: 1.5 !important;
        word-break: break-word !important;
        box-sizing: border-box !important;
        margin-top: 4px !important;
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
    td[data-label="Acciones"] .acciones-wrapper {
        width: 100% !important;
        display: flex !important;
        flex-direction: row !important;
        gap: 12px !important;
        justify-content: center !important;
        align-items: center !important;
        flex-wrap: wrap !important;
    }
    td[data-label="Acciones"] .acciones-wrapper > button,
    td[data-label="Acciones"] .acciones-wrapper > .form-cancelar {
        width: auto !important;
        flex: none !important;
        display: flex !important;
        justify-content: center !important;
    }
    td[data-label="Acciones"] .acciones-wrapper button {
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
    <div class="card-header">
        <span class="card-title">Historial de solicitudes</span>
        <a href="{{ route('cliente.propiedades') }}" class="btn-primary">+ Nueva solicitud</a>
    </div>
    <div class="table-container w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
        <table class="w-full text-sm text-left">
            <thead>
                <tr>
                    <th>Propiedad</th>
                    <th class="hidden sm:table-cell">Zona</th>
                    <th class="hidden sm:table-cell">Tipo</th>
                    <th>Fecha</th>
                    <th class="hidden md:table-cell">Mensaje</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($solicitudes as $s)
            @php
                $cls = match($s->estado) {
                    'Pendiente'  => 'badge-pendiente',
                    'Aceptada'   => 'badge-confirmada',
                    'Completada' => 'badge-confirmada',
                    'Rechazada'  => 'badge-cancelada',
                    default      => 'badge-pendiente'
                };
                $label = $s->estado === 'Rechazada' ? 'Cancelada' : $s->estado;
            @endphp
            <tr>
                <td data-label="Propiedad"><span class="td-value">{{ $s->propiedad->titulo ?? '—' }}</span></td>
                <td class="hidden sm:table-cell" data-label="Zona"><span class="td-value">{{ $s->propiedad->zona ?? '—' }}</span></td>
                <td class="hidden sm:table-cell" data-label="Tipo"><span class="td-value">{{ $s->propiedad->tipo ?? '—' }}</span></td>
                <td data-label="Fecha"><span class="td-value">{{ \Carbon\Carbon::parse($s->fecha_solicitada)->format('d/m/Y') }}</span></td>
                <td data-label="Mensaje">
                    <span class="hidden md:inline" style="max-width:200px;color:#6c757d">{{ Str::limit($s->mensaje, 60) }}</span>
                    <div class="block md:hidden mensaje-container">{{ $s->mensaje ?? 'Sin mensaje' }}</div>
                </td>
                <td data-label="Estado"><span class="td-value"><span class="badge {{ $cls }}">{{ $label }}</span></span></td>
                <td data-label="Acciones">
                    @if($s->estado === 'Pendiente')
                    <div class="acciones-wrapper">
                        <button
                            type="button"
                            class="btn-reagendar-solicitud"
                            data-id="{{ $s->id }}"
                            data-fecha="{{ \Carbon\Carbon::parse($s->fecha_solicitada)->format('Y-m-d') }}"
                            data-titulo="{{ $s->propiedad->titulo ?? 'esta solicitud' }}"
                        >Reagendar</button>
                        <form method="POST" action="{{ route('cliente.solicitudes.cancelar', $s->id) }}" class="form-cancelar">
                            @csrf @method('PATCH')
                            <button
                                type="button"
                                class="btn-cancelar-solicitud"
                                data-id="{{ $s->id }}"
                                data-titulo="{{ $s->propiedad->titulo ?? 'esta solicitud' }}"
                            >Cancelar</button>
                        </form>
                    </div>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;color:#6c757d;padding:30px">
                    No tienes solicitudes aún.
                    <a href="{{ route('cliente.propiedades') }}" style="color:#185FA5;margin-left:6px">Ver propiedades →</a>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL CONFIRMAR CANCELACIÓN --}}
<div id="cancelOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:36px 32px;max-width:420px;width:90%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
        <div style="width:72px;height:72px;border-radius:50%;background:#e8f0fe;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <span style="font-size:36px;">🏠</span>
        </div>
        <h2 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:10px;">Cancelar solicitud</h2>
        <p id="cancelMsg" style="font-size:14px;color:#64748b;margin-bottom:28px;line-height:1.6;"></p>
        <div style="display:flex;gap:12px;justify-content:center;">
            <button onclick="cerrarCancelModal()" style="padding:10px 24px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;font-size:14px;font-weight:500;cursor:pointer;">Volver</button>
            <button id="btnConfirmarCancelar" style="padding:10px 24px;border:none;border-radius:8px;background:#dc2626;color:#fff;font-size:14px;font-weight:600;cursor:pointer;">Sí, cancelar</button>
        </div>
    </div>
</div>

{{-- MODAL REAGENDAR --}}
<div id="reagendarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:36px 32px;max-width:420px;width:90%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
        <div style="width:72px;height:72px;border-radius:50%;background:#e0f2fe;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <span style="font-size:36px;">📅</span>
        </div>
        <h2 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:6px;">Reagendar visita</h2>
        <p id="reagendarMsg" style="font-size:13px;color:#64748b;margin-bottom:20px;"></p>
        <form id="formReagendar" method="POST" action="#">
            @csrf @method('PATCH')
            <div style="text-align:left;margin-bottom:20px;">
                <label style="font-size:12px;color:#6c757d;font-weight:500;display:block;margin-bottom:6px;">Nueva fecha</label>
                <input type="date" name="fecha_solicitada" id="inputNuevaFecha"
                    min="{{ date('Y-m-d') }}" required
                    style="width:100%;padding:9px 12px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;font-family:inherit;">
            </div>
            <div style="display:flex;gap:12px;justify-content:center;">
                <button type="button" onclick="cerrarReagendarModal()"
                    style="padding:10px 24px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;font-size:14px;font-weight:500;cursor:pointer;">
                    Cancelar
                </button>
                <button type="submit"
                    style="padding:10px 24px;border:none;border-radius:8px;background:#0369a1;color:#fff;font-size:14px;font-weight:600;cursor:pointer;">
                    Confirmar fecha
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
//CANCELAR
let pendingCancelForm = null;

document.querySelectorAll('.btn-cancelar-solicitud').forEach(btn => {
    btn.addEventListener('click', function() {
        pendingCancelForm = this.closest('form');
        document.getElementById('cancelMsg').textContent =
            `¿Seguro que deseas cancelar la solicitud para "${this.dataset.titulo}"? Esta acción no se puede deshacer.`;
        document.getElementById('cancelOverlay').style.display = 'flex';
    });
});

function cerrarCancelModal() {
    document.getElementById('cancelOverlay').style.display = 'none';
    pendingCancelForm = null;
}

document.getElementById('btnConfirmarCancelar').addEventListener('click', function() {
    if (pendingCancelForm) pendingCancelForm.submit();
});

document.getElementById('cancelOverlay').addEventListener('click', function(e) {
    if (e.target === this) cerrarCancelModal();
});

//REAGENDAR
document.querySelectorAll('.btn-reagendar-solicitud').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('reagendarMsg').textContent =
            `Selecciona una nueva fecha para la visita a "${this.dataset.titulo}".`;
        document.getElementById('inputNuevaFecha').value = this.dataset.fecha;
        document.getElementById('formReagendar').action =
            `/cliente/solicitudes/${this.dataset.id}/reagendar`;
        document.getElementById('reagendarOverlay').style.display = 'flex';
    });
});

function cerrarReagendarModal() {
    document.getElementById('reagendarOverlay').style.display = 'none';
}

document.getElementById('reagendarOverlay').addEventListener('click', function(e) {
    if (e.target === this) cerrarReagendarModal();
});
</script>
@endpush