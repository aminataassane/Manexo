{{-- LEFT: Palette + Templates --}}
<div class="w-64 border-r border-slate-200 bg-white flex flex-col">
    <div class="p-4 border-b border-slate-100">
        <div class="relative">
            <iconify-icon icon="solar:magnifer-linear" class="absolute left-2.5 top-2.5 text-slate-400 text-sm"></iconify-icon>
            <input type="text" placeholder="{{ __('Rechercher (bientôt)') }}" class="w-full bg-slate-50 text-xs py-2 pl-8 pr-3 rounded-lg border-0 ring-1 ring-slate-200 focus:ring-2 focus:ring-slate-900 focus:bg-white transition-all placeholder:text-slate-400" disabled>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-5">
        <div>
            <h3 class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-3">{{ __('Champs de base') }}</h3>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" wire:click="quickAddField('text')" @disabled(! $canManageForms || ! $fb_selected_template_id)
                        class="cursor-pointer hover:bg-slate-50 hover:border-slate-300 border border-slate-200 rounded-lg p-3 flex flex-col items-center justify-center gap-2 text-center transition-all group disabled:opacity-50 disabled:cursor-not-allowed">
                    <iconify-icon icon="solar:text-field-linear" class="text-slate-500 group-hover:text-slate-900 text-xl"></iconify-icon>
                    <span class="text-[10px] font-medium text-slate-600 group-hover:text-slate-900">{{ __('Texte') }}</span>
                </button>
                <button type="button" wire:click="quickAddField('textarea')" @disabled(! $canManageForms || ! $fb_selected_template_id)
                        class="cursor-pointer hover:bg-slate-50 hover:border-slate-300 border border-slate-200 rounded-lg p-3 flex flex-col items-center justify-center gap-2 text-center transition-all group disabled:opacity-50 disabled:cursor-not-allowed">
                    <iconify-icon icon="solar:text-square-linear" class="text-slate-500 group-hover:text-slate-900 text-xl"></iconify-icon>
                    <span class="text-[10px] font-medium text-slate-600 group-hover:text-slate-900">{{ __('Paragraphe') }}</span>
                </button>
                <button type="button" wire:click="quickAddField('select')" @disabled(! $canManageForms || ! $fb_selected_template_id)
                        class="cursor-pointer hover:bg-slate-50 hover:border-slate-300 border border-slate-200 rounded-lg p-3 flex flex-col items-center justify-center gap-2 text-center transition-all group disabled:opacity-50 disabled:cursor-not-allowed">
                    <iconify-icon icon="solar:list-arrow-down-linear" class="text-slate-500 group-hover:text-slate-900 text-xl"></iconify-icon>
                    <span class="text-[10px] font-medium text-slate-600 group-hover:text-slate-900">{{ __('Sélection') }}</span>
                </button>
                <button type="button" wire:click="quickAddField('checkbox')" @disabled(! $canManageForms || ! $fb_selected_template_id)
                        class="cursor-pointer hover:bg-slate-50 hover:border-slate-300 border border-slate-200 rounded-lg p-3 flex flex-col items-center justify-center gap-2 text-center transition-all group disabled:opacity-50 disabled:cursor-not-allowed">
                    <iconify-icon icon="solar:check-square-linear" class="text-slate-500 group-hover:text-slate-900 text-xl"></iconify-icon>
                    <span class="text-[10px] font-medium text-slate-600 group-hover:text-slate-900">{{ __('Case') }}</span>
                </button>
            </div>
        </div>

        <div>
            <h3 class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-3">{{ __('Avancé') }}</h3>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" wire:click="quickAddField('date')" @disabled(! $canManageForms || ! $fb_selected_template_id)
                        class="cursor-pointer hover:bg-slate-50 hover:border-slate-300 border border-slate-200 rounded-lg p-3 flex flex-col items-center justify-center gap-2 text-center transition-all group disabled:opacity-50 disabled:cursor-not-allowed">
                    <iconify-icon icon="solar:calendar-linear" class="text-slate-500 group-hover:text-slate-900 text-xl"></iconify-icon>
                    <span class="text-[10px] font-medium text-slate-600 group-hover:text-slate-900">{{ __('Date') }}</span>
                </button>
                <button type="button" wire:click="quickAddField('number')" @disabled(! $canManageForms || ! $fb_selected_template_id)
                        class="cursor-pointer hover:bg-slate-50 hover:border-slate-300 border border-slate-200 rounded-lg p-3 flex flex-col items-center justify-center gap-2 text-center transition-all group disabled:opacity-50 disabled:cursor-not-allowed">
                    <iconify-icon icon="solar:ruler-linear" class="text-slate-500 group-hover:text-slate-900 text-xl"></iconify-icon>
                    <span class="text-[10px] font-medium text-slate-600 group-hover:text-slate-900">{{ __('Nombre') }}</span>
                </button>
            </div>
        </div>

        <div class="border-t border-slate-100 pt-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">{{ __('Formulaires') }}</h3>
                <span class="text-[10px] text-slate-400">{{ $formTemplates->count() }}</span>
            </div>
            <div class="space-y-1">
                @foreach ($formTemplates as $tpl)
                    <button type="button" wire:click="selectTemplate({{ $tpl->id }})"
                            class="w-full text-left px-3 py-2 rounded-lg border transition-all"
                            @class([
                                'bg-white border-slate-200 hover:bg-slate-50' => (int) $fb_selected_template_id !== (int) $tpl->id,
                                'bg-slate-900 border-slate-900 text-white' => (int) $fb_selected_template_id === (int) $tpl->id,
                            ])>
                        <div class="text-[12px] font-semibold truncate">{{ $tpl->name }}</div>
                        <div class="text-[10px] opacity-80 truncate">
                            {{ $tpl->category?->name ?? __('Toutes catégories') }}
                            ·
                            {{ $tpl->targetUser ? __('Réservé: :name', ['name' => $tpl->targetUser->name]) : __('Public') }}
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="border-t border-slate-100 pt-4">
            <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-2">{{ __('Nouveau formulaire') }}</div>
            <div class="space-y-2">
                <input type="text" wire:model="fb_template_name" placeholder="{{ __('Ex: Support technique') }}"
                       @disabled(! $canManageForms)
                       class="w-full bg-slate-50 text-xs py-2 px-3 rounded-lg border-0 ring-1 ring-slate-200 focus:ring-2 focus:ring-slate-900 focus:bg-white transition-all placeholder:text-slate-400 disabled:opacity-50">
                <x-input-error :messages="$errors->get('fb_template_name')" />
                <div class="relative">
                    <select wire:model="fb_template_category_id" @disabled(! $canManageForms)
                            class="block w-full bg-slate-50 text-xs h-9 rounded-lg border-0 ring-1 ring-slate-200 focus:ring-2 focus:ring-slate-900 focus:bg-white appearance-none pr-8 transition disabled:opacity-50">
                        <option value="">{{ __('Toutes catégories') }}</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                    <iconify-icon icon="solar:alt-arrow-down-linear" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" width="14"></iconify-icon>
                </div>
                <div class="relative">
                    <select wire:model="fb_template_target_user_id" @disabled(! $canManageForms)
                            class="block w-full bg-slate-50 text-xs h-9 rounded-lg border-0 ring-1 ring-slate-200 focus:ring-2 focus:ring-slate-900 focus:bg-white appearance-none pr-8 transition disabled:opacity-50">
                        <option value="">{{ __('Public (tous)') }}</option>
                        @foreach ($members as $m)
                            <option value="{{ $m->user_id }}">{{ $m->user?->name ?? '—' }}</option>
                        @endforeach
                    </select>
                    <iconify-icon icon="solar:alt-arrow-down-linear" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" width="14"></iconify-icon>
                </div>
                <label class="flex items-center justify-between text-xs text-slate-700">
                    <span>{{ __('Actif') }}</span>
                    <input type="checkbox" wire:model="fb_template_active" @disabled(! $canManageForms) class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900" />
                </label>
                <button type="button" wire:click="createFormTemplate"
                        @disabled(! $canManageForms || trim($fb_template_name) === '')
                        class="w-full px-3 py-2 text-xs font-semibold text-white bg-slate-900 rounded-lg hover:bg-slate-800 shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ __('Créer') }}
                </button>
            </div>
        </div>
    </div>
</div>
