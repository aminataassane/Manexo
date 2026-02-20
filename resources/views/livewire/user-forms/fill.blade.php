@php
    $form = $assignment->form;
    $fields = $form->fields;
@endphp

<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('forms.index') }}" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors">
            <iconify-icon icon="solar:arrow-left-linear" width="20"></iconify-icon>
        </a>
        <div>
            <h1 class="text-xl font-bold text-slate-900">{{ $form->name }}</h1>
            @if($form->description)
                <p class="text-sm text-slate-500 mt-1">{{ $form->description }}</p>
            @endif
        </div>
    </div>

    @if($assignment->due_date)
        <div class="flex items-center gap-2 text-xs text-slate-500 bg-white rounded-lg border border-slate-200 px-4 py-2.5">
            <iconify-icon icon="solar:calendar-linear" width="14" class="text-slate-400"></iconify-icon>
            {{ __('Échéance') }}: <span class="font-semibold text-slate-700">{{ $assignment->due_date->format('d/m/Y') }}</span>
        </div>
    @endif

    <form wire:submit="submit" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 sm:p-8 space-y-5">
            @foreach($fields as $field)
                @php
                    $type = (string) $field->type;
                    $key = (string) $field->key;
                    $config = is_array($field->configuration) ? $field->configuration : [];
                    $placeholder = $config['placeholder'] ?? '';
                    $helpText = $config['help_text'] ?? '';
                    $options = $config['options'] ?? [];
                @endphp

                @if($type === 'section')
                    <div class="pt-4 pb-2 border-t border-slate-100 first:border-t-0 first:pt-0">
                        <h3 class="text-base font-bold text-slate-900">{{ $field->label }}</h3>
                        @if($helpText)
                            <p class="text-sm text-slate-500 mt-0.5">{{ $helpText }}</p>
                        @endif
                    </div>
                @else
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            {{ $field->label }}
                            @if($field->required) <span class="text-red-500">*</span> @endif
                        </label>

                        @if($type === 'textarea')
                            <textarea wire:model="answers.{{ $key }}" rows="4"
                                      placeholder="{{ $placeholder }}"
                                      class="w-full rounded-lg border-slate-200 text-sm focus:ring-[var(--accent)] focus:border-[var(--accent)]"></textarea>
                        @elseif($type === 'select')
                            <select wire:model="answers.{{ $key }}"
                                    class="w-full rounded-lg border-slate-200 text-sm focus:ring-[var(--accent)] focus:border-[var(--accent)]">
                                <option value="">{{ $placeholder ?: __('Sélectionnez...') }}</option>
                                @foreach((array) $options as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>
                        @elseif($type === 'radio')
                            <div class="space-y-2">
                                @foreach((array) $options as $opt)
                                    <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:border-[var(--accent)] hover:bg-slate-50 transition-colors cursor-pointer">
                                        <input type="radio" wire:model="answers.{{ $key }}" value="{{ $opt }}"
                                               class="text-[var(--accent)] focus:ring-[var(--accent)]">
                                        <span class="text-sm text-slate-700">{{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @elseif($type === 'checkbox')
                            <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 cursor-pointer">
                                <input type="checkbox" wire:model="answers.{{ $key }}"
                                       class="rounded text-[var(--accent)] focus:ring-[var(--accent)]">
                                <span class="text-sm text-slate-700">{{ $field->label }}</span>
                            </label>
                        @elseif($type === 'date')
                            <input type="date" wire:model="answers.{{ $key }}"
                                   class="w-full rounded-lg border-slate-200 text-sm focus:ring-[var(--accent)] focus:border-[var(--accent)]">
                        @elseif($type === 'datetime')
                            <input type="datetime-local" wire:model="answers.{{ $key }}"
                                   class="w-full rounded-lg border-slate-200 text-sm focus:ring-[var(--accent)] focus:border-[var(--accent)]">
                        @elseif($type === 'number')
                            <input type="number" wire:model="answers.{{ $key }}" placeholder="{{ $placeholder }}"
                                   class="w-full rounded-lg border-slate-200 text-sm focus:ring-[var(--accent)] focus:border-[var(--accent)]">
                        @elseif($type === 'email')
                            <input type="email" wire:model="answers.{{ $key }}" placeholder="{{ $placeholder }}"
                                   class="w-full rounded-lg border-slate-200 text-sm focus:ring-[var(--accent)] focus:border-[var(--accent)]">
                        @elseif($type === 'file')
                            <input type="file" wire:model="answers.{{ $key }}"
                                   class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[var(--accent-soft)] file:text-[var(--accent)] hover:file:bg-[var(--accent-soft)]/80">
                        @else
                            <input type="text" wire:model="answers.{{ $key }}" placeholder="{{ $placeholder }}"
                                   class="w-full rounded-lg border-slate-200 text-sm focus:ring-[var(--accent)] focus:border-[var(--accent)]">
                        @endif

                        @if($helpText)
                            <p class="mt-1 text-xs text-slate-500">{{ $helpText }}</p>
                        @endif
                        <x-input-error :messages="$errors->get('answers.' . $key)" />
                    </div>
                @endif
            @endforeach
        </div>

        <div class="px-6 sm:px-8 py-4 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
            <a href="{{ route('forms.index') }}" class="px-4 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                {{ __('Annuler') }}
            </a>
            <button type="submit"
                    class="px-6 py-2.5 text-sm font-bold text-white rounded-lg shadow-sm bg-[var(--accent)] hover:opacity-90 transition-all">
                {{ __('Soumettre') }}
            </button>
        </div>
    </form>
</div>
