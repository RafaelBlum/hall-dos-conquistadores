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
        <div class="relative max-w-screen-xl px-4 pt-32 pb-24 mx-auto lg:pt-44">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span
                        class="inline-block px-4 py-1 mb-6 text-sm font-medium rounded-full bg-white/10 text-white/90 border border-white/20">
                        Campanhas
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
                    <div
                        class="absolute w-[450px] h-[450px] bg-violet-400/30 dark:bg-purple-500/30 rounded-full blur-3xl">
                    </div>
                    <img src="{{ asset('images/hero-3.png') }}" alt="hero" class="up relative w-[400px] h-auto z-10">
                    <img src="{{ asset('images/hero-7.png') }}" alt="hero"
                        class="vertical-loop-animation absolute inset-0 w-[400px] h-auto mx-auto z-20">
                </div>
            </div>
        </div>
        <div
            class="absolute bottom-0 left-0 right-0 h-56 bg-gradient-to-b from-transparent via-slate-700/40 to-white dark:via-slate-900/40 dark:to-slate-900">
        </div>
    </section>

    {{-- CAMPAIGNS SECTION --}}
    <section class="bg-white dark:bg-slate-900 py-20">
        <div class="max-w-screen-xl px-4 mx-auto">
            <div class="text-center mb-12">
                <span
                    class="inline-block px-4 py-1 mb-4 text-sm font-medium rounded-full bg-green-500/10 text-green-600">
                    Ativas
                </span>
                <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-4">Campanhas</h2>
                <p class="text-gray-500 dark:text-gray-400 max-w-lg mx-auto">Apoie os canais parceiros através das
                    campanhas ativas.</p>
            </div>

            @if($campings)
                <div class="space-y-12">
                    @foreach($campings as $campaing)
                        <div
                            class="group flex flex-col lg:flex-row gap-8 p-8 rounded-2xl bg-gray-50 dark:bg-slate-800 hover:shadow-xl transition-all duration-300">
                            <div class="flex-shrink-0 flex items-center justify-center">
                                <a href="{{route('my.channel', ['slug' => $campaing->channel->slug])}}" class="block">
                                    <div
                                        class="w-40 h-40 rounded-2xl overflow-hidden ring-4 ring-gray-200 dark:ring-slate-700 group-hover:ring-purple-500 transition-all">
                                        <img src="{{Storage::url($campaing->channel->brand)}}"
                                            alt="{{$campaing->channel->title}}" class="w-full h-full object-cover">
                                    </div>
                                </a>
                            </div>
                            <div class="flex-1 flex flex-col justify-center">
                                <a href="{{route('my.channel', ['slug' => $campaing->channel->slug])}}"
                                    class="text-lg font-bold text-gray-900 dark:text-white hover:text-purple-600 transition-colors">
                                    {{$campaing->channel->title}}
                                </a>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{$campaing->channel->name}}</p>
                                <p class="text-gray-600 dark:text-gray-300 mt-4 leading-relaxed">{{$campaing->title}}</p>
                                <div class="mt-4">
                                    <a href="{{route('my.channel', ['slug' => $campaing->channel->slug . '#campaind_id'])}}"
                                        class="inline-flex items-center gap-2 text-sm font-medium text-purple-600 hover:text-purple-700 transition-colors group/link">
                                        Acesse e saiba mais
                                        <svg class="w-4 h-4 group-hover/link:translate-x-1 transition-transform" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
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
                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Campanhas dos canais parceiros</h3>
                    <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto mb-4">
                        Desculpe, mas no momento não existem campanhas criadas pelos canais.
                    </p>
                    <p class="text-purple-600 dark:text-purple-400 font-semibold">
                        Em breve as campanhas estarão disponíveis para que todos possam fortalecer e ajudar seu canal
                        favorito!
                    </p>
                </div>
            @endif
        </div>
    </section>

    <x-partials.footer />

</x-layouts.app>