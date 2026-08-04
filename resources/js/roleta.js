
document.addEventListener('alpine:init', () => {
    Alpine.data('roletaComponent', (names) => ({
        names: names,
        spin() {
            if (!this.names || this.names.length === 0) {
                alert('Digite alguns nomes primeiro!');
                return;
            }

            const wheel = document.getElementById('wheel');
            const result = document.getElementById('result');

            const min = 1024;
            const max = 9999;
            const spins = Math.floor(Math.random() * (max - min)) + min;
            const deg = spins;

            wheel.style.transition = 'transform 4s ease-out';
            wheel.style.transform = `rotate(${deg}deg)`;

            wheel.addEventListener('transitionend', function handler() {
                const normalizedDeg = deg % 360;
                const sectorSize = 360 / names.length;
                const index = Math.floor(((360 - normalizedDeg + (sectorSize / 2)) % 360) / sectorSize);

                const selectedName = names[index];
                result.textContent = `🎉 Sorteado: ${selectedName} 🎉`;
                result.classList.remove('hidden');

                if (window.confetti) window.confetti();

                wheel.removeEventListener('transitionend', handler);
            });
        }
    }))

    function roleta() {
        return {
            angle: 0,
            speed: 1, // segundos para rotação lenta

            init() {
                this.slowSpin();
            },

            slowSpin() {
                // rotação lenta infinita
                setInterval(() => {
                    this.angle += 1;
                    if (this.angle >= 360) this.angle = 0;
                }, 100);
            },

            spin() {
                // gira rápido e para aleatório
                let extra = Math.floor(Math.random() * 360) + 720; // pelo menos 2 voltas
                this.speed = 3;
                this.angle += extra;
            }
        }
    }
})
