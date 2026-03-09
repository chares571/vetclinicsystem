import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const animatedElements = [...document.querySelectorAll('.animate-rise, .animate-fade-in')];

    animatedElements.forEach((element, index) => {
        element.style.animationDelay = `${Math.min(index * 45, 320)}ms`;
    });
});
