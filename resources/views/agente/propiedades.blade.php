@extends('layouts.panel')
@section('titulo','Mis propiedades')
@section('titulo_pagina','Mis propiedades')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
/* Barra de búsqueda global */
.card-search { background: transparent; display:block !important; padding:8px 12px; }
.card-search form input[type="search"] { min-height:44px; }
.card-search .btn-primary { height:44px; border-radius:10px; }
.card-search #btnOpenFilters { height:44px; }
/* ensure search and filters are visible on small screens */
@media (max-width:640px) {
    .card-search { display:block !important; }
    .card-search form { display:flex; gap:8px; align-items:center; }
    .card-search form input[type="search"] { flex:1; min-width:0; }
    .card-search .btn-primary, .card-search #btnOpenFilters { flex:0 0 auto; }
}

/* Panel lateral de filtros (off-canvas) */
.filter-backdrop {
    position: fixed; inset: 0; background: rgba(2,6,23,0.45); display: none; z-index: 80;
}
.filter-backdrop.open { display:block; }
.filter-panel {
    position: fixed; top: 0; right: 0; height: 100%; width: 92%; max-width: 380px;
    background: #ffffff; border-left: 1px solid #e6eef6; box-shadow: -12px 0 34px rgba(2,6,23,0.12);
    border-radius: 12px 0 0 12px; padding: 18px; transform: translateX(110%);
    transition: transform 260ms ease-in-out; z-index: 90; overflow:auto;
}
.filter-panel.open { transform: translateX(0); }
.filter-panel h3 { margin:0 0 10px 0; font-size:16px; color:#0f172a }
.filter-panel .field { margin-bottom:12px }
.filter-panel label { display:block;font-size:12px;color:#64748b;margin-bottom:6px }
.filter-panel select, .filter-panel input[type="number"] { width:100%; padding:12px;border:1px solid #e6eef6;border-radius:10px;font-size:14px }
.filter-panel .filter-actions { display:flex;gap:10px;margin-top:14px }
.filter-panel .filter-actions button { flex:1;padding:12px;border-radius:10px;font-size:15px }
.filter-panel .btn-apply { background: linear-gradient(135deg,#10b981,#059669); color:#fff;border:none }
.filter-panel .btn-reset { background:#f8fafc;border:1px solid #e6eef6 }

/* Make buttons more touch-friendly on small screens */
@media (max-width:640px) {
    .filter-panel { width:100%; max-width:100%; border-radius:12px 12px 0 0; right:0; bottom:0; top:auto; height:auto; transform: translateY(110%); border-left:none; border-top:1px solid #e6eef6 }
    .filter-panel.open { transform: translateY(0); }
    .filter-backdrop.open { display:block }
}
.mobile-only-badge { display: none; }

/* Mobile card design */
@media (max-width: 640px) {
    @keyframes badge-pulse {
        0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.6), inset 0 1px 3px rgba(255,255,255,0.9); }
        70% { box-shadow: 0 0 0 10px rgba(255, 255, 255, 0), inset 0 1px 3px rgba(255,255,255,0.9); }
        100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0), inset 0 1px 3px rgba(255,255,255,0.9); }
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
        display: grid !important;
        grid-template-columns: 1fr 1fr;
        grid-template-areas:
            "titulo titulo"
            "tipo zona"
            "precio area"
            "acciones acciones";
        background: #fff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 16px !important;
        margin-bottom: 18px !important;
        padding: 16px !important; /* Estandarizado a 16px */
        box-shadow: 0 6px 18px rgba(15,23,42,0.06) !important;
        gap: 12px !important;
    }
    
    /* Ocultar ID y Estado (se mueve al título) */
    td[data-label="ID"], 
    td[data-label="Estado"] { display: none !important; }

    /* Título - Destacado (Header azul) */
    td[data-label="Título"] {
        grid-area: titulo;
        background: linear-gradient(135deg, #1d4ed8, #2563eb) !important; /* Azul vibrante */
        margin: -16px -16px 12px -16px !important; /* Compensa el padding del tr exacto */
        width: calc(100% + 32px) !important; /* Fuerza a llenar el 100% + el padding compensado */
        max-width: none !important; /* IMPORTANTE: evita que se corte a la derecha */
        padding: 12px 16px !important; /* Barra más delgada */
        border-radius: 14px 14px 0 0 !important;
        border-bottom: none !important;
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 16px !important; /* Mayor separación con el título */
    }
    td[data-label="Título"]::before { display: none !important; }
    td[data-label="Título"] .td-value {
        font-size: 16px !important;
        font-weight: 700 !important;
        color: #ffffff !important;
        line-height: 1.3 !important;
    }

    .mobile-only-badge {
        display: inline-flex !important;
        flex-shrink: 0 !important;
        padding: 4px 10px !important;
        font-size: 9.5px !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: .06em !important;
        border-radius: 20px !important;
        border: 1px solid rgba(255,255,255,0.6) !important; /* Borde más brillante */
        backdrop-filter: blur(6px) saturate(1.8) !important; /* Efecto cristal más fuerte */
        animation: badge-pulse 2s infinite !important; /* Pulso más notorio y brillo interno */
        color: #ffffff !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.4) !important; /* Texto legible sobre fondos claros */
    }
    /* Fondos súper claros y brillantes (tipo neón/lechuga) para un efecto "re pro" */
    .mobile-only-badge.badge-disponible { background: linear-gradient(135deg, rgba(74,222,128,0.95), rgba(34,197,94,0.95)) !important; }
    .mobile-only-badge.badge-reservado  { background: linear-gradient(135deg, rgba(251,191,36,0.95), rgba(245,158,11,0.95)) !important; }
    .mobile-only-badge.badge-vendido    { background: linear-gradient(135deg, rgba(248,113,113,0.95), rgba(239,68,68,0.95)) !important; }

    /* Bloques verticales */
    td[data-label="Tipo"] { grid-area: tipo; }
    td[data-label="Zona"] { grid-area: zona; }
    td[data-label="Precio"] { grid-area: precio; }
    td[data-label="Área"] { grid-area: area; }

    td[data-label="Tipo"],
    td[data-label="Zona"],
    td[data-label="Precio"],
    td[data-label="Área"] {
        display: flex !important;
        flex-direction: column !important;
        align-items: flex-start !important;
        padding: 10px !important;
        border: none !important;
        background: #f8fafc !important;
        border-radius: 8px !important;
    }
    td::before {
        content: attr(data-label);
        font-weight: 700 !important;
        color: #94a3b8 !important;
        font-size: 10px !important;
        text-transform: uppercase !important;
        letter-spacing: .06em !important;
        margin-bottom: 4px !important;
        width: auto !important;
    }
    .td-value {
        font-size: 14px !important;
        color: #334155 !important;
        font-weight: 600 !important;
    }

    /* Acciones - Botones juntos */
    td[data-label="Acciones"] {
        grid-area: acciones;
        padding-top: 12px !important;
        border-top: 1px solid #f1f5f9 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding-bottom: 0 !important;
    }
    td[data-label="Acciones"]::before { display: none !important; }
    
    .action-btns {
        width: 100% !important;
        display: flex !important;
        flex-direction: row !important;
        gap: 12px !important;
        justify-content: space-between !important;
        align-items: center !important;
        flex-wrap: nowrap !important;
    }
    .action-btns > button, 
    .action-btns > form {
        width: calc(50% - 6px) !important; /* Exactamente 50% menos la mitad del gap */
        flex: 0 0 calc(50% - 6px) !important; /* Fuerza a no crecer ni encoger */
        margin: 0 !important;
    }
    .action-btns form button {
        width: 100% !important;
    }

    /* Colores vibrantes para los botones en móvil */
    .btn-edit {
        background: linear-gradient(135deg, #0ea5e9, #0284c7) !important;
        color: #fff !important;
        border: none !important;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3) !important;
        height: 38px !important;
        border-radius: 10px !important;
        font-weight: 600 !important;
    }
    .btn-delete {
        background: linear-gradient(135deg, #f43f5e, #e11d48) !important;
        color: #fff !important;
        border: none !important;
        box-shadow: 0 4px 12px rgba(225, 29, 72, 0.3) !important;
        height: 38px !important;
        border-radius: 10px !important;
        font-weight: 600 !important;
    }
}
</style>
@endpush

@section('contenido')
<div class="card">
    <div class="card-header">
        <span class="card-title">Lista de mis propiedades</span>
        <button class="btn-primary" onclick="abrirModal()">+ Registrar propiedad</button>
    </div>
    
<div class="table-container w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
<table class="min-w-[600px] w-full text-sm text-left">
        <thead><tr><th>#</th><th>Título</th><th>Tipo</th><th>Zona</th><th>Precio</th><th>Área</th><th>Estado</th><th>Acciones</th></tr></thead>
        <tbody>
        @forelse($propiedades as $p)
        <tr>
            <td data-label="ID"><span class="td-value">{{ $p->id }}</span></td>
            <td data-label="Título">
                <span class="td-value">{{ $p->titulo }}</span>
                <span class="badge badge-{{ strtolower($p->estado) }} mobile-only-badge">
                    {{ $p->estado }}
                </span>
            </td>
            <td data-label="Tipo"><span class="td-value">{{ $p->tipo }}</span></td>
            <td data-label="Zona"><span class="td-value">{{ $p->zona }}</span></td>
            <td data-label="Precio"><span class="td-value">${{ number_format($p->precio,0,',','.') }}</span></td>
            <td data-label="Área"><span class="td-value">{{ $p->area ? $p->area.' m²' : '—' }}</span></td>
            <td data-label="Estado"><span class="td-value"><span class="badge badge-{{ strtolower($p->estado) }}">{{ $p->estado }}</span></span></td>
            <td data-label="Acciones">
                <div class="action-btns">
                    <button
                        type="button"
                        class="btn-edit btn-editar-propiedad w-full sm:w-auto"
                        data-id="{{ $p->id }}"
                        data-titulo="{{ $p->titulo }}"
                        data-tipo="{{ $p->tipo }}"
                        data-zona="{{ $p->zona }}"
                        data-precio="{{ $p->precio }}"
                        data-area="{{ $p->area }}"
                        data-descripcion="{{ $p->descripcion }}"
                        data-estado="{{ $p->estado }}"
                        data-imagen="{{ $p->imagen ? \Illuminate\Support\Facades\Storage::url($p->imagen) : '' }}"
                        data-lat="{{ $p->latitud }}"
                        data-lng="{{ $p->longitud }}">
                        ✏️ Editar
                    </button>
                    <form method="POST"
                        action="{{ route('admin.propiedades.destroy', $p) }}"
                        class="form-eliminar"
                        data-title="{{ $p->titulo }}">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                                class="btn-delete open-delete-modal w-full sm:w-auto"
                                data-name="{{ $p->titulo }}">
                            🗑️ Eliminar
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;color:#6c757d;padding:20px">No tienes propiedades registradas.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
</div>

@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px">
    <strong>Errores de validación:</strong>
    <ul style="margin:6px 0 0 18px;padding:0">
        @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="modal-overlay" id="modalOverlay">
<div class="modal w-[95%] max-w-lg mx-auto sm:w-full">
    <h2 id="modalTitulo">Registrar propiedad</h2>
    <form id="formPropiedad" method="POST" action="{{ route('agente.propiedades.store') }}" enctype="multipart/form-data">
        @csrf <span id="methodField"></span>
        <div class="form-grid">
            <div class="form-group full"><label>Título</label><input type="text" name="titulo" id="propTitulo" required></div>
            <div class="form-group"><label>Tipo</label>
                <select name="tipo" id="propTipo">
                    <option value="Venta">Venta</option><option value="Alquiler">Alquiler</option><option value="Anticretico">Anticretico</option>
                </select>
            </div>
            <div class="form-group"><label>Zona</label><input type="text" name="zona" id="propZona" required></div>
            <div class="form-group"><label>Precio ($)</label><input type="number" name="precio" id="propPrecio" step="0.01" min="0" required></div>
            <div class="form-group"><label>Área (m²)</label><input type="number" name="area" id="propArea" step="0.01" min="0" required></div>
            <div class="form-group"><label>Estado</label>
                <select name="estado" id="propEstado">
                    <option value="Disponible">Disponible</option><option value="Reservado">Reservado</option><option value="Vendido">Vendido</option>
                </select>
            </div>
            <div class="form-group full"><label>Descripción</label><textarea name="descripcion" id="propDescripcion" rows="3" required></textarea></div>
            <div class="form-group full">
                <label>Imagen <small style="color:#6c757d;font-weight:400">(dejar vacío para mantener la actual)</small></label>
                <img id="aImgActual" src="" alt="Foto actual" style="display:none;max-height:100px;border-radius:6px;object-fit:cover;margin-bottom:6px;">
                <input type="file" name="imagen" id="aImagen" accept="image/jpeg,image/png,image/jpg,image/webp">
                <img id="aPreview" src="" alt="Vista previa" style="display:none;margin-top:8px;max-height:100px;border-radius:6px;object-fit:cover">
            </div>

            {{-- MAPA --}}
            <div class="form-group full">
                <label>Ubicación en el mapa <small style="color:#6c757d;font-weight:400">(clic para marcar)</small></label>
                <div style="display:flex;gap:8px;margin-bottom:8px;">
                    <input type="text" id="aMapBuscar" placeholder="Ej: Plan 3000, Santa Cruz"
                        style="flex:1;padding:8px 12px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;font-family:inherit;">
                    <button type="button" onclick="buscarUbicacionA()"
                        style="padding:8px 14px;background:#185FA5;color:#fff;border:none;border-radius:6px;font-size:13px;cursor:pointer;">
                        Buscar
                    </button>
                </div>
                <div id="mapaAgente" style="height:260px;border-radius:8px;border:1px solid #dee2e6;"></div>
                <input type="hidden" name="latitud"  id="aLatitud">
                <input type="hidden" name="longitud" id="aLongitud">
                <p id="aCoordsTexto" style="font-size:11px;color:#6c757d;margin-top:5px;"></p>
            </div>

        </div>
        <div class="form-actions">
            <button type="button" class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
            <button type="submit" class="btn-primary" id="btnSubmit">Registrar</button>
        </div>
    </form>
</div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const SCZ = [-17.7833, -63.1822];

const overlay = document.getElementById('modalOverlay');
const form    = document.getElementById('formPropiedad');

function abrirModal() {
    form.reset();
    form.action = '{{ route("agente.propiedades.store") }}';
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('modalTitulo').textContent = 'Registrar propiedad';
    document.getElementById('btnSubmit').textContent = 'Registrar';
    overlay.classList.add('open');
    setTimeout(() => {
        iniciarMapaAgente();
        setTimeout(() => { if (mapaA) mapaA.invalidateSize(); }, 300);
    }, 500);
}

function cerrarModal() { overlay.classList.remove('open'); }

overlay.addEventListener('click', e => { if (e.target === overlay) cerrarModal(); });

function editarPropiedad(id, titulo, tipo, zona, precio, area, descripcion, estado, imagen, lat, lng) {
    form.action = `/agente/propiedades/${id}`;
    document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('propTitulo').value      = titulo;
    document.getElementById('propTipo').value        = tipo;
    document.getElementById('propZona').value        = zona;
    document.getElementById('propPrecio').value      = precio;
    document.getElementById('propArea').value        = area;
    document.getElementById('propDescripcion').value = descripcion;
    document.getElementById('propEstado').value      = estado;

    const imgActual = document.getElementById('aImgActual');
    if (imagen) { imgActual.src = imagen; imgActual.style.display = 'block'; }
    else        { imgActual.src = ''; imgActual.style.display = 'none'; }
    document.getElementById('aPreview').style.display = 'none';
    document.getElementById('aImagen').value = '';

    document.getElementById('modalTitulo').textContent = 'Editar propiedad';
    document.getElementById('btnSubmit').textContent   = 'Guardar cambios';
    overlay.classList.add('open');

    iniciarMapaAgente(lat ? parseFloat(lat) : null, lng ? parseFloat(lng) : null);
}

document.getElementById('aImagen').addEventListener('change', function() {
    const prev = document.getElementById('aPreview');
    if (this.files && this.files[0]) {
        prev.src = URL.createObjectURL(this.files[0]);
        prev.style.display = 'block';
    }
});

document.querySelectorAll('.btn-editar-propiedad').forEach(btn => {
    btn.addEventListener('click', function() {
        editarPropiedad(
            this.dataset.id,
            this.dataset.titulo,
            this.dataset.tipo,
            this.dataset.zona,
            this.dataset.precio,
            this.dataset.area,
            this.dataset.descripcion,
            this.dataset.estado,
            this.dataset.imagen,
            this.dataset.lat,
            this.dataset.lng
        );
    });
});

// ── MAPA AGENTE ───────────────────────────────────────
let mapaA = null, marcadorA = null;

function iniciarMapaAgente(lat = null, lng = null) {
    const centro = (lat && lng) ? [lat, lng] : SCZ;
    const zoom   = (lat && lng) ? 16 : 13;

    setTimeout(() => {
        if (mapaA) { mapaA.remove(); mapaA = null; marcadorA = null; }
        mapaA = L.map('mapaAgente').setView(centro, zoom);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            { attribution: '© OpenStreetMap' }).addTo(mapaA);
        if (lat && lng) colocarMarcadorA(lat, lng);
        mapaA.on('click', e => colocarMarcadorA(e.latlng.lat, e.latlng.lng));
        setTimeout(() => mapaA.invalidateSize(), 200);
    }, 500);
}

function colocarMarcadorA(lat, lng) {
    if (marcadorA) mapaA.removeLayer(marcadorA);
    marcadorA = L.marker([lat, lng], { draggable: true }).addTo(mapaA);
    marcadorA.on('dragend', () => {
        const p = marcadorA.getLatLng();
        guardarCoordsA(p.lat, p.lng);
    });
    guardarCoordsA(lat, lng);
}

function guardarCoordsA(lat, lng) {
    document.getElementById('aLatitud').value  = lat.toFixed(7);
    document.getElementById('aLongitud').value = lng.toFixed(7);
    document.getElementById('aCoordsTexto').textContent =
        `📍 ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
}

function buscarUbicacionA() {
    const q = document.getElementById('aMapBuscar').value.trim();
    if (!q) return;
    fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(q)}&format=json&limit=1`)
        .then(r => r.json()).then(data => {
            if (data.length) {
                const lat = parseFloat(data[0].lat), lng = parseFloat(data[0].lon);
                mapaA.setView([lat, lng], 16);
                colocarMarcadorA(lat, lng);
            } else alert('Ubicación no encontrada.');
        });
}

// (filters removed from this view)
</script>
@endpush