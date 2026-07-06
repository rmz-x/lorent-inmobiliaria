<?php
namespace App\Http\Controllers;

use App\Models\{HistorialCliente, Notificacion, Propiedad, Recomendacion, SolicitudVisita, Usuario, RegistroActividad};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    // dashboard para el administrador, muestra estadísticas generales del sistema como total de propiedades, usuarios, ventas y las últimas propiedades registradas.
    public function admin()
    {
        $totalProps    = Propiedad::count();
        $disponibles   = Propiedad::where('estado','Disponible')->count();
        $reservadas    = Propiedad::where('estado','Reservado')->count();
        $totalUsuarios = Usuario::count();
        $totalVentas   = Propiedad::where('estado','Vendido')->count();
        $totalVisitas  = SolicitudVisita::count();
        $ultimas       = Propiedad::with('agente')->orderBy('id','desc')->limit(5)->get();
        $visitas       = SolicitudVisita::with(['propiedad','cliente'])->orderBy('fecha_solicitada')->limit(6)->get();
        $actividad     = RegistroActividad::orderBy('fecha_hora', 'desc')->limit(6)->get();
        $notificaciones = Schema::hasTable('notificaciones')
            ? Notificacion::with('propiedad')->orderBy('fecha_envio', 'desc')->limit(5)->get()
            : collect();

        $porEstado = [
            'Disponible' => $disponibles,
            'Reservado' => $reservadas,
            'Vendido' => $totalVentas,
        ];

        $porTipo = [
            'Venta' => Propiedad::where('tipo', 'Venta')->count(),
            'Alquiler' => Propiedad::where('tipo', 'Alquiler')->count(),
            'Anticretico' => Propiedad::where('tipo', 'Anticretico')->count(),
        ];

        $propiedadesPorMes = RegistroActividad::query()
            ->where('accion', 'Propiedad registrada')
            ->selectRaw("TO_CHAR(fecha_hora, 'YYYY-MM') as mes, COUNT(*) as total")
            ->groupBy('mes')
            ->orderBy('mes')
            ->limit(6)
            ->pluck('total', 'mes');

        $mapaPropiedades = Propiedad::with('agente')
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->get()
            ->map(fn ($p) => [
                'titulo' => $p->titulo,
                'tipo' => $p->tipo,
                'estado' => $p->estado,
                'zona' => $p->zona,
                'precio' => number_format((float) $p->precio, 0, ',', '.'),
                'latitud' => (float) $p->latitud,
                'longitud' => (float) $p->longitud,
                'imagen_url' => $p->imagen_url,
                'agente' => $p->agente->nombre ?? 'Sin asignar',
            ])
            ->values();

        return view('admin.dashboard', compact(
            'totalProps','disponibles','reservadas','totalUsuarios','totalVentas','totalVisitas',
            'ultimas','visitas','actividad','notificaciones','porEstado','porTipo','propiedadesPorMes',
            'mapaPropiedades'
        ));
    }
// dashboard para el agente, muestra estadísticas de sus propiedades, visitas pendientes y las últimas visitas solicitadas por los clientes.
    public function agente()
    {
        $id          = Auth::id();
        $misProps    = Propiedad::where('agente_id',$id)->count();
        $disponibles = Propiedad::where('agente_id',$id)->where('estado','Disponible')->count();
        $vendidas    = Propiedad::where('agente_id',$id)->where('estado','Vendido')->count();
        $visitasPend = SolicitudVisita::where('estado', 'Pendiente')->whereHas('propiedad', function($q) { $q->where('agente_id', auth()->id()); })->count();
        $ultimas     = Propiedad::where('agente_id',$id)->orderBy('id','desc')->limit(5)->get();
        $visitas     = SolicitudVisita::with(['propiedad','cliente'])
                           ->where('estado', 'Pendiente')
                           ->whereHas('propiedad', function($q) { $q->where('agente_id', auth()->id()); })
                           ->orderBy('fecha_solicitada')->take(5)->get();

        return view('agente.dashboard', compact(
            'misProps','disponibles','vendidas','visitasPend','ultimas','visitas'
        ));
    }
// dashboard para el asistente, muestra estadísticas de clientes, visitas pendientes, visitas programadas para hoy y las últimas actividades realizadas en el sistema.
    public function asistente(Request $request)
    {
        // Stats
        $totalClientes = Usuario::where('rol','cliente')->count();
        $visitasPend   = SolicitudVisita::where('estado','pendiente')->count();
        $visitasHoy    = SolicitudVisita::whereDate('fecha_solicitada', today())->count();
        $totalProps    = Propiedad::where('estado','Disponible')->count();

        // Últimos clientes registrados
        $clientes = Usuario::where('rol','cliente')->orderBy('id','desc')->limit(5)->get();

        // Próximas visitas pendientes (para el dashboard)
        $visitas = SolicitudVisita::with(['propiedad','cliente'])
                        ->where('estado','pendiente')
                        ->orderBy('fecha_solicitada')
                        ->limit(5)
                        ->get();

        return view('asistente.dashboard', compact(
            'totalClientes','visitasPend','visitasHoy','totalProps',
            'clientes','visitas'
        ));
    }
// dashboard para el cliente, muestra estadísticas de propiedades disponibles, ventas, alquileres y las últimas propiedades registradas en el sistema.
    public function cliente()
    {
        $clienteId = Auth::id();
        $totalDisp     = Propiedad::where('estado','Disponible')->count();
        $totalVenta    = Propiedad::where('estado','Disponible')->where('tipo','Venta')->count();
        $totalAlquiler = Propiedad::where('estado','Disponible')->where('tipo','Alquiler')->count();
        $propiedades   = Propiedad::where('estado','Disponible')->orderBy('id','desc')->limit(6)->get();
        $visitas       = SolicitudVisita::with('propiedad')
            ->where('cliente_id', $clienteId)
            ->orderBy('fecha_solicitada')
            ->limit(5)
            ->get();
        $historial = Schema::hasTable('historial_cliente')
            ? HistorialCliente::with('propiedad')
                ->where('cliente_id', $clienteId)
                ->orderBy('fecha_accion', 'desc')
                ->limit(5)
                ->get()
            : collect();
        $recomendaciones = Schema::hasTable('recomendaciones')
            ? Recomendacion::with('propiedad')
                ->where('cliente_id', $clienteId)
                ->orderByDesc('puntuacion_recomendacion')
                ->limit(6)
                ->get()
            : collect();
        $mapaPropiedades = ($recomendaciones->isNotEmpty()
                ? $recomendaciones->pluck('propiedad')->filter()
                : Propiedad::where('estado', 'Disponible')->whereNotNull('latitud')->whereNotNull('longitud')->limit(8)->get())
            ->filter(fn ($p) => $p && $p->latitud !== null && $p->longitud !== null)
            ->map(fn ($p) => [
                'titulo' => $p->titulo,
                'tipo' => $p->tipo,
                'zona' => $p->zona,
                'precio' => number_format((float) $p->precio, 0, ',', '.'),
                'latitud' => (float) $p->latitud,
                'longitud' => (float) $p->longitud,
                'imagen_url' => $p->imagen_url,
                'detalle_url' => route('cliente.propiedades.detalle', $p),
            ])
            ->values();

        return view('cliente.dashboard', compact(
            'totalDisp','totalVenta','totalAlquiler','propiedades','visitas','historial',
            'recomendaciones','mapaPropiedades'
        ));
    }
}
