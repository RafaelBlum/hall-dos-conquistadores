<section id="about" class="game-section">
    <div class="stars-bg"></div>
    <div class="nebula-glow"></div>

    {{-- Retro Spaceship 1 --}}
    <div class="retro-ship ship-1">
        <div class="ship-body">
            <div class="ship-row">
                <div class="px c1"></div>
                <div class="px c2"></div>
                <div class="px c2"></div>
                <div class="px c1"></div>
            </div>
            <div class="ship-row">
                <div class="px c3"></div>
                <div class="px c4"></div>
                <div class="px c4"></div>
                <div class="px c4"></div>
                <div class="px c3"></div>
            </div>
            <div class="ship-row">
                <div class="px c5"></div>
                <div class="px c4"></div>
                <div class="px c4"></div>
                <div class="px c4"></div>
                <div class="px c4"></div>
                <div class="px c4"></div>
                <div class="px c5"></div>
            </div>
            <div class="ship-row">
                <div class="px c3"></div>
                <div class="px c4"></div>
                <div class="px c4"></div>
                <div class="px c4"></div>
                <div class="px c3"></div>
            </div>
            <div class="ship-row">
                <div class="px c1"></div>
                <div class="px c2"></div>
                <div class="px c2"></div>
                <div class="px c1"></div>
            </div>
        </div>
        <div class="ship-engine">
            <div class="flame flame-1"></div>
            <div class="flame flame-2"></div>
            <div class="flame flame-3"></div>
        </div>
    </div>

    {{-- Retro Spaceship 2 --}}
    <div class="retro-ship ship-2">
        <div class="ship-body">
            <div class="ship-row">
                <div class="px c1"></div>
                <div class="px c2"></div>
                <div class="px c2"></div>
                <div class="px c1"></div>
            </div>
            <div class="ship-row">
                <div class="px c3"></div>
                <div class="px c4"></div>
                <div class="px c4"></div>
                <div class="px c4"></div>
                <div class="px c3"></div>
            </div>
            <div class="ship-row">
                <div class="px c5"></div>
                <div class="px c4"></div>
                <div class="px c4"></div>
                <div class="px c4"></div>
                <div class="px c4"></div>
                <div class="px c4"></div>
                <div class="px c5"></div>
            </div>
            <div class="ship-row">
                <div class="px c3"></div>
                <div class="px c4"></div>
                <div class="px c4"></div>
                <div class="px c4"></div>
                <div class="px c3"></div>
            </div>
            <div class="ship-row">
                <div class="px c1"></div>
                <div class="px c2"></div>
                <div class="px c2"></div>
                <div class="px c1"></div>
            </div>
        </div>
        <div class="ship-engine">
            <div class="flame flame-1"></div>
            <div class="flame flame-2"></div>
            <div class="flame flame-3"></div>
        </div>
    </div>

    {{-- Projectiles from ships toward button --}}
    <div class="projectile p1"></div>
    <div class="projectile p2"></div>
    <div class="projectile p3"></div>
    <div class="projectile p4"></div>
    <div class="projectile p5"></div>
    <div class="projectile p6"></div>

    <div class="about-content">
        <div class="about-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2L2 7l10 5 10-5-10-5z" />
                <path d="M2 17l10 5 10-5" />
                <path d="M2 12l10 5 10-5" />
            </svg>
        </div>
        <h2 class="font-pixel">Objetivo da Comunidade</h2>
        <p class="about-subtitle">Faça parte dessa comunidade</p>

        <p id="text-sm" class="text-xs text-gray-400 leading-relaxed max-w-2xl mx-auto">
            Nosso objetivo é fomentar desafios coletivos e a busca pela "masterização", transformando as
            experiências individuais — de glórias e persistência — em uma jornada comunitária.
            Aqui, conectamos os inscritos aos seus canais favoritos em um ambiente imersivo,
            interativo e totalmente dedicado à cultura dos jogos retro.
        </p>

        <a href="{{ route('app.game') }}" class="game-btn">
            <span class="game-btn-pulse"></span>
            <span class="impact-flash"></span>
            <span class="game-btn-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="5 3 19 12 5 21 5 3" />
                </svg>
            </span>
            Jogar agora
        </a>

        <a href="{{ route('app.game2') }}" class="v2-btn block p-10 px-4 py-1.5 rounded-full text-xs font-semibold
                   bg-pink-500/10 border border-pink-500/30 text-pink-400 hover:bg-pink-500/20 transition">
            ✦ User Invaders v2 — Boss Wave Edition
        </a>
    </div>

</section>