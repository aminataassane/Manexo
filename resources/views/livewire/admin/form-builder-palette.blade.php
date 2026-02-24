@php
    $fieldGroups = [
        [
            'label' => __('forms_builder.group_text'),
            'icon' => 'solar:text-bold',
            'types' => [
                ['type' => 'text', 'label' => __('forms_builder.field_short_text'), 'icon' => 'solar:text-field-linear'],
                ['type' => 'textarea', 'label' => __('forms_builder.field_paragraph'), 'icon' => 'solar:text-square-linear'],
                ['type' => 'email', 'label' => __('forms_builder.field_email'), 'icon' => 'solar:letter-linear'],
            ],
        ],
        [
            'label' => __('forms_builder.group_choice'),
            'icon' => 'solar:list-check-bold',
            'types' => [
                ['type' => 'select', 'label' => __('forms_builder.field_dropdown'), 'icon' => 'solar:list-arrow-down-linear'],
                ['type' => 'radio', 'label' => __('forms_builder.field_single_choice'), 'icon' => 'solar:record-circle-linear'],
                ['type' => 'checkbox', 'label' => __('forms_builder.field_checkbox'), 'icon' => 'solar:check-square-linear'],
            ],
        ],
        [
            'label' => __('forms_builder.group_date_number'),
            'icon' => 'solar:calendar-bold',
            'types' => [
                ['type' => 'date', 'label' => __('forms_builder.field_date'), 'icon' => 'solar:calendar-linear'],
                ['type' => 'datetime', 'label' => __('forms_builder.field_datetime'), 'icon' => 'solar:calendar-date-linear'],
                ['type' => 'number', 'label' => __('forms_builder.field_number'), 'icon' => 'solar:ruler-linear'],
            ],
        ],
        [
            'label' => __('forms_builder.group_other'),
            'icon' => 'solar:widget-2-bold',
            'types' => [
                ['type' => 'file', 'label' => __('forms_builder.field_file'), 'icon' => 'solar:upload-linear'],
                ['type' => 'section', 'label' => __('forms_builder.field_section'), 'icon' => 'solar:minus-circle-linear'],
            ],
        ],
    ];
@endphp

{{-- Desktop: vertical sidebar --}}
<div class="hidden lg:flex flex-col h-full" x-data="{ paletteSearch: '' }">
    <!-- Search -->
    <div class="px-4 pt-5 pb-4">
        <div class="relative">
            <iconify-icon icon="solar:magnifer-linear" width="15" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></iconify-icon>
            <input type="text" x-model="paletteSearch"
                   placeholder="{{ __('forms_builder.search_fields') }}"
                   class="input-builder w-full text-xs py-2 pl-9 pr-3">
        </div>
    </div>

    <!-- Grouped field types -->
    <div class="flex-1 overflow-y-auto custom-scrollbar px-3 pb-4 space-y-4">
        @foreach($fieldGroups as $group)
            <div x-show="!paletteSearch || {{ json_encode(collect($group['types'])->pluck('label')->join(' ')) }}.toLowerCase().includes(paletteSearch.toLowerCase())">
                <h4 class="text-[10px] font-bold uppercase tracking-widest text-slate-400/80 mb-2 px-1.5">
                    {{ $group['label'] }}
                </h4>
                <div class="space-y-0.5">
                    @foreach($group['types'] as $ft)
                        <button
                            type="button"
                            wire:click="quickAddField('{{ $ft['type'] }}')"
                            @disabled(! $canManageForms || ! $fb_selected_form_id)
                            x-show="!paletteSearch || '{{ strtolower($ft['label']) }}'.includes(paletteSearch.toLowerCase())"
                            class="w-full flex items-center gap-3 px-2.5 py-2 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all text-xs font-medium disabled:opacity-40 disabled:cursor-not-allowed group"
                        >
                            <div class="w-8 h-8 rounded-lg bg-slate-50 group-hover:bg-[var(--accent-soft)] flex items-center justify-center transition-colors shrink-0">
                                <iconify-icon icon="{{ $ft['icon'] }}" width="16" class="text-slate-400 group-hover:text-[var(--accent)] transition-colors"></iconify-icon>
                            </div>
                            <span>{{ $ft['label'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Mobile: FAB + Bottom drawer --}}
<div class="lg:hidden" x-data="{ paletteOpen: false, mobileSearch: '' }">
    <!-- FAB button -->
    <button type="button"
            @click="paletteOpen = true"
            @disabled(! $canManageForms || ! $fb_selected_form_id)
            class="fixed bottom-6 right-6 z-30 h-14 w-14 rounded-2xl text-white shadow-lg shadow-black/15 flex items-center justify-center transition-all hover:scale-105 active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed"
            style="background: var(--accent);">
        <iconify-icon icon="solar:add-circle-bold" width="26"></iconify-icon>
    </button>

    <!-- Bottom drawer -->
    <div x-show="paletteOpen" x-cloak class="fixed inset-0 z-50" style="display:none;">
        <div class="absolute inset-0 bg-black/25 backdrop-blur-[2px]" @click="paletteOpen = false"></div>
        <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-2xl shadow-xl border-t border-slate-200/60 max-h-[70vh] flex flex-col"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full">
            <!-- Handle -->
            <div class="flex justify-center pt-3 pb-2">
                <div class="w-10 h-1 rounded-full bg-slate-300"></div>
            </div>
            <!-- Search -->
            <div class="px-4 pb-3">
                <div class="relative">
                    <iconify-icon icon="solar:magnifer-linear" width="15" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></iconify-icon>
                    <input type="text" x-model="mobileSearch"
                           placeholder="{{ __('forms_builder.search_fields') }}"
                           class="input-builder w-full text-sm py-2.5 pl-9 pr-3">
                </div>
            </div>
            <!-- Types -->
            <div class="flex-1 overflow-y-auto px-4 pb-6 space-y-5">
                @foreach($fieldGroups as $group)
                    <div x-show="!mobileSearch || {{ json_encode(collect($group['types'])->pluck('label')->join(' ')) }}.toLowerCase().includes(mobileSearch.toLowerCase())">
                        <h4 class="text-[10px] font-bold uppercase tracking-widest text-slate-400/80 mb-2">
                            {{ $group['label'] }}
                        </h4>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($group['types'] as $ft)
                                <button
                                    type="button"
                                    wire:click="quickAddField('{{ $ft['type'] }}')"
                                    @click="paletteOpen = false"
                                    @disabled(! $canManageForms || ! $fb_selected_form_id)
                                    x-show="!mobileSearch || '{{ strtolower($ft['label']) }}'.includes(mobileSearch.toLowerCase())"
                                    class="flex items-center gap-2.5 px-3 py-3 rounded-xl bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-all text-xs font-medium disabled:opacity-40 disabled:cursor-not-allowed"
                                >
                                    <iconify-icon icon="{{ $ft['icon'] }}" width="18" class="text-slate-400"></iconify-icon>
                                    <span>{{ $ft['label'] }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
