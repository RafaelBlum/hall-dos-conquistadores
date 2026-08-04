<x-layouts.app>
    <x-partials.navbar />

    {{-- HERO SECTION - CHANNEL PROFILE --}}
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
                        Perfil do Canal
                    </span>
                    <h1
                        class="text-5xl lg:text-6xl font-extrabold leading-tight mb-6 bg-gradient-to-r from-white via-violet-200 to-violet-300 dark:from-white dark:via-purple-200 dark:to-purple-400 bg-clip-text text-transparent">
                        {{ $channel->name }}
                    </h1>
                    <p class="text-lg text-white/80 dark:text-gray-300 mb-8 leading-relaxed max-w-lg">
                        {{ $channel->description ?? 'Bem-vindo ao meu canal na ' . config('app.name') . '!' }}
                    </p>
                    <div class="flex flex-wrap gap-4">
                        @auth
                            @if(auth()->user()->channel && auth()->user()->channel->id === $channel->id)
                                <a href="{{ route('filament.admin.pages.dashboard') }}"
                                    class="px-8 py-3 bg-white/15 hover:bg-white/25 backdrop-blur rounded-lg font-semibold transition-all border border-white/20 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Gerenciar Canal
                                </a>
                            @else
                                <livewire:follow-button :channel="$channel" :wire:key="'follow-' . $channel->id" />
                            @endif
                        @endauth

                        @guest
                            <livewire:follow-button :channel="$channel" :wire:key="'follow-guest-' . $channel->id" />
                        @endguest

                        <a href="{{ 'https://www.youtube.com/@' . $channel->link }}" target="_blank"
                            class="px-8 py-3 bg-red-500/80 hover:bg-red-500 backdrop-blur rounded-lg font-semibold transition-all border border-red-400/20 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.5 6.2c-.3-1-1-1.8-2-2.1C19.6 3.5 12 3.5 12 3.5s-7.6 0-9.5.6c-1 .3-1.7 1.1-2 2.1C0 8.1 0 12 0 12s0 3.9.5 5.8c.3 1 1 1.8 2 2.1 1.9.6 9.5.6 9.5.6s7.6 0 9.5-.6c1-.3 1.7-1.1 2-2.1.5-1.9.5-5.8.5-5.8s0-3.9-.5-5.8zM9.5 15.6V8.4l6.3 3.6-6.3 3.6z" />
                            </svg>
                            YouTube
                        </a>
                    </div>
                </div>
                <div class="relative flex justify-center">
                    <div
                        class="absolute w-[450px] h-[450px] bg-violet-400/30 dark:bg-purple-500/30 rounded-full blur-3xl">
                    </div>
                    @if($channel->brand)
                        <img src="{{ Storage::url($channel->brand) }}" alt="{{ $channel->name }}"
                            class="up relative w-[300px] h-[300px] rounded-full object-cover ring-4 ring-white/20 dark:ring-purple-500/30 shadow-2xl z-10">
                    @else
                        <div
                            class="up relative w-[300px] h-[300px] rounded-full bg-white/10 dark:bg-purple-500/20 flex items-center justify-center ring-4 ring-white/20 dark:ring-purple-500/30 shadow-2xl z-10">
                            <span class="text-8xl font-bold text-white/50">{{ substr($channel->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        {{-- SOFT FADE TRANSITION --}}
        <div
            class="absolute bottom-0 left-0 right-0 h-56 bg-gradient-to-b from-transparent via-slate-700/40 to-white dark:via-slate-900/40 dark:to-slate-900">
        </div>
    </section>

    {{-- CHANNEL OWNER CARD SECTION --}}
    <section class="bg-white dark:bg-slate-900 py-16">
        <div class="max-w-screen-xl px-4 mx-auto">
            <div
                class="flex flex-col md:flex-row items-center gap-8 p-8 rounded-3xl bg-gray-50 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-700/50">
                <div class="flex-shrink-0">
                    @if($channel->user->avatar)
                        <img src="{{ Storage::url($channel->user->avatar) }}" alt="{{ $channel->user->name }}"
                            class="w-24 h-24 rounded-full object-cover ring-4 ring-purple-500 shadow-xl"
                            @if($channel->color) style="ring-color: {{ $channel->color }}" @endif>
                    @else
                        <div
                            class="w-24 h-24 rounded-full bg-gradient-to-br from-purple-500 to-violet-600 flex items-center justify-center shadow-xl">
                            <span class="text-3xl font-bold text-white">{{ substr($channel->user->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <div class="text-center md:text-left flex-1">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $channel->user->name }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        {{ $channel->description ?? 'Criador de conteúdo' }}
                    </p>
                    <div class="flex flex-wrap gap-3 justify-center md:justify-start">
                        <a href="{{ 'https://www.youtube.com/@' . $channel->link }}" target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-full transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.5 6.2c-.3-1-1-1.8-2-2.1C19.6 3.5 12 3.5 12 3.5s-7.6 0-9.5.6c-1 .3-1.7 1.1-2 2.1C0 8.1 0 12 0 12s0 3.9.5 5.8c.3 1 1 1.8 2 2.1 1.9.6 9.5.6 9.5.6s7.6 0 9.5-.6c1-.3 1.7-1.1 2-2.1.5-1.9.5-5.8.5-5.8s0-3.9-.5-5.8zM9.5 15.6V8.4l6.3 3.6-6.3 3.6z" />
                            </svg>
                            Inscrever-se
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ROULETTE SECTION - PLACEHOLDER --}}
    @auth
        <section class="bg-gray-100 dark:bg-slate-950 py-20">
            <div class="max-w-screen-xl px-4 mx-auto">
                <div class="text-center mb-12">
                    <span
                        class="inline-block px-4 py-1 mb-4 text-sm font-medium rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400">
                        Em breve
                    </span>
                    <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-4">Roleta de Sorteios</h2>
                    <p class="text-gray-500 dark:text-gray-400 max-w-lg mx-auto">Sistema de sorteios interativo para seus
                        seguidores.</p>
                </div>

                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    {{-- Roulette Visual Placeholder --}}
                    <div class="relative flex justify-center">
                        <div class="relative w-80 h-80">
                            {{-- Outer ring --}}
                            <div
                                class="absolute inset-0 rounded-full border-8 border-dashed border-purple-300 dark:border-purple-700 animate-spin-slow">
                            </div>
                            {{-- Inner circle --}}
                            <div
                                class="absolute inset-8 rounded-full bg-gradient-to-br from-purple-500 to-violet-600 shadow-2xl flex items-center justify-center">
                                <div class="text-center text-white">
                                    <svg class="w-16 h-16 mx-auto mb-4 animate-pulse" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-lg font-bold">Em desenvolvimento</p>
                                    <p class="text-sm opacity-80">Aguarde!</p>
                                </div>
                            </div>
                            {{-- Decorative dots --}}
                            <div
                                class="absolute w-4 h-4 rounded-full bg-amber-400 shadow-lg top-0 left-1/2 -translate-x-1/2 -translate-y-1/2">
                            </div>
                            <div
                                class="absolute w-4 h-4 rounded-full bg-amber-400 shadow-lg top-[14%] right-[14%] -translate-x-1/2 -translate-y-1/2">
                            </div>
                            <div
                                class="absolute w-4 h-4 rounded-full bg-amber-400 shadow-lg top-1/2 right-0 -translate-x-1/2 -translate-y-1/2">
                            </div>
                            <div
                                class="absolute w-4 h-4 rounded-full bg-amber-400 shadow-lg bottom-[14%] right-[14%] -translate-x-1/2 -translate-y-1/2">
                            </div>
                            <div
                                class="absolute w-4 h-4 rounded-full bg-amber-400 shadow-lg bottom-0 left-1/2 -translate-x-1/2 -translate-y-1/2">
                            </div>
                            <div
                                class="absolute w-4 h-4 rounded-full bg-amber-400 shadow-lg bottom-[14%] left-[14%] -translate-x-1/2 -translate-y-1/2">
                            </div>
                            <div
                                class="absolute w-4 h-4 rounded-full bg-amber-400 shadow-lg top-1/2 left-0 -translate-x-1/2 -translate-y-1/2">
                            </div>
                            <div
                                class="absolute w-4 h-4 rounded-full bg-amber-400 shadow-lg top-[14%] left-[14%] -translate-x-1/2 -translate-y-1/2">
                            </div>
                        </div>
                    </div>

                    {{-- Participants List Placeholder --}}
                    <div
                        class="p-8 rounded-3xl bg-white dark:bg-slate-800/50 border border-gray-100 dark:border-slate-700/50">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Participantes
                        </h3>
                        <div class="space-y-3">
                            @for($i = 1; $i <= 5; $i++)
                                <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-slate-700/50">
                                    <div
                                        class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-400 to-violet-500 flex items-center justify-center text-white font-bold text-sm">
                                        {{ $i }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="h-4 bg-gray-200 dark:bg-slate-600 rounded-full w-3/4 animate-pulse"></div>
                                    </div>
                                </div>
                            @endfor
                            <p class="text-center text-sm text-gray-400 dark:text-gray-500 mt-4">
                                Lista de participantes aparecerá aqui
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endauth

    {{-- CAMPAIGN SECTION --}}
    @if($channel->camping)
        <section class="bg-white dark:bg-slate-900 py-20" id="campaind_id">
            <div class="max-w-screen-xl px-4 mx-auto">
                <div class="text-center mb-12">
                    <span
                        class="inline-block px-4 py-1 mb-4 text-sm font-medium rounded-full bg-green-500/10 text-green-600 dark:text-green-400">
                        Campanha ativa
                    </span>
                    <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-4">{{ $channel->camping->title }}
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">{{ $channel->camping->content }}</p>
                </div>

                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    <div
                        class="p-8 rounded-3xl bg-gray-50 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-700/50">
                        <iframe class="w-full h-[200px] rounded-xl" src="{{ $channel->camping->linkGoal }}"
                            frameborder="0"></iframe>
                    </div>
                    <div class="flex justify-center">
                        <iframe class="w-[220px] h-[300px] rounded-xl" src="{{ $channel->camping->qrCode }}"
                            frameborder="0"></iframe>
                    </div>
                </div>

                @if($channel->camping->image)
                    <div class="mt-12">
                        <img class="w-full max-w-3xl mx-auto rounded-2xl shadow-xl"
                            src="{{ Storage::url($channel->camping->image) }}" alt="Campaign image">
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- POSTS SECTION --}}
    <section class="bg-gray-100 dark:bg-slate-950 py-20">
        <div class="max-w-screen-xl px-4 mx-auto">
            <div class="text-center mb-12">
                <span
                    class="inline-block px-4 py-1 mb-4 text-sm font-medium rounded-full bg-blue-500/10 text-blue-600 dark:text-blue-400">
                    Conteúdo
                </span>
                <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-4">
                    {{ $posts->count() > 0 ? 'Postagens recentes' : 'Em breve novos conteúdos' }}
                </h2>
                @if($posts->count() > 0)
                    <p class="text-gray-500 dark:text-gray-400 max-w-lg mx-auto">Confira as últimas postagens deste canal.
                    </p>
                @endif
            </div>

            @if($posts->count() > 0)
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($posts as $post)
                        <article
                            class="group bg-white dark:bg-slate-900 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                            <a href="{{ route('posts.post', ['slug' => $post->slug]) }}" class="block overflow-hidden">
                                <img src="{{ Storage::url($post->featured_image_url) }}" alt="{{ $post->title }}"
                                    class="w-full h-52 object-cover group-hover:scale-105 transition-transform duration-500">
                            </a>
                            <div class="p-6">
                                <div class="flex items-center gap-2 mb-3">
                                    <span
                                        class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold uppercase rounded-full bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-gray-400">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        {{ $post->views }}
                                    </span>
                                </div>
                                <a href="{{ route('posts.post', ['slug' => $post->slug]) }}">
                                    <h3
                                        class="text-lg font-bold text-gray-900 dark:text-white mb-2 group-hover:text-purple-600 transition-colors line-clamp-2">
                                        {{ Str::limit($post->title, 50) }}
                                    </h3>
                                </a>
                                <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-4">
                                    {!! Str::limit($post->summary, 100) !!}
                                </p>
                            </div>
                        </article>
                    @endforeach
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
                        Este canal ainda não possui postagens.
                    </p>
                </div>
            @endif
        </div>
    </section>

    <x-partials.footer />
</x-layouts.app>