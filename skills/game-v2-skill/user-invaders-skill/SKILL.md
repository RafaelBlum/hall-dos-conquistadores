---
name: user-invaders
description: >
  Cria um jogo Space Invaders completo em uma página Laravel Blade onde as naves inimigas
  são os usuários cadastrados no sistema. Use esta skill sempre que o usuário quiser criar
  um mini-game, jogo de usuários, gamificação de página, landing com jogo, ou qualquer
  variação de "Space Invaders com usuários do sistema". Também use quando pedirem para
  adicionar features ao jogo (boss, sons, ondas, placar), integrar o jogo com Auth do
  Laravel, ou criar uma landing page interativa para sistemas com usuários cadastrados.
  Stack: Laravel Blade + Alpine.js + Tailwind CSS + Canvas API + Web Audio API.
---

# User Invaders — Skill

Jogo Space Invaders integrado ao Laravel onde cada usuário cadastrado vira uma nave inimiga.
Toda a lógica de jogo roda em JavaScript puro (Canvas API), sem dependências externas.
O Laravel/Blade apenas fornece os dados; o Alpine.js faz a ponte entre PHP e o game engine.

## Arquitetura

```
landing.blade.php
│
├── Blade/PHP  →  serializa $users e $authUser como JSON no x-data
├── Alpine.js  →  gerencia estado da UI (score, lives, wave, overlays)
├── Canvas API →  game loop, física, colisões, renderização
└── Web Audio  →  síntese de sons sem arquivos externos
```

## Fluxo de dados Laravel → JS

```php
// Controller
return view('landing', [
    'users'    => \App\Models\User::all(['id', 'name']),
    'authUser' => auth()->user(),   // null se não logado
]);
```

```blade
{{-- View --}}
<div x-data="spaceInvaders(
    {{ $users->map(fn($u) => ['id'=>$u->id,'name'=>$u->name])->toJson() }},
    {{ $authUser?->id ?? 'null' }}
)">
```

O segundo parâmetro (`authUserId`) é usado para:
- Excluir o usuário logado do sorteio de boss
- Controlar se o botão de iniciar aparece ou redireciona para login

## Condição de Auth no botão de início

```blade
@auth
    <button @click="startGame()">[ INICIAR JOGO ]</button>
@else
    <a href="{{ route('login') }}"
       class="border border-yellow-600 text-yellow-500 px-8 py-2 text-sm">
        [ FAÇA LOGIN PARA JOGAR ]
    </a>
@endauth
```

**Alternativa via Alpine** (quando a condição vem do JS):
```js
// No x-data, passar um booleano do Blade
x-data="spaceInvaders(users, authUserId, {{ auth()->check() ? 'true' : 'false' }})"

// No template
<button x-if="isLoggedIn" @click="startGame()">[ INICIAR JOGO ]</button>
<a x-if="!isLoggedIn" href="/login">[ FAÇA LOGIN PARA JOGAR ]</a>
```

Prefira a abordagem Blade (`@auth/@else`) pois evita flash de conteúdo.

## Estrutura do Game Engine

Leia `references/engine.md` para detalhes completos de cada método.

### Propriedades principais do Alpine component

```js
function spaceInvaders(users, authUserId, isLoggedIn) {
    return {
        // UI state (Alpine reativo)
        users, authUserId, isLoggedIn,
        state: 'idle',      // 'idle' | 'playing' | 'gameover' | 'victory'
        score: 0,
        lives: 3,
        wave: 1,
        keys: { left: false, right: false, space: false },

        // Game engine (não reativo, performance-critical)
        ctx: null, canvas: null,
        animFrame: null, lastTime: 0,
        audioCtx: null,
        player: null,
        bullets: [], enemyBullets: [],
        enemies: [], barriers: [], particles: [],
        boss: null,             // entidade boss (ver Boss Wave)
        currentBossUser: null,  // { id, name } do usuário sorteado

        // Configurações de ritmo
        enemyDir: 1,
        enemySpeed: 40,
        enemyShootTimer: 0,
        enemyShootInterval: 1800,
        playerShootCooldown: 0,
        playerShootDelay: 350,
    }
}
```

### Ciclo de vida dos métodos

```
init()
  └─ setupKeyboard()

startGame()          ← novo jogo (reseta tudo)
nextWave()           ← próxima onda (mantém score/lives/wave)
  └─ _launchWave()
       ├─ isBossWave() → spawnBoss() se true
       └─ spawnEnemies()

loop(timestamp)
  ├─ update(dt, now)
  │    ├─ movimentação player/inimigos/boss
  │    ├─ tiros (player, inimigos, boss)
  │    ├─ colisões
  │    └─ condições de vitória/derrota
  └─ draw(now)
       ├─ drawPlayer()
       ├─ drawEnemy()
       ├─ drawBoss()      ← sprite 8-bit + HP bar
       └─ drawBossAlert() ← aviso piscante no início da boss wave

endGame(result)      ← 'gameover' | 'victory'
```

## Boss Wave — regras e implementação

Leia `references/boss.md` para o código completo do boss.

### Quando ocorre
- Wave é boss wave quando: `wave % 3 === 1 && wave > 1`
- Ou seja: waves 4, 7, 10, 13...
- Método: `isBossWave() { return this.wave % 3 === 1 && this.wave > 1; }`

### Sorteio do usuário
```js
pickBossUser() {
    const pool = this.users.filter(u => u.id !== this.authUserId);
    if (!pool.length) return this.users[0]; // fallback
    return pool[Math.floor(Math.random() * pool.length)];
},
```

### Entidade boss
```js
// Criada em spawnBoss()
this.boss = {
    x: W / 2, y: 80,
    w: 80, h: 64,
    hp: 5 + this.wave * 2,
    maxHp: 5 + this.wave * 2,
    name: bossUser.name.substring(0, 10).toUpperCase(),
    color: '#f472b6',
    alive: true,
    dir: 1,
    speed: 60 + this.wave * 5,
    shootTimer: 0,
    shootInterval: 1200,
    hitFlash: 0,   // contador de frames para efeito de piscada
};
```

### Condição de vitória com boss
```js
// Em update(), substituir o check simples por:
const allEnemiesDead = this.enemies.filter(e => e.alive).length === 0;
const bossDead = !this.boss || !this.boss.alive;
if (allEnemiesDead && bossDead) {
    this.wave++;
    this.endGame('victory');
}
```

## Sons (Web Audio API)

Todos os sons são sintetizados sem arquivos externos. Leia `references/audio.md`.

| Evento            | Método             | Tipo de síntese                    |
|-------------------|--------------------|-------------------------------------|
| Jogador atira     | soundPlayerShoot() | Oscilador square 880→220 Hz         |
| Inimigo atira     | soundEnemyShoot()  | Oscilador sawtooth 180→80 Hz        |
| Inimigo morre     | soundEnemyDie()    | Ruído branco + filtro bandpass      |
| Jogador leva hit  | soundPlayerHit()   | Oscilador sawtooth 120→40 Hz        |
| Boss leva hit     | soundBossHit()     | Dois osciladores em dissonância     |
| Boss morre        | soundBossDie()     | Ruído longo + queda de frequência   |
| Vitória           | soundVictory()     | Jingle C-E-G-C (triangle)           |
| Game Over         | soundGameOver()    | Jingle G-E-C-G (sawtooth)           |
| Boss aparece      | soundBossAppear()  | Fanfarra grave crescente            |

**Regra importante:** `initAudio()` só deve ser chamado em resposta a uma interação do usuário (click/touch) para respeitar a política dos browsers modernos.

## Configurações de dificuldade por wave

```js
// Velocidade dos inimigos
enemySpeed = 40 + (wave - 1) * 10

// Intervalo de tiro dos inimigos (ms)
enemyShootInterval = Math.max(400, 1800 - (wave-1)*200 - (40 - alive.length)*18)

// HP do boss
boss.hp = boss.maxHp = 5 + wave * 2

// Velocidade do boss
boss.speed = 60 + wave * 5
```

## Layout da tela

```
┌─────────────────────────────────────────┐
│  SCORE: 000000    USER INVADERS   ❤️❤️❤️  │
├─────────────────────────────────────────┤
│                                         │
│   [BOSS: JOAOSILVA]  ████████░░ HP      │  ← só em boss wave
│                                         │
│   👾 👾 👾 👾 👾 👾 👾 👾 👾 👾         │
│   👾 👾 👾 👾 👾 👾 👾 👾 👾 👾         │
│                                         │
│   ▓▓▓▓    ▓▓▓▓    ▓▓▓▓    ▓▓▓▓         │  ← barreiras
│                    △                    │  ← player
├─────────────────────────────────────────┤
│         [←]  [→]         [ATIRAR]       │  ← mobile controls
└─────────────────────────────────────────┘
```

## Overlays Alpine

| state       | Overlay exibido                                      |
|-------------|------------------------------------------------------|
| `idle`      | Título + botão iniciar (ou link de login se !auth)   |
| `playing`   | Nenhum (só controles mobile)                         |
| `boss_alert`| "⚠ BOSS WAVE — NOME DO USUÁRIO" piscando 2s          |
| `victory`   | Score + botão "Próxima Onda"                         |
| `gameover`  | Score final + botão "Jogar Novamente"                |

## Checklist de implementação

- [ ] Controller passa `$users` e `$authUser` para a view
- [ ] Blade usa `@auth/@else` no botão de início
- [ ] `spaceInvaders()` recebe `users`, `authUserId`, `isLoggedIn`
- [ ] `isBossWave()` implementado e chamado em `_launchWave()`
- [ ] `pickBossUser()` exclui `authUserId` do pool
- [ ] `drawBoss()` usa sprite 8-bit pixel-art com `fillRect`
- [ ] Nome do boss truncado em 10 caracteres
- [ ] Barra de HP acima do boss
- [ ] Boss tem tiro em leque (3 balas)
- [ ] Condição de vitória verifica `boss.alive` quando em boss wave
- [ ] `soundBossAppear()` chamado ao entrar em boss wave
- [ ] Overlay `boss_alert` exibido por ~2s antes de liberar o jogo

## Referências

- `references/engine.md` — Código completo de update/draw/colisões
- `references/boss.md`   — Sprite 8-bit, lógica de HP, tiro em leque
- `references/audio.md`  — Todos os métodos de síntese de som
