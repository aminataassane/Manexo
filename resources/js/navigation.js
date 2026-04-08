/**
 * Manexo + Livewire wire:navigate
 *
 * 1) Le layout utilise un scroll interne (.manexo-app-main-scroll) alors que body est
 *    overflow:hidden — Livewire ne remonte que window ; on remet le conteneur en haut.
 * 2) Transition ultra-rapide : fade-out ancien contenu → swap → fade-in nouveau contenu.
 */

function resetMainScrollAreas() {
    document.querySelectorAll('.manexo-app-main-scroll').forEach((el) => {
        el.scrollTop = 0;
        el.scrollLeft = 0;
    });
}

document.addEventListener('livewire:navigating', () => {
    document.documentElement.classList.add('manexo-is-navigating');
    // Transition très légère (perception « quasi instantanée » vs écran vide)
    const wrap = document.querySelector('.manexo-content-wrap');
    if (wrap) {
        wrap.style.opacity = '0.92';
        wrap.style.transform = 'translateY(1px)';
    }
});

document.addEventListener('livewire:navigated', () => {
    document.documentElement.classList.remove('manexo-is-navigating');

    // Fade in le nouveau contenu
    const wrap = document.querySelector('.manexo-content-wrap');
    if (wrap) {
        wrap.style.transition = 'none';
        wrap.style.opacity = '0.96';
        wrap.style.transform = 'translateY(2px)';
        // Force reflow avant d'animer
        wrap.offsetHeight;
        wrap.style.transition = 'opacity 0.08s ease-out, transform 0.08s ease-out';
        wrap.style.opacity = '1';
        wrap.style.transform = 'translateY(0)';
        // Cleanup
        wrap.addEventListener('transitionend', () => {
            wrap.style.transition = '';
            wrap.style.opacity = '';
            wrap.style.transform = '';
        }, { once: true });
    }

    queueMicrotask(() => {
        queueMicrotask(resetMainScrollAreas);
    });
});
