<header class="fixed w-full z-50" x-data="{ mobileOpen: false }">
    <nav
        class="backdrop-blur-xl bg-white/80 dark:bg-slate-900/80 border-b border-gray-200/50 dark:border-slate-700/50 py-3 text-gray-900 dark:text-white transition-all">
        <div class="flex flex-wrap items-center justify-between max-w-screen-xl px-4 mx-auto">
            <a href="{{ route('app.landing') }}" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg overflow-hidden">
                    <img src="/images/brandname/favicon-hall-dos-conquistadores.png" alt="" class="w-full h-full object-cover">
                </div>
                <span class="self-center text-lg font-bold whitespace-nowrap">{{ config('app.name') }}</span>
            </a>

            <div class="flex items-center gap-3 lg:order-2">
                @guest
                    <a href="{{ route('filament.admin.pages.dashboard') }}"
                        class="text-sm font-medium px-5 py-2 rounded-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 hover:bg-gray-700 dark:hover:bg-gray-200 transition-colors">
                        Login
                    </a>
                @endguest
                @auth
                    @if(auth()->user()->hasVerifiedEmail())
                        {{-- User dropdown --}}
                        <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" class="relative">
                            <button
                                class="flex items-center gap-2 text-sm font-medium hover:text-violet-600 transition-colors focus:outline-none">
                                @if(auth()->user()->channel)
                                    <img src="{{ Storage::url(auth()->user()->channel->brand) }}"
                                        class="w-7 h-7 rounded-full ring-2 ring-gray-200 dark:ring-slate-600">
                                    <span class="hidden sm:inline">{{ auth()->user()->channel->name }}</span>
                                @else
                                    <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                                @endif
                                <svg class="w-4 h-4 transition-transform" :class="open && 'rotate-180'" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            {{-- Dropdown --}}
                            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-2"
                                class="absolute right-0 mt-1 w-48 bg-white/95 dark:bg-slate-800/95 backdrop-blur-sm rounded-2xl shadow-xl shadow-gray-200/50 dark:shadow-black/20 border border-gray-100/80 dark:border-slate-700/60 py-2 overflow-hidden"
                                style="display: none;">
                                @if(auth()->user()->channel)
                                    <a href="{{ route('my.channel', ['slug' => auth()->user()->channel->slug]) }}"
                                        class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Meu canal
                                    </a>
                                    <a href="{{ route('my.channel.dashboard', ['slug' => auth()->user()->channel->slug]) }}"
                                        class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        Dashboard
                                    </a>
                                @endif
                                <a href="{{ route('filament.admin.pages.dashboard') }}"
                                    class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Admin
                                </a>
                                <div class="h-px bg-gray-100 dark:bg-slate-700 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="flex items-center gap-3 w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Sair
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('verification.notice') }}" class="text-sm font-medium px-4 py-2 rounded-full bg-amber-500 text-white hover:bg-amber-600 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4 animate-shake" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="hidden sm:inline">Verifique seu e-mail</span>
                        </a>
                    @endif
                @endauth

                <x-partials.btn-darkmode />

                {{-- Mobile hamburger --}}
                <div @mouseenter="mobileOpen = true" @mouseleave="mobileOpen = false" class="lg:hidden">
                    <button @click="mobileOpen = !mobileOpen" type="button"
                        class="inline-flex items-center p-2 ml-1 text-sm text-gray-500 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 focus:outline-none">
                        <span class="sr-only">Open main menu</span>
                        <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Desktop menu --}}
            <div class="items-center justify-between hidden w-full lg:flex lg:w-auto lg:order-1">
                <ul class="flex flex-col font-medium lg:flex-row lg:items-center lg:gap-1 lg:mt-0">
                    <li>
                        <a href="{{ route('app.home') }}"
                            class="block py-2 px-3 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 lg:hover:bg-transparent lg:hover:text-violet-600 transition-colors {{ request()->routeIs('app.home') ? 'text-violet-600 font-semibold' : '' }}">Home</a>
                    </li>
                    <li>
                        <a href="{{ route('posts.index') }}"
                            class="block py-2 px-3 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 lg:hover:bg-transparent lg:hover:text-violet-600 transition-colors {{ request()->routeIs('posts.*') ? 'text-violet-600 font-semibold' : '' }}">Blog</a>
                    </li>
                    <li>
                        <a href="{{ route('app.channels') }}"
                            class="block py-2 px-3 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 lg:hover:bg-transparent lg:hover:text-violet-600 transition-colors {{ request()->routeIs('app.channels') ? 'text-violet-600 font-semibold' : '' }}">Canais</a>
                    </li>
                    <li>
                        <a href="{{ route('app.campaings') }}"
                            class="block py-2 px-3 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 lg:hover:bg-transparent lg:hover:text-violet-600 transition-colors {{ request()->routeIs('app.campaings') ? 'text-violet-600 font-semibold' : '' }}">Campanhas</a>
                    </li>
                    <li>
                        <a href="{{ route('app.about') }}"
                            class="block py-2 px-3 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 lg:hover:bg-transparent lg:hover:text-violet-600 transition-colors {{ request()->routeIs('app.about') ? 'text-violet-600 font-semibold' : '' }}">Sobre</a>
                    </li>
                </ul>
            </div>

            {{-- Mobile menu --}}
            <div @mouseenter="mobileOpen = true" @mouseleave="mobileOpen = false" x-show="mobileOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                class="w-full lg:hidden order-3 mt-3" style="display: none;">
                <ul
                    class="flex flex-col font-medium bg-white/95 dark:bg-slate-800/95 backdrop-blur-sm rounded-2xl border border-gray-100/80 dark:border-slate-700/60 p-2 shadow-lg">
                    <li>
                        <a href="{{ route('app.home') }}"
                            class="block py-2.5 px-4 text-sm rounded-xl hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors {{ request()->routeIs('app.home') ? 'text-violet-600 font-semibold' : '' }}">Home</a>
                    </li>
                    <li>
                        <a href="{{ route('posts.index') }}"
                            class="block py-2.5 px-4 text-sm rounded-xl hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors {{ request()->routeIs('posts.*') ? 'text-violet-600 font-semibold' : '' }}">Blog</a>
                    </li>
                    <li>
                        <a href="{{ route('app.channels') }}"
                            class="block py-2.5 px-4 text-sm rounded-xl hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors {{ request()->routeIs('app.channels') ? 'text-violet-600 font-semibold' : '' }}">Canais</a>
                    </li>
                    <li>
                        <a href="{{ route('app.campaings') }}"
                            class="block py-2.5 px-4 text-sm rounded-xl hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors {{ request()->routeIs('app.campaings') ? 'text-violet-600 font-semibold' : '' }}">Campanhas</a>
                    </li>
                    <li>
                        <a href="{{ route('app.about') }}"
                            class="block py-2.5 px-4 text-sm rounded-xl hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors {{ request()->routeIs('app.about') ? 'text-violet-600 font-semibold' : '' }}">Sobre</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>