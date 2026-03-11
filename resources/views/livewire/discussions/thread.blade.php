@php
    $participants = $thread->participants ?? collect();
    $title = $thread->is_group
        ? ($thread->name ?: __('pages.discussions.discussion_group'))
        : ($participants->where('id', '!=', auth()->id())->first()?->name ?: __('pages.discussions.discussion'));
    $participantIds = $participants->pluck('id')->all();
@endphp

<div
    class="flex flex-col min-h-0 overflow-hidden bg-white border-0 sm:border border-slate-200 shadow-sm rounded-none sm:rounded-2xl"
    style="{{ ($embedded ?? false) ? 'height: 100%; min-height: 0;' : 'height: calc(100dvh - 4rem); min-height: 12rem;' }} padding-bottom: env(safe-area-inset-bottom, 0);"
    x-data="threadWebSocket({{ $thread->id }}, {{ auth()->id() ?? 'null' }})"
    @keydown.enter.window="if (document.activeElement?.closest('[data-composer]') && !$event.shiftKey) { $event.preventDefault(); $refs.submitBtn?.click() }"
>
    <div class="flex flex-1 min-h-0 overflow-hidden" x-data="{ infoOpen: false, mobileInfoOpen: false, toggleInfo(){ if (window.innerWidth >= 1024) this.infoOpen = !this.infoOpen; else this.mobileInfoOpen = !this.mobileInfoOpen; } }">
        <!-- CENTER -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50/50">
            <header class="shrink-0 bg-white border-b border-slate-100 px-3 py-2.5 sm:px-6 sm:py-3" style="padding-top: max(0.625rem, env(safe-area-inset-top));">
                <div class="flex items-center justify-between gap-2 sm:gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="inline-flex h-8 w-8 sm:h-9 sm:w-9 items-center justify-center rounded-lg sm:rounded-xl bg-[var(--accent-soft)] text-[var(--accent)] shrink-0">
                                <iconify-icon icon="{{ $thread->is_group ? 'solar:users-group-rounded-bold-duotone' : 'solar:user-circle-bold-duotone' }}" width="20"></iconify-icon>
                            </span>
                            <div class="min-w-0 flex-1">
                                <h1 class="text-sm sm:text-base font-bold text-slate-900 truncate">{{ $title }}</h1>
                                <p class="text-xs text-slate-500 truncate">
                                    {{ $participants->count() }} {{ $participants->count() > 1 ? __('pages.discussions.participants') : __('pages.discussions.participant') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                        <button type="button" @click="toggleInfo()" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-colors touch-manipulation" aria-label="{{ __('pages.discussions.info') }}">
                            <iconify-icon icon="solar:sidebar-minimalistic-linear" width="20"></iconify-icon>
                        </button>
                        <button type="button" wire:click="$refresh" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-colors touch-manipulation" title="{{ __('pages.discussions.refresh') }}">
                            <iconify-icon icon="solar:refresh-linear" width="18" class="wire-loading:animate-spin"></iconify-icon>
                        </button>
                    </div>
                </div>
            </header>

            <div id="thread-messages" class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden custom-scrollbar overscroll-contain">
                <div class="mx-auto w-full max-w-4xl px-3 py-4 sm:px-6 sm:py-8" style="padding-left: max(0.75rem, env(safe-area-inset-left)); padding-right: max(0.75rem, env(safe-area-inset-right));">
                    <div id="thread-timeline" class="space-y-4">
                        @forelse($thread->messages as $msg)
                        @php
                            $isOwn = $msg->user_id && (int) $msg->user_id === (int) auth()->id();
                            $avatarUrl = $msg->user
                                ? 'https://ui-avatars.com/api/?name=' . urlencode($msg->user->name) . '&size=32&background=e2e8f0&color=475569'
                                : 'https://ui-avatars.com/api/?name=U&size=32&background=e2e8f0&color=475569';
                            $time = $msg->created_at->diffForHumans();
                            $bodyEscaped = e($msg->body);
                            $bodyFormatted = nl2br($bodyEscaped);
                            $messageAttachments = is_array($msg->attachments) ? $msg->attachments : [];
                        @endphp

                        @if($isOwn)
                            <div class="flex justify-end">
                                <div class="max-w-[85%] min-w-0">
                                    <div class="flex items-center justify-end gap-2 mb-1">
                                        <span class="text-[10px] text-slate-400">{{ $time }}</span>
                                        <span class="text-xs font-semibold text-slate-900">{{ $msg->user?->name ?? '—' }}</span>
                                    </div>
                                    <div class="rounded-2xl rounded-br-md px-4 py-3 text-sm leading-relaxed text-white shadow-sm" style="background-color: var(--accent);">
                                        {!! $bodyFormatted !!}
                                        @if(count($messageAttachments) > 0)
                                            <div class="mt-2 pt-2 border-t border-white/20 space-y-1.5">
                                                @foreach($messageAttachments as $att)
                                                    @include('livewire.tickets.partials.attachment-link', ['att' => $att, 'variant' => 'mine'])
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="flex items-end gap-2 sm:gap-3">
                                <img src="{{ $avatarUrl }}" class="w-7 h-7 sm:w-8 sm:h-8 rounded-full border border-slate-200 bg-white shrink-0" alt="">
                                <div class="max-w-[85%] min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs font-semibold text-slate-900">{{ $msg->user?->name ?? '—' }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $time }}</span>
                                    </div>
                                    <div class="rounded-2xl rounded-bl-md px-4 py-3 text-sm leading-relaxed bg-white border border-slate-200 text-slate-700 shadow-sm">
                                        {!! $bodyFormatted !!}
                                        @if(count($messageAttachments) > 0)
                                            <div class="mt-2 pt-2 border-t border-slate-100 space-y-1.5">
                                                @foreach($messageAttachments as $att)
                                                    @include('livewire.tickets.partials.attachment-link', ['att' => $att, 'variant' => 'theirs'])
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="py-16 text-center" data-empty-thread>
                            <div class="mx-auto mb-4 inline-flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                <iconify-icon icon="solar:chat-round-dots-linear" width="24"></iconify-icon>
                            </div>
                            <p class="text-sm text-slate-500">{{ __('pages.discussions.empty_message') }}</p>
                        </div>
                    @endforelse
                    </div>
                </div>
            </div>

            <!-- Composer (toujours visible en bas : ne pas scroller avec les messages) -->
            <div class="shrink-0 bg-white border-t border-slate-200 p-2 sm:p-4 z-10 flex-shrink-0" data-composer style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom));">
                <div class="mx-auto w-full max-w-4xl">
                    <form wire:submit="sendMessage" class="relative rounded-2xl bg-slate-50 border border-slate-200 shadow-sm focus-within:ring-2 focus-within:ring-[var(--accent)] focus-within:border-transparent transition-all">
                        <div class="p-2">
                            <textarea
                                wire:model="body"
                                rows="3"
                                class="w-full bg-transparent border-0 text-slate-900 placeholder:text-slate-400 focus:ring-0 resize-none text-sm p-2"
                                placeholder="{{ __('pages.discussions.write_message') }}"
                                @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); $el.closest('form').requestSubmit(); }"
                            ></textarea>
                        </div>
                        <div class="flex items-center justify-between px-3 py-2 border-t border-slate-200/50 bg-white/50 rounded-b-2xl">
                            <div class="flex items-center gap-1">
                                <input type="file" wire:model="attachmentFiles" multiple class="hidden" id="thread-file-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.gif,.webp,image/*">
                                <button type="button" onclick="document.getElementById('thread-file-input').click()" class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors" title="{{ __('pages.discussions.attach_file') }}">
                                    <iconify-icon icon="solar:paperclip-linear" width="20"></iconify-icon>
                                </button>
                                @if(count($attachmentFiles ?? []) > 0)
                                    <span class="ml-2 text-xs font-medium text-[var(--accent)] bg-[var(--accent-soft)] px-2 py-1 rounded-md">{{ count($attachmentFiles) }} fichier(s)</span>
                                @endif
                            </div>
                            <button type="submit" x-ref="submitBtn" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-bold text-white shadow-md hover:opacity-90 transition-all" style="background-color: var(--accent);">
                                <span>{{ __('pages.discussions.send') }}</span>
                                <iconify-icon icon="solar:plain-bold" width="16"></iconify-icon>
                            </button>
                        </div>
                    </form>
                    <x-input-error :messages="$errors->get('body')" class="mt-2" />
                </div>
            </div>
        </div>

        <!-- RIGHT INFO -->
        <aside class="hidden lg:flex shrink-0 flex-col bg-white border-l border-slate-200 overflow-hidden transition-[width] duration-300 ease-in-out" :class="infoOpen ? 'w-[320px]' : 'w-0 border-l-0'">
            <div class="flex flex-col flex-1 min-w-0 min-h-0 w-[320px]">
                <div class="flex h-[60px] shrink-0 items-center justify-between border-b border-slate-100 px-5">
                    <span class="text-sm font-bold text-slate-900">{{ __('pages.discussions.participants') }} ({{ $participants->count() }})</span>
                    <button type="button" @click="infoOpen = false" class="text-slate-400 hover:text-slate-900 transition-colors">
                        <iconify-icon icon="solar:close-circle-linear" width="20"></iconify-icon>
                    </button>
                </div>
                <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar p-5 space-y-3">
                    {{-- Add participant (groups only, for creator/staff) --}}
                    @if($thread->is_group && $canManageParticipants)
                        <div x-data="{ showAdd: false, search: '' }" class="mb-3">
                            <button type="button" @click="showAdd = !showAdd; if(showAdd) $nextTick(() => $refs.addSearch?.focus())" class="flex items-center gap-2 w-full rounded-lg border border-dashed border-slate-300 px-3 py-2 text-xs font-medium text-slate-500 hover:border-[var(--accent)] hover:text-[var(--accent)] transition-colors">
                                <iconify-icon icon="solar:user-plus-linear" width="16"></iconify-icon>
                                {{ __('pages.discussions.add_member') }}
                            </button>
                            <div x-show="showAdd" x-cloak x-transition class="mt-2">
                                <input
                                    type="text"
                                    x-ref="addSearch"
                                    x-model="search"
                                    placeholder="{{ __('pages.discussions.search') }}"
                                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs focus:ring-2 focus:ring-[var(--accent)] focus:border-transparent"
                                >
                                <div class="mt-1 max-h-40 overflow-y-auto rounded-lg border border-slate-200 bg-white shadow-sm">
                                    @foreach($orgUsers as $ou)
                                        @if(! in_array($ou->id, $participantIds))
                                            <button
                                                type="button"
                                                x-show="!search || '{{ strtolower(e($ou->name)) }}'.includes(search.toLowerCase()) || '{{ strtolower(e($ou->email)) }}'.includes(search.toLowerCase())"
                                                wire:click="addParticipant({{ $ou->id }})"
                                                class="flex w-full items-center gap-2 px-3 py-2 text-xs hover:bg-slate-50 transition-colors"
                                            >
                                                <div class="h-6 w-6 rounded-full flex items-center justify-center text-[10px] font-semibold shrink-0" style="background: var(--accent-soft); color: var(--accent);">
                                                    {{ strtoupper(mb_substr($ou->name, 0, 1)) }}
                                                </div>
                                                <div class="min-w-0 text-left">
                                                    <div class="font-medium text-slate-900 truncate">{{ $ou->name }}</div>
                                                    <div class="text-slate-400 truncate">{{ $ou->email }}</div>
                                                </div>
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Participant list --}}
                    @foreach($participants as $p)
                        <div class="flex items-center gap-3 group">
                            <div class="h-9 w-9 rounded-full flex items-center justify-center text-sm font-semibold shrink-0" style="background: var(--accent-soft); color: var(--accent);">
                                {{ strtoupper(mb_substr($p->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-semibold text-slate-900 truncate">
                                    {{ $p->name }}
                                    @if((int) $p->id === (int) $thread->created_by)
                                        <span class="text-[10px] text-slate-400 font-normal ml-1">{{ __('pages.discussions.creator') }}</span>
                                    @endif
                                </div>
                                <div class="text-xs text-slate-500 truncate">{{ $p->email }}</div>
                            </div>
                            @if($thread->is_group && $canManageParticipants && (int) $p->id !== (int) $thread->created_by)
                                <button
                                    type="button"
                                    @click="$dispatch('confirm-action', { title: 'Retirer', message: '{{ __('pages.discussions.remove_participant_confirm') }}', confirmLabel: 'Retirer', variant: 'danger', onConfirm: () => $wire.removeParticipant({{ $p->id }}) })"
                                    class="opacity-0 group-hover:opacity-100 shrink-0 p-1 rounded text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all"
                                    title="{{ __('pages.discussions.remove') }}"
                                >
                                    <iconify-icon icon="solar:close-circle-linear" width="16"></iconify-icon>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>
    </div>
</div>

@script
<script>
    Alpine.data('threadWebSocket', (threadId, currentUserId) => ({
        threadId,
        currentUserId,
        init() {
            const tryConnect = () => {
                if (typeof window.Echo !== 'undefined') {
                    window.Echo.private('discussion.' + this.threadId)
                        .listen('.discussion.message.sent', (e) => this.appendMessage(e))
                        .listen('.discussion.participant.changed', () => {
                            this.$wire.$refresh();
                        });
                    return;
                }
                setTimeout(tryConnect, 300);
            };
            tryConnect();
        },
        appendMessage(e) {
            const timeline = document.getElementById('thread-timeline');
            const scroll = document.getElementById('thread-messages');
            if (!timeline || !scroll) return;

            const empty = timeline.querySelector('[data-empty-thread]');
            if (empty) empty.remove();

            const isOwn = e.user_id && parseInt(e.user_id, 10) === parseInt(this.currentUserId, 10);
            const time = e.created_at ? new Date(e.created_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }) : '';
            const name = this.escapeHtml(e.user_name || '');
            const body = this.escapeHtml(e.body || '').replace(/\n/g, '<br>');
            const avatarUrl = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name || 'U') + '&size=32&background=e2e8f0&color=475569';

            const attachments = Array.isArray(e.attachments) ? e.attachments : [];
            const attachmentsHtml = attachments.length
                ? `<div class="mt-2 pt-2 ${isOwn ? 'border-t border-white/20' : 'border-t border-slate-100'} space-y-1.5">` +
                    attachments.map(a => {
                        const url = a?.url || (a?.path ? (window.location.origin + '/storage/' + a.path) : '#');
                        const n = this.escapeHtml(a?.name || 'Fichier');
                        return `<a href="${url}" target="_blank" rel="noopener" class="block text-xs ${isOwn ? 'text-white/90' : 'text-slate-600'} hover:underline flex items-center gap-1"><iconify-icon icon="solar:file-linear" width="12"></iconify-icon> ${n}</a>`;
                    }).join('') +
                  `</div>`
                : '';

            const html = isOwn
                ? `<div class="flex justify-end"><div class="max-w-[85%]"><div class="flex items-center justify-end gap-2 mb-1"><span class="text-[10px] text-slate-400">${time}</span><span class="text-xs font-semibold text-slate-900">${name}</span></div><div class="rounded-2xl rounded-br-md px-4 py-3 text-sm leading-relaxed text-white shadow-sm" style="background-color: var(--accent);">${body}${attachmentsHtml}</div></div></div>`
                : `<div class="flex items-end gap-3"><img src="${avatarUrl}" class="w-8 h-8 rounded-full border border-slate-200 bg-white shrink-0" alt=""><div class="max-w-[85%]"><div class="flex items-center gap-2 mb-1"><span class="text-xs font-semibold text-slate-900">${name}</span><span class="text-[10px] text-slate-400">${time}</span></div><div class="rounded-2xl rounded-bl-md px-4 py-3 text-sm leading-relaxed bg-white border border-slate-200 text-slate-700 shadow-sm">${body}${attachmentsHtml}</div></div></div>`;

            const div = document.createElement('div');
            div.className = 'animate-enter';
            div.innerHTML = html;
            timeline.appendChild(div);
            scroll.scrollTop = scroll.scrollHeight;
        },
        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text ?? '';
            return div.innerHTML;
        }
    }));
</script>
@endscript
