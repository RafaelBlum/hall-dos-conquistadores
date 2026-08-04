<x-layouts.app>

    <x-partials.navbar />

    {{-- HERO SECTION --}}
    <section
        class="relative overflow-hidden text-white bg-gradient-to-br from-slate-700 via-violet-600 to-slate-700 dark:from-slate-900 dark:via-purple-900 dark:to-slate-900">
        <div class="absolute inset-0 opacity-15 dark:opacity-20">
            <div class="absolute top-20 left-10 w-96 h-96 bg-violet-400 dark:bg-purple-500 rounded-full blur-3xl"></div>
            <div
                class="absolute bottom-10 right-20 w-[500px] h-[500px] bg-sky-400 dark:bg-blue-500 rounded-full blur-3xl">
            </div>
        </div>
        <div class="relative max-w-screen-xl px-4 pt-32 pb-56 mx-auto lg:pt-44">
            <div class="max-w-3xl mx-auto text-center">
                <span
                    class="inline-block px-4 py-1 mb-6 text-sm font-medium rounded-full bg-white/10 text-white/90 border border-white/20">
                    Canais Parceiros
                </span>
                <h1
                    class="text-4xl lg:text-6xl font-extrabold leading-tight mb-6 bg-gradient-to-r from-white via-violet-200 to-violet-300 dark:from-white dark:via-purple-200 dark:to-purple-400 bg-clip-text text-transparent">
                    Streamers parceiros da<br>{{ config('app.name') }}
                </h1>
                <p class="text-lg text-white/80 dark:text-gray-300 leading-relaxed max-w-lg mx-auto">
                    Bem-vindo à sua comunidade dedicada aos clássicos dos games retrô! Explore os canais parceiros que apoiam o projeto.
                </p>
            </div>
        </div>
        <div
            class="absolute bottom-0 left-0 right-0 h-56 bg-gradient-to-b from-transparent via-slate-700/40 to-white dark:via-slate-900/40 dark:to-slate-900">
        </div>
    </section>

    {{-- CHANNELS SECTION --}}
    <section class="bg-white dark:bg-slate-900 py-20">
        <div class="max-w-screen-xl px-4 mx-auto text-center">
            <span class="inline-block px-4 py-1 mb-4 text-sm font-medium rounded-full bg-blue-500/10 text-blue-600">
                Parceiros
            </span>
            <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-4">Canais apoiadores</h2>
            <p class="text-gray-500 mb-12 max-w-lg mx-auto">Veja todos os canais que estão na comunidade.</p>

            @if($channels->isNotEmpty())
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($channels as $channel)
                        <div
                            class="group p-6 rounded-2xl bg-white dark:bg-slate-800 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                            <div
                                class="w-28 h-28 mx-auto mb-4 rounded-full overflow-hidden ring-4 ring-gray-200 dark:ring-slate-700 group-hover:ring-purple-500 transition-all">
                                <img src="{{ Storage::url($channel->brand) }}" class="w-full h-full object-cover"
                                    alt="{{ $channel->title }}">
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ $channel->title }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $channel->name }}</p>
                            <div class="mt-4">
                                <a href="{{ 'https://www.youtube.com/@' . $channel->link }}" target="_blank"
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
            @else
                <div class="text-center py-16">
                    <div
                        class="w-20 h-20 mx-auto mb-6 rounded-full bg-gray-200 dark:bg-slate-700 flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <p class="text-lg text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                        Nenhum canal encontrado no momento.
                    </p>
                </div>
            @endif
        </div>
    </section>

    <x-partials.footer />

</x-layouts.app>
