# Audio Reference — Web Audio API (síntese pura)

Todos os sons são gerados por síntese em tempo real.
Nenhum arquivo de áudio externo é necessário.

## Inicialização

```js
// Propriedade no Alpine component
audioCtx: null,

// Chamar SEMPRE antes do primeiro som, em resposta a interação do usuário
initAudio() {
    if (this.audioCtx) return;
    this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
},
```

Chamar `initAudio()` em `startGame()` e `nextWave()` — ambos são acionados por click/touch.

---

## soundPlayerShoot() — pulso agudo descendente

```js
soundPlayerShoot() {
    if (!this.audioCtx) return;
    const ac = this.audioCtx;
    const osc = ac.createOscillator();
    const gain = ac.createGain();
    osc.connect(gain); gain.connect(ac.destination);
    osc.type = 'square';
    osc.frequency.setValueAtTime(880, ac.currentTime);
    osc.frequency.exponentialRampToValueAtTime(220, ac.currentTime + 0.08);
    gain.gain.setValueAtTime(0.18, ac.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001, ac.currentTime + 0.1);
    osc.start(ac.currentTime); osc.stop(ac.currentTime + 0.1);
},
```

## soundEnemyShoot() — tom grave sinistro

```js
soundEnemyShoot() {
    if (!this.audioCtx) return;
    const ac = this.audioCtx;
    const osc = ac.createOscillator();
    const gain = ac.createGain();
    osc.connect(gain); gain.connect(ac.destination);
    osc.type = 'sawtooth';
    osc.frequency.setValueAtTime(180, ac.currentTime);
    osc.frequency.exponentialRampToValueAtTime(80, ac.currentTime + 0.12);
    gain.gain.setValueAtTime(0.12, ac.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001, ac.currentTime + 0.13);
    osc.start(ac.currentTime); osc.stop(ac.currentTime + 0.13);
},
```

## soundEnemyDie() — explosão de ruído branco

```js
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
    filter.type = 'bandpass'; filter.frequency.value = 400; filter.Q.value = 0.8;
    src.connect(filter); filter.connect(gain); gain.connect(ac.destination);
    gain.gain.setValueAtTime(0.35, ac.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001, ac.currentTime + 0.22);
    src.start(ac.currentTime); src.stop(ac.currentTime + 0.22);
},
```

## soundPlayerHit() — buzz grave impactante

```js
soundPlayerHit() {
    if (!this.audioCtx) return;
    const ac = this.audioCtx;
    const osc = ac.createOscillator();
    const gain = ac.createGain();
    osc.connect(gain); gain.connect(ac.destination);
    osc.type = 'sawtooth';
    osc.frequency.setValueAtTime(120, ac.currentTime);
    osc.frequency.exponentialRampToValueAtTime(40, ac.currentTime + 0.3);
    gain.gain.setValueAtTime(0.25, ac.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001, ac.currentTime + 0.3);
    osc.start(ac.currentTime); osc.stop(ac.currentTime + 0.3);
},
```

## soundBossHit() — dissonância metálica

Dois osciladores levemente desafinados criam um som de "impacto pesado".

```js
soundBossHit() {
    if (!this.audioCtx) return;
    const ac = this.audioCtx;
    [260, 267].forEach(freq => {
        const osc = ac.createOscillator();
        const gain = ac.createGain();
        osc.connect(gain); gain.connect(ac.destination);
        osc.type = 'square';
        osc.frequency.value = freq;
        gain.gain.setValueAtTime(0.15, ac.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ac.currentTime + 0.18);
        osc.start(ac.currentTime); osc.stop(ac.currentTime + 0.18);
    });
},
```

## soundBossDie() — explosão longa + queda

```js
soundBossDie() {
    if (!this.audioCtx) return;
    const ac = this.audioCtx;

    // Ruído longo
    const bufSize = ac.sampleRate * 0.8;
    const buf = ac.createBuffer(1, bufSize, ac.sampleRate);
    const data = buf.getChannelData(0);
    for (let i = 0; i < bufSize; i++) data[i] = Math.random() * 2 - 1;
    const src = ac.createBufferSource();
    src.buffer = buf;
    const noiseGain = ac.createGain();
    const filter = ac.createBiquadFilter();
    filter.type = 'lowpass'; filter.frequency.value = 800;
    src.connect(filter); filter.connect(noiseGain); noiseGain.connect(ac.destination);
    noiseGain.gain.setValueAtTime(0.4, ac.currentTime);
    noiseGain.gain.exponentialRampToValueAtTime(0.001, ac.currentTime + 0.8);
    src.start(ac.currentTime); src.stop(ac.currentTime + 0.8);

    // Tom descendente dramático
    const osc = ac.createOscillator();
    const oscGain = ac.createGain();
    osc.connect(oscGain); oscGain.connect(ac.destination);
    osc.type = 'sawtooth';
    osc.frequency.setValueAtTime(400, ac.currentTime);
    osc.frequency.exponentialRampToValueAtTime(30, ac.currentTime + 0.7);
    oscGain.gain.setValueAtTime(0.3, ac.currentTime);
    oscGain.gain.exponentialRampToValueAtTime(0.001, ac.currentTime + 0.7);
    osc.start(ac.currentTime); osc.stop(ac.currentTime + 0.7);
},
```

## soundBossAppear() — fanfarra grave crescente

```js
soundBossAppear() {
    if (!this.audioCtx) return;
    const ac = this.audioCtx;
    // Três notas crescentes em sequência
    const notes = [
        { freq: 110, t: 0,    dur: 0.25 },
        { freq: 146, t: 0.22, dur: 0.25 },
        { freq: 196, t: 0.44, dur: 0.5  },
    ];
    notes.forEach(({ freq, t, dur }) => {
        const osc = ac.createOscillator();
        const gain = ac.createGain();
        osc.connect(gain); gain.connect(ac.destination);
        osc.type = 'sawtooth';
        osc.frequency.value = freq;
        const st = ac.currentTime + t;
        gain.gain.setValueAtTime(0, st);
        gain.gain.linearRampToValueAtTime(0.22, st + 0.03);
        gain.gain.exponentialRampToValueAtTime(0.001, st + dur);
        osc.start(st); osc.stop(st + dur);
    });
},
```

## soundVictory() — jingle ascendente C-E-G-C

```js
soundVictory() {
    if (!this.audioCtx) return;
    const ac = this.audioCtx;
    [523, 659, 784, 1047].forEach((freq, i) => {
        const osc = ac.createOscillator();
        const gain = ac.createGain();
        osc.connect(gain); gain.connect(ac.destination);
        osc.type = 'triangle';
        osc.frequency.value = freq;
        const t = ac.currentTime + i * 0.13;
        gain.gain.setValueAtTime(0, t);
        gain.gain.linearRampToValueAtTime(0.2, t + 0.04);
        gain.gain.exponentialRampToValueAtTime(0.001, t + 0.25);
        osc.start(t); osc.stop(t + 0.26);
    });
},
```

## soundGameOver() — jingle descendente G-E-C-G

```js
soundGameOver() {
    if (!this.audioCtx) return;
    const ac = this.audioCtx;
    [392, 330, 262, 196].forEach((freq, i) => {
        const osc = ac.createOscillator();
        const gain = ac.createGain();
        osc.connect(gain); gain.connect(ac.destination);
        osc.type = 'sawtooth';
        osc.frequency.value = freq;
        const t = ac.currentTime + i * 0.18;
        gain.gain.setValueAtTime(0, t);
        gain.gain.linearRampToValueAtTime(0.18, t + 0.05);
        gain.gain.exponentialRampToValueAtTime(0.001, t + 0.35);
        osc.start(t); osc.stop(t + 0.36);
    });
},
```

---

## Notas de implementação

- Todos os métodos verificam `if (!this.audioCtx) return;` — seguro chamar antes do init
- `osc.stop()` sempre com um tempo futuro — nunca `stop(0)` pois causa erro
- Para sons que disparam frequentemente (tiro do player), o volume está baixo (0.18) para não saturar
- O boss tem `soundBossHit()` separado do `soundEnemyDie()` para diferenciar o feedback tátil
- `soundBossAppear()` tem 3 notas com ~1s total — combina bem com o `setTimeout(2200)` do alerta
