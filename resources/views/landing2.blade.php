<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ dark: true }" :class="dark ? 'dark' : ''">

<head>
    <meta charset="UTF-8">
    @include('components.partials.favicon')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/game2.css'])
    @livewireStyles

    <style>
        .center-content {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            padding: 2rem;
            perspective: 800px;
        }

        .logo-glow {
            filter: drop-shadow(0 0 40px rgba(139, 92, 246, 0.5));
            animation: logoDeepShake 6s ease-in-out infinite;
        }

        /* Logo: desce suavemente + escala de profundidade */
        @keyframes logoDeepShake {
            0%, 100% {
                filter: drop-shadow(0 0 40px rgba(139, 92, 246, 0.5));
                transform: translateY(0) scale(1);
            }
            50% {
                filter: drop-shadow(0 0 15px rgba(139, 92, 246, 0.2));
                transform: translateY(18px) scale(0.92);
            }
        }

        /* Tremor sutil na logo */
        @keyframes microShake {
            0%, 100% { transform: translate(0, 0); }
            10% { transform: translate(0.3px, -0.3px); }
            15% { transform: translate(-0.3px, 0.3px); }
            20% { transform: translate(0.2px, 0.2px); }
            30% { transform: translate(-0.2px, 0); }
            50% { transform: translate(0.4px, -0.2px); }
            70% { transform: translate(-0.1px, 0.3px); }
            80% { transform: translate(0.3px, 0.1px); }
            85% { transform: translate(-0.2px, -0.3px); }
            90% { transform: translate(0.1px, 0.2px); }
            95% { transform: translate(-0.3px, -0.1px); }
        }

        .logo-wrapper {
            animation: microShake 8s ease-in-out infinite;
        }

        /* Texto "A live já vai começar" */
        .live-text {
            margin-top: 2.5rem;
            font-family: 'Share Tech Mono', monospace;
            font-size: 1.1rem;
            letter-spacing: 4px;
            text-transform: uppercase;
            position: relative;
            z-index: 10;
            animation: liveGlow 2s ease-in-out infinite alternate;
        }

        .live-text .dot {
            animation: dotBlink 1.5s step-end infinite;
        }

        .live-text .dot:nth-child(2) {
            animation-delay: 0.5s;
        }

        .live-text .dot:nth-child(3) {
            animation-delay: 1s;
        }

        @keyframes liveGlow {
            0% {
                text-shadow: 0 0 10px rgba(139, 92, 246, 0.4);
                opacity: 0.9;
            }
            100% {
                text-shadow: 0 0 20px rgba(139, 92, 246, 0.8), 0 0 40px rgba(139, 92, 246, 0.3);
                opacity: 1;
            }
        }

        @keyframes dotBlink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center relative"
    :class="dark ? 'bg-[#0a0a1a] text-white' : 'bg-[#f0e6ff] text-purple-700'">

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
            <template x-if="dark">
                <svg class="w-6 h-6" viewBox="0 0 16 16" style="image-rendering: pixelated">
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
            </template>
            <template x-if="!dark">
                <svg class="w-6 h-6" viewBox="0 0 16 16" style="image-rendering: pixelated">
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
            </template>
        </button>
    </div>

    {{-- Center content --}}
    <div class="center-content">
        <div class="logo-wrapper">
            <img src="{{ asset('images/brandname/Hall-dos-conquistadores.png') }}"
                alt="Hall dos Conquistadores"
                class="logo-glow"
                style="max-height: 70vh; width: auto;" />
        </div>

        <div class="live-text">
            A live já vai começar<span class="dot">.</span><span class="dot">.</span><span class="dot">.</span>
        </div>
    </div>

    @livewireScripts
</body>

</html>
