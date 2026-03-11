{{-- Global confirm dialog (replaces native browser confirm) --}}
<div
    x-data="{
        open: false,
        title: '',
        message: '',
        confirmLabel: '',
        variant: 'danger',
        onConfirm: null,
        show(detail) {
            this.title = detail.title || '{{ __('Confirmer') }}';
            this.message = detail.message || '';
            this.confirmLabel = detail.confirmLabel || '{{ __('Confirmer') }}';
            this.variant = detail.variant || 'danger';
            this.onConfirm = detail.onConfirm || null;
            this.open = true;
        },
        confirm() {
            if (typeof this.onConfirm === 'function') {
                this.onConfirm();
            }
            this.open = false;
        },
        cancel() {
            this.open = false;
        }
    }"
    @confirm-action.window="show($event.detail)"
    @keydown.escape.window="if (open) cancel()"
    x-cloak
>
    <div x-show="open" class="fixed inset-0 z-[210] flex items-center justify-center p-4">
        {{-- Overlay --}}
        <div
            x-show="open"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"
            @click="cancel()"
        ></div>
        {{-- Dialog --}}
        <div
            x-show="open"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative z-10 w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden"
            @click.stop
        >
            <div class="p-5 sm:p-6">
                <div class="flex items-start gap-3">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full"
                        :class="variant === 'danger' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600'"
                    >
                        <iconify-icon
                            :icon="variant === 'danger' ? 'solar:trash-bin-trash-bold-duotone' : 'solar:danger-triangle-bold-duotone'"
                            width="22"
                        ></iconify-icon>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-slate-900" x-text="title"></h3>
                        <p class="mt-1 text-sm text-slate-600 break-words" x-text="message"></p>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-2 px-5 pb-5 sm:px-6 sm:pb-6">
                <button
                    type="button"
                    @click="cancel()"
                    class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors"
                >
                    {{ __('Annuler') }}
                </button>
                <button
                    type="button"
                    @click="confirm()"
                    class="px-4 py-2 rounded-xl text-white text-sm font-bold transition-colors inline-flex items-center gap-1.5"
                    :class="variant === 'danger' ? 'bg-red-600 hover:bg-red-700' : 'bg-amber-600 hover:bg-amber-700'"
                >
                    <iconify-icon
                        :icon="variant === 'danger' ? 'solar:trash-bin-trash-bold-duotone' : 'solar:check-circle-bold-duotone'"
                        width="16"
                    ></iconify-icon>
                    <span x-text="confirmLabel"></span>
                </button>
            </div>
        </div>
    </div>
</div>
