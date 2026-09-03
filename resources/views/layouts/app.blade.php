<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Toko Madura') · Toko Madura</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-100 text-slate-800">
    <div class="min-h-screen">
        <!-- ===== TOPBAR ===== -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
            <div class="flex items-center justify-between px-4 h-14">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('img/logo-toko.png') }}" alt="Logo" class="w-8 h-8 rounded-lg object-cover ring-1 ring-slate-200">
                    <span class="font-bold text-slate-800">Toko Madura</span>
                </div>
                <div class="flex items-center gap-3">
                    @auth
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                        {{ auth()->user()->isOwner() ? 'bg-emerald-100 text-emerald-700' : 'bg-sky-100 text-sky-700' }}">
                        {{ auth()->user()->isOwner() ? 'Owner' : 'Kasir' }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-slate-600" title="Keluar">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15"/><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 9.75 4.5 4.5-4.5 4.5m5.25-4.5H3"/></svg></button>
                    </form>
                    <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    @endauth
                </div>
            </div>
        </header>

        <!-- ===== BODY: sidebar (lg) + konten ===== -->
        <div class="flex">
            <!-- Sidebar desktop -->
            <aside class="hidden lg:flex flex-col w-56 border-r border-slate-200 bg-white min-h-[calc(100vh-3.5rem)] sticky top-14">
                <nav class="p-3 space-y-1 text-sm">
                    @include('layouts._menu')
                </nav>
            </aside>

            <!-- Konten utama -->
            <main class="flex-1 p-4 lg:p-6">
                @yield('content')
            </main>
        </div>

        <!-- ===== BOTTOM NAV (mobile) ===== -->
        <nav class="lg:hidden fixed bottom-0 inset-x-0 bg-white border-t border-slate-200 z-20">
            <div class="grid grid-cols-4 text-xs text-center">
                @include('layouts._bottommenu')
            </div>
        </nav>
    </div>
</body>
</html>
