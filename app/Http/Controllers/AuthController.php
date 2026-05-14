<?php
// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\RegistroActividad;
use App\Mail\PasswordResetMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http; //add para API

/**
 * Controlador de autenticación.
 *
 * Este controlador maneja el inicio de sesión, registro, recuperación de contraseña
 * y cierre de sesión de los usuarios del sistema.
 */
class AuthController extends Controller
{
    // Mostrar login
    public function showLogin()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return view('auth.login');
    }

    // Procesar login
    public function login(Request $request)
    {
        // Validar que el correo y la contraseña se envíen al servidor.
        $request->validate([
            'correo'    => 'required|email',
            'contrasena'=> 'required',
        ], [
            'correo.required'     => 'El correo es obligatorio.',
            'correo.email'        => 'Ingresa un correo válido.',
            'contrasena.required' => 'La contraseña es obligatoria.',
        ]);

        // Buscar el usuario registrado por correo y contraseña.
        // En esta implementación, la contraseña se compara directamente en texto plano.
        $usuario = Usuario::where('correo', $request->correo)
            ->where('contrasena', $request->contrasena)
            ->first();

        if ($usuario) {
            Auth::loginUsingId($usuario->id);
            $request->session()->regenerate();

            try {
                RegistroActividad::log(
                    'Inicio de sesión',
                    "El usuario {$usuario->nombre} ({$usuario->rol}) inició sesión."
                );
            } catch (\Exception $e) {
                // Ignorar errores en el registro de actividad para no interrumpir el inicio de sesión.
            }

            return $this->redirigirPorRol();
        }

        try {
            RegistroActividad::log(
                'Intento de sesión fallido',
                "Intento fallido con correo: {$request->correo}"
            );
        } catch (\Exception $e) {
            // Ignorar errores en el registro de actividad.
        }

        return back()->withErrors(['correo' => 'Correo o contraseña incorrectos.']);
    }
    // Mostrar registro
    public function showRegistro()
    {
        return view('auth.registro');
    }

    // Mostrar formulario de recuperación de contraseña
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    // Enviar enlace de recuperación
    public function sendForgotPassword(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
        ], [
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'Ingresa un correo válido.',
        ]);

        $usuario = Usuario::where('correo', $request->correo)->first();

        if ($usuario) {
            $token = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $usuario->correo],
                [
                    'token' => Hash::make($token),
                    'created_at' => now(),
                ]
            );

            $resetUrl = route('password.reset', [
                'token' => $token,
                'email' => $usuario->correo,
            ]);

            // Mail::to($usuario->correo)->send(new PasswordResetMail($resetUrl, $usuario->nombre));
            //NEW API BREVO
            Http::withHeaders([
                'api-key' => env('BREVO_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', [
                'sender' => [
                    'name' => 'Lorent Inmobiliaria',
                    'email' => 'lorent.proyecto@gmail.com',
                ],
                'to' => [
                    [
                        'email' => $usuario->correo,
                        'name' => $usuario->nombre,
                    ]
                ],
                'subject' => 'Recuperación de contraseña',
                'htmlContent' => "
                    <h3>Recuperación de contraseña</h3>
                    <p>Hola {$usuario->nombre},</p>
                    <p>Haz clic aquí para recuperar tu contraseña:</p>
                    <a href='{$resetUrl}'>Restablecer contraseña</a>
                ",
            ]);
        }

        return back()->with('success', 'Si ese correo existe, te enviamos un enlace para recuperar la contraseña.');
    }

    // Mostrar formulario para crear nueva contraseña
    public function showResetPassword(Request $request, string $token)
    {
        $email = $request->query('email');

        if (!$email) {
            return redirect()->route('password.request')->withErrors(['correo' => 'Correo inválido.']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    // Guardar nueva contraseña
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'contrasena' => 'required|min:6|confirmed',
        ], [
            'contrasena.required' => 'La contraseña es obligatoria.',
            'contrasena.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'contrasena.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'El enlace de recuperación no es válido o ya expiró.']);
        }

        $expires = now()->subMinutes(config('auth.passwords.usuarios.expire'));
        $createdAt = Carbon::parse($record->created_at);

        if ($createdAt->lt($expires)) {
            return back()->withErrors(['email' => 'El enlace de recuperación no es válido o ya expiró.']);
        }

        $usuario = Usuario::where('correo', $request->email)->first();

        if (!$usuario) {
            return back()->withErrors(['email' => 'No encontramos un usuario con ese correo.']);
        }

        $usuario->contrasena = $request->contrasena;
        $usuario->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Contraseña actualizada. Ya puedes iniciar sesión.');
    }

    public function registro(Request $request)
    {
        $validated = $request->validate(
            [
                'nombre'    => 'required|min:3',
                'correo'    => 'required|email|unique:usuarios,correo',
                'usuario'   => 'required|min:3|unique:usuarios,usuario|regex:/^\S+$/',
                'contrasena'=> [
                    'required',
                    'string',
                    'min:6',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                ],
            ],
            [
                'nombre.required'      => 'El nombre es obligatorio.',
                'nombre.min'           => 'El nombre debe tener al menos 3 caracteres.',
                'correo.required'      => 'El correo es obligatorio.',
                'correo.email'         => 'Ingresa un correo válido.',
                'correo.unique'        => 'Este correo ya está registrado.',
                'usuario.required'     => 'El usuario es obligatorio.',
                'usuario.min'          => 'El usuario debe tener al menos 3 caracteres.',
                'usuario.unique'       => 'Este nombre de usuario ya existe.',
                'usuario.regex'        => 'El usuario no puede contener espacios.',
                'contrasena.required'  => 'La contraseña es obligatoria.',
                'contrasena.min'       => 'La contraseña debe tener al menos 6 caracteres.',
                'contrasena.regex'     => 'La contraseña debe contener mayúscula, minúscula y número.',
                'contrasena.string'    => 'La contraseña debe ser un texto válido.',
            ]
        );

        Usuario::create([
            'nombre'     => $request->nombre,
            'correo'     => $request->correo,
            'usuario'    => $request->usuario,
            'contrasena' => $request->contrasena, // Se hashea automáticamente por el cast
            'rol'        => 'cliente',
        ]);

        RegistroActividad::log(
            'Nuevo registro',
            "Se registró el usuario: {$request->usuario} ({$request->correo})"
        );

        return redirect()->route('login')->with('success', 'Usuario registrado. Ya puedes iniciar sesión.');
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        $user = Auth::user();
        RegistroActividad::log(
            'Cierre de sesión',
            "El usuario {$user->nombre} ({$user->rol}) cerró sesión."
        );

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // Redirigir según rol del usuario
    private function redirigirPorRol()
    {
        $user = Auth::user();

        switch ($user->rol) {
            case 'administrador':
                return redirect()->route('admin.dashboard');
            case 'agente':
                return redirect()->route('agente.dashboard');
            case 'asistente':
                return redirect()->route('asistente.dashboard');
            case 'cliente':
                return redirect()->route('cliente.dashboard');
            default:
                return redirect()->route('login');
        }
    }
}
