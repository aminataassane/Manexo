{{-- Livewire exige une balise racine unique : wrapper même si aucun formulaire en attente --}}
<div>
    @if($this->pendingFormsCount > 0)
        <div class="rounded-xl sm:rounded-2xl bg-white p-4 sm:p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base sm:text-lg font-bold text-slate-900">{{ __('pages.dashboard.forms_to_fill') }}</h3>
                <a href="{{ route('forms.index') }}" class="text-sm font-semibold hover:underline w-fit" style="color: var(--accent);" wire:navigate>{{ __('pages.dashboard.see_all_forms') }}</a>
            </div>
            <ul class="space-y-3">
                @foreach($this->pendingFormAssignments as $fa)
                    @php
                        $faStatus = $fa->status instanceof \App\Enums\FormAssignmentStatus ? $fa->status->value : (string) $fa->status;
                        $isOverdue = $faStatus === 'overdue';
                    @endphp
                    <li>
                        <a href="{{ route('forms.fill', $fa) }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 bg-slate-50/30 hover:bg-slate-50/60 transition-colors shadow-sm group block" wire:navigate>
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $isOverdue ? 'bg-red-50 text-red-600' : 'bg-[var(--accent-soft)] text-[var(--accent)]' }}">
                                <iconify-icon icon="{{ $isOverdue ? 'solar:alarm-bold-duotone' : 'solar:clipboard-text-bold-duotone' }}" width="20"></iconify-icon>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-slate-900 truncate group-hover:text-[var(--accent)] transition-colors">{{ $fa->form?->name ?? '—' }}</p>
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 mt-0.5 text-xs text-slate-500">
                                    @if($isOverdue)
                                        <span class="font-semibold text-red-600">{{ __('pages.dashboard.past_due') }}</span>
                                    @endif
                                    @if($fa->due_date)
                                        <span class="flex items-center gap-1">
                                            <iconify-icon icon="solar:calendar-linear" width="12"></iconify-icon>
                                            {{ __('pages.dashboard.due_date') }}: {{ $fa->due_date->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium border shrink-0 {{ $isOverdue ? 'bg-red-50 text-red-700 border-red-100' : 'bg-[var(--accent-soft)] text-[var(--accent)] border-[var(--accent)]/20' }}">
                                {{ $isOverdue ? __('pages.dashboard.past_due') : __('pages.forms.status_pending') }}
                            </span>
                            <iconify-icon icon="solar:arrow-right-linear" width="18" class="text-slate-400 group-hover:text-[var(--accent)] transition-colors shrink-0"></iconify-icon>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
