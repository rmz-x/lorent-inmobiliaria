<?php
// app/Http/Controllers/ReporteController.php
namespace App\Http\Controllers;

use App\Models\RegistroActividad;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $filtroAccion = $request->query('accion','todas');
        $filtroRol    = $request->query('rol','todos');
        $filtroFecha  = $request->query('fecha','');

        $query = RegistroActividad::query()->orderBy('id','desc');

        if ($filtroAccion !== 'todas') $query->where('accion', $filtroAccion);
        if ($filtroRol    !== 'todos') $query->where('rol', $filtroRol);
        if ($filtroFecha)              $query->whereRaw("DATE(fecha_hora) = ?", [$filtroFecha]);

        $actividad = $query->limit(200)->get();
        $acciones  = RegistroActividad::select('accion')->distinct()->pluck('accion');

        $totalLogins   = RegistroActividad::where('accion','Inicio de sesión')->count();
        $totalFallidos = RegistroActividad::where('accion','Intento de sesión fallido')->count();
        $totalProps    = RegistroActividad::where('accion','Propiedad registrada')->count();
        $totalHoy      = RegistroActividad::whereRaw("DATE(fecha_hora) = CURRENT_DATE")->count();

        return view('compartido.reportes', compact(
            'actividad','acciones',
            'totalLogins','totalFallidos','totalProps','totalHoy',
            'filtroAccion','filtroRol','filtroFecha'
        ));
    }
}