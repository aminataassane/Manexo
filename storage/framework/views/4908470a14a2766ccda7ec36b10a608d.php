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
        <div class="discussion-chat-stream mx-auto w-full max-w-4xl px-2 py-2 sm:px-5 sm:py-5" style="padding-left: max(0.5rem, env(safe-area-inset-left)); padding-right: max(0.5rem, env(safe-area-inset-right));">

            <!-- Tabs -->
            <div class="mb-3 sm:mb-4 w-full max-w-full overflow-x-auto scrollbar-hide">
                <div class="inline-flex items-center gap-0.5 sm:gap-1 rounded-xl border border-slate-200 bg-white p-0.5 sm:p-1 shadow-sm">
                <button type="button" @click="tab = 'discussion'" class="rounded-lg px-2.5 sm:px-4 py-1.5 text-[11px] sm:text-sm font-semibold transition-all touch-manipulation min-h-[34px] sm:min-h-[36px] whitespace-nowrap" :class="tab === 'discussion' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'">
                    <?php echo e(__('Discussion')); ?>

                </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canSeeInternalNotes): ?>
                    <button type="button" @click="tab = 'notes'" class="flex items-center gap-1 sm:gap-1.5 rounded-lg px-2.5 sm:px-4 py-1.5 text-[11px] sm:text-sm font-semibold transition-all touch-manipulation min-h-[34px] sm:min-h-[36px] whitespace-nowrap" :class="tab === 'notes' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'">
                        <iconify-icon icon="solar:lock-keyhole-bold-duotone" width="14"></iconify-icon>
                        <span><?php echo e(__('Notes internes')); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notesCount > 0): ?>
                            <span class="ml-0.5 sm:ml-1 px-1.5 py-0.5 rounded-full bg-slate-200 text-[10px]"><?php echo e($notesCount); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div id="discussion-tab-content" x-show="tab === 'discussion'" x-cloak class="space-y-4 sm:space-y-6">

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasMoreMessages): ?>
                    <div class="flex justify-center">
                        <button
                            type="button"
                            wire:click="loadMore"
                            wire:loading.attr="disabled"
                            wire:target="loadMore"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-sm"
                        >
                            <iconify-icon icon="solar:arrow-up-linear" width="14" wire:loading.remove wire:target="loadMore"></iconify-icon>
                            <span wire:loading.remove wire:target="loadMore"><?php echo e(__('Charger les messages précédents')); ?></span>
                            <span wire:loading wire:target="loadMore" class="inline-flex items-center gap-2">
                                <span class="inline-block h-3 w-3 rounded-full border-2 border-slate-400 border-t-transparent animate-spin"></span>
                                <?php echo e(__('Chargement...')); ?>

                            </span>
                        </button>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="discussion-thread relative min-w-0 overflow-hidden space-y-4 sm:space-y-6" data-timeline="discussion">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $itemsByDate; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <div class="flex flex-col gap-3 sm:gap-4">
                            <div class="flex items-center gap-2 sm:gap-3 my-1 sm:my-2 first:mt-0">
                                <span class="flex-1 h-px bg-slate-200" aria-hidden="true"></span>
                                <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-widest whitespace-nowrap"><?php echo e(\Carbon\Carbon::parse($date)->translatedFormat('l d F')); ?></span>
                                <span class="flex-1 h-px bg-slate-200" aria-hidden="true"></span>
                            </div>
                            <div class="space-y-1">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <?php echo $__env->make('livewire.tickets.partials.timeline-item', ['item' => $item], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div data-empty-discussion class="py-10 sm:py-16 text-center px-4">
                            <div class="inline-flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-3 sm:mb-4">
                                <iconify-icon icon="solar:chat-round-dots-linear" width="24" class="sm:hidden"></iconify-icon>
                                <iconify-icon icon="solar:chat-round-dots-linear" width="28" class="hidden sm:block"></iconify-icon>
                            </div>
                            <p class="text-sm sm:text-base font-semibold text-slate-700"><?php echo e(__('La discussion commence ici.')); ?></p>
                            <p class="mt-1.5 sm:mt-2 text-xs sm:text-sm text-slate-500"><?php echo e(__('Utilisez le formulaire ci-dessous pour envoyer un message.')); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canSeeInternalNotes): ?>
            <div id="notes-tab-content" x-show="tab === 'notes'" x-cloak class="space-y-6">
                <div class="discussion-thread relative min-w-0 overflow-hidden space-y-6" data-timeline="notes">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $noteItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <div class="flex flex-col gap-4">
                            <div class="flex items-center gap-3 my-2">
                                <span class="flex-1 h-px bg-amber-100" aria-hidden="true"></span>
                                <span class="text-[11px] font-bold text-amber-600/80 uppercase tracking-widest"><?php echo e(\Carbon\Carbon::parse($date)->translatedFormat('l d F')); ?></span>
                                <span class="flex-1 h-px bg-amber-100" aria-hidden="true"></span>
                            </div>
                            <div class="space-y-1">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <?php echo $__env->make('livewire.tickets.partials.timeline-item', ['item' => $item], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div data-empty-notes class="py-14 text-center">
                            <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-500 mb-4">
                                <iconify-icon icon="solar:lock-keyhole-linear" width="28"></iconify-icon>
                            </div>
                            <p class="text-base font-semibold text-slate-600"><?php echo e(__('Aucune note interne pour le moment.')); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
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
                :title="goToTopPrimary ? '<?php echo e(__('tickets.discussion_fab_go_top')); ?>' : '<?php echo e(__('tickets.discussion_fab_go_latest')); ?>'"
                :aria-label="goToTopPrimary ? '<?php echo e(__('tickets.discussion_fab_go_top')); ?>' : '<?php echo e(__('tickets.discussion_fab_go_latest')); ?>'"
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
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/livewire/tickets/partials/ticket-timeline.blade.php ENDPATH**/ ?>