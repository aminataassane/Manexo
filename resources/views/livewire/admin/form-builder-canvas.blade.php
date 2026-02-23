<div class="bg-white rounded-2xl shadow-lg shadow-slate-200/50 border border-slate-200/80 flex-1 flex flex-col overflow-hidden min-h-0">
    <!-- Form Header Simulation -->
    <div class="px-6 sm:px-8 py-5 sm:py-6 border-b border-slate-100 bg-gradient-to-b from-slate-50/50 to-white shrink-0">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">{{ $fb_selected_form_name ?: __('forms_builder.new_form') }}</h2>
                <div class="flex items-center gap-2 mt-2 text-sm text-slate-500 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-xs font-medium text-slate-600">
                        {{ $categories->firstWhere('id', $fb_selected_form_category_id)?->name ?? __('forms_builder.all_categories_label') }}
                    </span>
                    <span class="text-slate-300">·</span>
                    <span class="text-slate-500">{{ $fb_selected_form_target_user_id ? __('forms_builder.reserved') : __('forms_builder.team') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Fields List (flat) - drag & drop to reorder -->
    <div class="flex-1 overflow-y-auto p-6 sm:p-8 bg-white min-h-0" x-data="{ dragFieldId: null, dropTargetId: null }">
        @php
            $selected = $selectedForm ?? ($fb_selected_form_id ? $forms->firstWhere('id', (int) $fb_selected_form_id) : null);
            $allFields = $selected?->fields ?? collect();
        @endphp

        @if ($selected)
            {{-- Drop zone: move to top --}}
            @if($allFields->isNotEmpty())
                <div class="rounded-lg border-2 border-dashed transition-colors -mb-1 min-h-[8px]"
                     :class="{ 'min-h-[40px] border-[var(--accent)] bg-[var(--accent-soft)]/25': dragFieldId && dropTargetId === 'top', 'border-transparent': !dragFieldId || dropTargetId !== 'top' }"
                     @dragover.prevent="if (dragFieldId) dropTargetId = 'top'"
                     @dragleave="dropTargetId = null"
                     @drop.prevent="if (dragFieldId) { $wire.reorderFormField(dragFieldId, 0); dragFieldId = null; dropTargetId = null; }"
                ></div>
            @endif
            <div class="space-y-4">
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
                @endphp

                @if($type === 'section')
                    {{-- Section divider --}}
                    <div
                        draggable="true"
                        wire:click="selectField({{ (int) $f->id }})"
                        class="group relative rounded-xl border-l-4 transition-all cursor-grab active:cursor-grabbing px-5 py-4 {{ $isActive ? 'border-l-[var(--accent)] bg-[var(--accent-soft)]/15 ring-2 ring-[var(--accent)]/20 shadow-sm' : 'border-l-slate-200 bg-slate-50/80 hover:border-l-slate-300 hover:bg-slate-50' }}"
                        :class="{ 'ring-2 ring-[var(--accent)] bg-[var(--accent-soft)]/20': dropTargetId === {{ (int) $f->id }} }"
                        @dragstart="dragFieldId = {{ (int) $f->id }}"
                        @dragend="dragFieldId = null; dropTargetId = null"
                        @dragover.prevent="if (dragFieldId && dragFieldId !== {{ (int) $f->id }}) dropTargetId = {{ (int) $f->id }}"
                        @dragleave="if (dropTargetId === {{ (int) $f->id }}) dropTargetId = null"
                        @drop.prevent="if (dragFieldId && dragFieldId !== {{ (int) $f->id }}) { $wire.reorderFormField(dragFieldId, {{ (int) $f->id }}); dragFieldId = null; dropTargetId = null; }"
                    >
                        <div class="absolute left-1 top-1/2 -translate-y-1/2 text-slate-400 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                            <iconify-icon icon="solar:hamburger-menu-linear" width="16"></iconify-icon>
                        </div>
                        <div class="absolute right-3 top-3 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button wire:click.stop="deleteFormField({{ (int) $f->id }})" class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors" aria-label="{{ __('forms_builder.delete') }}">
                                <iconify-icon icon="solar:trash-bin-trash-linear" width="16"></iconify-icon>
                            </button>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-1 rounded-full shrink-0 {{ $isActive ? 'bg-[var(--accent)]' : 'bg-slate-300' }}"></div>
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-slate-900">{{ $label }}</h3>
                                @if($help !== '')
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $help }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
                @if($type !== 'section')
                    {{-- Regular field --}}
                    <div
                        draggable="true"
                        wire:click="selectField({{ (int) $f->id }})"
                        class="group relative p-4 sm:p-5 rounded-xl border-l-4 transition-all cursor-grab active:cursor-grabbing {{ $isActive ? 'border-l-[var(--accent)] bg-white ring-2 ring-[var(--accent)]/20 shadow-md shadow-slate-200/50' : 'border-l-transparent bg-white hover:border-l-slate-200 hover:bg-slate-50/70 border border-slate-100 hover:border-slate-200' }}"
                        :class="{ 'ring-2 ring-[var(--accent)] bg-[var(--accent-soft)]/20': dropTargetId === {{ (int) $f->id }} }"
                        @dragstart="dragFieldId = {{ (int) $f->id }}"
                        @dragend="dragFieldId = null; dropTargetId = null"
                        @dragover.prevent="if (dragFieldId && dragFieldId !== {{ (int) $f->id }}) dropTargetId = {{ (int) $f->id }}"
                        @dragleave="if (dropTargetId === {{ (int) $f->id }}) dropTargetId = null"
                        @drop.prevent="if (dragFieldId && dragFieldId !== {{ (int) $f->id }}) { $wire.reorderFormField(dragFieldId, {{ (int) $f->id }}); dragFieldId = null; dropTargetId = null; }"
                    >
                        <div class="absolute left-1 top-6 text-slate-400 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                            <iconify-icon icon="solar:hamburger-menu-linear" width="16"></iconify-icon>
                        </div>
                        <div class="absolute right-3 top-3 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button wire:click.stop="deleteFormField({{ (int) $f->id }})" class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors" aria-label="{{ __('forms_builder.delete') }}">
                                <iconify-icon icon="solar:trash-bin-trash-linear" width="16"></iconify-icon>
                            </button>
                        </div>

                        <label class="block text-sm font-semibold text-slate-900 mb-2 pr-12">
                            {{ $label }} @if($isRequired) <span class="text-red-500">*</span> @endif
                            <span class="ml-2 text-[10px] font-mono text-slate-400">{{ $type }}</span>
                        </label>

                        @if ($type === 'select')
                            <div class="relative pointer-events-none">
                                <div class="block w-full rounded-lg border border-slate-200 bg-white py-2.5 px-3 text-slate-500 shadow-sm text-sm flex justify-between items-center">
                                    {{ $placeholder !== '' ? $placeholder : __('forms_builder.select_option') }}
                                    <iconify-icon icon="solar:alt-arrow-down-linear" class="text-slate-400"></iconify-icon>
                                </div>
                            </div>
                        @elseif ($type === 'radio')
                            <div class="space-y-2 pointer-events-none">
                                @foreach(array_slice((array) $options, 0, 3) as $opt)
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex h-4 w-4 items-center justify-center rounded-full border border-slate-300 bg-white"></span>
                                        <span class="text-sm text-slate-700">{{ $opt }}</span>
                                    </div>
                                @endforeach
                                @if(count((array) $options) > 3)
                                    <span class="text-xs text-slate-400">+{{ count((array) $options) - 3 }} {{ __('forms_builder.other_options') }}</span>
                                @endif
                            </div>
                        @elseif ($type === 'textarea')
                            <div class="block w-full rounded-lg border border-slate-200 bg-white py-3 px-3 text-slate-400 shadow-sm text-sm h-24 pointer-events-none">
                                {{ $placeholder !== '' ? $placeholder : __('forms_builder.text_area_placeholder') }}
                            </div>
                        @elseif ($type === 'checkbox')
                            <div class="space-y-2 pointer-events-none">
                                @if(count((array) $options) > 0)
                                    @foreach((array) $options as $opt)
                                        <div class="flex items-center gap-3 p-2 rounded-lg border border-slate-200 bg-white">
                                            <span class="inline-flex h-5 w-5 items-center justify-center rounded border border-slate-300 bg-white shrink-0"></span>
                                            <span class="text-sm text-slate-700">{{ $opt }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 bg-white">
                                        <span class="inline-flex h-5 w-5 items-center justify-center rounded border border-slate-300 bg-white"></span>
                                        <span class="text-sm text-slate-700">{{ $label }}</span>
                                    </div>
                                @endif
                            </div>
                        @elseif ($type === 'file')
                            <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-4 text-center pointer-events-none">
                                <iconify-icon icon="solar:upload-linear" width="20" class="text-slate-400"></iconify-icon>
                                <p class="text-xs text-slate-500 mt-1">{{ __('forms_builder.drag_or_click') }}</p>
                            </div>
                        @elseif ($type === 'date')
                            <div class="block w-full rounded-lg border border-slate-200 bg-white py-2.5 px-3 text-slate-400 shadow-sm text-sm pointer-events-none">
                                {{ __('forms_builder.date_placeholder') }}
                            </div>
                        @elseif ($type === 'datetime')
                            <div class="block w-full rounded-lg border border-slate-200 bg-white py-2.5 px-3 text-slate-400 shadow-sm text-sm pointer-events-none">
                                {{ __('forms_builder.datetime_placeholder') }}
                            </div>
                        @else
                            <div class="block w-full rounded-lg border border-slate-200 bg-white py-2.5 px-3 text-slate-400 shadow-sm text-sm pointer-events-none">
                                {{ $placeholder !== '' ? $placeholder : __('forms_builder.short_text_placeholder') }}
                            </div>
                        @endif

                        @if($help !== '')
                            <div class="mt-2 text-[11px] text-slate-500">{{ $help }}</div>
                        @endif
                    </div>
                @endif
            @empty
                {{-- No fields --}}
            @endforelse
            </div>
            @if($allFields->isEmpty())
                <div class="flex flex-col items-center justify-center text-center py-16 px-8 rounded-2xl border-2 border-dashed border-slate-200 bg-gradient-to-b from-slate-50/80 to-white">
                    <div class="w-20 h-20 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-center mb-5">
                        <iconify-icon icon="solar:clipboard-add-linear" class="text-slate-400" width="40"></iconify-icon>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">{{ __('forms_builder.form_empty') }}</h3>
                    <p class="text-sm text-slate-500 mt-2 max-w-sm">{{ __('forms_builder.form_empty_hint') }}</p>
                    <p class="text-xs text-slate-400 mt-3">{{ __('forms_builder.click_to_add') }}</p>
                </div>
            @endif
        @else
            <div class="flex flex-col items-center justify-center text-center py-20 px-8">
                <div class="w-24 h-24 rounded-2xl bg-slate-100 flex items-center justify-center mb-6 shadow-inner">
                    <iconify-icon icon="solar:document-add-bold-duotone" class="text-slate-400" width="48"></iconify-icon>
                </div>
                <h3 class="text-lg font-bold text-slate-900">{{ __('forms_builder.no_form_selected') }}</h3>
                <p class="text-sm text-slate-500 mt-2 max-w-sm">{{ __('forms_builder.no_form_selected_hint') }}</p>
            </div>
        @endif
    </div>
</div>
