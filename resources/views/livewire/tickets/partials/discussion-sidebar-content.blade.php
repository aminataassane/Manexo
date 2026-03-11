{{-- Panneau "Infos" (design dédié, pas un dump de détails) --}}
@php
    use App\Enums\TicketStatus;
    $roleLabels = [
        'owner' => __('Admin'),
        'admin' => __('Admin'),
        'agent' => __('Agent'),
        'member' => __('Membre'),
    ];
    /** @var \App\Models\Ticket $ticket */
    /** @var \Illuminate\Support\Collection<int, \App\Models\TicketPriority>|array $orgPriorities */
    /** @var \Illuminate\Support\Collection<int, \App\Models\User>|array $orgUsers */
    /** @var \Illuminate\Support\Collection<int, \App\Models\OrganizationFunction>|array $organizationFunctions */
    /** @var \Illuminate\Support\Collection<int, \App\Models\TicketGroup>|array $ticketGroups */
@endphp
<div class="flex flex-col h-full gap-4 min-w-0">
    {{-- Lock banner --}}
    @if($isLocked ?? false)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-3 sm:p-4 shadow-sm min-w-0">
            <div class="flex items-start gap-2 text-sm text-amber-800">
                <iconify-icon icon="solar:lock-keyhole-bold-duotone" width="20" class="text-amber-600 shrink-0 mt-0.5"></iconify-icon>
                <div>
                    <div class="font-bold">{{ __('tickets.locked_banner') }}</div>
                    @if($canBypassLock ?? false)
                        <div class="text-xs text-amber-700/80 mt-0.5">{{ __('Vous avez les droits administrateur pour modifier ce ticket.') }}</div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Aperçu (aligné détail ticket) -->
    <div class="rounded-2xl border border-slate-200 bg-white p-3 sm:p-4 shadow-sm min-w-0">
        <div class="flex flex-col gap-3 min-w-0">
            <div class="flex items-start gap-2 min-w-0">
                <span class="shrink-0 inline-flex items-center justify-center rounded-lg px-2.5 py-1.5 min-w-[6rem] text-[11px] font-semibold font-mono tracking-tight bg-[var(--accent-soft)] text-[var(--accent)] border border-[var(--accent-soft)]">
                    {{ $ticket->shortReference() }}
                </span>
                <span class="text-sm font-bold text-slate-900 line-clamp-2 min-w-0 leading-snug">{{ $ticket->subject }}</span>
            </div>
            <a href="{{ route('tickets.discussion', $ticket) }}" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors w-full sm:w-auto">
                <iconify-icon icon="solar:arrow-right-linear" width="14"></iconify-icon>
                {{ __('Ouvrir') }}
            </a>
        </div>

        <div class="mt-3 flex flex-wrap gap-2">
            {{-- Statut : dropdown pour staff, badge pour les autres --}}
            @if($canAssignTicket ?? false)
                <select wire:change="changeStatus($event.target.value)" class="rounded-full border-slate-200 bg-[var(--accent-soft)] text-[var(--accent)] py-1 pl-2.5 pr-7 text-xs font-bold shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] cursor-pointer max-w-full min-w-0 disabled:opacity-50 disabled:cursor-not-allowed" @disabled(($isLocked ?? false) && !($canBypassLock ?? false))>
                    @foreach(TicketStatus::cases() as $s)
                        <option value="{{ $s->value }}" @selected($ticket->status === $s)>{{ __('tickets.status.' . $s->value) }}</option>
                    @endforeach
                </select>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold bg-[var(--accent-soft)] text-[var(--accent)]">
                    <iconify-icon icon="solar:bolt-circle-bold-duotone" width="14"></iconify-icon>
                    {{ __('tickets.status.' . $ticket->status->value) }}
                </span>
            @endif

            {{-- Priorité : dropdown pour staff, badge pour les autres --}}
            @if($canAssignTicket ?? false)
                <select x-on:change="$wire.changePriority(Number($event.target.value))" class="rounded-full border-slate-200 bg-slate-100 text-slate-700 py-1 pl-2.5 pr-7 text-xs font-bold shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] cursor-pointer max-w-full min-w-0 disabled:opacity-50 disabled:cursor-not-allowed" @disabled(($isLocked ?? false) && !($canBypassLock ?? false))>
                    @foreach($orgPriorities ?? [] as $p)
                        <option value="{{ $p->id }}" @selected($ticket->ticket_priority_id === $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold bg-slate-100 text-slate-700">
                    <span class="h-1.5 w-1.5 rounded-full {{ $priorityDot }}"></span>
                    {{ optional($ticket->priority)->name ?? '—' }}
                </span>
            @endif

            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold bg-slate-100 text-slate-700">
                <iconify-icon icon="solar:tag-bold-duotone" width="14"></iconify-icon>
                {{ optional($ticket->category)->name ?? '—' }}
            </span>

            {{-- Groupe : dropdown pour staff, badge pour les autres --}}
            @if(($ticketGroups ?? collect())->isNotEmpty())
                @if($canAssignTicket ?? false)
                    <select wire:change="changeGroup($event.target.value)" class="rounded-full border-slate-200 py-1 pl-2.5 pr-7 text-xs font-bold shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] cursor-pointer max-w-full min-w-0 disabled:opacity-50 disabled:cursor-not-allowed" style="background-color: {{ optional($ticket->group)->color ? optional($ticket->group)->color . '15' : '#f1f5f9' }}; color: {{ optional($ticket->group)->color ?? '#334155' }};" @disabled(($isLocked ?? false) && !($canBypassLock ?? false))>
                        <option value="" @selected(!$ticket->ticket_group_id)>{{ __('— Aucun groupe') }}</option>
                        @foreach($ticketGroups as $tg)
                            <option value="{{ $tg->id }}" @selected($ticket->ticket_group_id === $tg->id)>{{ $tg->name }}</option>
                        @endforeach
                    </select>
                @elseif($ticket->group)
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold" style="background-color: {{ $ticket->group->color ?? 'var(--accent)' }}15; color: {{ $ticket->group->color ?? 'var(--accent)' }};">
                        <iconify-icon icon="solar:widget-5-bold-duotone" width="14"></iconify-icon>
                        {{ $ticket->group->name }}
                    </span>
                @endif
            @endif
        </div>

        {{-- Assignés, Fonction, Échéance : 1 colonne pour lisibilité (panneau étroit), 2 colonnes sur viewport large --}}
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            {{-- Assignés (multi-assignee avec distinction responsable/collaborateur) --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 min-w-0">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('Assignés') }}</div>
                <div class="mt-1 flex flex-col gap-2 min-w-0">
                    @if($ticket->assignees->isNotEmpty())
                        @php
                            $responsible = $ticket->assignees->first(fn ($u) => ($u->pivot->role ?? '') === 'responsible');
                            $collaborators = $ticket->assignees->filter(fn ($u) => ($u->pivot->role ?? '') !== 'responsible');
                        @endphp
                        {{-- Responsable --}}
                        @if($responsible)
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="h-6 w-6 rounded-full flex items-center justify-center text-[10px] font-semibold shrink-0 ring-2 ring-[var(--accent)]" style="background: var(--accent-soft); color: var(--accent);">
                                    {{ strtoupper(mb_substr($responsible->name ?? '?', 0, 1)) }}
                                </div>
                                <span class="text-sm font-semibold text-slate-900 truncate flex-1 min-w-0">{{ $responsible->name }}</span>
                                <span class="text-[10px] font-bold text-white bg-[var(--accent)] px-1.5 py-0.5 rounded-full shrink-0">{{ __('Responsable') }}</span>
                                @if(($canAssignTicket ?? false) && !($isLocked ?? false))
                                    <button type="button" @click="$dispatch('confirm-action', { title: '{{ __('Retirer') }}', message: '{{ __('Retirer cet assigné du ticket ?') }}', confirmLabel: '{{ __('Retirer') }}', variant: 'danger', onConfirm: () => $wire.removeAssignee({{ $responsible->id }}) })" class="shrink-0 text-slate-400 hover:text-red-500 transition-colors" title="{{ __('Retirer') }}">
                                        <iconify-icon icon="solar:close-circle-linear" width="16"></iconify-icon>
                                    </button>
                                @endif
                            </div>
                        @endif
                        {{-- Collaborateurs --}}
                        @foreach($collaborators as $asg)
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="h-6 w-6 rounded-full flex items-center justify-center text-[10px] font-semibold shrink-0 ring-1 ring-slate-200" style="background: var(--accent-soft); color: var(--accent);">
                                    {{ strtoupper(mb_substr($asg->name ?? '?', 0, 1)) }}
                                </div>
                                <span class="text-sm font-semibold text-slate-900 truncate flex-1 min-w-0">{{ $asg->name }}</span>
                                @if(($canAssignTicket ?? false) && !($isLocked ?? false))
                                    <button type="button" wire:click="promoteToResponsible({{ $asg->id }})" class="shrink-0 text-[10px] font-bold text-[var(--accent)] hover:underline" title="{{ __('Promouvoir en responsable') }}">
                                        <iconify-icon icon="solar:star-bold" width="14"></iconify-icon>
                                    </button>
                                    <button type="button" @click="$dispatch('confirm-action', { title: '{{ __('Retirer') }}', message: '{{ __('Retirer cet assigné du ticket ?') }}', confirmLabel: '{{ __('Retirer') }}', variant: 'danger', onConfirm: () => $wire.removeAssignee({{ $asg->id }}) })" class="shrink-0 text-slate-400 hover:text-red-500 transition-colors" title="{{ __('Retirer') }}">
                                        <iconify-icon icon="solar:close-circle-linear" width="16"></iconify-icon>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <span class="text-sm text-slate-400 italic">—</span>
                    @endif
                    @if(($canAssignTicket ?? false) && !($isLocked ?? false))
                        <div class="flex flex-wrap gap-1.5">
                            @if($ticket->assignees->isEmpty() || (auth()->id() && !$ticket->assignees->contains('id', auth()->id())))
                                <button type="button" wire:click="assignToMe" class="inline-flex items-center gap-1 rounded-lg bg-[var(--accent)] px-2.5 py-1.5 text-xs font-bold text-white shadow-sm hover:opacity-90 transition-all shrink-0">
                                    <iconify-icon icon="solar:user-check-bold" width="14"></iconify-icon>
                                    {{ __("M'assigner") }}
                                </button>
                            @endif
                            <select class="rounded-lg border-slate-200 bg-white py-1.5 pl-2 pr-7 text-xs font-medium text-slate-700 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] min-w-0 w-full max-w-full" x-on:change="if ($event.target.value > 0) $wire.addAssignee(Number($event.target.value)); $event.target.selectedIndex = 0">
                                <option value="0">{{ __('Assigner à…') }}</option>
                                @foreach($orgUsers ?? [] as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 min-w-0">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('Fonction') }}</div>
                @if($canSeeInternalNotes ?? false)
                    <select class="mt-1 w-full min-w-0 rounded-lg border-slate-200 bg-white py-1.5 px-2 text-xs font-medium text-slate-700 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50 disabled:cursor-not-allowed" wire:change="setAssignedToFunction($event.target.value)" @disabled(($isLocked ?? false) && !($canBypassLock ?? false))>
                        <option value="">{{ __('— Aucune —') }}</option>
                        @foreach($organizationFunctions ?? [] as $fn)
                            <option value="{{ $fn->id }}" @selected($ticket->assigned_to_function_id === $fn->id)>{{ $fn->name }}</option>
                        @endforeach
                    </select>
                @else
                    <div class="mt-1 min-w-0">
                        @if($ticket->assignedToFunction ?? null)
                            <span class="text-sm font-semibold text-slate-900 truncate block">{{ $ticket->assignedToFunction->name }}</span>
                        @else
                            <span class="text-sm text-slate-400 italic">—</span>
                        @endif
                    </div>
                @endif
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 min-w-0 sm:col-span-2">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('Échéance') }}</div>
                <div class="mt-1 flex items-center gap-2 min-w-0">
                    <iconify-icon icon="solar:calendar-add-linear" width="16" class="text-slate-400 shrink-0"></iconify-icon>
                    @php
                        /** @var \Carbon\Carbon|null $ticketDueDate */
                        $ticketDueDate = $ticket->due_date;
                    @endphp
                    @if($canEditDueDate ?? false)
                        <input
                            type="date"
                            value="{{ $ticketDueDate?->format('Y-m-d') ?? '' }}"
                            wire:change="updateDueDate($event.target.value)"
                            class="min-w-0 flex-1 rounded-lg border-slate-200 bg-white py-1 px-2 text-sm font-semibold text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] max-w-full disabled:opacity-50 disabled:cursor-not-allowed"
                            @disabled(($isLocked ?? false) && !($canBypassLock ?? false))
                        />
                    @else
                        <span class="text-sm font-semibold text-slate-900 truncate">
                            {{ $ticketDueDate ? $ticketDueDate->translatedFormat('d M Y') : '—' }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Champs personnalisés (formulaire) --}}
    @if($formResponse && !empty($formResponse->field_snapshot) && !empty($formResponse->responses))
        <div class="rounded-2xl border border-slate-200 bg-white p-3 sm:p-4 shadow-sm min-w-0">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-3">{{ __('Champs personnalisés') }}</div>
            <div class="space-y-3">
                @foreach($formResponse->field_snapshot as $field)
                    @php
                        $fieldKey = $field['key'] ?? $field['id'] ?? null;
                        $fieldValue = $fieldKey ? ($formResponse->responses[$fieldKey] ?? null) : null;
                    @endphp
                    @if($fieldValue !== null && $fieldValue !== '' && $fieldValue !== [])
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ $field['label'] ?? $fieldKey }}</div>
                            <div class="mt-1 text-sm text-slate-900 break-words min-w-0">
                                @if(is_array($fieldValue))
                                    {{ implode(', ', $fieldValue) }}
                                @elseif(($field['type'] ?? '') === 'boolean')
                                    {{ $fieldValue ? __('Oui') : __('Non') }}
                                @else
                                    {{ $fieldValue }}
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Checklist -->
    @php
        $checklistItems = $ticket->checklistItems ?? collect();
        $checklistTotal = $checklistItems->count();
        $checklistDone = $checklistItems->where('is_done', true)->count();
        $checklistPct = $checklistTotal > 0 ? (int) round(100 * $checklistDone / $checklistTotal) : 0;
    @endphp
    <div class="rounded-2xl border border-slate-200 bg-white p-3 sm:p-4 shadow-sm min-w-0">
        <div class="flex items-center justify-between gap-2 min-w-0">
            <div class="min-w-0">
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
                    @php
                        $canToggleThis = (($isStaffOrTicketOwner ?? false) || ($item->assigned_to && (int)$item->assigned_to === ($authUserId ?? 0)) || ($item->relationLoaded('assignees') && $item->assignees->contains('id', $authUserId ?? 0))) && !(($isLocked ?? false) && !($canBypassLock ?? false));
                    @endphp
                    <li class="flex items-start gap-2 rounded-xl border border-slate-200 bg-slate-50 p-3" x-data="{ editing: false, editTitle: '{{ str_replace("'", "\\'", $item->title) }}' }">
                        @if($canToggleThis)
                            <button type="button" wire:click="toggleChecklistItem({{ $item->id }})" class="cursor-pointer mt-0.5 shrink-0 flex items-center justify-center h-5 w-5 rounded border-2 transition-colors {{ $item->is_done ? 'bg-[var(--accent)] border-[var(--accent)] text-white' : 'border-slate-300 bg-white text-transparent hover:border-[var(--accent)]' }}">
                                @if($item->is_done)
                                    <iconify-icon icon="solar:check-read-linear" width="12"></iconify-icon>
                                @endif
                            </button>
                        @else
                            <span class="mt-0.5 shrink-0 flex items-center justify-center h-5 w-5 rounded border-2 {{ $item->is_done ? 'bg-[var(--accent)] border-[var(--accent)] text-white' : 'border-slate-300 bg-white' }}" title="{{ __('checklist_items.cannot_toggle') }}">
                                @if($item->is_done)
                                    <iconify-icon icon="solar:check-read-linear" width="12"></iconify-icon>
                                @endif
                            </span>
                        @endif
                        <div class="min-w-0 flex-1">
                            {{-- Inline edit title --}}
                            <template x-if="!editing">
                                <span
                                    class="text-sm font-medium {{ $item->is_done ? 'text-slate-500 line-through' : 'text-slate-900' }} {{ ($canEditChecklist ?? false) ? 'cursor-pointer hover:text-[var(--accent)]' : '' }}"
                                    @if($canEditChecklist ?? false) @click="editing = true; $nextTick(() => $refs['editInput{{ $item->id }}']?.focus())" @endif
                                >{{ $item->title }}</span>
                            </template>
                            @if($canEditChecklist ?? false)
                                <template x-if="editing">
                                    <input
                                        type="text"
                                        x-ref="editInput{{ $item->id }}"
                                        x-model="editTitle"
                                        @blur="editing = false; if (editTitle.trim() && editTitle !== '{{ str_replace("'", "\\'", $item->title) }}') $wire.updateChecklistItemTitle({{ $item->id }}, editTitle)"
                                        @keydown.enter.prevent="$event.target.blur()"
                                        @keydown.escape.prevent="editing = false; editTitle = '{{ str_replace("'", "\\'", $item->title) }}'"
                                        class="w-full rounded-lg border border-slate-200 bg-white px-2 py-1 text-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                    />
                                </template>
                            @endif
                            @if($item->relationLoaded('assignees') && $item->assignees->isNotEmpty())
                                <div class="mt-0.5 flex flex-wrap items-center gap-1">
                                    @foreach($item->assignees as $itemAsg)
                                        <span class="inline-flex items-center gap-1 text-[11px] {{ ($itemAsg->pivot->role ?? '') === 'responsible' ? 'text-[var(--accent)] font-bold' : 'text-slate-500' }}">
                                            <span class="h-4 w-4 rounded-full flex items-center justify-center text-[8px] font-semibold shrink-0 {{ ($itemAsg->pivot->role ?? '') === 'responsible' ? 'ring-1 ring-[var(--accent)]' : 'ring-1 ring-slate-200' }}" style="background: var(--accent-soft); color: var(--accent);">{{ strtoupper(mb_substr($itemAsg->name ?? '?', 0, 1)) }}</span>
                                            {{ $itemAsg->name }}
                                            @if(($canEditChecklist ?? false) && !($isLocked ?? false))
                                                <button type="button" @click="$dispatch('confirm-action', { title: '{{ __('Retirer') }}', message: '{{ __('Retirer cet assigné de la tâche ?') }}', confirmLabel: '{{ __('Retirer') }}', variant: 'danger', onConfirm: () => $wire.removeChecklistItemAssignee({{ $item->id }}, {{ $itemAsg->id }}) })" class="text-slate-400 hover:text-red-500" title="{{ __('Retirer') }}">
                                                    <iconify-icon icon="solar:close-circle-linear" width="12"></iconify-icon>
                                                </button>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            @elseif($item->assignee)
                                <div class="mt-0.5 text-[11px] text-slate-500">{{ __('Responsable') }}: {{ $item->assignee->name }}</div>
                            @endif
                            @if(($canEditChecklist ?? false) && !($isLocked ?? false))
                                <div class="mt-1" x-data="{ open: false }">
                                    <button type="button" @click="open = !open" class="text-[10px] font-medium text-[var(--accent)] hover:underline">
                                        + {{ __('Assigner') }}
                                    </button>
                                    <div x-show="open" x-cloak class="mt-1">
                                        <select class="w-full rounded-lg border-slate-200 bg-white py-1 px-2 text-[11px] text-slate-700 focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                                x-on:change="if ($event.target.value > 0) { $wire.addChecklistItemAssignee({{ $item->id }}, Number($event.target.value)); $event.target.selectedIndex = 0; open = false; }">
                                            <option value="0">{{ __('Assigner à…') }}</option>
                                            @foreach($orgUsers ?? [] as $u)
                                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                            @if($item->assigned_to_function_id && !$item->assigned_to)
                                <div class="mt-0.5 flex items-center gap-2">
                                    <span class="text-[11px] text-amber-600 font-medium">
                                        {{ $item->assignedToFunction?->name ?? '—' }} — {{ __('checklist_items.to_claim') }}
                                    </span>
                                    @if(in_array((int)$item->assigned_to_function_id, $userFunctionIds ?? []))
                                        <button type="button" wire:click="claimChecklistItem({{ $item->id }})" class="text-[11px] font-bold text-[var(--accent)] hover:underline">
                                            {{ __('checklist_items.claim') }}
                                        </button>
                                    @endif
                                </div>
                            @endif
                            @if($item->due_date)
                                <div class="mt-0.5 text-[11px] text-slate-500">{{ __('Échéance') }}: {{ $item->due_date->translatedFormat('d M Y') }}</div>
                            @endif
                            @if($item->is_done && $item->done_at)
                                <div class="mt-1 text-[10px] text-slate-400">
                                    {{ __('checklist_items.done_by_at', ['name' => $item->doneByUser?->name ?? '—', 'time' => $item->done_at->format('H:i')]) }}
                                </div>
                            @endif
                        </div>
                        @if(($canEditChecklist ?? false) && !(($isLocked ?? false) && !($canBypassLock ?? false)))
                            <button type="button" @click="$dispatch('confirm-action', { title: '{{ __('Supprimer') }}', message: '{{ __('Supprimer cet élément ?') }}', confirmLabel: '{{ __('Supprimer') }}', variant: 'danger', onConfirm: () => $wire.deleteChecklistItem({{ $item->id }}) })" class="shrink-0 mt-0.5 text-slate-400 hover:text-red-500 transition-colors" title="{{ __('Supprimer') }}">
                                <iconify-icon icon="solar:trash-bin-trash-linear" width="14"></iconify-icon>
                            </button>
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <p class="mt-3 text-sm text-slate-400">{{ __('Aucune étape.') }}</p>
        @endif
        @if(($canEditChecklist ?? false) && !(($isLocked ?? false) && !($canBypassLock ?? false)))
            @if($showAddChecklistItem ?? false)
                <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-3 space-y-2" x-data="{ assignMode: 'user' }">
                    <input type="text" wire:model="newChecklistTitle" class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm @error('newChecklistTitle') border-red-500 @enderror" placeholder="{{ __('Intitulé') }}" />
                    @error('newChecklistTitle')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    {{-- Toggle: Responsable / Fonction --}}
                    <div class="flex items-center gap-2 text-xs">
                        <button type="button" @click="assignMode = 'user'; $wire.set('newChecklistAssignedToFunction', null)" :class="assignMode === 'user' ? 'bg-[var(--accent)] text-white' : 'bg-white text-slate-700 border border-slate-200'" class="rounded-lg px-2.5 py-1.5 font-medium transition-colors">
                            {{ __('Responsable') }}
                        </button>
                        <button type="button" @click="assignMode = 'function'; $wire.set('newChecklistAssignedTo', null)" :class="assignMode === 'function' ? 'bg-[var(--accent)] text-white' : 'bg-white text-slate-700 border border-slate-200'" class="rounded-lg px-2.5 py-1.5 font-medium transition-colors">
                            {{ __('checklist_items.assigned_to_function') }}
                        </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <template x-if="assignMode === 'user'">
                            <select wire:model="newChecklistAssignedTo" class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">
                                <option value="">{{ __('— Responsable') }}</option>
                                @foreach($orgUsers ?? [] as $u)
                                    <option value="{{ (int) optional($u)->id }}">{{ optional($u)->name ?? '—' }}</option>
                                @endforeach
                            </select>
                        </template>
                        <template x-if="assignMode === 'function'">
                            <select wire:model="newChecklistAssignedToFunction" class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">
                                <option value="">{{ __('— Fonction') }}</option>
                                @foreach($organizationFunctions ?? [] as $fn)
                                    <option value="{{ $fn->id }}">{{ $fn->name }}</option>
                                @endforeach
                            </select>
                        </template>
                        <input type="date" wire:model="newChecklistDueDate" class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm" />
                    </div>
                    <div class="flex gap-2">
                        <button type="button" wire:click="addChecklistItemToTicket" wire:loading.attr="disabled" class="cursor-pointer flex-1 inline-flex items-center justify-center gap-1.5 h-9 rounded-lg bg-[var(--accent)] text-white text-xs font-bold disabled:opacity-70 disabled:cursor-not-allowed">
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
    <div class="rounded-2xl border border-slate-200 bg-white p-3 sm:p-4 shadow-sm min-w-0">
        <div class="flex items-center justify-between gap-2 min-w-0 flex-wrap">
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
                        <div class="flex items-center gap-2 min-w-0 flex-wrap">
                            <span class="text-sm font-semibold text-slate-900 truncate">{{ $u->name }}</span>
                            @if($u->id === $creator?->id)
                                <span class="text-[10px] font-bold text-slate-500 bg-white border border-slate-200 px-2 py-0.5 rounded-full shrink-0">{{ __('Créateur') }}</span>
                            @endif
                            @if($ticket->assignees->contains('id', $u->id))
                                <span class="text-[10px] font-bold text-slate-500 bg-white border border-slate-200 px-2 py-0.5 rounded-full shrink-0">{{ __('Assigné') }}</span>
                            @endif
                            @php $memberRole = $orgMemberRoles[$u->id] ?? null; @endphp
                            @if($memberRole && $memberRole !== 'member')
                                <span class="text-[10px] font-bold text-[var(--accent)] bg-[var(--accent-soft)] px-2 py-0.5 rounded-full shrink-0">{{ $roleLabels[$memberRole] ?? $memberRole }}</span>
                            @endif
                        </div>
                        <div class="text-xs text-slate-500 truncate">{{ $u->email ?? '' }}</div>
                    </div>
                    {{-- Remove button: staff can remove anyone except creator; participants can remove themselves --}}
                    @if($u->id !== $creator?->id && !$ticket->assignees->contains('id', $u->id))
                        @if(($canAssignTicket ?? false) || (auth()->id() && (int)$u->id === (int)auth()->id()))
                            <button type="button" @click="$dispatch('confirm-action', { title: '{{ __('Retirer') }}', message: '{{ __('Retirer ce participant de la discussion ?') }}', confirmLabel: '{{ __('Retirer') }}', variant: 'danger', onConfirm: () => $wire.removeParticipant({{ $u->id }}) })" class="shrink-0 text-slate-400 hover:text-red-500 transition-colors" title="{{ __('Retirer') }}">
                                <iconify-icon icon="solar:close-circle-linear" width="18"></iconify-icon>
                            </button>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Fichiers -->
    <div class="rounded-2xl border border-slate-200 bg-white p-3 sm:p-4 shadow-sm min-w-0">
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
    @if($canArchive ?? false)
    <div class="rounded-2xl border border-slate-200 bg-white p-3 sm:p-4 shadow-sm min-w-0">
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
            <button type="button" @click="$dispatch('confirm-action', { title: '{{ __('Archiver') }}', message: '{{ __('Archiver ce ticket ? Il sera retiré des listes actives.') }}', confirmLabel: '{{ __('Archiver') }}', variant: 'warning', onConfirm: () => $wire.archiveTicket() })" class="w-full inline-flex items-center justify-center gap-2 h-10 rounded-xl bg-slate-900 text-sm font-bold text-white hover:bg-slate-800 transition-colors">
                <iconify-icon icon="solar:archive-bold-duotone" width="18"></iconify-icon>
                {{ __('Archiver') }}
            </button>
        @endif
    </div>
    @endif

    @if($canDeleteTicket ?? false)
    <!-- Suppression (soft delete) -->
    <div class="rounded-2xl border border-red-100 bg-red-50/50 p-3 sm:p-4 shadow-sm min-w-0">
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

    <div class="mt-auto rounded-2xl border border-slate-200 bg-slate-50 p-3 sm:p-4 text-xs text-slate-500 text-center min-w-0">
        {{ __('Créé') }} {{ $ticket->created_at->translatedFormat('d M H:i') }} · {{ __('Mis à jour') }} {{ $lastActivity->diffForHumans() }}
    </div>
</div>
