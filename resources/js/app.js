import './bootstrap';
import './retro-animation.js';
import ScrollReveal from "scrollreveal";
import JSConfetti from 'js-confetti';

const sr = ScrollReveal({
    origin: 'top',
    distance: '30px',
    duration: 4000,
    delay: 400,
});

sr.reveal('.up', { origin: 'top', distance: '30px', duration: 4000, delay: 400, });
sr.reveal('.down', { origin: 'bottom', distance: '30px', duration: 4000, delay: 400, });
sr.reveal('.dir', { origin: 'right', distance: '30px', duration: 4000, delay: 400, });
sr.reveal('.esq', { origin: 'left', distance: '30px', duration: 4000, delay: 400, });
sr.reveal('.header_img_2', { origin: 'top', reset: true, interval: 2000 });

const jsConfetti = new JSConfetti();

window.confetti = () => jsConfetti.addConfetti(
    {
        emojis: ['⭐'],
    }
);

