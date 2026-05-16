@extends('layouts.panel')

@section('titulo', 'Propiedades')
@section('titulo_pagina', 'Propiedades')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #mapaRegistro,
    #mapaEditar {
        min-height: 260px;
        width: 100%;
        z-index: 1;
    }
    .modal .leaflet-container {
        width: 100%;
        height: 100%;
        border-radius: 8px;
    }

    .mobile-only-badge { display: none; }

    /* Mobile card design */
    @media (max-width: 640px) {
        @keyframes badge-pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.6), inset 0 1px 3px rgba(255,255,255,0.9); }
            70% { box-shadow: 0 0 0 10px rgba(255, 255, 255, 0), inset 0 1px 3px rgba(255,255,255,0.9); }
            100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0), inset 0 1px 3px rgba(255,255,255,0.9); }
        }
        .wrap-tabla {
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
                "agente agente"
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

        /* Bloques de información verticales (Grid Areas) */
        td[data-label="Tipo"] { grid-area: tipo; }
        td[data-label="Zona"] { grid-area: zona; }
        td[data-label="Precio"] { grid-area: precio; }
        td[data-label="Área"] { grid-area: area; }
        td[data-label="Agente"] { grid-area: agente; padding-bottom: 4px !important; }

        td[data-label="Tipo"],
        td[data-label="Zona"],
        td[data-label="Precio"],
        td[data-label="Área"],
        td[data-label="Agente"] {
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

        /* Acciones - Botones juntos en la misma fila */
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
        <span class="card-title">Lista de propiedades</span>

        <button class="btn-primary" onclick="abrirModal()">
            + Registrar propiedad
        </button>
    </div>
<div class="wrap-tabla w-full overflow-x-auto shadow-sm rounded-lg border border-gray-200">
<table class="tabla-propiedades min-w-[600px] w-full text-sm text-left">

        <thead>
            <tr>
                <th>#</th>
                <th>Título</th>
                <th>Tipo</th>
                <th>Zona</th>
                <th>Precio</th>
                <th>Área</th>
                <th>Estado</th>
                <th>Agente</th>
                <th>Acciones</th>
            </tr>
        </thead>

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

                <td data-label="Precio"><span class="td-value">
                    ${{ number_format($p->precio, 0, ',', '.') }}
                </span></td>

                <td data-label="Área"><span class="td-value">
                    {{ $p->area ? $p->area . ' m²' : '—' }}
                </span></td>

                <td data-label="Estado"><span class="td-value">
                    <span class="badge badge-{{ strtolower($p->estado) }}">
                        {{ $p->estado }}
                    </span>
                </span></td>

                <td data-label="Agente"><span class="td-value">
                    {{ $p->agente->nombre ?? 'Sin asignar' }}
                </span></td>

                <td data-label="Acciones">

                    <div class="action-btns flex flex-col sm:flex-row gap-2">

                        <button
                            class="btn-edit w-full sm:w-auto"
                            data-id="{{ $p->id }}"
                            data-titulo="{{ $p->titulo }}"
                            data-tipo="{{ $p->tipo }}"
                            data-zona="{{ $p->zona }}"
                            data-precio="{{ $p->precio }}"
                            data-area="{{ $p->area }}"
                            data-descripcion="{{ $p->descripcion }}"
                            data-estado="{{ $p->estado }}"
                            data-agente="{{ $p->agente_id }}"
                            data-imagen="{{ $p->imagen ? \Illuminate\Support\Facades\Storage::url($p->imagen) : '' }}"
                            data-lat="{{ $p->latitud }}"
                            data-lng="{{ $p->longitud }}"
                        >
                            ✏️ Editar
                        </button>

                        <form method="POST"
                            action="{{ route('admin.propiedades.destroy', $p) }}"
                            class="form-eliminar"
                            data-title="{{ $p->titulo }}"
                            style="display:inline">

                            @csrf
                            @method('DELETE')

                            <button type="button" class="btn-delete open-delete-modal w-full sm:w-auto"
                                data-name="{{ $p->titulo }}">
                                🗑️ Eliminar
                            </button>

                        </form>

                    </div>

                </td>

            </tr>

        @empty

            <tr>
                <td
                    colspan="9"
                    style="text-align:center;color:#6c757d;padding:20px"
                >
                    No hay propiedades registradas.
                </td>
            </tr>

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

    <div class="modal w-[95%] max-w-2xl mx-auto sm:w-full">

        <h2 id="modalTitulo">
            Registrar propiedad
        </h2>

        {{-- FORMULARIO REGISTRAR --}}

        <form
            id="formRegistrar"
            method="POST"
            action="{{ route('admin.propiedades.store') }}"
            enctype="multipart/form-data"
        >

            @csrf

            <div class="form-grid">

                <div class="form-group full">

                    <label>Título</label>

                    <input
                        type="text"
                        name="titulo"
                        id="rTitulo"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Tipo</label>

                    <select name="tipo" id="rTipo">
                        <option value="Venta">Venta</option>
                        <option value="Alquiler">Alquiler</option>
                        <option value="Anticretico">Anticretico</option>
                    </select>

                </div>

                <div class="form-group">

                    <label>Zona</label>

                    <input
                        type="text"
                        name="zona"
                        id="rZona"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Precio ($)</label>

                    <input
                        type="number"
                        name="precio"
                        id="rPrecio"
                        step="0.01"
                        min="0"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Área (m²)</label>

                    <input
                        type="number"
                        name="area"
                        id="rArea"
                        step="0.01"
                        min="0"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Estado</label>

                    <select name="estado" id="rEstado">
                        <option value="Disponible">Disponible</option>
                        <option value="Reservado">Reservado</option>
                        <option value="Vendido">Vendido</option>
                    </select>

                </div>

                <div class="form-group">

                    <label>Agente</label>

                    <select name="agente_id" id="rAgente">

                        <option value="">
                            Sin asignar
                        </option>

                        @foreach($agentes as $a)

                            <option value="{{ $a->id }}">
                                {{ $a->nombre }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="form-group full">

                    <label>Descripción</label>

                    <textarea
                        name="descripcion"
                        id="rDescripcion"
                        rows="3"
                        required
                    ></textarea>

                </div>

                <div class="form-group full">
                    <label>Imagen</label>
                    <input type="file" name="imagen" id="rImagen" accept="image/jpeg,image/png,image/jpg,image/webp">
                    <img id="rPreview" src="" alt="Vista previa" style="display:none;margin-top:8px;max-height:100px;border-radius:6px;object-fit:cover">
                </div>
                {{-- MAPA REGISTRAR --}}
                <div class="form-group full">
                    <label>Ubicación en el mapa <small style="color:#6c757d;font-weight:400">(clic para marcar)</small></label>
                    <div style="display:flex;gap:8px;margin-bottom:8px;">
                        <input type="text" id="rMapBuscar" placeholder="Ej: Plan 3000, Santa Cruz"
                            style="flex:1;padding:8px 12px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;font-family:inherit;">
                        <button type="button" onclick="buscarUbicacionR()"
                            style="padding:8px 14px;background:#185FA5;color:#fff;border:none;border-radius:6px;font-size:13px;cursor:pointer;">
                            Buscar
                        </button>
                    </div>
                    <div id="mapaRegistro" style="height:260px;border-radius:8px;border:1px solid #dee2e6;background:#e9ecef;"></div>
                    <input type="hidden" name="latitud"  id="rLatitud">
                    <input type="hidden" name="longitud" id="rLongitud">
                    <p id="rCoordsTexto" style="font-size:11px;color:#6c757d;margin-top:5px;"></p>
                </div>

            </div>

            <div class="form-actions flex flex-col sm:flex-row gap-2 mt-4">

                <button
                    type="button"
                    class="btn-cancel"
                    onclick="cerrarModal()"
                >
                    Cancelar
                </button>

                <button type="submit" class="btn-primary">
                    Registrar
                </button>

            </div>

        </form>

        {{-- FORMULARIO EDITAR --}}

        <form
            id="formEditar"
            method="POST"
            style="display:none"
            enctype="multipart/form-data"
        >

            @csrf
            @method('PUT')

            <div class="form-grid">

                <div class="form-group full">

                    <label>Título</label>

                    <input
                        type="text"
                        name="titulo"
                        id="eTitulo"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Tipo</label>

                    <select name="tipo" id="eTipo">
                        <option value="Venta">Venta</option>
                        <option value="Alquiler">Alquiler</option>
                        <option value="Anticretico">Anticretico</option>
                    </select>

                </div>

                <div class="form-group">

                    <label>Zona</label>

                    <input
                        type="text"
                        name="zona"
                        id="eZona"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Precio ($)</label>

                    <input
                        type="number"
                        name="precio"
                        id="ePrecio"
                        step="0.01"
                        min="0"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Área (m²)</label>

                    <input
                        type="number"
                        name="area"
                        id="eArea"
                        step="0.01"
                        min="0"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Estado</label>

                    <select name="estado" id="eEstado">
                        <option value="Disponible">Disponible</option>
                        <option value="Reservado">Reservado</option>
                        <option value="Vendido">Vendido</option>
                    </select>

                </div>

                <div class="form-group">

                    <label>Agente</label>

                    <select name="agente_id" id="eAgente">

                        <option value="">
                            Sin asignar
                        </option>

                        @foreach($agentes as $a)

                            <option value="{{ $a->id }}">
                                {{ $a->nombre }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="form-group full">
                    <label>Imagen <small style="color:#6c757d;font-weight:400">(dejar vacío para mantener la actual)</small></label>
                    <img id="eImgActual" src="" alt="Foto actual" style="display:none;max-height:100px;border-radius:6px;object-fit:cover;margin-bottom:6px">
                    <input type="file" name="imagen" id="eImagen" accept="image/jpeg,image/png,image/jpg,image/webp">
                    <img id="ePreview" src="" alt="Vista previa" style="display:none;margin-top:8px;max-height:100px;border-radius:6px;object-fit:cover">
                </div>

                <div class="form-group full">

                    <label>Descripción</label>

                    <textarea
                        name="descripcion"
                        id="eDescripcion"
                        rows="3"
                        required
                    ></textarea>

                </div>

                {{-- MAPA EDITAR --}}
                <div class="form-group full">
                    <label>Ubicación en el mapa <small style="color:#6c757d;font-weight:400">(clic para mover el marcador)</small></label>
                    <div style="display:flex;gap:8px;margin-bottom:8px;">
                        <input type="text" id="eMapBuscar" placeholder="Ej: Plan 3000, Santa Cruz"
                            style="flex:1;padding:8px 12px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;font-family:inherit;">
                        <button type="button" onclick="buscarUbicacionE()"
                            style="padding:8px 14px;background:#185FA5;color:#fff;border:none;border-radius:6px;font-size:13px;cursor:pointer;">
                            Buscar
                        </button>
                    </div>
                    <div id="mapaEditar" style="height:260px;border-radius:8px;border:1px solid #dee2e6;"></div>
                    <input type="hidden" name="latitud"  id="eLatitud">
                    <input type="hidden" name="longitud" id="eLongitud">
                    <p id="eCoordsTexto" style="font-size:11px;color:#6c757d;margin-top:5px;"></p>
                </div>

            </div>

            <div class="form-actions flex flex-col sm:flex-row gap-2 mt-4">

                <button
                    type="button"
                    class="btn-cancel"
                    onclick="cerrarModal()"
                >
                    Cancelar
                </button>

                <button type="submit" class="btn-primary">
                    Guardar cambios
                </button>

            </div>

        </form>

    </div>

</div>



@endsection

@push('scripts')

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>     

<script>

const SCZ = [-17.7833, -63.1822]; // Coordenadas aproximadas del centro de Santa Cruz para centrar los mapas inicialmente

const overlay = document.getElementById('modalOverlay');

const formRegistrar = document.getElementById('formRegistrar');

const formEditar = document.getElementById('formEditar');

const titulo = document.getElementById('modalTitulo');

function abrirModal() {

    formRegistrar.reset();

    formRegistrar.style.display = 'block';

    formEditar.style.display = 'none';

    titulo.textContent = 'Registrar propiedad';

    document.getElementById('rLatitud').value = '';
    document.getElementById('rLongitud').value = '';
    document.getElementById('rCoordsTexto').textContent = '';
    document.getElementById('rMapBuscar').value = '';

    overlay.classList.add('open');

    setTimeout(() => {
        iniciarMapaRegistro();
        setTimeout(() => { if (mapaR) mapaR.invalidateSize(); }, 300);
    }, 350);
}

function cerrarModal() {

    overlay.classList.remove('open');

    formRegistrar.reset();

    formEditar.reset();

    formRegistrar.style.display = 'block';

    formEditar.style.display = 'none';
}

overlay.addEventListener('click', function(e) {

    if (e.target === overlay) {

        cerrarModal();
    }
});

function editarPropiedad(
    id,
    t,
    tipo,
    zona,
    precio,
    area,
    desc,
    estado,
    agenteId,
    imagen,
    lat,
    lng
) {

    formEditar.action = `/admin/propiedades/${id}`;

    document.getElementById('eTitulo').value = t;
    document.getElementById('eTipo').value = tipo;
    document.getElementById('eZona').value = zona;
    document.getElementById('ePrecio').value = precio;
    document.getElementById('eArea').value = area;
    document.getElementById('eDescripcion').value = desc;
    document.getElementById('eEstado').value = estado;
    document.getElementById('eAgente').value = agenteId || '';

    const imgActual = document.getElementById('eImgActual');
    if (imagen) {
        imgActual.src = imagen;
        imgActual.style.display = 'block';
    } else {
        imgActual.src = '';
        imgActual.style.display = 'none';
    }
    document.getElementById('ePreview').style.display = 'none';
    document.getElementById('eImagen').value = '';

    formRegistrar.style.display = 'none';

    formEditar.style.display = 'block';

    titulo.textContent = 'Editar propiedad';

    overlay.classList.add('open');

    iniciarMapaEditar(
        lat ? parseFloat(lat) : null,
        lng ? parseFloat(lng) : null
    );
}

// BOTONES EDITAR

document.querySelectorAll('.btn-edit')
.forEach(button => {

    button.addEventListener('click', function () {

        editarPropiedad(

            this.dataset.id,
            this.dataset.titulo,
            this.dataset.tipo,
            this.dataset.zona,
            this.dataset.precio,
            this.dataset.area,
            this.dataset.descripcion,
            this.dataset.estado,
            this.dataset.agente,
            this.dataset.imagen,    
            this.dataset.lat,   // ← nuevo
            this.dataset.lng    // ← nuevo
        );

    });

});

document.getElementById('rImagen').addEventListener('change', function(){
    const prev = document.getElementById('rPreview');
    if (this.files && this.files[0]) {
        prev.src = URL.createObjectURL(this.files[0]);
        prev.style.display = 'block';
    }
});

document.getElementById('eImagen').addEventListener('change', function(){
    const prev = document.getElementById('ePreview');
    if (this.files && this.files[0]) {
        prev.src = URL.createObjectURL(this.files[0]);
        prev.style.display = 'block';
    }
});

//MAPA REGISTRAR
let mapaR = null, marcadorR = null;

function iniciarMapaRegistro(lat = null, lng = null) {
    if (typeof L === 'undefined') {
        console.error('Leaflet no cargó. Revisa la conexión o recarga la página.');
        return;
    }
    const el = document.getElementById('mapaRegistro');
    if (!el) return;

    const centro = (lat && lng) ? [lat, lng] : SCZ;
    const zoom   = (lat && lng) ? 16 : 13;

    setTimeout(() => {
        if (mapaR) { mapaR.remove(); mapaR = null; marcadorR = null; }
        mapaR = L.map(el, { preferCanvas: true }).setView(centro, zoom);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            { attribution: '© OpenStreetMap' }).addTo(mapaR);
        if (lat && lng) colocarMarcadorR(lat, lng);
        mapaR.on('click', e => colocarMarcadorR(e.latlng.lat, e.latlng.lng));
        setTimeout(() => mapaR.invalidateSize(), 200);
    }, 350);
}

function colocarMarcadorR(lat, lng) {
    if (marcadorR) mapaR.removeLayer(marcadorR);
    marcadorR = L.marker([lat, lng], { draggable: true }).addTo(mapaR);
    marcadorR.on('dragend', () => {
        const p = marcadorR.getLatLng();
        guardarCoordsR(p.lat, p.lng);
    });
    guardarCoordsR(lat, lng);
}

function guardarCoordsR(lat, lng) {
    document.getElementById('rLatitud').value  = lat.toFixed(7);
    document.getElementById('rLongitud').value = lng.toFixed(7);
    document.getElementById('rCoordsTexto').textContent =
        `📍 ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
}

function buscarUbicacionR() {
    const q = document.getElementById('rMapBuscar').value.trim();
    if (!q) return;
    fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(q)}&format=json&limit=1`)
        .then(r => r.json()).then(data => {
            if (data.length) {
                const lat = parseFloat(data[0].lat), lng = parseFloat(data[0].lon);
                mapaR.setView([lat, lng], 16);
                colocarMarcadorR(lat, lng);
            } else alert('Ubicación no encontrada.');
        });
}

//MAPA EDITAR
let mapaE = null, marcadorE = null;

function iniciarMapaEditar(lat, lng) {
    if (typeof L === 'undefined') return;
    const el = document.getElementById('mapaEditar');
    if (!el) return;

    const centro = (lat && lng) ? [lat, lng] : SCZ;
    const zoom   = (lat && lng) ? 16 : 13;

    setTimeout(() => {
        if (mapaE) { mapaE.remove(); mapaE = null; marcadorE = null; }
        mapaE = L.map(el, { preferCanvas: true }).setView(centro, zoom);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            { attribution: '© OpenStreetMap' }).addTo(mapaE);
        if (lat && lng) colocarMarcadorE(lat, lng);
        mapaE.on('click', e => colocarMarcadorE(e.latlng.lat, e.latlng.lng));
        setTimeout(() => mapaE.invalidateSize(), 200);
    }, 350);
}

function colocarMarcadorE(lat, lng) {
    if (marcadorE) mapaE.removeLayer(marcadorE);
    marcadorE = L.marker([lat, lng], { draggable: true }).addTo(mapaE);
    marcadorE.on('dragend', () => {
        const p = marcadorE.getLatLng();
        guardarCoordsE(p.lat, p.lng);
    });
    guardarCoordsE(lat, lng);
}

function guardarCoordsE(lat, lng) {
    document.getElementById('eLatitud').value  = lat.toFixed(7);
    document.getElementById('eLongitud').value = lng.toFixed(7);
    document.getElementById('eCoordsTexto').textContent =
        `📍 ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
}

function buscarUbicacionE() {
    const q = document.getElementById('eMapBuscar').value.trim();
    if (!q) return;
    fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(q)}&format=json&limit=1`)
        .then(r => r.json()).then(data => {
            if (data.length) {
                const lat = parseFloat(data[0].lat), lng = parseFloat(data[0].lon);
                mapaE.setView([lat, lng], 16);
                colocarMarcadorE(lat, lng);
            } else alert('Ubicación no encontrada.');
        });
}

</script>

@endpush