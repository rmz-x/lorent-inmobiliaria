<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña | Lorent Inmobiliaria</title>
    <!-- Favicons -->
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/icons2-180.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icons/icons2-32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('icons/icons2-16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<div class="auth-container">
    <div class="auth-left hidden md:flex">
        <div class="overlay"></div>
        <div class="left-content">
            <div class="badge-status">
                <span class="dot"></span>
                Plataforma activa
            </div>
            <h1>
                Cambia tu contraseña
                con seguridad
            </h1>
            <p>
                Ingresa una nueva contraseña para tu cuenta.
            </p>
        </div>
    </div>
    <div class="auth-right">
        <div class="auth-card w-full max-w-md mx-auto p-4">
            <div class="logo-area">
                <div class="logo-icon">🏠</div>
                <div class="logo-text">Lorent<span>Inmobiliaria</span></div>
            </div>
            <div class="panel active">
                <h2>Restablecer contraseña</h2>
                <p class="subtitle">
                    Define una nueva contraseña para tu cuenta.
                </p>
                @if($errors->any())
                    <div class="alert error">
                        {{ $errors->first() }}
                    </div>
                @endif
                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    {{-- El token y el correo se envían ocultos para validar el restablecimiento. --}}
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">
                    <div class="input-group">
                        <label>Nueva contraseña</label>
                        <div class="password-wrapper">
                            <input
                                type="password"
                                name="contrasena"
                                id="resetPassword"
                                class="pw-input"
                                data-pw-enable="1"
                                placeholder="********"
                                required
                            >
                            <button
                                type="button"
                                class="toggle-password"
                                aria-label="Mostrar contraseña"
                            >
                                <span class="eye-icon"></span>
                            </button>
                        </div>
                    </div>
                    <div class="input-group">
                        <label>Confirmar contraseña</label>
                        <div class="password-wrapper">
                            <input
                                type="password"
                                name="contrasena_confirmation"
                                id="resetPasswordConfirmation"
                                class="pw-input"
                                data-pw-enable="1"
                                placeholder="********"
                                required
                            >
                            <button
                                type="button"
                                class="toggle-password"
                                aria-label="Mostrar contraseña"
                            >
                                <span class="eye-icon"></span>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary">
                        Guardar contraseña
                    </button>
                </form>
                <div class="forgot-password">
                    <a href="{{ route('login') }}">Volver al inicio de sesión</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/compartido/password-toggle.js') }}"></script>
<script src="{{ asset('js/auth/login.js') }}"></script>
<script src="{{ asset('js/compartido/pw-helper.js') }}"></script>
<style>
/* pw-helper minimal styles (copiado de registro) */
.pw-helper {
    background: #f8fafc;
    border: 1px solid #e6e9ee;
    border-radius: 12px;
    padding: 6px 8px;
    margin-top: 4px;
    font-size: 13px;
    color: #374151;
    max-width:420px;
    box-shadow: 0 1px 2px rgba(16,24,40,0.03);
}
.pw-helper.pw-hidden{ opacity:0; max-height:0; overflow:hidden; transform:translateY(-6px); transition:opacity .16s ease, transform .16s ease, max-height .16s ease }
.pw-helper.pw-visible{ opacity:1; max-height:400px; transform:translateY(0); transition:opacity .16s ease, transform .16s ease, max-height .2s ease }
.pw-helper .pw-list { margin:0; padding:0; list-style:none; font-size:13px }
.pw-helper .pw-item { display:flex; gap:8px; align-items:center; color:#6b7280; margin:3px 0; font-size:13px; padding:4px 6px; border-radius:8px }
.pw-helper .pw-icon { width:18px; display:inline-flex; justify-content:center; align-items:center; color:#ef4444; font-weight:700 }
.pw-helper .pw-item.satisfied { color:#16A34A; }
.pw-helper .pw-item.satisfied .pw-icon { color:#16A34A }
.pw-helper .pw-item, .pw-helper .pw-icon { transition: color .12s ease, background .12s ease }
.pw-icon svg { width:18px; height:18px; display:block }
.pw-item.satisfied .pw-text { color:#16A34A; font-weight:600 }
</style>
</body>
</html>
