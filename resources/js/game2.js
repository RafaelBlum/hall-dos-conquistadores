// resources/js/game2.js — User Invaders v2 (Alpine component)
// Stack: Alpine.js + Canvas API + Web Audio API

document.addEventListener('alpine:init', () => {
    Alpine.data('userInvadersV2', (users, authUserId, isLoggedIn) => ({
        users,
        authUserId,
        isLoggedIn,
        state: 'idle',
        score: 0,
        lives: 3,
        wave: 1,
        keys: { left: false, right: false, space: false },

        ctx: null,
        canvas: null,
        animFrame: null,
        lastTime: 0,
        audioCtx: null,
        player: null,
        bullets: [],
        enemyBullets: [],
        enemies: [],
        barriers: [],
        particles: [],
        boss: null,
        currentBossUser: null,

        enemyDir: 1,
        enemySpeed: 40,
        enemyShootTimer: 0,
        enemyShootInterval: 1800,
        playerShootCooldown: 0,
        playerShootDelay: 350,

        init() {
            this.canvas = this.$refs.canvas;
            this.ctx = this.canvas.getContext('2d');
            this.setupKeyboard();
            this._generateStarsDOM();
            this.draw(performance.now());
        },

        setupKeyboard() {
            window.addEventListener('keydown', e => {
                if (e.key === 'ArrowLeft' || e.key === 'a') this.keys.left = true;
                if (e.key === 'ArrowRight' || e.key === 'd') this.keys.right = true;
                if (e.key === ' ') { this.keys.space = true; e.preventDefault(); }
            });
            window.addEventListener('keyup', e => {
                if (e.key === 'ArrowLeft' || e.key === 'a') this.keys.left = false;
                if (e.key === 'ArrowRight' || e.key === 'd') this.keys.right = false;
                if (e.key === ' ') this.keys.space = false;
            });
        },

        touchLeftStart() { this.keys.left = true; },
        touchLeftEnd() { this.keys.left = false; },
        touchRightStart() { this.keys.right = true; },
        touchRightEnd() { this.keys.right = false; },
        touchShoot() { this.keys.space = true; setTimeout(() => this.keys.space = false, 80); },

        startGame() {
            this.initAudio();
            this.score = 0;
            this.lives = 3;
            this.wave = 1;
            this._launchWave();
        },

        nextWave() {
            this.initAudio();
            this._launchWave();
        },

        _launchWave() {
            if (this.animFrame) {
                cancelAnimationFrame(this.animFrame);
                this.animFrame = null;
            }
            const W = this.canvas.width;
            const H = this.canvas.height;

            this.bullets = [];
            this.enemyBullets = [];
            this.particles = [];
            this.enemyDir = 1;
            this.enemyShootTimer = 0;
            this.playerShootCooldown = 0;
            this.boss = null;
            this.currentBossUser = null;

            this.player = { x: W / 2, y: H - 50, w: 36, h: 20, speed: 220 };

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

            if (this.isBossWave()) this.spawnBoss();
            this.spawnEnemies();

            if (this.isBossWave()) {
                this.state = 'boss_alert';
                this.draw(performance.now());
                setTimeout(() => {
                    this.state = 'playing';
                    this.lastTime = performance.now();
                    this.loop(this.lastTime);
                }, 2200);
            } else {
                this.state = 'playing';
                this.lastTime = performance.now();
                this.loop(this.lastTime);
            }
        },

        endGame(result) {
            // ── Cancela o loop ANTES de mudar o state ──────────────────
            if (this.animFrame) {
                cancelAnimationFrame(this.animFrame);
                this.animFrame = null;
            }
            this.state = result;
            if (result === 'gameover') this.soundGameOver();
            if (result === 'victory') this.soundVictory();
        },

        // ── Game loop ─────────────────────────────────────────────────
        // CORREÇÃO: só continua se state === 'playing'
        loop(timestamp) {
            if (this.state !== 'playing') return;

            const dt = Math.min((timestamp - this.lastTime) / 1000, 0.05);
            this.lastTime = timestamp;
            this.update(dt, timestamp);

            // update() pode ter chamado endGame() e mudado state —
            // verificar novamente antes de agendar o próximo frame
            if (this.state !== 'playing') return;

            this.draw(timestamp);
            this.animFrame = requestAnimationFrame(t => this.loop(t));
        },

        update(dt, now) {
            const W = this.canvas.width;
            const H = this.canvas.height;

            if (this.keys.left)
                this.player.x = Math.max(this.player.w / 2, this.player.x - this.player.speed * dt);
            if (this.keys.right)
                this.player.x = Math.min(W - this.player.w / 2, this.player.x + this.player.speed * dt);
            if (this.playerShootCooldown > 0) this.playerShootCooldown -= dt * 1000;

            if (this.keys.space && this.playerShootCooldown <= 0) {
                this.bullets.push({ x: this.player.x, y: this.player.y - 14 });
                this.playerShootCooldown = this.playerShootDelay;
                this.soundPlayerShoot();
            }

            this.bullets = this.bullets.filter(b => { b.y -= 480 * dt; return b.y > 0; });

            let hitWall = false;
            const alive = this.enemies.filter(e => e.alive);
            const speed = this.enemySpeed + (this.wave - 1) * 10;
            for (const e of alive) {
                e.x += this.enemyDir * speed * dt;
                if (e.x + e.w / 2 >= W - 10 || e.x - e.w / 2 <= 10) hitWall = true;
            }
            if (hitWall) {
                this.enemyDir *= -1;
                for (const e of alive) e.y += 18;
            }

            if (this.boss && this.boss.alive) {
                this.boss.x += this.boss.dir * this.boss.speed * dt;
                if (this.boss.x + this.boss.w / 2 >= W - 20) this.boss.dir = -1;
                if (this.boss.x - this.boss.w / 2 <= 20) this.boss.dir = 1;
                this.boss.shootTimer += dt * 1000;
                if (this.boss.shootTimer >= this.boss.shootInterval) {
                    this.boss.shootTimer = 0;
                    for (const ox of [-12, 0, 12])
                        this.enemyBullets.push({ x: this.boss.x + ox, y: this.boss.y + this.boss.h / 2 + 4, isBoss: true });
                    this.soundEnemyShoot();
                }
                if (this.boss.hitFlash > 0) this.boss.hitFlash -= dt * 10;
            }

            this.enemyShootTimer += dt * 1000;
            const shootInterval = Math.max(400, 1800 - (this.wave - 1) * 200 - (40 - alive.length) * 18);
            if (this.enemyShootTimer >= shootInterval && alive.length > 0) {
                this.enemyShootTimer = 0;
                const shooter = alive[Math.floor(Math.random() * alive.length)];
                this.enemyBullets.push({ x: shooter.x, y: shooter.y + 20 });
                this.soundEnemyShoot();
            }

            this.enemyBullets = this.enemyBullets.filter(b => {
                b.y += (b.isBoss ? 280 : 240) * dt;
                return b.y < H;
            });

            this.particles = this.particles.filter(p => {
                p.x += p.vx * dt; p.y += p.vy * dt;
                p.life -= dt; p.vy += 80 * dt;
                return p.life > 0;
            });

            // Bullet × Boss
            if (this.boss && this.boss.alive) {
                for (const b of this.bullets) {
                    if (Math.abs(b.x - this.boss.x) < this.boss.w / 2 + 3 &&
                        Math.abs(b.y - this.boss.y) < this.boss.h / 2 + 3) {
                        b.y = -999;
                        this.boss.hp--;
                        this.boss.hitFlash = 1;
                        this.soundBossHit();
                        if (this.boss.hp <= 0) {
                            this.boss.alive = false;
                            this.score += 100 * this.wave;
                            this.spawnExplosion(this.boss.x, this.boss.y, this.boss.color, true);
                            this.soundBossDie();
                        }
                    }
                }
            }

            // Bullet × Enemy
            for (const b of this.bullets) {
                for (const e of this.enemies) {
                    if (!e.alive) continue;
                    if (Math.abs(b.x - e.x) < e.w / 2 + 3 && Math.abs(b.y - e.y) < e.h / 2 + 3) {
                        e.alive = false;
                        b.y = -999;
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
                        bar.health--; b.y = -999;
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
                    if (this.lives <= 0) { this.endGame('gameover'); return; }
                }
            }

            // EnemyBullet × Barrier
            for (const b of this.enemyBullets) {
                for (const bar of this.barriers) {
                    if (bar.health <= 0) continue;
                    if (b.x >= bar.x && b.x <= bar.x + 10 && b.y >= bar.y && b.y <= bar.y + 10) {
                        bar.health--; b.y = H + 1;
                    }
                }
            }

            // Enemy alcançou a base
            for (const e of this.enemies) {
                if (e.alive && e.y + e.h / 2 >= H - 60) { this.endGame('gameover'); return; }
            }

            // ── Condição de vitória ──────────────────────────────────────
            // Recalcula 'alive' aqui para pegar as mortes desta frame
            const allEnemiesDead = this.enemies.filter(e => e.alive).length === 0;
            const bossDead = !this.boss || !this.boss.alive;
            if (allEnemiesDead && bossDead) {
                this.wave++;
                this.endGame('victory');
            }
        },

        draw(now) {
            const W = this.canvas.width;
            const H = this.canvas.height;
            const ctx = this.ctx;

            ctx.fillStyle = '#030712';
            ctx.fillRect(0, 0, W, H);

            let seed = 42;
            const rand = () => { seed = (seed * 16807) % 2147483647; return (seed - 1) / 2147483646; };
            for (let i = 0; i < 60; i++) {
                const sx = rand() * W, sy = rand() * H, sr = rand() * 1.5 + 0.5;
                const twinkle = Math.sin(now / 1000 * 3 + i) * 0.4 + 0.6;
                ctx.globalAlpha = twinkle;
                ctx.fillStyle = '#ffffff';
                ctx.beginPath();
                ctx.arc(sx, sy, sr, 0, Math.PI * 2);
                ctx.fill();
            }
            ctx.globalAlpha = 1;

            for (const bar of this.barriers) {
                if (bar.health <= 0) continue;
                ctx.fillStyle = `rgba(74,222,128,${bar.health / 3})`;
                ctx.fillRect(bar.x, bar.y, 10, 10);
            }

            if (this.player) this.drawPlayer(this.player.x, this.player.y, '#4ade80');

            for (const b of this.bullets) {
                ctx.fillStyle = '#bbf7d0';
                ctx.shadowBlur = 6; ctx.shadowColor = '#4ade80';
                ctx.fillRect(b.x - 2, b.y - 8, 4, 10);
                ctx.shadowBlur = 0;
            }

            if (this.boss && this.boss.alive) this.drawBoss(this.boss, now);

            for (const e of this.enemies) {
                if (!e.alive) continue;
                this.drawEnemy(e, now);
            }

            for (const b of this.enemyBullets) {
                ctx.fillStyle = b.isBoss ? '#fb923c' : '#f87171';
                ctx.shadowBlur = 6;
                ctx.shadowColor = b.isBoss ? '#f97316' : '#ef4444';
                ctx.fillRect(b.x - 2, b.y, 4, 12);
                ctx.shadowBlur = 0;
            }

            for (const p of this.particles) {
                ctx.globalAlpha = p.life / (p.maxLife || 0.7);
                ctx.fillStyle = p.color;
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.size * (p.life / p.maxLife), 0, Math.PI * 2);
                ctx.fill();
            }
            ctx.globalAlpha = 1;

            ctx.strokeStyle = 'rgba(74,222,128,0.3)';
            ctx.lineWidth = 1;
            ctx.beginPath(); ctx.moveTo(0, H - 42); ctx.lineTo(W, H - 42); ctx.stroke();
        },

        drawPlayer(x, y, color) {
            const ctx = this.ctx;
            ctx.fillStyle = color;
            ctx.shadowBlur = 8; ctx.shadowColor = color;
            ctx.beginPath();
            ctx.moveTo(x, y - 12);
            ctx.lineTo(x + 18, y + 8);
            ctx.lineTo(x + 10, y + 4);
            ctx.lineTo(x + 10, y + 10);
            ctx.lineTo(x - 10, y + 10);
            ctx.lineTo(x - 10, y + 4);
            ctx.lineTo(x - 18, y + 8);
            ctx.closePath();
            ctx.fill();
            ctx.shadowBlur = 0;
        },

        drawEnemy(e, now) {
            const ctx = this.ctx;
            const bob = Math.sin(now / 1000 * 2 + e.animOffset) * 3;
            ctx.shadowBlur = 12; ctx.shadowColor = e.color;
            ctx.beginPath();
            ctx.arc(e.x, e.y + bob, 16, 0, Math.PI * 2);
            ctx.fillStyle = e.color + '33'; ctx.fill();
            ctx.beginPath();
            ctx.arc(e.x, e.y + bob, 14, 0, Math.PI * 2);
            ctx.strokeStyle = e.color; ctx.lineWidth = 1.5; ctx.stroke();
            ctx.shadowBlur = 0;
            ctx.fillStyle = e.color;
            ctx.font = 'bold 14px "Share Tech Mono", monospace';
            ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
            ctx.fillText(e.letter, e.x, e.y + bob);
            ctx.fillStyle = '#030712';
            ctx.beginPath();
            ctx.arc(e.x - 5, e.y + bob - 2, 2, 0, Math.PI * 2);
            ctx.arc(e.x + 5, e.y + bob - 2, 2, 0, Math.PI * 2);
            ctx.fill();
        },

        drawBoss(boss, now) {
            const ctx = this.ctx;
            const S = 6, cols = 13, rows = 9;
            const ox = boss.x - (cols * S) / 2;
            const oy = boss.y - (rows * S) / 2;
            if (boss.hitFlash > 0 && Math.floor(boss.hitFlash * 8) % 2 === 0) ctx.globalAlpha = 0.3;
            const sprite = [
                [0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0],
                [0, 0, 0, 1, 0, 0, 0, 0, 0, 1, 0, 0, 0],
                [0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0],
                [0, 1, 1, 2, 1, 1, 2, 1, 1, 2, 1, 1, 0],
                [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
                [1, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1],
                [1, 0, 1, 0, 0, 1, 0, 1, 0, 0, 1, 0, 1],
                [0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 0, 0, 0],
                [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            ];
            const dark = boss.color + 'aa';
            for (let r = 0; r < sprite.length; r++) {
                for (let c = 0; c < sprite[r].length; c++) {
                    const v = sprite[r][c];
                    if (v === 0) continue;
                    ctx.fillStyle = v === 1 ? boss.color : dark;
                    ctx.shadowBlur = v === 1 ? 8 : 0;
                    ctx.shadowColor = boss.color;
                    ctx.fillRect(ox + c * S, oy + r * S, S, S);
                }
            }
            ctx.shadowBlur = 0; ctx.globalAlpha = 1;
            ctx.fillStyle = boss.color;
            ctx.font = 'bold 11px "Share Tech Mono", monospace';
            ctx.textAlign = 'center'; ctx.textBaseline = 'top';
            ctx.fillText(boss.name, boss.x, oy + rows * S + 4);
            const barW = cols * S, barH = 5, barX = ox, barY = oy - 12;
            const hpRatio = boss.hp / boss.maxHp;
            ctx.fillStyle = '#1f2937'; ctx.fillRect(barX, barY, barW, barH);
            ctx.fillStyle = hpRatio > 0.5 ? '#4ade80' : hpRatio > 0.25 ? '#facc15' : '#ef4444';
            ctx.fillRect(barX, barY, barW * hpRatio, barH);
            ctx.strokeStyle = '#374151'; ctx.lineWidth = 1;
            ctx.strokeRect(barX, barY, barW, barH);
            ctx.fillStyle = '#9ca3af';
            ctx.font = '9px "Share Tech Mono", monospace';
            ctx.textAlign = 'left'; ctx.textBaseline = 'bottom';
            ctx.fillText(`HP ${boss.hp}/${boss.maxHp}`, barX, barY - 1);
        },

        isBossWave() {
            return this.wave % 3 === 1 && this.wave > 1;
        },

        pickBossUser() {
            const pool = this.users.filter(u => u.id !== this.authUserId);
            if (!pool.length) return this.users[0];
            return pool[Math.floor(Math.random() * pool.length)];
        },

        spawnBoss() {
            const W = this.canvas.width;
            const bossUser = this.pickBossUser();
            this.currentBossUser = bossUser;
            this.boss = {
                x: W / 2, y: 70, w: 80, h: 56,
                hp: 5 + this.wave * 2,
                maxHp: 5 + this.wave * 2,
                name: bossUser.name.substring(0, 10).toUpperCase(),
                color: '#f472b6',
                alive: true, dir: 1,
                speed: 60 + this.wave * 5,
                shootTimer: 0,
                shootInterval: Math.max(600, 1200 - this.wave * 60),
                hitFlash: 0,
            };
            this.soundBossAppear();
        },

        spawnEnemies() {
            const W = this.canvas.width;
            this.enemies = [];
            this.enemyDir = 1;
            const pool = this.users.length > 0 ? this.users : [{ name: 'Guest' }];
            const cols = Math.min(10, pool.length);
            const rows = Math.ceil(Math.min(pool.length, 40) / cols);
            const yOffset = this.isBossWave() ? 80 : 0;
            const colors = ['#4ade80', '#34d399', '#a3e635', '#22d3ee',
                '#60a5fa', '#f472b6', '#fb923c', '#facc15'];
            let idx = 0;
            for (let r = 0; r < rows; r++) {
                for (let c = 0; c < cols && idx < pool.length && idx < 40; c++, idx++) {
                    this.enemies.push({
                        x: 60 + c * 54, y: 80 + yOffset + r * 52,
                        w: 36, h: 36,
                        letter: pool[idx].name.charAt(0).toUpperCase(),
                        color: colors[(r * cols + c) % colors.length],
                        name: pool[idx].name, alive: true,
                        animOffset: Math.random() * Math.PI * 2,
                    });
                }
            }
        },

        spawnExplosion(x, y, color, big = false) {
            const count = big ? 24 : 12;
            for (let i = 0; i < count; i++) {
                const angle = (Math.PI * 2 / count) * i + Math.random() * 0.3;
                const speed = (big ? 100 : 60) + Math.random() * (big ? 180 : 120);
                this.particles.push({
                    x, y,
                    vx: Math.cos(angle) * speed,
                    vy: Math.sin(angle) * speed - (big ? 50 : 30),
                    life: 0.4 + Math.random() * (big ? 0.6 : 0.3),
                    maxLife: big ? 1.0 : 0.7,
                    color,
                    size: (big ? 3 : 2) + Math.random() * 3,
                });
            }
        },

        // ── Áudio ─────────────────────────────────────────────────────
        initAudio() {
            if (this.audioCtx) return;
            this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        },

        soundPlayerShoot() {
            if (!this.audioCtx) return;
            const ac = this.audioCtx, osc = ac.createOscillator(), gain = ac.createGain();
            osc.connect(gain); gain.connect(ac.destination);
            osc.type = 'square';
            osc.frequency.setValueAtTime(880, ac.currentTime);
            osc.frequency.exponentialRampToValueAtTime(220, ac.currentTime + 0.08);
            gain.gain.setValueAtTime(0.18, ac.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ac.currentTime + 0.1);
            osc.start(ac.currentTime); osc.stop(ac.currentTime + 0.1);
        },

        soundEnemyShoot() {
            if (!this.audioCtx) return;
            const ac = this.audioCtx, osc = ac.createOscillator(), gain = ac.createGain();
            osc.connect(gain); gain.connect(ac.destination);
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(180, ac.currentTime);
            osc.frequency.exponentialRampToValueAtTime(80, ac.currentTime + 0.12);
            gain.gain.setValueAtTime(0.12, ac.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ac.currentTime + 0.13);
            osc.start(ac.currentTime); osc.stop(ac.currentTime + 0.13);
        },

        soundEnemyDie() {
            if (!this.audioCtx) return;
            const ac = this.audioCtx, bufSize = ac.sampleRate * 0.22;
            const buf = ac.createBuffer(1, bufSize, ac.sampleRate);
            const data = buf.getChannelData(0);
            for (let i = 0; i < bufSize; i++) data[i] = Math.random() * 2 - 1;
            const src = ac.createBufferSource(); src.buffer = buf;
            const gain = ac.createGain(), filter = ac.createBiquadFilter();
            filter.type = 'bandpass'; filter.frequency.value = 400; filter.Q.value = 0.8;
            src.connect(filter); filter.connect(gain); gain.connect(ac.destination);
            gain.gain.setValueAtTime(0.35, ac.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ac.currentTime + 0.22);
            src.start(ac.currentTime); src.stop(ac.currentTime + 0.22);
        },

        soundPlayerHit() {
            if (!this.audioCtx) return;
            const ac = this.audioCtx, osc = ac.createOscillator(), gain = ac.createGain();
            osc.connect(gain); gain.connect(ac.destination);
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(120, ac.currentTime);
            osc.frequency.exponentialRampToValueAtTime(40, ac.currentTime + 0.3);
            gain.gain.setValueAtTime(0.25, ac.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ac.currentTime + 0.3);
            osc.start(ac.currentTime); osc.stop(ac.currentTime + 0.3);
        },

        soundBossHit() {
            if (!this.audioCtx) return;
            const ac = this.audioCtx;
            [260, 267].forEach(freq => {
                const osc = ac.createOscillator(), gain = ac.createGain();
                osc.connect(gain); gain.connect(ac.destination);
                osc.type = 'square'; osc.frequency.value = freq;
                gain.gain.setValueAtTime(0.15, ac.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ac.currentTime + 0.18);
                osc.start(ac.currentTime); osc.stop(ac.currentTime + 0.18);
            });
        },

        soundBossDie() {
            if (!this.audioCtx) return;
            const ac = this.audioCtx, bufSize = ac.sampleRate * 0.8;
            const buf = ac.createBuffer(1, bufSize, ac.sampleRate);
            const data = buf.getChannelData(0);
            for (let i = 0; i < bufSize; i++) data[i] = Math.random() * 2 - 1;
            const src = ac.createBufferSource(); src.buffer = buf;
            const noiseGain = ac.createGain(), filter = ac.createBiquadFilter();
            filter.type = 'lowpass'; filter.frequency.value = 800;
            src.connect(filter); filter.connect(noiseGain); noiseGain.connect(ac.destination);
            noiseGain.gain.setValueAtTime(0.4, ac.currentTime);
            noiseGain.gain.exponentialRampToValueAtTime(0.001, ac.currentTime + 0.8);
            src.start(ac.currentTime); src.stop(ac.currentTime + 0.8);
            const osc = ac.createOscillator(), oscGain = ac.createGain();
            osc.connect(oscGain); oscGain.connect(ac.destination);
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(400, ac.currentTime);
            osc.frequency.exponentialRampToValueAtTime(30, ac.currentTime + 0.7);
            oscGain.gain.setValueAtTime(0.3, ac.currentTime);
            oscGain.gain.exponentialRampToValueAtTime(0.001, ac.currentTime + 0.7);
            osc.start(ac.currentTime); osc.stop(ac.currentTime + 0.7);
        },

        soundBossAppear() {
            if (!this.audioCtx) return;
            const ac = this.audioCtx;
            [{ freq: 110, t: 0, dur: 0.25 }, { freq: 146, t: 0.22, dur: 0.25 }, { freq: 196, t: 0.44, dur: 0.5 }]
                .forEach(({ freq, t, dur }) => {
                    const osc = ac.createOscillator(), gain = ac.createGain();
                    osc.connect(gain); gain.connect(ac.destination);
                    osc.type = 'sawtooth'; osc.frequency.value = freq;
                    const st = ac.currentTime + t;
                    gain.gain.setValueAtTime(0, st);
                    gain.gain.linearRampToValueAtTime(0.22, st + 0.03);
                    gain.gain.exponentialRampToValueAtTime(0.001, st + dur);
                    osc.start(st); osc.stop(st + dur);
                });
        },

        soundVictory() {
            if (!this.audioCtx) return;
            const ac = this.audioCtx;
            [523, 659, 784, 1047].forEach((freq, i) => {
                const osc = ac.createOscillator(), gain = ac.createGain();
                osc.connect(gain); gain.connect(ac.destination);
                osc.type = 'triangle'; osc.frequency.value = freq;
                const t = ac.currentTime + i * 0.13;
                gain.gain.setValueAtTime(0, t);
                gain.gain.linearRampToValueAtTime(0.2, t + 0.04);
                gain.gain.exponentialRampToValueAtTime(0.001, t + 0.25);
                osc.start(t); osc.stop(t + 0.26);
            });
        },

        soundGameOver() {
            if (!this.audioCtx) return;
            const ac = this.audioCtx;
            [392, 330, 262, 196].forEach((freq, i) => {
                const osc = ac.createOscillator(), gain = ac.createGain();
                osc.connect(gain); gain.connect(ac.destination);
                osc.type = 'sawtooth'; osc.frequency.value = freq;
                const t = ac.currentTime + i * 0.18;
                gain.gain.setValueAtTime(0, t);
                gain.gain.linearRampToValueAtTime(0.18, t + 0.05);
                gain.gain.exponentialRampToValueAtTime(0.001, t + 0.35);
                osc.start(t); osc.stop(t + 0.36);
            });
        },

        _generateStarsDOM() {
            const container = document.getElementById('stars-dark');
            if (!container) return;
            for (let i = 0; i < 80; i++) {
                const star = document.createElement('div');
                star.className = 'star';
                star.style.left = Math.random() * 100 + '%';
                star.style.top = Math.random() * 100 + '%';
                star.style.width = star.style.height = (1 + Math.random() * 3) + 'px';
                star.style.animationDelay = Math.random() * 3 + 's';
                container.appendChild(star);
            }
        },
    }));
});