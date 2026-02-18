<div class="space-y-8">
    <!-- Form Selection -->
    <div>
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 px-1">{{ __('Formulaires') }}</h3>

        <!-- New Form Input -->
        <div class="mb-4 space-y-2">
            <div class="relative">
                <input type="text" wire:model.live.debounce.150ms="fb_template_name" placeholder="{{ __('Nouveau formulaire...') }}"
                       @disabled(! $canManageForms)
                       class="w-full bg-slate-50 text-xs py-2.5 pl-3 pr-10 rounded-lg border-0 ring-1 ring-slate-200 focus:ring-2 focus:ring-slate-900 focus:bg-white transition-all placeholder:text-slate-400 disabled:opacity-50">
                <button
                    type="button"
                    wire:click="createFormTemplate"
                    @disabled(! $canManageForms || trim($fb_template_name) === '')
                    class="absolute right-1 top-1 p-1.5 rounded-md text-slate-400 hover:text-[var(--accent)] hover:bg-white disabled:opacity-40 disabled:pointer-events-none transition-all"
                >
                    <iconify-icon icon="solar:add-circle-bold" width="16"></iconify-icon>
                </button>
            </div>
            <x-input-error :messages="$errors->get('fb_template_name')" />
        </div>

        <!-- List -->
        <div class="space-y-1 max-h-48 overflow-y-auto custom-scrollbar pr-1">
            @foreach ($formTemplates as $tpl)
                <button type="button" wire:click="selectTemplate({{ $tpl->id }})"
                        class="w-full text-left px-3 py-2.5 rounded-lg border transition-all flex items-center justify-between group"
                        @class([
                            'bg-white border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-600' => (int) $fb_selected_template_id !== (int) $tpl->id,
                            'bg-slate-900 border-slate-900 text-white shadow-md' => (int) $fb_selected_template_id === (int) $tpl->id,
                        ])>
                    <span class="text-xs font-semibold truncate">{{ $tpl->name }}</span>
                    @if((int) $fb_selected_template_id === (int) $tpl->id)
                        <iconify-icon icon="solar:check-circle-bold" width="14"></iconify-icon>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    <hr class="border-slate-100">

    <!-- Steps -->
    <div>
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 px-1">{{ __('Étapes') }}</h3>

        @php
            $selected = $fb_selected_template_id
                ? $formTemplates->firstWhere('id', (int) $fb_selected_template_id)
                : null;
            $steps = $selected?->steps ?? collect();
        @endphp

        <div class="mb-3 space-y-2">
            <div class="relative">
                <input
                    type="text"
                    wire:model.live.debounce.150ms="fb_step_title"
                    placeholder="{{ __('Titre de l’étape...') }}"
                    @disabled(! $canManageForms || ! $fb_selected_template_id)
                    class="w-full bg-slate-50 text-xs py-2.5 pl-3 pr-10 rounded-lg border-0 ring-1 ring-slate-200 focus:ring-2 focus:ring-slate-900 focus:bg-white transition-all placeholder:text-slate-400 disabled:opacity-50"
                >
                <button
                    type="button"
                    wire:click="createStep"
                    @disabled(! $canManageForms || ! $fb_selected_template_id || trim($fb_step_title) === '')
                    class="absolute right-1 top-1 p-1.5 rounded-md text-slate-400 hover:text-[var(--accent)] hover:bg-white disabled:opacity-40 disabled:pointer-events-none transition-all"
                    title="{{ __('Ajouter') }}"
                >
                    <iconify-icon icon="solar:add-circle-bold" width="16"></iconify-icon>
                </button>
            </div>
            <x-input-error :messages="$errors->get('fb_step_title')" />
        </div>

        <div class="space-y-1">
            @foreach ($steps as $s)
                @php
                    $isActive = (int) $fb_selected_step_id === (int) $s->id;
                    $count = (int) ($s->fields?->count() ?? 0);
                @endphp
                <button
                    type="button"
                    wire:click="selectStep({{ (int) $s->id }})"
                    class="w-full text-left px-3 py-2 rounded-lg border transition-all flex items-center justify-between"
                    @class([
                        'bg-white border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-700' => ! $isActive,
                        'bg-[var(--accent-soft)]/30 border-[var(--accent)] text-[var(--accent)] shadow-sm' => $isActive,
                    ])
                >
                    <span class="text-xs font-semibold truncate">{{ $s->title }}</span>
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full border border-slate-200 bg-white/70 text-slate-500">{{ $count }}</span>
                </button>
            @endforeach
        </div>
    </div>

    <hr class="border-slate-100">

    <!-- Field Types -->
    <div>
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 px-1">{{ __('Champs disponibles') }}</h3>
        <div class="grid grid-cols-2 gap-3">
            @php
                $fields = [
                    ['type' => 'text', 'label' => 'Texte court', 'icon' => 'solar:text-field-linear'],
                    ['type' => 'textarea', 'label' => 'Paragraphe', 'icon' => 'solar:text-square-linear'],
                    ['type' => 'select', 'label' => 'Liste déroulante', 'icon' => 'solar:list-arrow-down-linear'],
                    ['type' => 'checkbox', 'label' => 'Case à cocher', 'icon' => 'solar:check-square-linear'],
                    ['type' => 'date', 'label' => 'Date', 'icon' => 'solar:calendar-linear'],
                    ['type' => 'number', 'label' => 'Nombre', 'icon' => 'solar:ruler-linear'],
                ];
            @endphp

            @foreach($fields as $field)
                <button
                    type="button"
                    wire:click="quickAddField('{{ $field['type'] }}')"
                    @disabled(! $canManageForms || ! $fb_selected_template_id)
                    class="flex flex-col items-center justify-center gap-2 p-3 rounded-xl border border-slate-200 bg-white hover:border-[var(--accent)] hover:bg-[var(--accent-soft)]/5 hover:text-[var(--accent)] transition-all group disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <iconify-icon icon="{{ $field['icon'] }}" class="text-2xl text-slate-400 group-hover:text-[var(--accent)] transition-colors"></iconify-icon>
                    <span class="text-[10px] font-semibold text-slate-600 group-hover:text-[var(--accent)]">{{ $field['label'] }}</span>
                </button>
            @endforeach
        </div>
    </div>
</div>
