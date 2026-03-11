<div class="flex flex-col min-h-0 flex-1">
    <div class="flex-1 min-h-0" x-data="{ dragFieldId: null, dropTargetId: null }">
        @php
            $selected = $selectedForm ?? ($fb_selected_form_id ? $forms->firstWhere('id', (int) $fb_selected_form_id) : null);
            $allFields = $selected?->fields ?? collect();
        @endphp

        @if ($selected)
            {{-- Form header --}}
            <div class="mb-8">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <h2 class="text-xl font-bold text-slate-900 tracking-tight">{{ $selected->name }}</h2>
                        @if($selected->description)
                            <p class="text-sm text-slate-500 mt-1.5 leading-relaxed max-w-lg">{{ $selected->description }}</p>
                        @endif
                    </div>
                    <span class="shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-[11px] font-medium text-slate-500">
                        <iconify-icon icon="solar:layers-linear" width="13"></iconify-icon>
                        {{ trans_choice('forms_builder.field_count', $allFields->count(), ['count' => $allFields->count()]) }}
                    </span>
                </div>
                @if($allFields->isNotEmpty())
                    <div class="mt-5 h-px bg-gradient-to-r from-slate-200 via-slate-200/60 to-transparent"></div>
                @endif
            </div>

            {{-- Drop zone: move to top --}}
            @if($allFields->isNotEmpty())
                <div class="rounded-lg border-2 border-dashed transition-all duration-200 -mb-1 min-h-[6px]"
                     :class="{ 'min-h-[44px] border-[var(--accent)] bg-[var(--accent-soft)]/20 shadow-sm': dragFieldId && dropTargetId === 'top', 'border-transparent': !dragFieldId || dropTargetId !== 'top' }"
                     @dragover.prevent="if (dragFieldId) dropTargetId = 'top'"
                     @dragleave="dropTargetId = null"
                     @drop.prevent="if (dragFieldId) { $wire.reorderFormField(dragFieldId, 0); dragFieldId = null; dropTargetId = null; }"
                ></div>
            @endif

            {{-- Fields grid --}}
            <div class="canvas-grid">
                @forelse ($allFields as $f)
                    @php
                        $isActive = (int) $fb_selected_field_id === (int) $f->id;
                        $isRequired = (bool) $f->required;
                        $label = (string) $f->label;
                        $type = (string) $f->type;
                        $config = is_array($f->configuration) ? $f->configuration : [];
                        $placeholder = (string) ($config['placeholder'] ?? '');
                        $help = (string) ($config['help_text'] ?? '');
                        $options = $config['options'] ?? [];
                        $displayMode = $config['display_mode'] ?? 'list';
                        $fieldLayout = $config['layout'] ?? 'full';
                        $layoutClass = match($fieldLayout) {
                            'half' => 'canvas-field-half',
                            'third' => 'canvas-field-third',
                            default => 'canvas-field-full',
                        };
                    @endphp

                    @if($type === 'section')
                        {{-- ━━━ SECTION ━━━ --}}
                        <div
                            draggable="true"
                            wire:click="selectField({{ (int) $f->id }})"
                            class="canvas-field-full canvas-section group px-4 py-4 {{ $isActive ? 'is-active' : '' }}"
                            :class="{ 'is-drop-target': dropTargetId === {{ (int) $f->id }} }"
                            @dragstart="dragFieldId = {{ (int) $f->id }}"
                            @dragend="dragFieldId = null; dropTargetId = null"
                            @dragover.prevent="if (dragFieldId && dragFieldId !== {{ (int) $f->id }}) dropTargetId = {{ (int) $f->id }}"
                            @dragleave="if (dropTargetId === {{ (int) $f->id }}) dropTargetId = null"
                            @drop.prevent="if (dragFieldId && dragFieldId !== {{ (int) $f->id }}) { $wire.reorderFormField(dragFieldId, {{ (int) $f->id }}); dragFieldId = null; dropTargetId = null; }"
                        >
                            <div class="flex items-center gap-3">
                                {{-- Drag handle --}}
                                <div class="canvas-drag-handle w-6 h-6 -ml-0.5" title="{{ __('forms_builder.drag_to_reorder') }}">
                                    <iconify-icon icon="solar:widget-6-bold" width="14"></iconify-icon>
                                </div>

                                {{-- Section content --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="w-1 h-5 rounded-full shrink-0 {{ $isActive ? 'bg-[var(--accent)]' : 'bg-slate-300' }} transition-colors"></span>
                                        <h3 class="text-sm font-bold text-slate-800 truncate">{{ $label }}</h3>
                                    </div>
                                    @if($help !== '')
                                        <p class="text-xs text-slate-500 mt-1 ml-3 leading-relaxed">{{ $help }}</p>
                                    @endif
                                </div>

                                {{-- Delete --}}
                                <div class="shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click.stop="deleteFormField({{ (int) $f->id }})"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50/80 transition-colors"
                                            aria-label="{{ __('forms_builder.delete') }}">
                                        <iconify-icon icon="solar:trash-bin-trash-linear" width="14"></iconify-icon>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- ━━━ REGULAR FIELD ━━━ --}}
                        <div
                            draggable="true"
                            wire:click="selectField({{ (int) $f->id }})"
                            class="{{ $layoutClass }} canvas-card group p-4 {{ $isActive ? 'is-active' : '' }}"
                            :class="{ 'is-drop-target': dropTargetId === {{ (int) $f->id }} }"
                            @dragstart="dragFieldId = {{ (int) $f->id }}"
                            @dragend="dragFieldId = null; dropTargetId = null"
                            @dragover.prevent="if (dragFieldId && dragFieldId !== {{ (int) $f->id }}) dropTargetId = {{ (int) $f->id }}"
                            @dragleave="if (dropTargetId === {{ (int) $f->id }}) dropTargetId = null"
                            @drop.prevent="if (dragFieldId && dragFieldId !== {{ (int) $f->id }}) { $wire.reorderFormField(dragFieldId, {{ (int) $f->id }}); dragFieldId = null; dropTargetId = null; }"
                        >
                            {{-- Top bar: drag handle + type badge + actions --}}
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="canvas-drag-handle w-5 h-5" title="{{ __('forms_builder.drag_to_reorder') }}">
                                        <iconify-icon icon="solar:widget-6-bold" width="12"></iconify-icon>
                                    </div>
                                    <span class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">
                                        @switch($type)
                                            @case('text') Texte @break
                                            @case('textarea') Zone texte @break
                                            @case('number') Nombre @break
                                            @case('select') Liste @break
                                            @case('radio') Choix unique @break
                                            @case('checkbox') Cases @break
                                            @case('date') Date @break
                                            @case('datetime') Date/Heure @break
                                            @case('file') Fichier @break
                                            @default {{ $type }} @break
                                        @endswitch
                                    </span>
                                </div>
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click.stop="deleteFormField({{ (int) $f->id }})"
                                            class="p-1 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50/80 transition-colors"
                                            aria-label="{{ __('forms_builder.delete') }}">
                                        <iconify-icon icon="solar:trash-bin-trash-linear" width="13"></iconify-icon>
                                    </button>
                                </div>
                            </div>

                            {{-- Label --}}
                            <label class="block text-sm font-semibold text-slate-700 mb-2.5">
                                {{ $label }}
                                @if($isRequired)
                                    <span class="text-red-400 text-xs font-normal ml-0.5">*</span>
                                @endif
                            </label>

                            {{-- Field preview --}}
                            @if ($type === 'select')
                                <div class="canvas-input flex justify-between items-center pointer-events-none">
                                    <span>{{ $placeholder !== '' ? $placeholder : __('forms_builder.select_option') }}</span>
                                    <iconify-icon icon="solar:alt-arrow-down-linear" width="16" class="text-slate-400"></iconify-icon>
                                </div>
                            @elseif ($type === 'radio')
                                @php $radioOpts = (array) $options; $showOpts = array_slice($radioOpts, 0, 5); @endphp
                                <div class="pointer-events-none
                                    {{ $displayMode === 'inline' ? 'flex flex-wrap gap-2.5' : '' }}
                                    {{ $displayMode === 'grid' ? 'grid grid-cols-2 gap-2.5' : '' }}
                                    {{ $displayMode === 'card' ? 'grid grid-cols-1 gap-2.5' : '' }}
                                    {{ $displayMode === 'list' ? 'space-y-2' : '' }}
                                ">
                                    @foreach($showOpts as $idx => $opt)
                                        @if($displayMode === 'card')
                                            <div class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl border {{ $idx === 0 ? 'border-[var(--accent)]/40 bg-[var(--accent-soft)]/10' : 'border-slate-200 bg-white' }} transition-all">
                                                <span class="shrink-0 w-[20px] h-[20px] rounded-full border-2 flex items-center justify-center {{ $idx === 0 ? 'border-[var(--accent)]' : 'border-slate-300' }}">
                                                    @if($idx === 0)
                                                        <span class="w-2.5 h-2.5 rounded-full bg-[var(--accent)]"></span>
                                                    @endif
                                                </span>
                                                <span class="text-sm font-medium {{ $idx === 0 ? 'text-slate-900' : 'text-slate-600' }}">{{ $opt }}</span>
                                            </div>
                                        @elseif($displayMode === 'inline')
                                            <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg border {{ $idx === 0 ? 'border-[var(--accent)]/40 bg-[var(--accent-soft)]/10' : 'border-slate-200 bg-white' }} transition-all">
                                                <span class="shrink-0 w-[16px] h-[16px] rounded-full border-2 flex items-center justify-center {{ $idx === 0 ? 'border-[var(--accent)]' : 'border-slate-300' }}">
                                                    @if($idx === 0)
                                                        <span class="w-2 h-2 rounded-full bg-[var(--accent)]"></span>
                                                    @endif
                                                </span>
                                                <span class="text-xs font-medium {{ $idx === 0 ? 'text-slate-900' : 'text-slate-600' }}">{{ $opt }}</span>
                                            </div>
                                        @elseif($displayMode === 'grid')
                                            <div class="flex items-center gap-3 px-3.5 py-3 rounded-xl border {{ $idx === 0 ? 'border-[var(--accent)]/40 bg-[var(--accent-soft)]/10' : 'border-slate-200 bg-white' }} transition-all">
                                                <span class="shrink-0 w-[18px] h-[18px] rounded-full border-2 flex items-center justify-center {{ $idx === 0 ? 'border-[var(--accent)]' : 'border-slate-300' }}">
                                                    @if($idx === 0)
                                                        <span class="w-2 h-2 rounded-full bg-[var(--accent)]"></span>
                                                    @endif
                                                </span>
                                                <span class="text-sm font-medium {{ $idx === 0 ? 'text-slate-900' : 'text-slate-600' }}">{{ $opt }}</span>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg border {{ $idx === 0 ? 'border-[var(--accent)]/40 bg-[var(--accent-soft)]/8' : 'border-slate-200/80 bg-white' }} transition-all">
                                                <span class="shrink-0 w-[18px] h-[18px] rounded-full border-2 flex items-center justify-center {{ $idx === 0 ? 'border-[var(--accent)]' : 'border-slate-300' }}">
                                                    @if($idx === 0)
                                                        <span class="w-2 h-2 rounded-full bg-[var(--accent)]"></span>
                                                    @endif
                                                </span>
                                                <span class="text-sm font-medium {{ $idx === 0 ? 'text-slate-900' : 'text-slate-600' }}">{{ $opt }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                    @if(count($radioOpts) > 5)
                                        <span class="text-xs text-slate-400 pl-1">+{{ count($radioOpts) - 5 }} {{ __('forms_builder.other_options') }}</span>
                                    @endif
                                </div>
                            @elseif ($type === 'textarea')
                                <div class="canvas-input h-20 pointer-events-none resize-none">
                                    {{ $placeholder !== '' ? $placeholder : __('forms_builder.text_area_placeholder') }}
                                </div>
                            @elseif ($type === 'checkbox')
                                @php $cbOpts = count((array) $options) > 0 ? (array) $options : [$label]; $showCbOpts = array_slice($cbOpts, 0, 5); @endphp
                                <div class="pointer-events-none
                                    {{ $displayMode === 'inline' ? 'flex flex-wrap gap-2.5' : '' }}
                                    {{ $displayMode === 'grid' ? 'grid grid-cols-2 gap-2.5' : '' }}
                                    {{ $displayMode === 'card' ? 'grid grid-cols-1 gap-2.5' : '' }}
                                    {{ $displayMode === 'list' ? 'space-y-2' : '' }}
                                ">
                                    @foreach($showCbOpts as $idx => $opt)
                                        @if($displayMode === 'card')
                                            <div class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl border {{ $idx === 0 ? 'border-[var(--accent)]/40 bg-[var(--accent-soft)]/10' : 'border-slate-200 bg-white' }} transition-all">
                                                <span class="shrink-0 w-[20px] h-[20px] rounded flex items-center justify-center border-2 {{ $idx === 0 ? 'border-[var(--accent)] bg-[var(--accent)]' : 'border-slate-300 bg-white' }}">
                                                    @if($idx === 0)
                                                        <iconify-icon icon="solar:check-read-linear" width="13" class="text-white"></iconify-icon>
                                                    @endif
                                                </span>
                                                <span class="text-sm font-medium {{ $idx === 0 ? 'text-slate-900' : 'text-slate-600' }}">{{ $opt }}</span>
                                            </div>
                                        @elseif($displayMode === 'inline')
                                            <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg border {{ $idx === 0 ? 'border-[var(--accent)]/40 bg-[var(--accent-soft)]/10' : 'border-slate-200 bg-white' }} transition-all">
                                                <span class="shrink-0 w-[16px] h-[16px] rounded flex items-center justify-center border-2 {{ $idx === 0 ? 'border-[var(--accent)] bg-[var(--accent)]' : 'border-slate-300 bg-white' }}">
                                                    @if($idx === 0)
                                                        <iconify-icon icon="solar:check-read-linear" width="11" class="text-white"></iconify-icon>
                                                    @endif
                                                </span>
                                                <span class="text-xs font-medium {{ $idx === 0 ? 'text-slate-900' : 'text-slate-600' }}">{{ $opt }}</span>
                                            </div>
                                        @elseif($displayMode === 'grid')
                                            <div class="flex items-center gap-3 px-3.5 py-3 rounded-xl border {{ $idx === 0 ? 'border-[var(--accent)]/40 bg-[var(--accent-soft)]/10' : 'border-slate-200 bg-white' }} transition-all">
                                                <span class="shrink-0 w-[18px] h-[18px] rounded flex items-center justify-center border-2 {{ $idx === 0 ? 'border-[var(--accent)] bg-[var(--accent)]' : 'border-slate-300 bg-white' }}">
                                                    @if($idx === 0)
                                                        <iconify-icon icon="solar:check-read-linear" width="12" class="text-white"></iconify-icon>
                                                    @endif
                                                </span>
                                                <span class="text-sm font-medium {{ $idx === 0 ? 'text-slate-900' : 'text-slate-600' }}">{{ $opt }}</span>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg border {{ $idx === 0 ? 'border-[var(--accent)]/40 bg-[var(--accent-soft)]/8' : 'border-slate-200/80 bg-white' }} transition-all">
                                                <span class="shrink-0 w-[18px] h-[18px] rounded flex items-center justify-center border-2 {{ $idx === 0 ? 'border-[var(--accent)] bg-[var(--accent)]' : 'border-slate-300 bg-white' }}">
                                                    @if($idx === 0)
                                                        <iconify-icon icon="solar:check-read-linear" width="12" class="text-white"></iconify-icon>
                                                    @endif
                                                </span>
                                                <span class="text-sm font-medium {{ $idx === 0 ? 'text-slate-900' : 'text-slate-600' }}">{{ $opt }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                    @if(count($cbOpts) > 5)
                                        <span class="text-xs text-slate-400 pl-1">+{{ count($cbOpts) - 5 }} {{ __('forms_builder.other_options') }}</span>
                                    @endif
                                </div>
                            @elseif ($type === 'file')
                                <div class="rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/50 p-5 text-center pointer-events-none">
                                    <iconify-icon icon="solar:cloud-upload-linear" width="22" class="text-slate-400"></iconify-icon>
                                    <p class="text-xs text-slate-500 mt-1.5 font-medium">{{ __('forms_builder.drag_or_click') }}</p>
                                </div>
                            @elseif ($type === 'date')
                                <div class="canvas-input flex items-center justify-between pointer-events-none">
                                    <span>{{ __('forms_builder.date_placeholder') }}</span>
                                    <iconify-icon icon="solar:calendar-linear" width="16" class="text-slate-400"></iconify-icon>
                                </div>
                            @elseif ($type === 'datetime')
                                <div class="canvas-input flex items-center justify-between pointer-events-none">
                                    <span>{{ __('forms_builder.datetime_placeholder') }}</span>
                                    <iconify-icon icon="solar:calendar-date-linear" width="16" class="text-slate-400"></iconify-icon>
                                </div>
                            @elseif ($type === 'number')
                                <div class="canvas-input pointer-events-none">
                                    {{ $placeholder !== '' ? $placeholder : '0' }}
                                </div>
                            @else
                                <div class="canvas-input pointer-events-none">
                                    {{ $placeholder !== '' ? $placeholder : __('forms_builder.short_text_placeholder') }}
                                </div>
                            @endif

                            {{-- Help text --}}
                            @if($help !== '')
                                <p class="mt-2 text-xs text-slate-400 leading-relaxed">{{ $help }}</p>
                            @endif
                        </div>
                    @endif
                @empty
                    {{-- No fields --}}
                @endforelse
            </div>

            {{-- Empty state --}}
            @if($allFields->isEmpty())
                <div class="flex flex-col items-center justify-center text-center py-20 px-8 rounded-2xl border-2 border-dashed border-slate-200 bg-white/50">
                    <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center mb-5">
                        <iconify-icon icon="solar:clipboard-add-linear" class="text-slate-300" width="32"></iconify-icon>
                    </div>
                    <h3 class="text-base font-semibold text-slate-800">{{ __('forms_builder.form_empty') }}</h3>
                    <p class="text-sm text-slate-500 mt-2 max-w-xs leading-relaxed">{{ __('forms_builder.form_empty_hint') }}</p>
                    <p class="text-xs text-slate-400 mt-4">{{ __('forms_builder.click_to_add') }}</p>
                </div>
            @endif
        @else
            {{-- No form selected --}}
            <div class="flex flex-col items-center justify-center text-center py-24 px-8">
                <div class="w-20 h-20 rounded-2xl bg-slate-50 flex items-center justify-center mb-6">
                    <iconify-icon icon="solar:document-add-bold-duotone" class="text-slate-300" width="40"></iconify-icon>
                </div>
                <h3 class="text-lg font-bold text-slate-800">{{ __('forms_builder.no_form_selected') }}</h3>
                <p class="text-sm text-slate-500 mt-2 max-w-sm leading-relaxed">{{ __('forms_builder.no_form_selected_hint') }}</p>
            </div>
        @endif
    </div>
</div>
