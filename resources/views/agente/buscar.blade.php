@extends('layouts.panel')
@section('titulo', 'Buscar propiedades')
@section('titulo_pagina', 'Mis propiedades')

@section('contenido')

{{-- ══════════ BUSCADOR PRINCIPAL ══════════ --}}
<div style="
    background: linear-gradient(135deg, #0f4c35 0%, #083326 100%);
    border-radius: 14px;
    padding: 28px 24px;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
">
    <div style="position:absolute;top:-30px;right:-30px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,0.04);pointer-events:none"></div>
    <div style="position:absolute;bottom:-50px;right:80px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.03);pointer-events:none"></div>

    <p style="color:rgba(255,255,255,0.6);font-size:12px;margin-bottom:6px;position:relative">
        Panel Agente
    </p>
    <h2 style="color:#fff;font-size:20px;font-weight:700;margin-bottom:4px;position:relative">
        Buscar mis propiedades
    </h2>
    <p style="color:rgba(255,255,255,0.55);font-size:13px;margin-bottom:20px;position:relative">
        Tienes {{ $totalPropiedades }} propiedades registradas a tu nombre
    </p>

    <form method="GET" action="{{ route('agente.buscar') }}" id="formBuscar" style="position:relative">
        <div style="display:flex;gap:10px;flex-wrap:wrap">
            <div style="flex:1;min-width:220px;position:relative">
                <i class="ti ti-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:17px;pointer-events:none"></i>
                <input
                    type="text"
                    name="q"
                    value="{{ $q }}"
                    placeholder="Título, zona, descripción..."
                    autocomplete="off"
                    style="
                        width:100%; padding:12px 14px 12px 40px;
                        border:1.5px solid rgba(255,255,255,0.12);
                        border-radius:10px;
                        background:rgba(255,255,255,0.08);
                        color:#fff; font-size:13px; font-family:inherit;
                        outline:none; transition:border-color .2s;
                    "
                    onfocus="this.style.borderColor='rgba(99,247,160,0.8)'"
                    onblur="this.style.borderColor='rgba(255,255,255,0.12)'"
                >
            </div>
            <button type="submit" style="
                padding:12px 22px;
                background: linear-gradient(135deg, #10b981, #059669);
                color:#fff; border:none; border-radius:10px;
                font-size:13px; font-weight:700; font-family:inherit;
                cursor:pointer; transition:all .2s ease;
                display:flex; align-items:center; gap:7px; white-space:nowrap;
                box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
            "
            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 15px rgba(16, 185, 129, 0.35)'"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.25)'">
                <i class="ti ti-search" style="font-size:15px"></i>
                Buscar
            </button>

            <!-- Botón filtros (visible en móvil) -->
            <button type="button" id="btnOpenFiltersMobile" onclick="openFiltersMobile()" style="padding:12px 14px;border-radius:10px;border:1px solid rgba(255,255,255,0.12);background:rgba(255,255,255,0.06);color:#fff;font-size:13px;display:none;align-items:center;gap:8px">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 5h18v2l-7 7v5l-4 2v-7L3 7V5z" fill="#fff"/></svg>
                Filtros
            </button>

            <a href="{{ route('agente.propiedades') }}" style="
                padding:12px 18px;
                background:rgba(255,255,255,0.1); color:#fff;
                border:1.5px solid rgba(255,255,255,0.2); border-radius:10px;
                font-size:13px; font-weight:700; font-family:inherit;
                cursor:pointer; transition:all .2s ease;
                display:flex; align-items:center; gap:7px; white-space:nowrap;
                text-decoration:none;
                backdrop-filter: blur(4px);
            "
            onmouseover="this.style.background='rgba(255,255,255,0.2)'; this.style.transform='translateY(-1px)'"
            onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.transform='translateY(0)'">
                <i class="ti ti-plus" style="font-size:15px"></i>
                Nueva propiedad
            </a>

            @if($q || $tipo !== 'Todas' || $estado !== 'Todas' || $precioMax)
            <a href="{{ route('agente.buscar') }}" style="
                padding:12px 18px;
                background:rgba(255,255,255,0.08); color:rgba(255,255,255,0.7);
                border:1.5px solid rgba(255,255,255,0.12); border-radius:10px;
                font-size:13px; font-family:inherit; cursor:pointer;
                display:flex; align-items:center; gap:6px; white-space:nowrap;
                text-decoration:none;
            ">
                <i class="ti ti-x" style="font-size:14px"></i>
                Limpiar
            </a>
            @endif
        </div>
    </form>
</div>

{{-- ══════════ FILTROS + RESULTADOS ══════════ --}}
<div id="gridBuscar" style="display:grid;grid-template-columns:220px 1fr;gap:16px;align-items:start">

<!-- Backdrop para filtros en móvil -->
<div id="filtersBackdrop" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:900"></div>

    {{-- PANEL FILTROS --}}
    <div class="card" style="padding:0;overflow:hidden" id="panelFiltros">
        <div style="padding:14px 16px;border-bottom:1px solid #e2e8f0">
            <span style="font-size:13px;font-weight:600;color:#0f172a;display:flex;align-items:center;gap:7px">
                <i class="ti ti-adjustments-horizontal" style="font-size:16px;color:#1a9e5c"></i>
                Filtros
            </span>
        </div>

        <form method="GET" action="{{ route('agente.buscar') }}" style="padding:16px">
            <input type="hidden" name="q" value="{{ $q }}">

            {{-- Tipo --}}
            <div style="margin-bottom:16px">
                <label style="font-size:11px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:8px">
                    Tipo
                </label>
                @foreach(['Todas','Venta','Alquiler','Anticretico'] as $t)
                <label style="display:flex;align-items:center;gap:8px;padding:6px 0;cursor:pointer">
                    <input
                        type="radio" name="tipo" value="{{ $t }}"
                        {{ $tipo === $t ? 'checked' : '' }}
                        style="accent-color:#1a9e5c;width:15px;height:15px;cursor:pointer"
                        onchange="this.form.submit()"
                    >
                    <span style="font-size:13px;color:#374151">{{ $t }}</span>
                </label>
                @endforeach
            </div>

            <div style="height:1px;background:#e2e8f0;margin-bottom:16px"></div>

            {{-- Estado --}}
            <div style="margin-bottom:16px">
                <label style="font-size:11px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:8px">
                    Estado
                </label>
                @foreach(['Todas'=>'Todos','Disponible'=>'Disponible','Reservado'=>'Reservado','Vendido'=>'Vendido'] as $val => $label)
                <label style="display:flex;align-items:center;gap:8px;padding:6px 0;cursor:pointer">
                    <input
                        type="radio" name="estado" value="{{ $val }}"
                        {{ $estado === $val ? 'checked' : '' }}
                        style="accent-color:#1a9e5c;width:15px;height:15px;cursor:pointer"
                        onchange="this.form.submit()"
                    >
                    <span style="font-size:13px;color:#374151">{{ $label }}</span>
                </label>
                @endforeach
            </div>

            <div style="height:1px;background:#e2e8f0;margin-bottom:16px"></div>

            {{-- Precio máximo --}}
            <div style="margin-bottom:16px">
                <label style="font-size:11px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:8px">
                    Precio máximo ($)
                </label>
                <input
                    type="number"
                    name="precio_max"
                    value="{{ $precioMax }}"
                    placeholder="Ej: 200000"
                    min="0"
                    style="
                        width:100%; padding:9px 10px;
                        border:1.5px solid #e2e8f0; border-radius:8px;
                        font-size:13px; font-family:inherit; color:#0f172a;
                        background:#f8fafc; outline:none;
                        transition:border-color .15s;
                    "
                    onfocus="this.style.borderColor='#1a9e5c'"
                    onblur="this.style.borderColor='#e2e8f0'"
                >
            </div>

            {{-- Área mínima --}}
            <div style="margin-bottom:18px">
                <label style="font-size:11px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:8px">
                    Área mínima (m²)
                </label>
                <input
                    type="number"
                    name="area_min"
                    value="{{ $areaMin }}"
                    placeholder="Ej: 100"
                    min="0"
                    style="
                        width:100%; padding:9px 10px;
                        border:1.5px solid #e2e8f0; border-radius:8px;
                        font-size:13px; font-family:inherit; color:#0f172a;
                        background:#f8fafc; outline:none;
                        transition:border-color .15s;
                    "
                    onfocus="this.style.borderColor='#1a9e5c'"
                    onblur="this.style.borderColor='#e2e8f0'"
                >
            </div>

            <button type="submit" style="
                width:100%; padding:11px;
                background: linear-gradient(135deg, #10b981, #059669);
                color:#fff; border:none; border-radius:8px;
                font-size:13px; font-weight:700; font-family:inherit;
                cursor:pointer; transition:all .2s ease;
                box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
            "
            onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 12px rgba(16, 185, 129, 0.3)'"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 10px rgba(16, 185, 129, 0.2)'">
                Aplicar filtros
            </button>
        </form>
    </div>

<style>
@media (max-width:640px) {
    #panelFiltros { display:none; }
    #panelFiltros.mobile-open {
        display:block !important;
        position:fixed; left:0; right:0; bottom:0; top:auto;
        width:100%; max-height:80vh; overflow:auto; z-index:1000;
        border-radius:12px 12px 0 0; box-shadow: 0 -10px 30px rgba(2,6,23,0.2);
        background:#fff;
    }
    #btnOpenFiltersMobile { display:inline-flex !important; }
}
</style>

    {{-- RESULTADOS --}}
    <div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:8px">
            <div>
                <span style="font-size:14px;font-weight:600;color:#0f172a">Resultados</span>
                <span style="font-size:13px;color:#64748b;margin-left:6px">
                    ({{ $propiedades->count() }} encontradas)
                </span>
            </div>
            <div style="display:flex;gap:6px;flex-wrap:wrap">
                @if($q)
                <span style="display:inline-flex;align-items:center;gap:5px;background:#d1fae5;color:#065f46;font-size:11px;font-weight:500;padding:4px 10px;border-radius:20px">
                    <i class="ti ti-search" style="font-size:12px"></i>
                    "{{ $q }}"
                </span>
                @endif
                @if($tipo !== 'Todas')
                <span style="display:inline-flex;align-items:center;gap:5px;background:#d1fae5;color:#065f46;font-size:11px;font-weight:500;padding:4px 10px;border-radius:20px">
                    <i class="ti ti-tag" style="font-size:12px"></i>
                    {{ $tipo }}
                </span>
                @endif
            </div>
        </div>

    <script>
    function openFiltersMobile(){
        const p = document.getElementById('panelFiltros');
        const b = document.getElementById('filtersBackdrop');
        if(!p||!b) return;
        p.classList.add('mobile-open');
        b.style.display='block';
        b.addEventListener('click', closeFiltersMobile);
        document.body.style.overflow='hidden';
    }
    function closeFiltersMobile(){
        const p = document.getElementById('panelFiltros');
        const b = document.getElementById('filtersBackdrop');
        if(!p||!b) return;
        p.classList.remove('mobile-open');
        b.style.display='none';
        document.body.style.overflow='';
    }
    document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') closeFiltersMobile(); });
    </script>

        @forelse($propiedades as $p)
        <div class="card" style="margin-bottom:12px;transition:box-shadow .2s"
             onmouseover="this.style.boxShadow='0 4px 20px rgba(26,158,92,0.12)'"
             onmouseout="this.style.boxShadow=''">
            <div class="flex flex-col sm:flex-row gap-0 overflow-hidden rounded-xl">

                <div class="w-full sm:w-48 flex-shrink-0 relative" style="background:{{ $p->tipo==='Venta' ? '#1e3a5f' : ($p->tipo==='Alquiler' ? '#0f4c35' : '#2e1a5f') }};">
                    @if($p->imagen)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($p->imagen) }}" alt="{{ $p->titulo }}" class="w-full h-48 object-cover rounded-t-lg sm:h-full sm:rounded-l-lg sm:rounded-tr-none">
                    @else
                        <i class="ti ti-building-estate" style="font-size:32px;color:rgba(255,255,255,0.2)"></i>
                    @endif
                    <span style="
                        position:absolute; bottom:8px; left:8px;
                        font-size:10px; font-weight:600; padding:3px 8px; border-radius:20px;
                        {{ $p->tipo==='Venta' ? 'background:#dbeafe;color:#1e40af' : ($p->tipo==='Alquiler' ? 'background:#d1fae5;color:#065f46' : 'background:#ede9fe;color:#5b21b6') }}
                    ">{{ $p->tipo }}</span>
                </div>

                <div class="flex-1 p-4 flex flex-col justify-between gap-3 sm:gap-4">
                    <div style="flex:1;min-width:150px">
                        <p style="font-size:14px;font-weight:600;color:#0f172a;margin-bottom:3px">
                            {{ $p->titulo }}
                        </p>
                        <p style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:4px;margin-bottom:8px">
                            <i class="ti ti-map-pin" style="font-size:13px"></i>
                            {{ $p->zona }}
                        </p>
                        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
                            @if($p->area)
                            <span style="font-size:11px;color:#64748b;display:flex;align-items:center;gap:4px">
                                <i class="ti ti-ruler" style="font-size:13px"></i>
                                {{ $p->area }} m²
                            </span>
                            @endif
                            <span style="
                                font-size:11px; font-weight:600; padding:2px 9px; border-radius:20px;
                                {{ $p->estado==='Disponible' ? 'background:#d1fae5;color:#065f46' : ($p->estado==='Reservado' ? 'background:#fef3c7;color:#92400e' : 'background:#fee2e2;color:#991b1b') }}
                            ">{{ $p->estado }}</span>
                        </div>
                    </div>

                    <div style="text-align:right;flex-shrink:0">
                        <p style="font-size:18px;font-weight:700;color:#1a9e5c;margin-bottom:10px">
                            ${{ number_format($p->precio,0,',','.') }}
                        </p>
                        <div class="prop-actions-container">
                            <button
                                type="button"
                                onclick="editarPropiedad(
                                    {{ $p->id }},
                                    '{{ addslashes($p->titulo) }}',
                                    '{{ $p->tipo }}',
                                    '{{ addslashes($p->zona) }}',
                                    '{{ $p->precio }}',
                                    '{{ $p->area }}',
                                    '{{ addslashes($p->descripcion) }}',
                                    '{{ $p->estado }}',
                                    '{{ $p->agente_id }}',
                                    '{{ $p->imagen ? \Illuminate\Support\Facades\Storage::url($p->imagen) : '' }}'
                                    )"
                                style="
                                    padding:8px 16px;
                                    background: linear-gradient(135deg, #10b981, #059669);
                                    color:#fff; border:none; border-radius:8px;
                                    font-size:12px; font-weight:700; font-family:inherit;
                                    cursor:pointer; transition:all .2s;
                                    box-shadow: 0 3px 8px rgba(16, 185, 129, 0.2);
                                    flex: 1; max-width: 120px;
                                "
                                onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 5px 12px rgba(16, 185, 129, 0.3)'"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 3px 8px rgba(16, 185, 129, 0.2)'">
                                <i class="ti ti-pencil" style="font-size:13px"></i>
                                Editar
                            </button>
                            <form method="POST"
                                  action="{{ route('agente.propiedades.destroy', $p) }}"
                                  class="form-eliminar"
                                  data-title="{{ $p->titulo }}"
                                  style="display:contents">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-delete open-delete-modal"
                                    style="
                                        padding:8px 16px;
                                        background: linear-gradient(135deg, #ef4444, #dc2626);
                                        color:#fff; border:none; border-radius:8px;
                                        font-size:12px; font-weight:700; font-family:inherit;
                                        cursor:pointer; transition:all .2s;
                                        box-shadow: 0 3px 8px rgba(239, 68, 68, 0.2);
                                        flex: 1; max-width: 120px;
                                    "
                                    onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 5px 12px rgba(239, 68, 68, 0.3)'"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 3px 8px rgba(239, 68, 68, 0.2)'">
                                    <i class="ti ti-trash" style="font-size:13px"></i>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="card" style="padding:48px 24px;text-align:center">
            <div style="width:64px;height:64px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
                <i class="ti ti-building-off" style="font-size:28px;color:#94a3b8"></i>
            </div>
            <p style="font-size:15px;font-weight:600;color:#0f172a;margin-bottom:6px">
                No se encontraron propiedades
            </p>
            <p style="font-size:13px;color:#64748b;margin-bottom:20px">
                Intenta con otros términos o ajusta los filtros
            </p>
            <a href="{{ route('agente.buscar') }}" style="
                display:inline-flex; align-items:center; gap:6px;
                padding:9px 20px; background:#1a9e5c; color:#fff;
                border-radius:8px; font-size:13px; font-weight:600;
                text-decoration:none;
            ">
                <i class="ti ti-refresh" style="font-size:15px"></i>
                Ver todas mis propiedades
            </a>
        </div>
        @endforelse
    </div>
</div>

{{-- MODAL EDITAR --}}
<div class="modal-overlay" id="modalOverlay">
    <div class="modal w-[95%] max-w-lg mx-auto sm:w-full">
        <h2 id="modalTitulo">Editar propiedad</h2>
        <form id="formEditar" method="POST" action="#" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <div class="form-group full"><label>Título</label><input type="text" name="titulo" id="eTitulo" required></div>
                <div class="form-group"><label>Tipo</label>
                    <select name="tipo" id="eTipo">
                        <option value="Venta">Venta</option>
                        <option value="Alquiler">Alquiler</option>
                        <option value="Anticretico">Anticretico</option>
                    </select>
                </div>
                <div class="form-group"><label>Zona</label><input type="text" name="zona" id="eZona" required></div>
                <div class="form-group"><label>Precio ($)</label><input type="number" name="precio" id="ePrecio" step="0.01" min="0" required></div>
                <div class="form-group"><label>Área (m²)</label><input type="number" name="area" id="eArea" step="0.01" min="0"></div>
                <div class="form-group"><label>Estado</label>
                    <select name="estado" id="eEstado">
                        <option value="Disponible">Disponible</option>
                        <option value="Reservado">Reservado</option>
                        <option value="Vendido">Vendido</option>
                    </select>
                </div>
                <div class="form-group full"><label>Descripción</label><textarea name="descripcion" id="eDescripcion" rows="3" required></textarea></div>
                <div class="form-group full">
                    <label>Imagen <small style="color:#6c757d;font-weight:400">(dejar vacío para mantener la actual)</small></label>
                    <img id="cImgActual" src="" alt="Foto actual" style="display:none;max-height:80px;border-radius:6px;object-fit:cover;margin-bottom:6px">
                    <input type="file" name="imagen" id="cImagen" accept="image/jpeg,image/png,image/jpg,image/webp">
                    <img id="cPreview" src="" alt="Vista previa" style="display:none;margin-top:6px;max-height:80px;border-radius:6px;object-fit:cover">
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
                <button type="button" class="btn-primary" onclick="guardarEdicion()">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<style>
.prop-actions-container {
    display: flex;
    gap: 8px;
    justify-content: flex-end;
    width: 100%;
    margin-top: 8px;
}
@media (max-width: 768px) {
    #panelFiltros { display: none; }
    #panelFiltros.abierto { display: block; }
    #gridBuscar { grid-template-columns: 1fr !important; }
    .prop-actions-container {
        justify-content: center !important;
        margin-top: 14px !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
const overlay = document.getElementById('modalOverlay');

function cerrarModal() { overlay.classList.remove('open'); }

overlay.addEventListener('click', e => { if (e.target === overlay) cerrarModal(); });

function editarPropiedad(id, titulo, tipo, zona, precio, area, descripcion, estado, agenteId, imagen) {
    document.getElementById('formEditar').action = `/agente/propiedades/${id}`;
    document.getElementById('eTitulo').value      = titulo;
    document.getElementById('eTipo').value        = tipo;
    document.getElementById('eZona').value        = zona;
    document.getElementById('ePrecio').value      = precio;
    document.getElementById('eArea').value        = area;
    document.getElementById('eDescripcion').value = descripcion;
    document.getElementById('eEstado').value      = estado;
    const imgActual = document.getElementById('cImgActual');
    if (imagen) { imgActual.src = imagen; imgActual.style.display = 'block'; }
    else { imgActual.src = ''; imgActual.style.display = 'none'; }
    document.getElementById('cPreview').style.display = 'none';
    document.getElementById('cImagen').value = '';
    overlay.classList.add('open');
}

function guardarEdicion() {
    const form = document.getElementById('formEditar');
    if (form.action && form.action !== '#' && form.action !== '') {
        form.submit();
    }
}

document.getElementById('cImagen').addEventListener('change', function(){
    const prev = document.getElementById('cPreview');
    if (this.files && this.files[0]) { prev.src = URL.createObjectURL(this.files[0]); prev.style.display = 'block'; }
});
</script>
@endpush