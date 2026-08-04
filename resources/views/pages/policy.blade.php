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
                Políticas
            </span>
            <h1
                class="text-5xl lg:text-6xl font-extrabold leading-tight mb-6 bg-gradient-to-r from-white via-violet-200 to-violet-300 dark:from-white dark:via-purple-200 dark:to-purple-400 bg-clip-text text-transparent">
                Privacidade e Termos
            </h1>
            <p class="text-lg text-white/80 dark:text-gray-300 mb-8 leading-relaxed max-w-2xl mx-auto">
                Tudo que você precisa saber sobre a política de privacidade da {{ config('app.name') }}.
            </p>
        </div>
        <div
            class="absolute bottom-0 left-0 right-0 h-56 bg-gradient-to-b from-transparent via-slate-700/40 to-white dark:via-slate-900/40 dark:to-slate-900">
        </div>
    </section>

    {{-- POLICY CONTENT --}}
    <section class="bg-white dark:bg-slate-900 py-20">
        <div class="max-w-3xl px-4 mx-auto">
            <div class="prose prose-lg dark:prose-invert max-w-none">
                <p class="text-gray-600 dark:text-gray-300 text-lg leading-relaxed mb-8">
                    Bem-vindo à <a href="{{route('app.home')}}"
                        class="text-purple-600 hover:underline font-semibold">Hall dos Conquistadores</a>.
                    Esta Política de Privacidade descreve nossas práticas em relação à coleta, uso e compartilhamento de
                    informações pessoais por meio de nossos produtos e serviços digitais.
                    Ao usar nossos serviços, você concorda com a coleta e uso de informações conforme descrito nesta
                    política.
                </p>

                <div class="space-y-8">
                    <div class="p-6 rounded-2xl bg-gray-50 dark:bg-slate-800">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Informações coletadas</h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            Na <a href="{{route('app.home')}}"
                                class="text-purple-600 hover:underline font-semibold">Hall dos Conquistadores</a>,
                            sua privacidade é nossa prioridade. Não compartilhamos, vendemos ou alugamos suas
                            informações pessoais a terceiros.
                            Todos os dados armazenados por meio de nossos serviços são de sua propriedade e podem ser
                            transferidos
                            ou excluídos mediante solicitação.
                        </p>
                    </div>

                    <div class="p-6 rounded-2xl bg-gray-50 dark:bg-slate-800">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Seus direitos</h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            Você tem o direito de acessar, corrigir e solicitar a exclusão de seus dados pessoais, com
                            certas limitações no acesso aos nossos produtos e serviços.
                        </p>
                    </div>

                    <div class="p-6 rounded-2xl bg-gray-50 dark:bg-slate-800">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Segurança</h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            Estamos comprometidos com a segurança dos seus dados, implementando criptografia e padrões
                            regulares de segurança.
                        </p>
                    </div>

                    <div class="p-6 rounded-2xl bg-gray-50 dark:bg-slate-800">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Responsabilidades</h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            Toda e qualquer informação postada na <a href="{{route('app.home')}}"
                                class="text-purple-600 hover:underline font-semibold">Hall dos Conquistadores</a>
                            é de responsabilidade do usuário ou canal. Qualquer tipo de abuso ou ilegalidade poderá
                            sofrer banimento com deleção de todas as suas informações.
                        </p>
                    </div>

                    <div class="p-6 rounded-2xl bg-gray-50 dark:bg-slate-800">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Privacidade das crianças</h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            O <a href="{{route('app.home')}}" class="text-purple-600 hover:underline font-semibold">Hall
                                dos Conquistadores</a> é destinado ao uso por indivíduos
                            que atingiram a idade legal em sua jurisdição. Não coletamos intencionalmente dados de
                            indivíduos abaixo da idade legal.
                        </p>
                    </div>

                    <div class="p-6 rounded-2xl bg-gray-50 dark:bg-slate-800">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Alterações na Política</h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            Podemos modificar esta política a qualquer momento. O uso contínuo do serviço significa
                            aceitação dessas mudanças.
                            Se mudarmos nossa política de privacidade, publicaremos essas alterações nesta página.
                        </p>
                    </div>

                    <div class="p-6 rounded-2xl bg-gray-50 dark:bg-slate-800">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Contate-nos</h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            Para dúvidas sobre esta Política de Privacidade, entre em contato conosco pelo
                            <a href="{{route('app.support')}}"
                                class="text-purple-600 hover:underline font-semibold">SUPORTE</a> em nosso site.
                        </p>
                    </div>

                    <div class="p-6 rounded-2xl bg-gray-50 dark:bg-slate-800">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Acordo</h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            Ao utilizar os serviços da <a href="{{route('app.home')}}"
                                class="text-purple-600 hover:underline font-semibold">Hall dos Conquistadores</a>,
                            você reconhece que entendeu e concorda com esta Política de Privacidade.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-partials.footer />

</x-layouts.app>