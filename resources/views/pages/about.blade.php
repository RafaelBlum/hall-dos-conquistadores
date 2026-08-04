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
        <div class="relative max-w-screen-xl px-4 pt-32 pb-40 mx-auto lg:pt-44 text-center">
            <span
                class="inline-block px-4 py-1 mb-6 text-sm font-medium rounded-full bg-white/10 text-white/90 border border-white/20">
                Sobre nós
            </span>
            <h1
                class="text-5xl lg:text-6xl font-extrabold leading-tight mb-6 bg-gradient-to-r from-white via-violet-200 to-violet-300 dark:from-white dark:via-purple-200 dark:to-purple-400 bg-clip-text text-transparent">
                Hall dos Conquistadores
            </h1>
            <p class="text-lg text-white/80 dark:text-gray-300 mb-8 leading-relaxed max-w-2xl mx-auto">
                Bem-vindo à sua comunidade dedicada aos clássicos dos games retrô! A nostalgia dos anos 80 e 90, uma
                época de ouro que foi incrível para muitas pessoas.
            </p>
        </div>
        <div
            class="absolute bottom-0 left-0 right-0 h-56 bg-gradient-to-b from-transparent via-slate-700/40 to-white dark:via-slate-900/40 dark:to-slate-900">
        </div>
    </section>

    {{-- ABOUT TEXT SECTION --}}
    <section class="bg-white dark:bg-slate-900 py-20">
        <div class="max-w-screen-xl px-4 mx-auto">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="up">
                    <img src="{{ asset('images/about_img.jpg') }}" alt="Sobre" class="w-full rounded-2xl shadow-xl">
                </div>
                <div class="up">
                    <span
                        class="inline-block px-4 py-1 mb-4 text-sm font-medium rounded-full bg-purple-500/10 text-purple-600 dark:text-purple-400">
                        Nossa história
                    </span>
                    <p class="text-gray-600 dark:text-gray-300 text-lg leading-relaxed mb-6">
                        Na <a href="{{route('app.home')}}" class="text-purple-600 hover:underline font-semibold">Hall
                            dos Conquistadores</a>, celebramos não apenas os jogos que marcaram época, mas também
                        fortalecemos a comunidade através do apoio a nossos parceiros do YouTube. Trabalhamos com
                        criadores apaixonados que compartilham histórias, dicas valiosas e momentos inesquecíveis.
                    </p>
                    <p class="text-gray-600 dark:text-gray-300 text-lg leading-relaxed mb-6">
                        Aqui, o bate-papo é tão importante quanto a jogatina, proporcionando uma plataforma onde os fãs
                        de games retrô podem se reunir para discutir seus títulos favoritos, criar amizades, descobrir
                        novos clássicos e viver momentos de felicidade e nostalgia.
                    </p>
                    <p class="text-gray-600 dark:text-gray-300 text-lg leading-relaxed">
                        Nosso blog oferece atualizações regulares sobre eventos de gaming, análises de gameplays
                        icônicos e previews de novos lançamentos imperdíveis. Prepare-se para uma viagem no tempo
                        através dos mundos pixelizados que capturaram nossa imaginação!
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- STATISTICS SECTION --}}
    <section class="bg-gray-100 dark:bg-slate-950 py-20">
        <div class="max-w-screen-xl px-4 mx-auto text-center">
            <span class="inline-block px-4 py-1 mb-4 text-sm font-medium rounded-full bg-blue-500/10 text-blue-600">
                Números
            </span>
            <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-12">Estatísticas</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 max-w-3xl mx-auto">
                <div class="p-8 rounded-2xl bg-white dark:bg-slate-800 shadow-sm">
                    <div class="text-5xl font-extrabold text-purple-600 dark:text-purple-400">{{$posts->count()}}</div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 tracking-widest uppercase mt-2">
                        Postagens</div>
                </div>
                <div class="p-8 rounded-2xl bg-white dark:bg-slate-800 shadow-sm">
                    <div class="text-5xl font-extrabold text-purple-600 dark:text-purple-400">{{$channels->count()}}
                    </div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 tracking-widest uppercase mt-2">
                        Canais</div>
                </div>
                <div class="p-8 rounded-2xl bg-white dark:bg-slate-800 shadow-sm">
                    <div class="text-5xl font-extrabold text-purple-600 dark:text-purple-400">{{$campaings->count()}}
                    </div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 tracking-widest uppercase mt-2">
                        Campanhas</div>
                </div>
            </div>
        </div>
    </section>

    {{-- CHANNELS SECTION --}}
    <section class="bg-white dark:bg-slate-900 py-20">
        <div class="max-w-screen-xl px-4 mx-auto text-center">
            <span class="inline-block px-4 py-1 mb-4 text-sm font-medium rounded-full bg-red-500/10 text-red-500">
                Parceiros
            </span>
            <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-4">Canais apoiadores</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-12 max-w-lg mx-auto">Canais parceiros que apoiam o projeto
                Hall dos Conquistadores.</p>

            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($channels as $channel)
                    <div
                        class="group p-6 rounded-2xl bg-gray-50 dark:bg-slate-800 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
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
        </div>
    </section>

    <x-partials.footer />

</x-layouts.app>