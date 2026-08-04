<!DOCTYPE html>
<html lang="pt-BR" x-data="{ dark: true }" :class="dark ? 'dark' : ''">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Invaders v2 — Hall dos Conquistadores</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/game2.css'])
    @livewireStyles
</head>

<body class="min-h-screen flex flex-col items-center justify-center p-4 select-none relative"
    :class="dark ? 'bg-[#0a0a1a] text-green-400' : 'bg-[#f0e6ff] text-purple-700'">

    {{-- Scanlines CRT --}}
    <div class="crt-scanlines"></div>

    {{-- Synthwave Background --}}
    <div class="synthwave-bg">
        <div class="grid-gradient"></div>
        <div class="grid-floor"></div>
        <div class="stars" id="stars-dark"></div>
    </div>

    {{-- Theme toggle --}}
    <div class="absolute top-4 right-4 z-50">
        <button @click="dark = !dark"
            class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-500 cursor-pointer"
            :class="dark ? 'bg-yellow-500/20 hover:bg-yellow-500/30' : 'bg-indigo-500/20 hover:bg-indigo-500/30'"
            :style="dark ? 'box-shadow: 0 0 15px 5px rgba(250,204,21,0.3)' : 'box-shadow: 0 0 15px 5px rgba(192,132,252,0.3)'">
            <svg x-show="dark" class="w-6 h-6" viewBox="0 0 16 16" style="image-rendering: pixelated">
                <rect x="7" y="0" width="2" height="2" fill="#facc15" />
                <rect x="3" y="1" width="2" height="1" fill="#fde047" />
                <rect x="11" y="1" width="2" height="1" fill="#fde047" />
                <rect x="5" y="3" width="6" height="2" fill="#facc15" />
                <rect x="0" y="5" width="2" height="2" fill="#fde047" />
                <rect x="14" y="5" width="2" height="2" fill="#fde047" />
                <rect x="3" y="5" width="10" height="6" fill="#facc15" />
                <rect x="4" y="6" width="8" height="4" fill="#fde047" />
                <rect x="5" y="7" width="6" height="2" fill="#fef08a" />
                <rect x="0" y="9" width="2" height="2" fill="#fde047" />
                <rect x="14" y="9" width="2" height="2" fill="#fde047" />
                <rect x="5" y="11" width="6" height="2" fill="#facc15" />
                <rect x="3" y="13" width="2" height="1" fill="#fde047" />
                <rect x="11" y="13" width="2" height="1" fill="#fde047" />
                <rect x="7" y="14" width="2" height="2" fill="#facc15" />
            </svg>
            <svg x-show="!dark" class="w-6 h-6" viewBox="0 0 16 16" style="image-rendering: pixelated">
                <rect x="6" y="0" width="2" height="2" fill="#c084fc" />
                <rect x="4" y="2" width="2" height="2" fill="#a855f7" />
                <rect x="8" y="2" width="4" height="2" fill="#c084fc" />
                <rect x="2" y="4" width="2" height="2" fill="#a855f7" />
                <rect x="10" y="4" width="4" height="2" fill="#c084fc" />
                <rect x="2" y="6" width="6" height="4" fill="#c084fc" />
                <rect x="3" y="7" width="5" height="2" fill="#a855f7" />
                <rect x="10" y="6" width="4" height="4" fill="#c084fc" />
                <rect x="4" y="10" width="4" height="2" fill="#a855f7" />
                <rect x="8" y="10" width="2" height="2" fill="#c084fc" />
                <rect x="3" y="12" width="3" height="2" fill="#a855f7" />
                <rect x="6" y="14" width="2" height="2" fill="#c084fc" />
            </svg>
        </button>
    </div>

    {{-- Content --}}
    <div class="relative z-10 w-full max-w-2xl">

        {{-- Game container --}}
        <div x-data="userInvadersV2(
                {{ $users->map(fn($u) => ['id' => $u->id, 'name' => $u->name])->toJson() }},
                {{ $authUser?->id ?? 'null' }},
                {{ auth()->check() ? 'true' : 'false' }}
            )" x-init="init()">

            {{-- Header --}}
            <div class="flex justify-between items-center mb-3 px-1">
                <div>
                    <span class="text-xs" :class="dark ? 'text-green-600' : 'text-purple-400'">SCORE</span>
                    <div class="text-2xl" :class="dark ? 'text-green-300' : 'text-purple-600'"
                        x-text="score.toString().padStart(6,'0')">000000</div>
                </div>
                <div class="text-center">
                    <div class="text-sm" :class="dark ? 'text-green-500' : 'text-purple-500'">INTRUDER USERS</div>
                    <div class="text-xs mt-0.5" :class="dark ? 'text-green-700' : 'text-purple-300'">v2.0.0</div>
                </div>
                <div class="text-right">
                    <span class="text-xs" :class="dark ? 'text-green-600' : 'text-purple-400'">VIDAS</span>
                    <div class="flex gap-1 justify-end mt-1">
                        <template x-for="i in 3" :key="i">
                            <svg width="16" height="16" viewBox="0 0 16 16"
                                :style="i > lives ? 'opacity:0.2' : ''"
                                style="image-rendering: pixelated">
                                <rect x="2" y="1" width="3" height="2" fill="#ef4444" />
                                <rect x="8" y="1" width="3" height="2" fill="#ef4444" />
                                <rect x="1" y="3" width="5" height="2" fill="#ef4444" />
                                <rect x="7" y="3" width="5" height="2" fill="#ef4444" />
                                <rect x="1" y="5" width="11" height="2" fill="#ef4444" />
                                <rect x="2" y="7" width="9" height="2" fill="#ef4444" />
                                <rect x="3" y="9" width="7" height="2" fill="#ef4444" />
                                <rect x="4" y="11" width="5" height="2" fill="#ef4444" />
                                <rect x="5" y="13" width="3" height="2" fill="#ef4444" />
                                <rect x="6" y="15" width="1" height="1" fill="#ef4444" />
                            </svg>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Canvas --}}
            <div class="relative w-full" style="max-width:640px; margin: 0 auto;">
                <canvas x-ref="canvas" width="640" height="480"
                    class="w-full rounded border-2"
                    :class="dark ? 'border-green-500/20' : 'border-purple-500/20'"></canvas>

                {{-- OVERLAY: IDLE --}}
                <div x-show="state === 'idle'"
                    class="absolute inset-0 flex flex-col items-center justify-center bg-black/70 rounded">
                    <div class="text-3xl font-bold mb-1"
                        :class="dark ? 'text-green-400' : 'text-purple-600'">INTRUDER USERS</div>
                    <div class="text-sm mb-4" :class="dark ? 'text-green-700' : 'text-purple-400'">v2.0.0 — Boss Wave Edition</div>
                    <p class="text-xs mb-6" :class="dark ? 'text-green-600' : 'text-purple-400'">
                        {{ count($users) }} {{ count($users) === 1 ? 'usuário' : 'usuários' }} no sistema
                    </p>
                    @auth
                        <button @click="startGame()"
                            class="border px-8 py-2 text-sm transition blink"
                            :class="dark ? 'border-green-500 text-green-400 hover:bg-green-500/20' : 'border-purple-500 text-purple-600 hover:bg-purple-500/20'">
                            [ INICIAR JOGO ]
                        </button>
                    @else
                        <a href="{{ route('login') }}"
                            class="border border-yellow-600 text-yellow-500 px-8 py-2 text-sm hover:bg-yellow-500/20 transition">
                            [ FAÇA LOGIN PARA JOGAR ]
                        </a>
                    @endauth
                    <div class="mt-6 text-center text-xs" :class="dark ? 'text-green-800' : 'text-purple-400'">
                        <p>← → ou A/D para mover</p>
                        <p>ESPAÇO para atirar</p>
                    </div>
                </div>

                {{-- OVERLAY: BOSS ALERT --}}
                <div x-show="state === 'boss_alert'"
                    class="absolute inset-0 flex flex-col items-center justify-center bg-black/80 rounded">
                    <div class="text-yellow-400 text-xl mb-1 blink">⚠ BOSS WAVE ⚠</div>
                    <div class="text-3xl font-bold mb-2"
                        :class="dark ? 'text-pink-400' : 'text-pink-600'"
                        x-text="currentBossUser?.name?.substring(0,10).toUpperCase() ?? '???'"></div>
                    <div class="text-xs" :class="dark ? 'text-green-700' : 'text-purple-500'">prepare-se...</div>
                </div>

                {{-- OVERLAY: VICTORY --}}
                <div x-show="state === 'victory'"
                    class="absolute inset-0 flex flex-col items-center justify-center bg-black/70 rounded">
                    <div class="text-3xl font-bold mb-2"
                        :class="dark ? 'text-green-400' : 'text-purple-600'">VITÓRIA!</div>
                    <p class="text-lg mb-1" :class="dark ? 'text-green-300' : 'text-purple-500'">
                        Score: <span x-text="score.toString().padStart(6,'0')">000000</span>
                    </p>
                    <p class="text-xs mb-6" :class="dark ? 'text-green-700' : 'text-purple-400'">
                        Wave <span x-text="wave - 1">1</span> completa
                    </p>
                    <button @click="nextWave()"
                        class="border px-8 py-2 text-sm transition"
                        :class="dark ? 'border-green-500 text-green-400 hover:bg-green-500/20' : 'border-purple-500 text-purple-600 hover:bg-purple-500/20'">
                        [ PRÓXIMA ONDA ]
                    </button>
                </div>

                {{-- OVERLAY: GAME OVER --}}
                <div x-show="state === 'gameover'"
                    class="absolute inset-0 flex flex-col items-center justify-center bg-black/70 rounded">
                    <div class="text-red-500 text-3xl font-bold mb-2"
                        style="text-shadow: 0 0 20px #ef4444">GAME OVER</div>
                    <p class="text-lg mb-1" :class="dark ? 'text-green-300' : 'text-purple-500'">
                        Score: <span x-text="score.toString().padStart(6,'0')">000000</span>
                    </p>
                    <p class="text-xs mb-6" :class="dark ? 'text-green-700' : 'text-purple-400'">
                        Chegou até Wave <span x-text="wave">1</span>
                    </p>
                    <button @click="startGame()"
                        class="border px-8 py-2 text-sm transition"
                        :class="dark ? 'border-green-500 text-green-400 hover:bg-green-500/20' : 'border-purple-500 text-purple-600 hover:bg-purple-500/20'">
                        [ JOGAR NOVAMENTE ]
                    </button>
                </div>
            </div>

            {{-- Mobile controls --}}
            <div class="flex justify-center gap-4 mt-3 md:hidden">
                <button class="border px-6 py-3 text-lg select-none"
                    :class="dark ? 'border-green-500/30 text-green-400' : 'border-purple-500/30 text-purple-600'"
                    @touchstart.prevent="touchLeftStart()" @touchend.prevent="touchLeftEnd()">◀</button>
                <button class="border px-6 py-3 text-sm select-none"
                    :class="dark ? 'border-orange-500/30 text-orange-400' : 'border-orange-500/30 text-orange-600'"
                    @touchstart.prevent="touchShoot()">ATIRAR</button>
                <button class="border px-6 py-3 text-lg select-none"
                    :class="dark ? 'border-green-500/30 text-green-400' : 'border-purple-500/30 text-purple-600'"
                    @touchstart.prevent="touchRightStart()" @touchend.prevent="touchRightEnd()">▶</button>
            </div>

            {{-- Link voltar --}}
            <div class="mt-4 text-center">
                <a href="{{ route('app.home') }}"
                    class="text-xs transition"
                    :class="dark ? 'text-green-600 hover:text-green-400' : 'text-purple-400 hover:text-purple-600'">
                    ← voltar ao início
                </a>
                <span class="mx-2 text-xs" :class="dark ? 'text-green-800' : 'text-purple-300'">|</span>
                <a href="{{ route('app.game') }}"
                    class="text-xs transition"
                    :class="dark ? 'text-green-600 hover:text-green-400' : 'text-purple-400 hover:text-purple-600'">
                    jogar v1 →
                </a>
            </div>
        </div>
    </div>

    @livewireScripts
    @vite('resources/js/game2.js')
</body>

</html>
