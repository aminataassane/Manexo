<div
    @if(isset($assignment) && $assignment) wire:init="loadFormFields" @endif
    class="flex flex-col w-full max-w-3xl mx-auto min-w-0 px-0 sm:px-2 pb-6 sm:pb-8"
    style="padding-bottom: max(1.5rem, env(safe-area-inset-bottom, 0));"
>
@if(! $formReady)
    {{-- Coquille immédiate : pas de requête lourde avant wire:init --}}
    <div class="animate-pulse space-y-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-slate-200"></div>
            <div class="flex-1 space-y-2">
                <div class="h-6 bg-slate-200 rounded-lg w-2/3"></div>
                <div class="h-4 bg-slate-100 rounded w-1/2"></div>
            </div>
        </div>
        <div class="rounded-xl border border-slate-100 bg-white p-6 space-y-4 shadow-sm">
            <div class="h-4 bg-slate-100 rounded w-1/4"></div>
            <div class="h-10 bg-slate-100 rounded w-full"></div>
            <div class="h-4 bg-slate-100 rounded w-1/3"></div>
            <div class="h-24 bg-slate-50 rounded-lg w-full"></div>
            <div class="h-10 bg-slate-100 rounded w-full"></div>
        </div>
    </div>
@else
@php
    $form = isset($assignment) && $assignment ? $assignment->form : ($form ?? null);
    abort_if(! $form, 404);
    $fields = $form->fields ?? collect();

    // Grouper les champs en étapes : chaque section = une étape, les champs avant la 1ère section = étape 1
    $steps = [];
    $currentTitle = $form->name;
    $currentFields = [];
    foreach ($fields as $field) {
        $type = (string) $field->type;
        if ($type === 'section') {
            if (count($currentFields) > 0) {
                $steps[] = ['title' => $currentTitle, 'fields' => $currentFields];
            }
            $currentTitle = $field->label;
            $currentFields = [];
        } else {
            $currentFields[] = $field;
        }
    }
    if (count($currentFields) > 0) {
        $steps[] = ['title' => $currentTitle, 'fields' => $currentFields];
    }
    $hasStepper = count($steps) > 1;
    $stepsCount = count($steps);
    $stepperCompact = $stepsCount > 4; // Beaucoup d'étapes : stepper compact (numéros seuls ou scroll)
@endphp

<div
    x-data="{
        step: 0,
        totalSteps: {{ $stepsCount }},
        get canPrev() { return this.step > 0; },
        get canNext() { return this.step < this.totalSteps - 1; },
        get isLastStep() { return this.step === this.totalSteps - 1; },
        next() { if (this.canNext) this.step++; },
        prev() { if (this.canPrev) this.step--; }
    }"
>
    {{-- Bannière d'avertissement : formulaire en retard --}}
    @if(isset($assignment) && $assignment && $assignment->isOverdue() && !$assignment->isExpired())
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 flex items-center gap-3 shadow-sm mb-4 sm:mb-6 shrink-0">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                <iconify-icon icon="solar:danger-triangle-bold" width="18"></iconify-icon>
            </div>
            <span>{{ __('pages.forms.form_overdue_warning') }}</span>
        </div>
    @endif

    {{-- En-tête : retour + titre formulaire (aligné maquette) — fixe --}}
    <div class="flex items-center gap-3 sm:gap-4 mb-4 sm:mb-6 shrink-0">
        <a href="{{ route('forms.index') }}"
           class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-colors touch-manipulation">
            <iconify-icon icon="solar:arrow-left-linear" width="20"></iconify-icon>
        </a>
        <div class="min-w-0 flex-1">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight sm:text-2xl truncate">{{ $form->name }}</h1>
            @if($form->description || (isset($assignment) && $assignment && $assignment->due_date))
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1 text-sm text-slate-500">
                    @if($form->description)
                        <span class="line-clamp-2">{{ $form->description }}</span>
                    @endif
                    @if(isset($assignment) && $assignment && $assignment->due_date)
                        <span class="inline-flex items-center gap-1.5 rounded-md bg-slate-100 px-2 py-0.5 font-medium text-slate-600 shrink-0">
                            <iconify-icon icon="solar:calendar-linear" width="12"></iconify-icon>
                            {{ __('pages.forms.due_date') }}: {{ $assignment->due_date->format('d/m/Y') }}
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Stepper : scroll horizontal si beaucoup d'étapes, structure qui ne casse pas --}}
    @if($hasStepper)
        <div class="mb-4 sm:mb-5 shrink-0 overflow-x-auto overflow-y-hidden -mx-1 px-1">
            <div class="flex items-center {{ $stepperCompact ? 'gap-1 min-w-max' : 'w-full' }} {{ $stepperCompact ? '' : 'gap-0' }}">
                @foreach($steps as $i => $s)
                    <button type="button"
                            @click="if (step >= {{ $i }}) step = {{ $i }}"
                            class="flex {{ $stepperCompact ? 'shrink-0' : 'flex-1' }} items-center justify-center gap-1.5 sm:gap-2 min-w-0 group touch-manipulation py-1">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-sm font-bold transition-all"
                              :class="step >= {{ $i }}
                                ? 'bg-[var(--accent)] text-white shadow-sm'
                                : 'bg-slate-200 text-slate-500 group-hover:bg-slate-300'">
                            {{ $i + 1 }}
                        </span>
                        @if(!$stepperCompact)
                            <span class="text-xs font-semibold truncate max-w-[70px] sm:max-w-[90px]"
                                  :class="step >= {{ $i }} ? 'text-slate-900' : 'text-slate-400'">
                                {{ \Illuminate\Support\Str::limit($s['title'], 12) }}
                            </span>
                        @endif
                    </button>
                    @if($i < count($steps) - 1)
                        <div class="flex-shrink-0 w-4 sm:w-6 h-0.5 rounded-full bg-slate-200 mx-0.5"
                             :class="step > {{ $i }} ? '!bg-[var(--accent)]' : ''"></div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    {{-- Formulaire : page scroll normale (pas de scroll interne) --}}
    <form wire:submit="submit" wire:loading.class="opacity-90 pointer-events-none" wire:target="submit" class="flex flex-col bg-white rounded-xl sm:rounded-2xl border border-slate-200 shadow-sm overflow-visible">
        <div class="p-5 sm:p-6 lg:p-8 space-y-6">
            @foreach($steps as $stepIndex => $stepData)
                <div x-show="{{ $hasStepper ? 'step === ' . $stepIndex : 'true' }}"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     @if($hasStepper && $stepIndex > 0) x-cloak @endif>
                    @if($hasStepper)
                        <h2 class="text-base font-bold text-slate-900 pb-3">
                            {{ $stepData['title'] }}
                        </h2>
                    @endif
                    <div class="grid grid-cols-6 gap-x-4 gap-y-5">
                    @foreach($stepData['fields'] as $field)
                        @php
                            $type = (string) $field->type;
                            $key = (string) $field->key;
                            $config = is_array($field->configuration) ? $field->configuration : [];
                            $placeholder = $config['placeholder'] ?? '';
                            $helpText = $config['help_text'] ?? '';
                            $options = $config['options'] ?? [];
                            $displayMode = $config['display_mode'] ?? 'list';
                            $fieldLayout = $config['layout'] ?? 'full';
                            $colSpan = match($fieldLayout) {
                                'half' => 'col-span-6 sm:col-span-3',
                                'third' => 'col-span-6 sm:col-span-2',
                                default => 'col-span-6',
                            };
                        @endphp
                        <div class="{{ $colSpan }} min-w-0">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                {{ $field->label }}
                                @if($field->required) <span class="text-red-500">*</span> @endif
                            </label>

                            @if($type === 'textarea')
                                <textarea wire:model.blur="answers.{{ $key }}" rows="4"
                                          placeholder="{{ $placeholder ?: $field->label }}"
                                          class="input-manexo w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-[var(--accent)] focus:border-[var(--accent)] resize-y min-h-[100px]"></textarea>
                            @elseif($type === 'select')
                                <div class="relative group/select">
                                    <select wire:model="answers.{{ $key }}"
                                            class="select-manexo-inset block w-full text-[13px] font-medium text-slate-800 transition-colors duration-200">
                                        <option value="">{{ $placeholder ?: __('Sélectionnez...') }}</option>
                                        @foreach((array) $options as $opt)
                                            <option value="{{ $opt }}">{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 transition-colors duration-200 group-hover/select:text-slate-600">
                                        <iconify-icon icon="solar:alt-arrow-down-linear" width="16" class="opacity-90"></iconify-icon>
                                    </div>
                                </div>
                            @elseif($type === 'radio')
                                <div class="{{ $displayMode === 'inline' ? 'flex flex-wrap gap-2.5' : ($displayMode === 'grid' ? 'grid grid-cols-2 gap-2.5' : ($displayMode === 'card' ? 'space-y-2.5' : 'space-y-2')) }}">
                                    @foreach((array) $options as $opt)
                                        <label class="flex items-center gap-3 px-3 py-3 rounded-lg border border-slate-200 bg-white hover:border-slate-300 transition-colors cursor-pointer has-[:checked]:border-[var(--accent)] has-[:checked]:bg-[var(--accent-soft)]/30
                                            {{ $displayMode === 'card' ? 'rounded-xl px-4 py-3.5' : '' }}
                                            {{ $displayMode === 'inline' ? 'inline-flex px-3.5 py-2.5 rounded-xl' : '' }}
                                            {{ $displayMode === 'grid' ? 'rounded-xl' : '' }}">
                                            <input type="radio" wire:model="answers.{{ $key }}" value="{{ $opt }}"
                                                   class="text-[var(--accent)] focus:ring-[var(--accent)] focus:ring-2 size-4">
                                            <span class="text-sm font-medium text-slate-700">{{ $opt }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @elseif($type === 'checkbox')
                                @php $cbOptions = $config['options'] ?? $field->options ?? []; @endphp
                                @if(count((array) $cbOptions) > 0)
                                    <div class="{{ $displayMode === 'inline' ? 'flex flex-wrap gap-2.5' : ($displayMode === 'grid' ? 'grid grid-cols-2 gap-2.5' : ($displayMode === 'card' ? 'space-y-2.5' : 'space-y-2')) }}">
                                        @foreach((array) $cbOptions as $opt)
                                            <label class="flex items-center gap-3 px-3 py-3 rounded-lg border border-slate-200 bg-white hover:border-slate-300 transition-colors cursor-pointer has-[:checked]:border-[var(--accent)] has-[:checked]:bg-[var(--accent-soft)]/30
                                                {{ $displayMode === 'card' ? 'rounded-xl px-4 py-3.5' : '' }}
                                                {{ $displayMode === 'inline' ? 'inline-flex px-3.5 py-2.5 rounded-xl' : '' }}
                                                {{ $displayMode === 'grid' ? 'rounded-xl' : '' }}">
                                                <input type="checkbox" wire:model="answers.{{ $key }}" value="{{ $opt }}"
                                                       class="rounded border-slate-300 text-[var(--accent)] focus:ring-[var(--accent)] focus:ring-2 size-4">
                                                <span class="text-sm font-medium text-slate-700">{{ $opt }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <label class="flex items-center gap-3 px-3 py-3 rounded-lg border border-slate-200 bg-white hover:border-slate-300 cursor-pointer has-[:checked]:border-[var(--accent)] has-[:checked]:bg-[var(--accent-soft)]/30 transition-colors">
                                        <input type="checkbox" wire:model="answers.{{ $key }}"
                                               class="rounded border-slate-300 text-[var(--accent)] focus:ring-[var(--accent)] focus:ring-2 size-4">
                                        <span class="text-sm font-medium text-slate-700">{{ $field->label }}</span>
                                    </label>
                                @endif
                            @elseif($type === 'date')
                                <input type="date" wire:model="answers.{{ $key }}"
                                       class="input-manexo w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-[var(--accent)] focus:border-[var(--accent)]">
                            @elseif($type === 'datetime')
                                <input type="datetime-local" wire:model="answers.{{ $key }}"
                                       class="input-manexo w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-[var(--accent)] focus:border-[var(--accent)]">
                            @elseif($type === 'number')
                                <input type="number" wire:model.blur="answers.{{ $key }}" placeholder="{{ $placeholder ?: $field->label }}"
                                       class="input-manexo w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-[var(--accent)] focus:border-[var(--accent)]">
                            @elseif($type === 'email')
                                <input type="email" wire:model.blur="answers.{{ $key }}" placeholder="{{ $placeholder ?: $field->label }}"
                                       class="input-manexo w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-[var(--accent)] focus:border-[var(--accent)]">
                            @elseif($type === 'file')
                                <input type="file" wire:model="fileUploads.{{ $key }}"
                                       class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[var(--accent-soft)] file:text-[var(--accent)] hover:file:opacity-90 transition-opacity">
                            @else
                                <input type="text" wire:model.blur="answers.{{ $key }}" placeholder="{{ $placeholder ?: $field->label }}"
                                       class="input-manexo w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-[var(--accent)] focus:border-[var(--accent)]">
                            @endif

                            @if($helpText)
                                <p class="mt-1.5 text-xs text-slate-500">{{ $helpText }}</p>
                            @endif
                            <x-input-error :messages="$errors->get('answers.' . $key)" class="mt-1" />
                        </div>
                    @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pied : Annuler (icône X) | Précédent / Suivant / Soumettre — toujours visible --}}
        <div class="sticky bottom-0 z-20 shrink-0 px-5 sm:px-6 lg:px-8 py-4 border-t border-slate-200 bg-white/90 backdrop-blur flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('forms.index') }}"
               class="order-2 sm:order-1 inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors touch-manipulation">
                <iconify-icon icon="solar:close-circle-bold" width="18" class="text-red-500"></iconify-icon>
                {{ __('Annuler') }}
            </a>
            <div class="order-1 sm:order-2 flex items-center gap-2 w-full sm:w-auto justify-end">
                @if($hasStepper)
                    <template x-if="canPrev">
                        <button type="button" @click="prev()"
                                class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors touch-manipulation">
                            <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
                            {{ __('Précédent') }}
                        </button>
                    </template>
                    <template x-if="canNext">
                        <button type="button" @click="next()"
                                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white rounded-lg shadow-sm hover:opacity-90 transition-all touch-manipulation"
                                style="background-color: var(--accent);">
                            {{ __('Suivant') }}
                            <iconify-icon icon="solar:arrow-right-linear" width="18"></iconify-icon>
                        </button>
                    </template>
                    <template x-if="isLastStep">
                        <x-manexo.action-button
                            type="submit"
                            wire-target="submit"
                            variant="primary"
                            class="!rounded-lg !px-5 touch-manipulation !font-bold"
                            style="background-color: var(--accent);"
                            :loading-label="__('Envoi...')"
                        >
                            {{ __('Soumettre') }}
                            <iconify-icon icon="solar:plain-bold" width="16"></iconify-icon>
                        </x-manexo.action-button>
                    </template>
                @else
                    <x-manexo.action-button
                        type="submit"
                        wire-target="submit"
                        variant="primary"
                        class="!rounded-lg !px-5 touch-manipulation !font-bold"
                        style="background-color: var(--accent);"
                        :loading-label="__('Envoi...')"
                    >
                        {{ __('Soumettre') }}
                        <iconify-icon icon="solar:plain-bold" width="16"></iconify-icon>
                    </x-manexo.action-button>
                @endif
            </div>
        </div>
    </form>
</div>
@endif
</div>
