<div class="space-y-8">
    <!-- Form Selection -->
    <div>
        <h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-3 px-0">{{ __('forms_builder.forms') }}</h3>

        <!-- New Form Input -->
        <div class="mb-4 space-y-2">
            <div class="relative group">
                <input type="text" wire:model.live.debounce.150ms="fb_form_name" placeholder="{{ __('forms_builder.new_form_placeholder') }}"
                       @disabled(! $canManageForms)
                       class="w-full bg-slate-50/80 text-sm py-3 pl-4 pr-11 rounded-xl border border-slate-200/80 focus:ring-2 focus:ring-[var(--accent)]/30 focus:border-[var(--accent)] focus:bg-white transition-all placeholder:text-slate-400 disabled:opacity-50">
                <button
                    type="button"
                    wire:click="createForm"
                    @disabled(! $canManageForms || trim($fb_form_name) === '')
                    class="absolute right-2 top-1/2 -translate-y-1/2 p-2 rounded-lg text-slate-400 hover:text-[var(--accent)] hover:bg-white border border-transparent hover:border-slate-200 disabled:opacity-40 disabled:pointer-events-none transition-all"
                >
                    <iconify-icon icon="solar:add-circle-bold" width="18"></iconify-icon>
                </button>
            </div>
            <x-input-error :messages="$errors->get('fb_form_name')" />
        </div>

        <!-- List -->
        <div class="space-y-1.5 max-h-52 overflow-y-auto custom-scrollbar pr-0.5">
            @foreach ($forms as $f)
                @php
                    $fStatus = $f->status instanceof \App\Enums\FormStatus ? $f->status->value : (string) $f->status;
                    $isSelected = (int) $fb_selected_form_id === (int) $f->id;
                @endphp
                <button type="button" wire:click="selectForm({{ $f->id }})"
                        class="w-full text-left px-3.5 py-3 rounded-xl border transition-all flex items-center justify-between gap-2 group {{ $isSelected ? 'bg-[var(--accent)]/10 border-[var(--accent)]/40 text-slate-900 shadow-sm' : 'bg-white border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-700' }}">
                    <span class="text-sm font-semibold truncate">{{ $f->name }}</span>
                    <div class="flex items-center gap-2 shrink-0">
                        @if($fStatus === 'published')
                            <span class="h-2 w-2 rounded-full bg-emerald-500" title="{{ __('forms_builder.published') }}"></span>
                        @elseif($fStatus === 'archived')
                            <span class="h-2 w-2 rounded-full bg-slate-400" title="{{ __('forms_builder.archived') }}"></span>
                        @else
                            <span class="h-2 w-2 rounded-full bg-amber-500" title="{{ __('forms_builder.draft') }}"></span>
                        @endif
                        @if($isSelected)
                            <iconify-icon icon="solar:check-circle-bold" width="16" class="text-[var(--accent)]"></iconify-icon>
                        @endif
                    </div>
                </button>
            @endforeach
        </div>
    </div>

    <hr class="border-slate-100">

    <!-- Field Types -->
    <div>
        <h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-3 px-0">{{ __('forms_builder.available_fields') }}</h3>
        <div class="grid grid-cols-2 gap-2.5">
            @php
                $fieldTypes = [
                    ['type' => 'text', 'label' => __('forms_builder.field_short_text'), 'icon' => 'solar:text-field-linear'],
                    ['type' => 'textarea', 'label' => __('forms_builder.field_paragraph'), 'icon' => 'solar:text-square-linear'],
                    ['type' => 'select', 'label' => __('forms_builder.field_dropdown'), 'icon' => 'solar:list-arrow-down-linear'],
                    ['type' => 'radio', 'label' => __('forms_builder.field_single_choice'), 'icon' => 'solar:record-circle-linear'],
                    ['type' => 'checkbox', 'label' => __('forms_builder.field_checkbox'), 'icon' => 'solar:check-square-linear'],
                    ['type' => 'date', 'label' => __('forms_builder.field_date'), 'icon' => 'solar:calendar-linear'],
                    ['type' => 'datetime', 'label' => __('forms_builder.field_datetime'), 'icon' => 'solar:calendar-date-linear'],
                    ['type' => 'number', 'label' => __('forms_builder.field_number'), 'icon' => 'solar:ruler-linear'],
                    ['type' => 'email', 'label' => __('forms_builder.field_email'), 'icon' => 'solar:letter-linear'],
                    ['type' => 'file', 'label' => __('forms_builder.field_file'), 'icon' => 'solar:upload-linear'],
                    ['type' => 'section', 'label' => __('forms_builder.field_section'), 'icon' => 'solar:minus-circle-linear'],
                ];
            @endphp

            @foreach($fieldTypes as $ft)
                <button
                    type="button"
                    wire:click="quickAddField('{{ $ft['type'] }}')"
                    @disabled(! $canManageForms || ! $fb_selected_form_id)
                    class="flex flex-col items-center justify-center gap-2.5 p-3.5 rounded-xl border border-slate-200 bg-white hover:border-[var(--accent)]/60 hover:bg-[var(--accent-soft)]/10 hover:text-[var(--accent)] transition-all group disabled:opacity-50 disabled:cursor-not-allowed shadow-sm"
                >
                    <iconify-icon icon="{{ $ft['icon'] }}" class="text-2xl text-slate-500 group-hover:text-[var(--accent)] transition-colors" width="28"></iconify-icon>
                    <span class="text-[11px] font-semibold text-slate-600 group-hover:text-[var(--accent)] text-center leading-tight">{{ $ft['label'] }}</span>
                </button>
            @endforeach
        </div>
    </div>
</div>
