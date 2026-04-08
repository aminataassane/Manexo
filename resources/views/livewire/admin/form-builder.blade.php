<div
    class="builder-full-bleed flex min-h-0 flex-col"
    x-data="{
        paletteDrawer: false,
        propsDrawer: false,
        shareCopied: false,
    }"
    x-on:field-selected.window="if (window.innerWidth < 1024) { propsDrawer = true; paletteDrawer = false; }"
>

    {{-- ═══════ TOOLBAR — premium, clean, sticky ═══════ --}}
    <header class="shrink-0 z-30 bg-white border-b border-slate-100">
        <div class="h-14 px-3 sm:px-5 lg:px-6 flex items-center gap-3">
            {{-- Left: mobile toggle + form selector --}}
            <div class="flex items-center gap-2 min-w-0 flex-1">
                {{-- Palette toggle (tablet/mobile) --}}
                <button type="button" @click="paletteDrawer = !paletteDrawer; propsDrawer = false"
                        class="lg:hidden shrink-0 h-9 w-9 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors inline-flex items-center justify-center"
                        title="{{ __('forms_builder.search_fields') }}">
                    <iconify-icon icon="solar:widget-add-linear" width="18"></iconify-icon>
                </button>

                {{-- Form selector dropdown --}}
                <div x-data="{ formDropdownOpen: false }" class="relative min-w-0 flex-1 max-w-md">
                    <button type="button" @click="formDropdownOpen = !formDropdownOpen"
                            class="flex items-center gap-2 min-w-0 w-full group py-1.5 px-2 rounded-xl hover:bg-slate-50 transition-colors text-left">
                        <div class="min-w-0 flex-1 flex items-center gap-2.5">
                            <span class="text-sm font-bold text-slate-900 truncate">
                                {{ $fb_selected_form_name ?: __('forms_builder.select_form') }}
                            </span>
                            @if($fb_selected_form_id)
                                @php
                                    $statusBadge = match($fb_selected_form_status) {
                                        'published' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'dot' => 'bg-emerald-500', 'label' => __('forms_builder.published')],
                                        'archived' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-500', 'dot' => 'bg-slate-400', 'label' => __('forms_builder.archived')],
                                        default => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'dot' => 'bg-amber-500', 'label' => __('forms_builder.draft')],
                                    };
                                @endphp
                                <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-full {{ $statusBadge['bg'] }} {{ $statusBadge['text'] }} text-[10px] font-bold shrink-0">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $statusBadge['dot'] }}"></span>
                                    {{ $statusBadge['label'] }}
                                </span>
                            @endif
                        </div>
                        <iconify-icon icon="solar:alt-arrow-down-linear" width="14" class="shrink-0 text-slate-300 group-hover:text-slate-500 transition-colors"></iconify-icon>
                    </button>

                    {{-- Dropdown formulaires --}}
                    <div x-show="formDropdownOpen" @click.away="formDropdownOpen = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute left-0 right-0 top-full z-50 mt-1.5 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-xl shadow-slate-200/40 sm:right-auto sm:w-72"
                         x-cloak>
                        <div class="p-3 border-b border-slate-50">
                            <div class="relative">
                                <input type="text" wire:model.live.debounce.150ms="fb_form_name"
                                       placeholder="{{ __('forms_builder.new_form_placeholder') }}"
                                       @disabled(! $canManageForms)
                                       class="input-builder w-full text-xs py-2 pl-3 pr-8">
                                <button type="button" wire:click="createForm" @click="formDropdownOpen = false"
                                        @disabled(! $canManageForms || trim($fb_form_name) === '')
                                        class="absolute right-1.5 top-1/2 -translate-y-1/2 p-1 rounded text-slate-400 hover:text-[var(--accent)] disabled:opacity-40 disabled:pointer-events-none transition">
                                    <iconify-icon icon="solar:add-circle-bold" width="16"></iconify-icon>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('fb_form_name')" />
                        </div>
                        <div class="max-h-60 overflow-y-auto custom-scrollbar">
                            @foreach ($forms as $f)
                                @php
                                    $fStatus = $f->status instanceof \App\Enums\FormStatus ? $f->status->value : (string) $f->status;
                                    $isSelected = (int) $fb_selected_form_id === (int) $f->id;
                                @endphp
                                <button type="button" wire:click="selectForm({{ $f->id }})" @click="formDropdownOpen = false"
                                        class="w-full text-left px-3.5 py-2.5 flex items-center justify-between gap-2 text-xs transition-colors {{ $isSelected ? 'bg-slate-50 text-slate-900 font-semibold' : 'hover:bg-slate-50 text-slate-600' }}">
                                    <span class="truncate">{{ $f->name }}</span>
                                    <div class="flex items-center gap-2 shrink-0">
                                        @if($fStatus === 'published')
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" title="{{ __('forms_builder.published') }}"></span>
                                        @elseif($fStatus === 'archived')
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400" title="{{ __('forms_builder.archived') }}"></span>
                                        @else
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500" title="{{ __('forms_builder.draft') }}"></span>
                                        @endif
                                        @if($isSelected)
                                            <iconify-icon icon="solar:check-circle-bold" width="14" class="text-[var(--accent)]"></iconify-icon>
                                        @endif
                                    </div>
                                </button>
                            @endforeach
                            @if($forms->isEmpty())
                                <div class="px-4 py-8 text-center text-xs text-slate-400">
                                    {{ __('forms_builder.no_form_selected_hint') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: actions --}}
            <div class="flex items-center gap-1.5 shrink-0">
                @if($fb_selected_form_id)
                    @if($fb_selected_form_slug)
                        <a href="{{ url('/f/' . $fb_selected_form_slug) }}" target="_blank"
                           class="hidden sm:inline-flex h-8 items-center gap-1.5 rounded-lg border border-slate-100 bg-white px-2.5 text-xs font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-800 transition-all">
                            <iconify-icon icon="solar:eye-linear" width="15"></iconify-icon>
                            <span class="hidden md:inline">{{ __('forms_builder.preview') }}</span>
                        </a>
                        <button type="button"
                                @click="navigator.clipboard.writeText('{{ url('/f/' . $fb_selected_form_slug) }}'); shareCopied = true; setTimeout(() => shareCopied = false, 2000)"
                                class="hidden sm:inline-flex h-8 items-center gap-1.5 rounded-lg border border-slate-100 bg-white px-2.5 text-xs font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-800 transition-all">
                            <iconify-icon icon="solar:share-linear" width="15"></iconify-icon>
                            <span class="hidden md:inline" x-text="shareCopied ? '{{ __('forms_builder.share_copied') }}' : '{{ __('forms_builder.share') }}'"></span>
                        </button>
                    @endif

                    <div class="hidden sm:block w-px h-5 bg-slate-100 mx-0.5" aria-hidden="true"></div>

                    @if($fb_selected_form_status !== 'published')
                        <button type="button" wire:click="publishForm" @disabled(! $canManageForms)
                                class="hidden sm:inline-flex h-8 items-center gap-1.5 rounded-lg border border-emerald-100 bg-white px-2.5 text-xs font-medium text-emerald-600 hover:bg-emerald-50 transition-all disabled:opacity-50">
                            <iconify-icon icon="solar:check-circle-bold" width="15"></iconify-icon>
                            <span class="hidden md:inline">{{ __('forms_builder.publish') }}</span>
                        </button>
                    @else
                        <button type="button" wire:click="unpublishForm" @disabled(! $canManageForms)
                                class="hidden sm:inline-flex h-8 items-center gap-1.5 rounded-lg border border-slate-100 bg-white px-2.5 text-xs font-medium text-slate-500 hover:bg-slate-50 transition-all disabled:opacity-50">
                            <iconify-icon icon="solar:pause-circle-linear" width="15"></iconify-icon>
                            <span class="hidden md:inline">{{ __('forms_builder.draft') }}</span>
                        </button>
                    @endif
                    <button type="button" wire:click="duplicateSelectedForm" @disabled(! $canManageForms)
                            class="hidden lg:inline-flex h-8 items-center gap-1.5 rounded-lg border border-slate-100 bg-white px-2.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-all disabled:opacity-50">
                        <iconify-icon icon="solar:copy-linear" width="15"></iconify-icon>
                        {{ __('forms_builder.duplicate') }}
                    </button>
                @endif

                {{-- Properties toggle (tablet/mobile) --}}
                <button type="button" @click="propsDrawer = !propsDrawer; paletteDrawer = false"
                        class="lg:hidden shrink-0 h-9 w-9 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors inline-flex items-center justify-center"
                        title="{{ __('forms_builder.properties') }}">
                    <iconify-icon icon="solar:settings-linear" width="18"></iconify-icon>
                </button>

                {{-- Save --}}
                <button type="button" wire:click="saveSelectedForm"
                        @disabled(! $canManageForms || ! $fb_selected_form_id)
                        class="h-9 px-3 sm:px-4 rounded-xl text-xs font-bold text-white shadow-sm transition-all disabled:opacity-40 hover:opacity-90 inline-flex items-center gap-1.5"
                        style="background: var(--accent);">
                    <iconify-icon icon="solar:diskette-bold" width="16"></iconify-icon>
                    <span class="hidden sm:inline">{{ __('forms_builder.save') }}</span>
                </button>
            </div>
        </div>

        {{-- Erreurs de sauvegarde --}}
        @php
            $formSaveErrorKeys = ['fb_selected_form_name', 'fb_selected_form_category_id', 'fb_selected_form_target_user_id', 'fb_selected_form_slug', 'fb_selected_form_public_title', 'fb_selected_form_public_description', 'fb_selected_form_public_thank_you', 'fb_selected_form_description'];
            $viewErrors = isset($errors) ? $errors : new \Illuminate\Support\ViewErrorBag();
            $formSaveErrors = collect($formSaveErrorKeys)->flatMap(fn ($key) => $viewErrors->get($key))->filter()->values();
        @endphp
        @if($formSaveErrors->isNotEmpty())
            <div class="mx-4 sm:mx-5 lg:mx-6 mb-0 mt-0 py-2.5 px-3 border-b border-amber-100 bg-amber-50/40">
                <div class="flex items-start gap-2.5">
                    <iconify-icon icon="solar:danger-triangle-bold" width="16" class="text-amber-500 shrink-0 mt-0.5"></iconify-icon>
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-bold text-amber-800">{{ __('forms_builder.save_error') }}</p>
                        <ul class="mt-0.5 list-inside list-disc text-[10px] text-amber-600 space-y-0.5">
                            @foreach($formSaveErrors as $msg)
                                <li>{{ $msg }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" @click="propsDrawer = true" class="shrink-0 rounded-lg border border-amber-200 bg-white px-2 py-1 text-[10px] font-bold text-amber-700 hover:bg-amber-50 transition-colors lg:hidden">
                        {{ __('forms_builder.open_properties') }}
                    </button>
                </div>
            </div>
        @endif

        {{-- Onglets --}}
        @if($fb_selected_form_id)
        <nav class="builder-tabs-scroll overflow-x-auto overscroll-x-contain px-3 sm:px-5 lg:px-6" aria-label="{{ __('forms_builder.tabs_label') }}">
            <div class="flex min-w-max gap-0">
                <button type="button" wire:click="setActiveTab('champs')" wire:loading.attr="disabled" wire:target="setActiveTab"
                        class="relative whitespace-nowrap px-3 py-2.5 text-xs font-semibold transition-colors {{ $activeTab === 'champs' ? 'text-slate-900' : 'text-slate-400 hover:text-slate-600' }}">
                    {{ __('forms_builder.fields_tab') }}
                    @if($activeTab === 'champs')
                        <span class="absolute bottom-0 left-0 right-0 h-[2px] rounded-full" style="background: var(--accent);"></span>
                    @endif
                </button>
                <button type="button" wire:click="setActiveTab('assignations')" wire:loading.attr="disabled" wire:target="setActiveTab"
                        class="relative flex items-center gap-1.5 whitespace-nowrap px-3 py-2.5 text-xs font-semibold transition-colors {{ $activeTab === 'assignations' ? 'text-slate-900' : 'text-slate-400 hover:text-slate-600' }}">
                    {{ __('forms_builder.assignments_tab') }}
                    @if($assignments->where('status.value', 'pending')->count() > 0)
                        <span class="inline-flex items-center justify-center h-4 min-w-[16px] rounded-full bg-amber-100 px-1 text-[9px] font-bold text-amber-700">{{ $assignments->where('status.value', 'pending')->count() }}</span>
                    @endif
                    @if($activeTab === 'assignations')
                        <span class="absolute bottom-0 left-0 right-0 h-[2px] rounded-full" style="background: var(--accent);"></span>
                    @endif
                </button>
                <button type="button" wire:click="setActiveTab('reponses')" wire:loading.attr="disabled" wire:target="setActiveTab"
                        class="relative whitespace-nowrap px-3 py-2.5 text-xs font-semibold transition-colors {{ $activeTab === 'reponses' ? 'text-slate-900' : 'text-slate-400 hover:text-slate-600' }}">
                    {{ __('forms_builder.responses_tab') }}
                    @if($activeTab === 'reponses')
                        <span class="absolute bottom-0 left-0 right-0 h-[2px] rounded-full" style="background: var(--accent);"></span>
                    @endif
                </button>
            </div>
        </nav>
        @endif
    </header>

    {{-- ═══════ CONTENU PRINCIPAL ═══════ --}}
    <div class="flex-1 flex min-h-0 overflow-hidden">
    @if($activeTab === 'champs')
        {{-- Palette gauche (desktop) --}}
        <aside class="hidden lg:flex fb-sidebar-palette bg-white border-r border-slate-100 shrink-0 flex-col overflow-hidden transition-[width] duration-200 ease-out">
            @include('livewire.admin.form-builder-palette')
        </aside>

        {{-- Canvas central --}}
        <div class="flex-1 flex flex-col overflow-hidden min-w-0 bg-[#fafbfc]">
            <div class="flex flex-1 justify-center overflow-y-auto overscroll-y-contain px-3 py-6 pb-[max(1.25rem,env(safe-area-inset-bottom))] sm:px-8 sm:py-10 lg:px-12 lg:py-12 xl:px-16">
                <div class="w-full max-w-2xl xl:max-w-3xl flex flex-col">
                    @include('livewire.admin.form-builder-canvas')
                </div>
            </div>
        </div>

        {{-- Proprietes droite (desktop) --}}
        <aside class="hidden lg:flex fb-sidebar-properties bg-white border-l border-slate-100 flex-col shrink-0 z-10 transition-[width] duration-200 ease-out">
            <div class="flex-1 overflow-y-auto custom-scrollbar p-4 min-[1100px]:p-5 xl:p-6" wire:key="props-desktop-{{ $fb_selected_field_id ?? 'none' }}">
                @include('livewire.admin.form-builder-properties')
            </div>
        </aside>

        {{-- Palette mobile (FAB + bottom sheet) --}}
        <div class="lg:hidden">
            @include('livewire.admin.form-builder-palette')
        </div>
    @elseif($activeTab === 'assignations')
        {{-- ═══════ ONGLET ASSIGNATIONS ═══════ --}}
        <div class="flex-1 overflow-y-auto overscroll-y-contain custom-scrollbar bg-[#fafbfc]">
            <div class="mx-auto w-full max-w-4xl space-y-6 px-3 py-5 pb-[max(1.25rem,env(safe-area-inset-bottom))] sm:px-6 sm:py-8 lg:px-8">

                @if($canManageForms)
                {{-- Nouvelle assignation --}}
                <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden" x-data="{ assignMode: 'user' }">
                    <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-slate-50 bg-slate-50/30">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white shrink-0" style="background: var(--accent);">
                                <iconify-icon icon="solar:user-plus-bold" width="18"></iconify-icon>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">{{ __('forms_builder.new_assignment') }}</h3>
                                <p class="text-[11px] text-slate-500 mt-0.5">Choisissez un destinataire pour ce formulaire</p>
                            </div>
                        </div>
                    </div>

                    <div class="px-5 sm:px-6 py-5">
                        {{-- Selecteur de mode : Utilisateur / Fonction --}}
                        <div class="flex gap-0 mb-5 bg-slate-100/60 rounded-xl p-0.5">
                            <button type="button" @click="assignMode = 'user'"
                                    class="flex-1 py-2 text-xs font-semibold rounded-lg transition-all text-center flex items-center justify-center gap-1.5"
                                    :class="assignMode === 'user' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'">
                                <iconify-icon icon="solar:user-linear" width="15"></iconify-icon>
                                {{ __('forms_builder.user') }}
                            </button>
                            <button type="button" @click="assignMode = 'function'"
                                    class="flex-1 py-2 text-xs font-semibold rounded-lg transition-all text-center flex items-center justify-center gap-1.5"
                                    :class="assignMode === 'function' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'">
                                <iconify-icon icon="solar:users-group-rounded-linear" width="15"></iconify-icon>
                                {{ __('forms_builder.or_function') }}
                            </button>
                        </div>

                        {{-- Formulaire assignation utilisateur --}}
                        <div x-show="assignMode === 'user'" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3">
                                <div class="flex-1 min-w-0">
                                    <label class="text-[11px] font-semibold text-slate-500 mb-1.5 block">{{ __('forms_builder.user') }}</label>
                                    <x-select-input wire:model.defer="assign_user_id" wire:loading.attr="disabled" wire:target="assignForm">
                                        <option value="">{{ __('forms_builder.choose') }}</option>
                                        @foreach($members as $m)
                                            <option value="{{ $m->user_id }}">{{ $m->user?->name ?? '—' }}</option>
                                        @endforeach
                                    </x-select-input>
                                    <x-input-error :messages="$errors->get('assign_user_id')" />
                                </div>
                                <div class="shrink-0">
                                    <button type="button" wire:click="assignForm" wire:loading.attr="disabled" wire:target="assignForm"
                                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:opacity-90 transition-all"
                                            style="background: var(--accent);">
                                        <iconify-icon icon="solar:user-plus-bold" width="16"></iconify-icon>
                                        {{ __('forms_builder.assign') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Formulaire assignation fonction --}}
                        <div x-show="assignMode === 'function'" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3">
                                <div class="flex-1 min-w-0">
                                    <label class="text-[11px] font-semibold text-slate-500 mb-1.5 block">{{ __('forms_builder.or_function') }}</label>
                                    <x-select-input wire:model.defer="assign_function_id" wire:loading.attr="disabled" wire:target="assignForm">
                                        <option value="">{{ __('forms_builder.choose') }}</option>
                                        @foreach($organizationFunctions as $fn)
                                            <option value="{{ $fn->id }}">{{ $fn->name }}</option>
                                        @endforeach
                                    </x-select-input>
                                    <x-input-error :messages="$errors->get('assign_function_id')" />
                                </div>
                                <div class="shrink-0">
                                    <button type="button" wire:click="assignForm" wire:loading.attr="disabled" wire:target="assignForm"
                                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:opacity-90 transition-all"
                                            style="background: var(--accent);">
                                        <iconify-icon icon="solar:users-group-rounded-bold" width="16"></iconify-icon>
                                        {{ __('forms_builder.assign') }}
                                    </button>
                                </div>
                            </div>
                            @if($organizationFunctions->isEmpty())
                                <p class="mt-3 text-[11px] text-slate-400 flex items-center gap-1.5">
                                    <iconify-icon icon="solar:info-circle-linear" width="14"></iconify-icon>
                                    Aucune fonction n'est configurée. Ajoutez-en dans les paramètres.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                {{-- Liste des assignations --}}
                <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
                    <div class="px-5 sm:px-6 py-4 border-b border-slate-50 bg-slate-50/30 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">{{ __('forms_builder.assignments_count') }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $assignments->count() }} {{ $assignments->count() <= 1 ? 'assignation' : 'assignations' }}</p>
                        </div>
                        @if($assignments->isNotEmpty())
                            @php
                                $pendingCount = $assignments->filter(fn($a) => ($a->status instanceof \App\Enums\FormAssignmentStatus ? $a->status->value : (string) $a->status) === 'pending')->count();
                                $submittedCount = $assignments->filter(fn($a) => ($a->status instanceof \App\Enums\FormAssignmentStatus ? $a->status->value : (string) $a->status) === 'submitted')->count();
                            @endphp
                            <div class="flex items-center gap-2">
                                @if($pendingCount > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-amber-50 text-[10px] font-bold text-amber-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                        {{ $pendingCount }} en attente
                                    </span>
                                @endif
                                @if($submittedCount > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-emerald-50 text-[10px] font-bold text-emerald-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        {{ $submittedCount }} soumis
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="divide-y divide-slate-50">
                        @forelse($assignments as $a)
                            @php
                                $aStatus = $a->status instanceof \App\Enums\FormAssignmentStatus ? $a->status->value : (string) $a->status;
                                $aBadge = match($aStatus) {
                                    'submitted' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'icon' => 'solar:check-circle-bold', 'label' => __('forms_builder.submitted')],
                                    'overdue' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'icon' => 'solar:clock-circle-bold', 'label' => __('forms_builder.overdue')],
                                    'expired' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-500', 'icon' => 'solar:lock-keyhole-bold', 'label' => __('forms_builder.expired')],
                                    default => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'icon' => 'solar:clock-circle-linear', 'label' => __('forms_builder.pending')],
                                };
                                $assigneeName = $a->user?->name ?? $a->organizationFunction?->name ?? '—';
                                $initial = mb_substr($assigneeName, 0, 1);
                                $isFunction = ! $a->user_id && $a->organization_function_id;
                            @endphp
                            <div class="group flex flex-wrap items-start gap-x-3 gap-y-2.5 px-4 py-3.5 transition-colors hover:bg-slate-50/40 sm:flex-nowrap sm:items-center sm:gap-4 sm:px-5 sm:py-3.5">
                                <div class="w-10 h-10 rounded-xl {{ $isFunction ? 'bg-blue-50 text-blue-600' : 'bg-slate-50 text-slate-600' }} flex items-center justify-center font-bold text-sm shrink-0">
                                    @if($isFunction)
                                        <iconify-icon icon="solar:users-group-rounded-bold" width="18"></iconify-icon>
                                    @else
                                        {{ $initial }}
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-semibold text-slate-900 truncate">{{ $assigneeName }}</p>
                                        @if($isFunction)
                                            <span class="text-[9px] font-bold uppercase tracking-wider text-blue-500 bg-blue-50 px-1.5 py-0.5 rounded">Fonction</span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                        <span>{{ __('forms_builder.by') }} <span class="font-medium text-slate-600">{{ $a->assignedBy?->name ?? '—' }}</span></span>
                                        @if($a->due_date)
                                            <span class="text-slate-300">·</span>
                                            <span class="inline-flex items-center gap-1">
                                                <iconify-icon icon="solar:calendar-linear" width="12" class="text-slate-400"></iconify-icon>
                                                {{ __('forms_builder.due') }} {{ $a->due_date->format('d/m/Y') }}
                                            </span>
                                        @endif
                                        @if($a->expires_at)
                                            <span class="text-slate-300">·</span>
                                            <span class="inline-flex items-center gap-1 {{ $aStatus === 'expired' ? 'text-slate-500 font-medium' : '' }}">
                                                <iconify-icon icon="solar:lock-keyhole-linear" width="12" class="text-slate-400"></iconify-icon>
                                                {{ __('forms_builder.assign_expires_at') }} {{ $a->expires_at->format('d/m/Y H:i') }}
                                            </span>
                                        @endif
                                    </p>
                                </div>
                                <div class="flex basis-full shrink-0 items-center justify-end gap-3 sm:basis-auto sm:w-auto sm:justify-end">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-[11px] font-bold {{ $aBadge['bg'] }} {{ $aBadge['text'] }}">
                                        <iconify-icon icon="{{ $aBadge['icon'] }}" width="14"></iconify-icon>
                                        {{ $aBadge['label'] }}
                                    </span>
                                    @if($canManageForms && $aStatus === 'pending')
                                        <button type="button" @click="$dispatch('confirm-action', { title: 'Supprimer', message: '{{ __('forms_builder.delete_assignment_confirm') }}', confirmLabel: 'Supprimer', variant: 'danger', onConfirm: () => $wire.deleteAssignment({{ $a->id }}) })"
                                                class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors opacity-0 group-hover:opacity-100 sm:opacity-100">
                                            <iconify-icon icon="solar:trash-bin-trash-linear" width="16"></iconify-icon>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="px-5 sm:px-6 py-16 text-center">
                                <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto mb-4">
                                    <iconify-icon icon="solar:user-check-linear" width="28" class="text-slate-300"></iconify-icon>
                                </div>
                                <h4 class="text-sm font-bold text-slate-700">{{ __('forms_builder.no_assignments') }}</h4>
                                <p class="text-xs text-slate-400 mt-1.5 max-w-xs mx-auto">Assignez ce formulaire à un utilisateur ou une fonction pour qu'il puisse le remplir.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @elseif($activeTab === 'reponses')
        {{-- ═══════ ONGLET RÉPONSES ═══════ --}}
        <div class="flex-1 overflow-y-auto overscroll-y-contain custom-scrollbar bg-[#fafbfc]">
            <div class="mx-auto w-full max-w-4xl px-3 py-5 pb-[max(1.25rem,env(safe-area-inset-bottom))] sm:px-6 sm:py-8 lg:px-8 space-y-4">

                {{-- Header avec compteur et lien --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                            <iconify-icon icon="solar:chart-2-bold-duotone" width="18"></iconify-icon>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">{{ __('forms_builder.responses_tab') }}</h3>
                            <p class="text-[11px] text-slate-400">{{ trans_choice('forms_builder.response_count', $responsesTotal, ['count' => $responsesTotal]) }}</p>
                        </div>
                    </div>
                    @if($selectedForm && $responsesTotal > 0)
                        <a href="{{ route('admin.forms.responses', ['form' => $selectedForm->id]) }}"
                           wire:navigate.hover
                           class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold text-white shadow-sm hover:opacity-90 transition-all"
                           style="background: var(--accent);">
                            <iconify-icon icon="solar:eye-bold" width="15"></iconify-icon>
                            {{ __('forms_builder.view_all_responses') }}
                        </a>
                    @endif
                </div>

                {{-- Liste des réponses récentes --}}
                @if($recentResponses->isNotEmpty())
                    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
                        @foreach($recentResponses as $response)
                            @php
                                $sourceBadge = match($response->submitted_from) {
                                    'public' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'label' => __('forms_builder.source_public')],
                                    'internal_assignment' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-600', 'label' => __('forms_builder.source_internal_assignment')],
                                    'internal_team', 'internal_team_slug' => ['bg' => 'bg-slate-50', 'text' => 'text-slate-500', 'label' => __('forms_builder.source_internal_team')],
                                    default => ['bg' => 'bg-slate-50', 'text' => 'text-slate-500', 'label' => __('forms_builder.source_internal')],
                                };
                            @endphp
                            <div class="px-4 sm:px-5 py-3 flex items-center justify-between gap-3 {{ !$loop->last ? 'border-b border-slate-100/80' : '' }}">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <p class="text-xs font-semibold text-slate-900 truncate">
                                            {{ $response->respondent_name ?? $response->user?->name ?? __('Anonyme') }}
                                        </p>
                                        <span class="shrink-0 px-1.5 py-0.5 rounded-md text-[9px] font-medium {{ $sourceBadge['bg'] }} {{ $sourceBadge['text'] }}">
                                            {{ $sourceBadge['label'] }}
                                        </span>
                                    </div>
                                    <p class="text-[10px] text-slate-400 mt-0.5">
                                        {{ $response->respondent_email ?? $response->user?->email ?? '' }}
                                        @if($response->respondent_email || $response->user?->email)
                                            <span class="text-slate-300 mx-1">&middot;</span>
                                        @endif
                                        {{ $response->created_at->format('d/m/Y H:i') }}
                                        <span class="text-slate-300 mx-1">&middot;</span>
                                        v{{ $response->form_version }}
                                        @if($response->ticket_id)
                                            <span class="text-slate-300 mx-1">&middot;</span>
                                            <span class="text-[var(--accent)]">#{{ $response->ticket_id }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Lien vers toutes les réponses si plus de 5 --}}
                    @if($responsesTotal > 5 && $selectedForm)
                        <div class="text-center">
                            <a href="{{ route('admin.forms.responses', ['form' => $selectedForm->id]) }}"
                               wire:navigate.hover
                               class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-slate-700 transition-colors">
                                <iconify-icon icon="solar:arrow-right-linear" width="14"></iconify-icon>
                                {{ __('forms_builder.view_remaining_responses', ['count' => $responsesTotal - 5]) }}
                            </a>
                        </div>
                    @endif
                @else
                    {{-- État vide --}}
                    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-8 sm:p-10 text-center">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-50 text-slate-300 mx-auto mb-4">
                            <iconify-icon icon="solar:inbox-linear" width="28"></iconify-icon>
                        </div>
                        <h3 class="text-sm font-semibold text-slate-600 mb-1">{{ __('forms_builder.no_responses') }}</h3>
                        <p class="text-xs text-slate-400">{{ __('forms_builder.no_responses_hint') }}</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
    </div>

    {{-- ═══════ DRAWER PALETTE (tablet/mobile — left) ═══════ --}}
    <div class="lg:hidden fixed inset-0 z-[100]" x-show="paletteDrawer" x-cloak style="display:none;">
        <div
            class="absolute inset-0 z-0 bg-slate-900/45 backdrop-blur-sm"
            x-show="paletteDrawer"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="paletteDrawer = false"
        ></div>
        {{-- Plein écran (viewport < lg) : aligné sur le drawer Infos ticket (fondu voile + glissé doux) --}}
        <div
            class="absolute inset-0 z-10 flex w-full max-w-none flex-col overflow-hidden bg-white shadow-2xl"
            x-show="paletteDrawer"
            x-transition:enter="transform transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-300"
            x-transition:enter-start="-translate-x-full opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="-translate-x-full opacity-0"
            @click.stop
        >
            <div class="flex h-12 shrink-0 items-center justify-end border-b border-slate-50 px-3">
                <button type="button" @click="paletteDrawer = false" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-50 hover:text-slate-600" aria-label="{{ __('forms_builder.close') }}">
                    <iconify-icon icon="solar:close-circle-linear" width="16"></iconify-icon>
                </button>
            </div>
            <div class="flex min-h-0 flex-1 flex-col overflow-hidden">
                @include('livewire.admin.form-builder-palette', ['paletteDrawerEmbed' => true])
            </div>
        </div>
    </div>

    {{-- ═══════ DRAWER PROPRIÉTÉS (tablet/mobile — right) ═══════ --}}
    <div class="lg:hidden fixed inset-0 z-[100]" x-show="propsDrawer" x-cloak style="display:none;">
        <div
            class="absolute inset-0 z-0 bg-slate-900/45 backdrop-blur-sm"
            x-show="propsDrawer"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="propsDrawer = false"
        ></div>
        {{-- Plein écran (viewport < lg) : aligné sur le drawer Infos ticket --}}
        <div
            class="absolute inset-0 z-10 flex w-full max-w-none flex-col overflow-hidden bg-white shadow-2xl"
            x-show="propsDrawer"
            x-transition:enter="transform transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-300"
            x-transition:enter-start="translate-x-full opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-full opacity-0"
            @click.stop
        >
            <div class="h-12 px-4 border-b border-slate-50 flex items-center justify-between shrink-0">
                <h3 class="text-xs font-bold text-slate-900">{{ __('forms_builder.properties') }}</h3>
                <button type="button" @click="propsDrawer = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition" aria-label="{{ __('forms_builder.close') }}">
                    <iconify-icon icon="solar:close-circle-linear" width="16"></iconify-icon>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto overscroll-y-contain custom-scrollbar p-5 pb-[max(1.25rem,env(safe-area-inset-bottom))]" wire:key="props-mobile-{{ $fb_selected_field_id ?? 'none' }}">
                @include('livewire.admin.form-builder-properties')
            </div>
        </div>
    </div>
</div>
