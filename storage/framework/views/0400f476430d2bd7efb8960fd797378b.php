
<div
    x-data="{
        toasts: [],
        add(type, message) {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, type, message });
            setTimeout(() => this.remove(id), 4000);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    @toast.window="add($event.detail.type ?? 'info', $event.detail.message ?? '')"
    class="fixed top-4 right-4 z-[200] flex flex-col items-end gap-2 pointer-events-none"
    style="max-width: min(24rem, calc(100vw - 2rem));"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="true"
            x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-6"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-6"
            class="pointer-events-auto flex items-start gap-3 rounded-xl border px-4 py-3 shadow-lg backdrop-blur-sm min-w-[16rem] max-w-full"
            :class="{
                'bg-white/95 border-slate-200 text-slate-800': toast.type === 'info',
                'bg-emerald-50/95 border-emerald-200 text-emerald-800': toast.type === 'success',
                'bg-red-50/95 border-red-200 text-red-800': toast.type === 'error',
                'bg-amber-50/95 border-amber-200 text-amber-800': toast.type === 'warning',
            }"
        >
            <div class="shrink-0 mt-0.5">
                <iconify-icon
                    :icon="toast.type === 'success' ? 'solar:check-circle-bold-duotone' : toast.type === 'error' ? 'solar:close-circle-bold-duotone' : toast.type === 'warning' ? 'solar:danger-triangle-bold-duotone' : 'solar:info-circle-bold-duotone'"
                    width="20"
                ></iconify-icon>
            </div>
            <p class="text-sm font-medium flex-1 min-w-0 break-words" x-text="toast.message"></p>
            <button @click="remove(toast.id)" class="shrink-0 opacity-50 hover:opacity-100 transition-opacity">
                <iconify-icon icon="solar:close-circle-linear" width="16"></iconify-icon>
            </button>
        </div>
    </template>
</div>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\components\manexo\toast.blade.php ENDPATH**/ ?>