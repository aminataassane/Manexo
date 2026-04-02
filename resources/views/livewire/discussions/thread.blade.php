@php
    $participants = $thread->participants ?? collect();
    $title = $thread->is_group
        ? ($thread->name ?: __('pages.discussions.discussion_group'))
        : ($participants->where('id', '!=', auth()->id())->first()?->name ?: __('pages.discussions.discussion'));
    $participantIds = $participants->pluck('id')->all();
@endphp

<div
    class="flex flex-col h-full min-h-0 overflow-hidden bg-white"
    x-data="threadWebSocket({{ $thread->id }}, {{ auth()->id() ?? 'null' }})"
    @keydown.enter.window="if (document.activeElement?.closest('[data-composer]') && !$event.shiftKey) { $event.preventDefault(); $refs.submitBtn?.click() }"
>
    <div class="flex flex-1 min-h-0 overflow-hidden" x-data="{ infoOpen: false, mobileInfoOpen: false, toggleInfo(){ if (window.innerWidth >= 768) this.infoOpen = !this.infoOpen; else this.mobileInfoOpen = !this.mobileInfoOpen; } }">
        {{-- CENTER COLUMN --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            {{-- Chat header (slim) --}}
            <header class="shrink-0 bg-white border-b border-slate-100 px-4 py-2" style="padding-top: max(0.375rem, env(safe-area-inset-top));">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                        @if($embedded ?? false)
                            <a href="{{ route('discussions.index') }}" wire:navigate
                               class="md:hidden shrink-0 h-8 w-8 rounded-lg text-slate-400 hover:bg-slate-50 hover:text-slate-700 transition-colors inline-flex items-center justify-center mr-1">
                                <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
                            </a>
                        @endif
                        <div class="h-8 w-8 rounded-full flex items-center justify-center text-sm font-semibold shrink-0" style="background: var(--accent-soft); color: var(--accent);">
                            <iconify-icon icon="{{ $thread->is_group ? 'solar:users-group-rounded-bold-duotone' : 'solar:user-circle-bold-duotone' }}" width="18"></iconify-icon>
                        </div>
                        <div class="min-w-0">
                            <h1 class="text-sm font-bold text-slate-900 truncate">{{ $title }}</h1>
                            <p class="text-[11px] text-slate-400 truncate">
                                {{ $participants->count() }} {{ $participants->count() > 1 ? __('pages.discussions.participants') : __('pages.discussions.participant') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <button type="button" @click="toggleInfo()" class="h-8 w-8 rounded-lg text-slate-400 hover:bg-slate-50 hover:text-slate-700 transition-colors inline-flex items-center justify-center" aria-label="{{ __('pages.discussions.info') }}">
                            <iconify-icon icon="solar:sidebar-minimalistic-linear" width="18"></iconify-icon>
                        </button>
                        <button type="button" wire:click="$refresh" class="h-8 w-8 rounded-lg text-slate-400 hover:bg-slate-50 hover:text-slate-700 transition-colors inline-flex items-center justify-center" title="{{ __('pages.discussions.refresh') }}">
                            <iconify-icon icon="solar:refresh-linear" width="16" class="wire-loading:animate-spin"></iconify-icon>
                        </button>
                    </div>
                </div>
            </header>

            {{-- Chat scroll area --}}
            <div id="thread-messages" class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden custom-scrollbar overscroll-contain messaging-chat-scroll">
                <div class="mx-auto w-full max-w-3xl px-4 py-6" style="padding-left: max(1rem, env(safe-area-inset-left)); padding-right: max(1rem, env(safe-area-inset-right));">
                    @if(($hasMoreMessages ?? false))
                        <div class="mb-4 flex justify-center">
                            <button
                                type="button"
                                wire:click="loadMoreMessages"
                                wire:loading.attr="disabled"
                                wire:target="loadMoreMessages"
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 disabled:opacity-60"
                            >
                                <iconify-icon icon="solar:alt-arrow-up-linear" width="14"></iconify-icon>
                                {{ __('Charger les messages plus anciens') }}
                            </button>
                        </div>
                    @endif
                    <div id="thread-timeline" class="space-y-3">
                        @forelse($thread->messages as $msg)
                        @php
                            $isOwn = $msg->user_id && (int) $msg->user_id === (int) auth()->id();
                            $avatarInitials = \Illuminate\Support\Str::of($msg->user?->name ?? 'U')
                                ->explode(' ')
                                ->take(2)
                                ->map(fn ($p) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($p, 0, 1)))
                                ->implode('');
                            $time = $msg->created_at->diffForHumans();
                            $bodyEscaped = e($msg->body);
                            $bodyFormatted = nl2br($bodyEscaped);
                            $messageAttachments = is_array($msg->attachments) ? $msg->attachments : [];
                        @endphp

                        <div wire:key="disc-msg-{{ $msg->id }}" data-discussion-message-id="{{ $msg->id }}">
                        @if($isOwn)
                            <div class="flex justify-end">
                                <div class="max-w-[85%] sm:max-w-[80%] md:max-w-[75%] min-w-0">
                                    <div class="flex items-center justify-end gap-2 mb-1">
                                        <span class="text-[10px] text-slate-400">{{ $time }}</span>
                                        <span class="text-[11px] font-semibold text-slate-600">{{ $msg->user?->name ?? '—' }}</span>
                                    </div>
                                    <div class="messaging-bubble-own rounded-2xl rounded-br-sm px-4 py-2.5 text-[0.88rem] leading-relaxed text-white" style="background-color: var(--accent);">
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
                            <div class="flex items-end gap-2">
                                <span class="w-7 h-7 rounded-full border border-slate-100 bg-slate-100 text-slate-700 inline-flex items-center justify-center text-[10px] font-semibold shrink-0">
                                    {{ $avatarInitials }}
                                </span>
                                <div class="max-w-[85%] sm:max-w-[80%] md:max-w-[75%] min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[11px] font-semibold text-slate-600">{{ $msg->user?->name ?? '—' }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $time }}</span>
                                    </div>
                                    <div class="messaging-bubble-incoming rounded-2xl rounded-bl-sm px-4 py-2.5 text-[0.88rem] leading-relaxed bg-white border border-slate-100 text-slate-700">
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
                        </div>
                    @empty
                        <div class="py-16 text-center" data-empty-thread>
                            <div class="mx-auto mb-4 inline-flex h-12 w-12 items-center justify-center rounded-full text-slate-300" style="background: var(--accent-soft);">
                                <iconify-icon icon="solar:chat-round-dots-linear" width="24" style="color: var(--accent); opacity: 0.5;"></iconify-icon>
                            </div>
                            <p class="text-sm text-slate-400">{{ __('pages.discussions.empty_message') }}</p>
                        </div>
                    @endforelse
                    </div>
                </div>
            </div>

            {{-- Composer --}}
            <div class="shrink-0 messaging-composer border-t border-slate-100 p-2 sm:p-3 z-10" data-composer style="padding-bottom: max(0.5rem, env(safe-area-inset-bottom));">
                <div class="mx-auto w-full max-w-3xl">
                    <form wire:submit="sendMessage" wire:loading.class="pointer-events-none opacity-90" wire:target="sendMessage" class="flex items-end gap-2 bg-[#F1F5F9]/80 rounded-2xl border border-slate-200 p-1.5 focus-within:ring-2 focus-within:ring-[var(--accent)]/30 focus-within:border-[var(--accent)]/30 transition-all"
                        x-data="{
                            draftKey: 'discussion-draft-{{ $thread->id }}',
                            draftTimer: null,
                            init() {
                                try {
                                    const saved = localStorage.getItem(this.draftKey);
                                    if (saved && !this.$wire.get('body')) this.$wire.set('body', saved);
                                } catch (e) {}
                            },
                            saveDraft(val) {
                                clearTimeout(this.draftTimer);
                                this.draftTimer = setTimeout(() => {
                                    try {
                                        if (val && val.trim()) localStorage.setItem(this.draftKey, val);
                                        else localStorage.removeItem(this.draftKey);
                                    } catch (e) {}
                                }, 500);
                            },
                            autoGrow(el) {
                                el.style.height = 'auto';
                                el.style.height = Math.min(el.scrollHeight, 128) + 'px';
                            }
                        }"
                        x-on:submit="
                            const txt = ($wire.get('body') || '').trim();
                            if (txt.length > 0) {
                                window.dispatchEvent(new CustomEvent('discussion:pending-send', {
                                    detail: { body: txt, userName: '{{ addslashes(auth()->user()?->name ?? '') }}' }
                                }));
                            }
                            localStorage.removeItem('discussion-draft-{{ $thread->id }}');
                        "
                    >
                        {{-- Paperclip --}}
                        <input
                            type="file"
                            multiple
                            class="hidden"
                            id="thread-file-input"
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.gif,.webp,image/*"
                            x-on:change="async (e) => {
                                const list = e.target.files;
                                if (!list?.length) return;
                                const ready = await window.manexoCompressFilesForUpload(list);
                                e.target.value = '';
                                $wire.uploadMultiple('attachmentFiles', ready);
                            }"
                        >
                        <button type="button" onclick="document.getElementById('thread-file-input').click()" class="shrink-0 h-10 w-10 sm:h-9 sm:w-9 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-white/70 transition-colors inline-flex items-center justify-center" title="{{ __('pages.discussions.attach_file') }}">
                            <iconify-icon icon="solar:paperclip-linear" width="19"></iconify-icon>
                        </button>

                        {{-- Textarea --}}
                        <textarea
                            wire:model="body"
                            rows="1"
                            wire:loading.attr="disabled"
                            wire:target="sendMessage"
                            class="flex-1 bg-transparent border-0 text-slate-900 placeholder:text-slate-400 focus:ring-0 resize-none text-sm py-2 px-1 max-h-32"
                            style="min-height: 2.25rem;"
                            placeholder="{{ __('pages.discussions.write_message') }}"
                            @input="saveDraft($event.target.value); autoGrow($event.target)"
                            @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); $el.closest('form').requestSubmit(); }"
                        ></textarea>

                        {{-- File count badge --}}
                        @if(count($attachmentFiles ?? []) > 0)
                            <span class="shrink-0 text-[11px] font-semibold px-2 py-1 rounded-lg" style="background: var(--accent-soft); color: var(--accent);">{{ count($attachmentFiles) }}</span>
                        @endif

                        {{-- Send button --}}
                        <x-manexo.action-button
                            type="submit"
                            wire-target="sendMessage"
                            variant="primary"
                            icon-only
                            x-ref="submitBtn"
                            class="!rounded-xl shrink-0 h-10 w-10 sm:h-9 sm:w-9 p-0 !px-0"
                            style="background-color: var(--accent);"
                            :loading-label="__('ui.action.sending')"
                        >
                            <iconify-icon icon="solar:plain-bold" width="16"></iconify-icon>
                        </x-manexo.action-button>
                    </form>
                    <x-input-error :messages="$errors->get('body')" class="mt-1.5" />
                </div>
            </div>
        </div>

        {{-- RIGHT INFO SIDEBAR --}}
        <aside class="hidden md:flex shrink-0 flex-col bg-white border-l border-slate-100 overflow-hidden transition-[width] duration-300 ease-in-out" :class="infoOpen ? 'w-60 lg:w-72' : 'w-0 border-l-0'">
            <div class="flex flex-col flex-1 min-w-0 min-h-0 w-60 lg:w-72">
                <div class="flex h-12 shrink-0 items-center justify-between border-b border-slate-100 px-4">
                    <span class="text-sm font-bold text-slate-900">{{ __('pages.discussions.participants') }} ({{ $participants->count() }})</span>
                    <button type="button" @click="infoOpen = false" class="text-slate-400 hover:text-slate-700 transition-colors">
                        <iconify-icon icon="solar:close-circle-linear" width="18"></iconify-icon>
                    </button>
                </div>
                <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar p-4 space-y-2">
                    {{-- Add participant (groups only) --}}
                    @if($thread->is_group && $canManageParticipants)
                        <div x-data="{ showAdd: false, search: '' }" class="mb-2">
                            <button type="button" @click="showAdd = !showAdd; if(showAdd) $nextTick(() => $refs.addSearch?.focus())" class="flex items-center gap-2 w-full rounded-lg border border-dashed border-slate-200 px-3 py-2 text-xs font-medium text-slate-400 hover:border-[var(--accent)] hover:text-[var(--accent)] transition-colors">
                                <iconify-icon icon="solar:user-plus-linear" width="15"></iconify-icon>
                                {{ __('pages.discussions.add_member') }}
                            </button>
                            <div x-show="showAdd" x-cloak x-transition class="mt-2">
                                <input
                                    type="text"
                                    x-ref="addSearch"
                                    x-model="search"
                                    placeholder="{{ __('pages.discussions.search') }}"
                                    class="w-full rounded-lg bg-[#F1F5F9] border-0 px-3 py-2 text-xs focus:ring-2 focus:ring-[var(--accent)]/20"
                                >
                                <div class="mt-1 max-h-40 overflow-y-auto rounded-lg border border-slate-100 bg-white shadow-sm">
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
                        <div class="flex items-center gap-2.5 group py-1">
                            <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-semibold shrink-0" style="background: var(--accent-soft); color: var(--accent);">
                                {{ strtoupper(mb_substr($p->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-[13px] font-semibold text-slate-900 truncate">
                                    {{ $p->name }}
                                    @if((int) $p->id === (int) $thread->created_by)
                                        <span class="text-[10px] text-slate-400 font-normal ml-1">{{ __('pages.discussions.creator') }}</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-400 truncate">{{ $p->email }}</div>
                            </div>
                            @if($thread->is_group && $canManageParticipants && (int) $p->id !== (int) $thread->created_by)
                                <button
                                    type="button"
                                    @click="$dispatch('confirm-action', { title: 'Retirer', message: '{{ __('pages.discussions.remove_participant_confirm') }}', confirmLabel: 'Retirer', variant: 'danger', onConfirm: () => $wire.removeParticipant({{ $p->id }}) })"
                                    class="opacity-0 group-hover:opacity-100 shrink-0 p-1 rounded text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all"
                                    title="{{ __('pages.discussions.remove') }}"
                                >
                                    <iconify-icon icon="solar:close-circle-linear" width="15"></iconify-icon>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>

        {{-- MOBILE INFO DRAWER --}}
        <div x-show="mobileInfoOpen" x-cloak class="md:hidden fixed inset-0 z-50">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="mobileInfoOpen = false" x-transition.opacity></div>
            <div class="absolute right-0 top-0 bottom-0 w-full max-w-[min(100%,20rem)] bg-white shadow-2xl flex flex-col rounded-l-2xl overflow-hidden"
                 x-show="mobileInfoOpen"
                 x-transition:enter="transform transition ease-out duration-300"
                 x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in duration-300"
                 x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
                <div class="flex h-12 shrink-0 items-center justify-between border-b border-slate-100 px-4">
                    <span class="text-sm font-bold text-slate-900">{{ __('pages.discussions.participants') }} ({{ $participants->count() }})</span>
                    <button type="button" @click="mobileInfoOpen = false" class="text-slate-400 hover:text-slate-700 transition-colors">
                        <iconify-icon icon="solar:close-circle-linear" width="18"></iconify-icon>
                    </button>
                </div>
                <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar p-4 space-y-2">
                    @if($thread->is_group && $canManageParticipants)
                        <div x-data="{ showAdd: false, search: '' }" class="mb-2">
                            <button type="button" @click="showAdd = !showAdd; if(showAdd) $nextTick(() => $refs.mobileAddSearch?.focus())" class="flex items-center gap-2 w-full rounded-lg border border-dashed border-slate-200 px-3 py-2 text-xs font-medium text-slate-400 hover:border-[var(--accent)] hover:text-[var(--accent)] transition-colors">
                                <iconify-icon icon="solar:user-plus-linear" width="15"></iconify-icon>
                                {{ __('pages.discussions.add_member') }}
                            </button>
                            <div x-show="showAdd" x-cloak x-transition class="mt-2">
                                <input
                                    type="text"
                                    x-ref="mobileAddSearch"
                                    x-model="search"
                                    placeholder="{{ __('pages.discussions.search') }}"
                                    class="w-full rounded-lg bg-[#F1F5F9] border-0 px-3 py-2 text-xs focus:ring-2 focus:ring-[var(--accent)]/20"
                                >
                                <div class="mt-1 max-h-40 overflow-y-auto rounded-lg border border-slate-100 bg-white shadow-sm">
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

                    @foreach($participants as $p)
                        <div class="flex items-center gap-2.5 py-1">
                            <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-semibold shrink-0" style="background: var(--accent-soft); color: var(--accent);">
                                {{ strtoupper(mb_substr($p->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-[13px] font-semibold text-slate-900 truncate">
                                    {{ $p->name }}
                                    @if((int) $p->id === (int) $thread->created_by)
                                        <span class="text-[10px] text-slate-400 font-normal ml-1">{{ __('pages.discussions.creator') }}</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-400 truncate">{{ $p->email }}</div>
                            </div>
                            @if($thread->is_group && $canManageParticipants && (int) $p->id !== (int) $thread->created_by)
                                <button
                                    type="button"
                                    @click="$dispatch('confirm-action', { title: 'Retirer', message: '{{ __('pages.discussions.remove_participant_confirm') }}', confirmLabel: 'Retirer', variant: 'danger', onConfirm: () => $wire.removeParticipant({{ $p->id }}) })"
                                    class="shrink-0 p-1 rounded text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all"
                                    title="{{ __('pages.discussions.remove') }}"
                                >
                                    <iconify-icon icon="solar:close-circle-linear" width="15"></iconify-icon>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    Alpine.data('threadWebSocket', (threadId, currentUserId) => ({
        threadId,
        currentUserId,
        init() {
            window.addEventListener('discussion:pending-send', (evt) => {
                const body = evt?.detail?.body || '';
                const userName = evt?.detail?.userName || '';
                if (body) this.appendPendingMessage(body, userName);
            });

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

            const messageId = e.id != null && String(e.id).match(/^\d+$/) ? String(e.id) : '';
            if (messageId !== '' && timeline.querySelector('[data-discussion-message-id="' + messageId + '"]')) {
                timeline.querySelectorAll('[data-pending-own="1"]').forEach((el) => el.remove());
                return;
            }

            timeline.querySelectorAll('[data-pending-own="1"]').forEach((el) => el.remove());

            const empty = timeline.querySelector('[data-empty-thread]');
            if (empty) empty.remove();

            const isOwn = e.user_id && parseInt(e.user_id, 10) === parseInt(this.currentUserId, 10);
            const time = e.created_at ? new Date(e.created_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }) : '';
            const name = this.escapeHtml(e.user_name || '');
            const body = this.escapeHtml(e.body || '').replace(/\n/g, '<br>');
            const initials = (name || 'U')
                .split(' ')
                .filter(Boolean)
                .slice(0, 2)
                .map((part) => part.charAt(0).toUpperCase())
                .join('') || 'U';

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
                ? `<div class="flex justify-end"><div class="max-w-[85%] sm:max-w-[80%] md:max-w-[75%] min-w-0"><div class="flex items-center justify-end gap-2 mb-1"><span class="text-[10px] text-slate-400">${time}</span><span class="text-[11px] font-semibold text-slate-600">${name}</span></div><div class="messaging-bubble-own rounded-2xl rounded-br-sm px-4 py-2.5 text-[0.88rem] leading-relaxed text-white" style="background-color: var(--accent);">${body}${attachmentsHtml}</div></div></div>`
                : `<div class="flex items-end gap-2"><span class="w-7 h-7 rounded-full border border-slate-100 bg-slate-100 text-slate-700 inline-flex items-center justify-center text-[10px] font-semibold shrink-0">${this.escapeHtml(initials)}</span><div class="max-w-[85%] sm:max-w-[80%] md:max-w-[75%] min-w-0"><div class="flex items-center gap-2 mb-1"><span class="text-[11px] font-semibold text-slate-600">${name}</span><span class="text-[10px] text-slate-400">${time}</span></div><div class="messaging-bubble-incoming rounded-2xl rounded-bl-sm px-4 py-2.5 text-[0.88rem] leading-relaxed bg-white border border-slate-100 text-slate-700">${body}${attachmentsHtml}</div></div></div>`;

            const div = document.createElement('div');
            div.className = 'animate-enter';
            if (messageId !== '') div.setAttribute('data-discussion-message-id', messageId);
            div.innerHTML = html;
            timeline.appendChild(div);
            scroll.scrollTop = scroll.scrollHeight;
        },
        appendPendingMessage(rawBody, rawName) {
            const timeline = document.getElementById('thread-timeline');
            const scroll = document.getElementById('thread-messages');
            if (!timeline || !scroll) return;

            timeline.querySelectorAll('[data-pending-own="1"]').forEach((el) => el.remove());

            const empty = timeline.querySelector('[data-empty-thread]');
            if (empty) empty.remove();

            const body = this.escapeHtml(rawBody || '').replace(/\n/g, '<br>');
            const name = this.escapeHtml(rawName || '');
            const html = `<div data-pending-own="1" class="flex justify-end opacity-70"><div class="max-w-[85%] sm:max-w-[80%] md:max-w-[75%] min-w-0"><div class="flex items-center justify-end gap-2 mb-1"><span class="text-[10px] text-slate-400">{{ __('ui.action.sending') }}</span><span class="text-[11px] font-semibold text-slate-600">${name}</span></div><div class="messaging-bubble-own rounded-2xl rounded-br-sm px-4 py-2.5 text-[0.88rem] leading-relaxed text-white" style="background-color: var(--accent);">${body}</div></div></div>`;

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
