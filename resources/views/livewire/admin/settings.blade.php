<div class="w-full max-w-full min-w-0 mx-auto" x-data="{ tab: $wire.entangle('activeTab').live }">
    {{-- ═══ HEADER ═══ --}}
    <div class="page-header">
        <div class="min-w-0">
            <h1 class="page-title">{{ __('settings.title') }}</h1>
            <p class="page-subtitle">{{ __('settings.subtitle') }}</p>
        </div>
        <div class="page-actions">
            <a
                href="{{ route('dashboard') }}"
                wire:navigate
                class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all touch-target sm:min-h-0 sm:min-w-0"
                style="border: 1px solid #e2e8f0;"
            >
                <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
                {{ __('settings.back') }}
            </a>
        </div>
    </div>

    @if (session('settings_status') || $successMessage)
        <div class="mb-6 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-800 shadow-sm flex items-center gap-3" style="border: 1px solid #a7f3d0;">
            <iconify-icon icon="solar:check-circle-bold" width="22"></iconify-icon>
            <span>{{ $successMessage ?: session('settings_status') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-xl bg-red-50 p-4 text-sm text-red-800 shadow-sm" style="border: 1px solid #fecaca;">
            <p class="font-semibold flex items-center gap-2 mb-2">
                <iconify-icon icon="solar:danger-triangle-bold" width="20"></iconify-icon>
                {{ __('settings.fix_errors') }}
            </p>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $navItems = [
            ['key' => 'branding', 'icon' => 'solar:palette-bold-duotone', 'label' => __('settings.appearance')],
            ['key' => 'tickets', 'icon' => 'solar:ticket-bold-duotone', 'label' => __('menu.tickets')],
            ['key' => 'categories', 'icon' => 'solar:tag-bold-duotone', 'label' => __('settings.categories')],
            ['key' => 'groups', 'icon' => 'solar:widget-5-bold-duotone', 'label' => __('settings.groups')],
            ['key' => 'priorities', 'icon' => 'solar:flag-bold-duotone', 'label' => __('settings.priorities')],
            ['key' => 'functions', 'icon' => 'solar:user-id-bold-duotone', 'label' => __('settings.business_functions')],
            ['key' => 'forms', 'icon' => 'solar:clipboard-list-bold-duotone', 'label' => __('settings.forms')],
            ['key' => 'email', 'icon' => 'solar:letter-bold-duotone', 'label' => 'Email'],
            ['key' => 'sla', 'icon' => 'solar:alarm-bold-duotone', 'label' => 'SLA'],
            ['key' => 'automations', 'icon' => 'solar:bolt-circle-bold-duotone', 'label' => 'Automatisations'],
            ['key' => 'knowledge_base', 'icon' => 'solar:book-2-bold-duotone', 'label' => 'Base de connaissances'],
            ['key' => 'api', 'icon' => 'solar:programming-bold-duotone', 'label' => 'API'],
            ['key' => 'roles', 'icon' => 'solar:shield-keyhole-bold-duotone', 'label' => __('settings.roles_permissions')],
            ['key' => 'maintenance', 'icon' => 'solar:tuning-2-bold-duotone', 'label' => __('settings.maintenance')],
            ['key' => 'danger', 'icon' => 'solar:danger-triangle-bold-duotone', 'label' => __('settings.danger_zone')],
        ];
    @endphp

    {{-- ═══ MOBILE/TABLET HORIZONTAL NAV ═══ --}}
    <div class="lg:hidden mb-4 -mx-1">
        <div
            class="overflow-x-auto scrollbar-hide overscroll-x-contain [touch-action:pan-x]"
            x-ref="mobileNav"
        >
            <div class="flex gap-1.5 px-1 pb-1 min-w-max">
                @foreach ($navItems as $nav)
                    <button type="button"
                        wire:click="setActiveTab('{{ $nav['key'] }}')"
                        @click="tab = '{{ $nav['key'] }}'; $nextTick(() => $el.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' }))"
                        class="shrink-0 inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-sm font-medium whitespace-nowrap transition-all"
                        :class="tab === '{{ $nav['key'] }}'
                            ? '{{ $nav['key'] === 'danger' ? 'bg-red-50 text-red-700 shadow-sm' : 'bg-[var(--accent)]/10 text-[var(--accent)] shadow-sm' }}'
                            : '{{ $nav['key'] === 'danger' ? 'text-slate-500 hover:bg-red-50 hover:text-red-700' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700' }}'"
                    >
                        <iconify-icon icon="{{ $nav['icon'] }}" width="16" class="shrink-0"
                            :class="tab === '{{ $nav['key'] }}'
                                ? '{{ $nav['key'] === 'danger' ? 'text-red-600' : 'text-[var(--accent)]' }}'
                                : 'text-slate-400'"
                        ></iconify-icon>
                        {{ $nav['label'] }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- ── LEFT SIDEBAR NAV (desktop only) ── --}}
        <div class="hidden lg:block lg:col-span-3 space-y-4 sm:space-y-6">
            <div class="sidebar-panel sticky top-24">
                <div class="sidebar-panel-body">
                    @foreach ($navItems as $nav)
                        @if ($nav['key'] === 'danger')
                            <div class="pt-3 mt-3" style="border-top: 1px solid #f1f5f9;">
                                <button type="button" wire:click="setActiveTab('danger')" @click="tab = 'danger'"
                                    class="sidebar-item" :class="tab === 'danger' ? 'bg-red-50 text-red-700' : 'text-slate-600 hover:bg-red-50 hover:text-red-700'">
                                    <span class="flex items-center gap-2.5">
                                        <iconify-icon icon="solar:danger-triangle-bold-duotone" width="18" :class="tab === 'danger' ? 'text-red-600' : 'text-slate-400'" class="shrink-0"></iconify-icon>
                                        {{ __('settings.danger_zone') }}
                                    </span>
                                </button>
                            </div>
                        @else
                            <button type="button" wire:click="setActiveTab('{{ $nav['key'] }}')" @click="tab = '{{ $nav['key'] }}'"
                                class="sidebar-item" :class="tab === '{{ $nav['key'] }}' ? 'sidebar-item-active' : 'sidebar-item-default'">
                                <span class="flex items-center gap-2.5">
                                    <iconify-icon icon="{{ $nav['icon'] }}" width="18" :class="tab === '{{ $nav['key'] }}' ? 'text-[var(--accent)]' : 'text-slate-400'" class="shrink-0"></iconify-icon>
                                    {{ $nav['label'] }}
                                </span>
                            </button>
                        @endif
                    @endforeach
                </div>
                <div wire:loading.flex wire:target="setActiveTab" class="items-center justify-center gap-2 px-3 py-2 text-xs text-slate-500">
                    <iconify-icon icon="solar:refresh-linear" class="animate-spin" width="14"></iconify-icon>
                    Chargement...
                </div>
            </div>

            {{-- Info Box --}}
            <div class="rounded-2xl bg-slate-50 p-5" style="border: 1px solid #f1f5f9;">
                <div class="flex items-start gap-3">
                    <iconify-icon icon="solar:info-circle-bold" class="text-slate-400 mt-0.5 shrink-0" width="20"></iconify-icon>
                    <div>
                        <h4 class="text-sm font-semibold text-slate-900">{{ __('settings.need_help') }}</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            {{ __('settings.help_text') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT CONTENT AREA -->
        <div class="lg:col-span-9 space-y-6">
            @if($loadStage < 2)
                <div class="content-card p-8 min-h-[24rem] animate-pulse space-y-6">
                    <div class="h-7 bg-slate-200 rounded-lg w-2/5 max-w-xs"></div>
                    <div class="space-y-3">
                        <div class="h-4 bg-slate-100 rounded w-full"></div>
                        <div class="h-4 bg-slate-100 rounded w-11/12"></div>
                        <div class="h-4 bg-slate-100 rounded w-4/5"></div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4 pt-4">
                        <div class="h-32 bg-slate-50 rounded-xl" style="border: 1px solid #f1f5f9;"></div>
                        <div class="h-32 bg-slate-50 rounded-xl" style="border: 1px solid #f1f5f9;"></div>
                    </div>
                </div>
            @else
            @if (! $canManage)
                <div class="rounded-xl bg-amber-50 p-4 text-sm text-amber-800 flex items-center gap-3" style="border: 1px solid #fde68a;">
                    <iconify-icon icon="solar:lock-keyhole-bold" width="20" class="shrink-0"></iconify-icon>
                    {{ __('settings.read_only') }}
                </div>
            @endif

            <!-- BRANDING TAB -->
            @if ($activeTab === 'branding')
            <div class="content-card">
                <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                    <h2 class="text-lg font-bold text-slate-900">{{ __('settings.branding_title') }}</h2>
                    <p class="text-sm text-slate-500">{{ __('settings.branding_subtitle') }}</p>
                </div>

                <form wire:submit.prevent="save" class="p-6 space-y-8">
                    <!-- Logo Section -->
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-4">{{ __('settings.company_logo') }}</h3>
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
                                    <button type="button" @click="$dispatch('confirm-action', { title: '{{ __('Supprimer') }}', message: '{{ __('Supprimer le logo de l\u0027organisation ?') }}', confirmLabel: '{{ __('Supprimer') }}', variant: 'danger', onConfirm: () => $wire.removeLogo() })" class="absolute -top-2 -right-2 h-6 w-6 rounded-full bg-red-100 text-red-600 flex items-center justify-center hover:bg-red-200 transition-colors shadow-sm" title="{{ __('settings.remove') }}">
                                        <iconify-icon icon="solar:trash-bin-trash-bold" width="14"></iconify-icon>
                                    </button>
                                @endif
                            </div>
                            <div class="flex-1 space-y-3">
                                <div class="flex items-center gap-3">
                                    <label for="logo-upload" class="cursor-pointer inline-flex items-center gap-2 rounded-xl bg-white border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                                        <iconify-icon icon="solar:upload-linear" width="18"></iconify-icon>
                                        {{ __('settings.upload_logo') }}
                                        <input id="logo-upload" type="file" class="hidden" accept="image/*" wire:model="logo" @disabled(! $canManage)>
                                    </label>
                                    <span class="text-xs text-slate-500">{{ __('settings.logo_formats') }}</span>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    {{ __('settings.logo_usage') }}
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
                                wire:model.blur="name"
                                required
                                @disabled(! $canManage)
                                class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all disabled:bg-slate-50 disabled:text-slate-500"
                            />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="org_slug" :value="__('settings.space_slug')" />
                            <div class="mt-1 flex rounded-xl shadow-sm">
                                <span class="inline-flex items-center rounded-l-xl border border-r-0 border-slate-200 bg-slate-50 px-3 text-sm text-slate-500">manexo.io/</span>
                                <input type="text" id="org_slug" wire:model.blur="slug" class="block w-full min-w-0 flex-1 rounded-none rounded-r-xl border-slate-200 focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm" @disabled(! $canManage)>
                            </div>
                            <x-input-error :messages="$errors->get('slug')" class="mt-1" />
                        </div>
                    </div>

                    <!-- Colors -->
                    <div>
                        <x-input-label for="primary_color" :value="__('settings.primary_color')" />
                        <div class="mt-2 flex items-center gap-4">
                            <div class="relative group cursor-pointer">
                                <input type="color" id="color-picker" class="absolute inset-0 h-full w-full opacity-0 cursor-pointer z-10" wire:model.debounce.300ms="primary_color" @disabled(! $canManage)>
                                <div class="h-11 w-11 rounded-xl shadow-sm border border-slate-200 flex items-center justify-center transition-transform group-hover:scale-105" style="background-color: {{ $primary_color ?? '#005F02' }};">
                                    <iconify-icon icon="solar:pen-new-square-linear" class="text-white opacity-50"></iconify-icon>
                                </div>
                            </div>
                            <div class="flex-1 max-w-xs">
                                <input
                                    id="primary_color"
                                    type="text"
                                    class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm font-mono uppercase focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all disabled:bg-slate-50 disabled:text-slate-500"
                                    wire:model.debounce.300ms="primary_color"
                                    maxlength="7"
                                    @disabled(! $canManage)
                                    placeholder="#000000"
                                />
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">{{ __('settings.primary_color_help') }}</p>
                        <x-input-error :messages="$errors->get('primary_color')" class="mt-1" />
                    </div>

                    <div class="pt-4 flex justify-end">
                        <x-manexo.action-button type="submit" wire-target="save" variant="primary" class="btn-primary" :loading-label="__('ui.action.saving')" :disabled="! $canManage">
                            {{ __('settings.save_changes') }}
                        </x-manexo.action-button>
                    </div>
                </form>
            </div>
            @endif

            <!-- TICKETS TAB -->
            @if ($activeTab === 'tickets')
            <div class="content-card">
                <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                    <h2 class="text-lg font-bold text-slate-900">{{ __('settings.tickets_title') }}</h2>
                    <p class="text-sm text-slate-500">{{ __('settings.tickets_subtitle') }}</p>
                </div>

                <form wire:submit.prevent="save" class="p-6 space-y-8">
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <x-input-label for="default_category" :value="__('settings.default_category')" />
                            <div class="mt-1">
                                <x-select-input id="default_category" wire:model="default_category_id" :disabled="! $canManage">
                                    <option value="">{{ __('settings.select') }}</option>
                                    @foreach ($categories as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </x-select-input>
                            </div>
                            <x-input-error :messages="$errors->get('default_category_id')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="default_priority" :value="__('settings.default_priority')" />
                            <div class="mt-1">
                                <x-select-input id="default_priority" wire:model="default_priority_id" :disabled="! $canManage">
                                    <option value="">{{ __('settings.select') }}</option>
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
                        <h3 class="text-sm font-semibold text-slate-900 mb-4">{{ __('settings.rules_title') }}</h3>
                        <div class="space-y-4">
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors cursor-pointer">
                                <input type="checkbox" wire:model="members_can_edit" class="mt-1 rounded border-slate-300 text-[var(--accent)] focus:ring-[var(--accent)]" @disabled(! $canManage)>
                                <div>
                                    <span class="block text-sm font-medium text-slate-900">{{ __('settings.allow_edit') }}</span>
                                    <span class="block text-xs text-slate-500 mt-0.5">{{ __('settings.allow_edit_help') }}</span>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors cursor-pointer">
                                <input type="checkbox" wire:model="members_can_delete" class="mt-1 rounded border-slate-300 text-[var(--accent)] focus:ring-[var(--accent)]" @disabled(! $canManage)>
                                <div>
                                    <span class="block text-sm font-medium text-slate-900">{{ __('settings.allow_delete') }}</span>
                                    <span class="block text-xs text-slate-500 mt-0.5">{{ __('settings.allow_delete_help') }}</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <hr class="border-slate-100">

                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-4">{{ __('settings.ticket_resolution_mode_label') }}</h3>
                        <div class="max-w-md">
                            <x-select-input id="ticket_resolution_mode" wire:model="ticket_resolution_mode" :disabled="! $canManage">
                                <option value="flexible">{{ __('settings.ticket_resolution_mode_flexible') }}</option>
                                <option value="strict">{{ __('settings.ticket_resolution_mode_strict') }}</option>
                            </x-select-input>
                        </div>
                    </div>

                    <hr class="border-slate-100">

                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-4">{{ __('settings.auto_close_title') }}</h3>
                        <div class="max-w-xs">
                            <x-input-label for="auto_close_days" :value="__('settings.auto_close_label')" />
                            <input
                                id="auto_close_days"
                                type="number"
                                wire:model="auto_close_days"
                                min="1"
                                max="365"
                                placeholder="{{ __('settings.auto_close_placeholder') }}"
                                class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all disabled:bg-slate-50 disabled:text-slate-500"
                                @disabled(! $canManage)
                            />
                            <p class="text-xs text-slate-500 mt-1.5">{{ __('settings.auto_close_help') }}</p>
                            <x-input-error :messages="$errors->get('auto_close_days')" class="mt-1" />
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <x-manexo.action-button type="submit" wire-target="save" variant="primary" class="btn-primary" :loading-label="__('ui.action.saving')" :disabled="! $canManage">
                            {{ __('settings.save') }}
                        </x-manexo.action-button>
                    </div>
                </form>
            </div>
            @endif

            <!-- CATEGORIES TAB -->
            @if ($activeTab === 'categories')
            <div class="content-card">
                <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                    <h2 class="text-lg font-bold text-slate-900">{{ __('settings.categories_title') }}</h2>
                    <p class="text-sm text-slate-500">{{ __('settings.categories_subtitle') }}</p>
                </div>

                <div class="p-6 space-y-6">
                    {{-- Create form --}}
                    @if ($canManage)
                        <form wire:submit.prevent="createCategory" class="space-y-3">
                            <div class="flex items-end gap-3">
                                <div class="flex-1">
                                    <x-input-label for="new_cat_name" :value="__('settings.category_name')" />
                                    <input
                                        id="new_cat_name"
                                        type="text"
                                        wire:model.blur="newCategoryName"
                                        placeholder="{{ __('settings.category_placeholder') }}"
                                        class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                                    />
                                    <x-input-error :messages="$errors->get('newCategoryName')" class="mt-1" />
                                </div>
                                <x-manexo.action-button type="submit" wire-target="createCategory" variant="primary" class="shrink-0 !font-semibold">
                                    <iconify-icon icon="solar:add-circle-linear" width="18"></iconify-icon>
                                    {{ __('settings.add') }}
                                </x-manexo.action-button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <x-input-label :value="__('settings.default_group')" />
                                    <x-select-input wire:model="newCategoryDefaultGroupId" class="mt-1">
                                        <option value="">{{ __('settings.none') }}</option>
                                        @foreach ($ticketGroups as $tg)
                                            <option value="{{ $tg->id }}">{{ $tg->name }}</option>
                                        @endforeach
                                    </x-select-input>
                                </div>
                                <div>
                                    <x-input-label :value="__('settings.default_form')" />
                                    <x-select-input wire:model="newCategoryDefaultFormId" class="mt-1">
                                        <option value="">{{ __('settings.none') }}</option>
                                        @foreach ($forms as $f)
                                            <option value="{{ $f->id }}">{{ $f->name }}</option>
                                        @endforeach
                                    </x-select-input>
                                </div>
                            </div>
                            {{-- Approval config --}}
                            <div class="mt-3 rounded-xl border border-slate-100 bg-slate-50/50 p-3 space-y-3">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="newCategoryRequiresApproval" class="rounded border-slate-300 text-[var(--accent)] focus:ring-[var(--accent)]" />
                                    <span class="text-sm font-medium text-slate-700">Nécessite une approbation</span>
                                </label>
                                @if($newCategoryRequiresApproval)
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <x-input-label value="Type d'approbateur" />
                                            <x-select-input wire:model="newCategoryApprovalType" class="mt-1">
                                                <option value="">Choisir...</option>
                                                <option value="user">Utilisateur spécifique</option>
                                                <option value="role">Rôle</option>
                                                <option value="org_admin">Owner / Admin de l'org</option>
                                            </x-select-input>
                                        </div>
                                        @if($newCategoryApprovalType === 'user')
                                            <div>
                                                <x-input-label value="Approbateur" />
                                                <x-select-input wire:model="newCategoryApprovalUserId" class="mt-1">
                                                    <option value="">Choisir...</option>
                                                    @foreach($members as $m)
                                                        <option value="{{ $m->user?->id }}">{{ $m->user?->name }} ({{ $m->role }})</option>
                                                    @endforeach
                                                </x-select-input>
                                            </div>
                                        @elseif($newCategoryApprovalType === 'role')
                                            <div>
                                                <x-input-label value="Rôle approbateur" />
                                                <x-select-input wire:model="newCategoryApprovalRole" class="mt-1">
                                                    <option value="">Choisir...</option>
                                                    @foreach($roles as $r)
                                                        <option value="{{ $r->slug }}">{{ $r->label ?? $r->slug }}</option>
                                                    @endforeach
                                                </x-select-input>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </form>
                        <hr class="border-slate-100">
                    @endif

                    {{-- List --}}
                    @if ($categories->isEmpty())
                        <div class="py-8 text-center">
                            <div class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 mb-3">
                                <iconify-icon icon="solar:tag-linear" width="28" class="text-slate-400"></iconify-icon>
                            </div>
                            <p class="text-sm text-slate-500">{{ __('settings.no_categories') }}</p>
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach ($categories as $cat)
                                <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-100 hover:border-slate-200 transition-colors group"
                                     wire:key="cat-{{ $cat->id }}">
                                    @if ($editingCategoryId === $cat->id)
                                        {{-- Inline edit --}}
                                        <form wire:submit.prevent="updateCategory" class="flex-1 space-y-2">
                                            <div class="flex items-center gap-3">
                                                <input
                                                    type="text"
                                                    wire:model.blur="editingCategoryName"
                                                    class="flex-1 rounded-lg border-slate-200 py-1.5 px-2.5 text-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                                    autofocus
                                                />
                                                <x-manexo.action-button type="submit" wire-target="updateCategory" variant="ghost" icon-only class="!p-1 text-[var(--accent)] hover:opacity-80" title="{{ __('settings.save') }}" :loading-label="__('settings.save')">
                                                    <iconify-icon icon="solar:check-circle-bold" width="20"></iconify-icon>
                                                </x-manexo.action-button>
                                                <button type="button" wire:click="cancelEditCategory" class="text-slate-400 hover:text-slate-600" title="{{ __('settings.cancel') }}">
                                                    <iconify-icon icon="solar:close-circle-bold" width="20"></iconify-icon>
                                                </button>
                                            </div>
                                            <div class="grid grid-cols-2 gap-2">
                                                <x-select-input wire:model="editingCategoryDefaultGroupId" class="text-xs">
                                                    <option value="">{{ __('settings.default_group') }}: {{ __('settings.none') }}</option>
                                                    @foreach ($ticketGroups as $tg)
                                                        <option value="{{ $tg->id }}">{{ $tg->name }}</option>
                                                    @endforeach
                                                </x-select-input>
                                                <x-select-input wire:model="editingCategoryDefaultFormId" class="text-xs">
                                                    <option value="">{{ __('settings.default_form') }}: {{ __('settings.none') }}</option>
                                                    @foreach ($forms as $f)
                                                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                                                    @endforeach
                                                </x-select-input>
                                            </div>
                                            {{-- Approval config (edit) --}}
                                            <div class="mt-2 rounded-lg border border-slate-100 bg-slate-50/50 p-2.5 space-y-2">
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="checkbox" wire:model="editingCategoryRequiresApproval" class="rounded border-slate-300 text-[var(--accent)] focus:ring-[var(--accent)]" />
                                                    <span class="text-xs font-medium text-slate-700">Nécessite une approbation</span>
                                                </label>
                                                @if($editingCategoryRequiresApproval)
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <x-select-input wire:model="editingCategoryApprovalType" class="text-xs">
                                                            <option value="">Type...</option>
                                                            <option value="user">Utilisateur</option>
                                                            <option value="role">Rôle</option>
                                                            <option value="org_admin">Owner/Admin</option>
                                                        </x-select-input>
                                                        @if($editingCategoryApprovalType === 'user')
                                                            <x-select-input wire:model="editingCategoryApprovalUserId" class="text-xs">
                                                                <option value="">Approbateur...</option>
                                                                @foreach($members as $m)
                                                                    <option value="{{ $m->user?->id }}">{{ $m->user?->name }}</option>
                                                                @endforeach
                                                            </x-select-input>
                                                        @elseif($editingCategoryApprovalType === 'role')
                                                            <x-select-input wire:model="editingCategoryApprovalRole" class="text-xs">
                                                                <option value="">Rôle...</option>
                                                                @foreach($roles as $r)
                                                                    <option value="{{ $r->slug }}">{{ $r->label ?? $r->slug }}</option>
                                                                @endforeach
                                                            </x-select-input>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </form>
                                        <x-input-error :messages="$errors->get('editingCategoryName')" class="mt-1" />
                                    @else
                                        {{-- Display --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-sm font-medium text-slate-900">{{ $cat->name }}</span>
                                                <span class="text-[10px] font-mono text-slate-400 bg-slate-50 px-1.5 py-0.5 rounded">{{ $cat->slug }}</span>
                                                @if ($cat->default_ticket_group_id)
                                                    @php $linkedGroup = $ticketGroups->firstWhere('id', $cat->default_ticket_group_id); @endphp
                                                    @if ($linkedGroup)
                                                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold bg-blue-50 text-blue-700">
                                                            <iconify-icon icon="solar:widget-5-linear" width="10"></iconify-icon>
                                                            {{ $linkedGroup->name }}
                                                        </span>
                                                    @endif
                                                @endif
                                                @if ($cat->default_form_id)
                                                    @php $linkedForm = $forms->firstWhere('id', $cat->default_form_id); @endphp
                                                    @if ($linkedForm)
                                                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold bg-violet-50 text-violet-700">
                                                            <iconify-icon icon="solar:clipboard-list-linear" width="10"></iconify-icon>
                                                            {{ $linkedForm->name }}
                                                        </span>
                                                    @endif
                                                @endif
                                                @if($cat->requires_approval)
                                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold bg-amber-50 text-amber-700">
                                                        <iconify-icon icon="solar:shield-check-linear" width="10"></iconify-icon>
                                                        Approbation
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Active toggle --}}
                                        <button
                                            type="button"
                                            wire:click="toggleCategory({{ $cat->id }})"
                                            class="shrink-0 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold transition-colors {{ $cat->is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}"
                                            @disabled(! $canManage)
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full {{ $cat->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                            {{ $cat->is_active ? __('settings.active') : __('settings.inactive') }}
                                        </button>

                                        @if ($canManage)
                                            {{-- Edit --}}
                                            <button type="button" wire:click="startEditCategory({{ $cat->id }})" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-[var(--accent)] transition-all" title="{{ __('settings.edit') }}">
                                                <iconify-icon icon="solar:pen-2-linear" width="16"></iconify-icon>
                                            </button>
                                            {{-- Delete --}}
                                            <button type="button" @click="$dispatch('confirm-action', { title: 'Supprimer', message: '{{ __('settings.delete_category_confirm') }}', confirmLabel: 'Supprimer', variant: 'danger', onConfirm: () => $wire.deleteCategory({{ $cat->id }}) })" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-red-600 transition-all" title="{{ __('settings.delete') }}">
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
            @endif

            <!-- GROUPS TAB -->
            @if ($activeTab === 'groups')
            <div class="content-card">
                <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                    <h2 class="text-lg font-bold text-slate-900">{{ __('settings.groups_title') }}</h2>
                    <p class="text-sm text-slate-500">{{ __('settings.groups_subtitle') }}</p>
                </div>

                <div class="p-6 space-y-6">
                    {{-- Create form --}}
                    @if ($canManage)
                        <form wire:submit.prevent="createGroup" class="flex items-end gap-3">
                            <div class="flex-1">
                                <x-input-label for="new_group_name" :value="__('settings.group_name')" />
                                <input
                                    id="new_group_name"
                                    type="text"
                                    wire:model.blur="newGroupName"
                                    placeholder="{{ __('settings.group_placeholder') }}"
                                    class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                                />
                                <x-input-error :messages="$errors->get('newGroupName')" class="mt-1" />
                            </div>
                            <div class="w-24">
                                <x-input-label for="new_group_color" :value="__('settings.group_color')" />
                                <div class="mt-1 relative">
                                    <input type="color" id="new_group_color" wire:model="newGroupColor" class="h-[42px] w-full rounded-xl border border-slate-200 cursor-pointer" />
                                </div>
                            </div>
                            <x-manexo.action-button type="submit" wire-target="createGroup" variant="primary" class="shrink-0 !font-semibold">
                                <iconify-icon icon="solar:add-circle-linear" width="18"></iconify-icon>
                                {{ __('settings.add') }}
                            </x-manexo.action-button>
                        </form>
                        <hr class="border-slate-100">
                    @endif

                    {{-- List --}}
                    @if ($ticketGroups->isEmpty())
                        <div class="py-8 text-center">
                            <div class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 mb-3">
                                <iconify-icon icon="solar:widget-5-linear" width="28" class="text-slate-400"></iconify-icon>
                            </div>
                            <p class="text-sm text-slate-500">{{ __('settings.no_groups') }}</p>
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach ($ticketGroups as $grp)
                                <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-100 hover:border-slate-200 transition-colors group"
                                     wire:key="grp-{{ $grp->id }}">
                                    @if ($editingGroupId === $grp->id)
                                        {{-- Inline edit --}}
                                        <form wire:submit.prevent="updateGroup" class="flex-1 flex items-center gap-3">
                                            <input
                                                type="text"
                                                wire:model.blur="editingGroupName"
                                                class="flex-1 rounded-lg border-slate-200 py-1.5 px-2.5 text-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                                autofocus
                                            />
                                            <input
                                                type="color"
                                                wire:model="editingGroupColor"
                                                class="h-8 w-10 rounded border border-slate-200 cursor-pointer"
                                            />
                                            <x-manexo.action-button type="submit" wire-target="updateGroup" variant="ghost" icon-only class="!p-1 text-[var(--accent)] hover:opacity-80" title="{{ __('settings.save') }}" :loading-label="__('settings.save')">
                                                <iconify-icon icon="solar:check-circle-bold" width="20"></iconify-icon>
                                            </x-manexo.action-button>
                                            <button type="button" wire:click="cancelEditGroup" class="text-slate-400 hover:text-slate-600" title="{{ __('settings.cancel') }}">
                                                <iconify-icon icon="solar:close-circle-bold" width="20"></iconify-icon>
                                            </button>
                                        </form>
                                        <x-input-error :messages="$errors->get('editingGroupName')" class="mt-1" />
                                    @else
                                        {{-- Color dot --}}
                                        <span class="h-3 w-3 rounded-full shrink-0" style="background-color: {{ $grp->color ?? 'var(--accent)' }};"></span>

                                        {{-- Display --}}
                                        <div class="flex-1 min-w-0">
                                            <span class="text-sm font-medium text-slate-900">{{ $grp->name }}</span>
                                            <span class="ml-2 text-[10px] font-mono text-slate-400 bg-slate-50 px-1.5 py-0.5 rounded">{{ $grp->slug }}</span>
                                        </div>

                                        {{-- Active toggle --}}
                                        <button
                                            type="button"
                                            wire:click="toggleGroup({{ $grp->id }})"
                                            class="shrink-0 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold transition-colors {{ $grp->is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}"
                                            @disabled(! $canManage)
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full {{ $grp->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                            {{ $grp->is_active ? __('settings.active') : __('settings.inactive') }}
                                        </button>

                                        @if ($canManage)
                                            {{-- Edit --}}
                                            <button type="button" wire:click="startEditGroup({{ $grp->id }})" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-[var(--accent)] transition-all" title="{{ __('settings.edit') }}">
                                                <iconify-icon icon="solar:pen-2-linear" width="16"></iconify-icon>
                                            </button>
                                            {{-- Delete --}}
                                            <button type="button" @click="$dispatch('confirm-action', { title: 'Supprimer', message: '{{ __('settings.delete_group_confirm') }}', confirmLabel: 'Supprimer', variant: 'danger', onConfirm: () => $wire.deleteGroup({{ $grp->id }}) })" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-red-600 transition-all" title="{{ __('settings.delete') }}">
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
            @endif

            <!-- PRIORITIES TAB -->
            @if ($activeTab === 'priorities')
            <div class="content-card">
                <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                    <h2 class="text-lg font-bold text-slate-900">{{ __('settings.priorities_title') }}</h2>
                    <p class="text-sm text-slate-500">{{ __('settings.priorities_subtitle') }}</p>
                </div>

                <div class="p-6 space-y-6">
                    {{-- Create form --}}
                    @if ($canManage)
                        <form wire:submit.prevent="createPriority" class="flex items-end gap-3">
                            <div class="flex-1">
                                <x-input-label for="new_prio_name" :value="__('settings.name')" />
                                <input
                                    id="new_prio_name"
                                    type="text"
                                    wire:model.blur="newPriorityName"
                                    placeholder="{{ __('settings.priority_placeholder') }}"
                                    class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                                />
                                <x-input-error :messages="$errors->get('newPriorityName')" class="mt-1" />
                            </div>
                            <div class="w-28">
                                <x-input-label for="new_prio_level" :value="__('settings.level')" />
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
                            <x-manexo.action-button type="submit" wire-target="createPriority" variant="primary" class="shrink-0 !font-semibold">
                                <iconify-icon icon="solar:add-circle-linear" width="18"></iconify-icon>
                                {{ __('settings.add') }}
                            </x-manexo.action-button>
                        </form>
                        <hr class="border-slate-100">
                    @endif

                    {{-- List --}}
                    @if ($priorities->isEmpty())
                        <div class="py-8 text-center">
                            <div class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 mb-3">
                                <iconify-icon icon="solar:flag-linear" width="28" class="text-slate-400"></iconify-icon>
                            </div>
                            <p class="text-sm text-slate-500">{{ __('settings.no_priorities') }}</p>
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
                                                wire:model.blur="editingPriorityName"
                                                class="flex-1 rounded-lg border-slate-200 py-1.5 px-2.5 text-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                                autofocus
                                            />
                                            <input
                                                type="number"
                                                wire:model="editingPriorityLevel"
                                                min="0"
                                                class="w-20 rounded-lg border-slate-200 py-1.5 px-2.5 text-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                            />
                                            <x-manexo.action-button type="submit" wire-target="updatePriority" variant="ghost" icon-only class="!p-1 text-[var(--accent)] hover:opacity-80" title="{{ __('settings.save') }}" :loading-label="__('settings.save')">
                                                <iconify-icon icon="solar:check-circle-bold" width="20"></iconify-icon>
                                            </x-manexo.action-button>
                                            <button type="button" wire:click="cancelEditPriority" class="text-slate-400 hover:text-slate-600" title="{{ __('settings.cancel') }}">
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
                                            <button type="button" wire:click="startEditPriority({{ $prio->id }})" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-[var(--accent)] transition-all" title="{{ __('settings.edit') }}">
                                                <iconify-icon icon="solar:pen-2-linear" width="16"></iconify-icon>
                                            </button>
                                            {{-- Delete --}}
                                            <button type="button" @click="$dispatch('confirm-action', { title: 'Supprimer', message: '{{ __('settings.delete_priority_confirm') }}', confirmLabel: 'Supprimer', variant: 'danger', onConfirm: () => $wire.deletePriority({{ $prio->id }}) })" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-red-600 transition-all" title="{{ __('settings.delete') }}">
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
            @endif

            <!-- FONCTIONS MÉTIER TAB -->
            @if ($activeTab === 'functions')
            <div class="content-card">
                <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                    <h2 class="text-lg font-bold text-slate-900">{{ __('settings.functions_title') }}</h2>
                    <p class="text-sm text-slate-500">{{ __('settings.functions_subtitle') }}</p>
                </div>
                <div class="p-6 space-y-6">
                    @if ($canManage)
                        <form wire:submit.prevent="createFunction" class="flex items-end gap-3">
                            <div class="flex-1">
                                <x-input-label for="new_function_name" :value="__('settings.function_name')" />
                                <input id="new_function_name" type="text" wire:model.blur="newFunctionName" placeholder="{{ __('settings.function_placeholder') }}" class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all" />
                                <x-input-error :messages="$errors->get('newFunctionName')" class="mt-1" />
                            </div>
                            <x-manexo.action-button type="submit" wire-target="createFunction" variant="primary" class="shrink-0 !font-semibold">
                                <iconify-icon icon="solar:add-circle-linear" width="18"></iconify-icon>
                                {{ __('settings.add') }}
                            </x-manexo.action-button>
                        </form>
                        <hr class="border-slate-100">
                    @endif
                    @if ($organizationFunctions->isEmpty())
                        <div class="py-8 text-center">
                            <div class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 mb-3">
                                <iconify-icon icon="solar:user-id-linear" width="28" class="text-slate-400"></iconify-icon>
                            </div>
                            <p class="text-sm text-slate-500">{{ __('settings.no_functions') }}</p>
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach ($organizationFunctions as $fn)
                                <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-100 hover:border-slate-200 transition-colors group" wire:key="fn-{{ $fn->id }}">
                                    @if ($editingFunctionId === $fn->id)
                                        <form wire:submit.prevent="updateFunction" class="flex-1 flex items-center gap-3">
                                            <input type="text" wire:model.blur="editingFunctionName" class="flex-1 rounded-lg border-slate-200 py-1.5 px-2.5 text-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]" autofocus />
                                            <x-manexo.action-button type="submit" wire-target="updateFunction" variant="ghost" icon-only class="!p-1 text-[var(--accent)] hover:opacity-80" title="{{ __('settings.save') }}" :loading-label="__('settings.save')">
                                                <iconify-icon icon="solar:check-circle-bold" width="20"></iconify-icon>
                                            </x-manexo.action-button>
                                            <button type="button" wire:click="cancelEditFunction" class="text-slate-400 hover:text-slate-600" title="{{ __('settings.cancel') }}">
                                                <iconify-icon icon="solar:close-circle-bold" width="20"></iconify-icon>
                                            </button>
                                        </form>
                                        <x-input-error :messages="$errors->get('editingFunctionName')" class="mt-1" />
                                    @else
                                        <span class="flex-1 text-sm font-medium text-slate-900">{{ $fn->name }}</span>
                                        @if ($canManage)
                                            <button type="button" wire:click="startEditFunction({{ $fn->id }})" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-[var(--accent)] transition-all" title="{{ __('settings.edit') }}">
                                                <iconify-icon icon="solar:pen-2-linear" width="16"></iconify-icon>
                                            </button>
                                            <button type="button" @click="$dispatch('confirm-action', { title: 'Supprimer', message: '{{ __('settings.delete_function_confirm') }}', confirmLabel: 'Supprimer', variant: 'danger', onConfirm: () => $wire.deleteFunction({{ $fn->id }}) })" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-red-600 transition-all" title="{{ __('settings.delete') }}">
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
            @endif

            @if ($activeTab === 'forms')
            <div class="content-card">
                <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                    <h2 class="text-lg font-bold text-slate-900">{{ __('settings.forms_editor_title') }}</h2>
                    <p class="text-sm text-slate-500 mt-1">{{ __('settings.forms_editor_subtitle') }}</p>
                </div>
                <form wire:submit.prevent="saveFormsSettings" class="p-6 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="forms_default_due_days" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('settings.forms_default_due_days') }}</label>
                            <input type="number" id="forms_default_due_days" name="forms_default_due_days" wire:model.blur="forms_default_due_days" min="1" max="365" placeholder="7"
                                   class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm">
                            <p class="mt-1 text-xs text-slate-500">{{ __('settings.forms_default_due_days_help') }}</p>
                        </div>
                        <div>
                            <label for="forms_default_expiry_days" class="block text-sm font-semibold text-slate-700 mb-1.5">{{ __('settings.forms_default_expiry_days') }}</label>
                            <input type="number" id="forms_default_expiry_days" name="forms_default_expiry_days" wire:model.blur="forms_default_expiry_days" min="1" max="365" placeholder="30"
                                   class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm">
                            <p class="mt-1 text-xs text-slate-500">{{ __('settings.forms_default_expiry_days_help') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <input type="checkbox" id="forms_notify_on_response" name="forms_notify_on_response" wire:model="forms_notify_on_response"
                               class="h-4 w-4 rounded border-slate-300 text-[var(--accent)] focus:ring-[var(--accent)]">
                        <div>
                            <label for="forms_notify_on_response" class="text-sm font-semibold text-slate-700">{{ __('settings.forms_notify_on_response') }}</label>
                            <p class="text-xs text-slate-500 mt-0.5">{{ __('settings.forms_notify_on_response_help') }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <x-manexo.action-button type="submit" wire-target="saveFormsSettings" variant="primary" class="!font-semibold">
                            <iconify-icon icon="solar:check-circle-bold" width="18"></iconify-icon>
                            {{ __('settings.save') }}
                        </x-manexo.action-button>
                        <a href="{{ route('admin.forms') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                            <iconify-icon icon="solar:magic-stick-3-bold-duotone" width="18"></iconify-icon>
                            {{ __('settings.open_builder') }}
                        </a>
                    </div>
                </form>
            </div>
            @endif

            @if ($activeTab === 'email')
                @include('livewire.admin.partials.settings-email')
            @endif

            @if ($activeTab === 'sla')
                @include('livewire.admin.partials.settings-sla')
            @endif

            @if ($activeTab === 'automations')
                @include('livewire.admin.partials.settings-automations')
            @endif

            @if ($activeTab === 'knowledge_base')
                @include('livewire.admin.partials.settings-knowledge-base')
            @endif

            @if ($activeTab === 'api')
                @include('livewire.admin.partials.settings-api')
            @endif

            <!-- ROLES & PERMISSIONS TAB -->
            @if ($activeTab === 'roles')
            <div class="space-y-6">
                {{-- Card 1: Role Management --}}
                <div class="content-card">
                    <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                        <h2 class="text-lg font-bold text-slate-900">{{ __('settings.roles_management_title') }}</h2>
                        <p class="text-sm text-slate-500">{{ __('settings.roles_management_subtitle') }}</p>
                    </div>

                    <div class="p-6 space-y-6">
                        {{-- Create form --}}
                        @if ($canManage)
                            <form wire:submit.prevent="createRole" class="flex items-end gap-3">
                                <div class="flex-1">
                                    <x-input-label for="new_role_name" :value="__('settings.role_name')" />
                                    <input
                                        id="new_role_name"
                                        type="text"
                                        wire:model.blur="newRoleName"
                                        placeholder="{{ __('settings.role_placeholder') }}"
                                        class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                                    />
                                    <x-input-error :messages="$errors->get('newRoleName')" class="mt-1" />
                                </div>
                                <div class="w-44">
                                    <x-input-label for="new_role_base" :value="__('settings.role_base')" />
                                    <x-select-input id="new_role_base" wire:model="newRoleBaseSlug" class="mt-1">
                                        <option value="">{{ __('settings.role_base_none') }}</option>
                                        @foreach ($roles as $r)
                                            @if ($r->slug !== 'owner')
                                                <option value="{{ $r->slug }}">{{ $r->name }}</option>
                                            @endif
                                        @endforeach
                                    </x-select-input>
                                </div>
                                <x-manexo.action-button type="submit" wire-target="createRole" variant="primary" class="shrink-0 !font-semibold">
                                    <iconify-icon icon="solar:add-circle-linear" width="18"></iconify-icon>
                                    {{ __('settings.add') }}
                                </x-manexo.action-button>
                            </form>
                            <hr class="border-slate-100">
                        @endif

                        {{-- Role list --}}
                        @if ($roles->isEmpty())
                            <div class="py-8 text-center">
                                <div class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 mb-3">
                                    <iconify-icon icon="solar:shield-keyhole-linear" width="28" class="text-slate-400"></iconify-icon>
                                </div>
                                <p class="text-sm text-slate-500">{{ __('settings.no_roles') }}</p>
                            </div>
                        @else
                            <div class="space-y-2">
                                @foreach ($roles as $r)
                                    <div
                                        class="flex items-center gap-3 px-4 py-3 rounded-xl border transition-colors group cursor-pointer {{ $selectedRole === $r->slug ? 'border-[var(--accent)] bg-[var(--accent-soft)]/10 ring-1 ring-[var(--accent)]/20' : 'border-slate-100 hover:border-slate-200' }}"
                                        wire:key="role-{{ $r->id }}"
                                        wire:click="selectRoleTab('{{ $r->slug }}')"
                                    >
                                        @if ($editingRoleId === $r->id)
                                            {{-- Inline edit --}}
                                            <form wire:submit.prevent="updateRoleName" class="flex-1 flex items-center gap-3" @click.stop>
                                                <input
                                                    type="text"
                                                    wire:model.blur="editingRoleName"
                                                    class="flex-1 rounded-lg border-slate-200 py-1.5 px-2.5 text-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                                    autofocus
                                                />
                                                <x-manexo.action-button type="submit" wire-target="updateRoleName" variant="ghost" icon-only class="!p-1 text-[var(--accent)] hover:opacity-80" title="{{ __('settings.save') }}" :loading-label="__('settings.save')">
                                                    <iconify-icon icon="solar:check-circle-bold" width="20"></iconify-icon>
                                                </x-manexo.action-button>
                                                <button type="button" wire:click.stop="cancelEditRole" class="text-slate-400 hover:text-slate-600" title="{{ __('settings.cancel') }}">
                                                    <iconify-icon icon="solar:close-circle-bold" width="20"></iconify-icon>
                                                </button>
                                            </form>
                                            <x-input-error :messages="$errors->get('editingRoleName')" class="mt-1" />
                                        @else
                                            {{-- Selection dot --}}
                                            <span class="h-2.5 w-2.5 rounded-full shrink-0 {{ $selectedRole === $r->slug ? 'bg-[var(--accent)]' : 'bg-slate-300' }}"></span>

                                            {{-- Display --}}
                                            <div class="flex-1 min-w-0 flex items-center gap-2">
                                                <span class="text-sm font-medium text-slate-900">{{ $r->name }}</span>
                                                @if ($r->is_default)
                                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-500">{{ __('settings.role_default_badge') }}</span>
                                                @endif
                                            </div>

                                            {{-- Member count --}}
                                            <span class="text-xs text-slate-400 tabular-nums">{{ trans_choice('settings.role_member_count', $roleMemberCounts[$r->slug] ?? 0, ['count' => $roleMemberCounts[$r->slug] ?? 0]) }}</span>

                                            @if ($canManage)
                                                {{-- Edit --}}
                                                <button type="button" wire:click.stop="startEditRole({{ $r->id }})" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-[var(--accent)] transition-all" title="{{ __('settings.edit') }}">
                                                    <iconify-icon icon="solar:pen-2-linear" width="16"></iconify-icon>
                                                </button>
                                                {{-- Delete (custom roles only) --}}
                                                @if (! $r->is_default)
                                                    <button type="button" @click.stop="$dispatch('confirm-action', { title: 'Supprimer', message: '{{ __('settings.delete_role_confirm') }}', confirmLabel: 'Supprimer', variant: 'danger', onConfirm: () => $wire.deleteRole({{ $r->id }}) })" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-red-600 transition-all" title="{{ __('settings.delete') }}">
                                                        <iconify-icon icon="solar:trash-bin-trash-linear" width="16"></iconify-icon>
                                                    </button>
                                                @endif
                                            @endif
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Card 2: Tableau des permissions du rôle sélectionné (clic sur un rôle = ouvre son tableau) --}}
                <div class="content-card">
                    <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                        @php
                            $selectedRoleDef = $roles->firstWhere('slug', $selectedRole);
                        @endphp
                        <h2 class="text-lg font-bold text-slate-900">{{ __('settings.permissions_for', ['role' => $selectedRoleDef?->name ?? $selectedRole]) }}</h2>
                        <p class="text-sm text-slate-500">{{ __('settings.permissions_subtitle') }}</p>
                    </div>

                    <div class="p-6">
                        @if ($roles->isEmpty())
                            <div class="py-8 text-center text-slate-500 text-sm">
                                {{ __('settings.no_roles') }}
                            </div>
                        @elseif (! $selectedRoleDef)
                            <div class="py-8 text-center text-slate-500 text-sm">
                                {{ __('settings.select_role_to_manage') }}
                            </div>
                        @else
                            {{-- Message propriétaire --}}
                            @if ($selectedRole === 'owner')
                                <div class="rounded-xl bg-amber-50 p-4 text-sm text-amber-800 flex items-center gap-3 mb-6" style="border: 1px solid #fde68a;">
                                    <iconify-icon icon="solar:crown-bold-duotone" width="22" class="text-amber-600 shrink-0"></iconify-icon>
                                    {{ __('settings.owner_all_permissions') }}
                                </div>
                            @endif

                            @php
                                $grouped = \App\Enums\Permission::grouped();
                                $groupIcons = [
                                    'tickets' => 'solar:ticket-bold-duotone',
                                    'team' => 'solar:users-group-rounded-bold-duotone',
                                    'forms' => 'solar:clipboard-list-bold-duotone',
                                    'settings' => 'solar:settings-bold-duotone',
                                    'reports' => 'solar:chart-2-bold-duotone',
                                    'discussions' => 'solar:chat-round-bold-duotone',
                                ];
                            @endphp

                            {{-- Accordéon : un groupe ouvert à la fois pour raccourcir la page --}}
                            <div class="rounded-xl overflow-hidden" style="border: 1px solid #e2e8f0;" x-data="{ openGroup: 'tickets' }">
                                <div class="divide-y divide-slate-100">
                                    @foreach ($grouped as $group => $permissions)
                                        <div class="bg-white">
                                            <button
                                                type="button"
                                                @click="openGroup = openGroup === '{{ $group }}' ? null : '{{ $group }}'"
                                                class="w-full flex items-center justify-between gap-3 px-4 py-3 text-left rounded-lg hover:bg-slate-50 transition-colors"
                                            >
                                                <span class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                                                    <iconify-icon icon="{{ $groupIcons[$group] ?? 'solar:widget-bold-duotone' }}" width="18" class="text-slate-500"></iconify-icon>
                                                    {{ __('permissions.group_' . $group) }}
                                                </span>
                                                <span class="text-slate-400 transition-transform" :class="openGroup === '{{ $group }}' ? 'rotate-180' : ''">
                                                    <iconify-icon icon="solar:alt-arrow-down-linear" width="20"></iconify-icon>
                                                </span>
                                            </button>
                                            <div x-show="openGroup === '{{ $group }}'" x-cloak class="border-t border-slate-100">
                                                <div class="overflow-x-auto">
                                                    <table class="w-full text-left border-collapse">
                                                        <tbody class="divide-y divide-slate-50">
                                                            @foreach ($permissions as $perm)
                                                                @php
                                                                    $isGranted = $selectedRole === 'owner' || ($rolePermissions[$selectedRole][$perm->value] ?? false);
                                                                @endphp
                                                                <tr class="hover:bg-slate-50/50 transition-colors">
                                                                    <td class="px-4 py-2 text-sm text-slate-900 pl-8">
                                                                        {{ __('permissions.' . $perm->value) }}
                                                                    </td>
                                                                    <td class="px-4 py-2 w-20 text-center">
                                                                        <label class="inline-flex items-center justify-center {{ $selectedRole === 'owner' ? 'cursor-default opacity-80' : 'cursor-pointer' }}">
                                                                            <input
                                                                                type="checkbox"
                                                                                @checked($isGranted)
                                                                                @if ($selectedRole !== 'owner')
                                                                                    wire:click="toggleRolePermission('{{ $selectedRole }}', '{{ $perm->value }}')"
                                                                                @else
                                                                                    disabled
                                                                                @endif
                                                                                class="rounded border-slate-300 text-[var(--accent)] focus:ring-[var(--accent)]"
                                                                            />
                                                                        </label>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            @if ($canManage && $selectedRole !== 'owner')
                                <div class="mt-6 pt-4 flex justify-end border-t border-slate-100">
                                    <button
                                        type="button"
                                        wire:click="saveRolePermissions"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-[var(--accent)] px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:opacity-90 transition-all disabled:opacity-50"
                                    >
                                        <span wire:loading.remove wire:target="saveRolePermissions">{{ __('settings.save_permissions') }}</span>
                                        <span wire:loading wire:target="saveRolePermissions"><iconify-icon icon="solar:refresh-linear" class="animate-spin" width="18"></iconify-icon></span>
                                    </button>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- MAINTENANCE TAB -->
            @if ($activeTab === 'maintenance')
            <div class="space-y-6">
                {{-- Org Info Card --}}
                <div class="content-card">
                    <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                        <h2 class="text-lg font-bold text-slate-900">{{ __('settings.org_info_title') }}</h2>
                        <p class="text-sm text-slate-500">{{ __('settings.org_info_subtitle') }}</p>
                    </div>
                    <div class="p-6">
                        {{-- Org details --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                            <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 border border-slate-100">
                                <iconify-icon icon="solar:buildings-2-bold-duotone" width="20" class="text-slate-400 shrink-0"></iconify-icon>
                                <div class="min-w-0">
                                    <span class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">{{ __('settings.org_name_label') }}</span>
                                    <span class="block text-sm font-medium text-slate-900 truncate">{{ $org?->name ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 border border-slate-100">
                                <iconify-icon icon="solar:link-round-bold-duotone" width="20" class="text-slate-400 shrink-0"></iconify-icon>
                                <div class="min-w-0">
                                    <span class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">{{ __('settings.org_slug_label') }}</span>
                                    <span class="block text-sm font-mono text-slate-900 truncate">{{ $org?->slug ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 border border-slate-100 sm:col-span-2">
                                <iconify-icon icon="solar:calendar-bold-duotone" width="20" class="text-slate-400 shrink-0"></iconify-icon>
                                <div class="min-w-0">
                                    <span class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">{{ __('settings.org_created_label') }}</span>
                                    <span class="block text-sm font-medium text-slate-900">{{ $org?->created_at?->translatedFormat('d F Y à H:i') ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Stats grid --}}
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @php
                                $statItems = [
                                    ['key' => 'members', 'icon' => 'solar:users-group-rounded-bold-duotone', 'label' => __('settings.org_members_label'), 'color' => 'text-blue-500'],
                                    ['key' => 'tickets', 'icon' => 'solar:ticket-bold-duotone', 'label' => __('settings.org_tickets_label'), 'color' => 'text-amber-500'],
                                    ['key' => 'forms', 'icon' => 'solar:clipboard-text-bold-duotone', 'label' => __('settings.org_forms_label'), 'color' => 'text-violet-500'],
                                    ['key' => 'categories', 'icon' => 'solar:tag-bold-duotone', 'label' => __('settings.org_categories_label'), 'color' => 'text-emerald-500'],
                                    ['key' => 'priorities', 'icon' => 'solar:flag-bold-duotone', 'label' => __('settings.org_priorities_label'), 'color' => 'text-rose-500'],
                                    ['key' => 'roles', 'icon' => 'solar:shield-keyhole-bold-duotone', 'label' => __('settings.org_roles_label'), 'color' => 'text-cyan-500'],
                                ];
                            @endphp
                            @foreach ($statItems as $stat)
                                <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-100 bg-white hover:bg-slate-50/50 transition-colors">
                                    <iconify-icon icon="{{ $stat['icon'] }}" width="22" class="{{ $stat['color'] }} shrink-0"></iconify-icon>
                                    <div>
                                        <span class="block text-lg font-bold text-slate-900 tabular-nums">{{ number_format($maintenanceStats[$stat['key']] ?? 0) }}</span>
                                        <span class="block text-[11px] text-slate-500 font-medium">{{ $stat['label'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Cache Management Card --}}
                <div class="content-card">
                    <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                        <h2 class="text-lg font-bold text-slate-900">{{ __('settings.cache_title') }}</h2>
                        <p class="text-sm text-slate-500">{{ __('settings.cache_subtitle') }}</p>
                    </div>
                    <div class="p-6 space-y-4">
                        {{-- Clear org cache --}}
                        <div class="flex items-start sm:items-center justify-between gap-4 p-4 rounded-xl border border-slate-100 hover:border-slate-200 transition-colors flex-col sm:flex-row">
                            <div class="flex items-start gap-3">
                                <iconify-icon icon="solar:database-bold-duotone" width="22" class="text-amber-500 mt-0.5 shrink-0"></iconify-icon>
                                <div>
                                    <span class="block text-sm font-semibold text-slate-900">{{ __('settings.clear_org_cache') }}</span>
                                    <span class="block text-xs text-slate-500 mt-0.5 leading-relaxed">{{ __('settings.clear_org_cache_help') }}</span>
                                </div>
                            </div>
                            <button
                                type="button"
                                @click="$dispatch('confirm-action', { title: 'Vider le cache', message: 'Vider le cache de l\u0027organisation ?', confirmLabel: 'Confirmer', variant: 'warning', onConfirm: () => $wire.clearOrganizationCache() })"
                                class="shrink-0 inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700 hover:bg-amber-100 transition-all"
                                @disabled(! $canManage)
                            >
                                <span wire:loading.remove wire:target="clearOrganizationCache">
                                    <iconify-icon icon="solar:trash-bin-minimalistic-linear" width="16"></iconify-icon>
                                    {{ __('settings.clear_org_cache') }}
                                </span>
                                <span wire:loading wire:target="clearOrganizationCache">
                                    <iconify-icon icon="solar:refresh-linear" class="animate-spin" width="16"></iconify-icon>
                                </span>
                            </button>
                        </div>

                        {{-- Clear view cache --}}
                        <div class="flex items-start sm:items-center justify-between gap-4 p-4 rounded-xl border border-slate-100 hover:border-slate-200 transition-colors flex-col sm:flex-row">
                            <div class="flex items-start gap-3">
                                <iconify-icon icon="solar:code-bold-duotone" width="22" class="text-blue-500 mt-0.5 shrink-0"></iconify-icon>
                                <div>
                                    <span class="block text-sm font-semibold text-slate-900">{{ __('settings.clear_view_cache') }}</span>
                                    <span class="block text-xs text-slate-500 mt-0.5 leading-relaxed">{{ __('settings.clear_view_cache_help') }}</span>
                                </div>
                            </div>
                            <button
                                type="button"
                                @click="$dispatch('confirm-action', { title: 'Vider le cache', message: 'Vider le cache des vues ?', confirmLabel: 'Confirmer', variant: 'warning', onConfirm: () => $wire.clearViewCache() })"
                                class="shrink-0 inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100 transition-all"
                                @disabled(! $canManage)
                            >
                                <span wire:loading.remove wire:target="clearViewCache">
                                    <iconify-icon icon="solar:trash-bin-minimalistic-linear" width="16"></iconify-icon>
                                    {{ __('settings.clear_view_cache') }}
                                </span>
                                <span wire:loading wire:target="clearViewCache">
                                    <iconify-icon icon="solar:refresh-linear" class="animate-spin" width="16"></iconify-icon>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- DANGER ZONE -->
            @if ($activeTab === 'danger')
            <div class="rounded-xl sm:rounded-2xl bg-red-50 overflow-hidden" style="border: 1px solid #fecaca;">
                <div class="px-6 py-5 bg-red-100/50" style="border-bottom: 1px solid #fecaca;">
                    <h2 class="text-lg font-bold text-red-900">{{ __('settings.danger_title') }}</h2>
                    <p class="text-sm text-red-700">{{ __('settings.danger_subtitle') }}</p>
                </div>
                <div class="p-6">
                    <div class="rounded-xl bg-white p-6" style="border: 1px solid #fecaca;">
                        <h3 class="text-base font-bold text-slate-900">{{ __('settings.delete_organization') }}</h3>
                        <p class="text-sm text-slate-500 mt-1 mb-4">{{ __('settings.delete_organization_help') }}</p>

                        <div class="flex items-end gap-4">
                            <div class="flex-1">
                                <x-input-label for="confirm_delete" :value="__('settings.confirm_delete_label')" />
                                <x-text-input id="confirm_delete" type="text" class="mt-1 w-full" wire:model.blur="dangerConfirmName" placeholder="{{ $org?->name }}" :disabled="! $isOwner" />
                            </div>
                            <button
                                type="button"
                                @click="$dispatch('confirm-action', { title: '{{ __('Supprimer d\u00e9finitivement') }}', message: '{{ __('Cette action est irr\u00e9versible. Toutes les donn\u00e9es seront supprim\u00e9es.') }}', confirmLabel: '{{ __('Supprimer') }}', variant: 'danger', onConfirm: () => $wire.deleteOrganization() })"
                                class="h-[42px] px-4 rounded-xl bg-red-600 text-white text-sm font-bold shadow-md hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                @disabled(! $isOwner || $dangerConfirmName !== $org?->name)
                            >
                                {{ __('settings.delete_permanently') }}
                            </button>
                        </div>
                        @if(!$isOwner)
                            <p class="text-xs text-red-600 mt-2 font-medium">{{ __('settings.owner_only') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        @endif
        </div>
    </div>
</div>
