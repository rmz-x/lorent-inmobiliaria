{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Lorent Inmobiliaria')</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
<link rel="stylesheet" href="{{ asset('css/topbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar h1 {
            font-size: 1.5rem;
        }

        .navbar .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .navbar .user-role {
            background: rgba(255,255,255,0.2);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .navbar a:hover {
            background: rgba(255,255,255,0.2);
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 73px;
            width: 250px;
            height: calc(100vh - 73px);
            background: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            padding: 2rem 0;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar li {
            margin-bottom: 0.5rem;
        }

        .sidebar a {
            display: block;
            padding: 1rem 2rem;
            color: #666;
            text-decoration: none;
            transition: all 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #667eea;
            color: white;
        }

        .main-content {
            margin-left: 250px;
            padding: 2rem;
            min-height: calc(100vh - 73px);
        }

        .logout-btn {
            background: #dc3545 !important;
        }

        .logout-btn:hover {
            background: #c82333 !important;
        }
    </style>
</head>
<body>
    {{-- NAVBAR --}}
    <nav class="navbar">
        <h1>🏢 Lorent Inmobiliaria</h1>
        <div class="user-info">
            <span class="user-role">{{ ucfirst(auth()->user()->rol) }}</span>
            <span>{{ auth()->user()->nombre }}</span>
            <a href="{{ route('perfil') }}">👤 Perfil</a>
            <a href="{{ route('logout') }}" class="logout-btn" onclick="return confirm('¿Estás seguro de cerrar sesión?')">🚪 Salir</a>
        </div>
    </nav>

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <ul>
            <li><a href="{{ route(auth()->user()->rol . '.dashboard') }}" class="{{ request()->routeIs(auth()->user()->rol . '.dashboard') ? 'active' : '' }}">📊 Dashboard</a></li>

            @if(auth()->user()->esAdmin())
                <li><a href="{{ route('admin.usuarios') }}" class="{{ request()->routeIs('admin.usuarios*') ? 'active' : '' }}">👥 Usuarios</a></li>
                <li><a href="{{ route('admin.propiedades') }}" class="{{ request()->routeIs('admin.propiedades*') ? 'active' : '' }}">🏠 Propiedades</a></li>
                <li><a href="{{ route('admin.reportes') }}" class="{{ request()->routeIs('admin.reportes*') ? 'active' : '' }}">📊 Reportes</a></li>
            @elseif(auth()->user()->esAgente())
                <li><a href="{{ route('agente.propiedades') }}" class="{{ request()->routeIs('agente.propiedades*') ? 'active' : '' }}">🏠 Mis Propiedades</a></li>
                <li><a href="{{ route('agente.visitas') }}" class="{{ request()->routeIs('agente.visitas*') ? 'active' : '' }}">📋 Solicitudes</a></li>
                <li><a href="{{ route('agente.clientes') }}" class="{{ request()->routeIs('agente.clientes*') ? 'active' : '' }}">👥 Clientes</a></li>
            @elseif(auth()->user()->esAsistente())
                <li><a href="{{ route('asistente.visitas') }}" class="{{ request()->routeIs('asistente.visitas*') ? 'active' : '' }}">📋 Visitas</a></li>
                <li><a href="{{ route('asistente.reportes') }}" class="{{ request()->routeIs('asistente.reportes*') ? 'active' : '' }}">📊 Reportes</a></li>
            @else
                <li><a href="{{ route('cliente.propiedades') }}" class="{{ request()->routeIs('cliente.propiedades*') ? 'active' : '' }}">🏠 Propiedades</a></li>
                <li><a href="{{ route('cliente.solicitudes') }}" class="{{ request()->routeIs('cliente.solicitudes*') ? 'active' : '' }}">📋 Mis Solicitudes</a></li>
            @endif
        </ul>
    </aside>

    {{-- CONTENIDO PRINCIPAL --}}
    <main class="main-content">
        @yield('content')
    </main>

    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>