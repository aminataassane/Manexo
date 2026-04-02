<div
    class="discussion-shell flex flex-col min-h-0 rounded-none sm:rounded-xl lg:rounded-2xl overflow-hidden bg-white border-0 sm:border border-slate-200 shadow-sm"
    style="height: calc(100dvh - var(--discussion-offset, 7rem)); min-height: 12rem; padding-bottom: env(safe-area-inset-bottom, 0);"
    x-init="
        $nextTick(() => {
            const shell = $el.closest('.manexo-shell-scroll');
            if (shell) {
                const shellStyle = getComputedStyle(shell);
                const shellRect = shell.getBoundingClientRect();
                const offset = shellRect.top + parseFloat(shellStyle.paddingTop);
                const gap = window.innerWidth < 640 ? '0px' : '1rem';
                $el.style.height = 'calc(100dvh - ' + offset + 'px - ' + gap + ')';
                shell.style.overflow = 'hidden';
            }
        });
    "
    x-data="discussionWebSocket('<?php echo e($ticketPublicId); ?>', <?php echo e(auth()->id() ?? 'null'); ?>, <?php echo e($canSeeInternalNotes ? 'true' : 'false'); ?>)"
    @keydown.escape.window="addParticipantOpen = false; $dispatch('close-attachment-preview')"
>
    <div
        class="flex flex-1 min-h-0 overflow-hidden flex-row"
        x-data="{
            sidebarOpen: true,
            mobileDrawerOpen: false,
            addParticipantOpen: false,
            init() {
                try {
                    const s = localStorage.getItem('ticket-sidebar-open');
                    if (s !== null) this.sidebarOpen = s === 'true';
                } catch (e) {}
            },
            toggleSidebar() {
                this.sidebarOpen = !this.sidebarOpen;
                try { localStorage.setItem('ticket-sidebar-open', this.sidebarOpen); } catch (e) {}
            },
            togglePanel() {
                if (window.innerWidth >= 1024) this.toggleSidebar();
                else this.mobileDrawerOpen = !this.mobileDrawerOpen;
            }
        }"
        x-init="init()"
        @keydown.escape.window="if (mobileDrawerOpen && window.innerWidth < 1024) mobileDrawerOpen = false"
    >
        
        <template x-teleport="body">
                <div
                    x-show="mobileDrawerOpen"
                    x-cloak
                    class="lg:hidden fixed inset-0 z-[200]"
                >
                    <div
                        class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"
                        x-show="mobileDrawerOpen"
                        x-transition:enter="ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        @click="mobileDrawerOpen = false"
                    ></div>
                    <section
                        x-show="mobileDrawerOpen"
                        x-transition:enter="ease-out duration-300 transform"
                        x-transition:enter-start="translate-x-full"
                        x-transition:enter-end="translate-x-0"
                        x-transition:leave="ease-in duration-200 transform"
                        x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="translate-x-full"
                        class="absolute inset-y-0 right-0 flex w-full max-w-full flex-col bg-white shadow-2xl sm:max-w-[26rem] md:max-w-[30rem] sm:rounded-l-2xl sm:border-l sm:border-slate-100"
                        @click.stop
                    >
                        <div class="flex h-14 shrink-0 items-center justify-between border-b border-slate-100 px-4">
                            <span class="text-sm font-bold text-slate-900"><?php echo e(__('Infos')); ?></span>
                            <button type="button" @click="mobileDrawerOpen = false" class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-900 transition-colors touch-manipulation" aria-label="<?php echo e(__('Fermer')); ?>">
                                <iconify-icon icon="solar:close-circle-bold" width="24"></iconify-icon>
                            </button>
                        </div>
                        <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar p-4" style="padding-bottom: max(1rem, env(safe-area-inset-bottom));">
                            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('tickets.ticket-sidebar', ['ticket-id' => $ticket->id,'ticket-public-id' => $ticketPublicId,'can-see-internal-notes' => $canSeeInternalNotes,'can-write-internal-notes' => $canWriteInternalNotes]);

$key = 'sidebar-mobile-'.e($ticket->id).'';
$__componentSlots = [];

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1682800250-12', $key);

$__html = app('livewire')->mount($__name, $__params, $key, $__componentSlots);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
                        </div>
                    </section>
                </div>
        </template>

        
        <section
            id="ticket-details-section"
            aria-label="<?php echo e(__('Détails du ticket')); ?>"
            class="hidden lg:flex shrink-0 flex-col bg-white border-slate-200 overflow-hidden h-full
                relative border-r flex-shrink-0 transition-[width] duration-300 ease-in-out"
            :class="{
                'w-0 border-r-0 overflow-hidden': !sidebarOpen,
                'w-[290px] xl:w-[310px]': sidebarOpen
            }"
        >
            <aside class="flex h-full min-h-0 min-w-0 w-full flex-col overflow-hidden">
                <div class="flex flex-col flex-1 min-w-0 min-h-0 w-[290px] xl:w-[310px]">
                    <div class="flex h-[60px] shrink-0 items-center justify-between border-b border-slate-100 px-5">
                        <span class="text-sm font-bold text-slate-900"><?php echo e(__('Infos')); ?></span>
                        <button type="button" @click="toggleSidebar()" class="text-slate-400 hover:text-slate-900 transition-colors" :title="sidebarOpen ? '<?php echo e(__('Fermer le panneau')); ?>' : '<?php echo e(__('Ouvrir le panneau')); ?>'">
                            <iconify-icon icon="solar:close-circle-linear" width="20"></iconify-icon>
                        </button>
                    </div>
                    <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar p-5" style="padding-bottom: max(1rem, env(safe-area-inset-bottom));">
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('tickets.ticket-sidebar', ['ticket-id' => $ticket->id,'ticket-public-id' => $ticketPublicId,'can-see-internal-notes' => $canSeeInternalNotes,'can-write-internal-notes' => $canWriteInternalNotes]);

$key = 'sidebar-'.e($ticket->id).'';
$__componentSlots = [];

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1682800250-13', $key);

$__html = app('livewire')->mount($__name, $__params, $key, $__componentSlots);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
                    </div>
                </div>
            </aside>
        </section>

        
        <section
            id="ticket-discussion-section"
            aria-label="<?php echo e(__('Discussion')); ?>"
            class="discussion-chat-panel flex-1 flex flex-col min-w-0 min-h-0 overflow-hidden bg-white"
        >
            <!-- Header -->
            <header class="sticky top-0 z-20 shrink-0 bg-white/95 border-b border-slate-200 px-2.5 py-1.5 sm:px-5 sm:py-2 backdrop-blur safe-area-inset-top" style="padding-top: max(0.375rem, env(safe-area-inset-top));">
                <div class="flex items-center justify-between gap-1.5 sm:gap-3">
                    <nav class="flex items-center gap-1 sm:gap-2 min-w-0 flex-1 text-xs sm:text-sm overflow-hidden">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($embedded ?? false): ?>
                            <a href="<?php echo e(route('discussions.index', ['discussionParam' => $ticket->public_id])); ?>" wire:navigate class="inline-flex items-center gap-1 shrink-0 text-slate-500 hover:text-slate-900 transition-colors touch-manipulation py-1">
                                <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
                                <span class="hidden sm:inline font-medium"><?php echo e(__('Retour')); ?></span>
                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('tickets.index')); ?>" wire:navigate class="inline-flex items-center gap-1 shrink-0 text-slate-500 hover:text-slate-900 transition-colors touch-manipulation py-1">
                                <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
                                <span class="hidden sm:inline font-medium"><?php echo e(__('Retour')); ?></span>
                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <span class="text-slate-300 shrink-0 hidden sm:inline">/</span>
                        <div class="flex items-center gap-1 sm:gap-2 min-w-0 flex-1 overflow-hidden">
                            <span class="font-mono text-[10px] sm:text-xs font-bold text-slate-400 shrink-0"><?php echo e($ticket->shortReference()); ?></span>
                            <span class="font-semibold text-slate-900 truncate min-w-0 text-xs sm:text-sm"><?php echo e($ticket->subject); ?></span>
                            <span
                                x-show="!wsConnected"
                                x-cloak
                                class="h-2 w-2 rounded-full bg-amber-400 shrink-0 animate-pulse"
                                title="<?php echo e(__('Connexion en cours…')); ?>"
                            ></span>
                        </div>
                    </nav>
                    <div class="flex items-center gap-1 sm:gap-2 shrink-0">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($embedded ?? false): ?>
                            <a
                                href="<?php echo e(route('tickets.discussion', $ticket)); ?>"
                                class="inline-flex items-center gap-1.5 h-9 px-2.5 sm:px-3 rounded-lg border border-slate-200 bg-white text-slate-700 text-xs sm:text-sm font-medium hover:bg-slate-50 hover:text-slate-900 transition-colors"
                                title="<?php echo e(__('pages.discussions.open_full_ticket')); ?>"
                            >
                                <iconify-icon icon="solar:maximize-square-linear" width="18" class="shrink-0"></iconify-icon>
                                <span class="hidden sm:inline"><?php echo e(__('pages.discussions.open_full_ticket')); ?></span>
                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <button type="button" @click="mobileDrawerOpen = !mobileDrawerOpen" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-colors lg:hidden" title="<?php echo e(__('Infos ticket')); ?>">
                            <iconify-icon icon="solar:sidebar-minimalistic-linear" width="20"></iconify-icon>
                        </button>
                        <button type="button" @click="toggleSidebar()" class="hidden lg:flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-colors" :title="sidebarOpen ? '<?php echo e(__('Fermer le panneau')); ?>' : '<?php echo e(__('Ouvrir le panneau Infos')); ?>'">
                            <iconify-icon icon="solar:sidebar-minimalistic-linear" width="20" class="transition-transform" :class="sidebarOpen ? 'rotate-180' : ''"></iconify-icon>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Timeline (isolated sub-component with cursor pagination) -->
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('tickets.ticket-timeline', ['ticket-id' => $ticket->id,'ticket-public-id' => $ticketPublicId,'ticket-creator-id' => (int) $ticket->created_by,'can-see-internal-notes' => $canSeeInternalNotes]);

$key = 'timeline-'.e($ticket->id).'';
$__componentSlots = [];

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1682800250-14', $key);

$__html = app('livewire')->mount($__name, $__params, $key, $__componentSlots);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>

            <!-- Composer (isolated sub-component) -->
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('tickets.ticket-composer', ['ticket-id' => $ticket->id,'ticket-public-id' => $ticketPublicId,'can-write-internal-notes' => $canWriteInternalNotes,'is-locked' => $isLocked,'mentionable-users' => $mentionableUsers ?? []]);

$key = 'composer-'.e($ticket->id).'';
$__componentSlots = [];

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1682800250-15', $key);

$__html = app('livewire')->mount($__name, $__params, $key, $__componentSlots);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
        </section>

    </div>

    
    <div x-data="attachmentLightbox()" x-cloak
         @open-attachment-preview.window="openPreview($event.detail)"
         @keydown.escape.window="close()"
         @keydown.left.window="prev()"
         @keydown.right.window="next()"
         x-show="open"
         class="fixed inset-0 z-[100]"
         style="display: none;">

        
        <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" @click="close()"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        
        <div class="absolute inset-0 flex flex-col items-center justify-center p-4 sm:p-8 pointer-events-none">

            
            <div class="w-full max-w-4xl flex items-center justify-between gap-4 mb-3 pointer-events-auto" x-show="open"
                 x-transition:enter="transition ease-out duration-200 delay-75" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="text-sm font-medium text-white truncate min-w-0" x-text="name"></span>
                    <span x-show="gallery.length > 1" class="text-xs text-white/60 shrink-0" x-text="(currentIndex + 1) + ' / ' + gallery.length"></span>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a :href="url" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white text-slate-700 hover:bg-slate-100 text-xs font-semibold transition-colors shadow-sm">
                        <iconify-icon icon="solar:square-top-down-linear" width="14"></iconify-icon>
                        <span class="hidden sm:inline">Ouvrir</span>
                    </a>
                    <a :href="downloadUrl"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white text-slate-700 hover:bg-slate-100 text-xs font-semibold transition-colors shadow-sm">
                        <iconify-icon icon="solar:download-minimalistic-linear" width="14"></iconify-icon>
                        <span class="hidden sm:inline">Télécharger</span>
                    </a>
                    <button @click="close()" class="p-2 rounded-xl bg-white/20 hover:bg-white/40 text-white transition-colors ml-0.5">
                        <iconify-icon icon="solar:close-circle-linear" width="18"></iconify-icon>
                    </button>
                </div>
            </div>

            
            <div class="relative flex items-center justify-center flex-1 min-h-0 w-full pointer-events-auto" x-show="open"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

                
                <button x-show="gallery.length > 1" @click.stop="prev()"
                        class="absolute left-2 sm:left-4 z-20 w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-white/15 hover:bg-white/30 text-white flex items-center justify-center transition-colors backdrop-blur-sm shadow-lg">
                    <iconify-icon icon="solar:alt-arrow-left-linear" width="20"></iconify-icon>
                </button>

                
                <template x-if="type === 'image'">
                    <img :src="url" :alt="name"
                         class="max-w-full max-h-full object-contain rounded-2xl shadow-2xl bg-white/5 select-none"
                         @click.stop>
                </template>

                
                <template x-if="type === 'pdf'">
                    <iframe :src="url" class="w-full h-full rounded-2xl shadow-2xl bg-white" @click.stop></iframe>
                </template>

                
                <button x-show="gallery.length > 1" @click.stop="next()"
                        class="absolute right-2 sm:right-4 z-20 w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-white/15 hover:bg-white/30 text-white flex items-center justify-center transition-colors backdrop-blur-sm shadow-lg">
                    <iconify-icon icon="solar:alt-arrow-right-linear" width="20"></iconify-icon>
                </button>
            </div>

            
            <div x-show="gallery.length > 1" class="mt-3 flex items-center gap-2 pointer-events-auto overflow-x-auto max-w-full pb-1">
                <template x-for="(item, i) in gallery" :key="i">
                    <button @click="goTo(i)" class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl overflow-hidden border-2 shrink-0 transition-all"
                            :class="i === currentIndex ? 'border-white shadow-lg scale-105' : 'border-white/30 opacity-60 hover:opacity-90'">
                        <img :src="item.url" :alt="item.name" class="w-full h-full object-cover">
                    </button>
                </template>
            </div>
        </div>
    </div>
</div>

    <?php
        $__scriptKey = '1682800250-3';
        ob_start();
    ?>
<script>
    Alpine.data('attachmentLightbox', () => ({
        open: false,
        url: '',
        name: '',
        type: '',
        downloadUrl: '',
        gallery: [],
        currentIndex: 0,

        openPreview(detail) {
            this.type = detail.type || 'file';
            this.gallery = Array.isArray(detail.gallery) && detail.gallery.length > 0 ? detail.gallery : [{ url: detail.url, name: detail.name }];
            this.currentIndex = typeof detail.index === 'number' ? detail.index : 0;
            this.applyCurrentItem();
            this.open = true;
            document.body.style.overflow = 'hidden';
        },
        applyCurrentItem() {
            const item = this.gallery[this.currentIndex];
            if (!item) return;
            this.url = item.url;
            this.name = item.name;
            this.downloadUrl = item.url + (item.url.includes('?') ? '&' : '?') + 'download=1';
        },
        close() {
            this.open = false;
            document.body.style.overflow = '';
        },
        next() {
            if (this.gallery.length <= 1) return;
            this.currentIndex = (this.currentIndex + 1) % this.gallery.length;
            this.applyCurrentItem();
        },
        prev() {
            if (this.gallery.length <= 1) return;
            this.currentIndex = (this.currentIndex - 1 + this.gallery.length) % this.gallery.length;
            this.applyCurrentItem();
        },
        goTo(i) {
            this.currentIndex = i;
            this.applyCurrentItem();
        }
    }));

    Alpine.data('discussionWebSocket', (ticketPublicId, currentUserId, canSeeInternalNotes) => ({
        ticketPublicId,
        currentUserId,
        canSeeInternalNotes: !!canSeeInternalNotes,
        seenIds: new Set(),
        wsConnected: false,
        init() {
            const tryConnect = () => {
                if (typeof window.Echo !== 'undefined') {
                    const chan = window.Echo.private('ticket.' + this.ticketPublicId);
                    chan.listen('.message.sent', (e) => this.appendMessage(e));
                    chan.subscribed(() => { this.wsConnected = true; });

                    if (this.canSeeInternalNotes) {
                        window.Echo.private('ticket.staff.' + this.ticketPublicId)
                            .listen('.message.sent', (e) => this.appendMessage(e));
                    }
                    return;
                }
                setTimeout(tryConnect, 300);
            };
            tryConnect();
        },
        appendMessage(e) {
            if (e.id && (this.seenIds.has(e.id) || document.getElementById('message-' + e.id))) {
                return;
            }
            if (e.id) this.seenIds.add(e.id);

            const isNote = e.type === 'internal_note';
            const targets = [];
            if (!isNote) targets.push('discussion');
            if (isNote) { targets.push('discussion'); targets.push('notes'); }

            for (const kind of targets) {
                const containerId = kind === 'notes' ? 'notes-tab-content' : 'discussion-tab-content';
                const container = document.getElementById(containerId);
                const fallback = document.getElementById('discussion-messages');
                const parent = container || fallback;
                if (!parent) continue;

                const empty = parent.querySelector('[data-empty-discussion]') || parent.querySelector('[data-empty-notes]');
                if (empty) empty.remove();

                const timeline = parent.querySelector('[data-timeline="' + kind + '"]');
                if (!timeline) continue;

                const lastDateGroup = timeline.querySelector('.flex.flex-col.gap-4:last-child');
                const messageContainer = lastDateGroup && lastDateGroup.querySelector('.space-y-1') ? lastDateGroup.querySelector('.space-y-1') : timeline;

                const div = document.createElement('div');
                div.className = 'animate-enter';
                if (e.id) div.id = 'message-' + e.id;
                div.innerHTML = this.renderBubble(e);
                messageContainer.appendChild(div);
            }

            const scroll = document.getElementById('discussion-messages');
            if (scroll) scroll.scrollTop = scroll.scrollHeight;
        },
        fileUrl(att) {
            if (!att || !att.path) return '#';
            const origin = window.location.origin;
            const path = String(att.path);
            if (path.startsWith('ticket-messages/')) {
                const filename = path.split('/').pop();
                return origin + '/tickets/' + this.ticketPublicId + '/files/' + encodeURIComponent(filename);
            }
            if (path.startsWith('ticket-attachments/')) {
                const filename = path.split('/').pop();
                return origin + '/tickets/' + this.ticketPublicId + '/attachment/' + encodeURIComponent(filename);
            }
            if (att.url) return att.url;
            return origin + '/storage/' + path;
        },
        fileIcon(ext) {
            if (['doc','docx'].includes(ext)) return 'solar:document-text-linear';
            if (['xls','xlsx','csv'].includes(ext)) return 'solar:chart-square-linear';
            if (['zip','rar','7z'].includes(ext)) return 'solar:archive-linear';
            if (ext === 'txt') return 'solar:notes-linear';
            return 'solar:file-text-linear';
        },
        attachmentsHtml(attachments, variant) {
            if (!Array.isArray(attachments) || attachments.length === 0) return '';
            const imgExts = ['png','jpg','jpeg','gif','webp'];
            const images = [];
            const files = [];

            attachments.forEach(a => {
                const ext = (a.name || '').split('.').pop().toLowerCase();
                if (imgExts.includes(ext)) images.push(a);
                else files.push(a);
            });

            if (images.length === 0 && files.length === 0) return '';

            const borderTone = variant === 'note' ? 'border-amber-200/45' : variant === 'mine' ? 'border-[color:color-mix(in_srgb,var(--accent)_22%,#e2e8f0)]' : 'border-slate-200/45';
            let html = `<div class="mt-2 border-t border-dashed pt-2.5 ${borderTone}">`;

            // Images grid
            if (images.length > 0) {
                const gallery = images.map(a => ({ url: this.fileUrl(a), name: this.escapeHtml(a.name || 'Image') }));
                const galleryJson = JSON.stringify(gallery).replace(/'/g, '&#39;').replace(/"/g, '&quot;');
                const maxVisible = 4;
                const extra = Math.max(0, images.length - maxVisible);
                const gridCols = images.length === 1 ? 'grid-cols-1 max-w-[280px]' : 'grid-cols-2 max-w-[340px]';
                const imgTileClass = variant === 'mine'
                    ? 'border border-[color:color-mix(in_srgb,var(--accent)_18%,#e2e8f0)] bg-[color-mix(in_srgb,var(--accent-soft)_45%,white)]'
                    : variant === 'note'
                        ? 'border border-amber-200/50 bg-amber-50/40'
                        : 'border border-slate-200/55 bg-slate-50';

                html += `<div class="grid ${gridCols} gap-1.5 ${files.length > 0 ? 'mb-2.5' : ''}">`;

                images.forEach((a, idx) => {
                    if (idx >= maxVisible) return;
                    const url = this.fileUrl(a);
                    const dlUrl = url + (url.includes('?') ? '&' : '?') + 'download=1';
                    const name = this.escapeHtml(a.name || 'Image');
                    const aspectClass = images.length === 1 ? 'max-h-[220px]' : (images.length === 3 && idx === 0 ? 'row-span-2 aspect-[3/4]' : 'aspect-square');

                    html += `<div class="${aspectClass} relative overflow-hidden rounded-xl group/img cursor-pointer ${imgTileClass}"
                        onclick="window.dispatchEvent(new CustomEvent('open-attachment-preview', { detail: { type: 'image', index: ${idx}, gallery: ${galleryJson} } }))">
                        <img src="${url}" alt="${name}" class="block w-full h-full object-cover transition-transform duration-200 group-hover/img:scale-105" loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent opacity-0 group-hover/img:opacity-100 transition-opacity duration-200 flex flex-col items-start justify-end p-2.5">
                            <span class="text-[11px] font-medium text-white truncate max-w-full drop-shadow">${name}</span>
                        </div>
                        <div class="absolute top-1.5 right-1.5 opacity-0 group-hover/img:opacity-100 transition-opacity duration-200">
                            <a href="${dlUrl}" onclick="event.stopPropagation()" title="Télécharger" class="w-7 h-7 rounded-lg bg-white/85 hover:bg-white text-slate-600 flex items-center justify-center shadow-sm backdrop-blur-sm transition-colors">
                                <iconify-icon icon="solar:download-minimalistic-linear" width="13"></iconify-icon>
                            </a>
                        </div>`;

                    if (idx === maxVisible - 1 && extra > 0) {
                        html += `<div class="absolute inset-0 bg-slate-900/50 flex items-center justify-center pointer-events-none rounded-xl"><span class="text-white text-xl font-bold drop-shadow">+${extra}</span></div>`;
                    }
                    html += `</div>`;
                });
                html += `</div>`;
            }

            // Files stack
            if (files.length > 0) {
                const fileRow = variant === 'note' ? 'bg-amber-200/20 hover:bg-amber-200/30' : variant === 'mine' ? 'bg-[color-mix(in_srgb,var(--accent)_14%,white)] hover:bg-[color-mix(in_srgb,var(--accent)_20%,white)]' : 'bg-slate-900/[0.025] hover:bg-slate-900/[0.045]';
                const iconWrap = variant === 'note' ? 'bg-amber-100/50 text-amber-800/60 ring-amber-200/30' : variant === 'mine' ? 'bg-white/95 text-[var(--accent)] shadow-[0_1px_2px_rgba(15,23,42,0.05)] ring-[color:color-mix(in_srgb,var(--accent)_32%,transparent)]' : 'bg-white/90 text-slate-500 ring-slate-900/[0.06] shadow-[0_1px_2px_rgba(15,23,42,0.04)]';
                const nameCls = variant === 'note' ? 'text-amber-950/90' : variant === 'mine' ? 'text-slate-800' : 'text-slate-700';
                const metaCls = variant === 'note' ? 'text-amber-800/55' : variant === 'mine' ? 'text-slate-600/85' : 'text-slate-400';
                const pillCls = variant === 'note' ? 'bg-amber-50/90 ring-amber-200/35' : variant === 'mine' ? 'bg-white/95 ring-1 ring-[color:color-mix(in_srgb,var(--accent)_24%,transparent)]' : 'bg-white/75 ring-slate-900/[0.05] sm:bg-white/85';
                const actionCls = variant === 'mine' ? 'flex h-8 w-8 items-center justify-center rounded-full text-[var(--accent-dark)] transition-colors hover:bg-[var(--accent-soft)]' : 'flex h-8 w-8 items-center justify-center rounded-full text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-800';
                html += `<div class="space-y-2">`;
                files.forEach(a => {
                    const url = this.fileUrl(a);
                    const dlUrl = url + (url.includes('?') ? '&' : '?') + 'download=1';
                    const name = this.escapeHtml(a.name || 'Fichier');
                    const ext = (a.name || '').split('.').pop().toLowerCase();
                    const isPdf = ext === 'pdf';
                    const icon = this.fileIcon(ext);
                    const extLabel = ext.toUpperCase();
                    let sizeStr = '';
                    if (a.size) {
                        const b = parseInt(a.size, 10);
                        if (!isNaN(b) && b > 0) {
                            sizeStr = b >= 1048576 ? (b / 1048576).toFixed(1) + ' MB' : (b / 1024).toFixed(1) + ' KB';
                        }
                    }
                    const metaBits = sizeStr
                        ? `<span class="uppercase tracking-wide">${extLabel}</span><span class="mx-1 opacity-40">·</span><span class="tabular-nums">${sizeStr}</span>`
                        : `<span class="uppercase tracking-wide">${extLabel}</span>`;
                    const openBtn = isPdf
                        ? `<button type="button" onclick="window.dispatchEvent(new CustomEvent('open-attachment-preview', { detail: { url: '${url}', name: '${name.replace(/'/g, "\\'")}', type: 'pdf' } }))" title="Aperçu" class="${actionCls}"><iconify-icon icon="solar:eye-linear" width="16"></iconify-icon></button>`
                        : `<a href="${url}" target="_blank" rel="noopener" title="Ouvrir" class="${actionCls}"><iconify-icon icon="solar:square-top-down-linear" width="16"></iconify-icon></a>`;

                    html += `<div class="attachment-file-row flex max-w-full cursor-default items-center gap-2.5 rounded-2xl px-2.5 py-2 transition-colors duration-200 sm:gap-3 sm:px-3 sm:py-2.5 ${fileRow} group/file">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl ring-1 ${iconWrap}"><iconify-icon icon="${icon}" width="18" class="opacity-90"></iconify-icon></div>
                        <div class="min-w-0 flex-1 py-0.5">
                            <p class="truncate text-[13px] font-medium leading-snug tracking-tight ${nameCls}">${name}</p>
                            <p class="mt-0.5 text-[11px] font-normal ${metaCls}">${metaBits}</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-0.5 self-center rounded-full p-0.5 shadow-sm ring-1 backdrop-blur-[2px] transition-opacity duration-200 opacity-80 group-hover/file:opacity-100 ${pillCls}">
                            ${openBtn}
                            <a href="${dlUrl}" title="Télécharger" class="${actionCls}"><iconify-icon icon="solar:download-minimalistic-linear" width="16"></iconify-icon></a>
                        </div>
                    </div>`;
                });
                html += `</div>`;
            }

            html += `</div>`;
            return html;
        },
        renderBubble(e) {
            const isNote = e.type === 'internal_note';
            const isSystem = e.type === 'system';
            const isOwn = e.user_id && parseInt(e.user_id, 10) === parseInt(this.currentUserId, 10);
            const timeAgo = e.created_at ? (function(d){const s=Math.floor((Date.now()-new Date(d))/1000); return s<60?'à l\'instant':s<3600?Math.floor(s/60)+' min':s<86400?Math.floor(s/3600)+' h':Math.floor(s/86400)+' j';})(e.created_at) : '';
            const name = this.escapeHtml(e.user_name || '');
            const body = this.escapeHtml(e.body || '').replace(/\n/g, '<br>');
            const avatarUrl = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name || 'U') + '&size=40&background=e2e8f0&color=475569';
            const hasAtt = (e.attachments && e.attachments.length > 0);
            const attVariant = isNote ? 'note' : (isOwn ? 'mine' : 'theirs');
            const attBlock = this.attachmentsHtml(e.attachments || [], attVariant);
            const attPad = hasAtt ? `<div class="px-3 sm:px-4 pb-3">${attBlock}</div>` : '';

            if (isSystem) {
                return `<div class="message-row message-system flex justify-center py-3"><div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 text-slate-600 text-xs"><iconify-icon icon="solar:info-circle-linear" width="14" class="shrink-0 text-slate-500"></iconify-icon><span class="break-words max-w-[min(100%,28rem)]">${body}</span><span class="text-slate-400 shrink-0">· ${timeAgo}</span></div></div>`;
            }
            if (isNote) {
                return `<div class="message-row message-note flex gap-3 py-3 max-w-[85%]"><div class="w-8 h-8 shrink-0 rounded-full bg-amber-100 flex items-center justify-center text-amber-600"><iconify-icon icon="solar:lock-keyhole-linear" width="14"></iconify-icon></div><div class="message-bubble flex-1 min-w-0 rounded-2xl rounded-tl-md bg-amber-50/90 border border-amber-200/80 shadow-sm overflow-hidden"><div class="px-4 py-2.5 flex flex-wrap items-center justify-between gap-2 border-b border-amber-200/60 bg-amber-50/50"><div class="flex items-center gap-2"><span class="text-[11px] font-bold text-amber-800 uppercase tracking-wide">Note interne</span><span class="text-[10px] text-amber-600">${name}</span></div><span class="text-[10px] text-amber-600/90">${timeAgo}</span></div><div class="px-4 py-3 text-sm leading-relaxed text-amber-900 break-words">${body}</div>${attPad}</div></div>`;
            }
            if (isOwn) {
                const bubbleCls = 'rounded-2xl rounded-br-md text-sm leading-relaxed shadow-md w-full max-w-full overflow-hidden';
                const bubbleStyle = hasAtt
                    ? ' style="background: color-mix(in srgb, var(--accent) 8%, white); border: 1px solid color-mix(in srgb, var(--accent) 18%, #e2e8f0);"'
                    : ' style="background: linear-gradient(135deg, var(--accent) 0%, color-mix(in srgb, var(--accent) 85%, #1e293b) 100%); color: #fff;"';
                const textCls = hasAtt ? 'text-slate-800' : 'text-white';
                return `<div class="message-row message-own flex justify-end py-3"><div class="flex items-end gap-3 max-w-[85%] min-w-0 flex-row-reverse"><div class="w-9 h-9 shrink-0 rounded-full ring-2 ring-white shadow-md overflow-hidden bg-slate-100"><img src="${avatarUrl}" class="w-full h-full object-cover" alt=""></div><div class="flex flex-col items-end min-w-0 max-w-full"><div class="flex items-center gap-2 mb-1.5 flex-row-reverse"><span class="text-xs font-semibold text-slate-800">${name}</span><span class="text-[10px] text-slate-400">${timeAgo}</span></div><div class="message-bubble ${bubbleCls}"${bubbleStyle}><div class="px-3 py-2.5 sm:px-4 sm:py-3 text-left break-words ${textCls}">${body}</div>${attPad}</div></div></div></div>`;
            }
            return `<div class="message-row message-incoming flex gap-3 py-3 min-w-0 max-w-[85%]"><div class="w-9 h-9 shrink-0 rounded-full ring-2 ring-white shadow-md overflow-hidden bg-slate-100"><img src="${avatarUrl}" class="w-full h-full object-cover" alt=""></div><div class="min-w-0 max-w-full w-fit"><div class="flex flex-wrap items-center gap-2 mb-1.5"><span class="text-xs font-semibold text-slate-800">${name}</span><span class="text-[10px] text-slate-400 ml-auto">${timeAgo}</span></div><div class="message-bubble rounded-2xl rounded-bl-md text-sm leading-relaxed bg-white border border-slate-200 shadow-sm break-words w-fit max-w-full min-w-0 overflow-hidden"><div class="px-3 py-2.5 sm:px-4 sm:py-3">${body}</div>${attPad}</div></div></div>`;
        },
        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text ?? '';
            return div.innerHTML;
        }
    }));
</script>
    <?php
        $__output = ob_get_clean();

        \Livewire\store($this)->push('scripts', $__output, $__scriptKey)
    ?><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\tickets\discussion.blade.php ENDPATH**/ ?>