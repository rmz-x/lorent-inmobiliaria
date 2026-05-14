<?php
// app/Http/Controllers/UsuarioController.php
namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\RegistroActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::orderBy('nombre')->get();
        return view('admin.usuarios', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'    => 'required|min:3',
            'correo'    => 'required|email|unique:usuarios,correo',
            'usuario'   => 'required|min:3|unique:usuarios,usuario',
            'contrasena'=> 'required|min:3',
            'rol'       => 'required|in:administrador,agente,asistente,cliente',
        ]);

        $user = Usuario::create($request->only(['nombre','correo','usuario','contrasena','rol']));
        RegistroActividad::log('Usuario creado',
            "Se creó el usuario {$user->nombre} con rol {$user->rol}.");

        return back()->with('success','Usuario agregado correctamente.');
    }

    public function update(Request $request, Usuario $usuario)
    {
        $request->validate([
            'nombre'  => 'required|min:3',
            'correo'  => 'required|email|unique:usuarios,correo,'.$usuario->id,
            'usuario' => 'required|min:3|unique:usuarios,usuario,'.$usuario->id,
            'rol'     => 'required|in:administrador,agente,asistente,cliente',
        ]);

        $data = $request->only(['nombre','correo','usuario','rol']);
        if ($request->filled('contrasena')) {
            $data['contrasena'] = $request->contrasena;
        }

        $usuario->update($data);
        RegistroActividad::log('Usuario editado', "Se editó el usuario {$usuario->nombre}.");
        return back()->with('success','Usuario actualizado correctamente.');
    }

    public function destroy(Usuario $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return back()->with('error','No puedes eliminar tu propia cuenta.');
        }
        $nombre = $usuario->nombre;
        $usuario->delete();
        RegistroActividad::log('Usuario eliminado', "Se eliminó el usuario $nombre.");
        return back()->with('success','Usuario eliminado correctamente.');
    }

    public function perfil()
    {
        $usuario = Auth::user();
        return view('compartido.perfil', compact('usuario'));
    }

    public function actualizarPerfil(Request $request)
    {
        $usuario = Auth::user();
        $request->validate([
            'nombre'  => 'required|min:3',
            'usuario' => 'required|min:3|unique:usuarios,usuario,'.$usuario->id,
        ]);

        $data = ['nombre' => $request->nombre, 'usuario' => $request->usuario];
        if ($request->filled('contrasena_nueva')) {
            $data['contrasena'] = $request->contrasena_nueva;
        }
        $usuario->update($data);
        return back()->with('success','Perfil actualizado correctamente.');
    }
}