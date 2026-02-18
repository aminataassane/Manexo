<div class="bg-white rounded-xl shadow-sm border border-slate-200 flex-1 flex flex-col overflow-hidden">
    <!-- Form Header Simulation -->
    <div class="px-8 py-6 border-b border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">{{ $fb_selected_template_name ?: 'Nouveau formulaire' }}</h2>
                <div class="flex items-center gap-2 mt-1 text-sm text-slate-500">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 text-xs font-medium text-slate-600">
                        {{ $categories->firstWhere('id', $fb_selected_template_category_id)?->name ?? 'Toutes catégories' }}
                    </span>
                    <span>·</span>
                    <span>{{ $fb_selected_template_target_user_id ? 'Réservé' : 'Public' }}</span>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="button" class="p-2 text-slate-400 hover:text-slate-600 transition-colors">
                    <iconify-icon icon="solar:settings-linear" width="20"></iconify-icon>
                </button>
            </div>
        </div>

        <!-- Fake Tabs -->
        <div class="flex items-center gap-6 border-b border-slate-100">
            <button class="pb-3 text-sm font-semibold text-slate-900 border-b-2 border-slate-900">Champs</button>
            <button class="pb-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent">Règles</button>
            <button class="pb-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent">Scripts</button>
            <button class="pb-3 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent">Aperçu</button>
        </div>
    </div>

    <!-- Fields List -->
    <div class="flex-1 overflow-y-auto p-8 space-y-6 bg-white">
        @php
            $selected = $fb_selected_template_id
                ? $formTemplates->firstWhere('id', (int) $fb_selected_template_id)
                : null;
            $selectedSteps = $selected ? ($selected->steps ?? collect()) : collect();
        @endphp

        @if ($selected)
            @forelse ($selectedSteps as $s)
                @php
                    $isStepActive = (int) $fb_selected_step_id === (int) $s->id;
                    $fields = $s->fields ?? collect();
                @endphp

                <div class="rounded-2xl border border-slate-200 bg-slate-50/40">
                    <button
                        type="button"
                        wire:click="selectStep({{ (int) $s->id }})"
                        class="w-full text-left px-5 py-4 flex items-start justify-between gap-4 border-b border-slate-200/70 rounded-t-2xl transition-colors {{ $isStepActive ? 'bg-[var(--accent-soft)]/20' : 'hover:bg-white/60' }}"
                    >
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] font-mono text-slate-400">#{{ (int) $s->number }}</span>
                                <h3 class="text-sm font-bold text-slate-900 truncate">{{ $s->title }}</h3>
                            </div>
                            @if (!empty($s->description))
                                <p class="mt-1 text-xs text-slate-500 line-clamp-2">{{ $s->description }}</p>
                            @endif
                        </div>
                        <span class="shrink-0 text-[11px] font-semibold px-2 py-1 rounded-full border border-slate-200 bg-white text-slate-600">
                            {{ (int) $fields->count() }} {{ __('champ(s)') }}
                        </span>
                    </button>

                    <div class="p-5 space-y-3">
                        @forelse ($fields as $f)
                            @php
                                $isActive = (int) $fb_selected_field_id === (int) $f->id;
                                $isRequired = (bool) $f->required;
                                $label = (string) $f->label;
                                $type = (string) $f->type;
                                $placeholder = (string) ($f->placeholder ?? '');
                                $help = (string) ($f->help_text ?? '');
                            @endphp
                            <div
                                wire:click="selectField({{ (int) $f->id }})"
                                class="group relative p-4 rounded-xl border-2 transition-all cursor-pointer {{ $isActive ? 'border-[var(--accent)] bg-white ring-1 ring-[var(--accent)]' : 'border-transparent bg-white hover:border-slate-200 hover:bg-slate-50' }}"
                            >
                                <div class="absolute right-3 top-3 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click.stop="deleteFormField({{ (int) $f->id }})" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                        <iconify-icon icon="solar:trash-bin-trash-linear" width="16"></iconify-icon>
                                    </button>
                                </div>

                                <label class="block text-sm font-semibold text-slate-900 mb-2 pr-10">
                                    {{ $label }} @if($isRequired) <span class="text-red-500">*</span> @endif
                                </label>

                                @if ($type === 'select')
                                    <div class="relative pointer-events-none">
                                        <div class="block w-full rounded-lg border border-slate-200 bg-white py-2.5 px-3 text-slate-500 shadow-sm text-sm flex justify-between items-center">
                                            {{ $placeholder !== '' ? $placeholder : __('Sélectionnez une option') }}
                                            <iconify-icon icon="solar:alt-arrow-down-linear" class="text-slate-400"></iconify-icon>
                                        </div>
                                    </div>
                                @elseif ($type === 'textarea')
                                    <div class="block w-full rounded-lg border border-slate-200 bg-white py-3 px-3 text-slate-400 shadow-sm text-sm h-24 pointer-events-none">
                                        {{ $placeholder !== '' ? $placeholder : __('Zone de texte...') }}
                                    </div>
                                @elseif ($type === 'checkbox')
                                    <div class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 bg-white pointer-events-none">
                                        <span class="inline-flex h-5 w-5 items-center justify-center rounded border border-slate-300 bg-white"></span>
                                        <span class="text-sm text-slate-700">{{ $label }}</span>
                                    </div>
                                @else
                                    <div class="block w-full rounded-lg border border-slate-200 bg-white py-2.5 px-3 text-slate-400 shadow-sm text-sm pointer-events-none">
                                        {{ $placeholder !== '' ? $placeholder : __('Texte court...') }}
                                    </div>
                                @endif

                                @if($help !== '')
                                    <div class="mt-2 text-[11px] text-slate-500">{{ $help }}</div>
                                @endif
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-slate-200 bg-white/70 p-5 text-center">
                                <div class="text-xs font-semibold text-slate-700">{{ __('Aucun champ dans cette étape') }}</div>
                                <div class="mt-1 text-[11px] text-slate-500">{{ __('Ajoute des champs depuis la colonne “Champs disponibles”.') }}</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="h-full flex flex-col items-center justify-center text-center p-8 border-2 border-dashed border-slate-200 rounded-xl bg-slate-50/50">
                    <div class="w-16 h-16 rounded-full bg-white shadow-sm flex items-center justify-center mb-4">
                        <iconify-icon icon="solar:clipboard-add-linear" class="text-slate-400 text-3xl"></iconify-icon>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-900">{{ __('Ce formulaire est vide') }}</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-xs">{{ __('Ajoutez une étape puis des champs pour commencer à construire votre formulaire.') }}</p>
                </div>
            @endforelse
        @else
            <div class="h-full flex flex-col items-center justify-center text-center p-8">
                <div class="w-20 h-20 rounded-2xl bg-slate-100 flex items-center justify-center mb-6 rotate-3">
                    <iconify-icon icon="solar:document-add-bold-duotone" class="text-slate-400 text-4xl"></iconify-icon>
                </div>
                <h3 class="text-lg font-bold text-slate-900">{{ __('Aucun formulaire sélectionné') }}</h3>
                <p class="text-sm text-slate-500 mt-2 max-w-sm">{{ __('Sélectionnez un formulaire existant dans la liste ou créez-en un nouveau pour commencer.') }}</p>
            </div>
        @endif
    </div>
</div>