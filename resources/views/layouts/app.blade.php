<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} | Administración</title>
    <meta name="description" content="Panel administrativo de sorteos de Tienda Siete Market & Licorería.">
    <meta name="theme-color" content="#006b5e">
    <link rel="icon" type="image/png" href="{{ asset('brand/icono-tiendasiete.png') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('brand/tienda-siete.css') }}">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-md navbar-dark shadow-sm" style="background-color:#004f45">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}"><img src="{{ asset('brand/perfil-log-blanco.png') }}" alt="Tienda Siete" style="height:38px;width:auto"></a>
            @auth
                <form method="POST" action="{{ route('logout') }}" class="ms-auto">
                    @csrf
                    <button class="btn btn-outline-light btn-sm">Cerrar sesión</button>
                </form>
            @endauth
        </div>
    </nav>
    <main class="py-4">@yield('content')</main>
</body>
</html>
