@php
    $form = isset($assignment) && $assignment ? $assignment->form : ($form ?? null);
    abort_if(! $form, 404);
    $fields = $form->fields;

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
@endphp

<div
    class="w-full max-w-3xl mx-auto min-w-0 px-0 sm:px-2 pb-6 sm:pb-8"
    style="padding-bottom: max(1.5rem, env(safe-area-inset-bottom, 0));"
    x-data="{
        step: 0,
        totalSteps: {{ count($steps) }},
        get canPrev() { return this.step > 0; },
        get canNext() { return this.step < this.totalSteps - 1; },
        get isLastStep() { return this.step === this.totalSteps - 1; },
        next() { if (this.canNext) this.step++; },
        prev() { if (this.canPrev) this.step--; }
    }"
>
    {{-- En-tête : retour + titre formulaire --}}
    <div class="flex items-start gap-3 sm:gap-4 mb-6 sm:mb-8">
        <a href="{{ route('forms.index') }}"
           class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-colors touch-manipulation">
            <iconify-icon icon="solar:arrow-left-linear" width="20"></iconify-icon>
        </a>
        <div class="min-w-0 flex-1">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight sm:text-2xl break-words">{{ $form->name }}</h1>
            @if($form->description)
                <p class="text-sm text-slate-500 mt-1">{{ $form->description }}</p>
            @endif
            @if(isset($assignment) && $assignment && $assignment->due_date)
                <div class="flex items-center gap-2 mt-3 text-xs text-slate-500">
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-2.5 py-1.5 font-medium text-slate-700">
                        <iconify-icon icon="solar:calendar-linear" width="14"></iconify-icon>
                        {{ __('pages.forms.due_date') }}: {{ $assignment->due_date->format('d/m/Y') }}
                    </span>
                </div>
            @endif
        </div>
    </div>

    {{-- Stepper (affiché seulement s'il y a plusieurs étapes) --}}
    @if($hasStepper)
        <div class="mb-6 sm:mb-8">
            <div class="flex items-center justify-between gap-1">
                @foreach($steps as $i => $s)
                    <div class="flex flex-1 items-center min-w-0">
                        <button type="button"
                                @click="if (step >= {{ $i }}) step = {{ $i }}"
                                class="flex flex-col sm:flex-row items-center gap-1 sm:gap-2 shrink-0 group touch-manipulation">
                            <span class="flex h-8 w-8 sm:h-9 sm:w-9 items-center justify-center rounded-full text-xs font-bold transition-all shrink-0"
                                  :class="step >= {{ $i }}
                                    ? 'bg-[var(--accent)] text-white shadow-sm'
                                    : 'bg-slate-200 text-slate-500 group-hover:bg-slate-300'">
                                {{ $i + 1 }}
                            </span>
                            <span class="text-[10px] sm:text-xs font-semibold text-center truncate max-w-[72px] sm:max-w-none"
                                  :class="step >= {{ $i }} ? 'text-slate-900' : 'text-slate-400'">
                                {{ \Illuminate\Support\Str::limit($s['title'], 12) }}
                            </span>
                        </button>
                        @if($i < count($steps) - 1)
                            <div class="flex-1 h-0.5 mx-0.5 sm:mx-1 rounded-full bg-slate-200 min-w-[8px]"
                                 :class="step > {{ $i }} ? 'bg-[var(--accent)]' : ''"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <form wire:submit="submit" class="bg-white rounded-xl sm:rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 lg:p-8 space-y-6">
            @foreach($steps as $stepIndex => $stepData)
                <div x-show="{{ $hasStepper ? 'step === ' . $stepIndex : 'true' }}"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     class="space-y-5"
                     @if($hasStepper && $stepIndex > 0) x-cloak @endif>
                    @if($hasStepper)
                        <h2 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100">
                            {{ $stepData['title'] }}
                        </h2>
                    @endif
                    @foreach($stepData['fields'] as $field)
                        @php
                            $type = (string) $field->type;
                            $key = (string) $field->key;
                            $config = is_array($field->configuration) ? $field->configuration : [];
                            $placeholder = $config['placeholder'] ?? '';
                            $helpText = $config['help_text'] ?? '';
                            $options = $config['options'] ?? [];
                        @endphp
                        <div class="min-w-0">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                {{ $field->label }}
                                @if($field->required) <span class="text-red-500">*</span> @endif
                            </label>

                            @if($type === 'textarea')
                                <textarea wire:model="answers.{{ $key }}" rows="4"
                                          placeholder="{{ $placeholder }}"
                                          class="input-manexo w-full rounded-xl border-slate-200 text-sm focus:ring-2 focus:ring-[var(--accent)] focus:border-[var(--accent)] resize-y min-h-[100px]"></textarea>
                            @elseif($type === 'select')
                                <select wire:model="answers.{{ $key }}"
                                        class="input-manexo w-full rounded-xl border-slate-200 text-sm focus:ring-2 focus:ring-[var(--accent)] focus:border-[var(--accent)] py-2.5">
                                    <option value="">{{ $placeholder ?: __('Sélectionnez...') }}</option>
                                    @foreach((array) $options as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                    @endforeach
                                </select>
                            @elseif($type === 'radio')
                                <div class="space-y-2">
                                    @foreach((array) $options as $opt)
                                        <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-[var(--accent)]/50 hover:bg-[var(--accent-soft)]/30 transition-colors cursor-pointer has-[:checked]:border-[var(--accent)] has-[:checked]:bg-[var(--accent-soft)]/50">
                                            <input type="radio" wire:model="answers.{{ $key }}" value="{{ $opt }}"
                                                   class="text-[var(--accent)] focus:ring-[var(--accent)] focus:ring-2">
                                            <span class="text-sm font-medium text-slate-700">{{ $opt }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @elseif($type === 'checkbox')
                                @php $options = $field->options ?? []; @endphp
                                @if(count((array) $options) > 0)
                                    <div class="space-y-2">
                                        @foreach((array) $options as $opt)
                                            <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-[var(--accent)]/50 hover:bg-[var(--accent-soft)]/30 transition-colors cursor-pointer has-[:checked]:border-[var(--accent)] has-[:checked]:bg-[var(--accent-soft)]/50">
                                                <input type="checkbox" wire:model="answers.{{ $key }}" value="{{ $opt }}"
                                                       class="rounded text-[var(--accent)] focus:ring-[var(--accent)] focus:ring-2">
                                                <span class="text-sm font-medium text-slate-700">{{ $opt }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-[var(--accent)]/50 cursor-pointer has-[:checked]:border-[var(--accent)] has-[:checked]:bg-[var(--accent-soft)]/30 transition-colors">
                                        <input type="checkbox" wire:model="answers.{{ $key }}"
                                               class="rounded text-[var(--accent)] focus:ring-[var(--accent)] focus:ring-2">
                                        <span class="text-sm font-medium text-slate-700">{{ $field->label }}</span>
                                    </label>
                                @endif
                            @elseif($type === 'date')
                                <input type="date" wire:model="answers.{{ $key }}"
                                       class="input-manexo w-full rounded-xl border-slate-200 text-sm focus:ring-2 focus:ring-[var(--accent)] focus:border-[var(--accent)] py-2.5">
                            @elseif($type === 'datetime')
                                <input type="datetime-local" wire:model="answers.{{ $key }}"
                                       class="input-manexo w-full rounded-xl border-slate-200 text-sm focus:ring-2 focus:ring-[var(--accent)] focus:border-[var(--accent)] py-2.5">
                            @elseif($type === 'number')
                                <input type="number" wire:model="answers.{{ $key }}" placeholder="{{ $placeholder }}"
                                       class="input-manexo w-full rounded-xl border-slate-200 text-sm focus:ring-2 focus:ring-[var(--accent)] focus:border-[var(--accent)] py-2.5">
                            @elseif($type === 'email')
                                <input type="email" wire:model="answers.{{ $key }}" placeholder="{{ $placeholder }}"
                                       class="input-manexo w-full rounded-xl border-slate-200 text-sm focus:ring-2 focus:ring-[var(--accent)] focus:border-[var(--accent)] py-2.5">
                            @elseif($type === 'file')
                                <input type="file" wire:model="answers.{{ $key }}"
                                       class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[var(--accent-soft)] file:text-[var(--accent)] hover:file:opacity-90 transition-opacity">
                            @else
                                <input type="text" wire:model="answers.{{ $key }}" placeholder="{{ $placeholder }}"
                                       class="input-manexo w-full rounded-xl border-slate-200 text-sm focus:ring-2 focus:ring-[var(--accent)] focus:border-[var(--accent)] py-2.5">
                            @endif

                            @if($helpText)
                                <p class="mt-1.5 text-xs text-slate-500">{{ $helpText }}</p>
                            @endif
                            <x-input-error :messages="$errors->get('answers.' . $key)" class="mt-1" />
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        {{-- Pied : Annuler | Précédent / Suivant | Soumettre --}}
        <div class="px-5 sm:px-6 lg:px-8 py-4 border-t border-slate-100 bg-slate-50/80 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('forms.index') }}"
               class="order-2 sm:order-1 inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors touch-manipulation">
                <iconify-icon icon="solar:close-circle-linear" width="18"></iconify-icon>
                {{ __('Annuler') }}
            </a>
            <div class="order-1 sm:order-2 flex items-center gap-2 w-full sm:w-auto justify-end">
                @if($hasStepper)
                    <template x-if="canPrev">
                        <button type="button" @click="prev()"
                                class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors touch-manipulation">
                            <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
                            {{ __('Précédent') }}
                        </button>
                    </template>
                    <template x-if="canNext">
                        <button type="button" @click="next()"
                                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white rounded-xl shadow-sm hover:opacity-90 transition-all touch-manipulation"
                                style="background-color: var(--accent);">
                            {{ __('Suivant') }}
                            <iconify-icon icon="solar:arrow-right-linear" width="18"></iconify-icon>
                        </button>
                    </template>
                    <template x-if="isLastStep">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white rounded-xl shadow-sm hover:opacity-90 transition-all touch-manipulation disabled:opacity-70"
                                style="background-color: var(--accent);"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="submit">{{ __('Soumettre') }}</span>
                            <span wire:loading wire:target="submit" class="inline-flex items-center gap-2">
                                <span class="h-3.5 w-3.5 rounded-full border-2 border-white border-t-transparent animate-spin"></span>
                                {{ __('Envoi...') }}
                            </span>
                            <iconify-icon icon="solar:plain-bold" width="16" wire:loading.remove wire:target="submit"></iconify-icon>
                        </button>
                    </template>
                @else
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white rounded-xl shadow-sm hover:opacity-90 transition-all touch-manipulation disabled:opacity-70"
                            style="background-color: var(--accent);"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="submit">{{ __('Soumettre') }}</span>
                        <span wire:loading wire:target="submit" class="inline-flex items-center gap-2">
                            <span class="h-3.5 w-3.5 rounded-full border-2 border-white border-t-transparent animate-spin"></span>
                            {{ __('Envoi...') }}
                        </span>
                        <iconify-icon icon="solar:plain-bold" width="16" wire:loading.remove wire:target="submit"></iconify-icon>
                    </button>
                @endif
            </div>
        </div>
    </form>
</div>
