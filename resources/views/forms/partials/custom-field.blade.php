@php
    /** @var \App\Models\FormField $f */
    $type        = (string) $f->type;
    $config      = is_array($f->configuration) ? $f->configuration : [];
    $placeholder = $config['placeholder'] ?? '';
    $helpText    = $config['help_text'] ?? '';
    $options     = $config['options'] ?? $f->options ?? [];
    $displayMode = $config['display_mode'] ?? 'list';
    $fieldLayout = $config['layout'] ?? 'full';
    $name        = "custom[{$f->key}]";
    $oldVal      = old("custom.{$f->key}");
    $fieldId     = 'field-' . ((string) ($f->id ?? $f->key)) . '-' . $f->key;
    $hasChoiceOptions = in_array($type, ['radio', 'checkbox'], true) && is_array($options) && count($options) > 0;
    $labelForId = $hasChoiceOptions ? $fieldId . '-opt-0' : $fieldId;
@endphp

<div class="space-y-2 {{ $fieldLayout === 'half' ? 'col-span-6 sm:col-span-3' : ($fieldLayout === 'third' ? 'col-span-6 sm:col-span-2' : 'col-span-6') }}">
    <label for="{{ $labelForId }}" class="text-[13px] font-semibold text-slate-700 block">
        {{ $f->label }}
        @if($f->required)<span class="text-red-400 ml-0.5">*</span>@endif
    </label>

    @if($type === 'textarea')
        <textarea name="{{ $name }}" id="{{ $fieldId }}" rows="4"
                  class="mnx-input block w-full rounded-xl border border-slate-200 bg-white py-3 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0 transition resize-none"
                  placeholder="{{ $placeholder }}"
                  @required($f->required)>{{ $oldVal }}</textarea>

    @elseif($type === 'select')
        <div class="relative group/select">
            <select name="{{ $name }}" id="{{ $fieldId }}"
                    class="select-manexo-inset block w-full text-[13px] font-medium text-slate-800 transition-colors duration-200"
                    @required($f->required)>
                <option value="">{{ $placeholder ?: __('Sélectionner…') }}</option>
                @foreach((array) $options as $opt)
                    <option value="{{ $opt }}" @selected((string) $oldVal === (string) $opt)>{{ $opt }}</option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 transition-colors duration-200 group-hover/select:text-slate-600">
                <iconify-icon icon="solar:alt-arrow-down-linear" width="16" class="opacity-90"></iconify-icon>
            </div>
        </div>

    @elseif($type === 'radio')
        @php $radioOpts = (array) $options; @endphp
        <div class="pt-1
            {{ $displayMode === 'inline' ? 'flex flex-wrap gap-2.5' : '' }}
            {{ $displayMode === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 gap-2.5' : '' }}
            {{ $displayMode === 'card' ? 'space-y-2.5' : '' }}
            {{ $displayMode === 'list' ? 'space-y-2' : '' }}
        ">
            @foreach($radioOpts as $opt)
                @php $optionId = $fieldId . '-opt-' . $loop->index; @endphp
                @if($displayMode === 'card')
                    <label class="mnx-option-card flex items-center gap-4 px-4 py-3.5 rounded-xl border border-slate-200 bg-white cursor-pointer group"
                           :class="{ 'selected': false }">
                        <input type="radio" name="{{ $name }}" id="{{ $optionId }}" value="{{ $opt }}"
                               class="mnx-radio"
                               @checked((string) $oldVal === (string) $opt)
                               @required($f->required)>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition">{{ $opt }}</span>
                    </label>
                @elseif($displayMode === 'inline')
                    <label class="mnx-option-card inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl border border-slate-200 bg-white cursor-pointer group">
                        <input type="radio" name="{{ $name }}" id="{{ $optionId }}" value="{{ $opt }}"
                               class="mnx-radio" style="width:16px;height:16px;"
                               @checked((string) $oldVal === (string) $opt)
                               @required($f->required)>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition">{{ $opt }}</span>
                    </label>
                @elseif($displayMode === 'grid')
                    <label class="mnx-option-card flex items-center gap-3.5 px-4 py-3 rounded-xl border border-slate-200 bg-white cursor-pointer group">
                        <input type="radio" name="{{ $name }}" id="{{ $optionId }}" value="{{ $opt }}"
                               class="mnx-radio" style="width:18px;height:18px;"
                               @checked((string) $oldVal === (string) $opt)
                               @required($f->required)>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition">{{ $opt }}</span>
                    </label>
                @else
                    {{-- List (default) --}}
                    <label class="mnx-option-card flex items-center gap-3.5 px-4 py-3 rounded-xl border border-slate-200 bg-white cursor-pointer group">
                        <input type="radio" name="{{ $name }}" id="{{ $optionId }}" value="{{ $opt }}"
                               class="mnx-radio"
                               @checked((string) $oldVal === (string) $opt)
                               @required($f->required)>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition">{{ $opt }}</span>
                    </label>
                @endif
            @endforeach
        </div>

    @elseif($type === 'checkbox')
        @if(is_array($options) && count($options) > 0)
            {{-- Multiple checkboxes with options --}}
            <div class="pt-1
                {{ $displayMode === 'inline' ? 'flex flex-wrap gap-2.5' : '' }}
                {{ $displayMode === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 gap-2.5' : '' }}
                {{ $displayMode === 'card' ? 'space-y-2.5' : '' }}
                {{ $displayMode === 'list' ? 'space-y-2' : '' }}
            ">
                @foreach((array) $options as $opt)
                    @php $optionId = $fieldId . '-opt-' . $loop->index; @endphp
                    @if($displayMode === 'card')
                        <label class="mnx-option-card flex items-center gap-4 px-4 py-3.5 rounded-xl border border-slate-200 bg-white cursor-pointer group">
                            <input type="checkbox" name="{{ $name }}[]" id="{{ $optionId }}" value="{{ $opt }}"
                                   class="mnx-checkbox"
                                   @checked(is_array($oldVal) && in_array($opt, $oldVal))
                                   @if($f->required && $loop->first) required @endif>
                            <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition">{{ $opt }}</span>
                        </label>
                    @elseif($displayMode === 'inline')
                        <label class="mnx-option-card inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl border border-slate-200 bg-white cursor-pointer group">
                            <input type="checkbox" name="{{ $name }}[]" id="{{ $optionId }}" value="{{ $opt }}"
                                   class="mnx-checkbox" style="width:16px;height:16px;border-radius:4px;"
                                   @checked(is_array($oldVal) && in_array($opt, $oldVal))
                                   @if($f->required && $loop->first) required @endif>
                            <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition">{{ $opt }}</span>
                        </label>
                    @elseif($displayMode === 'grid')
                        <label class="mnx-option-card flex items-center gap-3.5 px-4 py-3 rounded-xl border border-slate-200 bg-white cursor-pointer group">
                            <input type="checkbox" name="{{ $name }}[]" id="{{ $optionId }}" value="{{ $opt }}"
                                   class="mnx-checkbox" style="width:18px;height:18px;"
                                   @checked(is_array($oldVal) && in_array($opt, $oldVal))
                                   @if($f->required && $loop->first) required @endif>
                            <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition">{{ $opt }}</span>
                        </label>
                    @else
                        {{-- List (default) --}}
                        <label class="mnx-option-card flex items-center gap-3.5 px-4 py-3 rounded-xl border border-slate-200 bg-white cursor-pointer group">
                            <input type="checkbox" name="{{ $name }}[]" id="{{ $optionId }}" value="{{ $opt }}"
                                   class="mnx-checkbox"
                                   @checked(is_array($oldVal) && in_array($opt, $oldVal))
                                   @if($f->required && $loop->first) required @endif>
                            <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition">{{ $opt }}</span>
                        </label>
                    @endif
                @endforeach
            </div>
        @else
            {{-- Single checkbox toggle --}}
            <label class="mnx-option-card flex items-center gap-3.5 px-4 py-3 rounded-xl border border-slate-200 bg-white cursor-pointer group">
                <input type="hidden" name="{{ $name }}" id="{{ $fieldId }}-hidden" value="0">
                <input type="checkbox" name="{{ $name }}" id="{{ $fieldId }}" value="1"
                       class="mnx-checkbox"
                       @checked((bool) $oldVal)>
                <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition">{{ __('Oui') }}</span>
            </label>
        @endif

    @elseif($type === 'file')
        <div x-data="{ fileName: null, dragging: false }" class="relative">
            <input type="file" name="{{ $name }}" id="{{ $fieldId }}"
                   class="peer absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                   @if(!empty($config['accept'])) accept="{{ $config['accept'] }}" @endif
                   @required($f->required)
                   @change="fileName = $event.target.files[0]?.name || null"
                   @dragover.prevent="dragging = true"
                   @dragleave="dragging = false"
                   @drop="dragging = false">
            <div class="flex items-center gap-4 px-4 py-4 rounded-xl border-2 border-dashed transition-all"
                 :class="{
                     'border-[var(--accent)] bg-[var(--accent-soft)]': fileName || dragging,
                     'border-slate-200 bg-white hover:border-slate-300': !fileName && !dragging
                 }">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl transition"
                     :class="fileName ? 'bg-white text-[var(--accent)] shadow-sm' : 'bg-slate-50 text-slate-400'">
                    <iconify-icon :icon="fileName ? 'solar:file-check-bold-duotone' : 'solar:cloud-upload-linear'" width="22"></iconify-icon>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-sm font-medium truncate transition"
                         :class="fileName ? 'text-slate-900' : 'text-slate-500'"
                         x-text="fileName || '{{ __('Glissez un fichier ou cliquez pour parcourir') }}'"></div>
                    <div class="text-[11px] mt-0.5 transition"
                         :class="fileName ? 'text-[var(--accent)]' : 'text-slate-400'">
                        <span x-show="!fileName">{{ !empty($config['accept']) ? $config['accept'] . ' · ' : '' }}{{ __('Max 10 Mo') }}</span>
                        <span x-show="fileName" x-cloak>{{ __('Fichier sélectionné') }}</span>
                    </div>
                </div>
                <div x-show="fileName" x-cloak class="shrink-0">
                    <iconify-icon icon="solar:check-circle-bold" width="20" class="text-[var(--accent)]"></iconify-icon>
                </div>
            </div>
        </div>

    @else
        @php
            $inputType = in_array($type, ['email', 'date', 'datetime', 'number'], true)
                ? ($type === 'datetime' ? 'datetime-local' : $type)
                : 'text';
        @endphp
        <input type="{{ $inputType }}" name="{{ $name }}" id="{{ $fieldId }}" value="{{ $oldVal }}"
               class="mnx-input block w-full rounded-xl border border-slate-200 bg-white py-3 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0 transition"
               placeholder="{{ $placeholder }}"
               @required($f->required)>
    @endif

    @if($helpText)
        <p class="text-[12px] text-slate-400 leading-relaxed">{{ $helpText }}</p>
    @endif

    @error("custom.{$f->key}")<p class="text-[12px] text-red-500 mt-1 flex items-center gap-1"><iconify-icon icon="solar:danger-circle-bold" width="13"></iconify-icon> {{ $message }}</p>@enderror
</div>
