<div class="mx-auto w-full max-w-7xl 2xl:max-w-[90rem] min-[1920px]:max-w-[110rem] py-8 px-4 sm:px-6 lg:px-8" x-data="{ tab: 'branding' }">
    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ __('Paramètres') }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ __('Gérez la configuration de votre entreprise.') }}</p>
        </div>
        <a
            href="{{ route('dashboard') }}"
            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all"
        >
            <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
            {{ __('Retour') }}
        </a>
    </div>

    @if (session('settings_status') || $successMessage)
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-sm flex items-center gap-3">
            <iconify-icon icon="solar:check-circle-bold" width="22"></iconify-icon>
            <span>{{ $successMessage ?: session('settings_status') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 shadow-sm">
            <p class="font-semibold flex items-center gap-2 mb-2">
                <iconify-icon icon="solar:danger-triangle-bold" width="20"></iconify-icon>
                {{ __('Veuillez corriger les erreurs ci-dessous.') }}
            </p>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- LEFT SIDEBAR NAV -->
        <div class="lg:col-span-3 space-y-6">
            <nav class="space-y-1">
                @php
                    $navItemClass = "group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 w-full text-left";
                    $activeClass = "bg-white text-[var(--accent)] shadow-sm ring-1 ring-slate-200";
                    $inactiveClass = "text-slate-600 hover:bg-slate-50 hover:text-slate-900";
                @endphp

                <button type="button" class="{{ $navItemClass }}" :class="tab === 'branding' ? '{{ $activeClass }}' : '{{ $inactiveClass }}'" @click="tab = 'branding'">
                    <iconify-icon icon="solar:palette-bold-duotone" width="20" :class="tab === 'branding' ? 'text-[var(--accent)]' : 'text-slate-400 group-hover:text-slate-600'"></iconify-icon>
                    {{ __('Apparence') }}
                </button>

                <button type="button" class="{{ $navItemClass }}" :class="tab === 'tickets' ? '{{ $activeClass }}' : '{{ $inactiveClass }}'" @click="tab = 'tickets'">
                    <iconify-icon icon="solar:ticket-bold-duotone" width="20" :class="tab === 'tickets' ? 'text-[var(--accent)]' : 'text-slate-400 group-hover:text-slate-600'"></iconify-icon>
                    {{ __('Tickets') }}
                </button>

                <button type="button" class="{{ $navItemClass }}" :class="tab === 'categories' ? '{{ $activeClass }}' : '{{ $inactiveClass }}'" @click="tab = 'categories'">
                    <iconify-icon icon="solar:tag-bold-duotone" width="20" :class="tab === 'categories' ? 'text-[var(--accent)]' : 'text-slate-400 group-hover:text-slate-600'"></iconify-icon>
                    {{ __('Catégories') }}
                </button>

                <button type="button" class="{{ $navItemClass }}" :class="tab === 'priorities' ? '{{ $activeClass }}' : '{{ $inactiveClass }}'" @click="tab = 'priorities'">
                    <iconify-icon icon="solar:flag-bold-duotone" width="20" :class="tab === 'priorities' ? 'text-[var(--accent)]' : 'text-slate-400 group-hover:text-slate-600'"></iconify-icon>
                    {{ __('Priorités') }}
                </button>
                <button type="button" class="{{ $navItemClass }}" :class="tab === 'functions' ? '{{ $activeClass }}' : '{{ $inactiveClass }}'" @click="tab = 'functions'">
                    <iconify-icon icon="solar:user-id-bold-duotone" width="20" :class="tab === 'functions' ? 'text-[var(--accent)]' : 'text-slate-400 group-hover:text-slate-600'"></iconify-icon>
                    {{ __('Fonctions métier') }}
                </button>
                <button type="button" class="{{ $navItemClass }}" :class="tab === 'forms' ? '{{ $activeClass }}' : '{{ $inactiveClass }}'" @click="tab = 'forms'">
                    <iconify-icon icon="solar:clipboard-list-bold-duotone" width="20" :class="tab === 'forms' ? 'text-[var(--accent)]' : 'text-slate-400 group-hover:text-slate-600'"></iconify-icon>
                    {{ __('Formulaires') }}
                </button>

                <div class="pt-4 mt-4 border-t border-slate-200">
                    <button type="button" class="{{ $navItemClass }}" :class="tab === 'danger' ? 'bg-red-50 text-red-700 ring-1 ring-red-100' : 'text-slate-600 hover:bg-red-50 hover:text-red-700'" @click="tab = 'danger'">
                        <iconify-icon icon="solar:danger-triangle-bold-duotone" width="20" :class="tab === 'danger' ? 'text-red-600' : 'text-slate-400 group-hover:text-red-500'"></iconify-icon>
                        {{ __('Zone Danger') }}
                    </button>
                </div>
            </nav>

            <!-- Info Box -->
            <div class="rounded-2xl bg-slate-50 p-5 border border-slate-100">
                <div class="flex items-start gap-3">
                    <iconify-icon icon="solar:info-circle-bold" class="text-slate-400 mt-0.5" width="20"></iconify-icon>
                    <div>
                        <h4 class="text-sm font-semibold text-slate-900">{{ __('Besoin d\'aide ?') }}</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            {{ __('Seuls les administrateurs peuvent modifier ces paramètres. Contactez le support si vous avez des questions.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT CONTENT AREA -->
        <div class="lg:col-span-9 space-y-6">
            @if (! $canManage)
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 flex items-center gap-3">
                    <iconify-icon icon="solar:lock-keyhole-bold" width="20"></iconify-icon>
                    {{ __('Accès en lecture seule. Vous devez être administrateur pour modifier ces paramètres.') }}
                </div>
            @endif

            <!-- BRANDING TAB -->
            <div x-show="tab === 'branding'" x-cloak class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-900">{{ __('Apparence & Identité') }}</h2>
                    <p class="text-sm text-slate-500">{{ __('Personnalisez l\'apparence de votre espace de travail.') }}</p>
                </div>

                <form wire:submit.prevent="save" class="p-6 space-y-8">
                    <!-- Logo Section -->
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-4">{{ __('Logo de l\'entreprise') }}</h3>
                        <div class="flex items-start gap-6">
                            <div class="shrink-0 relative group">
                                <div class="h-24 w-24 rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 flex items-center justify-center overflow-hidden transition-colors hover:border-[var(--accent)] hover:bg-[var(--accent-soft)]/10">
                                    @if ($logo)
                                        <img src="{{ $logo->temporaryUrl() }}" class="h-full w-full object-cover" alt="Preview">
                                    @elseif ($currentLogoUrl)
                                        <img src="{{ $currentLogoUrl }}" class="h-full w-full object-cover" alt="Logo">
                                    @else
                                        <iconify-icon icon="solar:gallery-add-linear" class="text-slate-400 group-hover:text-[var(--accent)]" width="32"></iconify-icon>
                                    @endif
                                </div>
                                @if ($currentLogoUrl || $logo)
                                    <button type="button" wire:click="removeLogo" class="absolute -top-2 -right-2 h-6 w-6 rounded-full bg-red-100 text-red-600 flex items-center justify-center hover:bg-red-200 transition-colors shadow-sm" title="Supprimer">
                                        <iconify-icon icon="solar:trash-bin-trash-bold" width="14"></iconify-icon>
                                    </button>
                                @endif
                            </div>
                            <div class="flex-1 space-y-3">
                                <div class="flex items-center gap-3">
                                    <label for="logo-upload" class="cursor-pointer inline-flex items-center gap-2 rounded-xl bg-white border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                                        <iconify-icon icon="solar:upload-linear" width="18"></iconify-icon>
                                        {{ __('Télécharger un logo') }}
                                        <input id="logo-upload" type="file" class="hidden" accept="image/*" wire:model="logo" @disabled(! $canManage)>
                                    </label>
                                    <span class="text-xs text-slate-500">{{ __('PNG, JPG, GIF jusqu\'à 2MB') }}</span>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    {{ __('Ce logo apparaîtra sur votre page de connexion, vos emails et dans la barre latérale.') }}
                                </p>
                                <x-input-error :messages="$errors->get('logo')" />
                            </div>
                        </div>
                    </div>

                    <hr class="border-slate-100">

                    <!-- General Info -->
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <x-input-label for="org_name" value="Nom de l’entreprise" />
                            <input
                                id="org_name"
                                type="text"
                                wire:model="name"
                                required
                                @disabled(! $canManage)
                                class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all disabled:bg-slate-50 disabled:text-slate-500"
                            />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="org_slug" value="URL de l'espace (slug)" />
                            <div class="mt-1 flex rounded-xl shadow-sm">
                                <span class="inline-flex items-center rounded-l-xl border border-r-0 border-slate-200 bg-slate-50 px-3 text-sm text-slate-500">manexo.io/</span>
                                <input type="text" id="org_slug" wire:model="slug" class="block w-full min-w-0 flex-1 rounded-none rounded-r-xl border-slate-200 focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm" @disabled(! $canManage)>
                            </div>
                            <x-input-error :messages="$errors->get('slug')" class="mt-1" />
                        </div>
                    </div>

                    <!-- Colors -->
                    <div>
                        <x-input-label for="primary_color" value="Couleur principale" />
                        <div class="mt-2 flex items-center gap-4">
                            <div class="relative group cursor-pointer">
                                <input type="color" id="color-picker" class="absolute inset-0 h-full w-full opacity-0 cursor-pointer z-10" wire:model.live="primary_color" @disabled(! $canManage)>
                                <div class="h-11 w-11 rounded-xl shadow-sm border border-slate-200 flex items-center justify-center transition-transform group-hover:scale-105" style="background-color: {{ $primary_color ?? '#005F02' }};">
                                    <iconify-icon icon="solar:pen-new-square-linear" class="text-white opacity-50"></iconify-icon>
                                </div>
                            </div>
                            <div class="flex-1 max-w-xs">
                                <input
                                    id="primary_color"
                                    type="text"
                                    class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm font-mono uppercase focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all disabled:bg-slate-50 disabled:text-slate-500"
                                    wire:model.live="primary_color"
                                    maxlength="7"
                                    @disabled(! $canManage)
                                    placeholder="#000000"
                                />
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">{{ __('Utilisée pour les boutons, liens et éléments actifs. Cliquez sur le carré pour ouvrir le sélecteur.') }}</p>
                        <x-input-error :messages="$errors->get('primary_color')" class="mt-1" />
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="btn-primary" @disabled(! $canManage)>
                            <span wire:loading.remove>{{ __('Enregistrer les modifications') }}</span>
                            <span wire:loading><iconify-icon icon="solar:refresh-linear" class="animate-spin"></iconify-icon></span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- TICKETS TAB -->
            <div x-show="tab === 'tickets'" x-cloak class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-900">{{ __('Configuration des Tickets') }}</h2>
                    <p class="text-sm text-slate-500">{{ __('Définissez les règles par défaut pour les nouveaux tickets.') }}</p>
                </div>

                <form wire:submit.prevent="save" class="p-6 space-y-8">
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <x-input-label for="default_category" value="Catégorie par défaut" />
                            <div class="mt-1">
                                <x-select-input id="default_category" wire:model="default_category_id" :disabled="! $canManage">
                                    <option value="">{{ __('Sélectionner...') }}</option>
                                    @foreach ($categories as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </x-select-input>
                            </div>
                            <x-input-error :messages="$errors->get('default_category_id')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="default_priority" value="Priorité par défaut" />
                            <div class="mt-1">
                                <x-select-input id="default_priority" wire:model="default_priority_id" :disabled="! $canManage">
                                    <option value="">{{ __('Sélectionner...') }}</option>
                                    @foreach ($priorities as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </x-select-input>
                            </div>
                            <x-input-error :messages="$errors->get('default_priority_id')" class="mt-1" />
                        </div>
                    </div>

                    <hr class="border-slate-100">

                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-4">{{ __('Règles de gestion') }}</h3>
                        <div class="space-y-4">
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors cursor-pointer">
                                <input type="checkbox" wire:model="members_can_edit" class="mt-1 rounded border-slate-300 text-[var(--accent)] focus:ring-[var(--accent)]" @disabled(! $canManage)>
                                <div>
                                    <span class="block text-sm font-medium text-slate-900">{{ __('Autoriser l\'édition') }}</span>
                                    <span class="block text-xs text-slate-500 mt-0.5">{{ __('Les membres peuvent modifier leurs propres tickets après création.') }}</span>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors cursor-pointer">
                                <input type="checkbox" wire:model="members_can_delete" class="mt-1 rounded border-slate-300 text-[var(--accent)] focus:ring-[var(--accent)]" @disabled(! $canManage)>
                                <div>
                                    <span class="block text-sm font-medium text-slate-900">{{ __('Autoriser la suppression') }}</span>
                                    <span class="block text-xs text-slate-500 mt-0.5">{{ __('Les membres peuvent supprimer leurs propres tickets (non recommandé).') }}</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <hr class="border-slate-100">

                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-4">{{ __('Fermeture automatique') }}</h3>
                        <div class="max-w-xs">
                            <x-input-label for="auto_close_days" value="Fermer après X jours d'inactivité" />
                            <input
                                id="auto_close_days"
                                type="number"
                                wire:model="auto_close_days"
                                min="1"
                                max="365"
                                placeholder="Désactivé"
                                class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all disabled:bg-slate-50 disabled:text-slate-500"
                                @disabled(! $canManage)
                            />
                            <p class="text-xs text-slate-500 mt-1.5">{{ __('Laissez vide pour désactiver la fermeture automatique.') }}</p>
                            <x-input-error :messages="$errors->get('auto_close_days')" class="mt-1" />
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="btn-primary" @disabled(! $canManage)>
                            {{ __('Enregistrer') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- CATEGORIES TAB -->
            <div x-show="tab === 'categories'" x-cloak class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-900">{{ __('Gestion des Catégories') }}</h2>
                    <p class="text-sm text-slate-500">{{ __('Créez et organisez les catégories pour classer vos tickets.') }}</p>
                </div>

                <div class="p-6 space-y-6">
                    {{-- Create form --}}
                    @if ($canManage)
                        <form wire:submit.prevent="createCategory" class="flex items-end gap-3">
                            <div class="flex-1">
                                <x-input-label for="new_cat_name" value="Nom de la catégorie" />
                                <input
                                    id="new_cat_name"
                                    type="text"
                                    wire:model="newCategoryName"
                                    placeholder="Ex : Support technique"
                                    class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                                />
                                <x-input-error :messages="$errors->get('newCategoryName')" class="mt-1" />
                            </div>
                            <button type="submit" class="shrink-0 inline-flex items-center gap-2 rounded-xl bg-[var(--accent)] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition-all">
                                <iconify-icon icon="solar:add-circle-linear" width="18"></iconify-icon>
                                {{ __('Ajouter') }}
                            </button>
                        </form>
                        <hr class="border-slate-100">
                    @endif

                    {{-- List --}}
                    @if ($categories->isEmpty())
                        <div class="py-8 text-center">
                            <div class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 mb-3">
                                <iconify-icon icon="solar:tag-linear" width="28" class="text-slate-400"></iconify-icon>
                            </div>
                            <p class="text-sm text-slate-500">{{ __('Aucune catégorie. Créez-en une pour commencer.') }}</p>
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach ($categories as $cat)
                                <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-100 hover:border-slate-200 transition-colors group"
                                     wire:key="cat-{{ $cat->id }}">
                                    @if ($editingCategoryId === $cat->id)
                                        {{-- Inline edit --}}
                                        <form wire:submit.prevent="updateCategory" class="flex-1 flex items-center gap-3">
                                            <input
                                                type="text"
                                                wire:model="editingCategoryName"
                                                class="flex-1 rounded-lg border-slate-200 py-1.5 px-2.5 text-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                                autofocus
                                            />
                                            <button type="submit" class="text-[var(--accent)] hover:opacity-80" title="Enregistrer">
                                                <iconify-icon icon="solar:check-circle-bold" width="20"></iconify-icon>
                                            </button>
                                            <button type="button" wire:click="cancelEditCategory" class="text-slate-400 hover:text-slate-600" title="Annuler">
                                                <iconify-icon icon="solar:close-circle-bold" width="20"></iconify-icon>
                                            </button>
                                        </form>
                                        <x-input-error :messages="$errors->get('editingCategoryName')" class="mt-1" />
                                    @else
                                        {{-- Display --}}
                                        <div class="flex-1 min-w-0">
                                            <span class="text-sm font-medium text-slate-900">{{ $cat->name }}</span>
                                            <span class="ml-2 text-[10px] font-mono text-slate-400 bg-slate-50 px-1.5 py-0.5 rounded">{{ $cat->slug }}</span>
                                        </div>

                                        {{-- Active toggle --}}
                                        <button
                                            type="button"
                                            wire:click="toggleCategory({{ $cat->id }})"
                                            class="shrink-0 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold transition-colors {{ $cat->is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}"
                                            @disabled(! $canManage)
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full {{ $cat->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                            {{ $cat->is_active ? __('Actif') : __('Inactif') }}
                                        </button>

                                        @if ($canManage)
                                            {{-- Edit --}}
                                            <button type="button" wire:click="startEditCategory({{ $cat->id }})" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-[var(--accent)] transition-all" title="Modifier">
                                                <iconify-icon icon="solar:pen-2-linear" width="16"></iconify-icon>
                                            </button>
                                            {{-- Delete --}}
                                            <button type="button" wire:click="deleteCategory({{ $cat->id }})" wire:confirm="{{ __('Supprimer cette catégorie ?') }}" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-red-600 transition-all" title="Supprimer">
                                                <iconify-icon icon="solar:trash-bin-trash-linear" width="16"></iconify-icon>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- PRIORITIES TAB -->
            <div x-show="tab === 'priorities'" x-cloak class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-900">{{ __('Niveaux de Priorité') }}</h2>
                    <p class="text-sm text-slate-500">{{ __('Définissez les niveaux d\'urgence pour vos tickets.') }}</p>
                </div>

                <div class="p-6 space-y-6">
                    {{-- Create form --}}
                    @if ($canManage)
                        <form wire:submit.prevent="createPriority" class="flex items-end gap-3">
                            <div class="flex-1">
                                <x-input-label for="new_prio_name" value="Nom" />
                                <input
                                    id="new_prio_name"
                                    type="text"
                                    wire:model="newPriorityName"
                                    placeholder="Ex : Urgente"
                                    class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                                />
                                <x-input-error :messages="$errors->get('newPriorityName')" class="mt-1" />
                            </div>
                            <div class="w-28">
                                <x-input-label for="new_prio_level" value="Niveau" />
                                <input
                                    id="new_prio_level"
                                    type="number"
                                    wire:model="newPriorityLevel"
                                    min="0"
                                    placeholder="0"
                                    class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                                />
                                <x-input-error :messages="$errors->get('newPriorityLevel')" class="mt-1" />
                            </div>
                            <button type="submit" class="shrink-0 inline-flex items-center gap-2 rounded-xl bg-[var(--accent)] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition-all">
                                <iconify-icon icon="solar:add-circle-linear" width="18"></iconify-icon>
                                {{ __('Ajouter') }}
                            </button>
                        </form>
                        <hr class="border-slate-100">
                    @endif

                    {{-- List --}}
                    @if ($priorities->isEmpty())
                        <div class="py-8 text-center">
                            <div class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 mb-3">
                                <iconify-icon icon="solar:flag-linear" width="28" class="text-slate-400"></iconify-icon>
                            </div>
                            <p class="text-sm text-slate-500">{{ __('Aucune priorité. Créez-en une pour commencer.') }}</p>
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach ($priorities as $prio)
                                <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-100 hover:border-slate-200 transition-colors group"
                                     wire:key="prio-{{ $prio->id }}">
                                    @if ($editingPriorityId === $prio->id)
                                        {{-- Inline edit --}}
                                        <form wire:submit.prevent="updatePriority" class="flex-1 flex items-center gap-3">
                                            <input
                                                type="text"
                                                wire:model="editingPriorityName"
                                                class="flex-1 rounded-lg border-slate-200 py-1.5 px-2.5 text-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                                autofocus
                                            />
                                            <input
                                                type="number"
                                                wire:model="editingPriorityLevel"
                                                min="0"
                                                class="w-20 rounded-lg border-slate-200 py-1.5 px-2.5 text-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                            />
                                            <button type="submit" class="text-[var(--accent)] hover:opacity-80" title="Enregistrer">
                                                <iconify-icon icon="solar:check-circle-bold" width="20"></iconify-icon>
                                            </button>
                                            <button type="button" wire:click="cancelEditPriority" class="text-slate-400 hover:text-slate-600" title="Annuler">
                                                <iconify-icon icon="solar:close-circle-bold" width="20"></iconify-icon>
                                            </button>
                                        </form>
                                        <x-input-error :messages="$errors->get('editingPriorityName')" class="mt-1" />
                                        <x-input-error :messages="$errors->get('editingPriorityLevel')" class="mt-1" />
                                    @else
                                        {{-- Display --}}
                                        <div class="flex-1 min-w-0 flex items-center gap-3">
                                            <span class="text-sm font-medium text-slate-900">{{ $prio->name }}</span>
                                            <span class="inline-flex items-center justify-center h-6 min-w-[24px] rounded-md bg-slate-100 px-1.5 text-[11px] font-bold text-slate-600 tabular-nums">{{ $prio->level }}</span>
                                        </div>

                                        {{-- Active toggle --}}
                                        <button
                                            type="button"
                                            wire:click="togglePriority({{ $prio->id }})"
                                            class="shrink-0 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold transition-colors {{ $prio->is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}"
                                            @disabled(! $canManage)
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full {{ $prio->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                            {{ $prio->is_active ? __('Actif') : __('Inactif') }}
                                        </button>

                                        @if ($canManage)
                                            {{-- Edit --}}
                                            <button type="button" wire:click="startEditPriority({{ $prio->id }})" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-[var(--accent)] transition-all" title="Modifier">
                                                <iconify-icon icon="solar:pen-2-linear" width="16"></iconify-icon>
                                            </button>
                                            {{-- Delete --}}
                                            <button type="button" wire:click="deletePriority({{ $prio->id }})" wire:confirm="{{ __('Supprimer cette priorité ?') }}" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-red-600 transition-all" title="Supprimer">
                                                <iconify-icon icon="solar:trash-bin-trash-linear" width="16"></iconify-icon>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- FONCTIONS MÉTIER TAB -->
            <div x-show="tab === 'functions'" x-cloak class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-900">{{ __('Fonctions métier') }}</h2>
                    <p class="text-sm text-slate-500">{{ __('Postes dans l\'entreprise (ex: Informaticien, RH, Comptable). Distinct du rôle système (permissions).') }}</p>
                </div>
                <div class="p-6 space-y-6">
                    @if ($canManage)
                        <form wire:submit.prevent="createFunction" class="flex items-end gap-3">
                            <div class="flex-1">
                                <x-input-label for="new_function_name" value="{{ __('Nom de la fonction') }}" />
                                <input id="new_function_name" type="text" wire:model="newFunctionName" placeholder="Ex : Informaticien, RH, Support niveau 2" class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all" />
                                <x-input-error :messages="$errors->get('newFunctionName')" class="mt-1" />
                            </div>
                            <button type="submit" class="shrink-0 inline-flex items-center gap-2 rounded-xl bg-[var(--accent)] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition-all">
                                <iconify-icon icon="solar:add-circle-linear" width="18"></iconify-icon>
                                {{ __('Ajouter') }}
                            </button>
                        </form>
                        <hr class="border-slate-100">
                    @endif
                    @if ($organizationFunctions->isEmpty())
                        <div class="py-8 text-center">
                            <div class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 mb-3">
                                <iconify-icon icon="solar:user-id-linear" width="28" class="text-slate-400"></iconify-icon>
                            </div>
                            <p class="text-sm text-slate-500">{{ __('Aucune fonction. Créez-en une pour attribuer des postes aux membres.') }}</p>
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach ($organizationFunctions as $fn)
                                <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-100 hover:border-slate-200 transition-colors group" wire:key="fn-{{ $fn->id }}">
                                    @if ($editingFunctionId === $fn->id)
                                        <form wire:submit.prevent="updateFunction" class="flex-1 flex items-center gap-3">
                                            <input type="text" wire:model="editingFunctionName" class="flex-1 rounded-lg border-slate-200 py-1.5 px-2.5 text-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]" autofocus />
                                            <button type="submit" class="text-[var(--accent)] hover:opacity-80" title="{{ __('Enregistrer') }}">
                                                <iconify-icon icon="solar:check-circle-bold" width="20"></iconify-icon>
                                            </button>
                                            <button type="button" wire:click="cancelEditFunction" class="text-slate-400 hover:text-slate-600" title="{{ __('Annuler') }}">
                                                <iconify-icon icon="solar:close-circle-bold" width="20"></iconify-icon>
                                            </button>
                                        </form>
                                        <x-input-error :messages="$errors->get('editingFunctionName')" class="mt-1" />
                                    @else
                                        <span class="flex-1 text-sm font-medium text-slate-900">{{ $fn->name }}</span>
                                        @if ($canManage)
                                            <button type="button" wire:click="startEditFunction({{ $fn->id }})" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-[var(--accent)] transition-all" title="{{ __('Modifier') }}">
                                                <iconify-icon icon="solar:pen-2-linear" width="16"></iconify-icon>
                                            </button>
                                            <button type="button" wire:click="deleteFunction({{ $fn->id }})" wire:confirm="{{ __('Supprimer cette fonction ?') }}" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-red-600 transition-all" title="{{ __('Supprimer') }}">
                                                <iconify-icon icon="solar:trash-bin-trash-linear" width="16"></iconify-icon>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div x-show="tab === 'forms'" x-cloak class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-8 flex flex-col items-center text-center">
                    <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-[var(--accent-soft)] mb-4 text-[var(--accent)]">
                        <iconify-icon icon="solar:clipboard-list-bold-duotone" width="32"></iconify-icon>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">{{ __('Éditeur de Formulaires') }}</h3>
                    <p class="text-slate-500 mt-2 max-w-lg mx-auto mb-8">{{ __('Créez des formulaires de ticket personnalisés avec des champs spécifiques pour chaque type de demande.') }}</p>

                    <a href="{{ route('admin.forms') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-6 py-3 text-sm font-bold text-white shadow-lg hover:bg-slate-800 hover:-translate-y-0.5 transition-all">
                        <iconify-icon icon="solar:magic-stick-3-bold-duotone" width="20"></iconify-icon>
                        {{ __('Ouvrir le constructeur') }}
                    </a>
                </div>
            </div>

            <!-- DANGER ZONE -->
            <div x-show="tab === 'danger'" x-cloak class="bg-red-50 rounded-2xl border border-red-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-red-100 bg-red-100/50">
                    <h2 class="text-lg font-bold text-red-900">{{ __('Zone de Danger') }}</h2>
                    <p class="text-sm text-red-700">{{ __('Attention, ces actions sont irréversibles.') }}</p>
                </div>
                <div class="p-6">
                    <div class="rounded-xl bg-white p-6 border border-red-100">
                        <h3 class="text-base font-bold text-slate-900">{{ __('Supprimer l\'entreprise') }}</h3>
                        <p class="text-sm text-slate-500 mt-1 mb-4">{{ __('Cela supprimera définitivement tous les tickets, membres et données associés à cet espace de travail.') }}</p>

                        <div class="flex items-end gap-4">
                            <div class="flex-1">
                                <x-input-label for="confirm_delete" value="Pour confirmer, tapez le nom de l'entreprise" />
                                <x-text-input id="confirm_delete" type="text" class="mt-1 w-full" wire:model="dangerConfirmName" placeholder="{{ $org?->name }}" :disabled="! $isOwner" />
                            </div>
                            <button
                                type="button"
                                wire:click="deleteOrganization"
                                class="h-[42px] px-4 rounded-xl bg-red-600 text-white text-sm font-bold shadow-md hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                @disabled(! $isOwner || $dangerConfirmName !== $org?->name)
                            >
                                {{ __('Supprimer définitivement') }}
                            </button>
                        </div>
                        @if(!$isOwner)
                            <p class="text-xs text-red-600 mt-2 font-medium">{{ __('Seul le propriétaire peut effectuer cette action.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
