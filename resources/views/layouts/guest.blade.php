<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Toko Sembako') }} — Masuk</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center px-4 py-10 bg-gradient-to-br from-emerald-700 via-emerald-600 to-teal-500 relative overflow-hidden">

            {{-- Dekorasi background lembut --}}
            <div class="absolute -top-24 -left-24 w-80 h-80 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-32 -right-20 w-96 h-96 bg-teal-300/20 rounded-full blur-3xl"></div>

            {{-- Branding --}}
            <div class="flex flex-col items-center mb-6 relative">
                <div class="relative mb-3">
                    <img src="{{ asset('img/logo-toko.png') }}" alt="Logo Toko Madura"
                        class="w-28 h-28 lg:w-32 lg:h-32 object-cover rounded-full ring-4 ring-white/95 shadow-2xl">
                    {{-- Lingkaran aksen di belakang --}}
                    <div class="absolute -inset-2 rounded-full bg-white/20 blur-sm -z-10"></div>
                </div>
                <h1 class="text-white text-3xl font-extrabold tracking-tight drop-shadow">Toko Madura</h1>
                <p class="text-emerald-100/90 text-sm mt-1 drop-shadow">Sumber Rejeki · Sistem kasir & manajemen toko</p>
            </div>

            {{-- Kartu login --}}
            <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8 relative">
                <h2 class="text-xl font-bold text-slate-800 mb-1">Selamat Datang 👋</h2>
                <p class="text-sm text-slate-500 mb-6">Masuk untuk melanjutkan ke sistem</p>

                {{ $slot }}
            </div>

            <p class="text-emerald-100/70 text-xs mt-8 relative">© {{ date('Y') }} Toko Sembako</p>
        </div>
    </body>
</html>
