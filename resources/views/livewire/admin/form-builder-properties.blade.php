<div class="space-y-6">
    @if ($fb_selected_field_id)
        <!-- FIELD PROPERTIES -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('forms_builder.selected_field') }}</h3>
                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-[10px] font-mono text-slate-600">{{ $fb_selected_field_type }}</span>
            </div>

            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label class="text-[11px] font-medium text-slate-700">{{ __('forms_builder.key_name') }}</label>
                    <input
                        type="text"
                        wire:model.live.debounce.150ms="fb_selected_field_key"
                        @disabled(! $canManageForms)
                        class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50 font-mono"
                        placeholder="{{ __('forms_builder.key_placeholder') }}"
                    >
                    <div class="text-[10px] text-slate-500">{{ __('forms_builder.key_help') }}</div>
                    <x-input-error :messages="$errors->get('fb_selected_field_key')" />
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] font-medium text-slate-700">{{ __('forms_builder.label') }}</label>
                    <input type="text" wire:model.live.debounce.150ms="fb_selected_field_label" @disabled(! $canManageForms)
                           class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50">
                    <x-input-error :messages="$errors->get('fb_selected_field_label')" />
                </div>

                @if(! in_array($fb_selected_field_type, ['section', 'checkbox']))
                <div class="space-y-1.5">
                    <label class="text-[11px] font-medium text-slate-700">{{ __('forms_builder.placeholder_optional') }}</label>
                    <input
                        type="text"
                        wire:model.live.debounce.150ms="fb_selected_field_placeholder"
                        @disabled(! $canManageForms)
                        class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50"
                        placeholder="{{ __('forms_builder.placeholder_example') }}"
                    >
                    <x-input-error :messages="$errors->get('fb_selected_field_placeholder')" />
                </div>
                @endif

                <div class="space-y-1.5">
                    <label class="text-[11px] font-medium text-slate-700">{{ $fb_selected_field_type === 'section' ? __('forms_builder.section_description') : __('forms_builder.help_text_optional') }}</label>
                    <textarea
                        wire:model.live.debounce.150ms="fb_selected_field_help_text"
                        rows="2"
                        @disabled(! $canManageForms)
                        class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50"
                        placeholder="{{ __('forms_builder.help_text_example') }}"
                    ></textarea>
                    <x-input-error :messages="$errors->get('fb_selected_field_help_text')" />
                </div>

                @if (in_array($fb_selected_field_type, ['select', 'radio', 'checkbox']))
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-[11px] font-medium text-slate-700">{{ __('forms_builder.options_list') }}</label>
                            <button type="button" wire:click="addOption" @disabled(! $canManageForms)
                                    class="text-[11px] font-semibold text-[var(--accent)] hover:opacity-80 disabled:opacity-50 flex items-center gap-1">
                                <iconify-icon icon="solar:add-circle-bold" width="14"></iconify-icon>
                                {{ __('forms_builder.add_option') }}
                            </button>
                        </div>
                        <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                            @forelse($fb_selected_field_options_list as $idx => $optionValue)
                                <div class="flex items-center gap-2 group" wire:key="option-row-{{ $idx }}">
                                    <input type="text"
                                           wire:model.live.debounce.200ms="fb_selected_field_options_list.{{ $idx }}"
                                           @disabled(! $canManageForms)
                                           class="flex-1 rounded-lg border-slate-200 bg-white py-2 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50"
                                           placeholder="{{ __('forms_builder.option_label', ['num' => $idx + 1]) }}">
                                    <button type="button" wire:click="removeOption({{ $idx }})" wire:key="opt-remove-{{ $idx }}"
                                            @disabled(! $canManageForms)
                                            class="shrink-0 p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors disabled:opacity-50"
                                            aria-label="{{ __('forms_builder.remove_option') }}">
                                        <iconify-icon icon="solar:trash-bin-trash-linear" width="18"></iconify-icon>
                                    </button>
                                </div>
                            @empty
                                <p class="text-xs text-slate-500 py-2">{{ __('forms_builder.no_options_yet') }}</p>
                            @endforelse
                        </div>
                        <x-input-error :messages="$errors->get('fb_selected_field_options_list')" />
                    </div>
                @endif

                @if($fb_selected_field_type !== 'section')
                <div class="flex items-center justify-between pt-2">
                    <span class="text-xs font-medium text-slate-700">{{ __('forms_builder.required') }}</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="fb_selected_field_required" class="sr-only peer" @disabled(! $canManageForms)>
                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[var(--accent)] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[var(--accent)]"></div>
                    </label>
                </div>
                @endif

                <div class="pt-4 border-t border-slate-100 flex flex-col gap-2">
                    <button type="button" wire:click="saveSelectedField" @disabled(! $canManageForms)
                            class="w-full px-4 py-2.5 text-sm font-semibold text-white rounded-xl hover:opacity-90 shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            style="background: var(--accent);">
                        {{ __('forms_builder.apply_changes') }}
                    </button>
                    <button type="button" wire:click="deleteFormField({{ (int) $fb_selected_field_id }})" @disabled(! $canManageForms)
                            class="w-full px-3 py-2 text-xs font-semibold text-red-600 bg-red-50 border border-red-100 rounded-lg hover:bg-red-100 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ __('forms_builder.delete_field') }}
                    </button>
                </div>
            </div>
        </div>
    @elseif ($fb_selected_form_id)
        <!-- FORM PROPERTIES -->
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">{{ __('forms_builder.form_config') }}</h3>

            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label class="text-[11px] font-medium text-slate-700">{{ __('forms_builder.name') }}</label>
                    <input type="text" wire:model="fb_selected_form_name" @disabled(! $canManageForms)
                           class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50">
                    <x-input-error :messages="$errors->get('fb_selected_form_name')" />
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] font-medium text-slate-700">{{ __('forms_builder.description_optional') }}</label>
                    <textarea wire:model="fb_selected_form_description" rows="2" @disabled(! $canManageForms)
                              class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50"
                              placeholder="{{ __('forms_builder.description_placeholder') }}"></textarea>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] font-medium text-slate-700">{{ __('forms_builder.category') }}</label>
                    <x-select-input wire:model="fb_selected_form_category_id" :disabled="! $canManageForms">
                        <option value="">{{ __('forms_builder.all_categories') }}</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </x-select-input>
                    <x-input-error :messages="$errors->get('fb_selected_form_category_id')" />
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] font-medium text-slate-700">{{ __('forms_builder.target_who') }}</label>
                    <x-select-input wire:model="fb_selected_form_target_user_id" :disabled="! $canManageForms">
                        <option value="">{{ __('forms_builder.target_team') }}</option>
                        <optgroup label="{{ __('forms_builder.target_one_person') }}">
                        @foreach ($members as $m)
                            <option value="{{ $m->user_id }}">{{ $m->user?->name ?? '—' }}</option>
                        @endforeach
                        </optgroup>
                    </x-select-input>
                    <p class="text-[10px] text-slate-500">{{ __('forms_builder.target_help') }}</p>
                    <x-input-error :messages="$errors->get('fb_selected_form_target_user_id')" />
                </div>

                <!-- Status actions -->
                <div class="flex items-center justify-between pt-2">
                    <span class="text-xs font-medium text-slate-700">{{ __('forms_builder.status') }}</span>
                    <div class="flex gap-2">
                        @if($fb_selected_form_status === 'published')
                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-[10px] font-medium text-emerald-700">{{ __('forms_builder.published') }}</span>
                        @elseif($fb_selected_form_status === 'archived')
                            <span class="px-2 py-0.5 rounded-full bg-slate-100 text-[10px] font-medium text-slate-500">{{ __('forms_builder.archived') }}</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full bg-amber-100 text-[10px] font-medium text-amber-700">{{ __('forms_builder.draft') }}</span>
                        @endif
                    </div>
                </div>

                @if($fb_selected_form_status !== 'archived')
                    <button type="button" wire:click="archiveForm" @disabled(! $canManageForms)
                            class="w-full px-3 py-2 text-xs font-semibold text-slate-600 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ __('forms_builder.archive') }}
                    </button>
                @endif

                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs font-semibold text-slate-800">{{ __('forms_builder.public_link') }}</div>
                            <div class="text-[11px] text-slate-500">{{ __('forms_builder.public_link_help') }}</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="fb_selected_form_public" class="sr-only peer" @disabled(! $canManageForms)>
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[var(--accent)] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[var(--accent)]"></div>
                        </label>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[11px] font-medium text-slate-700">{{ __('forms_builder.public_slug') }}</label>
                        <div class="flex items-center gap-2">
                            <input type="text" wire:model="fb_selected_form_slug" @disabled(! $canManageForms)
                                   placeholder="{{ __('forms_builder.slug_placeholder') }}"
                                   class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50">
                            <button type="button" wire:click="generatePublicSlug" @disabled(! $canManageForms)
                                    class="h-9 px-3 rounded-lg border border-slate-200 bg-slate-50 text-slate-800 text-xs font-semibold hover:bg-slate-100 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                {{ __('forms_builder.generate') }}
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('fb_selected_form_slug')" />
                        @if($fb_selected_form_public && $fb_selected_form_slug)
                            <div class="mt-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-[11px] text-slate-700">
                                <div class="font-semibold">{{ __('forms_builder.link') }}</div>
                                <div class="mt-1 font-mono break-all">{{ url('/f/' . $fb_selected_form_slug) }}</div>
                            </div>
                        @endif
                        @if(!$fb_selected_form_target_user_id && $fb_selected_form_slug)
                            <div class="mt-2 rounded-lg border border-[var(--accent)]/30 bg-[var(--accent-soft)]/20 px-3 py-2 text-[11px] text-slate-700">
                                <div class="font-semibold">{{ __('forms_builder.team_link') }}</div>
                                <p class="mt-0.5 text-slate-500">{{ __('forms_builder.team_link_help') }}</p>
                                <div class="mt-1 font-mono break-all text-[var(--accent)]">{{ url('/forms/l/' . $fb_selected_form_slug) }}</div>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[11px] font-medium text-slate-700">{{ __('forms_builder.public_title_optional') }}</label>
                        <input type="text" wire:model="fb_selected_form_public_title" @disabled(! $canManageForms)
                               class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[11px] font-medium text-slate-700">{{ __('forms_builder.public_description_optional') }}</label>
                        <textarea wire:model="fb_selected_form_public_description" rows="3" @disabled(! $canManageForms)
                                  class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50"></textarea>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[11px] font-medium text-slate-700">{{ __('forms_builder.thank_you_optional') }}</label>
                        <textarea wire:model="fb_selected_form_public_thank_you" rows="3" @disabled(! $canManageForms)
                                  class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50"
                                  placeholder="{{ __('forms_builder.thank_you_placeholder') }}"></textarea>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <button type="button" wire:click="deleteForm({{ (int) $fb_selected_form_id }})" @disabled(! $canManageForms)
                            wire:confirm="{{ __('forms_builder.delete_form_confirm') }}"
                            class="w-full px-3 py-2 text-xs font-semibold text-red-600 bg-red-50 border border-red-100 rounded-lg hover:bg-red-100 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ __('forms_builder.delete_form') }}
                    </button>
                </div>
            </div>
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center mb-3">
                <iconify-icon icon="solar:cursor-square-linear" class="text-slate-400 text-xl"></iconify-icon>
            </div>
            <p class="text-xs font-medium text-slate-900">{{ __('forms_builder.nothing_selected') }}</p>
            <p class="text-[10px] text-slate-500 mt-1 max-w-[150px]">{{ __('forms_builder.nothing_selected_hint') }}</p>
        </div>
    @endif
</div>
