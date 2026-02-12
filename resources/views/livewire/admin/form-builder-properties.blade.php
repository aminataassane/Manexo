{{-- RIGHT: Properties --}}
<div class="w-72 border-l border-slate-200 bg-white flex flex-col overflow-y-auto custom-scrollbar">
    <div class="p-4 border-b border-slate-100">
        <h3 class="text-sm font-semibold text-slate-900">{{ __('Propriétés') }}</h3>
        <p class="text-[11px] text-slate-500">{{ __('Configuration du formulaire / champ sélectionné') }}</p>
    </div>

    <div class="p-4 space-y-5">
        @if ($fb_selected_template_id)
            <div class="space-y-1.5">
                <label class="text-[11px] font-medium text-slate-700 uppercase tracking-wide">{{ __('Nom du formulaire') }}</label>
                <input type="text" wire:model="fb_selected_template_name" @disabled(! $canManageForms)
                       class="block w-full rounded-md border-0 bg-white py-1.5 px-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 focus:ring-2 focus:ring-inset focus:ring-slate-900 sm:text-xs sm:leading-6 disabled:opacity-50">
                <x-input-error :messages="$errors->get('fb_selected_template_name')" class="mt-2" />
            </div>

            <div class="space-y-1.5">
                <label class="text-[11px] font-medium text-slate-700 uppercase tracking-wide">{{ __('Catégorie') }}</label>
                <div class="relative">
                    <select wire:model="fb_selected_template_category_id" @disabled(! $canManageForms)
                            class="block w-full rounded-md border-0 bg-white py-1.5 pl-2.5 pr-8 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 focus:ring-2 focus:ring-inset focus:ring-slate-900 sm:text-xs sm:leading-6 appearance-none disabled:opacity-50">
                        <option value="">{{ __('Toutes') }}</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                    <iconify-icon icon="solar:alt-arrow-down-linear" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" width="14"></iconify-icon>
                </div>
                <x-input-error :messages="$errors->get('fb_selected_template_category_id')" class="mt-2" />
            </div>

            <div class="space-y-1.5">
                <label class="text-[11px] font-medium text-slate-700 uppercase tracking-wide">{{ __('Réservé à') }}</label>
                <div class="relative">
                    <select wire:model="fb_selected_template_target_user_id" @disabled(! $canManageForms)
                            class="block w-full rounded-md border-0 bg-white py-1.5 pl-2.5 pr-8 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 focus:ring-2 focus:ring-inset focus:ring-slate-900 sm:text-xs sm:leading-6 appearance-none disabled:opacity-50">
                        <option value="">{{ __('Public') }}</option>
                        @foreach ($members as $m)
                            <option value="{{ $m->user_id }}">{{ $m->user?->name ?? '—' }}</option>
                        @endforeach
                    </select>
                    <iconify-icon icon="solar:alt-arrow-down-linear" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" width="14"></iconify-icon>
                </div>
                <x-input-error :messages="$errors->get('fb_selected_template_target_user_id')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between pt-2">
                <span class="flex flex-col">
                    <span class="text-xs font-medium text-slate-900">{{ __('Actif') }}</span>
                    <span class="text-[10px] text-slate-500">{{ __('Visible dans la création') }}</span>
                </span>
                <div class="relative inline-block w-8 h-4 align-middle select-none transition duration-200 ease-in">
                    <input type="checkbox" wire:model="fb_selected_template_active" @disabled(! $canManageForms)
                           class="toggle-checkbox absolute block w-4 h-4 rounded-full bg-white border-4 appearance-none cursor-pointer border-slate-300 checked:border-slate-900 transition-colors duration-200"
                           style="right: 1rem;">
                    <span class="toggle-label block overflow-hidden h-4 rounded-full bg-slate-300 cursor-pointer"></span>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-4 mt-4"></div>

            @if ($fb_selected_field_id)
                <div class="space-y-1.5">
                    <label class="text-[11px] font-medium text-slate-700 uppercase tracking-wide">{{ __('Label du champ') }}</label>
                    <input type="text" wire:model="fb_selected_field_label" @disabled(! $canManageForms)
                           class="block w-full rounded-md border-0 bg-white py-1.5 px-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 focus:ring-2 focus:ring-inset focus:ring-slate-900 sm:text-xs sm:leading-6 disabled:opacity-50">
                    <div class="mt-1 text-[10px] text-slate-400 font-mono">{{ $fb_selected_field_key }} · {{ $fb_selected_field_type }}</div>
                    <x-input-error :messages="$errors->get('fb_selected_field_label')" class="mt-2" />
                </div>

                @if ($fb_selected_field_type === 'select')
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-medium text-slate-700 uppercase tracking-wide">{{ __('Options') }}</label>
                        <input type="text" wire:model="fb_selected_field_options" @disabled(! $canManageForms)
                               placeholder="Option 1, Option 2, Option 3"
                               class="block w-full rounded-md border-0 bg-white py-1.5 px-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 focus:ring-2 focus:ring-inset focus:ring-slate-900 sm:text-xs sm:leading-6 disabled:opacity-50">
                        <x-input-error :messages="$errors->get('fb_selected_field_options')" class="mt-2" />
                    </div>
                @endif

                <div class="flex items-center justify-between pt-1">
                    <span class="flex flex-col">
                        <span class="text-xs font-medium text-slate-900">{{ __('Obligatoire') }}</span>
                        <span class="text-[10px] text-slate-500">{{ __("L'utilisateur doit remplir") }}</span>
                    </span>
                    <div class="relative inline-block w-8 h-4 align-middle select-none transition duration-200 ease-in">
                        <input type="checkbox" wire:model="fb_selected_field_required" @disabled(! $canManageForms)
                               class="toggle-checkbox absolute block w-4 h-4 rounded-full bg-white border-4 appearance-none cursor-pointer border-slate-300 checked:border-slate-900 transition-colors duration-200"
                               style="right: 1rem;">
                        <span class="toggle-label block overflow-hidden h-4 rounded-full bg-slate-300 cursor-pointer"></span>
                    </div>
                </div>

                <button type="button" wire:click="saveSelectedField" @disabled(! $canManageForms)
                        class="w-full mt-2 px-3 py-2 text-xs font-semibold text-white bg-slate-900 rounded-lg hover:bg-slate-800 shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ __('Enregistrer le champ') }}
                </button>

                <button type="button" wire:click="deleteFormField({{ (int) $fb_selected_field_id }})" @disabled(! $canManageForms)
                        class="w-full mt-2 px-3 py-2 text-xs font-semibold text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ __('Supprimer le champ') }}
                </button>
            @else
                <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-3 text-[12px] text-slate-600">
                    {{ __('Clique sur un champ dans le canvas pour éditer ses propriétés.') }}
                </div>
            @endif

            <div class="border-t border-slate-100 pt-4 mt-4"></div>
            <button type="button" wire:click="deleteFormTemplate({{ (int) $fb_selected_template_id }})" @disabled(! $canManageForms)
                    class="w-full px-3 py-2 text-xs font-semibold text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                {{ __('Supprimer le formulaire') }}
            </button>
        @else
            <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-3 text-[12px] text-slate-600">
                {{ __('Crée ou sélectionne un formulaire à gauche.') }}
            </div>
        @endif
    </div>
</div>
