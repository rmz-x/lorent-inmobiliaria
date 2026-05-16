@extends('layouts.panel')
@section('titulo','Calendario')
@section('titulo_pagina','Calendario de visitas')

@section('contenido')
<div class="card">

    {{-- Leyenda fija --}}
    <div class="leyenda-fixed" style="display:flex;gap:16px;flex-wrap:wrap;border-bottom:1px solid #e2e6ea;">
        <span style="font-size:13px;font-weight:600;color:#1e293b;">Estados:</span>
        <span style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:500;">
            <span style="width:14px;height:14px;border-radius:4px;background:#f59e0b;display:inline-block;box-shadow:0 1px 2px rgba(0,0,0,0.1);"></span> Pendiente
        </span>
        <span style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:500;">
            <span style="width:14px;height:14px;border-radius:4px;background:#10b981;display:inline-block;box-shadow:0 1px 2px rgba(0,0,0,0.1);"></span> Aceptada
        </span>
        <span style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:500;">
            <span style="width:14px;height:14px;border-radius:4px;background:#ef4444;display:inline-block;box-shadow:0 1px 2px rgba(0,0,0,0.1);"></span> Rechazada
        </span>
        <span style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:500;">
            <span style="width:14px;height:14px;border-radius:4px;background:#6366f1;display:inline-block;box-shadow:0 1px 2px rgba(0,0,0,0.1);"></span> Completada
        </span>
    </div>

    {{-- Calendario --}}
    <div id="calendario"></div>
</div>

{{-- Modal detalle del evento --}}
<div id="eventoOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:32px;max-width:400px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h2 id="eventoTitulo" style="font-size:16px;font-weight:700;color:#0f172a;"></h2>
            <button onclick="cerrarEvento()" style="background:none;border:none;font-size:20px;cursor:pointer;color:#94a3b8;">✕</button>
        </div>
        <div style="display:flex;flex-direction:column;gap:12px;">
            <div style="display:flex;gap:10px;">
                <span style="font-size:13px;color:#6c757d;min-width:70px;">Estado</span>
                <span id="eventoEstado" style="font-size:13px;font-weight:600;"></span>
            </div>
            <div style="display:flex;gap:10px;">
                <span style="font-size:13px;color:#6c757d;min-width:70px;">Fecha</span>
                <span id="eventoFecha" style="font-size:13px;font-weight:500;"></span>
            </div>
            <div id="eventoClienteRow" style="display:flex;gap:10px;">
                <span style="font-size:13px;color:#6c757d;min-width:70px;">Cliente</span>
                <span id="eventoCliente" style="font-size:13px;font-weight:500;"></span>
            </div>
            <div style="display:flex;gap:10px;">
                <span style="font-size:13px;color:#6c757d;min-width:70px;">Zona</span>
                <span id="eventoZona" style="font-size:13px;font-weight:500;"></span>
            </div>
            <div style="display:flex;gap:10px;">
                <span style="font-size:13px;color:#6c757d;min-width:70px;">Mensaje</span>
                <span id="eventoMensaje" style="font-size:13px;color:#444;"></span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<style>
#calendario { max-width: 100%; width: 100%; }
.fc { font-size: 13px; }

/* Leyenda fija en desktop y mobile */
.leyenda-fixed {
    position: sticky;
    top: 0;
    z-index: 50;
    background: #f8fafc;
    padding: 12px 16px;
    border-radius: 10px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

/* Colores para badges */
.estado-pendiente  { background-color: #f59e0b !important; color: #fff !important; }
.estado-aceptada   { background-color: #10b981 !important; color: #fff !important; }
.estado-rechazada  { background-color: #ef4444 !important; color: #fff !important; }
.estado-completada { background-color: #6366f1 !important; color: #fff !important; }

/* Eventos en general (Desktop) */
.fc-event {
    border: none !important;
    border-radius: 4px !important;
    padding: 2px 4px !important;
    margin-bottom: 2px !important;
}

@media (max-width: 768px) {
    .card { padding: 12px !important; } /* menos padding lateral para dar espacio al calendario */
    
    /* Toolbar centralizada: Título arriba, [<] [Hoy] [>] medio, [Vistas] abajo */
    .fc .fc-toolbar {
        flex-direction: column;
        gap: 10px;
        align-items: center;
        margin-bottom: 12px !important;
    }
    .fc .fc-toolbar-chunk {
        display: flex;
        justify-content: center;
        width: 100%;
        flex-wrap: wrap;
    }
    .fc .fc-toolbar-title { font-size: 16px !important; text-align: center; }
    .fc .fc-button-group { display: flex; gap: 4px; justify-content: center; }
    .fc .fc-button {
        padding: 8px 14px !important;
        font-size: 13px !important;
        height: auto !important;
        border-radius: 8px !important;
    }

    /* FORZAR QUE TODO EL CALENDARIO (MES, SEMANA) ENCAJE AL 100% SIN SCROLL HORIZONTAL */
    .fc .fc-view-harness, .fc .fc-view-harness-active { width: 100% !important; }
    
    .fc .fc-scrollgrid,
    .fc .fc-scrollgrid table { 
        width: 100% !important; 
        border: none !important; 
        table-layout: fixed !important; 
        min-width: 0 !important; 
    }
    
    /* Prevenir cualquier scroll horizontal */
    .fc .fc-scroller { overflow-x: hidden !important; }
    
    /* Permitir scroll VERTICAL normal en Semana y Lista (para las horas y los items) */
    .fc-timegrid-view .fc-scroller,
    .fc-list-view .fc-scroller { overflow-y: auto !important; }
    
    /* Mes: sin scroll vertical (el JS se encarga de mostrar todo con height: auto) */
    .fc-daygrid-view .fc-scroller { overflow-y: hidden !important; }

    .fc th { font-size: 10px !important; border: none !important; padding: 4px 0 !important; }
    .fc td { border: none !important; }

    /* Celdas de MES con sombra y scroll interno */
    .fc .fc-daygrid-day-frame {
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        border: 1px solid #e2e8f0;
        margin: 1px;
        background: #fff;
        min-height: 55px !important; /* Más compacto para móvil */
        max-height: 60px !important;
        overflow-y: auto !important; 
        overflow-x: hidden !important;
        padding: 1px;
        display: flex;
        flex-direction: column;
    }
    .fc .fc-daygrid-day-frame::-webkit-scrollbar { width: 3px; }
    .fc .fc-daygrid-day-frame::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }

    /* Estilos mejorados para vista LISTA */
    .fc-list-table { border-radius: 8px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
    .fc-list-day-cushion { background-color: #f1f5f9 !important; font-weight: 700; color: #1e293b; padding: 8px 12px !important; }
    .fc-list-event { background: #fff !important; cursor: pointer; }
    .fc-list-event:hover td { background: #f8fafc !important; }
    .fc-list-event-title { font-size: 13px; font-weight: 600; padding: 10px 12px !important; }
    .fc-list-event-time { font-size: 11px; color: #64748b; padding: 10px 12px !important; }

    /* ESTILOS PARA VISTA SEMANA (timeGridWeek) */
    .fc-timegrid-col { border-left: 1px solid #e2e8f0 !important; }
    .fc-timegrid-slot { border-bottom: 1px solid #f1f5f9 !important; } /* Líneas horizontales de hora suaves */
    
    /* Resaltar día actual (Hoy) */
    .fc .fc-day-today { background-color: rgba(56, 189, 248, 0.04) !important; }
    .fc .fc-day-today .fc-col-header-cell-cushion { color: #0284c7 !important; font-weight: 800 !important; border-bottom: 2px solid #0284c7; padding-bottom: 2px; }

    /* Sombra y bordes redondeados en los eventos de la vista Semana */
    .fc-timegrid-event {
        border-radius: 6px !important;
        box-shadow: 0 2px 5px rgba(0,0,0,0.12) !important;
        border: none !important;
        margin: 1px !important;
        overflow: hidden;
    }

    /* Diseño del lateral de las horas (eje Y) */
    .fc-timegrid-axis-cushion, .fc-timegrid-slot-label-cushion {
        font-size: 10px !important;
        color: #64748b;
        font-weight: 600;
        padding: 0 4px !important;
    }

    .fc .fc-daygrid-day-top { justify-content: center; padding: 2px 0; }
    .fc .fc-daygrid-day-number { font-size: 12px !important; font-weight: 700; color: #334155; }
    
    .fc .fc-daygrid-day-events { min-height: 0 !important; padding: 0 !important; margin: 0 !important; }

    /* Badges de estado en móvil */
    .fc-daygrid-event {
        display: block !important;
        font-size: 9px !important;
        border-radius: 6px !important;
        padding: 3px !important;
        margin: 1px 0 2px 0 !important;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-weight: 600;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    .fc .fc-daygrid-event-harness { margin: 0 !important; }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    const eventosUrl = '{{ route("cliente.calendario.eventos") }}';
    const esMobil = window.innerWidth <= 768;

    const calendar = new FullCalendar.Calendar(document.getElementById('calendario'), {
        locale: 'es',
        height: 'auto',          /* Para que entren todas las semanas sin cortarse hacia abajo */
        contentHeight: 'auto',   /* Ajusta el contenido a la pantalla */
        allDaySlot: false,       /* Quita la fila fea de "all-day" en la vista de semana */
        slotMinTime: '07:00:00', /* Empieza a las 7am */
        slotMaxTime: '22:00:00', /* Termina a las 10pm */
        slotLabelFormat: {       /* Formato 08:00, 09:00 */
            hour: '2-digit',
            minute: '2-digit',
            omitZeroMinute: false,
            meridiem: false
        },
        initialView: 'dayGridMonth',
        dayHeaderFormat: esMobil ? { weekday: 'narrow' } : { weekday: 'short' },
        headerToolbar: esMobil
            ? { left: 'title', center: 'prev,today,next', right: 'dayGridMonth,timeGridWeek,listWeek' }
            : { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,listWeek' },
        buttonText: {
            today:    'Hoy',
            month:    'Mes',
            week:     'Semana',
            list:     'Lista'
        },
        events: eventosUrl,
        eventContent: function(arg) {
            const p = arg.event.extendedProps;
            const esMobil = window.innerWidth <= 768;
            
            // En móvil, renderizamos un badge colorido
            if (esMobil && arg.view.type === 'dayGridMonth') {
                return { 
                    html: `<div class="estado-${p.estado.toLowerCase()} text-white rounded-md px-1 py-[2px] text-center shadow-sm w-full truncate">
                              ${arg.event.title}
                           </div>` 
                };
            }

            // En Desktop
            let html = `<div class="fc-event-main-content estado-${p.estado.toLowerCase()} text-white p-1 rounded-sm w-full h-full">
                            <b>${arg.event.title}</b>`;
            if (arg.view.type === 'timeGridWeek' || arg.view.type === 'timeGridDay') {
                 html += `<br><small class="opacity-90">${p.cliente || p.agente || ''}</small>`;
            }
            html += `</div>`;
            return { html: html };
        },
        eventDidMount: function(info) {
            const p = info.event.extendedProps;
            // Tooltip nativo en hover (Desktop)
            info.el.title = `${info.event.title}\nZona: ${p.zona}\nEstado: ${p.estado}\nMensaje: ${p.mensaje || 'N/A'}`;
        },
        eventClick: function(info) {
            const p = info.event.extendedProps;

            document.getElementById('eventoTitulo').textContent  = info.event.title;
            document.getElementById('eventoFecha').textContent   = info.event.startStr;
            document.getElementById('eventoEstado').textContent  = p.estado;
            document.getElementById('eventoZona').textContent    = p.zona;
            document.getElementById('eventoMensaje').textContent = p.mensaje;

            const clienteRow = document.getElementById('eventoClienteRow');
            if (p.cliente) {
                document.getElementById('eventoCliente').textContent = p.cliente;
                clienteRow.style.display = 'flex';
            } else {
                clienteRow.style.display = 'none';
            }

            document.getElementById('eventoOverlay').style.display = 'flex';
        }
    });

    calendar.render();
});

function cerrarEvento() {
    document.getElementById('eventoOverlay').style.display = 'none';
}

document.getElementById('eventoOverlay').addEventListener('click', function(e) {
    if (e.target === this) cerrarEvento();
});
</script>
@endpush