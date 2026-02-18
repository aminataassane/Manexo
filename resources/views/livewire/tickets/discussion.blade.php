@php
    use App\Enums\TicketMessageType;
    use App\Enums\TicketStatus;
    $notesCount = $ticket->messages->where('type', TicketMessageType::InternalNote)->count();
    $lastActivity = $ticket->messages->last()?->created_at ?? $ticket->updated_at;
    $allAttachments = $ticket->messages->flatMap(fn ($m) => is_array($m->attachments) ? $m->attachments : [])->filter()->values();
    $creator = $ticket->creator;
    $assignee = $ticket->assignee;
    $discussionUsers = collect([$creator, $assignee])->merge($ticket->participants)->filter()->unique('id');
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
        'open' => __('Ouvert'),
        'in_progress' => __('En cours'),
        'pending' => __('En attente'),
        'resolved' => __('Résolu'),
        'closed' => __('Fermé'),
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
    class="flex flex-col min-h-0 rounded-2xl overflow-hidden bg-white border border-slate-200 shadow-sm"
    style="height: calc(100dvh - 6.5rem); min-height: 20rem;"
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
            <header class="sticky top-0 z-20 shrink-0 bg-white border-b border-slate-100 px-4 py-3 sm:px-6 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <nav class="flex items-center gap-2 min-w-0 text-sm">
                        <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-1.5 text-slate-500 hover:text-slate-900 transition-colors">
                            <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
                            <span class="hidden sm:inline font-medium">{{ __('Retour') }}</span>
                        </a>
                        <span class="text-slate-300">/</span>
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="font-mono text-xs font-bold text-slate-400">#{{ $ticket->id }}</span>
                            <span class="font-semibold text-slate-900 truncate max-w-[200px] sm:max-w-md">{{ $ticket->subject }}</span>
                        </div>
                    </nav>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="togglePanel()" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-colors lg:hidden" title="{{ __('Infos ticket') }}">
                            <iconify-icon icon="solar:sidebar-minimalistic-linear" width="20"></iconify-icon>
                        </button>
                        <button type="button" @click="toggleSidebar()" class="hidden lg:flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-colors" :title="sidebarOpen ? '{{ __('Fermer le panneau') }}' : '{{ __('Ouvrir le panneau Infos') }}'">
                            <iconify-icon icon="solar:sidebar-minimalistic-linear" width="20" class="transition-transform" :class="sidebarOpen ? 'rotate-180' : ''"></iconify-icon>
                        </button>
                        <button type="button" wire:click="$refresh" wire:loading.attr="disabled" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-colors" title="{{ __('Actualiser') }}">
                            <iconify-icon icon="solar:refresh-linear" width="18" class="wire-loading:animate-spin"></iconify-icon>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Messages Scroll Area (flex-1 + min-h-0 so this div gets bounded height and scrolls) -->
            <div id="discussion-messages" class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden custom-scrollbar overscroll-contain" x-data="{ tab: 'discussion' }">
                <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6 sm:py-8">
                    
                    <!-- Tabs -->
                    <div class="flex items-center gap-1 rounded-xl bg-slate-100 p-1 mb-6 sm:mb-8 w-fit mx-auto">
                        <button type="button" @click="tab = 'discussion'" class="rounded-lg px-4 py-1.5 text-sm font-semibold transition-all" :class="tab === 'discussion' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'">
                            {{ __('Discussion') }}
                        </button>
                        @if($canSeeInternalNotes)
                            <button type="button" @click="tab = 'notes'" class="flex items-center gap-1.5 rounded-lg px-4 py-1.5 text-sm font-semibold transition-all" :class="tab === 'notes' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'">
                                <iconify-icon icon="solar:lock-keyhole-bold-duotone" width="14"></iconify-icon>
                                {{ __('Notes internes') }}
                                @if($notesCount > 0)
                                    <span class="ml-1 px-1.5 py-0.5 rounded-full bg-slate-200 text-[10px]">{{ $notesCount }}</span>
                                @endif
                            </button>
                        @endif
                    </div>

                    {{-- Discussion Tab --}}
                    <div id="discussion-tab-content" x-show="tab === 'discussion'" x-cloak class="space-y-6 sm:space-y-8">
                        <!-- Ticket Summary Card (aligné comme la liste : Sujet, Créateur | Assigné, Catégorie, Priorité, Statut, Activité) -->
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
                            {{-- Ligne type liste : #id + Sujet --}}
                            <div class="flex items-start gap-3 sm:gap-4 min-w-0">
                                <span class="flex h-9 w-9 sm:h-10 sm:w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-500 font-mono">
                                    #{{ $ticket->id }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight break-words">{{ $ticket->subject }}</h1>
                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ $creator?->name ?? __('Inconnu') }}
                                        @if($assignee)
                                            <span class="mx-1 text-slate-300">|</span>
                                            {{ __('Assigné à') }} <span class="font-medium text-slate-700">{{ $assignee->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Grille détail : Catégorie, Priorité, Statut, Activité (comme les colonnes de la liste) --}}
                            <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 border-t border-slate-100 pt-4">
                                <div>
                                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('Catégorie') }}</div>
                                    <div class="mt-0.5 text-sm text-slate-700">{{ $ticket->category?->name ?? '—' }}</div>
                                </div>
                                <div>
                                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('Priorité') }}</div>
                                    <div class="mt-0.5 flex items-center gap-2">
                                        <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $priorityDot }}"></span>
                                        <span class="text-sm font-medium text-slate-700">{{ $ticket->priority?->name ?? '—' }}</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('Statut') }}</div>
                                    <div class="mt-0.5">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-[var(--accent-soft)] text-[var(--accent)]">
                                            <iconify-icon icon="solar:bolt-circle-bold-duotone" width="12"></iconify-icon>
                                            {{ $statusLabels[$ticket->status->value] ?? $ticket->status->value }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('Activité') }}</div>
                                    <div class="mt-0.5 text-sm text-slate-600">{{ $ticket->updated_at?->diffForHumans() }}</div>
                                </div>
                            </div>

                            {{-- Description --}}
                            <div class="mt-5 sm:mt-6 pt-4 border-t border-slate-100 text-base leading-relaxed text-slate-700 max-w-none break-words">
                                {!! nl2br(e($ticket->description ?? '')) !!}
                            </div>

                            @if(!empty($ticketAttachments))
                                <div class="mt-6 pt-4 border-t border-slate-100 flex flex-wrap gap-2">
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
                        <div class="relative pl-6 border-l-2 border-slate-200 ml-2 sm:ml-4 space-y-1" data-timeline="discussion">
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

            <!-- Composer -->
            <div class="shrink-0 bg-white border-t border-slate-200 p-4 sm:p-6 z-10" data-composer>
                <div class="mx-auto max-w-4xl">
                    <div class="flex items-center gap-4 mb-3">
                        <button type="button" wire:click="setAsInternalNote(false)" class="text-sm font-bold transition-colors border-b-2 pb-0.5 {{ !$asInternalNote ? 'text-slate-900 border-[var(--accent)]' : 'text-slate-500 border-transparent hover:text-slate-900' }}">
                            {{ __('Répondre') }}
                        </button>
                        @if($canWriteInternalNotes)
                            <button type="button" wire:click="setAsInternalNote(true)" class="text-sm font-bold transition-colors border-b-2 pb-0.5 flex items-center gap-1.5 {{ $asInternalNote ? 'text-amber-700 border-amber-500' : 'text-slate-500 border-transparent hover:text-slate-900' }}">
                                <iconify-icon icon="solar:lock-keyhole-bold-duotone" width="14"></iconify-icon>
                                {{ __('Note interne') }}
                            </button>
                        @endif
                    </div>

                    <form wire:submit="sendMessage" class="relative rounded-2xl bg-slate-50 border border-slate-200 shadow-sm focus-within:ring-2 focus-within:ring-[var(--accent)] focus-within:border-transparent transition-all">
                        <div class="p-2">
                            <textarea
                                wire:model="body"
                                rows="3"
                                class="w-full bg-transparent border-0 text-slate-900 placeholder:text-slate-400 focus:ring-0 resize-none text-sm p-2"
                                placeholder="{{ $asInternalNote ? __('Ajouter une note visible uniquement par l\'équipe...') : __('Écrivez votre réponse ici...') }}"
                            ></textarea>
                        </div>
                        
                        <div class="flex items-center justify-between px-3 py-2 border-t border-slate-200/50 bg-white/50 rounded-b-2xl">
                            <div class="flex items-center gap-1">
                                <input type="file" wire:model="attachmentFiles" multiple class="hidden" id="discussion-file-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.gif,.webp,image/*">
                                <button type="button" onclick="document.getElementById('discussion-file-input').click()" class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors" title="{{ __('Joindre un fichier') }}">
                                    <iconify-icon icon="solar:paperclip-linear" width="20"></iconify-icon>
                                </button>
                                <div x-data="{ emojiOpen: false }" class="relative">
                                    <button type="button" @click="emojiOpen = !emojiOpen" class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors" title="{{ __('Emoji') }}">
                                        <iconify-icon icon="solar:smile-circle-linear" width="20"></iconify-icon>
                                    </button>
                                    <div x-show="emojiOpen" @click.outside="emojiOpen = false" x-cloak class="absolute bottom-full left-0 mb-2 p-2 rounded-xl bg-white shadow-xl border border-slate-200 grid grid-cols-8 gap-1 max-h-48 overflow-y-auto z-50 w-64">
                                        @foreach(['😀','😃','😄','😁','😅','😂','🤣','😊','😇','🙂','🙃','😉','😌','😍','🥰','😘','👍','👎','👏','🙌','👋','💪','✨','🔥','❤️','💯','✅','📎','📁','🔒'] as $emoji)
                                            <button type="button" @click="$wire.set('body', ($wire.get('body') || '') + '{{ $emoji }}'); emojiOpen = false" class="p-1.5 hover:bg-slate-100 rounded-lg text-xl transition-colors">{{ $emoji }}</button>
                                        @endforeach
                                    </div>
                                </div>
                                @if(count($attachmentFiles ?? []) > 0)
                                    <span class="ml-2 text-xs font-medium text-[var(--accent)] bg-[var(--accent-soft)] px-2 py-1 rounded-md">{{ count($attachmentFiles) }} fichier(s)</span>
                                @endif
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <p class="text-xs text-slate-400 hidden sm:block">{{ __('Markdown supporté') }}</p>
                                <button type="submit" x-ref="submitBtn" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-bold text-white shadow-md hover:opacity-90 transition-all" style="background-color: {{ $asInternalNote ? '#d97706' : 'var(--accent)' }};">
                                    <span>{{ __('Envoyer') }}</span>
                                    <iconify-icon icon="solar:plain-bold" width="16"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </form>
                    <x-input-error :messages="$errors->get('body')" class="mt-2" />
                </div>
            </div>
        </section>

        <!-- MOBILE DRAWER (panneau Infos = même section Détails, overlay sur mobile) -->
        <div x-show="mobileDrawerOpen" x-cloak class="lg:hidden fixed inset-0 z-50" style="display: none;">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" x-show="mobileDrawerOpen" x-transition.opacity @click="mobileDrawerOpen = false"></div>
            <div class="absolute right-0 top-0 bottom-0 w-full max-w-sm bg-white shadow-2xl flex flex-col" 
                 x-show="mobileDrawerOpen"
                 x-transition:enter="transform transition ease-out duration-300" 
                 x-transition:enter-start="translate-x-full" 
                 x-transition:enter-end="translate-x-0" 
                 x-transition:leave="transform transition ease-in duration-300" 
                 x-transition:leave-start="translate-x-0" 
                 x-transition:leave-end="translate-x-full">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 shrink-0">
                    <h2 class="text-lg font-bold text-slate-900">{{ __('Infos') }}</h2>
                    <button type="button" @click="mobileDrawerOpen = false" class="text-slate-400 hover:text-slate-900 transition-colors">
                        <iconify-icon icon="solar:close-circle-bold" width="24"></iconify-icon>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto p-5 custom-scrollbar">
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
    </div>
</div>

@script
<script>
    Alpine.data('discussionWebSocket', (ticketId, currentUserId, canSeeInternalNotes) => ({
        ticketId,
        currentUserId,
        canSeeInternalNotes: !!canSeeInternalNotes,
        init() {
            const tryConnect = () => {
                if (typeof window.Echo !== 'undefined') {
                window.Echo.private('ticket.' + this.ticketId)
                    .listen('.message.sent', (e) => this.appendMessage(e));

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
