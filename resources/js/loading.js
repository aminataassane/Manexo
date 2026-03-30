/**
 * Global action-button loading feedback for Livewire.
 *
 * When a user clicks a button with wire:click or submits a form with
 * wire:submit, the button enters a loading state automatically:
 *   - pointer-events disabled
 *   - visual spinner replaces content
 *   - double-clicks prevented
 *
 * Cleanup happens via Livewire's DOM morphing (the re-rendered HTML
 * replaces the loading element) + a safety timeout fallback.
 *
 * Opt-out: add data-no-loading to any button to skip.
 */

const SAFETY_TIMEOUT = 10_000;

/** Actions that are pure client-side UI — no server round-trip. */
function isClientSideOnly(action) {
    if (!action) return true;
    // $set, $toggle, $dispatch, $emit, $refresh are Livewire client-side helpers
    if (/^\$/.test(action.trim())) return true;
    // Common UI-only method names (open/close modals, toggles, resets)
    if (
        /^(open|close|show|hide|toggle|reset|load|set(?:Tab|View|Box|Mode|Display))/i.test(
            action.trim(),
        )
    )
        return true;
    return false;
}

function markLoading(el) {
    if (!el || el.disabled || el.classList.contains("is-loading")) return;
    el.classList.add("is-loading");
    el.disabled = true;
    // Safety fallback: re-enable after timeout if Livewire morph didn't clean up
    el._loadingTimer = setTimeout(() => clearLoading(el), SAFETY_TIMEOUT);
}

function clearLoading(el) {
    if (!el) return;
    el.classList.remove("is-loading");
    el.disabled = false;
    if (el._loadingTimer) {
        clearTimeout(el._loadingTimer);
        el._loadingTimer = null;
    }
}

// --- wire:click buttons ---
document.addEventListener(
    "click",
    (e) => {
        const btn = e.target.closest(
            "button[wire\\:click]:not([data-no-loading])",
        );
        if (!btn) return;
        const action = btn.getAttribute("wire:click") || "";
        if (isClientSideOnly(action)) return;
        markLoading(btn);
    },
    true,
);

// --- Alpine @click with $wire calls ---
document.addEventListener(
    "click",
    (e) => {
        const btn = e.target.closest(
            "button[x-on\\:click]:not([data-no-loading]):not([wire\\:click])",
        );
        if (!btn) return;
        const action =
            btn.getAttribute("x-on:click") || btn.getAttribute("@click") || "";
        if (!action.includes("$wire.") || action.includes("$wire.$entangle"))
            return;
        // Only for $wire.methodName() calls, not $wire.set() or $dispatch
        if (/\$wire\.(?:set|dispatch|\$)/.test(action)) return;
        markLoading(btn);
    },
    true,
);

// --- form wire:submit ---
document.addEventListener(
    "submit",
    (e) => {
        const form = e.target;
        if (
            !form.hasAttribute("wire:submit") &&
            !form.hasAttribute("wire:submit.prevent")
        )
            return;
        const btn = form.querySelector(
            'button[type="submit"]:not([data-no-loading]):not(.is-loading)',
        );
        if (btn) markLoading(btn);
    },
    true,
);

// --- Cleanup via Livewire request hooks ---
document.addEventListener("livewire:init", () => {
    Livewire.hook("request", ({ respond, succeed, fail }) => {
        const cleanup = () => {
            document.querySelectorAll(".is-loading").forEach(clearLoading);
        };
        succeed(cleanup);
        fail(cleanup);
    });
});
