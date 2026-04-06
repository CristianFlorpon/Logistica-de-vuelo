<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Aerobooking — {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

        /* ── Fondo ── */
        .bg-auth {
            min-height: 100vh;
            background-image: url('https://img.freepik.com/vector-gratis/avion-cielo_1308-31418.jpg?semt=ais_incoming&w=740&q=80');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            display: flex;
            flex-direction: column;
        }

        /* ── Navbar superior ── */
        .auth-navbar {
            width: 100%;
            padding: 1.2rem 2.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(255,255,255,0.25);
        }

        .auth-navbar .brand {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 1.4rem;
            color: #0a2540;
            letter-spacing: -0.5px;
            text-decoration: none;
        }

        .auth-navbar .brand span {
            color: #1a6fbf;
        }

        .auth-navbar .plane-icon {
            font-size: 1.5rem;
        }

        /* ── Contenedor centrado ── */
        .auth-center {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        /* ── Tarjeta del formulario ── */
        .auth-card {
            width: 100%;
            max-width: 440px;
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid rgba(255,255,255,0.6);
            box-shadow:
                0 32px 80px rgba(10, 37, 64, 0.18),
                0 8px 24px rgba(10, 37, 64, 0.10);
            padding: 2.5rem 2.2rem 2rem;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(28px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Título del formulario ── */
        .auth-card-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 1.6rem;
            color: #0a2540;
            margin-bottom: 0.3rem;
        }

        .auth-card-subtitle {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 1.8rem;
        }

        /* ── Inputs ── */
        .auth-card label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.35rem;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .auth-card input[type="text"],
        .auth-card input[type="email"],
        .auth-card input[type="password"] {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(255,255,255,0.9);
            border: 1.5px solid #d1d5db;
            border-radius: 10px;
            font-size: 0.95rem;
            color: #0a2540;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            font-family: 'Inter', sans-serif;
        }

        .auth-card input:focus {
            border-color: #1a6fbf;
            box-shadow: 0 0 0 3px rgba(26,111,191,0.15);
            background: #fff;
        }

        /* ── Grupos de campo ── */
        .field-group {
            margin-bottom: 1.1rem;
        }

        /* ── Botón principal ── */
        .btn-primary-auth {
            width: 100%;
            padding: 0.85rem;
            background: linear-gradient(135deg, #1a6fbf 0%, #0a4a8a 100%);
            color: #fff;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 4px 16px rgba(26,111,191,0.35);
            margin-top: 0.5rem;
        }

        .btn-primary-auth:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(26,111,191,0.45);
        }

        .btn-primary-auth:active {
            transform: translateY(0);
        }

        /* ── Links y extras ── */
        .auth-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1.2rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .auth-footer a,
        .auth-card a {
            font-size: 0.85rem;
            color: #1a6fbf;
            text-decoration: none;
            font-weight: 500;
        }

        .auth-footer a:hover,
        .auth-card a:hover {
            text-decoration: underline;
        }

        /* ── Checkbox Remember me ── */
        .auth-card .remember-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #4b5563;
            text-transform: none;
            font-weight: 400;
            letter-spacing: 0;
            cursor: pointer;
        }

        .auth-card input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #1a6fbf;
            cursor: pointer;
        }

        /* ── Divisor ── */
        .auth-divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #d1d5db, transparent);
            margin: 1.2rem 0;
        }

        /* Errores de validación */
        .auth-card .text-sm.text-red-600 {
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }
    </style>
</head>

<body>
<div class="bg-auth">

    <!-- ── Navbar con marca ── -->
    <nav class="auth-navbar">
        <span class="plane-icon">✈️</span>
        <a href="/" class="brand">Aero<span>booking</span></a>
    </nav>

    <!-- ── Contenido centrado ── -->
    <div class="auth-center">
        <div class="auth-card">

            {{-- Título dinámico según la ruta --}}
            @if(request()->routeIs('login'))
                <div class="auth-card-title">Bienvenido de nuevo</div>
                <div class="auth-card-subtitle">Ingresa tus credenciales para continuar</div>
            @elseif(request()->routeIs('register'))
                <div class="auth-card-title">Crear cuenta</div>
                <div class="auth-card-subtitle">Únete a Aerobooking hoy mismo</div>
            @else
                <div class="auth-card-title">Aerobooking</div>
                <div class="auth-card-subtitle">Tu plataforma de vuelos</div>
            @endif

            <div class="auth-divider"></div>

            {{-- Aquí va el contenido del formulario (login o register) --}}
            {{ $slot }}

        </div>
    </div>

</div>

<style>
    {{-- Override estilos de Breeze para que hereden el diseño --}}
    /* Forzar que los inputs de Breeze usen nuestros estilos */
    .auth-card .block { display: block; }
    .auth-card .mt-1 { margin-top: 0.35rem; }
    .auth-card .mt-4 { margin-top: 1.1rem; }
    .auth-card .w-full { width: 100%; }

    /* Botón de Breeze (x-primary-button) */
    .auth-card button[type="submit"] {
        padding: 0.85rem 1.5rem;
        background: linear-gradient(135deg, #1a6fbf 0%, #0a4a8a 100%) !important;
        color: #fff !important;
        font-family: 'Montserrat', sans-serif !important;
        font-weight: 700 !important;
        font-size: 0.9rem !important;
        letter-spacing: 0.5px;
        border: none !important;
        border-radius: 10px !important;
        cursor: pointer;
        transition: transform 0.15s, box-shadow 0.15s;
        box-shadow: 0 4px 16px rgba(26,111,191,0.35);
    }

    .auth-card button[type="submit"]:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 24px rgba(26,111,191,0.45) !important;
    }

    /* Flex final de los formularios */
    .auth-card .flex.items-center.justify-end {
        margin-top: 1.4rem;
    }
</style>
</body>
</html>