<x-guest-layout>
    <div id="tsparticles" class="absolute inset-0 z-0"></div>

    <div class="z-10 w-full max-w-md bg-[#0f172a]/80 backdrop-blur-xl p-8 rounded-2xl border border-white/10 shadow-2xl text-center">

        <div class="mb-6 flex justify-center">
            <div class="p-4 bg-indigo-500/20 rounded-full">
                <span class="text-5xl text-indigo-400">✉️</span>
            </div>
        </div>

        <h2 class="text-2xl font-bold text-white mb-4">Quase lá! Valide sua inscrição</h2>

        <p class="text-gray-400 mb-8 leading-relaxed">
            Obrigado por se inscrever! Enviamos um link de ativação para o seu e-mail.
            Se não receber em alguns minutos, verifique sua caixa de spam.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 font-medium text-sm text-green-400 bg-green-900/30 p-3 rounded-lg border border-green-500/50">
                {{ __('Um novo link de verificação foi enviado para o endereço de e-mail fornecido.') }}
            </div>
        @endif

        <div class="flex flex-col gap-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold transition-all shadow-lg shadow-indigo-500/20">
                    REENVIAR E-MAIL DE VALIDAÇÃO
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-gray-500 hover:text-gray-300 text-sm underline">
                    Sair desta conta
                </button>
            </form>

            <a href="{{ route('app.home') }}" class="flex items-center justify-center gap-2 text-sm text-indigo-400 hover:text-indigo-300 transition-colors mt-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Voltar ao início
            </a>
        </div>
    </div>
</x-guest-layout>
