# Engine Reference — update / draw / colisões

## update(dt, now)

```js
update(dt, now) {
    const W = this.canvas.width;
    const H = this.canvas.height;

    // ── Player ──────────────────────────────────────────────────────────
    if (this.keys.left)
        this.player.x = Math.max(this.player.w / 2, this.player.x - this.player.speed * dt);
    if (this.keys.right)
        this.player.x = Math.min(W - this.player.w / 2, this.player.x + this.player.speed * dt);
    if (this.playerShootCooldown > 0) this.playerShootCooldown -= dt * 1000;

    // ── Player bullets ──────────────────────────────────────────────────
    this.bullets = this.bullets.filter(b => { b.y -= 480 * dt; return b.y > 0; });

    // ── Enemy movement ──────────────────────────────────────────────────
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

    // ── Boss movement ────────────────────────────────────────────────────
    if (this.boss && this.boss.alive) {
        this.boss.x += this.boss.dir * this.boss.speed * dt;
        if (this.boss.x + this.boss.w / 2 >= W - 20) this.boss.dir = -1;
        if (this.boss.x - this.boss.w / 2 <= 20)     this.boss.dir =  1;

        // Boss shoot (leque de 3 balas)
        this.boss.shootTimer += dt * 1000;
        if (this.boss.shootTimer >= this.boss.shootInterval) {
            this.boss.shootTimer = 0;
            const offsets = [-12, 0, 12];
            for (const ox of offsets) {
                this.enemyBullets.push({
                    x: this.boss.x + ox,
                    y: this.boss.y + this.boss.h / 2 + 4,
                    isBoss: true,
                });
            }
            this.soundEnemyShoot();
        }

        // Hit flash decay
        if (this.boss.hitFlash > 0) this.boss.hitFlash -= dt * 10;
    }

    // ── Enemy shoot ──────────────────────────────────────────────────────
    this.enemyShootTimer += dt * 1000;
    if (this.enemyShootTimer >= this.enemyShootInterval && alive.length > 0) {
        this.enemyShootTimer = 0;
        const shooter = alive[Math.floor(Math.random() * alive.length)];
        this.enemyBullets.push({ x: shooter.x, y: shooter.y + 20 });
        this.soundEnemyShoot();
        this.enemyShootInterval = Math.max(400,
            1800 - (this.wave - 1) * 200 - (40 - alive.length) * 18);
    }

    // ── Enemy bullets ────────────────────────────────────────────────────
    this.enemyBullets = this.enemyBullets.filter(b => {
        b.y += (b.isBoss ? 280 : 240) * dt;
        return b.y < H;
    });

    // ── Particles ────────────────────────────────────────────────────────
    this.particles = this.particles.filter(p => {
        p.x += p.vx * dt; p.y += p.vy * dt;
        p.life -= dt; p.vy += 80 * dt;
        return p.life > 0;
    });

    // ── Colisões ─────────────────────────────────────────────────────────

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

    // Enemy alcançou base
    for (const e of this.enemies) {
        if (e.alive && e.y + e.h / 2 >= H - 60) { this.endGame('gameover'); return; }
    }

    // ── Condição de vitória ───────────────────────────────────────────────
    const allEnemiesDead = alive.length === 0;
    const bossDead = !this.boss || !this.boss.alive;
    if (allEnemiesDead && bossDead) {
        this.wave++;
        this.endGame('victory');
    }
},
```

## draw(now)

```js
draw(now) {
    const W = this.canvas.width;
    const H = this.canvas.height;
    const ctx = this.ctx;

    // Background + stars
    ctx.fillStyle = '#030712';
    ctx.fillRect(0, 0, W, H);
    // ... (stars com semente fixa para consistência)

    // Barriers
    for (const bar of this.barriers) {
        if (bar.health <= 0) continue;
        ctx.fillStyle = `rgba(74,222,128,${bar.health/3})`;
        ctx.fillRect(bar.x, bar.y, 10, 10);
    }

    // Player
    this.drawPlayer(this.player.x, this.player.y, '#4ade80');

    // Player bullets (com glow)
    for (const b of this.bullets) {
        ctx.fillStyle = '#bbf7d0';
        ctx.shadowBlur = 6; ctx.shadowColor = '#4ade80';
        ctx.fillRect(b.x - 2, b.y - 8, 4, 10);
        ctx.shadowBlur = 0;
    }

    // Boss (renderizado antes dos inimigos para ficar "atrás")
    if (this.boss && this.boss.alive) this.drawBoss(this.boss, now);

    // Enemies
    for (const e of this.enemies) {
        if (!e.alive) continue;
        this.drawEnemy(e, now);
    }

    // Enemy bullets
    for (const b of this.enemyBullets) {
        ctx.fillStyle = b.isBoss ? '#fb923c' : '#f87171';
        ctx.shadowBlur = 6;
        ctx.shadowColor = b.isBoss ? '#f97316' : '#ef4444';
        ctx.fillRect(b.x - 2, b.y, 4, 12);
        ctx.shadowBlur = 0;
    }

    // Particles
    for (const p of this.particles) {
        ctx.globalAlpha = p.life / (p.maxLife || 0.7);
        ctx.fillStyle = p.color;
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.size * (p.life / p.maxLife), 0, Math.PI * 2);
        ctx.fill();
    }
    ctx.globalAlpha = 1;

    // Bottom line
    ctx.strokeStyle = 'rgba(74,222,128,0.3)';
    ctx.lineWidth = 1;
    ctx.beginPath(); ctx.moveTo(0, H - 42); ctx.lineTo(W, H - 42); ctx.stroke();
},
```

## drawPlayer(x, y, color)

```js
drawPlayer(x, y, color) {
    const ctx = this.ctx;
    ctx.fillStyle = color;
    ctx.shadowBlur = 8; ctx.shadowColor = color;
    ctx.beginPath();
    ctx.moveTo(x,      y - 12);
    ctx.lineTo(x + 18, y +  8);
    ctx.lineTo(x + 10, y +  4);
    ctx.lineTo(x + 10, y + 10);
    ctx.lineTo(x - 10, y + 10);
    ctx.lineTo(x - 10, y +  4);
    ctx.lineTo(x - 18, y +  8);
    ctx.closePath();
    ctx.fill();
    ctx.shadowBlur = 0;
},
```

## drawEnemy(e, now)

```js
drawEnemy(e, now) {
    const ctx = this.ctx;
    const bob = Math.sin(now / 1000 * 2 + e.animOffset) * 3;

    ctx.shadowBlur = 12; ctx.shadowColor = e.color;

    // Círculo com borda
    ctx.beginPath();
    ctx.arc(e.x, e.y + bob, 16, 0, Math.PI * 2);
    ctx.fillStyle = e.color + '33'; ctx.fill();
    ctx.beginPath();
    ctx.arc(e.x, e.y + bob, 14, 0, Math.PI * 2);
    ctx.strokeStyle = e.color; ctx.lineWidth = 1.5; ctx.stroke();

    // Letra
    ctx.shadowBlur = 0;
    ctx.fillStyle = e.color;
    ctx.font = 'bold 14px "Share Tech Mono", monospace';
    ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
    ctx.fillText(e.letter, e.x, e.y + bob);

    // Olhinhos
    ctx.fillStyle = '#030712';
    ctx.beginPath();
    ctx.arc(e.x - 5, e.y + bob - 2, 2, 0, Math.PI * 2);
    ctx.arc(e.x + 5, e.y + bob - 2, 2, 0, Math.PI * 2);
    ctx.fill();
},
```

## spawnEnemies() — com offset para boss

```js
spawnEnemies() {
    const W = this.canvas.width;
    this.enemies = [];
    this.enemyDir = 1;
    this.enemyDropped = false;

    const pool = this.users.length > 0 ? this.users : [{ name: 'Guest' }];
    const cols = Math.min(10, pool.length);
    const rows = Math.ceil(Math.min(pool.length, 40) / cols);

    // Se for boss wave, desce os inimigos para dar espaço ao boss
    const yOffset = this.isBossWave() ? 80 : 0;

    const colors = ['#4ade80','#34d399','#a3e635','#22d3ee',
                    '#60a5fa','#f472b6','#fb923c','#facc15'];
    let idx = 0;
    for (let r = 0; r < rows; r++) {
        for (let c = 0; c < cols && idx < pool.length && idx < 40; c++, idx++) {
            this.enemies.push({
                x: 60 + c * 54,
                y: 80 + yOffset + r * 52,
                w: 36, h: 36,
                letter: pool[idx].name.charAt(0).toUpperCase(),
                color:  colors[(r * cols + c) % colors.length],
                name:   pool[idx].name,
                alive:  true,
                animOffset: Math.random() * Math.PI * 2,
            });
        }
    }
},
```

## spawnExplosion(x, y, color, big = false)

```js
spawnExplosion(x, y, color, big = false) {
    const count = big ? 24 : 12;
    for (let i = 0; i < count; i++) {
        const angle = (Math.PI * 2 / count) * i + Math.random() * 0.3;
        const speed = (big ? 100 : 60) + Math.random() * (big ? 180 : 120);
        this.particles.push({
            x, y,
            vx: Math.cos(angle) * speed,
            vy: Math.sin(angle) * speed - (big ? 50 : 30),
            life:    0.4 + Math.random() * (big ? 0.6 : 0.3),
            maxLife: big ? 1.0 : 0.7,
            color,
            size: (big ? 3 : 2) + Math.random() * 3,
        });
    }
},
```
