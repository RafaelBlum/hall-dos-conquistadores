<!DOCTYPE html>
<html lang="pt-BR" x-data="{ dark: true }" :class="dark ? 'dark' : ''">

<head>
    <meta charset="UTF-8">
    @include('components.partials.favicon')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{config('app.name')}}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/game.css'])
    @livewireStyles
</head>

<body class="min-h-screen flex flex-col items-center justify-center p-4 select-none relative"
    :class="dark ? 'bg-[#0a0a1a] text-green-400' : 'bg-[#f0e6ff] text-purple-700'">

    {{-- Synthwave Background Dark --}}
    <div x-show="dark" class="synthwave-bg">
        <div class="gradient-top"></div>
        <div class="grid-floor"></div>
        <div class="stars" id="stars-dark"></div>
    </div>

    {{-- Synthwave Background Light --}}
    <div x-show="!dark" class="synthwave-bg-light">
        <div class="gradient-top"></div>
        <div class="grid-floor"></div>
    </div>

    {{-- Content --}}
    <div class="relative z-10 w-full max-w-2xl">
        {{-- Logo + Dark/Light Toggle --}}
        <div class="flex items-center justify-center relative mb-4">
            <a href="{{ route('app.home') }}"
                class="absolute left-0 top-1/2 -translate-y-1/2 text-xs transition-colors cursor-pointer"
                :class="dark ? 'text-green-700 hover:text-green-400' : 'text-purple-400 hover:text-purple-600'">
                ◀ HOME
            </a>
            <img src="{{ asset('images/game/conqueros-invaders-logo.png') }}" alt="Intruder Users"
                class="h-20 md:h-24 object-contain drop-shadow-lg">
            <button @click="dark = !dark"
                class="absolute right-0 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full flex items-center justify-center transition-all duration-500 cursor-pointer"
                :class="dark ? 'bg-yellow-500/20 hover:bg-yellow-500/30' : 'bg-indigo-500/20 hover:bg-indigo-500/30'"
                :style="dark ? 'box-shadow: 0 0 15px 5px rgba(250,204,21,0.3)' : 'box-shadow: 0 0 15px 5px rgba(192,132,252,0.3)'">

                {{-- Sun pixel art (dark mode) --}}
                <svg x-show="dark" class="w-6 h-6 transition-all duration-500" viewBox="0 0 16 16"
                    style="image-rendering: pixelated">
                    <rect x="7" y="0" width="2" height="2" fill="#facc15" />
                    <rect x="3" y="1" width="2" height="1" fill="#fde047" />
                    <rect x="11" y="1" width="2" height="1" fill="#fde047" />
                    <rect x="5" y="3" width="6" height="2" fill="#facc15" />
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

                {{-- Moon pixel art (light mode) --}}
                <svg x-show="!dark" class="w-6 h-6 transition-all duration-500" viewBox="0 0 16 16"
                    style="image-rendering: pixelated">
                    <rect x="6" y="0" width="2" height="2" fill="#c084fc" />
                    <rect x="3" y="1" width="3" height="2" fill="#a855f7" />
                    <rect x="10" y="2" width="2" height="2" fill="#a855f7" />
                    <rect x="1" y="3" width="3" height="2" fill="#a855f7" />
                    <rect x="12" y="4" width="2" height="2" fill="#a855f7" />
                    <rect x="1" y="5" width="3" height="2" fill="#a855f7" />
                    <rect x="13" y="6" width="2" height="2" fill="#a855f7" />
                    <rect x="2" y="7" width="2" height="2" fill="#a855f7" />
                    <rect x="13" y="8" width="2" height="2" fill="#a855f7" />
                    <rect x="1" y="9" width="3" height="2" fill="#a855f7" />
                    <rect x="12" y="10" width="2" height="2" fill="#a855f7" />
                    <rect x="1" y="11" width="3" height="2" fill="#a855f7" />
                    <rect x="10" y="12" width="2" height="2" fill="#a855f7" />
                    <rect x="3" y="13" width="3" height="2" fill="#a855f7" />
                    <rect x="6" y="14" width="2" height="2" fill="#c084fc" />
                </svg>
            </button>
        </div>

        <div x-data="spaceInvaders({{ $users->map(fn($u) => ['id' => $u->id, 'name' => $u->name])->toJson() }})"
            x-init="init()" class="w-full max-w-2xl">
            {{-- Header --}}
            <div class="flex justify-between items-center mb-3 px-1">
                <div>
                    <span class="text-xs" :class="dark ? 'text-green-600' : 'text-purple-400'">SCORE</span>
                    <div class="text-2xl" :class="dark ? 'text-green-300' : 'text-purple-600'"
                        x-text="score.toString().padStart(6,'0')">000000</div>
                </div>
                <div class="text-center">
                    <div class="text-sm" :class="dark ? 'text-green-500' : 'text-purple-500'">INTRUDER USERS</div>
                    <div class="text-xs mt-0.5" :class="dark ? 'text-green-700' : 'text-purple-300'">v1.0.0</div>
                </div>
                <div class="text-right">
                    <span class="text-xs" :class="dark ? 'text-green-600' : 'text-purple-400'">VIDAS</span>
                    <div class="flex gap-1 justify-end mt-0.5">
                        <template x-for="i in 3" :key="i">
                            <svg class="w-6 h-6" viewBox="0 0 16 16" :style="i <= lives ? '' : 'opacity: 0.2'">
                                <rect x="3" y="0" width="2" height="2" :fill="dark ? '#4ade80' : '#a855f7'" />
                                <rect x="5" y="0" width="2" height="2" :fill="dark ? '#4ade80' : '#a855f7'" />
                                <rect x="9" y="0" width="2" height="2" :fill="dark ? '#4ade80' : '#a855f7'" />
                                <rect x="11" y="0" width="2" height="2" :fill="dark ? '#4ade80' : '#a855f7'" />
                                <rect x="1" y="2" width="2" height="2" :fill="dark ? '#4ade80' : '#a855f7'" />
                                <rect x="3" y="2" width="2" height="2" :fill="dark ? '#86efac' : '#c084fc'" />
                                <rect x="5" y="2" width="2" height="2" :fill="dark ? '#86efac' : '#c084fc'" />
                                <rect x="7" y="2" width="2" height="2" :fill="dark ? '#4ade80' : '#a855f7'" />
                                <rect x="9" y="2" width="2" height="2" :fill="dark ? '#86efac' : '#c084fc'" />
                                <rect x="11" y="2" width="2" height="2" :fill="dark ? '#86efac' : '#c084fc'" />
                                <rect x="13" y="2" width="2" height="2" :fill="dark ? '#4ade80' : '#a855f7'" />
                                <rect x="1" y="4" width="2" height="2" :fill="dark ? '#86efac' : '#c084fc'" />
                                <rect x="3" y="4" width="2" height="2" :fill="dark ? '#86efac' : '#c084fc'" />
                                <rect x="5" y="4" width="2" height="2" :fill="dark ? '#86efac' : '#c084fc'" />
                                <rect x="7" y="4" width="2" height="2" :fill="dark ? '#86efac' : '#c084fc'" />
                                <rect x="9" y="4" width="2" height="2" :fill="dark ? '#86efac' : '#c084fc'" />
                                <rect x="11" y="4" width="2" height="2" :fill="dark ? '#86efac' : '#c084fc'" />
                                <rect x="13" y="4" width="2" height="2" :fill="dark ? '#86efac' : '#c084fc'" />
                                <rect x="3" y="6" width="2" height="2" :fill="dark ? '#86efac' : '#c084fc'" />
                                <rect x="5" y="6" width="2" height="2" :fill="dark ? '#86efac' : '#c084fc'" />
                                <rect x="7" y="6" width="2" height="2" :fill="dark ? '#86efac' : '#c084fc'" />
                                <rect x="9" y="6" width="2" height="2" :fill="dark ? '#86efac' : '#c084fc'" />
                                <rect x="11" y="6" width="2" height="2" :fill="dark ? '#86efac' : '#c084fc'" />
                                <rect x="5" y="8" width="2" height="2" :fill="dark ? '#86efac' : '#c084fc'" />
                                <rect x="7" y="8" width="2" height="2" :fill="dark ? '#86efac' : '#c084fc'" />
                                <rect x="9" y="8" width="2" height="2" :fill="dark ? '#86efac' : '#c084fc'" />
                                <rect x="7" y="10" width="2" height="2" :fill="dark ? '#86efac' : '#c084fc'" />
                            </svg>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Game Canvas --}}
            <div class="relative scanlines rounded-lg overflow-hidden border bg-gray-900 shadow-lg"
                :class="dark ? 'border-green-900 shadow-green-900/30' : 'border-purple-300 shadow-purple-500/20'">
                <canvas x-ref="canvas" width="640" height="480" class="w-full block"></canvas>

                {{-- Overlay: Start Screen --}}
                <div x-show="state === 'idle'" x-transition:leave="transition-opacity duration-300"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900/90">
                    <div class="text-3xl text-green-300 mb-2">👾 INTRUDER USERS</div>
                    <div class="text-green-600 text-sm mb-6">
                        <span x-text="users.length"></span> usuários cadastrados
                    </div>
                    <button @click="startGame()"
                        class="border border-green-500 text-green-400 px-8 py-2 text-sm hover:bg-green-900/40 transition-colors cursor-pointer">
                        [ INICIAR JOGO ]
                    </button>
                    <div class="mt-6 text-xs text-green-800 space-y-1 text-center">
                        <div>← → MOVER &nbsp; ESPAÇO ATIRAR</div>
                        <div>Mobile: botões abaixo</div>
                    </div>
                </div>

                {{-- Overlay: Game Over --}}
                <div x-show="state === 'gameover'" x-transition:enter="transition-opacity duration-500"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900/90">
                    <div class="text-red-400 text-3xl mb-2">GAME OVER</div>
                    <div class="text-green-600 text-sm mb-1">Pontuação Final</div>
                    <div class="text-green-300 text-4xl mb-6" x-text="score.toString().padStart(6,'0')"></div>
                    <button @click="startGame()"
                        class="border border-green-500 text-green-400 px-8 py-2 text-sm hover:bg-green-900/40 transition-colors cursor-pointer">
                        [ JOGAR NOVAMENTE ]
                    </button>
                </div>

                {{-- Overlay: Victory --}}
                <div x-show="state === 'victory'" x-transition:enter="transition-opacity duration-500"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900/90">
                    <div class="text-yellow-300 text-3xl mb-2">🏆 VITÓRIA!</div>
                    <div class="text-green-600 text-sm mb-1">Todos os usuários eliminados!</div>
                    <div class="text-green-300 text-4xl mb-6" x-text="score.toString().padStart(6,'0')"></div>
                    <button @click="nextWave()"
                        class="border border-yellow-500 text-yellow-400 px-8 py-2 text-sm hover:bg-yellow-900/40 transition-colors cursor-pointer">
                        [ PRÓXIMA ONDA ]
                    </button>
                </div>
            </div>

            {{-- Mobile Controls --}}
            <div class="flex justify-between items-center mt-4 px-4 gap-4" x-show="state === 'playing'">
                <div class="flex gap-2">
                    <button @touchstart.prevent="keys.left = true" @touchend.prevent="keys.left = false"
                        @mousedown="keys.left = true" @mouseup="keys.left = false"
                        class="border w-14 h-14 text-xl rounded-lg cursor-pointer"
                        :class="dark ? 'bg-green-900/30 border-green-800 text-green-400 active:bg-green-800/40' : 'bg-purple-100 border-purple-300 text-purple-600 active:bg-purple-200'">◀</button>
                    <button @touchstart.prevent="keys.right = true" @touchend.prevent="keys.right = false"
                        @mousedown="keys.right = true" @mouseup="keys.right = false"
                        class="border w-14 h-14 text-xl rounded-lg cursor-pointer"
                        :class="dark ? 'bg-green-900/30 border-green-800 text-green-400 active:bg-green-800/40' : 'bg-purple-100 border-purple-300 text-purple-600 active:bg-purple-200'">▶</button>
                </div>
                <button @touchstart.prevent="shoot()" @click="shoot()"
                    class="border px-10 h-14 text-sm rounded-lg cursor-pointer"
                    :class="dark ? 'bg-green-900/30 border-green-500 text-green-300 active:bg-green-700/40' : 'bg-purple-100 border-purple-400 text-purple-600 active:bg-purple-200'">ATIRAR</button>
            </div>

            {{-- Wave indicator --}}
            <div class="flex justify-center mt-3 gap-1" x-show="state !== 'idle'">
                <span class="text-xs" :class="dark ? 'text-green-700' : 'text-purple-400'">ONDA</span>
                <span class="text-xs" :class="dark ? 'text-green-500' : 'text-purple-600'" x-text="wave"></span>
            </div>
        </div>

        @livewireScripts
        @vite('resources/js/game.js')
</body>

</html>