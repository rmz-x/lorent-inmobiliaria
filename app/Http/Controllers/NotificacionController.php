<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// CU23: Gestionar Notificaciones Automaticas
class NotificacionController extends Controller
{
    public function index()
    {
        $notificaciones = Notificacion::with('propiedad')
            ->where('usuario_id', Auth::id())
            ->orderBy('fecha_envio', 'desc')
            ->limit(80)
            ->get();

        return view('notificaciones.index', compact('notificaciones'));
    }

    public function marcarLeida(Notificacion $notificacion)
    {
        abort_unless($notificacion->usuario_id === Auth::id(), 403);

        $notificacion->update(['leida' => true]);

        return back()->with('success', 'Notificación marcada como leída.');
    }

    public function marcarTodas()
    {
        Notificacion::where('usuario_id', Auth::id())
            ->where('leida', false)
            ->update(['leida' => true]);

        return back()->with('success', 'Todas las notificaciones fueron marcadas como leídas.');
    }
}
