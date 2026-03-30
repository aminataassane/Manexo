<div
    class="discussion-shell flex flex-col min-h-0 rounded-none sm:rounded-xl lg:rounded-2xl overflow-hidden bg-white border-0 sm:border border-slate-200 shadow-sm"
    style="height: calc(100dvh - var(--discussion-offset, 7rem)); min-height: 12rem; padding-bottom: env(safe-area-inset-bottom, 0);"
    x-init="
        // Calculate exact offset: topbar + shell padding + content wrap spacing
        $nextTick(() => {
            const shell = $el.closest('.manexo-shell-scroll');
            if (shell) {
                const shellStyle = getComputedStyle(shell);
                const shellRect = shell.getBoundingClientRect();
                const offset = shellRect.top + parseFloat(shellStyle.paddingTop);
                $el.style.height = 'calc(100dvh - ' + offset + 'px - 1rem)';
                // Prevent parent from scrolling
                shell.style.overflow = 'hidden';
            }
        });
    "
    x-data="discussionWebSocket('{{ $ticketPublicId }}', {{ auth()->id() ?? 'null' }}, {{ $canSeeInternalNotes ? 'true' : 'false' }})"
    @keydown.escape.window="addParticipantOpen = false"
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
    >
        {{-- SIDEBAR (isolated sub-component) --}}
        <section id="ticket-details-section" aria-label="{{ __('Détails du ticket') }}" class="hidden lg:block shrink-0">
            <aside class="flex shrink-0 flex-col bg-white border-r border-slate-200 overflow-hidden transition-[width] duration-300 ease-in-out h-full" :class="sidebarOpen ? 'w-[290px] xl:w-[310px]' : 'w-0 border-r-0'">
                <div class="flex flex-col flex-1 min-w-0 min-h-0 w-[290px] xl:w-[310px]">
                    <div class="flex h-[60px] shrink-0 items-center justify-between border-b border-slate-100 px-5">
                        <span class="text-sm font-bold text-slate-900">{{ __('Infos') }}</span>
                        <button type="button" @click="toggleSidebar()" class="text-slate-400 hover:text-slate-900 transition-colors" :title="sidebarOpen ? '{{ __('Fermer le panneau') }}' : '{{ __('Ouvrir le panneau') }}'">
                            <iconify-icon icon="solar:close-circle-linear" width="20"></iconify-icon>
                        </button>
                    </div>
                    <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar p-5">
                        @livewire('tickets.ticket-sidebar', [
                            'ticketId' => $ticket->id,
                            'ticketPublicId' => $ticketPublicId,
                            'canSeeInternalNotes' => $canSeeInternalNotes,
                            'canWriteInternalNotes' => $canWriteInternalNotes,
                        ], key('sidebar-' . $ticket->id))
                    </div>
                </div>
            </aside>
        </section>

        {{-- DISCUSSION SECTION --}}
        <section id="ticket-discussion-section" aria-label="{{ __('Discussion') }}" class="discussion-chat-panel flex-1 flex flex-col min-w-0 min-h-0 overflow-hidden bg-white">
            <!-- Header -->
            <header class="sticky top-0 z-20 shrink-0 bg-white/95 border-b border-slate-200 px-3 py-1.5 sm:px-5 sm:py-2 backdrop-blur safe-area-inset-top" style="padding-top: max(0.5rem, env(safe-area-inset-top));">
                <div class="flex flex-wrap items-center justify-between gap-2 sm:gap-3">
                    <nav class="flex items-center gap-1.5 sm:gap-2 min-w-0 flex-1 text-xs sm:text-sm" style="min-width: 0;">
                        @if($embedded ?? false)
                            <a href="{{ route('discussions.index', ['discussionParam' => $ticket->public_id]) }}" wire:navigate class="inline-flex items-center gap-1 shrink-0 text-slate-500 hover:text-slate-900 transition-colors touch-manipulation py-1">
                                <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
                                <span class="hidden sm:inline font-medium">{{ __('Retour') }}</span>
                            </a>
                        @else
                            <a href="{{ route('tickets.index') }}" onclick="if(history.length>1){event.preventDefault();history.back()}" class="inline-flex items-center gap-1 shrink-0 text-slate-500 hover:text-slate-900 transition-colors touch-manipulation py-1">
                                <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
                                <span class="hidden sm:inline font-medium">{{ __('Retour') }}</span>
                            </a>
                        @endif
                        <span class="text-slate-300 shrink-0">/</span>
                        <div class="flex items-center gap-1.5 sm:gap-2 min-w-0 flex-1 overflow-hidden">
                            <span class="font-mono text-xs font-bold text-slate-400 shrink-0">{{ $ticket->shortReference() }}</span>
                            <span class="font-semibold text-slate-900 truncate min-w-0">{{ $ticket->subject }}</span>
                            <span
                                x-show="!wsConnected"
                                x-cloak
                                class="h-2 w-2 rounded-full bg-amber-400 shrink-0 animate-pulse"
                                title="{{ __('Connexion en cours…') }}"
                            ></span>
                        </div>
                    </nav>
                    <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                        @if($embedded ?? false)
                            <a
                                href="{{ route('tickets.discussion', $ticket) }}"
                                class="inline-flex items-center gap-1.5 h-9 px-2.5 sm:px-3 rounded-lg border border-slate-200 bg-white text-slate-700 text-xs sm:text-sm font-medium hover:bg-slate-50 hover:text-slate-900 transition-colors"
                                title="{{ __('pages.discussions.open_full_ticket') }}"
                            >
                                <iconify-icon icon="solar:maximize-square-linear" width="18" class="shrink-0"></iconify-icon>
                                <span class="hidden sm:inline">{{ __('pages.discussions.open_full_ticket') }}</span>
                            </a>
                        @endif
                        <button type="button" @click="togglePanel()" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-colors lg:hidden" title="{{ __('Infos ticket') }}">
                            <iconify-icon icon="solar:sidebar-minimalistic-linear" width="20"></iconify-icon>
                        </button>
                        <button type="button" @click="toggleSidebar()" class="hidden lg:flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-colors" :title="sidebarOpen ? '{{ __('Fermer le panneau') }}' : '{{ __('Ouvrir le panneau Infos') }}'">
                            <iconify-icon icon="solar:sidebar-minimalistic-linear" width="20" class="transition-transform" :class="sidebarOpen ? 'rotate-180' : ''"></iconify-icon>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Timeline (isolated sub-component with cursor pagination) -->
            @livewire('tickets.ticket-timeline', [
                'ticketId' => $ticket->id,
                'ticketPublicId' => $ticketPublicId,
                'ticketCreatorId' => (int) $ticket->created_by,
                'canSeeInternalNotes' => $canSeeInternalNotes,
            ], key('timeline-' . $ticket->id))

            <!-- Composer (isolated sub-component) -->
            @livewire('tickets.ticket-composer', [
                'ticketId' => $ticket->id,
                'ticketPublicId' => $ticketPublicId,
                'canWriteInternalNotes' => $canWriteInternalNotes,
                'isLocked' => $isLocked,
                'mentionableUsers' => $mentionableUsers ?? [],
            ], key('composer-' . $ticket->id))
        </section>

        <!-- MOBILE DRAWER -->
        <div x-show="mobileDrawerOpen" x-cloak class="lg:hidden fixed inset-0 z-50" style="display: none; padding-left: env(safe-area-inset-left); padding-bottom: env(safe-area-inset-bottom);">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" x-show="mobileDrawerOpen" x-transition.opacity @click="mobileDrawerOpen = false"></div>
            <div class="absolute right-0 top-0 bottom-0 w-full max-w-[min(100%,24rem)] bg-white shadow-2xl flex flex-col rounded-l-2xl overflow-hidden"
                 x-show="mobileDrawerOpen"
                 x-transition:enter="transform transition ease-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in duration-300"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full">
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3 sm:px-5 sm:py-4 shrink-0" style="padding-top: max(0.75rem, env(safe-area-inset-top));">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">{{ __('Infos') }}</h2>
                    <button type="button" @click="mobileDrawerOpen = false" class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-900 transition-colors touch-manipulation" aria-label="{{ __('Fermer') }}">
                        <iconify-icon icon="solar:close-circle-bold" width="24"></iconify-icon>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto p-4 sm:p-5 custom-scrollbar" style="padding-bottom: max(1rem, env(safe-area-inset-bottom));">
                    {{-- Mobile reuses the same sidebar component via a second instance --}}
                    @livewire('tickets.ticket-sidebar', [
                        'ticketId' => $ticket->id,
                        'ticketPublicId' => $ticketPublicId,
                        'canSeeInternalNotes' => $canSeeInternalNotes,
                        'canWriteInternalNotes' => $canWriteInternalNotes,
                    ], key('sidebar-mobile-' . $ticket->id))
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
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
        attachmentsHtml(attachments, variant) {
            if (!Array.isArray(attachments) || attachments.length === 0) return '';
            const borderClass = 'border-t border-slate-100';
            const items = attachments.map(a => {
                const url = this.fileUrl(a);
                const name = this.escapeHtml(a.name || 'Fichier');
                const ext = (a.name || '').split('.').pop().toLowerCase();
                const isImg = ['png','jpg','jpeg','gif','webp'].includes(ext);
                if (isImg && url !== '#') {
                    return `<a href="${url}" target="_blank" rel="noopener" class="inline-block rounded-lg overflow-hidden border border-slate-200 max-w-[200px] mt-2"><img src="${url}" alt="${name}" class="block w-full h-auto max-h-36 object-cover" loading="lazy"></a>`;
                }
                return `<a href="${url}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg border border-slate-200/80 bg-white/30 hover:bg-white/50 p-2 text-xs text-slate-700"><iconify-icon icon="solar:file-text-linear" width="14"></iconify-icon><span class="truncate">${name}</span><iconify-icon icon="solar:download-linear" width="12"></iconify-icon></a>`;
            }).join('');
            return `<div class="mt-3 pt-3 ${borderClass} space-y-2">${items}</div>`;
        },
        renderBubble(e) {
            const isNote = e.type === 'internal_note';
            const isSystem = e.type === 'system';
            const isOwn = e.user_id && parseInt(e.user_id, 10) === parseInt(this.currentUserId, 10);
            const timeAgo = e.created_at ? (function(d){const s=Math.floor((Date.now()-new Date(d))/1000); return s<60?'à l\'instant':s<3600?Math.floor(s/60)+' min':s<86400?Math.floor(s/3600)+' h':Math.floor(s/86400)+' j';})(e.created_at) : '';
            const name = this.escapeHtml(e.user_name || '');
            const body = this.escapeHtml(e.body || '').replace(/\n/g, '<br>');
            const avatarUrl = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name || 'U') + '&size=40&background=e2e8f0&color=475569';
            const hasAttachments = (e.attachments && e.attachments.length > 0);
            const attachmentsBlock = this.attachmentsHtml(e.attachments || [], (isOwn && hasAttachments) ? 'theirs' : (isOwn ? 'mine' : 'theirs'));

            if (isSystem) {
                return `<div class="message-row message-system flex justify-center py-3"><div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 text-slate-600 text-xs"><iconify-icon icon="solar:info-circle-linear" width="14" class="shrink-0 text-slate-500"></iconify-icon><span class="break-words max-w-[min(100%,28rem)]">${body}</span><span class="text-slate-400 shrink-0">· ${timeAgo}</span></div></div>`;
            }
            if (isNote) {
                return `<div class="message-row message-note flex gap-3 py-3 max-w-[85%]"><div class="w-8 h-8 shrink-0 rounded-full bg-amber-100 flex items-center justify-center text-amber-600"><iconify-icon icon="solar:lock-keyhole-linear" width="14"></iconify-icon></div><div class="message-bubble flex-1 min-w-0 rounded-2xl rounded-tl-md bg-amber-50/90 border border-amber-200/80 shadow-sm overflow-hidden"><div class="px-4 py-2.5 flex flex-wrap items-center justify-between gap-2 border-b border-amber-200/60 bg-amber-50/50"><div class="flex items-center gap-2"><span class="text-[11px] font-bold text-amber-800 uppercase tracking-wide">Note interne</span><span class="text-[10px] text-amber-600">${name}</span></div><span class="text-[10px] text-amber-600/90">${timeAgo}</span></div><div class="px-4 py-3 text-sm leading-relaxed text-amber-900 break-words">${body}</div>${attachmentsBlock}</div></div>`;
            }
            if (isOwn) {
                const bubbleWrap = hasAttachments ? 'rounded-2xl rounded-br-md px-4 py-3 text-sm leading-relaxed shadow-md w-full max-w-full bg-white border border-slate-200 text-slate-700' : 'rounded-2xl rounded-br-md px-4 py-3 text-sm leading-relaxed shadow-md w-full max-w-full text-white';
                const bubbleStyle = hasAttachments ? '' : ' style="background: linear-gradient(135deg, var(--accent) 0%, color-mix(in srgb, var(--accent) 85%, #1e293b) 100%);"';
                return `<div class="message-row message-own flex justify-end py-3"><div class="flex items-end gap-3 max-w-[85%] min-w-0 flex-row-reverse"><div class="w-9 h-9 shrink-0 rounded-full ring-2 ring-white shadow-md overflow-hidden bg-slate-100"><img src="${avatarUrl}" class="w-full h-full object-cover" alt=""></div><div class="flex flex-col items-end min-w-0 max-w-full"><div class="flex items-center gap-2 mb-1.5 flex-row-reverse"><span class="text-xs font-semibold text-slate-800">${name}</span><span class="text-[10px] text-slate-400">${timeAgo}</span></div><div class="message-bubble ${bubbleWrap}"${bubbleStyle}><div class="text-left break-words ${!hasAttachments?'text-white':''}">${body}</div>${attachmentsBlock}</div></div></div></div>`;
            }
            return `<div class="message-row message-incoming flex gap-3 py-3 min-w-0 max-w-[85%]"><div class="w-9 h-9 shrink-0 rounded-full ring-2 ring-white shadow-md overflow-hidden bg-slate-100"><img src="${avatarUrl}" class="w-full h-full object-cover" alt=""></div><div class="min-w-0 max-w-full w-fit"><div class="flex flex-wrap items-center gap-2 mb-1.5"><span class="text-xs font-semibold text-slate-800">${name}</span><span class="text-[10px] text-slate-400 ml-auto">${timeAgo}</span></div><div class="message-bubble rounded-2xl rounded-bl-md px-4 py-3 text-sm leading-relaxed bg-white border border-slate-200 shadow-sm break-words w-fit max-w-full min-w-0">${body}${attachmentsBlock}</div></div></div>`;
        },
        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text ?? '';
            return div.innerHTML;
        }
    }));
</script>
@endscript
