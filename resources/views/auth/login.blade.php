<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <a href="{{ route('app.home') }}" class="flex items-center gap-2 text-sm text-indigo-400 hover:text-indigo-300 transition-colors mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Voltar ao início
    </a>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mt-4 relative">
            <x-input-label for="email" :value="__('Email')" class="text-indigo-400 font-bold uppercase text-[10px] tracking-widest ml-1" />

            <div class="relative group mt-1">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-indigo-500 transition-colors group-focus-within:text-indigo-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </span>

                <input
                    id="email"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    autocomplete="username"
                    class="block w-full pl-10 pr-4 py-3 bg-slate-900/50 border-2 border-indigo-900/50 text-white rounded-xl focus:border-indigo-500 focus:ring-0 transition-all duration-300 placeholder-indigo-900/50"
                    placeholder="seu@email.com"
                >
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4 relative">
            <x-input-label for="password" :value="__('Password')" class="text-indigo-400 font-bold uppercase text-[10px] tracking-widest ml-1" />

            <div class="relative group mt-1">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-indigo-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
        </span>

                <input id="password" type="password" name="password" required
                       class="block w-full pl-10 pr-4 py-3 bg-slate-900/50 border-2 border-indigo-900/50 text-white rounded-xl focus:border-indigo-500 focus:ring-0 transition-all duration-300 placeholder-indigo-900/50"
                       placeholder="••••••••"
                >
            </div>
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox"
                       class="rounded-none bg-black/40 border-primary/40 text-primary shadow-sm focus:ring-primary focus:ring-offset-dark w-5 h-5"
                       name="remember">
                <span class="ms-2 text-sm text-gray-400 font-pixel text-[10px] uppercase">{{ __('Lembrar de mim') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Esqueceu a senha?') }}
                </a>
            @endif

                <x-primary-button class="w-full justify-center btn-gamer py-3">
                    {{ __('Log in') }}
                </x-primary-button>
        </div>
    </form>
</x-guest-layout>
