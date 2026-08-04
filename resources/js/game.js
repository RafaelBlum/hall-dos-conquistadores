document.addEventListener('alpine:init', () => {
    Alpine.data('spaceInvaders', (users) => ({
        // ─── Estado Alpine ───────────────────────────────────────────
        users,
        state: 'idle',   // 'idle' | 'playing' | 'gameover' | 'victory'
        score: 0,
        lives: 3,
        wave: 1,
        keys: { left: false, right: false, space: false },

        // ─── Internals do jogo ───────────────────────────────────────
        ctx: null,
        canvas: null,
        animFrame: null,
        lastTime: 0,
        audioCtx: null,

        // Entidades
        player: null,
        bullets: [],        // { x, y, speed }
        enemies: [],        // { x, y, letter, color, alive, dx, dy }
        enemyBullets: [],   // { x, y }
        barriers: [],       // { x, y, health }
        particles: [],      // efeitos de explosão

        // Cadência de tiro inimigo
        enemyShootTimer: 0,
        enemyShootInterval: 1800, // ms
        playerShootCooldown: 0,
        playerShootDelay: 350,

        // Movimento dos inimigos
        enemyDir: 1,
        enemySpeed: 40,    // px/s
        enemyDropped: false,

        // ─── Init ────────────────────────────────────────────────────
        init() {
            this.canvas = this.$refs.canvas;
            this.ctx = this.canvas.getContext('2d');
            this.drawIdleScreen();
            this.setupKeyboard();
        },

        // Inicializa o AudioContext na primeira interação do usuário
        initAudio() {
            if (this.audioCtx) return;
            this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        },

        // ─── Sons (Web Audio API — síntese pura, sem arquivos) ───────

        // Tiro do jogador: pulso curto agudo
        soundPlayerShoot() {
            if (!this.audioCtx) return;
            const ac = this.audioCtx;
            const osc = ac.createOscillator();
            const gain = ac.createGain();
            osc.connect(gain);
            gain.connect(ac.destination);
            osc.type = 'square';
            osc.frequency.setValueAtTime(880, ac.currentTime);
            osc.frequency.exponentialRampToValueAtTime(220, ac.currentTime + 0.08);
            gain.gain.setValueAtTime(0.18, ac.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ac.currentTime + 0.1);
            osc.start(ac.currentTime);
            osc.stop(ac.currentTime + 0.1);
        },

        // Tiro inimigo: tom mais grave e sinistro
        soundEnemyShoot() {
            if (!this.audioCtx) return;
            const ac = this.audioCtx;
            const osc = ac.createOscillator();
            const gain = ac.createGain();
            osc.connect(gain);
            gain.connect(ac.destination);
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(180, ac.currentTime);
            osc.frequency.exponentialRampToValueAtTime(80, ac.currentTime + 0.12);
            gain.gain.setValueAtTime(0.12, ac.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ac.currentTime + 0.13);
            osc.start(ac.currentTime);
            osc.stop(ac.currentTime + 0.13);
        },

        // Explosão de inimigo: ruído com envelope
        soundEnemyDie() {
            if (!this.audioCtx) return;
            const ac = this.audioCtx;
            const bufSize = ac.sampleRate * 0.22;
            const buf = ac.createBuffer(1, bufSize, ac.sampleRate);
            const data = buf.getChannelData(0);
            for (let i = 0; i < bufSize; i++) data[i] = Math.random() * 2 - 1;
            const src = ac.createBufferSource();
            src.buffer = buf;
            const gain = ac.createGain();
            const filter = ac.createBiquadFilter();
            filter.type = 'bandpass';
            filter.frequency.value = 400;
            filter.Q.value = 0.8;
            src.connect(filter);
            filter.connect(gain);
            gain.connect(ac.destination);
            gain.gain.setValueAtTime(0.35, ac.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ac.currentTime + 0.22);
            src.start(ac.currentTime);
            src.stop(ac.currentTime + 0.22);
        },

        // Jogador levou hit: buzz grave
        soundPlayerHit() {
            if (!this.audioCtx) return;
            const ac = this.audioCtx;
            const osc = ac.createOscillator();
            const gain = ac.createGain();
            osc.connect(gain);
            gain.connect(ac.destination);
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(120, ac.currentTime);
            osc.frequency.exponentialRampToValueAtTime(40, ac.currentTime + 0.3);
            gain.gain.setValueAtTime(0.25, ac.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ac.currentTime + 0.3);
            osc.start(ac.currentTime);
            osc.stop(ac.currentTime + 0.3);
        },

        // Vitória: jingle ascendente
        soundVictory() {
            if (!this.audioCtx) return;
            const ac = this.audioCtx;
            const notes = [523, 659, 784, 1047]; // C5 E5 G5 C6
            notes.forEach((freq, i) => {
                const osc = ac.createOscillator();
                const gain = ac.createGain();
                osc.connect(gain);
                gain.connect(ac.destination);
                osc.type = 'triangle';
                osc.frequency.value = freq;
                const t = ac.currentTime + i * 0.13;
                gain.gain.setValueAtTime(0, t);
                gain.gain.linearRampToValueAtTime(0.2, t + 0.04);
                gain.gain.exponentialRampToValueAtTime(0.001, t + 0.25);
                osc.start(t);
                osc.stop(t + 0.26);
            });
        },

        // Game Over: jingle descendente
        soundGameOver() {
            if (!this.audioCtx) return;
            const ac = this.audioCtx;
            const notes = [392, 330, 262, 196]; // G4 E4 C4 G3
            notes.forEach((freq, i) => {
                const osc = ac.createOscillator();
                const gain = ac.createGain();
                osc.connect(gain);
                gain.connect(ac.destination);
                osc.type = 'sawtooth';
                osc.frequency.value = freq;
                const t = ac.currentTime + i * 0.18;
                gain.gain.setValueAtTime(0, t);
                gain.gain.linearRampToValueAtTime(0.18, t + 0.05);
                gain.gain.exponentialRampToValueAtTime(0.001, t + 0.35);
                osc.start(t);
                osc.stop(t + 0.36);
            });
        },

        setupKeyboard() {
            window.addEventListener('keydown', (e) => {
                if (e.code === 'ArrowLeft') this.keys.left = true;
                if (e.code === 'ArrowRight') this.keys.right = true;
                if (e.code === 'Space') {
                    e.preventDefault();
                    if (this.state === 'idle' || this.state === 'gameover') {
                        this.startGame();
                    } else if (this.state === 'victory') {
                        this.nextWave();
                    } else {
                        this.shoot();
                    }
                }
            });
            window.addEventListener('keyup', (e) => {
                if (e.code === 'ArrowLeft') this.keys.left = false;
                if (e.code === 'ArrowRight') this.keys.right = false;
            });
        },

        // ─── Game Setup ──────────────────────────────────────────────
        startGame() {
            this.initAudio();
            // Reset completo (novo jogo do zero)
            this.score = 0;
            this.lives = 3;
            this.wave = 1;
            this.enemySpeed = 40;
            this._launchWave();
        },

        nextWave() {
            this.initAudio();
            // Próxima onda: mantém score, lives e wave (já incrementados em endGame)
            this.enemySpeed = 40 + (this.wave - 1) * 10;
            this._launchWave();
        },

        _launchWave() {
            if (this.animFrame) cancelAnimationFrame(this.animFrame);
            this.animFrame = null;

            const W = this.canvas.width;
            const H = this.canvas.height;

            this.bullets = [];
            this.enemyBullets = [];
            this.particles = [];
            this.enemyDir = 1;
            this.enemyShootTimer = 0;
            this.playerShootCooldown = 0;

            // Player
            this.player = { x: W / 2, y: H - 50, w: 36, h: 20, speed: 220 };

            // Barriers (4 bunkers)
            this.barriers = [];
            const bPositions = [0.18, 0.38, 0.62, 0.82];
            for (const bx of bPositions) {
                for (let bi = 0; bi < 8; bi++) {
                    const col = bi % 4;
                    const row = Math.floor(bi / 4);
                    this.barriers.push({
                        x: bx * W - 24 + col * 12,
                        y: H - 100 + row * 12,
                        health: 3
                    });
                }
            }

            this.spawnEnemies();
            this.state = 'playing';
            this.lastTime = performance.now();
            this.loop(this.lastTime);
        },

        spawnEnemies() {
            const W = this.canvas.width;
            this.enemies = [];
            this.enemyDir = 1;
            this.enemyDropped = false;

            // Usa os usuários reais; se houver mais de 40, pega apenas os 40 primeiros
            const pool = this.users.length > 0 ? this.users : [{ name: 'Guest' }];
            const cols = Math.min(10, pool.length);
            const rows = Math.ceil(Math.min(pool.length, 40) / cols);

            const colors = ['#4ade80', '#34d399', '#a3e635', '#22d3ee', '#60a5fa', '#f472b6', '#fb923c', '#facc15'];

            let idx = 0;
            for (let r = 0; r < rows; r++) {
                for (let c = 0; c < cols && idx < pool.length && idx < 40; c++, idx++) {
                    this.enemies.push({
                        x: 60 + c * 54,
                        y: 60 + r * 52,
                        w: 36, h: 36,
                        letter: pool[idx].name.charAt(0).toUpperCase(),
                        color: colors[(r * cols + c) % colors.length],
                        name: pool[idx].name,
                        alive: true,
                        animOffset: Math.random() * Math.PI * 2,
                    });
                }
            }
        },

        // ─── Game Loop ───────────────────────────────────────────────
        loop(timestamp) {
            const dt = Math.min((timestamp - this.lastTime) / 1000, 0.05); // cap 50ms
            this.lastTime = timestamp;

            if (this.state === 'playing') {
                this.update(dt, timestamp);
                this.draw(timestamp);
                this.animFrame = requestAnimationFrame(t => this.loop(t));
            }
        },

        // ─── Update ──────────────────────────────────────────────────
        update(dt, now) {
            const W = this.canvas.width;
            const H = this.canvas.height;

            // Player movement
            if (this.keys.left) this.player.x = Math.max(this.player.w / 2, this.player.x - this.player.speed * dt);
            if (this.keys.right) this.player.x = Math.min(W - this.player.w / 2, this.player.x + this.player.speed * dt);

            // Player shoot cooldown
            if (this.playerShootCooldown > 0) this.playerShootCooldown -= dt * 1000;

            // Player bullets
            this.bullets = this.bullets.filter(b => {
                b.y -= 480 * dt;
                return b.y > 0;
            });

            // Enemy movement
            let hitWall = false;
            const alive = this.enemies.filter(e => e.alive);
            for (const e of alive) {
                e.x += this.enemyDir * (this.enemySpeed + this.wave * 8) * dt;
                if (e.x + e.w / 2 >= W - 10 || e.x - e.w / 2 <= 10) hitWall = true;
            }
            if (hitWall) {
                this.enemyDir *= -1;
                for (const e of alive) e.y += 18;
            }

            // Enemy shoot
            this.enemyShootTimer += dt * 1000;
            if (this.enemyShootTimer >= this.enemyShootInterval && alive.length > 0) {
                this.enemyShootTimer = 0;
                const shooter = alive[Math.floor(Math.random() * alive.length)];
                this.enemyBullets.push({ x: shooter.x, y: shooter.y + 20 });
                this.soundEnemyShoot();
                // Wave progresso: atiram mais rápido
                this.enemyShootInterval = Math.max(400, 1800 - (this.wave - 1) * 200 - (40 - alive.length) * 18);
            }

            // Enemy bullets
            this.enemyBullets = this.enemyBullets.filter(b => {
                b.y += 240 * dt;
                return b.y < H;
            });

            // Particles
            this.particles = this.particles.filter(p => {
                p.x += p.vx * dt;
                p.y += p.vy * dt;
                p.life -= dt;
                p.vy += 80 * dt;
                return p.life > 0;
            });

            // ── Colisões ──
            // Bullet × Enemy
            for (const b of this.bullets) {
                for (const e of this.enemies) {
                    if (!e.alive) continue;
                    if (Math.abs(b.x - e.x) < e.w / 2 + 3 && Math.abs(b.y - e.y) < e.h / 2 + 3) {
                        e.alive = false;
                        b.y = -999; // remove bala
                        this.score += 10 * this.wave;
                        this.spawnExplosion(e.x, e.y, e.color);
                        this.soundEnemyDie();
                    }
                }
            }

            // Bullet × Barrier
            for (const b of this.bullets) {
                for (const bar of this.barriers) {
                    if (bar.health <= 0) continue;
                    if (b.x >= bar.x && b.x <= bar.x + 10 && b.y >= bar.y && b.y <= bar.y + 10) {
                        bar.health--;
                        b.y = -999;
                    }
                }
            }

            // EnemyBullet × Player
            for (const b of this.enemyBullets) {
                if (Math.abs(b.x - this.player.x) < this.player.w / 2 + 4 &&
                    Math.abs(b.y - this.player.y) < this.player.h / 2 + 4) {
                    b.y = H + 1;
                    this.lives--;
                    this.spawnExplosion(this.player.x, this.player.y, '#4ade80');
                    this.soundPlayerHit();
                    if (this.lives <= 0) {
                        this.endGame('gameover');
                        return;
                    }
                }
            }

            // EnemyBullet × Barrier
            for (const b of this.enemyBullets) {
                for (const bar of this.barriers) {
                    if (bar.health <= 0) continue;
                    if (b.x >= bar.x && b.x <= bar.x + 10 && b.y >= bar.y && b.y <= bar.y + 10) {
                        bar.health--;
                        b.y = H + 1;
                    }
                }
            }

            // Enemy alcançou base
            for (const e of this.enemies) {
                if (e.alive && e.y + e.h / 2 >= H - 60) {
                    this.endGame('gameover');
                    return;
                }
            }

            // Todos eliminados → vitória
            if (alive.length === 0) {
                this.wave++;
                this.endGame('victory');
            }
        },

        // ─── Shoot ───────────────────────────────────────────────────
        shoot() {
            if (this.state !== 'playing') return;
            if (this.playerShootCooldown > 0) return;
            this.bullets.push({ x: this.player.x, y: this.player.y - this.player.h / 2 });
            this.playerShootCooldown = this.playerShootDelay;
            this.soundPlayerShoot();
        },

        // ─── Explosão ────────────────────────────────────────────────
        spawnExplosion(x, y, color) {
            for (let i = 0; i < 12; i++) {
                const angle = (Math.PI * 2 / 12) * i + Math.random() * 0.3;
                const speed = 60 + Math.random() * 120;
                this.particles.push({
                    x, y,
                    vx: Math.cos(angle) * speed,
                    vy: Math.sin(angle) * speed - 30,
                    life: 0.4 + Math.random() * 0.3,
                    maxLife: 0.7,
                    color,
                    size: 2 + Math.random() * 3,
                });
            }
        },

        // ─── End Game ────────────────────────────────────────────────
        endGame(result) {
            this.state = result;
            if (this.animFrame) {
                cancelAnimationFrame(this.animFrame);
                this.animFrame = null;
            }
            if (result === 'victory') this.soundVictory();
            if (result === 'gameover') this.soundGameOver();
            // Faz um último draw para mostrar o estado final
            this.draw(performance.now());
        },

        // ─── Draw ────────────────────────────────────────────────────
        draw(now) {
            const W = this.canvas.width;
            const H = this.canvas.height;
            const ctx = this.ctx;

            // Background
            ctx.fillStyle = '#030712';
            ctx.fillRect(0, 0, W, H);

            // Stars
            ctx.fillStyle = 'rgba(255,255,255,0.4)';
            // usa semente fixa para estrelas consistentes
            const seed = 42;
            for (let i = 0; i < 60; i++) {
                const sx = ((seed * (i + 1) * 1234567) % W + W) % W;
                const sy = ((seed * (i + 1) * 7654321) % H + H) % H;
                const brightness = 0.2 + ((i * 37) % 10) / 10 * 0.8;
                ctx.globalAlpha = brightness * (0.5 + 0.5 * Math.sin(now / 1000 + i));
                ctx.fillRect(sx, sy, 1, 1);
            }
            ctx.globalAlpha = 1;

            // Barriers
            for (const bar of this.barriers) {
                if (bar.health <= 0) continue;
                const alpha = bar.health / 3;
                ctx.fillStyle = `rgba(74, 222, 128, ${alpha})`;
                ctx.fillRect(bar.x, bar.y, 10, 10);
            }

            // Player
            this.drawPlayer(this.player.x, this.player.y, '#4ade80');

            // Player bullets
            for (const b of this.bullets) {
                ctx.fillStyle = '#bbf7d0';
                ctx.shadowBlur = 6;
                ctx.shadowColor = '#4ade80';
                ctx.fillRect(b.x - 2, b.y - 8, 4, 10);
                ctx.shadowBlur = 0;
            }

            // Enemies
            for (const e of this.enemies) {
                if (!e.alive) continue;
                this.drawEnemy(e, now);
            }

            // Enemy bullets
            for (const b of this.enemyBullets) {
                ctx.fillStyle = '#f87171';
                ctx.shadowBlur = 6;
                ctx.shadowColor = '#ef4444';
                ctx.fillRect(b.x - 2, b.y, 4, 12);
                ctx.shadowBlur = 0;
            }

            // Particles
            for (const p of this.particles) {
                const a = p.life / (p.maxLife || 0.7);
                ctx.globalAlpha = a;
                ctx.fillStyle = p.color;
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.size * a, 0, Math.PI * 2);
                ctx.fill();
            }
            ctx.globalAlpha = 1;

            // Bottom line
            ctx.strokeStyle = 'rgba(74, 222, 128, 0.3)';
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(0, H - 42);
            ctx.lineTo(W, H - 42);
            ctx.stroke();
        },

        drawPlayer(x, y, color) {
            const ctx = this.ctx;
            ctx.fillStyle = color;
            ctx.shadowBlur = 8;
            ctx.shadowColor = color;
            // Corpo da nave (forma de seta simples)
            ctx.beginPath();
            ctx.moveTo(x, y - 12);           // topo
            ctx.lineTo(x + 18, y + 8);       // direita
            ctx.lineTo(x + 10, y + 4);
            ctx.lineTo(x + 10, y + 10);
            ctx.lineTo(x - 10, y + 10);
            ctx.lineTo(x - 10, y + 4);
            ctx.lineTo(x - 18, y + 8);       // esquerda
            ctx.closePath();
            ctx.fill();
            ctx.shadowBlur = 0;
        },

        drawEnemy(e, now) {
            const ctx = this.ctx;
            const t = now / 1000;
            const bob = Math.sin(t * 2 + e.animOffset) * 3; // animação de flutuação

            // Sombra/glow
            ctx.shadowBlur = 12;
            ctx.shadowColor = e.color;

            // Círculo base
            ctx.beginPath();
            ctx.arc(e.x, e.y + bob, 16, 0, Math.PI * 2);
            ctx.fillStyle = e.color + '33';
            ctx.fill();

            ctx.beginPath();
            ctx.arc(e.x, e.y + bob, 14, 0, Math.PI * 2);
            ctx.strokeStyle = e.color;
            ctx.lineWidth = 1.5;
            ctx.stroke();

            // Letra do usuário
            ctx.shadowBlur = 0;
            ctx.fillStyle = e.color;
            ctx.font = 'bold 14px "Share Tech Mono", monospace';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(e.letter, e.x, e.y + bob);

            // Olhinhos alienígenas (detalhes)
            ctx.fillStyle = '#030712';
            ctx.beginPath();
            ctx.arc(e.x - 5, e.y + bob - 2, 2, 0, Math.PI * 2);
            ctx.arc(e.x + 5, e.y + bob - 2, 2, 0, Math.PI * 2);
            ctx.fill();
        },

        drawIdleScreen() {
            const ctx = this.ctx;
            const W = this.canvas.width;
            const H = this.canvas.height;
            ctx.fillStyle = '#030712';
            ctx.fillRect(0, 0, W, H);
        },
    }))
})

document.addEventListener('DOMContentLoaded', () => {
    const starsContainer = document.getElementById('stars-dark');
    if (!starsContainer) return;
    for (let i = 0; i < 80; i++) {
        const star = document.createElement('div');
        star.className = 'star';
        star.style.top = Math.random() * 50 + '%';
        star.style.left = Math.random() * 100 + '%';
        star.style.animationDelay = Math.random() * 3 + 's';
        star.style.animationDuration = (2 + Math.random() * 3) + 's';
        const size = Math.random() > 0.8 ? 3 : Math.random() > 0.5 ? 2 : 1;
        star.style.width = size + 'px';
        star.style.height = size + 'px';
        starsContainer.appendChild(star);
    }
});