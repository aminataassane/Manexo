<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">{{ __('Mes formulaires') }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ __('Formulaires qui vous ont été assignés.') }}</p>
        </div>
    </div>

    @if(session('form_success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
            {{ session('form_success') }}
        </div>
    @endif

    <!-- Tabs -->
    <div class="flex gap-1 bg-slate-100 p-1 rounded-lg w-fit">
        <button type="button" wire:click="$set('tab', 'pending')"
                class="px-4 py-2 text-xs font-semibold rounded-md transition-colors {{ $tab === 'pending' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-800' }}">
            {{ __('A remplir') }}
        </button>
        <button type="button" wire:click="$set('tab', 'submitted')"
                class="px-4 py-2 text-xs font-semibold rounded-md transition-colors {{ $tab === 'submitted' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-800' }}">
            {{ __('Soumis') }}
        </button>
        <button type="button" wire:click="$set('tab', 'all')"
                class="px-4 py-2 text-xs font-semibold rounded-md transition-colors {{ $tab === 'all' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-800' }}">
            {{ __('Tous') }}
        </button>
    </div>

    <!-- Assignments list -->
    <div class="space-y-3">
        @forelse($this->assignments as $a)
            @php
                $aStatus = $a->status instanceof \App\Enums\FormAssignmentStatus ? $a->status->value : (string) $a->status;
                $aBadge = match($aStatus) {
                    'submitted' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-100', 'label' => 'Soumis'],
                    'overdue' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-100', 'label' => 'En retard'],
                    default => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-100', 'label' => 'En attente'],
                };
            @endphp
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <h3 class="text-sm font-bold text-slate-900 truncate">{{ $a->form?->name ?? '—' }}</h3>
                        <div class="flex items-center gap-3 mt-1 text-xs text-slate-500">
                            <span>{{ __('Par') }} {{ $a->assignedBy?->name ?? '—' }}</span>
                            @if($a->due_date)
                                <span class="flex items-center gap-1">
                                    <iconify-icon icon="solar:calendar-linear" width="12"></iconify-icon>
                                    {{ __('Échéance') }}: {{ $a->due_date->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-medium border {{ $aBadge['bg'] }} {{ $aBadge['text'] }} {{ $aBadge['border'] }}">{{ $aBadge['label'] }}</span>
                        @if($aStatus !== 'submitted')
                            <a href="{{ route('forms.fill', $a->id) }}"
                               class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white rounded-lg bg-[var(--accent)] hover:opacity-90 transition-all shadow-sm">
                                <iconify-icon icon="solar:pen-bold" width="14"></iconify-icon>
                                {{ __('Remplir') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-slate-200 p-8 text-center shadow-sm">
                <iconify-icon icon="solar:clipboard-check-linear" width="40" class="text-slate-300 mb-3"></iconify-icon>
                <p class="text-sm font-medium text-slate-900">{{ __('Aucun formulaire') }}</p>
                <p class="text-xs text-slate-500 mt-1">
                    @if($tab === 'pending')
                        {{ __('Vous n\'avez aucun formulaire en attente.') }}
                    @elseif($tab === 'submitted')
                        {{ __('Vous n\'avez soumis aucun formulaire.') }}
                    @else
                        {{ __('Aucun formulaire ne vous a été assigné.') }}
                    @endif
                </p>
            </div>
        @endforelse
    </div>
</div>
