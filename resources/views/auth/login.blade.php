<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-sm font-semibold text-slate-700" />
            <div class="mt-1.5 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                </span>
                <x-text-input id="email" class="block w-full pl-10 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" type="email" name="email" :value="old('email')" placeholder="admin@toko.id" required autofocus autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-600 text-sm" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-slate-700" />
            <div class="mt-1.5 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25z"/></svg>
                </span>
                <x-text-input id="password" class="block w-full pl-10 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" type="password" name="password" placeholder="••••••••" required autocomplete="current-password" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-600 text-sm" />
        </div>

        <!-- Remember -->
        <div class="block mt-5">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-300 text-emerald-600 shadow-sm focus:ring-emerald-500">
                <span class="ms-2 text-sm text-slate-600">{{ __('Ingat saya') }}</span>
            </label>
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 rounded-lg transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/></svg>
                Masuk
            </button>
        </div>

        @if (Route::has('password.request'))
        <div class="flex items-center justify-center mt-5">
            <a class="text-sm text-slate-500 hover:text-emerald-600 hover:underline" href="{{ route('password.request') }}">
                Lupa password?
            </a>
        </div>
        @endif
    </form>

    {{-- Info akun demo (hanya saat APP_ENV=local) --}}
    @if(app()->environment('local'))
    <div class="mt-6 pt-5 border-t border-slate-100">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2">Akun demo</p>
        <div class="text-xs text-slate-500 space-y-1">
            <div class="flex justify-between"><span>Owner</span><code class="bg-slate-100 px-2 py-0.5 rounded">admin@toko.id / admin123</code></div>
            <div class="flex justify-between"><span>Kasir</span><code class="bg-slate-100 px-2 py-0.5 rounded">kasir@toko.id / kasir123</code></div>
        </div>
    </div>
    @endif
</x-guest-layout>
