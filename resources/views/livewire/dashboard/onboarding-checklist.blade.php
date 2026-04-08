@php
    $stepMeta = [
        'categories' => [
            'icon' => 'solar:tag-horizontal-bold-duotone',
            'title' => __('onboarding.steps.categories.title'),
            'desc' => __('onboarding.steps.categories.desc'),
            'btn' => __('onboarding.steps.categories.btn'),
        ],
        'priorities' => [
            'icon' => 'solar:flag-bold-duotone',
            'title' => __('onboarding.steps.priorities.title'),
            'desc' => __('onboarding.steps.priorities.desc'),
            'btn' => __('onboarding.steps.priorities.btn'),
        ],
        'functions' => [
            'icon' => 'solar:users-group-two-rounded-bold-duotone',
            'title' => __('onboarding.steps.functions.title'),
            'desc' => __('onboarding.steps.functions.desc'),
            'btn' => __('onboarding.steps.functions.btn'),
        ],
        'invite_team' => [
            'icon' => 'solar:user-plus-bold-duotone',
            'title' => __('onboarding.steps.invite_team.title'),
            'desc' => __('onboarding.steps.invite_team.desc'),
            'btn' => __('onboarding.steps.invite_team.btn'),
        ],
        'customize' => [
            'icon' => 'solar:palette-bold-duotone',
            'title' => __('onboarding.steps.customize.title'),
            'desc' => __('onboarding.steps.customize.desc'),
            'btn' => __('onboarding.steps.customize.btn'),
        ],
        'create_form' => [
            'icon' => 'solar:clipboard-text-bold-duotone',
            'title' => __('onboarding.steps.create_form.title'),
            'desc' => __('onboarding.steps.create_form.desc'),
            'btn' => __('onboarding.steps.create_form.btn'),
        ],
        'first_test' => [
            'icon' => 'solar:rocket-bold-duotone',
            'title' => __('onboarding.steps.first_test.title'),
            'desc' => __('onboarding.steps.first_test.desc'),
            'btn' => __('onboarding.steps.first_test.btn'),
        ],
    ];

    $steps = $this->steps;
    $stepKeys = array_keys($steps);

    // Find first incomplete step
    $firstIncomplete = null;
    foreach ($stepKeys as $k) {
        if (! $steps[$k]['completed']) {
            $firstIncomplete = $k;
            break;
        }
    }

    // Split into phases
    $phase1Keys = ['categories', 'priorities', 'functions', 'invite_team'];
    $phase2Keys = ['customize', 'create_form', 'first_test'];
@endphp

<div @class(['hidden' => ! $this->shouldShow]) wire:key="onboarding-checklist-root">
@if ($this->shouldShow)
<div x-data="{ expanded: true }">

    {{-- ── Header ── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
        <div class="min-w-0">
            <h2 class="text-base font-bold text-slate-900 tracking-tight">{{ __('onboarding.title') }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ __('onboarding.subtitle') }}</p>
        </div>

        <div class="flex items-center gap-4 shrink-0">
            {{-- Segment progress --}}
            <div class="flex items-center gap-2.5">
                <div class="flex gap-1">
                    @foreach ($steps as $step)
                        <div @class([
                            'h-1.5 rounded-full transition-all duration-500',
                            'w-6 bg-[color:var(--accent)]' => $step['completed'],
                            'w-4 bg-slate-200' => ! $step['completed'],
                        ])></div>
                    @endforeach
                </div>
                <span class="text-xs font-semibold text-slate-400">{{ $this->completedCount }}/{{ $this->totalSteps }}</span>
            </div>

            <div class="flex items-center gap-1">
                <button type="button" @click="expanded = !expanded" class="h-7 w-7 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition flex items-center justify-center">
                    <iconify-icon :icon="expanded ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear'" width="14"></iconify-icon>
                </button>
                <button type="button" wire:click="dismiss" wire:loading.attr="disabled" class="h-7 w-7 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition flex items-center justify-center" title="{{ __('onboarding.dismiss') }}">
                    <iconify-icon icon="solar:close-circle-linear" width="14"></iconify-icon>
                </button>
            </div>
        </div>
    </div>

    {{-- ── Steps ── --}}
    <div x-show="expanded" x-collapse>
        <div class="space-y-5">

            {{-- Phase 1 : Fondations --}}
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('onboarding.phase1') }}</span>
                    <div class="flex-1 h-px bg-slate-100"></div>
                </div>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($phase1Keys as $key)
                        @if (isset($steps[$key], $stepMeta[$key]))
                            @include('livewire.dashboard.partials.onboarding-step-card', [
                                'key' => $key,
                                'step' => $steps[$key],
                                'meta' => $stepMeta[$key],
                                'isCurrent' => $key === $firstIncomplete,
                                'isDone' => $steps[$key]['completed'],
                                'number' => array_search($key, $stepKeys) + 1,
                            ])
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Phase 2 : Mise en route --}}
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('onboarding.phase2') }}</span>
                    <div class="flex-1 h-px bg-slate-100"></div>
                </div>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($phase2Keys as $key)
                        @if (isset($steps[$key], $stepMeta[$key]))
                            @include('livewire.dashboard.partials.onboarding-step-card', [
                                'key' => $key,
                                'step' => $steps[$key],
                                'meta' => $stepMeta[$key],
                                'isCurrent' => $key === $firstIncomplete,
                                'isDone' => $steps[$key]['completed'],
                                'number' => array_search($key, $stepKeys) + 1,
                            ])
                        @endif
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>
@endif
</div>
