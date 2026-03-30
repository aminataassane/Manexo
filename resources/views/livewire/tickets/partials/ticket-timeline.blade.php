<div
    class="relative flex-1 min-h-0 flex flex-col min-w-0"
    x-data="{
        tab: 'discussion',
        isScrollable: false,
        goToTopPrimary: false,
        checkScroll() {
            const el = this.$refs.scrollArea;
            if (!el) return;
            const gap = el.scrollHeight - el.clientHeight;
            this.isScrollable = gap > 24;
            if (!this.isScrollable) {
                this.goToTopPrimary = false;
                return;
            }
            const maxScroll = gap;
            const distFromBottom = maxScroll - el.scrollTop;
            const dynamicThreshold = Math.max(100, el.clientHeight * 0.15);
            this.goToTopPrimary = distFromBottom < dynamicThreshold;
        },
        jumpToTop() {
            this.$refs.scrollArea?.scrollTo({ top: 0, behavior: 'smooth' });
        },
        jumpToBottom() {
            const el = this.$refs.scrollArea;
            if (el) el.scrollTo({ top: el.scrollHeight, behavior: 'smooth' });
        },
    }"
    x-init="$nextTick(() => {
        checkScroll();
        const el = $refs.scrollArea;
        if (el) new ResizeObserver(() => checkScroll()).observe(el);
    })"
    @resize.window="$nextTick(() => checkScroll())"
>
    <div
        id="discussion-messages"
        x-ref="scrollArea"
        class="discussion-chat-scroll flex-1 min-h-0 overflow-y-auto overflow-x-hidden custom-scrollbar overscroll-contain"
        x-on:scroll.throttle.100ms="checkScroll()"
        x-on:new-message-received.window="$nextTick(() => {
            const el = $refs.scrollArea;
            if (el) {
                el.scrollTop = el.scrollHeight;
                checkScroll();
            }
        })"
    >
        <div class="discussion-chat-stream mx-auto w-full max-w-4xl px-3 py-3 sm:px-5 sm:py-5" style="padding-left: max(0.75rem, env(safe-area-inset-left)); padding-right: max(0.75rem, env(safe-area-inset-right));">

            <!-- Tabs -->
            <div class="mb-4 w-full max-w-full">
                <div class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white p-1 shadow-sm">
                <button type="button" @click="tab = 'discussion'" class="rounded-lg px-3 sm:px-4 py-1.5 text-xs sm:text-sm font-semibold transition-all touch-manipulation min-h-[36px]" :class="tab === 'discussion' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'">
                    {{ __('Discussion') }}
                </button>
                @if($canSeeInternalNotes)
                    <button type="button" @click="tab = 'notes'" class="flex items-center gap-1.5 rounded-lg px-3 sm:px-4 py-1.5 text-xs sm:text-sm font-semibold transition-all touch-manipulation min-h-[36px]" :class="tab === 'notes' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'">
                        <iconify-icon icon="solar:lock-keyhole-bold-duotone" width="14"></iconify-icon>
                        <span class="whitespace-nowrap">{{ __('Notes internes') }}</span>
                        @if($notesCount > 0)
                            <span class="ml-1 px-1.5 py-0.5 rounded-full bg-slate-200 text-[10px]">{{ $notesCount }}</span>
                        @endif
                    </button>
                @endif
                </div>
            </div>

            {{-- Discussion Tab --}}
            <div id="discussion-tab-content" x-show="tab === 'discussion'" x-cloak class="space-y-4 sm:space-y-6">

                {{-- Load more button --}}
                @if($hasMoreMessages)
                    <div class="flex justify-center">
                        <button
                            type="button"
                            wire:click="loadMore"
                            wire:loading.attr="disabled"
                            wire:target="loadMore"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-sm"
                        >
                            <iconify-icon icon="solar:arrow-up-linear" width="14" wire:loading.remove wire:target="loadMore"></iconify-icon>
                            <span wire:loading.remove wire:target="loadMore">{{ __('Charger les messages précédents') }}</span>
                            <span wire:loading wire:target="loadMore" class="inline-flex items-center gap-2">
                                <span class="inline-block h-3 w-3 rounded-full border-2 border-slate-400 border-t-transparent animate-spin"></span>
                                {{ __('Chargement...') }}
                            </span>
                        </button>
                    </div>
                @endif

                <div class="discussion-thread relative min-w-0 overflow-hidden space-y-6" data-timeline="discussion">
                    @forelse($itemsByDate as $date => $items)
                        <div class="flex flex-col gap-4">
                            <div class="flex items-center gap-3 my-2 first:mt-0">
                                <span class="flex-1 h-px bg-slate-200" aria-hidden="true"></span>
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">{{ \Carbon\Carbon::parse($date)->translatedFormat('l d F') }}</span>
                                <span class="flex-1 h-px bg-slate-200" aria-hidden="true"></span>
                            </div>
                            <div class="space-y-1">
                                @foreach($items as $item)
                                    @include('livewire.tickets.partials.timeline-item', ['item' => $item])
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div data-empty-discussion class="py-14 sm:py-16 text-center">
                            <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-4">
                                <iconify-icon icon="solar:chat-round-dots-linear" width="28"></iconify-icon>
                            </div>
                            <p class="text-base font-semibold text-slate-700">{{ __('La discussion commence ici.') }}</p>
                            <p class="mt-2 text-sm text-slate-500">{{ __('Utilisez le formulaire ci-dessous pour envoyer un message.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Notes Tab --}}
            @if($canSeeInternalNotes)
            <div id="notes-tab-content" x-show="tab === 'notes'" x-cloak class="space-y-6">
                <div class="discussion-thread relative min-w-0 overflow-hidden space-y-6" data-timeline="notes">
                    @forelse($noteItems as $date => $items)
                        <div class="flex flex-col gap-4">
                            <div class="flex items-center gap-3 my-2">
                                <span class="flex-1 h-px bg-amber-100" aria-hidden="true"></span>
                                <span class="text-[11px] font-bold text-amber-600/80 uppercase tracking-widest">{{ \Carbon\Carbon::parse($date)->translatedFormat('l d F') }}</span>
                                <span class="flex-1 h-px bg-amber-100" aria-hidden="true"></span>
                            </div>
                            <div class="space-y-1">
                                @foreach($items as $item)
                                    @include('livewire.tickets.partials.timeline-item', ['item' => $item])
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div data-empty-notes class="py-14 text-center">
                            <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-500 mb-4">
                                <iconify-icon icon="solar:lock-keyhole-linear" width="28"></iconify-icon>
                            </div>
                            <p class="text-base font-semibold text-slate-600">{{ __('Aucune note interne pour le moment.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Navigation fil : un seul FAB, fixe dans la zone (ne scrolle pas), icône / action selon la position --}}
    <div class="pointer-events-none absolute inset-0 z-30 flex items-end justify-end" aria-hidden="true">
        <div
            class="pointer-events-auto flex flex-col items-end gap-2 pr-2 pb-2 sm:pr-3 sm:pb-3"
            style="padding-bottom: max(0.35rem, env(safe-area-inset-bottom, 0px)); padding-right: max(0.35rem, env(safe-area-inset-right, 0px));"
        >
            <button
                type="button"
                x-show="isScrollable"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="goToTopPrimary ? jumpToTop() : jumpToBottom()"
                :title="goToTopPrimary ? '{{ __('tickets.discussion_fab_go_top') }}' : '{{ __('tickets.discussion_fab_go_latest') }}'"
                :aria-label="goToTopPrimary ? '{{ __('tickets.discussion_fab_go_top') }}' : '{{ __('tickets.discussion_fab_go_latest') }}'"
                class="flex h-11 w-11 sm:h-12 sm:w-12 items-center justify-center rounded-full border border-slate-200/90 bg-white/95 text-slate-600 shadow-lg shadow-slate-900/10 backdrop-blur-md ring-1 ring-black/5 hover:bg-white hover:text-[var(--accent,#0d9488)] hover:shadow-xl hover:ring-[var(--accent,#0d9488)]/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--accent,#0d9488)]/40 focus-visible:ring-offset-2 transition-all duration-200 touch-manipulation"
            >
                <iconify-icon
                    :icon="goToTopPrimary ? 'solar:arrow-up-linear' : 'solar:arrow-down-linear'"
                    width="22"
                    class="shrink-0 transition-transform duration-200"
                ></iconify-icon>
            </button>
        </div>
    </div>
</div>
