/**
 * Fiches utilisateur (avatar) : API Popover en mode "manual" pour éviter la fermeture
 * immédiate au même clic que l’ouverture (comportement du light dismiss avec <details> ou popover=auto).
 * Position fixed dans le top layer, hors overflow des panneaux.
 */

function getManexoUserCardButton(panel) {
    if (!panel?.id) {
        return null;
    }
    const id = panel.id;
    const safe = typeof CSS !== 'undefined' && typeof CSS.escape === 'function' ? CSS.escape(id) : id;
    return document.querySelector(`[popovertarget="${safe}"]`);
}

function positionManexoUserCard(panel) {
    const btn = getManexoUserCardButton(panel);
    if (!btn) {
        return;
    }

    const margin = 12;
    const maxW = Math.min(24 * 16, window.innerWidth - 2 * margin);

    panel.style.position = 'fixed';
    panel.style.zIndex = '6000';
    panel.style.right = 'auto';
    panel.style.bottom = 'auto';
    panel.style.transform = 'none';
    panel.style.maxWidth = `min(24rem, calc(100vw - ${2 * margin}px))`;
    panel.style.removeProperty('width');

    const br = btn.getBoundingClientRect();
    const ph = panel.offsetHeight;
    const pw = Math.min(panel.getBoundingClientRect().width || panel.offsetWidth, maxW);

    let left = br.left + br.width / 2 - pw / 2;
    left = Math.max(margin, Math.min(left, window.innerWidth - pw - margin));

    let top = br.top - ph - margin;
    if (top < margin) {
        top = br.bottom + margin;
    }
    if (top + ph > window.innerHeight - margin) {
        top = Math.max(margin, window.innerHeight - ph - margin);
    }

    panel.style.left = `${left}px`;
    panel.style.top = `${top}px`;
}

function resetManexoUserCardPanel(panel) {
    if (!panel) {
        return;
    }
    [
        'position',
        'left',
        'top',
        'right',
        'bottom',
        'transform',
        'width',
        'z-index',
        'max-width',
    ].forEach((p) => panel.style.removeProperty(p));
}

function closeOtherUserCards(exceptPanel) {
    document.querySelectorAll('[data-manexo-user-card][popover]').forEach((p) => {
        if (p === exceptPanel || typeof p.hidePopover !== 'function') {
            return;
        }
        try {
            if (p.matches(':popover-open')) {
                p.hidePopover();
            }
        } catch {
            /* :popover-open / hidePopover */
        }
    });
}

function isPopoverOpen(panel) {
    try {
        return Boolean(panel?.matches?.(':popover-open'));
    } catch {
        return false;
    }
}

function onUserCardToggle(ev) {
    const panel = ev.target;
    if (!panel?.matches?.('[data-manexo-user-card][popover]')) {
        return;
    }
    const te = /** @type {ToggleEvent} */ (ev);
    if (te.newState !== 'open') {
        resetManexoUserCardPanel(panel);
        return;
    }

    closeOtherUserCards(panel);

    requestAnimationFrame(() => {
        requestAnimationFrame(() => positionManexoUserCard(panel));
    });
}

function onDiscussionScroll() {
    document.querySelectorAll('[data-manexo-user-card][popover]').forEach((panel) => {
        if (isPopoverOpen(panel)) {
            positionManexoUserCard(panel);
        }
    });
}

function bindScrollRepositionOnContainers() {
    document.querySelectorAll('.discussion-chat-scroll, #thread-messages').forEach((el) => {
        if (el.dataset.manexoUserCardScrollBound === '1') {
            return;
        }
        el.dataset.manexoUserCardScrollBound = '1';
        el.addEventListener('scroll', onDiscussionScroll, { passive: true });
    });
}

/** Clic hors de la zone avatar + fiche : fermer (popover=manual). */
document.addEventListener(
    'click',
    (e) => {
        if (!(e.target instanceof Element)) {
            return;
        }
        document.querySelectorAll('[data-manexo-user-card][popover]').forEach((panel) => {
            if (!isPopoverOpen(panel) || typeof panel.hidePopover !== 'function') {
                return;
            }
            const root = panel.closest('[data-manexo-user-card-root]');
            if (root && root.contains(e.target)) {
                return;
            }
            try {
                panel.hidePopover();
            } catch {
                /* ignore */
            }
        });
    },
    true,
);

document.addEventListener('toggle', onUserCardToggle, true);

window.addEventListener(
    'resize',
    () => {
        document.querySelectorAll('[data-manexo-user-card][popover]').forEach((panel) => {
            if (isPopoverOpen(panel)) {
                positionManexoUserCard(panel);
            }
        });
    },
    { passive: true },
);

document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') {
        return;
    }
    document.querySelectorAll('[data-manexo-user-card][popover]').forEach((panel) => {
        if (!isPopoverOpen(panel) || typeof panel.hidePopover !== 'function') {
            return;
        }
        try {
            panel.hidePopover();
        } catch {
            /* ignore */
        }
    });
});

bindScrollRepositionOnContainers();
document.addEventListener('livewire:navigated', bindScrollRepositionOnContainers);
document.addEventListener('livewire:init', () => {
    bindScrollRepositionOnContainers();
});
