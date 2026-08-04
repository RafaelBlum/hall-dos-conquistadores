# Boss Reference — sprite 8-bit, HP bar, alert overlay

## isBossWave() e pickBossUser()

```js
isBossWave() {
    return this.wave % 3 === 1 && this.wave > 1;
    // waves 4, 7, 10, 13...
},

pickBossUser() {
    const pool = this.users.filter(u => u.id !== this.authUserId);
    if (!pool.length) return this.users[0]; // fallback se só há 1 user
    return pool[Math.floor(Math.random() * pool.length)];
},
```

## spawnBoss()

```js
spawnBoss() {
    const W = this.canvas.width;
    const bossUser = this.pickBossUser();
    this.currentBossUser = bossUser;

    this.boss = {
        x: W / 2,
        y: 70,
        w: 80,
        h: 56,
        hp: 5 + this.wave * 2,
        maxHp: 5 + this.wave * 2,
        name: bossUser.name.substring(0, 10).toUpperCase(),
        color: '#f472b6',
        alive: true,
        dir: 1,
        speed: 60 + this.wave * 5,
        shootTimer: 0,
        shootInterval: Math.max(600, 1200 - this.wave * 60),
        hitFlash: 0,
    };

    this.soundBossAppear();
},
```

## _launchWave() — com boss

```js
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
    this.boss = null;
    this.currentBossUser = null;

    this.player = { x: W / 2, y: H - 50, w: 36, h: 20, speed: 220 };

    // Barriers
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

    // Boss wave: mostrar alerta antes de liberar o jogo
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
```

## drawBoss(boss, now) — sprite 8-bit com fillRect

O sprite é um grid de pixels desenhado com `fillRect(px*S, py*S, S, S)` onde S = tamanho do pixel (6px).
A âncora é o centro do boss (`boss.x`, `boss.y`).

```js
drawBoss(boss, now) {
    const ctx = this.ctx;
    const S = 6; // tamanho de cada "pixel" do sprite
    const cols = 13;
    const rows = 9;
    const ox = boss.x - (cols * S) / 2;
    const oy = boss.y - (rows * S) / 2;

    // Piscada quando leva hit
    if (boss.hitFlash > 0 && Math.floor(boss.hitFlash * 8) % 2 === 0) {
        ctx.globalAlpha = 0.3;
    }

    // Sprite 8-bit: 1 = cor do boss, 2 = cor escura, 0 = transparente
    const sprite = [
        [0,0,1,0,0,0,0,0,0,0,1,0,0],
        [0,0,0,1,0,0,0,0,0,1,0,0,0],
        [0,0,1,1,1,1,1,1,1,1,1,0,0],
        [0,1,1,2,1,1,2,1,1,2,1,1,0],
        [1,1,1,1,1,1,1,1,1,1,1,1,1],
        [1,0,1,1,1,1,1,1,1,1,1,0,1],
        [1,0,1,0,0,1,0,1,0,0,1,0,1],
        [0,0,0,1,1,0,0,0,1,1,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0],
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
    ctx.shadowBlur = 0;
    ctx.globalAlpha = 1;

    // Nome do boss (máx 10 chars) — centralizado abaixo do sprite
    ctx.fillStyle = boss.color;
    ctx.font = 'bold 11px "Share Tech Mono", monospace';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'top';
    ctx.fillText(boss.name, boss.x, oy + rows * S + 4);

    // Barra de HP acima do sprite
    const barW = cols * S;
    const barH = 5;
    const barX = ox;
    const barY = oy - 12;
    const hpRatio = boss.hp / boss.maxHp;

    ctx.fillStyle = '#1f2937';
    ctx.fillRect(barX, barY, barW, barH);

    const hpColor = hpRatio > 0.5 ? '#4ade80'
                  : hpRatio > 0.25 ? '#facc15'
                  : '#ef4444';
    ctx.fillStyle = hpColor;
    ctx.fillRect(barX, barY, barW * hpRatio, barH);

    ctx.strokeStyle = '#374151';
    ctx.lineWidth = 1;
    ctx.strokeRect(barX, barY, barW, barH);

    // Label HP
    ctx.fillStyle = '#9ca3af';
    ctx.font = '9px "Share Tech Mono", monospace';
    ctx.textAlign = 'left';
    ctx.textBaseline = 'bottom';
    ctx.fillText(`HP ${boss.hp}/${boss.maxHp}`, barX, barY - 1);
},
```

## Overlay boss_alert no Alpine template

Adicionar este bloco junto dos outros overlays (`x-show="state === ..."`):

```blade
{{-- Boss Wave Alert --}}
<div
    x-show="state === 'boss_alert'"
    class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900/85"
>
    <div class="text-yellow-400 text-xl mb-1 blink">⚠ BOSS WAVE ⚠</div>
    <div class="text-pink-400 text-3xl font-bold mb-2"
         x-text="currentBossUser?.name?.substring(0,10).toUpperCase() ?? '???'">
    </div>
    <div class="text-green-700 text-xs">prepare-se...</div>
</div>
```

## drawBossAlert() — alternativa canvas (sem Alpine overlay)

Se preferir desenhar o alerta diretamente no canvas durante o estado `boss_alert`,
chame este método em vez do overlay HTML:

```js
drawBossAlert(now) {
    const W = this.canvas.width;
    const H = this.canvas.height;
    const ctx = this.ctx;

    this.draw(now); // desenha o estado atual do jogo por baixo

    // Overlay semitransparente
    ctx.fillStyle = 'rgba(3,7,18,0.75)';
    ctx.fillRect(0, 0, W, H);

    // Texto piscante
    const blink = Math.floor(now / 350) % 2 === 0;
    if (blink) {
        ctx.fillStyle = '#fbbf24';
        ctx.font = 'bold 24px "Share Tech Mono", monospace';
        ctx.textAlign = 'center';
        ctx.fillText('⚠  BOSS WAVE  ⚠', W / 2, H / 2 - 30);
    }

    // Nome do boss
    ctx.fillStyle = '#f472b6';
    ctx.font = 'bold 36px "Share Tech Mono", monospace';
    ctx.textAlign = 'center';
    const name = this.boss?.name ?? '???';
    ctx.fillText(name, W / 2, H / 2 + 10);

    ctx.fillStyle = '#374151';
    ctx.font = '14px "Share Tech Mono", monospace';
    ctx.fillText('prepare-se...', W / 2, H / 2 + 44);
},
```
