<x-layouts.app>

    <x-partials.navbar />

    {{-- HERO SECTION - SOFT FADE TRANSITION --}}
    <section
        class="relative overflow-hidden text-white bg-gradient-to-br from-slate-700 via-violet-600 to-slate-700 dark:from-slate-900 dark:via-purple-900 dark:to-slate-900">
        <div class="absolute inset-0 opacity-15 dark:opacity-20">
            <div class="absolute top-20 left-10 w-96 h-96 bg-violet-400 dark:bg-purple-500 rounded-full blur-3xl"></div>
            <div
                class="absolute bottom-10 right-20 w-[500px] h-[500px] bg-sky-400 dark:bg-blue-500 rounded-full blur-3xl">
            </div>
        </div>
        <div class="relative max-w-screen-xl px-4 pt-32 pb-56 mx-auto lg:pt-44">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span
                        class="inline-block px-4 py-1 mb-6 text-sm font-medium rounded-full bg-white/10 text-white/90 border border-white/20">
                        Bem-vindo ao universo retrô
                    </span>
                    <h1
                        class="text-5xl lg:text-6xl font-extrabold leading-tight mb-6 bg-gradient-to-r from-white via-violet-200 to-violet-300 dark:from-white dark:via-purple-200 dark:to-purple-400 bg-clip-text text-transparent">
                        {{ config('app.name') }}
                    </h1>
                    <p class="text-lg text-white/80 dark:text-gray-300 mb-8 leading-relaxed max-w-lg">
                        Bem-vindo à sua comunidade retrô! Descubra canais incríveis, conteúdos exclusivos e fortaleça a
                        visibilidade da comunidade retrô.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('app.channels') }}"
                            class="px-8 py-3 bg-white/15 hover:bg-white/25 backdrop-blur rounded-lg font-semibold transition-all border border-white/20">
                            Explorar canais
                        </a>
                    </div>
                </div>
                <div class="relative flex justify-center">
                    <img src="{{ asset('images/brandname/Hall-dos-conquistadores.png') }}" alt="hero"
                        class="vertical-loop-animation relative w-[300px] h-auto mx-auto z-20">
                </div>
            </div>
        </div>
        {{-- SOFT FADE TRANSITION --}}
        <div
            class="absolute bottom-0 left-0 right-0 h-56 bg-gradient-to-b from-transparent via-slate-700/40 to-white dark:via-slate-900/40 dark:to-slate-900">
        </div>
    </section>

    @auth
        @if($followedChannels->isNotEmpty())
            <section class="bg-white dark:bg-slate-900 py-16">
                <div class="max-w-screen-xl px-4 mx-auto">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-8">Canais que você segue</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
                        @foreach($followedChannels as $channel)
                            <a href="{{ route('my.channel', $channel->slug) }}" class="group text-center">
                                <div
                                    class="w-24 h-24 mx-auto rounded-2xl overflow-hidden ring-2 ring-transparent group-hover:ring-purple-500 transition-all group-hover:scale-105 shadow-lg">
                                    <img src="{{ Storage::url($channel->brand) }}" class="w-full h-full object-cover">
                                </div>
                                <p class="mt-3 text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ $channel->name }}
                                </p>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    @endauth

    {{-- CHANNELS SECTION --}}
    <section class="bg-white dark:bg-slate-900 py-20">
        <div class="max-w-screen-xl px-4 mx-auto text-center">
            <span class="inline-block px-4 py-1 mb-4 text-sm font-medium rounded-full bg-blue-500/10 text-blue-600">
                Parceiros
            </span>
            <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-4">Canais apoiadores</h2>
            <p class="text-gray-500 mb-12 max-w-lg mx-auto">Canais parceiros que apoiam o projeto Hall dos
                Conquistadores.</p>

            @if($channels->isNotEmpty())
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($channels as $channel)
                        <div
                            class="group p-6 rounded-2xl bg-white dark:bg-slate-800 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                            <div
                                class="w-28 h-28 mx-auto mb-4 rounded-full overflow-hidden ring-4 ring-gray-200 dark:ring-slate-700 group-hover:ring-purple-500 transition-all">
                                <img src="{{ Storage::url($channel->brand) }}" class="w-full h-full object-cover"
                                    alt="{{ $channel->name }}">
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                <a href="{{ route('my.channel', $channel->slug) }}"
                                    class="hover:text-purple-600 transition-colors">
                                    {{ $channel->name }}
                                </a>
                            </h3>
                            <p class="text-xs text-purple-600 font-bold mt-1 uppercase tracking-wider">
                                {{ $channel->followers_count }} {{ $channel->followers_count === 1 ? 'inscrito' : 'inscritos' }}
                            </p>
                            <div class="mt-4">
                                <a href="{{ url('https://www.youtube.com/@' . $channel->link) }}" target="_blank"
                                    class="inline-flex items-center gap-2 text-sm text-red-500 hover:text-red-600 transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M23.5 6.2c-.3-1-1-1.8-2-2.1C19.6 3.5 12 3.5 12 3.5s-7.6 0-9.5.6c-1 .3-1.7 1.1-2 2.1C0 8.1 0 12 0 12s0 3.9.5 5.8c.3 1 1 1.8 2 2.1 1.9.6 9.5.6 9.5.6s7.6 0 9.5-.6c1-.3 1.7-1.1 2-2.1.5-1.9.5-5.8.5-5.8s0-3.9-.5-5.8zM9.5 15.6V8.4l6.3 3.6-6.3 3.6z" />
                                    </svg>
                                    YouTube
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-12">
                    <a href="{{ route('app.channels') }}"
                        class="inline-flex items-center gap-2 text-sm font-medium text-purple-600 hover:text-purple-700 transition-colors group">
                        Ver todos os canais
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- POSTS SECTION --}}
    <section class="bg-gray-100 dark:bg-slate-950 py-20">
        <div class="max-w-screen-xl px-4 mx-auto">
            <div class="text-center mb-12">
                <span
                    class="inline-block px-4 py-1 mb-4 text-sm font-medium rounded-full bg-green-500/10 text-green-600">
                    Novidades
                </span>
                <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-4">
                    {{ $posts->count() > 0 ? 'Postagens recentes' : 'Em breve novos conteúdos' }}
                </h2>
                @if($posts->count() > 0)
                    <p class="text-gray-500 max-w-lg mx-auto">Veja as últimas novidades criadas pela comunidade.</p>
                @endif
            </div>

            @if($posts->count() > 0)
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($posts as $post)
                        <x-post-card :post="$post" />
                    @endforeach
                </div>
                <div class="mt-12 text-center">
                    <a href="{{ route('posts.index') }}"
                        class="inline-flex items-center gap-2 text-sm font-medium text-purple-600 hover:text-purple-700 transition-colors group">
                        Ver todas as postagens
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            @else
                <div class="text-center py-16">
                    <div
                        class="w-20 h-20 mx-auto mb-6 rounded-full bg-gray-200 dark:bg-slate-700 flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                    </div>
                    <p class="text-lg text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                        Estamos trabalhando em novos conteúdos e notícias para informar a todos.
                    </p>
                </div>
            @endif
        </div>
    </section>

    {{-- FOOTER --}}
    <x-partials.footer />

</x-layouts.app>