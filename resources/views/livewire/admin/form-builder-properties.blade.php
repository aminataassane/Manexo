<div class="space-y-6">
    @if ($fb_selected_field_id)
        <!-- FIELD PROPERTIES -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Champ sélectionné') }}</h3>
                <span class="px-2 py-0.5 rounded bg-slate-100 text-[10px] font-mono text-slate-500">{{ $fb_selected_field_type }}</span>
            </div>

            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label class="text-[11px] font-medium text-slate-700">{{ __('Clé (name)') }}</label>
                    <input
                        type="text"
                        wire:model.live.debounce.150ms="fb_selected_field_key"
                        @disabled(! $canManageForms)
                        class="block w-full rounded-lg border-slate-200 bg-white py-2 px-3 text-xs shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50 font-mono"
                        placeholder="ex: company_email"
                    >
                    <div class="text-[10px] text-slate-500">{{ __('Lettres/chiffres/underscore. Utilisée dans custom_fields.') }}</div>
                    <x-input-error :messages="$errors->get('fb_selected_field_key')" />
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] font-medium text-slate-700">{{ __('Label') }}</label>
                    <input type="text" wire:model.live.debounce.150ms="fb_selected_field_label" @disabled(! $canManageForms)
                           class="block w-full rounded-lg border-slate-200 bg-white py-2 px-3 text-xs shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50">
                    <x-input-error :messages="$errors->get('fb_selected_field_label')" />
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] font-medium text-slate-700">{{ __('Placeholder (optionnel)') }}</label>
                    <input
                        type="text"
                        wire:model.live.debounce.150ms="fb_selected_field_placeholder"
                        @disabled(! $canManageForms)
                        class="block w-full rounded-lg border-slate-200 bg-white py-2 px-3 text-xs shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50"
                        placeholder="{{ __('Ex: Entrez votre email') }}"
                    >
                    <x-input-error :messages="$errors->get('fb_selected_field_placeholder')" />
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] font-medium text-slate-700">{{ __('Texte d’aide (optionnel)') }}</label>
                    <textarea
                        wire:model.live.debounce.150ms="fb_selected_field_help_text"
                        rows="2"
                        @disabled(! $canManageForms)
                        class="block w-full rounded-lg border-slate-200 bg-white py-2 px-3 text-xs shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50"
                        placeholder="{{ __('Ex: Nous utilisons cet email pour vous répondre.') }}"
                    ></textarea>
                    <x-input-error :messages="$errors->get('fb_selected_field_help_text')" />
                </div>

                @if ($fb_selected_field_type === 'select')
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-medium text-slate-700">{{ __('Options (séparées par virgule)') }}</label>
                        <textarea wire:model.live.debounce.150ms="fb_selected_field_options" rows="3" @disabled(! $canManageForms)
                                  class="block w-full rounded-lg border-slate-200 bg-white py-2 px-3 text-xs shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50"
                                  placeholder="Option 1, Option 2..."></textarea>
                        <x-input-error :messages="$errors->get('fb_selected_field_options')" />
                    </div>
                @endif

                <div class="flex items-center justify-between pt-2">
                    <span class="text-xs font-medium text-slate-700">{{ __('Obligatoire') }}</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="fb_selected_field_required" class="sr-only peer" @disabled(! $canManageForms)>
                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[var(--accent)] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[var(--accent)]"></div>
                    </label>
                </div>

                <div class="pt-4 border-t border-slate-100 flex flex-col gap-2">
                    <button type="button" wire:click="saveSelectedField" @disabled(! $canManageForms)
                            class="w-full px-3 py-2 text-xs font-semibold text-white bg-slate-900 rounded-lg hover:bg-slate-800 shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ __('Appliquer les modifications') }}
                    </button>
                    <button type="button" wire:click="deleteFormField({{ (int) $fb_selected_field_id }})" @disabled(! $canManageForms)
                            class="w-full px-3 py-2 text-xs font-semibold text-red-600 bg-red-50 border border-red-100 rounded-lg hover:bg-red-100 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ __('Supprimer ce champ') }}
                    </button>
                </div>
            </div>
        </div>
    @elseif ($fb_selected_template_id)
        @if ($fb_selected_step_id)
            <!-- STEP PROPERTIES -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ __('Étape sélectionnée') }}</h3>
                    <span class="px-2 py-0.5 rounded bg-slate-100 text-[10px] font-mono text-slate-500">#{{ (int) $fb_selected_step_id }}</span>
                </div>

                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-medium text-slate-700">{{ __('Titre') }}</label>
                        <input
                            type="text"
                            wire:model.live.debounce.150ms="fb_selected_step_title"
                            @disabled(! $canManageForms)
                            class="block w-full rounded-lg border-slate-200 bg-white py-2 px-3 text-xs shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50"
                            placeholder="{{ __('Titre de l’étape') }}"
                        >
                        <x-input-error :messages="$errors->get('fb_selected_step_title')" />
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[11px] font-medium text-slate-700">{{ __('Description (optionnel)') }}</label>
                        <textarea
                            wire:model.live.debounce.150ms="fb_selected_step_description"
                            rows="3"
                            @disabled(! $canManageForms)
                            class="block w-full rounded-lg border-slate-200 bg-white py-2 px-3 text-xs shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50"
                            placeholder="{{ __('Description de l’étape') }}"
                        ></textarea>
                        <x-input-error :messages="$errors->get('fb_selected_step_description')" />
                    </div>

                    <div class="pt-2 flex flex-col gap-2">
                        <button
                            type="button"
                            wire:click="saveSelectedStep"
                            @disabled(! $canManageForms)
                            class="w-full px-3 py-2 text-xs font-semibold text-white bg-slate-900 rounded-lg hover:bg-slate-800 shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ __('Enregistrer l’étape') }}
                        </button>
                        <button
                            type="button"
                            wire:click="deleteStep({{ (int) $fb_selected_step_id }})"
                            @disabled(! $canManageForms)
                            class="w-full px-3 py-2 text-xs font-semibold text-red-600 bg-red-50 border border-red-100 rounded-lg hover:bg-red-100 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ __('Supprimer l’étape') }}
                        </button>
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">
        @endif

        <!-- FORM PROPERTIES -->
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">{{ __('Configuration du formulaire') }}</h3>

            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label class="text-[11px] font-medium text-slate-700">{{ __('Nom') }}</label>
                    <input type="text" wire:model="fb_selected_template_name" @disabled(! $canManageForms)
                           class="block w-full rounded-lg border-slate-200 bg-white py-2 px-3 text-xs shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50">
                    <x-input-error :messages="$errors->get('fb_selected_template_name')" />
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] font-medium text-slate-700">{{ __('Catégorie') }}</label>
                    <x-select-input wire:model="fb_selected_template_category_id" :disabled="! $canManageForms">
                        <option value="">{{ __('Toutes') }}</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </x-select-input>
                    <x-input-error :messages="$errors->get('fb_selected_template_category_id')" />
                </div>

                <div class="space-y-1.5">
                    <label class="text-[11px] font-medium text-slate-700">{{ __('Cible (Qui peut utiliser)') }}</label>
                    <x-select-input wire:model="fb_selected_template_target_user_id" :disabled="! $canManageForms">
                        <option value="">{{ __('Public (Tout le monde)') }}</option>
                        @foreach ($members as $m)
                            <option value="{{ $m->user_id }}">{{ $m->user?->name ?? '—' }}</option>
                        @endforeach
                    </x-select-input>
                    <x-input-error :messages="$errors->get('fb_selected_template_target_user_id')" />
                </div>

                <div class="flex items-center justify-between pt-2">
                    <span class="text-xs font-medium text-slate-700">{{ __('Actif') }}</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="fb_selected_template_active" class="sr-only peer" @disabled(! $canManageForms)>
                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[var(--accent)] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[var(--accent)]"></div>
                    </label>
                </div>

                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs font-semibold text-slate-800">{{ __('Lien public') }}</div>
                            <div class="text-[11px] text-slate-500">{{ __('Permet de publier le formulaire sur /f/{slug} et de l’embed en iframe.') }}</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="fb_selected_template_public" class="sr-only peer" @disabled(! $canManageForms)>
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[var(--accent)] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[var(--accent)]"></div>
                        </label>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[11px] font-medium text-slate-700">{{ __('Slug public') }}</label>
                        <div class="flex items-center gap-2">
                            <input type="text" wire:model="fb_selected_template_public_slug" @disabled(! $canManageForms)
                                   placeholder="ex: support-internet"
                                   class="block w-full rounded-lg border-slate-200 bg-white py-2 px-3 text-xs shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50">
                            <button type="button" wire:click="generatePublicSlug" @disabled(! $canManageForms)
                                    class="h-9 px-3 rounded-lg border border-slate-200 bg-slate-50 text-slate-800 text-xs font-semibold hover:bg-slate-100 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                {{ __('Générer') }}
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('fb_selected_template_public_slug')" />
                        @if($fb_selected_template_public && $fb_selected_template_public_slug)
                            <div class="mt-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-[11px] text-slate-700">
                                <div class="font-semibold">{{ __('Lien') }}</div>
                                <div class="mt-1 font-mono break-all">{{ url('/f/' . $fb_selected_template_public_slug) }}</div>
                                <div class="mt-2 text-slate-500">{{ __('Embed (iframe)') }}</div>
                                <div class="mt-1 font-mono break-all">&lt;iframe src=&quot;{{ url('/f/' . $fb_selected_template_public_slug . '?embed=1') }}&quot; style=&quot;width:100%;height:700px;border:0;&quot;&gt;&lt;/iframe&gt;</div>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[11px] font-medium text-slate-700">{{ __('Titre public (optionnel)') }}</label>
                        <input type="text" wire:model="fb_selected_template_public_title" @disabled(! $canManageForms)
                               class="block w-full rounded-lg border-slate-200 bg-white py-2 px-3 text-xs shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[11px] font-medium text-slate-700">{{ __('Description publique (optionnel)') }}</label>
                        <textarea wire:model="fb_selected_template_public_description" rows="3" @disabled(! $canManageForms)
                                  class="block w-full rounded-lg border-slate-200 bg-white py-2 px-3 text-xs shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50"></textarea>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[11px] font-medium text-slate-700">{{ __('Message de confirmation (optionnel)') }}</label>
                        <textarea wire:model="fb_selected_template_public_thank_you" rows="3" @disabled(! $canManageForms)
                                  class="block w-full rounded-lg border-slate-200 bg-white py-2 px-3 text-xs shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50"
                                  placeholder="{{ __('Merci, votre demande a bien été envoyée.') }}"></textarea>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <button type="button" wire:click="deleteFormTemplate({{ (int) $fb_selected_template_id }})" @disabled(! $canManageForms)
                            class="w-full px-3 py-2 text-xs font-semibold text-red-600 bg-red-50 border border-red-100 rounded-lg hover:bg-red-100 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ __('Supprimer ce formulaire') }}
                    </button>
                </div>
            </div>
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center mb-3">
                <iconify-icon icon="solar:cursor-square-linear" class="text-slate-400 text-xl"></iconify-icon>
            </div>
            <p class="text-xs font-medium text-slate-900">{{ __('Rien de sélectionné') }}</p>
            <p class="text-[10px] text-slate-500 mt-1 max-w-[150px]">{{ __('Sélectionnez un champ dans le formulaire ou configurez le formulaire lui-même.') }}</p>
        </div>
    @endif
</div>
