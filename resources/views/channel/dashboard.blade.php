<x-layouts.app>
    <x-partials.navbar />

    {{-- HERO SECTION - CHANNEL DASHBOARD --}}
    <section
        class="relative overflow-hidden text-white bg-gradient-to-br from-slate-700 via-violet-600 to-slate-700 dark:from-slate-900 dark:via-purple-900 dark:to-slate-900">
        <div class="absolute inset-0 opacity-15 dark:opacity-20">
            <div class="absolute top-20 left-10 w-96 h-96 bg-violet-400 dark:bg-purple-500 rounded-full blur-3xl"></div>
            <div
                class="absolute bottom-10 right-20 w-[500px] h-[500px] bg-sky-400 dark:bg-blue-500 rounded-full blur-3xl">
            </div>
        </div>
        <div class="relative max-w-screen-xl px-4 pt-32 pb-56 mx-auto lg:pt-44">
            <div class="flex flex-col lg:flex-row items-center gap-8">
                <div class="flex-shrink-0">
                    @if($channel->brand)
                        <img src="{{ Storage::url($channel->brand) }}" alt="{{ $channel->name }}"
                            class="w-32 h-32 rounded-full object-cover ring-4 ring-white/20 dark:ring-purple-500/30 shadow-2xl">
                    @else
                        <div
                            class="w-32 h-32 rounded-full bg-white/10 dark:bg-purple-500/20 flex items-center justify-center ring-4 ring-white/20 dark:ring-purple-500/30 shadow-2xl">
                            <span class="text-5xl font-bold text-white/50">{{ substr($channel->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <div class="text-center lg:text-left">
                    <span
                        class="inline-block px-4 py-1 mb-4 text-sm font-medium rounded-full bg-white/10 text-white/90 border border-white/20">
                        Dashboard do Canal xx
                    </span>
                    <h1
                        class="text-4xl lg:text-5xl font-extrabold leading-tight mb-4 bg-gradient-to-r from-white via-violet-200 to-violet-300 dark:from-white dark:via-purple-200 dark:to-purple-400 bg-clip-text text-transparent">
                        {{ $channel->name }}
                    </h1>
                    <p class="text-lg text-white/80 dark:text-gray-300 mb-6">
                        {{ $channel->description ?? 'Dashboard com dados do canal' }}
                    </p>
                    <div class="flex flex-wrap gap-3 justify-center lg:justify-start">
                        <a href="{{ route('my.channel', $channel->slug) }}"
                            class="px-6 py-2 bg-white/15 hover:bg-white/25 backdrop-blur rounded-lg font-medium transition-all border border-white/20 text-sm">
                            Ver perfil público
                        </a>
                        <a href="{{ 'https://www.youtube.com/@' . $channel->link }}" target="_blank"
                            class="px-6 py-2 bg-red-500/80 hover:bg-red-500 backdrop-blur rounded-lg font-medium transition-all border border-red-400/20 text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.5 6.2c-.3-1-1-1.8-2-2.1C19.6 3.5 12 3.5 12 3.5s-7.6 0-9.5.6c-1 .3-1.7 1.1-2 2.1C0 8.1 0 12 0 12s0 3.9.5 5.8c.3 1 1 1.8 2 2.1 1.9.6 9.5.6 9.5.6s7.6 0 9.5-.6c1-.3 1.7-1.1 2-2.1.5-1.9.5-5.8.5-5.8s0-3.9-.5-5.8zM9.5 15.6V8.4l6.3 3.6-6.3 3.6z" />
                            </svg>
                            YouTube
                        </a>
                    </div>
                </div>
            </div>
        </div>
        {{-- SOFT FADE TRANSITION --}}
        <div
            class="absolute bottom-0 left-0 right-0 h-56 bg-gradient-to-b from-transparent via-slate-700/40 to-white dark:via-slate-900/40 dark:to-slate-900">
        </div>
    </section>

    {{-- STATS CARDS --}}
    <section class="bg-white dark:bg-slate-900 py-16">
        <div class="max-w-screen-xl px-4 mx-auto">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Seguidores --}}
                <div
                    class="p-6 rounded-2xl bg-gray-50 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-700/50 text-center">
                    <div
                        class="w-12 h-12 mx-auto mb-3 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $channel->followers_count ?? 0 }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Seguidores</p>
                </div>

                {{-- Posts --}}
                <div
                    class="p-6 rounded-2xl bg-gray-50 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-700/50 text-center">
                    <div
                        class="w-12 h-12 mx-auto mb-3 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $posts->count() }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Posts</p>
                </div>

                {{-- YouTube Subs (placeholder) --}}
                <div
                    class="p-6 rounded-2xl bg-gray-50 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-700/50 text-center">
                    <div
                        class="w-12 h-12 mx-auto mb-3 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M23.5 6.2c-.3-1-1-1.8-2-2.1C19.6 3.5 12 3.5 12 3.5s-7.6 0-9.5.6c-1 .3-1.7 1.1-2 2.1C0 8.1 0 12 0 12s0 3.9.5 5.8c.3 1 1 1.8 2 2.1 1.9.6 9.5.6 9.5.6s7.6 0 9.5-.6c1-.3 1.7-1.1 2-2.1.5-1.9.5-5.8.5-5.8s0-3.9-.5-5.8zM9.5 15.6V8.4l6.3 3.6-6.3 3.6z" />
                        </svg>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">--</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">YT Inscritos</p>
                </div>

                {{-- RA Score (placeholder) --}}
                <div
                    class="p-6 rounded-2xl bg-gray-50 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-700/50 text-center">
                    <div
                        class="w-12 h-12 mx-auto mb-3 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">--</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">RA Pontos</p>
                </div>
            </div>
        </div>
    </section>

    {{-- LIVE STATUS & YOUTUBE SECTION --}}
    <section class="bg-gray-100 dark:bg-slate-950 py-20">
        <div class="max-w-screen-xl px-4 mx-auto">
            <div class="grid lg:grid-cols-2 gap-8">
                {{-- Live Status --}}
                <div
                    class="p-8 rounded-3xl bg-white dark:bg-slate-800/50 border border-gray-100 dark:border-slate-700/50">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-3 h-3 rounded-full bg-gray-400 dark:bg-gray-600"></div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Status da Live</h3>
                    </div>
                    <div class="text-center py-12">
                        <div
                            class="w-20 h-20 mx-auto mb-6 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 mb-2">Canal offline no momento</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500">Integração com YouTube API será adicionada
                            em breve</p>
                    </div>
                </div>

                {{-- YouTube Data --}}
                <div
                    class="p-8 rounded-3xl bg-white dark:bg-slate-800/50 border border-gray-100 dark:border-slate-700/50">
                    <div class="flex items-center gap-3 mb-6">
                        <div
                            class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.5 6.2c-.3-1-1-1.8-2-2.1C19.6 3.5 12 3.5 12 3.5s-7.6 0-9.5.6c-1 .3-1.7 1.1-2 2.1C0 8.1 0 12 0 12s0 3.9.5 5.8c.3 1 1 1.8 2 2.1 1.9.6 9.5.6 9.5.6s7.6 0 9.5-.6c1-.3 1.7-1.1 2-2.1.5-1.9.5-5.8.5-5.8s0-3.9-.5-5.8zM9.5 15.6V8.4l6.3 3.6-6.3 3.6z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Dados do YouTube</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-slate-700/50">
                            <span class="text-gray-600 dark:text-gray-400">Inscritos</span>
                            <span class="font-bold text-gray-900 dark:text-white">--</span>
                        </div>
                        <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-slate-700/50">
                            <span class="text-gray-600 dark:text-gray-400">Total de views</span>
                            <span class="font-bold text-gray-900 dark:text-white">--</span>
                        </div>
                        <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-slate-700/50">
                            <span class="text-gray-600 dark:text-gray-400">Vídeos</span>
                            <span class="font-bold text-gray-900 dark:text-white">--</span>
                        </div>
                        <p class="text-center text-sm text-gray-400 dark:text-gray-500 pt-4">
                            Dados serão carregados via YouTube Data API v3
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- RETROACHIEVEMENTS SECTION --}}
    <section class="bg-white dark:bg-slate-900 py-20">
        <div class="max-w-screen-xl px-4 mx-auto">
            <div class="text-center mb-12">
                <span
                    class="inline-block px-4 py-1 mb-4 text-sm font-medium rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400">
                    RetroAchievements
                </span>
                <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-4">Conquistas Retro</h2>
                <p class="text-gray-500 dark:text-gray-400 max-w-lg mx-auto">Dados do RetroAchievements.org serão
                    exibidos aqui.</p>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                {{-- RA Profile --}}
                <div
                    class="p-8 rounded-3xl bg-gray-50 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-700/50 text-center">
                    <div
                        class="w-24 h-24 mx-auto mb-6 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center shadow-xl">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Perfil RA</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Username será exibido aqui</p>
                </div>

                {{-- Recent Achievements --}}
                <div
                    class="lg:col-span-2 p-8 rounded-3xl bg-gray-50 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-700/50">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                        Conquistas Recentes
                    </h3>
                    <div class="space-y-4">
                        @for($i = 1; $i <= 3; $i++)
                            <div class="flex items-center gap-4 p-4 rounded-xl bg-white dark:bg-slate-700/50">
                                <div class="w-12 h-12 rounded-lg bg-gray-200 dark:bg-slate-600 animate-pulse"></div>
                                <div class="flex-1">
                                    <div class="h-4 bg-gray-200 dark:bg-slate-600 rounded-full w-3/4 mb-2 animate-pulse">
                                    </div>
                                    <div class="h-3 bg-gray-200 dark:bg-slate-600 rounded-full w-1/2 animate-pulse"></div>
                                </div>
                            </div>
                        @endfor
                        <p class="text-center text-sm text-gray-400 dark:text-gray-500 pt-4">
                            Integração com RetroAchievements API será adicionada em breve
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- POSTS SECTION --}}
    <section class="bg-gray-100 dark:bg-slate-950 py-20">
        <div class="max-w-screen-xl px-4 mx-auto">
            <div class="text-center mb-12">
                <span
                    class="inline-block px-4 py-1 mb-4 text-sm font-medium rounded-full bg-blue-500/10 text-blue-600 dark:text-blue-400">
                    Conteúdo
                </span>
                <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-4">
                    {{ $posts->count() > 0 ? 'Postagens do Canal' : 'Sem postagens' }}
                </h2>
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
                                <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
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