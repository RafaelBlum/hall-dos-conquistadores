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
                Suporte
            </span>
            <h1
                class="text-5xl lg:text-6xl font-extrabold leading-tight mb-6 bg-gradient-to-r from-white via-violet-200 to-violet-300 dark:from-white dark:via-purple-200 dark:to-purple-400 bg-clip-text text-transparent">
                Fale Conosco
            </h1>
            <p class="text-lg text-white/80 dark:text-gray-300 mb-8 leading-relaxed max-w-2xl mx-auto">
                Precisa de ajuda? Estamos aqui para apoiar a comunidade retrô.
            </p>
        </div>
        <div
            class="absolute bottom-0 left-0 right-0 h-56 bg-gradient-to-b from-transparent via-slate-700/40 to-white dark:via-slate-900/40 dark:to-slate-900">
        </div>
    </section>

    {{-- SUPPORT CONTENT --}}
    <section class="bg-white dark:bg-slate-900 py-20">
        <div class="max-w-3xl px-4 mx-auto text-center">
            <span
                class="inline-block px-4 py-1 mb-4 text-sm font-medium rounded-full bg-purple-500/10 text-purple-600 dark:text-purple-400">
                Ajuda
            </span>
            <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-6">Como podemos ajudar?</h2>
            <p class="text-gray-600 dark:text-gray-300 text-lg leading-relaxed mb-12">
                A <a href="{{route('app.home')}}" class="text-purple-600 hover:underline font-semibold">Hall dos
                    Conquistadores</a> é uma comunidade dedicada aos clássicos dos games retrô.
                Se você tem dúvidas, sugestões ou precisa de suporte, entre em contato conosco.
            </p>

            <div class="grid gap-6 sm:grid-cols-2 text-left">
                <div class="p-6 rounded-2xl bg-gray-50 dark:bg-slate-800">
                    <div class="w-12 h-12 mb-4 rounded-xl bg-purple-500/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Dúvidas gerais</h3>
                    <p class="text-gray-600 dark:text-gray-400">Tire suas dúvidas sobre o funcionamento da plataforma e
                        como participar da comunidade.</p>
                </div>

                <div class="p-6 rounded-2xl bg-gray-50 dark:bg-slate-800">
                    <div class="w-12 h-12 mb-4 rounded-xl bg-blue-500/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Cadastro de canal</h3>
                    <p class="text-gray-600 dark:text-gray-400">Saiba como cadastrar seu canal YouTube na plataforma e
                        fazer parte da comunidade.</p>
                </div>

                <div class="p-6 rounded-2xl bg-gray-50 dark:bg-slate-800">
                    <div class="w-12 h-12 mb-4 rounded-xl bg-green-500/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Problemas de conta</h3>
                    <p class="text-gray-600 dark:text-gray-400">Precisa recuperar acesso ou reportar um problema com sua
                        conta? Fale conosco.</p>
                </div>

                <div class="p-6 rounded-2xl bg-gray-50 dark:bg-slate-800">
                    <div class="w-12 h-12 mb-4 rounded-xl bg-red-500/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Reportar abuso</h3>
                    <p class="text-gray-600 dark:text-gray-400">Encontrou conteúdo inadequado? Nos ajude a manter a
                        comunidade segura.</p>
                </div>
            </div>
        </div>
    </section>

    <x-partials.footer />

</x-layouts.app>