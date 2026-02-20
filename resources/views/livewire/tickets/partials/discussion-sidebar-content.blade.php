{{-- Panneau "Infos" (design dédié, pas un dump de détails) --}}
<div class="flex flex-col h-full gap-4">
    <!-- Aperçu -->
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('Ticket') }}</div>
                <div class="mt-1 flex items-center gap-2 min-w-0">
                    <span class="text-xs font-mono font-bold text-slate-400">#{{ $ticket->id }}</span>
                    <span class="text-sm font-bold text-slate-900 truncate">{{ $ticket->subject }}</span>
                </div>
            </div>
            <a href="{{ route('tickets.discussion', $ticket->id) }}" class="shrink-0 inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                <iconify-icon icon="solar:arrow-right-linear" width="14"></iconify-icon>
                {{ __('Ouvrir') }}
            </a>
        </div>

        <div class="mt-3 flex flex-wrap gap-2">
            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold bg-[var(--accent-soft)] text-[var(--accent)]">
                <iconify-icon icon="solar:bolt-circle-bold-duotone" width="14"></iconify-icon>
                {{ $statusLabels[$ticket->status->value] ?? $ticket->status->value }}
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold bg-slate-100 text-slate-700">
                <span class="h-1.5 w-1.5 rounded-full {{ $priorityDot }}"></span>
                {{ optional($ticket->priority)->name ?? '—' }}
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold bg-slate-100 text-slate-700">
                <iconify-icon icon="solar:tag-bold-duotone" width="14"></iconify-icon>
                {{ optional($ticket->category)->name ?? '—' }}
            </span>
        </div>

        <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('Assigné') }}</div>
                <div class="mt-1 flex flex-col gap-2 min-w-0">
                    @if($assignee)
                        <div class="flex items-center gap-2 min-w-0">
                            <x-avatar :name="$assignee->name" size="h-6 w-6" class="ring-1 ring-slate-200 shrink-0" />
                            <span class="text-sm font-semibold text-slate-900 truncate">{{ $assignee->name }}</span>
                        </div>
                    @else
                        <span class="text-sm text-slate-400 italic">—</span>
                    @endif
                    @if($canAssignTicket ?? false)
                        <div class="flex flex-wrap gap-1.5">
                            @if(!$assignee || (auth()->id() && (int)$assignee->id !== (int)auth()->id()))
                                <button type="button" wire:click="assignToMe" class="inline-flex items-center gap-1 rounded-lg bg-[var(--accent)] px-2.5 py-1.5 text-xs font-bold text-white shadow-sm hover:opacity-90 transition-all">
                                    <iconify-icon icon="solar:user-check-bold" width="14"></iconify-icon>
                                    {{ __("M'assigner") }}
                                </button>
                            @endif
                            <select class="rounded-lg border-slate-200 bg-white py-1.5 pl-2 pr-7 text-xs font-medium text-slate-700 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] min-w-0 max-w-full" wire:change="setAssignee($event.target.value)">
                                <option value="0">{{ __('Assigner à…') }}</option>
                                @foreach($orgUsers ?? [] as $u)
                                    <option value="{{ $u->id }}" @selected($assignee && $assignee->id === $u->id)>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('Fonction') }}</div>
                @if($canSeeInternalNotes ?? false)
                    <select class="mt-1 w-full rounded-lg border-slate-200 bg-white py-1.5 px-2 text-xs font-medium text-slate-700 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]" wire:change="setAssignedToFunction($event.target.value)">
                        <option value="">{{ __('— Aucune —') }}</option>
                        @foreach($organizationFunctions ?? [] as $fn)
                            <option value="{{ $fn->id }}" @selected($ticket->assigned_to_function_id === $fn->id)>{{ $fn->name }}</option>
                        @endforeach
                    </select>
                @else
                    <div class="mt-1">
                        @if($ticket->assignedToFunction ?? null)
                            <span class="text-sm font-semibold text-slate-900">{{ $ticket->assignedToFunction->name }}</span>
                        @else
                            <span class="text-sm text-slate-400 italic">—</span>
                        @endif
                    </div>
                @endif
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 col-span-2">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('Échéance') }}</div>
                <div class="mt-1 flex items-center gap-2">
                    <iconify-icon icon="solar:calendar-add-linear" width="16" class="text-slate-400"></iconify-icon>
                    <span class="text-sm font-semibold text-slate-900">
                        {{ $ticket->due_date ? $ticket->due_date->translatedFormat('d M Y') : '—' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Checklist -->
    @php
        $checklistItems = $ticket->checklistItems ?? collect();
        $checklistTotal = $checklistItems->count();
        $checklistDone = $checklistItems->where('is_done', true)->count();
        $checklistPct = $checklistTotal > 0 ? (int) round(100 * $checklistDone / $checklistTotal) : 0;
    @endphp
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between gap-2">
            <div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('Checklist') }}</div>
                <div class="mt-1 text-sm font-semibold text-slate-900">{{ $checklistDone }}/{{ $checklistTotal }} ({{ $checklistPct }}%)</div>
            </div>
            @if($checklistTotal > 0)
                <div class="flex-1 max-w-[120px] h-2 rounded-full bg-slate-200 overflow-hidden">
                    <div class="h-full rounded-full bg-[var(--accent)] transition-all duration-300" style="width: {{ $checklistPct }}%"></div>
                </div>
            @endif
        </div>
        @if($checklistTotal > 0)
            <ul class="mt-4 space-y-2">
                @foreach($checklistItems as $item)
                    <li class="flex items-start gap-2 rounded-xl border border-slate-200 bg-slate-50 p-3">
                        @if($canEditChecklist ?? false)
                            <button type="button" wire:click="toggleChecklistItem({{ $item->id }})" class="cursor-pointer mt-0.5 shrink-0 flex items-center justify-center h-5 w-5 rounded border-2 transition-colors {{ $item->is_done ? 'bg-[var(--accent)] border-[var(--accent)] text-white' : 'border-slate-300 bg-white text-transparent hover:border-[var(--accent)]' }}">
                                @if($item->is_done)
                                    <iconify-icon icon="solar:check-read-linear" width="12"></iconify-icon>
                                @endif
                            </button>
                        @else
                            <span class="mt-0.5 shrink-0 flex items-center justify-center h-5 w-5 rounded border-2 {{ $item->is_done ? 'bg-[var(--accent)] border-[var(--accent)] text-white' : 'border-slate-300 bg-white' }}">
                                @if($item->is_done)
                                    <iconify-icon icon="solar:check-read-linear" width="12"></iconify-icon>
                                @endif
                            </span>
                        @endif
                        <div class="min-w-0 flex-1">
                            <span class="text-sm font-medium {{ $item->is_done ? 'text-slate-500 line-through' : 'text-slate-900' }}">{{ $item->title }}</span>
                            @if($item->assignee)
                                <div class="mt-0.5 text-[11px] text-slate-500">{{ __('Responsable') }}: {{ $item->assignee->name }}</div>
                            @endif
                            @if($item->due_date)
                                <div class="mt-0.5 text-[11px] text-slate-500">{{ __('Échéance') }}: {{ $item->due_date->translatedFormat('d M Y') }}</div>
                            @endif
                            @if($item->is_done && $item->done_at)
                                <div class="mt-1 text-[10px] text-slate-400">
                                    {{ __('Fait par') }} {{ $item->doneByUser?->name ?? '—' }} · {{ $item->done_at->diffForHumans() }}
                                </div>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="mt-3 text-sm text-slate-400">{{ __('Aucune étape.') }}</p>
        @endif
        @if($canEditChecklist ?? false)
            @if($showAddChecklistItem ?? false)
                <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-3 space-y-2">
                    <input type="text" wire:model="newChecklistTitle" class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm @error('newChecklistTitle') border-red-500 @enderror" placeholder="{{ __('Intitulé') }}" />
                    @error('newChecklistTitle')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <select wire:model="newChecklistAssignedTo" class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">
                            <option value="">{{ __('— Responsable') }}</option>
                            @foreach($orgUsers ?? [] as $u)
                                <option value="{{ (int) optional($u)->id }}">{{ optional($u)->name ?? '—' }}</option>
                            @endforeach
                        </select>
                        <input type="date" wire:model="newChecklistDueDate" class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm" />
                    </div>
                    <div class="flex gap-2">
                        <button type="button" wire:click="addChecklistItemToTicket" class="cursor-pointer flex-1 inline-flex items-center justify-center gap-1.5 h-9 rounded-lg bg-[var(--accent)] text-white text-xs font-bold">
                            <iconify-icon icon="solar:add-circle-linear" width="14"></iconify-icon>
                            {{ __('Ajouter') }}
                        </button>
                        <button type="button" wire:click="closeAddChecklistForm" class="cursor-pointer h-9 px-3 rounded-lg border border-slate-200 bg-white text-slate-700 text-xs font-medium">
                            {{ __('Annuler') }}
                        </button>
                    </div>
                </div>
            @else
                <button type="button" wire:click="openAddChecklistForm" class="mt-3 w-full cursor-pointer inline-flex items-center justify-center gap-2 h-9 rounded-xl border border-dashed border-slate-300 bg-slate-50 text-slate-600 text-xs font-medium hover:bg-slate-100 transition-colors">
                    <iconify-icon icon="solar:add-circle-linear" width="16"></iconify-icon>
                    {{ __('Ajouter un élément') }}
                </button>
            @endif
        @endif
    </div>

    <!-- Participants -->
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('Participants') }}</div>
                <div class="mt-1 text-sm font-semibold text-slate-900">{{ count($discussionUsers) }} {{ count($discussionUsers) > 1 ? __('personnes') : __('personne') }}</div>
            </div>
            <button type="button" @click="addParticipantOpen = true" class="inline-flex items-center gap-2 rounded-xl bg-[var(--accent)] px-3 py-2 text-xs font-bold text-white shadow-sm shadow-[var(--accent-ring)] hover:opacity-90 transition-all">
                <iconify-icon icon="solar:user-plus-bold" width="16"></iconify-icon>
                {{ __('Ajouter') }}
            </button>
        </div>
        <div class="mt-4 space-y-2">
            @foreach($discussionUsers as $u)
                <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <div class="h-9 w-9 rounded-full flex items-center justify-center text-sm font-semibold shrink-0" style="background: var(--accent-soft); color: var(--accent);">
                        {{ strtoupper(mb_substr($u->name ?? '?', 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="text-sm font-semibold text-slate-900 truncate">{{ $u->name }}</span>
                            @if($u->id === $creator?->id)
                                <span class="text-[10px] font-bold text-slate-500 bg-white border border-slate-200 px-2 py-0.5 rounded-full shrink-0">{{ __('Créateur') }}</span>
                            @endif
                            @if($u->id === $assignee?->id)
                                <span class="text-[10px] font-bold text-slate-500 bg-white border border-slate-200 px-2 py-0.5 rounded-full shrink-0">{{ __('Assigné') }}</span>
                            @endif
                        </div>
                        <div class="text-xs text-slate-500 truncate">{{ $u->email ?? '' }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Fichiers -->
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('Fichiers') }}</div>
            <div class="text-xs font-semibold text-slate-500">{{ $allAttachments->count() }}</div>
        </div>
        <div class="mt-3 space-y-2">
            @forelse($allAttachments->take(10) as $att)
                @include('livewire.tickets.partials.attachment-link', ['att' => $att, 'variant' => 'theirs'])
            @empty
                <p class="text-sm text-slate-400">{{ __('Aucune pièce jointe.') }}</p>
            @endforelse
        </div>
    </div>

    <!-- Archive -->
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-3">{{ __('Archive') }}</div>
        @if($ticket->archived_at)
            <div class="mb-3 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
                <div class="flex items-start gap-2">
                    <iconify-icon icon="solar:archive-bold-duotone" width="18"></iconify-icon>
                    <div class="min-w-0">
                        <div class="font-semibold">{{ __('Ticket archivé') }}</div>
                        <div class="text-amber-700/80 mt-0.5">{{ __('Archivé le') }} {{ $ticket->archived_at->translatedFormat('d M Y à H:i') }}</div>
                    </div>
                </div>
            </div>
            <button type="button" wire:click="restoreTicket" class="w-full inline-flex items-center justify-center gap-2 h-10 rounded-xl border border-slate-200 bg-white text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                <iconify-icon icon="solar:restart-bold-duotone" width="18"></iconify-icon>
                {{ __('Restaurer') }}
            </button>
        @else
            <p class="text-sm text-slate-500 mb-3">{{ __('Archivez ce ticket pour le sortir des listes actives.') }}</p>
            <button type="button" wire:click="archiveTicket" class="w-full inline-flex items-center justify-center gap-2 h-10 rounded-xl bg-slate-900 text-sm font-bold text-white hover:bg-slate-800 transition-colors">
                <iconify-icon icon="solar:archive-bold-duotone" width="18"></iconify-icon>
                {{ __('Archiver') }}
            </button>
        @endif
    </div>

    @if($canDeleteTicket ?? false)
    <!-- Suppression (soft delete) -->
    <div class="rounded-2xl border border-red-100 bg-red-50/50 p-4 shadow-sm">
        <div class="text-[11px] font-bold uppercase tracking-wider text-red-600 mb-3">{{ __('Supprimer le ticket') }}</div>
        <p class="text-sm text-slate-600 mb-3">{{ __('Le ticket sera masqué des listes. La suppression peut être annulée par un administrateur.') }}</p>
        <button
            type="button"
            x-on:click="$dispatch('open-modal', 'confirm-delete-ticket')"
            class="w-full inline-flex items-center justify-center gap-2 h-10 rounded-xl bg-red-600 text-sm font-bold text-white hover:bg-red-700 transition-colors"
        >
            <iconify-icon icon="solar:trash-bin-trash-bold-duotone" width="18"></iconify-icon>
            {{ __('Supprimer') }}
        </button>
    </div>
    @endif

    <div class="mt-auto rounded-2xl border border-slate-200 bg-slate-50 p-4 text-xs text-slate-500 text-center">
        {{ __('Créé') }} {{ $ticket->created_at->translatedFormat('d M H:i') }} · {{ __('Mis à jour') }} {{ $lastActivity->diffForHumans() }}
    </div>
</div>
