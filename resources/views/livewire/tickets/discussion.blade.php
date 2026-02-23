@php
    use App\Enums\TicketMessageType;
    use App\Enums\TicketStatus;
    $notesCount = $ticket->messages->where('type', TicketMessageType::InternalNote)->count();
    $lastActivity = $ticket->messages->last()?->created_at ?? $ticket->updated_at;
    $allAttachments = $ticket->messages->flatMap(fn ($m) => is_array($m->attachments) ? $m->attachments : [])->filter()->values();
    $creator = $ticket->creator;
    $discussionUsers = collect([$creator])->merge($ticket->assignees)->merge($ticket->participants)->filter()->unique('id');
    $ticketAttachments = [];
    if (is_array($ticket->attachments)) {
        $files = $ticket->attachments['files'] ?? [];
        $links = $ticket->attachments['links'] ?? [];
        foreach ($files as $f) {
            $ticketAttachments[] = is_array($f) ? $f : [];
        }
        foreach ($links as $l) {
            $ticketAttachments[] = ['name' => $l['name'] ?? __('Lien'), 'url' => $l['url'] ?? '#', 'path' => null];
        }
    }
    $customFields = is_array($ticket->custom_fields) ? $ticket->custom_fields : [];

    $statusLabels = [
        'open' => __('tickets.status.open'),
        'in_progress' => __('tickets.status.in_progress'),
        'pending' => __('tickets.status.pending'),
        'resolved' => __('tickets.status.resolved'),
        'closed' => __('tickets.status.closed'),
    ];
    $priorityDotClass = [
        1 => 'bg-slate-400',
        2 => 'bg-blue-500',
        3 => 'bg-amber-500',
        4 => 'bg-red-500',
    ];
    $priorityLevel = optional($ticket->priority)->level ?? 2;
    $priorityDot = $priorityDotClass[$priorityLevel] ?? 'bg-slate-400';
@endphp

<div
    class="flex flex-col min-h-0 rounded-none sm:rounded-xl lg:rounded-2xl overflow-hidden bg-white border-0 sm:border border-slate-200 shadow-sm"
    style="height: calc(100dvh - 5rem); min-height: 14rem; padding-bottom: env(safe-area-inset-bottom, 0);"
    x-data="discussionWebSocket({{ $ticketId }}, {{ auth()->id() ?? 'null' }}, {{ $canSeeInternalNotes ? 'true' : 'false' }})"
    @keydown.enter.window="if (document.activeElement?.closest('[data-composer]') && !$event.shiftKey) { $event.preventDefault(); $refs.submitBtn?.click() }"
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
        {{-- SECTION DÉTAILS : uniquement infos ticket (sidebar). Récupération discussion n'affecte pas cette section. --}}
        <section id="ticket-details-section" aria-label="{{ __('Détails du ticket') }}" class="hidden lg:block shrink-0">
            <aside class="flex shrink-0 flex-col bg-white border-r border-slate-200 overflow-hidden transition-[width] duration-300 ease-in-out h-full" :class="sidebarOpen ? 'w-[300px] xl:w-[320px]' : 'w-0 border-r-0'">
                <div class="flex flex-col flex-1 min-w-0 min-h-0 w-[300px] xl:w-[320px]">
                    <div class="flex h-[60px] shrink-0 items-center justify-between border-b border-slate-100 px-5">
                        <span class="text-sm font-bold text-slate-900">{{ __('Infos') }}</span>
                        <button type="button" @click="toggleSidebar()" class="text-slate-400 hover:text-slate-900 transition-colors" :title="sidebarOpen ? '{{ __('Fermer le panneau') }}' : '{{ __('Ouvrir le panneau') }}'">
                            <iconify-icon icon="solar:close-circle-linear" width="20"></iconify-icon>
                        </button>
                    </div>
                    <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar p-5">
                        @include('livewire.tickets.partials.discussion-sidebar-content')
                    </div>
                </div>
            </aside>
        </section>

        {{-- SECTION DISCUSSION : uniquement fil de discussion + composer. Cible pour récupération discussion. --}}
        <section id="ticket-discussion-section" aria-label="{{ __('Discussion') }}" class="flex-1 flex flex-col min-w-0 min-h-0 overflow-hidden bg-slate-50/50">
            <!-- Header (sticky pour rester visible sous le header de l'app) -->
            <header class="sticky top-0 z-20 shrink-0 bg-white border-b border-slate-100 px-3 py-2.5 sm:px-6 sm:py-3 shadow-sm safe-area-inset-top" style="padding-top: max(0.625rem, env(safe-area-inset-top));">
                <div class="flex flex-wrap items-center justify-between gap-2 sm:gap-3">
                    <nav class="flex items-center gap-1.5 sm:gap-2 min-w-0 flex-1 text-xs sm:text-sm" style="min-width: 0;">
                        <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-1 shrink-0 text-slate-500 hover:text-slate-900 transition-colors touch-manipulation py-1">
                            <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
                            <span class="hidden sm:inline font-medium">{{ __('Retour') }}</span>
                        </a>
                        <span class="text-slate-300 shrink-0">/</span>
                        <div class="flex items-center gap-1.5 sm:gap-2 min-w-0 flex-1 overflow-hidden">
                            <span class="font-mono text-xs font-bold text-slate-400 shrink-0">#{{ $ticket->id }}</span>
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
                        <button type="button" @click="togglePanel()" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-colors lg:hidden" title="{{ __('Infos ticket') }}">
                            <iconify-icon icon="solar:sidebar-minimalistic-linear" width="20"></iconify-icon>
                        </button>
                        <button type="button" @click="toggleSidebar()" class="hidden lg:flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-colors" :title="sidebarOpen ? '{{ __('Fermer le panneau') }}' : '{{ __('Ouvrir le panneau Infos') }}'">
                            <iconify-icon icon="solar:sidebar-minimalistic-linear" width="20" class="transition-transform" :class="sidebarOpen ? 'rotate-180' : ''"></iconify-icon>
                        </button>
                        <button type="button" wire:click="$refresh" wire:loading.attr="disabled" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-colors" title="{{ __('Actualiser') }}">
                            <iconify-icon icon="solar:refresh-linear" width="18" class="wire-loading:animate-spin"></iconify-icon>
                        </button>
                        @if($canEditTicket ?? false)
                            <button type="button" wire:click="openEditTicketModal" class="inline-flex items-center gap-1.5 h-9 px-3 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm font-medium hover:bg-slate-50 hover:text-slate-900 transition-colors" title="{{ __('Modifier le ticket') }}">
                                <iconify-icon icon="solar:pen-linear" width="18"></iconify-icon>
                                <span class="hidden sm:inline">{{ __('Modifier') }}</span>
                            </button>
                        @endif
                    </div>
                </div>
            </header>

            <!-- Messages Scroll Area (flex-1 + min-h-0 so this div gets bounded height and scrolls) -->
            <div id="discussion-messages" class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden custom-scrollbar overscroll-contain" x-data="{ tab: 'discussion' }">
                <div class="mx-auto w-full max-w-4xl px-3 py-4 sm:px-6 sm:py-8" style="padding-left: max(0.75rem, env(safe-area-inset-left)); padding-right: max(0.75rem, env(safe-area-inset-right));">

                    <!-- Tabs -->
                    <div class="flex flex-wrap items-center justify-center gap-1 rounded-xl bg-slate-100 p-1 mb-4 sm:mb-8 w-full max-w-full mx-auto">
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

                    {{-- Discussion Tab --}}
                    <div id="discussion-tab-content" x-show="tab === 'discussion'" x-cloak class="space-y-6 sm:space-y-8">
                        <!-- Ticket Summary Card (aligné comme la liste : Sujet, Créateur | Assigné, Catégorie, Priorité, Statut, Activité) -->
                        <div class="rounded-xl sm:rounded-2xl border border-slate-200 bg-white p-3 sm:p-5 shadow-sm min-w-0 overflow-hidden">
                            {{-- Ligne type liste : #id + Sujet --}}
                            <div class="flex items-start gap-2 sm:gap-4 min-w-0">
                                <span class="flex h-8 w-8 sm:h-10 sm:w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-500 font-mono">
                                    #{{ $ticket->id }}
                                </span>
                                <div class="min-w-0 flex-1 overflow-hidden">
                                    <h1 class="text-sm sm:text-lg font-bold text-slate-900 tracking-tight break-words">{{ $ticket->subject }}</h1>
                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ $creator?->name ?? __('Inconnu') }}
                                        @if($ticket->assignees->isNotEmpty())
                                            <span class="mx-1 text-slate-300">|</span>
                                            {{ __('Assigné à') }} <span class="font-medium text-slate-700">{{ $ticket->assignees->first()->name }}</span>
                                            @if($ticket->assignees->count() > 1)
                                                <span class="text-slate-400">+{{ $ticket->assignees->count() - 1 }}</span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Grille détail : Catégorie, Priorité, Statut, Activité (comme les colonnes de la liste) --}}
                            <div class="mt-3 sm:mt-4 grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-4 border-t border-slate-100 pt-3 sm:pt-4">
                                <div class="min-w-0">
                                    <div class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-500 truncate">{{ __('Catégorie') }}</div>
                                    <div class="mt-0.5 text-xs sm:text-sm text-slate-700 truncate">{{ $ticket->category?->name ?? '—' }}</div>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('Priorité') }}</div>
                                    <div class="mt-0.5 flex items-center gap-1.5 min-w-0">
                                        <span class="h-2 w-2 sm:h-2.5 sm:w-2.5 shrink-0 rounded-full {{ $priorityDot }}"></span>
                                        <span class="text-xs sm:text-sm font-medium text-slate-700 truncate">{{ $ticket->priority?->name ?? '—' }}</span>
                                    </div>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('Statut') }}</div>
                                    <div class="mt-0.5">
                                        <span class="inline-flex items-center gap-1 sm:gap-1.5 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold bg-[var(--accent-soft)] text-[var(--accent)] truncate max-w-full">
                                            <iconify-icon icon="solar:bolt-circle-bold-duotone" width="10" class="sm:w-3 shrink-0"></iconify-icon>
                                            <span class="truncate">{{ $statusLabels[$ticket->status->value] ?? $ticket->status->value }}</span>
                                        </span>
                                    </div>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('Activité') }}</div>
                                    <div class="mt-0.5 text-xs sm:text-sm text-slate-600 truncate">{{ $ticket->updated_at?->diffForHumans() }}</div>
                                </div>
                            </div>

                            {{-- Description --}}
                            <div class="mt-4 sm:mt-6 pt-3 sm:pt-4 border-t border-slate-100 text-sm sm:text-base leading-relaxed text-slate-700 max-w-none break-words">
                                {!! nl2br(e($ticket->description ?? '')) !!}
                            </div>

                            @if(!empty($ticketAttachments))
                                <div class="mt-6 pt-4 border-t border-slate-100 flex flex-wrap gap-2 min-w-0">
                                    @foreach($ticketAttachments as $att)
                                        @include('livewire.tickets.partials.attachment-link', ['att' => $att, 'variant' => 'theirs'])
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Timeline (messages de la discussion) -->
                        @php
                            $messagesByDate = $ticket->messages->groupBy(fn ($m) => $m->created_at->format('Y-m-d'));
                            $discussionMessagesByDate = $messagesByDate
                                ->map(function ($msgs) use ($canSeeInternalNotes) {
                                    if (! $canSeeInternalNotes) {
                                        return $msgs->where('type', '!=', TicketMessageType::InternalNote);
                                    }
                                    return $msgs;
                                })
                                ->filter(fn ($msgs) => $msgs->isNotEmpty());
                        @endphp
                        <div class="relative pl-4 sm:pl-6 border-l-2 border-slate-200 ml-0 sm:ml-4 space-y-1 min-w-0 overflow-hidden" data-timeline="discussion">
                            @forelse($discussionMessagesByDate as $date => $msgs)
                                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 mt-6 first:mt-0 pl-2">{{ \Carbon\Carbon::parse($date)->translatedFormat('l d F') }}</div>
                                @foreach($msgs as $msg)
                                    @include('livewire.tickets.partials.timeline-item', ['msg' => $msg, 'ticket' => $ticket])
                                @endforeach
                            @empty
                                <div data-empty-discussion class="py-10 sm:py-12 text-center">
                                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-500 mb-3">
                                        <iconify-icon icon="solar:chat-line-linear" width="24"></iconify-icon>
                                    </div>
                                    <p class="text-sm font-medium text-slate-600">{{ __('La discussion commence ici.') }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ __('Utilisez le formulaire ci-dessous pour envoyer un message.') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Notes Tab --}}
                    @if($canSeeInternalNotes)
                    <div id="notes-tab-content" x-show="tab === 'notes'" x-cloak class="space-y-6">
                        @php $notesByDate = $ticket->messages->where('type', TicketMessageType::InternalNote)->groupBy(fn ($m) => $m->created_at->format('Y-m-d')); @endphp
                        <div class="relative pl-6 border-l-2 border-slate-100 ml-4 space-y-2" data-timeline="notes">
                            @forelse($notesByDate as $date => $msgs)
                                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 pl-2">{{ \Carbon\Carbon::parse($date)->translatedFormat('l d F') }}</div>
                                @foreach($msgs as $msg)
                                    @include('livewire.tickets.partials.timeline-item', ['msg' => $msg, 'ticket' => $ticket])
                                @endforeach
                            @empty
                                <div data-empty-notes class="py-12 text-center">
                                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-amber-50 text-amber-400 mb-3">
                                        <iconify-icon icon="solar:lock-keyhole-linear" width="24"></iconify-icon>
                                    </div>
                                    <p class="text-sm text-slate-500">{{ __('Aucune note interne pour le moment.') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Composer (compact) -->
            <div class="shrink-0 bg-white border-t border-slate-200 p-2 sm:p-3 z-10 safe-area-pb min-w-0 overflow-hidden" data-composer>
                <div class="mx-auto max-w-2xl min-w-0">
                    <div class="flex items-center gap-1.5 mb-1.5 flex-wrap">
                        <button type="button" wire:click="setAsInternalNote(false)" class="text-[11px] sm:text-xs font-bold transition-colors border-b-2 pb-0.5 {{ !$asInternalNote ? 'text-slate-900 border-[var(--accent)]' : 'text-slate-500 border-transparent hover:text-slate-900' }}">
                            {{ __('Répondre') }}
                        </button>
                        @if($canWriteInternalNotes)
                            <button type="button" wire:click="setAsInternalNote(true)" class="text-[11px] sm:text-xs font-bold transition-colors border-b-2 pb-0.5 flex items-center gap-1 {{ $asInternalNote ? 'text-amber-700 border-amber-500' : 'text-slate-500 border-transparent hover:text-slate-900' }}">
                                <iconify-icon icon="solar:lock-keyhole-bold-duotone" width="10"></iconify-icon>
                                {{ __('Note interne') }}
                            </button>
                        @endif
                    </div>

                    <form wire:submit="sendMessage" x-on:submit="localStorage.removeItem('ticket-draft-' + {{ $ticketId }})" class="relative rounded-lg sm:rounded-xl bg-slate-50 border border-slate-200 shadow-sm focus-within:ring-2 focus-within:ring-[var(--accent)] focus-within:border-transparent transition-all min-w-0"
                    x-data="{
                        users: {{ \Illuminate\Support\Js::from($mentionableUsers ?? []) }},
                        mentionOpen: false,
                        mentionQuery: '',
                        mentionStart: 0,
                        mentionCursor: 0,
                        draftTimer: null,
                        draftKey: 'ticket-draft-' + {{ $ticketId }},
                        init() {
                            try {
                                const saved = localStorage.getItem(this.draftKey);
                                if (saved && !this.$wire.get('body')) {
                                    this.$wire.set('body', saved);
                                }
                            } catch (e) {}
                        },
                        get filteredMentions() {
                            if (!this.mentionQuery) return this.users.slice(0, 8);
                            const q = this.mentionQuery.toLowerCase();
                            return this.users.filter(u =>
                                (u.tag && u.tag.toLowerCase().startsWith(q)) ||
                                (u.name && u.name.toLowerCase().includes(q))
                            ).slice(0, 8);
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
                        onInput(ev) {
                            const el = ev.target;
                            const val = el.value;
                            this.saveDraft(val);
                            const pos = el.selectionStart || 0;
                            const before = val.slice(0, pos);
                            const lastAt = before.lastIndexOf('@');
                            if (lastAt === -1) { this.mentionOpen = false; return; }
                            const afterAt = before.slice(lastAt + 1);
                            if (/[\s\n]/.test(afterAt)) { this.mentionOpen = false; return; }
                            this.mentionStart = lastAt;
                            this.mentionCursor = pos;
                            this.mentionQuery = afterAt;
                            this.mentionOpen = true;
                        },
                        pickUser(user) {
                            const el = this.$refs.mentionInput;
                            if (!el) return;
                            const val = el.value;
                            const newVal = val.slice(0, this.mentionStart) + '@' + user.tag + ' ' + val.slice(this.mentionCursor);
                            this.$wire.set('body', newVal);
                            this.mentionOpen = false;
                            this.$nextTick(() => { el.focus(); el.setSelectionRange(this.mentionStart + user.tag.length + 2, this.mentionStart + user.tag.length + 2); });
                        }
                    }"
                    @keydown.escape="mentionOpen = false">
                        <div class="p-1 sm:p-1.5 relative">
                            <textarea
                                x-ref="mentionInput"
                                wire:model="body"
                                rows="2"
                                @input="onInput($event)"
                                @keydown.arrow-down.prevent="mentionOpen && filteredMentions.length && (mentionOpen = true)"
                                class="w-full bg-transparent border-0 text-slate-900 placeholder:text-slate-400 focus:ring-0 resize-none text-xs p-1 sm:p-1.5 min-h-[2.5rem] sm:min-h-[2.75rem]"
                                placeholder="{{ $asInternalNote ? __('Ajouter une note visible uniquement par l\'équipe...') : __('Écrivez votre réponse ici...') }}"
                            ></textarea>
                            <div x-show="mentionOpen" x-cloak @click.outside="mentionOpen = false"
                                class="absolute left-1 right-1 sm:left-1.5 sm:right-1.5 bottom-full mb-1 py-1 bg-white border border-slate-200 rounded-lg shadow-lg z-50 max-h-36 overflow-y-auto"
                                style="display: none;" wire:ignore>
                                <template x-for="u in filteredMentions" :key="u.id">
                                    <button type="button" @click="pickUser(u)"
                                        class="w-full text-left px-3 py-2 text-sm hover:bg-slate-50 flex items-center gap-2">
                                        <span class="font-medium text-slate-900" x-text="u.name"></span>
                                        <span class="text-slate-400 text-xs" x-text="'@' + u.tag"></span>
                                    </button>
                                </template>
                                <p x-show="filteredMentions.length === 0" class="px-3 py-2 text-xs text-slate-500">{{ __('Aucun utilisateur') }}</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-1.5 px-2 py-1.5 border-t border-slate-200/50 bg-white/50 rounded-b-lg sm:rounded-b-xl">
                            <div class="flex items-center gap-0.5 min-w-0 flex-1 sm:flex-initial">
                                <input type="file" wire:model="attachmentFiles" multiple class="hidden" id="discussion-file-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.gif,.webp,image/*">
                                <button type="button" onclick="document.getElementById('discussion-file-input').click()" class="p-1.5 min-h-[32px] min-w-[32px] sm:min-h-0 sm:min-w-0 flex items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors touch-manipulation" title="{{ __('Joindre un fichier') }}">
                                    <iconify-icon icon="solar:paperclip-linear" width="16"></iconify-icon>
                                </button>
                                <div x-data="{ emojiOpen: false }" class="relative">
                                    <button type="button" @click="emojiOpen = !emojiOpen" class="p-1.5 min-h-[32px] min-w-[32px] sm:min-h-0 sm:min-w-0 flex items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors touch-manipulation" title="{{ __('Emoji') }}">
                                        <iconify-icon icon="solar:smile-circle-linear" width="16"></iconify-icon>
                                    </button>
                                    <div x-show="emojiOpen" @click.outside="emojiOpen = false" x-cloak class="absolute bottom-full left-0 mb-1 p-1.5 rounded-lg bg-white shadow-xl border border-slate-200 grid grid-cols-8 gap-1 max-h-36 overflow-y-auto z-50 w-48">
                                        @foreach(['😀','😃','😄','😁','😅','😂','🤣','😊','😇','🙂','🙃','😉','😌','😍','🥰','😘','👍','👎','👏','🙌','👋','💪','✨','🔥','❤️','💯','✅','📎','📁','🔒'] as $emoji)
                                            <button type="button" @click="$wire.set('body', ($wire.get('body') || '') + '{{ $emoji }}'); emojiOpen = false" class="p-1.5 hover:bg-slate-100 rounded-lg text-xl transition-colors">{{ $emoji }}</button>
                                        @endforeach
                                    </div>
                                </div>
                                @if(count($attachmentFiles ?? []) > 0)
                                    <span class="ml-1 text-[10px] sm:text-xs font-medium text-[var(--accent)] bg-[var(--accent-soft)] px-1.5 py-0.5 rounded">{{ count($attachmentFiles) }} fichier(s)</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                                <p class="text-[10px] text-slate-400 hidden sm:inline">{{ __('Markdown') }}</p>
                                <p class="text-[10px] text-slate-400">{{ __('Tapez') }} <kbd class="px-0.5 py-px rounded bg-slate-100 text-slate-600 font-mono text-[9px]">@</kbd> {{ __('pour mentionner') }}</p>
                                <button type="submit" x-ref="submitBtn" wire:loading.attr="disabled" wire:target="sendMessage" class="inline-flex items-center justify-center gap-1 rounded-lg px-3 py-1.5 min-h-[32px] sm:min-h-0 text-xs font-bold text-white shadow-sm hover:opacity-90 transition-all touch-manipulation disabled:opacity-70 disabled:cursor-not-allowed" style="background-color: {{ $asInternalNote ? '#d97706' : 'var(--accent)' }};">
                                    <span wire:loading.remove wire:target="sendMessage">{{ __('Envoyer') }}</span>
                                    <span wire:loading wire:target="sendMessage" class="inline-block h-3 w-3 rounded-full border-2 border-white border-t-transparent animate-spin"></span>
                                    <iconify-icon icon="solar:plain-bold" width="12" wire:loading.remove wire:target="sendMessage"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </form>
                    <x-input-error :messages="$errors->get('body')" class="mt-2" />
                </div>
            </div>
        </section>

        <!-- MOBILE DRAWER (panneau Infos = même section Détails, overlay sur mobile) -->
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
                    @include('livewire.tickets.partials.discussion-sidebar-content')
                </div>
            </div>
        </div>

        <!-- ADD PARTICIPANT MODAL (single instance, works for desktop + mobile) -->
        <div
            x-show="addParticipantOpen"
            x-cloak
            class="fixed inset-0 z-[80] flex items-center justify-center p-4 bg-black/50"
            @click.self="addParticipantOpen = false"
        >
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[80dvh] overflow-hidden border border-slate-200" @click.stop>
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-slate-900">{{ __('Ajouter à la discussion') }}</h3>
                    <button type="button" @click="addParticipantOpen = false" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition-colors" aria-label="{{ __('Fermer') }}">
                        <iconify-icon icon="solar:close-circle-linear" width="20"></iconify-icon>
                    </button>
                </div>
                <div class="p-2 overflow-y-auto max-h-[60dvh] custom-scrollbar">
                    @php
                        $availableUsers = $orgUsers->filter(fn ($u) =>
                            $u->id !== $ticket->created_by
                            && $u->id !== $ticket->assigned_to
                            && ! $ticket->participants->contains('id', $u->id)
                        );
                    @endphp

                    @forelse($availableUsers as $u)
                        <button
                            type="button"
                            wire:click="addParticipant({{ $u->id }})"
                            @click="addParticipantOpen = false"
                            class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 text-left transition-colors"
                        >
                            <div class="h-10 w-10 rounded-full flex items-center justify-center text-sm font-semibold shrink-0" style="background: var(--accent-soft); color: var(--accent);">
                                {{ strtoupper(mb_substr($u->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-slate-900 truncate">{{ $u->name }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ $u->email }}</p>
                            </div>
                        </button>
                    @empty
                        <div class="p-6 text-center">
                            <div class="mx-auto mb-3 inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                <iconify-icon icon="solar:users-group-rounded-linear" width="20"></iconify-icon>
                            </div>
                            <p class="text-sm text-slate-600">{{ __('Tous les membres sont déjà dans la discussion.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Modal d'édition du ticket (titre, description, pièces jointes) --}}
        @if($canEditTicket ?? false)
        <x-modal name="edit-ticket" maxWidth="2xl" focusable>
            <div class="p-6">
                <h2 class="text-lg font-bold text-slate-900 mb-4">{{ __('Modifier le ticket') }}</h2>
                <div class="space-y-4">
                    <div>
                        <label for="edit-subject" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('Titre') }}</label>
                        <input id="edit-subject" type="text" wire:model="editSubject" wire:blur="updateSubject" class="block w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]" placeholder="{{ __('Sujet du ticket') }}" />
                        @error('editSubject')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="edit-description" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('Description') }}</label>
                        <textarea id="edit-description" wire:model="editDescription" wire:blur="updateDescription" rows="4" class="block w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]" placeholder="{{ __('Description') }}"></textarea>
                        @error('editDescription')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-slate-700 mb-2">{{ __('Pièces jointes du ticket') }}</div>
                        @php
                            $editFiles = is_array($ticket->attachments) ? ($ticket->attachments['files'] ?? []) : [];
                            $editLinks = is_array($ticket->attachments) ? ($ticket->attachments['links'] ?? []) : [];
                        @endphp
                        <div class="space-y-2 mb-3">
                            @foreach($editFiles as $idx => $f)
                                @php $f = is_array($f) ? $f : []; @endphp
                                <div class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                                    <iconify-icon icon="solar:file-text-linear" width="18" class="text-slate-500 shrink-0"></iconify-icon>
                                    <span class="text-sm font-medium text-slate-800 truncate flex-1 min-w-0">{{ $f['name'] ?? __('Fichier') }}</span>
                                    <button type="button" wire:click="removeTicketAttachment('files', {{ $idx }})" wire:confirm="{{ __('Supprimer cette pièce jointe ?') }}" class="shrink-0 text-slate-400 hover:text-red-500 transition-colors" title="{{ __('Supprimer') }}">
                                        <iconify-icon icon="solar:trash-bin-trash-linear" width="16"></iconify-icon>
                                    </button>
                                </div>
                            @endforeach
                            @foreach($editLinks as $idx => $l)
                                @php $l = is_array($l) ? $l : []; $linkUrl = $l['url'] ?? '#'; @endphp
                                <div class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                                    <iconify-icon icon="solar:link-linear" width="18" class="text-slate-500 shrink-0"></iconify-icon>
                                    <a href="{{ $linkUrl }}" target="_blank" rel="noopener" class="text-sm font-medium text-[var(--accent)] truncate flex-1 min-w-0">{{ $linkUrl }}</a>
                                    <button type="button" wire:click="removeTicketAttachment('links', {{ $idx }})" wire:confirm="{{ __('Supprimer ce lien ?') }}" class="shrink-0 text-slate-400 hover:text-red-500 transition-colors" title="{{ __('Supprimer') }}">
                                        <iconify-icon icon="solar:trash-bin-trash-linear" width="16"></iconify-icon>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <div class="min-w-0">
                                <input type="file" wire:model="editAttachmentFiles" wire:change="addTicketAttachmentFile" class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-[var(--accent-soft)] file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-[var(--accent)] hover:file:opacity-90" />
                                <p class="mt-1 text-xs text-slate-500">{{ __('Max 5 fichiers, 10 Mo chacun.') }}</p>
                            </div>
                            <div class="flex gap-2 flex-1 min-w-0">
                                <input type="url" wire:model="editLinkUrl" wire:keydown.enter.prevent="addTicketAttachmentLink" placeholder="{{ __('Ajouter un lien') }}" class="block flex-1 min-w-0 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]" />
                                <button type="button" wire:click="addTicketAttachmentLink" class="shrink-0 inline-flex items-center gap-1.5 rounded-lg bg-[var(--accent)] px-3 py-2 text-sm font-bold text-white hover:opacity-90">
                                    <iconify-icon icon="solar:link-linear" width="16"></iconify-icon>
                                    {{ __('Ajouter') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex flex-wrap justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal', 'edit-ticket')" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors">
                        {{ __('Fermer') }}
                    </button>
                </div>
            </div>
        </x-modal>
        @endif

        {{-- Modal de confirmation de suppression du ticket (remplace l'alerte native) --}}
        @if($canDeleteTicket ?? false)
        <x-modal name="confirm-delete-ticket" maxWidth="sm" focusable>
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                        <iconify-icon icon="solar:trash-bin-trash-bold-duotone" width="24"></iconify-icon>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">{{ __('Supprimer le ticket') }}</h2>
                        <p class="mt-0.5 text-sm text-slate-600">{{ __('Êtes-vous sûr de vouloir supprimer ce ticket ?') }}</p>
                    </div>
                </div>
                <p class="text-sm text-slate-500">{{ __('Le ticket sera masqué des listes. La suppression peut être annulée par un administrateur.') }}</p>
                <div class="mt-6 flex flex-wrap justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal', 'confirm-delete-ticket')" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors">
                        {{ __('Annuler') }}
                    </button>
                    <button type="button" wire:click="deleteTicket" wire:loading.attr="disabled" class="px-4 py-2.5 rounded-xl bg-red-600 text-white text-sm font-bold hover:bg-red-700 transition-colors inline-flex items-center gap-2 disabled:opacity-70">
                        <span wire:loading.remove wire:target="deleteTicket"><iconify-icon icon="solar:trash-bin-trash-bold-duotone" width="18"></iconify-icon></span>
                        <span wire:loading wire:target="deleteTicket" class="inline-block h-[18px] w-[18px] rounded-full border-2 border-white border-t-transparent animate-spin"></span>
                        <span>{{ __('Supprimer') }}</span>
                    </button>
                </div>
            </div>
        </x-modal>
        @endif
    </div>
</div>

@script
<script>
    Alpine.data('discussionWebSocket', (ticketId, currentUserId, canSeeInternalNotes) => ({
        ticketId,
        currentUserId,
        canSeeInternalNotes: !!canSeeInternalNotes,
        seenIds: new Set(),
        wsConnected: false,
        init() {
            const tryConnect = () => {
                if (typeof window.Echo !== 'undefined') {
                    const chan = window.Echo.private('ticket.' + this.ticketId);
                    chan.listen('.message.sent', (e) => this.appendMessage(e));
                    chan.subscribed(() => { this.wsConnected = true; });

                    // Internal notes are broadcasted only to staff channel.
                    if (this.canSeeInternalNotes) {
                        window.Echo.private('ticket.staff.' + this.ticketId)
                            .listen('.message.sent', (e) => this.appendMessage(e));
                    }
                    return;
                }
                setTimeout(tryConnect, 300);
            };
            tryConnect();
        },
        appendMessage(e) {
            // Dedup: skip if already seen or already in DOM
            if (e.id && (this.seenIds.has(e.id) || document.getElementById('message-' + e.id))) {
                return;
            }
            if (e.id) this.seenIds.add(e.id);

            const isNote = e.type === 'internal_note';

            const targets = [];
            // Always append normal messages to discussion timeline.
            if (!isNote) targets.push('discussion');
            // For internal notes, append inline to discussion (staff view) and also to notes tab if present.
            if (isNote) {
                targets.push('discussion');
                targets.push('notes');
            }

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

                const div = document.createElement('div');
                div.className = 'animate-enter';
                if (e.id) div.id = 'message-' + e.id;
                div.innerHTML = this.renderBubble(e);
                timeline.appendChild(div);
            }

            const scroll = document.getElementById('discussion-messages');
            if (scroll) scroll.scrollTop = scroll.scrollHeight;
        },
        fileUrl(att) {
            if (!att || !att.path) return '#';
            const origin = window.location.origin;
            if (String(att.path).startsWith('ticket-messages/')) {
                const filename = att.path.split('/').pop();
                return origin + '/tickets/' + this.ticketId + '/files/' + encodeURIComponent(filename);
            }
            return origin + '/storage/' + att.path;
        },
        attachmentsHtml(attachments, variant) {
            if (!Array.isArray(attachments) || attachments.length === 0) return '';
            const borderClass = variant === 'mine' ? 'border-t border-white/20' : 'border-t border-slate-100';
            const items = attachments.map(a => {
                const url = this.fileUrl(a);
                const name = this.escapeHtml(a.name || 'Fichier');
                const ext = (a.name || '').split('.').pop().toLowerCase();
                const isImg = ['png','jpg','jpeg','gif','webp'].includes(ext);
                if (isImg && url !== '#') {
                    return `<a href="${url}" target="_blank" rel="noopener" class="inline-block rounded-lg overflow-hidden border border-slate-200 max-w-[200px] mt-2"><img src="${url}" alt="${name}" class="block w-full h-auto max-h-36 object-cover" loading="lazy"></a>`;
                }
                return `<a href="${url}" target="_blank" rel="noopener" class="block mt-1 text-xs text-inherit opacity-90 hover:underline flex items-center gap-1"><iconify-icon icon="solar:file-linear" width="12"></iconify-icon> ${name}</a>`;
            }).join('');
            return `<div class="mt-2 pt-2 ${borderClass}">${items}</div>`;
        },
        renderBubble(e) {
            const isNote = e.type === 'internal_note';
            const isSystem = e.type === 'system';
            const isOwn = e.user_id && parseInt(e.user_id, 10) === parseInt(this.currentUserId, 10);
            const time = e.created_at ? new Date(e.created_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }) : '';
            const name = this.escapeHtml(e.user_name || '');
            const body = this.escapeHtml(e.body || '').replace(/\n/g, '<br>');
            const avatarUrl = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name || 'U') + '&size=32&background=random';
            const hasAttachments = (e.attachments && e.attachments.length > 0);
            const attachmentsBlock = this.attachmentsHtml(e.attachments || [], (isOwn && hasAttachments) ? 'theirs' : (isOwn ? 'mine' : 'theirs'));

            if (isSystem) {
                return `<div class="relative py-2"><div class="absolute -left-[27px] top-1 w-5 h-5 rounded-full bg-slate-100 border-2 border-white flex items-center justify-center text-slate-500"><iconify-icon icon="solar:user-linear" width="10"></iconify-icon></div><div class="pl-2 flex items-center gap-2 text-[11px] text-slate-500">${body} <span class="text-slate-400 text-[10px]">• ${time}</span></div></div>`;
            }
            if (isNote) {
                return `<div class="relative group py-3"><div class="absolute -left-[27px] top-4 w-5 h-5 rounded-full bg-amber-50 border-2 border-white flex items-center justify-center text-amber-600"><iconify-icon icon="solar:lock-keyhole-linear" width="10"></iconify-icon></div><div class="pl-2"><div class="bg-amber-50 border border-amber-200 rounded-xl p-4"><div class="flex items-center justify-between mb-2"><div class="flex items-center gap-1.5"><span class="text-[11px] font-semibold text-amber-800">Note interne</span></div><span class="text-[10px] text-amber-600">${name} • ${time}</span></div><div class="text-[13px] leading-snug text-amber-900">${body}</div>${attachmentsBlock}</div></div></div>`;
            }
            if (isOwn) {
                const bubbleClass = hasAttachments ? 'rounded-2xl rounded-br-md px-4 py-3 text-[14px] leading-relaxed shadow-sm bg-white border border-slate-200 text-slate-700' : 'rounded-2xl rounded-br-md px-4 py-3 text-[14px] leading-relaxed text-white shadow-sm';
                const bubbleStyle = hasAttachments ? '' : ' style="background-color: var(--accent);"';
                const bodyClass = hasAttachments ? 'text-left' : 'text-left text-white';
                return `<div class="flex justify-end py-4"><div class="flex items-end gap-2 max-w-[85%]"><div class="flex flex-col items-end"><div class="flex items-center gap-1.5 mb-2 flex-row-reverse"><span class="text-[12px] font-semibold text-slate-900">${name}</span><span class="text-[10px] text-slate-400">${time}</span></div><div class="${bubbleClass}"${bubbleStyle}><div class="${bodyClass}">${body}</div>${attachmentsBlock}</div></div><img src="${avatarUrl}" class="w-8 h-8 rounded-full ring-2 ring-white shadow shrink-0 object-cover" alt=""></div></div>`;
            }
            return `<div class="relative py-4"><div class="absolute -left-[27px] top-3 w-8 h-8 rounded-full bg-white ring-2 ring-slate-200 overflow-hidden shrink-0 shadow-sm"><img src="${avatarUrl}" class="w-full h-full object-cover" alt=""></div><div class="pl-2 pr-2"><div class="flex items-baseline justify-between gap-2 mb-2"><div class="flex items-center gap-1.5"><span class="text-[12px] font-semibold text-slate-900">${name}</span></div><span class="text-[10px] text-slate-400 shrink-0">${time}</span></div><div class="rounded-2xl rounded-bl-md px-4 py-3 text-[14px] leading-relaxed bg-white border border-slate-200 text-slate-700 shadow-sm">${body}${attachmentsBlock}</div></div></div>`;
        },
        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text ?? '';
            return div.innerHTML;
        }
    }));
</script>
@endscript
