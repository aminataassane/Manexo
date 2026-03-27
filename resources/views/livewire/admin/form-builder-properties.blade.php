<div class="space-y-0">
    @if ($fb_selected_field_id)
        <!-- FIELD PROPERTIES -->
        <div x-data="{ activeTab: 'field' }">
            {{-- Header --}}
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center">
                        <iconify-icon icon="solar:settings-linear" width="15" class="text-slate-300"></iconify-icon>
                    </div>
                    <div>
                        <h3 class="text-[13px] font-bold text-slate-900">{{ __('forms_builder.field_settings') }}</h3>
                        <span class="text-[10px] font-mono text-slate-300 tracking-wide">{{ $fb_selected_field_type }}</span>
                    </div>
                </div>
                <button type="button" wire:click="$set('fb_selected_field_id', null)"
                        class="p-1.5 rounded-xl text-slate-300 hover:text-slate-600 hover:bg-slate-50 transition-colors">
                    <iconify-icon icon="solar:close-circle-linear" width="17"></iconify-icon>
                </button>
            </div>

            {{-- Tabs: Field / Appearance --}}
            <div class="flex gap-0 mb-6 bg-slate-50/80 rounded-xl p-1">
                <button type="button" @click="activeTab = 'field'"
                        class="flex-1 py-2 text-[11px] font-semibold rounded-lg transition-all text-center"
                        :class="activeTab === 'field' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-400 hover:text-slate-600'">
                    {{ __('forms_builder.tab_field') }}
                </button>
                <button type="button" @click="activeTab = 'appearance'"
                        class="flex-1 py-2 text-[11px] font-semibold rounded-lg transition-all text-center"
                        :class="activeTab === 'appearance' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-400 hover:text-slate-600'">
                    {{ __('forms_builder.tab_appearance') }}
                </button>
            </div>

            {{-- Tab: Field settings --}}
            <div x-show="activeTab === 'field'" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="space-y-5">
                    <div class="space-y-2">
                        <label for="fb_selected_field_key" class="text-[11px] font-semibold text-slate-400">{{ __('forms_builder.key_name') }}</label>
                        <input
                            type="text"
                            id="fb_selected_field_key"
                            name="fb_selected_field_key"
                            wire:model.live.debounce.150ms="fb_selected_field_key"
                            @disabled(! $canManageForms)
                            class="input-builder font-mono text-xs"
                            placeholder="{{ __('forms_builder.key_placeholder') }}"
                        >
                        <div class="text-[10px] text-slate-300 leading-relaxed">{{ __('forms_builder.key_help') }}</div>
                        <x-input-error :messages="$errors->get('fb_selected_field_key')" />
                    </div>

                    <div class="space-y-2">
                        <label for="fb_selected_field_label" class="text-[11px] font-semibold text-slate-400">{{ __('forms_builder.label') }}</label>
                        <input type="text" id="fb_selected_field_label" name="fb_selected_field_label" wire:model.live.debounce.150ms="fb_selected_field_label" @disabled(! $canManageForms)
                               class="input-builder text-xs">
                        <x-input-error :messages="$errors->get('fb_selected_field_label')" />
                    </div>

                    @if(! in_array($fb_selected_field_type, ['section', 'checkbox']))
                    <div class="space-y-2">
                        <label for="fb_selected_field_placeholder" class="text-[11px] font-semibold text-slate-400">{{ __('forms_builder.placeholder_optional') }}</label>
                        <input
                            type="text"
                            id="fb_selected_field_placeholder"
                            name="fb_selected_field_placeholder"
                            wire:model.live.debounce.150ms="fb_selected_field_placeholder"
                            @disabled(! $canManageForms)
                            class="input-builder text-xs"
                            placeholder="{{ __('forms_builder.placeholder_example') }}"
                        >
                        <x-input-error :messages="$errors->get('fb_selected_field_placeholder')" />
                    </div>
                    @endif

                    <div class="space-y-2">
                        <label for="fb_selected_field_help_text" class="text-[11px] font-semibold text-slate-400">{{ $fb_selected_field_type === 'section' ? __('forms_builder.section_description') : __('forms_builder.help_text_optional') }}</label>
                        <textarea
                            id="fb_selected_field_help_text"
                            name="fb_selected_field_help_text"
                            wire:model.live.debounce.150ms="fb_selected_field_help_text"
                            rows="2"
                            @disabled(! $canManageForms)
                            class="input-builder text-xs"
                            placeholder="{{ __('forms_builder.help_text_example') }}"
                        ></textarea>
                        <x-input-error :messages="$errors->get('fb_selected_field_help_text')" />
                    </div>

                    @if (in_array($fb_selected_field_type, ['select', 'radio', 'checkbox']))
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between">
                                <label class="text-[11px] font-semibold text-slate-400">{{ __('forms_builder.options_list') }}</label>
                                <button type="button" wire:click="addOption" @disabled(! $canManageForms)
                                        class="text-[11px] font-semibold text-[var(--accent)] hover:opacity-80 disabled:opacity-50 flex items-center gap-1 transition-colors">
                                    <iconify-icon icon="solar:add-circle-bold" width="14"></iconify-icon>
                                    {{ __('forms_builder.add_option') }}
                                </button>
                            </div>
                            <div class="space-y-2 max-h-48 overflow-y-auto custom-scrollbar pr-1">
                                @forelse($fb_selected_field_options_list as $idx => $optionValue)
                                    <div class="flex items-center gap-2 group" wire:key="option-row-{{ $idx }}">
                                        <input type="text"
                                               id="fb_selected_field_option_{{ $idx }}"
                                               name="fb_selected_field_option_{{ $idx }}"
                                               wire:model.live.debounce.200ms="fb_selected_field_options_list.{{ $idx }}"
                                               @disabled(! $canManageForms)
                                               class="input-builder flex-1 py-2 text-xs"
                                               placeholder="{{ __('forms_builder.option_label', ['num' => $idx + 1]) }}">
                                        <button type="button" wire:click="removeOption({{ $idx }})" wire:key="opt-remove-{{ $idx }}"
                                                @disabled(! $canManageForms)
                                                class="shrink-0 p-1.5 rounded-lg text-slate-300 hover:text-red-500 hover:bg-red-50 transition-colors disabled:opacity-50"
                                                aria-label="{{ __('forms_builder.remove_option') }}">
                                            <iconify-icon icon="solar:trash-bin-trash-linear" width="14"></iconify-icon>
                                        </button>
                                    </div>
                                @empty
                                    <p class="text-[11px] text-slate-300 py-3">{{ __('forms_builder.no_options_yet') }}</p>
                                @endforelse
                            </div>
                            <x-input-error :messages="$errors->get('fb_selected_field_options_list')" />
                        </div>
                    @endif

                    @if($fb_selected_field_type !== 'section')
                    <div class="flex items-center justify-between py-3.5 px-4 rounded-xl bg-slate-50/60">
                        <span class="text-xs font-medium text-slate-600">{{ __('forms_builder.required') }}</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="fb_selected_field_required" name="fb_selected_field_required" wire:model="fb_selected_field_required" class="sr-only peer" @disabled(! $canManageForms)>
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[var(--accent)]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[var(--accent)]"></div>
                        </label>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Tab: Appearance / Layout --}}
            <div x-show="activeTab === 'appearance'" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="space-y-6">
                    @if($fb_selected_field_type !== 'section')
                    {{-- Layout selector --}}
                    <div class="space-y-3">
                        <label class="text-[11px] font-semibold text-slate-400">{{ __('forms_builder.layout') }}</label>
                        <div class="grid grid-cols-3 gap-2.5">
                            {{-- Full --}}
                            <button type="button" wire:click="$set('fb_selected_field_layout', 'full')"
                                    class="layout-btn {{ $fb_selected_field_layout === 'full' ? 'active' : '' }}">
                                <div class="w-full h-2.5 rounded-md {{ $fb_selected_field_layout === 'full' ? 'bg-[var(--accent)]' : 'bg-slate-200' }}"></div>
                                <span class="text-[10px]">{{ __('forms_builder.layout_full') }}</span>
                            </button>
                            {{-- Half --}}
                            <button type="button" wire:click="$set('fb_selected_field_layout', 'half')"
                                    class="layout-btn {{ $fb_selected_field_layout === 'half' ? 'active' : '' }}">
                                <div class="w-full flex gap-1.5">
                                    <div class="flex-1 h-2.5 rounded-md {{ $fb_selected_field_layout === 'half' ? 'bg-[var(--accent)]' : 'bg-slate-200' }}"></div>
                                    <div class="flex-1 h-2.5 rounded-md {{ $fb_selected_field_layout === 'half' ? 'bg-[var(--accent)]/40' : 'bg-slate-100' }}"></div>
                                </div>
                                <span class="text-[10px]">{{ __('forms_builder.layout_half') }}</span>
                            </button>
                            {{-- Third --}}
                            <button type="button" wire:click="$set('fb_selected_field_layout', 'third')"
                                    class="layout-btn {{ $fb_selected_field_layout === 'third' ? 'active' : '' }}">
                                <div class="w-full flex gap-1">
                                    <div class="flex-1 h-2.5 rounded-md {{ $fb_selected_field_layout === 'third' ? 'bg-[var(--accent)]' : 'bg-slate-200' }}"></div>
                                    <div class="flex-1 h-2.5 rounded-md {{ $fb_selected_field_layout === 'third' ? 'bg-[var(--accent)]/40' : 'bg-slate-150' }}"></div>
                                    <div class="flex-1 h-2.5 rounded-md {{ $fb_selected_field_layout === 'third' ? 'bg-[var(--accent)]/20' : 'bg-slate-100' }}"></div>
                                </div>
                                <span class="text-[10px]">{{ __('forms_builder.layout_third') }}</span>
                            </button>
                        </div>
                        <p class="text-[10px] text-slate-300 leading-relaxed">{{ __('forms_builder.layout_help') }}</p>
                    </div>

                    {{-- Display mode selector (radio/checkbox only) --}}
                    @if(in_array($fb_selected_field_type, ['radio', 'checkbox']))
                    <div class="space-y-3">
                        <label class="text-[11px] font-semibold text-slate-400">{{ __('forms_builder.display_mode') }}</label>
                        <div class="grid grid-cols-2 gap-2.5">
                            {{-- List --}}
                            <button type="button" wire:click="$set('fb_selected_field_display_mode', 'list')"
                                    class="layout-btn {{ $fb_selected_field_display_mode === 'list' ? 'active' : '' }}">
                                <div class="w-full space-y-1.5">
                                    <div class="h-1.5 rounded-md w-full {{ $fb_selected_field_display_mode === 'list' ? 'bg-[var(--accent)]' : 'bg-slate-200' }}"></div>
                                    <div class="h-1.5 rounded-md w-full {{ $fb_selected_field_display_mode === 'list' ? 'bg-[var(--accent)]/50' : 'bg-slate-150' }}"></div>
                                    <div class="h-1.5 rounded-md w-3/4 {{ $fb_selected_field_display_mode === 'list' ? 'bg-[var(--accent)]/30' : 'bg-slate-100' }}"></div>
                                </div>
                                <span class="text-[10px]">{{ __('forms_builder.display_mode_list') }}</span>
                            </button>
                            {{-- Inline --}}
                            <button type="button" wire:click="$set('fb_selected_field_display_mode', 'inline')"
                                    class="layout-btn {{ $fb_selected_field_display_mode === 'inline' ? 'active' : '' }}">
                                <div class="w-full flex gap-1.5">
                                    <div class="h-1.5 rounded-md flex-1 {{ $fb_selected_field_display_mode === 'inline' ? 'bg-[var(--accent)]' : 'bg-slate-200' }}"></div>
                                    <div class="h-1.5 rounded-md flex-1 {{ $fb_selected_field_display_mode === 'inline' ? 'bg-[var(--accent)]/50' : 'bg-slate-150' }}"></div>
                                    <div class="h-1.5 rounded-md flex-1 {{ $fb_selected_field_display_mode === 'inline' ? 'bg-[var(--accent)]/30' : 'bg-slate-100' }}"></div>
                                </div>
                                <span class="text-[10px]">{{ __('forms_builder.display_mode_inline') }}</span>
                            </button>
                            {{-- Grid --}}
                            <button type="button" wire:click="$set('fb_selected_field_display_mode', 'grid')"
                                    class="layout-btn {{ $fb_selected_field_display_mode === 'grid' ? 'active' : '' }}">
                                <div class="w-full grid grid-cols-2 gap-1">
                                    <div class="h-1.5 rounded-md {{ $fb_selected_field_display_mode === 'grid' ? 'bg-[var(--accent)]' : 'bg-slate-200' }}"></div>
                                    <div class="h-1.5 rounded-md {{ $fb_selected_field_display_mode === 'grid' ? 'bg-[var(--accent)]/50' : 'bg-slate-150' }}"></div>
                                    <div class="h-1.5 rounded-md {{ $fb_selected_field_display_mode === 'grid' ? 'bg-[var(--accent)]/30' : 'bg-slate-100' }}"></div>
                                    <div class="h-1.5 rounded-md {{ $fb_selected_field_display_mode === 'grid' ? 'bg-[var(--accent)]/20' : 'bg-slate-100' }}"></div>
                                </div>
                                <span class="text-[10px]">{{ __('forms_builder.display_mode_grid') }}</span>
                            </button>
                            {{-- Card --}}
                            <button type="button" wire:click="$set('fb_selected_field_display_mode', 'card')"
                                    class="layout-btn {{ $fb_selected_field_display_mode === 'card' ? 'active' : '' }}">
                                <div class="w-full space-y-1.5">
                                    <div class="h-3.5 rounded-lg {{ $fb_selected_field_display_mode === 'card' ? 'bg-[var(--accent)]/15 border border-[var(--accent)]/30' : 'bg-slate-50 border border-slate-200' }}"></div>
                                    <div class="h-3.5 rounded-lg {{ $fb_selected_field_display_mode === 'card' ? 'bg-[var(--accent)]/8 border border-[var(--accent)]/15' : 'bg-slate-50/50 border border-slate-150' }}"></div>
                                </div>
                                <span class="text-[10px]">{{ __('forms_builder.display_mode_card') }}</span>
                            </button>
                        </div>
                        <p class="text-[10px] text-slate-300 leading-relaxed">{{ __('forms_builder.display_mode_help') }}</p>
                    </div>
                    @endif
                    @else
                        <div class="text-center py-10">
                            <iconify-icon icon="solar:pallete-2-linear" width="30" class="text-slate-200"></iconify-icon>
                            <p class="text-xs text-slate-300 mt-3">Les sections occupent toujours la largeur complète.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="pt-6 mt-6 border-t border-slate-50 flex flex-col gap-2.5">
                <button type="button" wire:click="saveSelectedField" @disabled(! $canManageForms)
                        class="w-full px-4 py-3 text-xs font-bold text-white rounded-xl hover:opacity-90 transition-all disabled:opacity-40 shadow-sm"
                        style="background: var(--accent);">
                    {{ __('forms_builder.apply_changes') }}
                </button>
                <button type="button" @click="$dispatch('confirm-action', { title: '{{ __('Supprimer') }}', message: '{{ __('Supprimer ce champ du formulaire ?') }}', confirmLabel: '{{ __('Supprimer') }}', variant: 'danger', onConfirm: () => $wire.deleteFormField({{ (int) $fb_selected_field_id }}) })" @disabled(! $canManageForms)
                        class="w-full px-3 py-2.5 text-[11px] font-medium text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all disabled:opacity-40">
                    {{ __('forms_builder.delete_field') }}
                </button>
            </div>
        </div>
    @elseif ($fb_selected_form_id)
        <!-- FORM PROPERTIES (collapsible sections) -->
        <div x-data="{ sections: { general: true, public: false, danger: false } }">

            {{-- Header --}}
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-50">
                <div class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center">
                    <iconify-icon icon="solar:document-text-linear" width="15" class="text-slate-300"></iconify-icon>
                </div>
                <h3 class="text-[13px] font-bold text-slate-900">{{ __('forms_builder.form_settings') }}</h3>
            </div>

            <!-- GENERAL -->
            <div class="border-b border-slate-50">
                <button type="button" @click="sections.general = !sections.general"
                        class="w-full flex items-center justify-between py-3.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
                    <span class="flex items-center gap-2.5">
                        <iconify-icon icon="solar:settings-linear" width="15"></iconify-icon>
                        {{ __('forms_builder.general_section') }}
                    </span>
                    <iconify-icon :icon="sections.general ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear'" width="14" class="text-slate-300"></iconify-icon>
                </button>
            </div>
            <div x-show="sections.general" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="space-y-5 pt-5 pb-6">
                    <div class="space-y-2">
                        <label for="fb_selected_form_name" class="text-[11px] font-semibold text-slate-400">{{ __('forms_builder.name') }}</label>
                        <input type="text" id="fb_selected_form_name" name="fb_selected_form_name" wire:model="fb_selected_form_name" @disabled(! $canManageForms)
                               class="input-builder text-xs">
                        <x-input-error :messages="$errors->get('fb_selected_form_name')" />
                    </div>

                    <div class="space-y-2">
                        <label for="fb_selected_form_description" class="text-[11px] font-semibold text-slate-400">{{ __('forms_builder.description_optional') }}</label>
                        <textarea id="fb_selected_form_description" name="fb_selected_form_description" wire:model="fb_selected_form_description" rows="2" @disabled(! $canManageForms)
                                  class="input-builder text-xs"
                                  placeholder="{{ __('forms_builder.description_placeholder') }}"></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] font-semibold text-slate-400">{{ __('forms_builder.category') }}</label>
                        <x-select-input wire:model="fb_selected_form_category_id" :disabled="! $canManageForms">
                            <option value="">{{ __('forms_builder.all_categories') }}</option>
                            @foreach ($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </x-select-input>
                        <x-input-error :messages="$errors->get('fb_selected_form_category_id')" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] font-semibold text-slate-400">{{ __('forms_builder.target_who') }}</label>
                        <x-select-input wire:model="fb_selected_form_target_user_id" :disabled="! $canManageForms">
                            <option value="">{{ __('forms_builder.target_team') }}</option>
                            <optgroup label="{{ __('forms_builder.target_one_person') }}">
                            @foreach ($members as $m)
                                <option value="{{ $m->user_id }}">{{ $m->user?->name ?? '—' }}</option>
                            @endforeach
                            </optgroup>
                        </x-select-input>
                        <p class="text-[10px] text-slate-300 leading-relaxed">{{ __('forms_builder.target_help') }}</p>
                        <x-input-error :messages="$errors->get('fb_selected_form_target_user_id')" />
                    </div>

                    <div class="flex items-center justify-between py-3.5 px-4 rounded-xl bg-slate-50/60">
                        <div>
                            <div class="text-xs font-medium text-slate-700">{{ __('forms_builder.creates_ticket') }}</div>
                            <div class="text-[10px] text-slate-300 mt-0.5">{{ __('forms_builder.creates_ticket_help') }}</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0 ml-3">
                            <input type="checkbox" id="fb_selected_form_creates_ticket" name="fb_selected_form_creates_ticket" wire:model="fb_selected_form_creates_ticket" class="sr-only peer" @disabled(! $canManageForms)>
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[var(--accent)]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[var(--accent)]"></div>
                        </label>
                    </div>

                    <div class="space-y-2">
                        <label for="fb_selected_form_due_date" class="text-[11px] font-semibold text-slate-400">{{ __('forms_builder.due_date') }}</label>
                        <input type="date" id="fb_selected_form_due_date" name="fb_selected_form_due_date" wire:model="fb_selected_form_due_date" @disabled(! $canManageForms)
                               class="input-builder text-xs">
                        <p class="text-[10px] text-slate-300 leading-relaxed">{{ __('forms_builder.form_due_date_help') }}</p>
                        <x-input-error :messages="$errors->get('fb_selected_form_due_date')" />
                    </div>

                    <div class="space-y-2">
                        <label for="fb_selected_form_expires_at" class="text-[11px] font-semibold text-slate-400">{{ __('forms_builder.assign_expires_at') }}</label>
                        <input type="datetime-local" id="fb_selected_form_expires_at" name="fb_selected_form_expires_at" wire:model="fb_selected_form_expires_at" @disabled(! $canManageForms)
                               class="input-builder text-xs">
                        <p class="text-[10px] text-slate-300 leading-relaxed">{{ __('forms_builder.assign_expires_at_help') }}</p>
                        <x-input-error :messages="$errors->get('fb_selected_form_expires_at')" />
                    </div>

                    <div class="flex items-center justify-between py-3.5 px-4 rounded-xl bg-slate-50/60">
                        <span class="text-xs font-medium text-slate-600">{{ __('forms_builder.status') }}</span>
                        <div class="flex gap-2">
                            @if($fb_selected_form_status === 'published')
                                <span class="px-3 py-1 rounded-full bg-emerald-50 text-[10px] font-bold text-emerald-600">{{ __('forms_builder.published') }}</span>
                            @elseif($fb_selected_form_status === 'archived')
                                <span class="px-3 py-1 rounded-full bg-slate-100 text-[10px] font-bold text-slate-500">{{ __('forms_builder.archived') }}</span>
                            @else
                                <span class="px-3 py-1 rounded-full bg-amber-50 text-[10px] font-bold text-amber-600">{{ __('forms_builder.draft') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- PUBLIC ACCESS -->
            <div class="border-b border-slate-50">
                <button type="button" @click="sections.public = !sections.public"
                        class="w-full flex items-center justify-between py-3.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
                    <span class="flex items-center gap-2.5">
                        <iconify-icon icon="solar:global-linear" width="15"></iconify-icon>
                        {{ __('forms_builder.public_access_section') }}
                    </span>
                    <iconify-icon :icon="sections.public ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear'" width="14" class="text-slate-300"></iconify-icon>
                </button>
            </div>
            <div x-show="sections.public" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="space-y-5 pt-5 pb-6">
                    <div class="flex items-center justify-between py-3.5 px-4 rounded-xl bg-slate-50/60">
                        <div>
                            <div class="text-xs font-medium text-slate-700">{{ __('forms_builder.public_link') }}</div>
                            <div class="text-[10px] text-slate-300 mt-0.5">{{ __('forms_builder.public_link_help') }}</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0 ml-3">
                            <input type="checkbox" id="fb_selected_form_public" name="fb_selected_form_public" wire:model="fb_selected_form_public" class="sr-only peer" @disabled(! $canManageForms)>
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[var(--accent)]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[var(--accent)]"></div>
                        </label>
                    </div>

                    <div class="space-y-2">
                        <label for="fb_selected_form_slug" class="text-[11px] font-semibold text-slate-400">{{ __('forms_builder.public_slug') }}</label>
                        <div class="flex items-center gap-2">
                            <input type="text" id="fb_selected_form_slug" name="fb_selected_form_slug" wire:model="fb_selected_form_slug" @disabled(! $canManageForms)
                                   placeholder="{{ __('forms_builder.slug_placeholder') }}"
                                   class="input-builder text-xs">
                            <button type="button" wire:click="generatePublicSlug" @disabled(! $canManageForms)
                                    class="h-9 px-3 rounded-xl border border-slate-100 bg-slate-50 text-slate-600 text-[11px] font-semibold hover:bg-slate-100 transition disabled:opacity-50 shrink-0">
                                {{ __('forms_builder.generate') }}
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('fb_selected_form_slug')" />
                        @if($fb_selected_form_public && $fb_selected_form_slug)
                            <div class="mt-3 rounded-xl border border-slate-50 bg-slate-50/40 px-4 py-3">
                                <div class="text-[11px] font-semibold text-slate-600 flex items-center gap-1.5">
                                    <iconify-icon icon="solar:link-linear" width="13"></iconify-icon>
                                    {{ __('forms_builder.link') }}
                                </div>
                                <div class="mt-2 font-mono break-all text-[10px] text-slate-400">{{ url('/f/' . $fb_selected_form_slug) }}</div>
                            </div>
                            <a href="{{ url('/f/' . $fb_selected_form_slug) }}" target="_blank"
                               class="mt-2 inline-flex items-center gap-1.5 text-[11px] font-semibold text-[var(--accent)] hover:opacity-80">
                                <iconify-icon icon="solar:eye-linear" width="14"></iconify-icon>
                                {{ __('forms_builder.preview') }}
                            </a>
                            @php
                                $embedUrl = url('/f/' . $fb_selected_form_slug) . '?embed=1';
                                $embedCode = '<iframe src="' . e($embedUrl) . '" width="100%" height="600" frameborder="0" title="' . e($fb_selected_form_name ?: __('Formulaire')) . '"></iframe>';
                            @endphp
                            <div class="mt-4 rounded-xl border border-slate-50 bg-slate-50/30 overflow-hidden" x-data="{ copied: false, embedCode: @js($embedCode), copyLabel: @js(__('forms_builder.copy_embed')), copiedLabel: @js(__('forms_builder.embed_copied')) }">
                                <div class="px-4 py-3 border-b border-slate-50">
                                    <div class="text-[11px] font-semibold text-slate-600">{{ __('forms_builder.embed_iframe_title') }}</div>
                                    <p class="text-[10px] text-slate-300 mt-0.5">{{ __('forms_builder.embed_iframe_help') }}</p>
                                </div>
                                <div class="p-4 space-y-2.5">
                                    <label for="fb_embed_code" class="text-[10px] font-semibold text-slate-400">{{ __('forms_builder.embed_code') }}</label>
                                    <textarea id="fb_embed_code" name="fb_embed_code" readonly rows="3" class="input-builder text-[10px] font-mono text-slate-400" x-ref="embedTextarea">{{ $embedCode }}</textarea>
                                    <button type="button"
                                            @click="navigator.clipboard.writeText(embedCode); copied = true; setTimeout(() => copied = false, 2000)"
                                            class="inline-flex items-center gap-1.5 rounded-xl border border-slate-100 bg-white px-3 py-2 text-[11px] font-semibold text-slate-500 hover:bg-slate-50 transition">
                                        <iconify-icon icon="solar:copy-linear" width="13"></iconify-icon>
                                        <span x-text="copied ? copiedLabel : copyLabel"></span>
                                    </button>
                                </div>
                                <div class="px-4 py-3 border-t border-slate-50">
                                    <div class="text-[10px] font-semibold text-slate-400 mb-2.5">{{ __('forms_builder.embed_preview') }}</div>
                                    <iframe src="{{ $embedUrl }}" class="w-full rounded-xl border border-slate-50 bg-white" height="320" title="{{ $fb_selected_form_name ?: __('Formulaire') }}"></iframe>
                                </div>
                            </div>
                        @endif
                        @if(!$fb_selected_form_target_user_id && $fb_selected_form_slug)
                            <div class="mt-3 rounded-xl border border-[var(--accent)]/10 bg-[var(--accent-soft)]/5 px-4 py-3">
                                <div class="text-[11px] font-semibold text-slate-600">{{ __('forms_builder.team_link') }}</div>
                                <p class="mt-0.5 text-slate-300 text-[10px]">{{ __('forms_builder.team_link_help') }}</p>
                                <div class="mt-2 font-mono break-all text-[10px] text-[var(--accent)]">{{ url('/forms/l/' . $fb_selected_form_slug) }}</div>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-2">
                        <label for="fb_selected_form_public_title" class="text-[11px] font-semibold text-slate-400">{{ __('forms_builder.public_title_optional') }}</label>
                        <input type="text" id="fb_selected_form_public_title" name="fb_selected_form_public_title" wire:model="fb_selected_form_public_title" @disabled(! $canManageForms)
                               class="input-builder text-xs">
                    </div>

                    <div class="space-y-2">
                        <label for="fb_selected_form_public_description" class="text-[11px] font-semibold text-slate-400">{{ __('forms_builder.public_description_optional') }}</label>
                        <textarea id="fb_selected_form_public_description" name="fb_selected_form_public_description" wire:model="fb_selected_form_public_description" rows="3" @disabled(! $canManageForms)
                                  class="input-builder text-xs"></textarea>
                    </div>

                    <div class="space-y-2">
                        <label for="fb_selected_form_public_thank_you" class="text-[11px] font-semibold text-slate-400">{{ __('forms_builder.thank_you_optional') }}</label>
                        <textarea id="fb_selected_form_public_thank_you" name="fb_selected_form_public_thank_you" wire:model="fb_selected_form_public_thank_you" rows="3" @disabled(! $canManageForms)
                                  class="input-builder text-xs"
                                  placeholder="{{ __('forms_builder.thank_you_placeholder') }}"></textarea>
                    </div>
                </div>
            </div>

            <!-- DANGER ZONE -->
            <div class="border-b border-slate-50">
                <button type="button" @click="sections.danger = !sections.danger"
                        class="w-full flex items-center justify-between py-3.5 text-xs font-semibold text-red-300 hover:text-red-500 transition-colors">
                    <span class="flex items-center gap-2.5">
                        <iconify-icon icon="solar:danger-triangle-linear" width="15"></iconify-icon>
                        {{ __('forms_builder.danger_zone') }}
                    </span>
                    <iconify-icon :icon="sections.danger ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear'" width="14" class="text-red-200"></iconify-icon>
                </button>
            </div>
            <div x-show="sections.danger" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="space-y-2.5 pt-5 pb-6">
                    @if($fb_selected_form_status !== 'archived')
                        <button type="button" @click="$dispatch('confirm-action', { title: '{{ __('Archiver') }}', message: '{{ __('Archiver ce formulaire ? Il ne sera plus accessible.') }}', confirmLabel: '{{ __('Archiver') }}', variant: 'warning', onConfirm: () => $wire.archiveForm() })" @disabled(! $canManageForms)
                                class="w-full px-4 py-3 text-[11px] font-semibold text-slate-500 bg-slate-50/60 border border-slate-100 rounded-xl hover:bg-slate-100 transition-all disabled:opacity-50">
                            {{ __('forms_builder.archive') }}
                        </button>
                    @endif

                    <button type="button" @click="$dispatch('confirm-action', { title: 'Supprimer', message: '{{ __('forms_builder.delete_form_confirm') }}', confirmLabel: 'Supprimer', variant: 'danger', onConfirm: () => $wire.deleteForm({{ (int) $fb_selected_form_id }}) })" @disabled(! $canManageForms)
                            class="w-full px-4 py-3 text-[11px] font-semibold text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all disabled:opacity-50">
                        {{ __('forms_builder.delete_form') }}
                    </button>
                </div>
            </div>

        </div>
    @else
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center mb-5">
                <iconify-icon icon="solar:cursor-square-linear" class="text-slate-200" width="26"></iconify-icon>
            </div>
            <p class="text-[13px] font-semibold text-slate-500">{{ __('forms_builder.nothing_selected') }}</p>
            <p class="text-[11px] text-slate-300 mt-2 max-w-[200px] leading-relaxed">{{ __('forms_builder.nothing_selected_hint') }}</p>
        </div>
    @endif
</div>
