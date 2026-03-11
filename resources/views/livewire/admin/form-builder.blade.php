<div
    class="w-full max-w-full min-w-0 mx-auto"
    x-data="{
        mobileSidebarOpen: false,
        closeMobile() { this.mobileSidebarOpen = false },
        shareCopied: false,
    }"
    x-on:field-selected.window="if (window.innerWidth < 1024) mobileSidebarOpen = true"
>
    {{-- En-tête page : titre + description (convention Tickets / Rapports) --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6 sm:mb-8">
        <div class="min-w-0 flex-1">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight sm:text-2xl lg:text-3xl min-[1920px]:text-4xl">{{ __('forms_builder.editor_title') }}</h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">{{ __('forms_builder.page_subtitle') }}</p>
        </div>
    </div>

    {{-- Statistiques (KPI) --}}
    @php $stats = $stats ?? ['forms_total' => 0, 'forms_published' => 0, 'assignments_pending' => 0, 'responses_total' => 0]; @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500 truncate block">{{ __('forms_builder.stat_forms') }}</span>
                    <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900">{{ $stats['forms_total'] ?? 0 }}</div>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 shrink-0">
                    <iconify-icon icon="solar:document-text-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500 truncate block">{{ __('forms_builder.stat_published') }}</span>
                    <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900">{{ $stats['forms_published'] ?? 0 }}</div>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 shrink-0">
                    <iconify-icon icon="solar:check-circle-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500 truncate block">{{ __('forms_builder.stat_pending_assignments') }}</span>
                    <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900">{{ $stats['assignments_pending'] ?? 0 }}</div>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600 shrink-0">
                    <iconify-icon icon="solar:clock-circle-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500 truncate block">{{ __('forms_builder.stat_responses') }}</span>
                    <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900">{{ $stats['responses_total'] ?? 0 }}</div>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--accent)]/10 text-[var(--accent)] shrink-0">
                    <iconify-icon icon="solar:clipboard-list-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    {{-- Section Éditeur (centrée, bords arrondis) --}}
    <div class="rounded-xl sm:rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden min-h-[70vh] flex flex-col">
        {{-- Barre outil éditeur : sélecteur de formulaire + actions --}}
        <div class="shrink-0 z-20 border-b border-slate-200/60 bg-white">
            <div class="min-h-14 py-3 px-4 sm:px-5 md:px-6 lg:px-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-2 min-w-0 flex-1">
                    <div class="lg:hidden shrink-0">
                        <button type="button" @click="mobileSidebarOpen = true" class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors" title="{{ __('forms_builder.properties') }}">
                            <iconify-icon icon="solar:slider-minimalistic-horizontal-linear" width="18"></iconify-icon>
                        </button>
                    </div>
                    <div x-data="{ formDropdownOpen: false }" class="relative min-w-0 flex-1">
                        <button type="button" @click="formDropdownOpen = !formDropdownOpen"
                                class="flex items-center gap-2 min-w-0 w-full group py-1.5 px-2 rounded-xl hover:bg-slate-50 transition-colors text-left">
                            <div class="min-w-0 flex-1">
                                <span class="text-base font-bold text-slate-900 truncate block sm:text-lg">
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
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full {{ $statusBadge['bg'] }} {{ $statusBadge['text'] }} text-[10px] font-bold mt-1">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $statusBadge['dot'] }}"></span>
                                        {{ $statusBadge['label'] }}
                                    </span>
                                @endif
                            </div>
                            <iconify-icon icon="solar:alt-arrow-down-linear" width="16" class="shrink-0 text-slate-400 group-hover:text-slate-600 transition-colors"></iconify-icon>
                        </button>

                    <!-- Dropdown panel -->
                    <div x-show="formDropdownOpen" @click.away="formDropdownOpen = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute left-0 top-full mt-1.5 w-72 bg-white rounded-xl border border-slate-200 shadow-xl shadow-slate-200/50 z-50 overflow-hidden"
                         x-cloak>
                        <!-- Create new form -->
                        <div class="p-3 border-b border-slate-100">
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
                        <!-- Forms list -->
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

            <div class="flex flex-wrap items-center gap-2 sm:gap-3 min-w-0 shrink-0">
                @if($fb_selected_form_id)
                    @if($fb_selected_form_slug)
                        <a href="{{ url('/f/' . $fb_selected_form_slug) }}" target="_blank"
                           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 sm:px-4 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                            <iconify-icon icon="solar:eye-linear" width="18"></iconify-icon>
                            <span class="hidden sm:inline">{{ __('forms_builder.preview') }}</span>
                        </a>
                        <button type="button"
                                @click="navigator.clipboard.writeText('{{ url('/f/' . $fb_selected_form_slug) }}'); shareCopied = true; setTimeout(() => shareCopied = false, 2000)"
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 sm:px-4 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                            <iconify-icon icon="solar:share-linear" width="18"></iconify-icon>
                            <span class="hidden sm:inline" x-text="shareCopied ? '{{ __('forms_builder.share_copied') }}' : '{{ __('forms_builder.share') }}'"></span>
                        </button>
                    @endif

                    <div class="hidden sm:block w-px h-5 bg-slate-200 shrink-0" aria-hidden="true"></div>

                    @if($fb_selected_form_status !== 'published')
                        <button type="button" wire:click="publishForm"
                                @disabled(! $canManageForms)
                                class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-white px-3 py-2.5 sm:px-4 text-sm font-semibold text-emerald-700 shadow-sm hover:bg-emerald-50 transition-all disabled:opacity-50">
                            <iconify-icon icon="solar:check-circle-bold" width="18"></iconify-icon>
                            <span class="hidden sm:inline">{{ __('forms_builder.publish') }}</span>
                        </button>
                    @else
                        <button type="button" wire:click="unpublishForm"
                                @disabled(! $canManageForms)
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 sm:px-4 text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50 transition-all disabled:opacity-50">
                            <iconify-icon icon="solar:pause-circle-linear" width="18"></iconify-icon>
                            <span class="hidden sm:inline">{{ __('forms_builder.draft') }}</span>
                        </button>
                    @endif
                    <button type="button" wire:click="duplicateSelectedForm"
                            @disabled(! $canManageForms)
                            class="hidden sm:inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 sm:px-4 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all disabled:opacity-50">
                        <iconify-icon icon="solar:copy-linear" width="18"></iconify-icon>
                        <span class="hidden md:inline">{{ __('forms_builder.duplicate') }}</span>
                    </button>
                @endif
                <button type="button" wire:click="saveSelectedForm"
                        @disabled(! $canManageForms || ! $fb_selected_form_id)
                        class="inline-flex items-center gap-2 rounded-xl px-3 py-2.5 sm:px-4 text-sm font-semibold text-white shadow-sm transition-all disabled:opacity-40 hover:opacity-90"
                        style="background: var(--accent);">
                    <iconify-icon icon="solar:diskette-bold" width="18"></iconify-icon>
                    <span class="hidden sm:inline">{{ __('forms_builder.save') }}</span>
                    <span class="sm:hidden">{{ __('forms_builder.save_short') }}</span>
                </button>
            </div>
        </div>

        {{-- Form save errors --}}
        @php
            $formSaveErrorKeys = ['fb_selected_form_name', 'fb_selected_form_category_id', 'fb_selected_form_target_user_id', 'fb_selected_form_slug', 'fb_selected_form_public_title', 'fb_selected_form_public_description', 'fb_selected_form_public_thank_you', 'fb_selected_form_description'];
            $viewErrors = isset($errors) ? $errors : new \Illuminate\Support\ViewErrorBag();
            $formSaveErrors = collect($formSaveErrorKeys)->flatMap(fn ($key) => $viewErrors->get($key))->filter()->values();
        @endphp
        @if($formSaveErrors->isNotEmpty())
            <div class="mx-4 sm:mx-5 md:mx-6 lg:mx-8 mb-0 mt-2 rounded-xl border border-amber-200 bg-amber-50/60 px-3 py-2.5">
                <div class="flex items-start gap-3">
                    <div class="shrink-0 flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                        <iconify-icon icon="solar:danger-triangle-bold" width="18"></iconify-icon>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-amber-800">{{ __('forms_builder.save_error') }}</p>
                        <ul class="mt-1 list-inside list-disc text-[11px] text-amber-700 space-y-0.5">
                            @foreach($formSaveErrors as $msg)
                                <li>{{ $msg }}</li>
                            @endforeach
                        </ul>
                        <p class="mt-1.5 text-[11px] text-amber-600">
                            {{ __('forms_builder.save_error_hint') }}
                        </p>
                    </div>
                    <button type="button" @click="mobileSidebarOpen = true" class="shrink-0 rounded-lg border border-amber-300 bg-white px-2.5 py-1.5 text-[11px] font-bold text-amber-800 hover:bg-amber-100 transition-colors lg:hidden">
                        {{ __('forms_builder.open_properties') }}
                    </button>
                </div>
            </div>
        @endif

        <!-- TABS -->
        @if($fb_selected_form_id)
        <nav class="flex gap-0 px-4 sm:px-5 md:px-6 lg:px-8" aria-label="{{ __('forms_builder.tabs_label') }}">
            <button type="button" wire:click="$set('activeTab', 'champs')"
                    class="relative px-3.5 py-2.5 text-xs font-semibold transition-colors {{ $activeTab === 'champs' ? 'text-slate-900' : 'text-slate-500 hover:text-slate-700' }}">
                {{ __('forms_builder.fields_tab') }}
                @if($activeTab === 'champs')
                    <span class="absolute bottom-0 left-0 right-0 h-0.5 rounded-full" style="background: var(--accent);"></span>
                @endif
            </button>
            <button type="button" wire:click="$set('activeTab', 'assignations')"
                    class="relative px-3.5 py-2.5 text-xs font-semibold transition-colors flex items-center gap-1.5 {{ $activeTab === 'assignations' ? 'text-slate-900' : 'text-slate-500 hover:text-slate-700' }}">
                {{ __('forms_builder.assignments_tab') }}
                @if($assignments->where('status.value', 'pending')->count() > 0)
                    <span class="inline-flex items-center justify-center h-4 min-w-[16px] rounded-full bg-amber-100 px-1 text-[9px] font-bold text-amber-700">{{ $assignments->where('status.value', 'pending')->count() }}</span>
                @endif
                @if($activeTab === 'assignations')
                    <span class="absolute bottom-0 left-0 right-0 h-0.5 rounded-full" style="background: var(--accent);"></span>
                @endif
            </button>
            <button type="button" wire:click="$set('activeTab', 'reponses')"
                    class="relative px-3.5 py-2.5 text-xs font-semibold transition-colors {{ $activeTab === 'reponses' ? 'text-slate-900' : 'text-slate-500 hover:text-slate-700' }}">
                {{ __('forms_builder.responses_tab') }}
                @if($activeTab === 'reponses')
                    <span class="absolute bottom-0 left-0 right-0 h-0.5 rounded-full" style="background: var(--accent);"></span>
                @endif
            </button>
        </nav>
        @endif
        </div>

        <!-- MAIN CONTENT (éditeur : canvas ou onglets) -->
        <div class="flex-1 flex min-h-0 overflow-hidden">
        @if($activeTab === 'champs')
            <!-- LEFT SIDEBAR: Palette (vertical) - Desktop only -->
            <aside class="w-56 xl:w-60 bg-white border-r border-slate-200/60 shrink-0 hidden lg:flex flex-col overflow-hidden">
                @include('livewire.admin.form-builder-palette')
            </aside>

            <!-- CENTER: Canvas -->
            <div class="flex-1 flex flex-col overflow-hidden min-w-0 bg-slate-50/30">
                <div class="flex-1 overflow-y-auto custom-scrollbar p-6 sm:p-8 lg:p-10 xl:p-12 flex justify-center">
                    <div class="w-full max-w-2xl xl:max-w-3xl flex flex-col h-full">
                        @include('livewire.admin.form-builder-canvas')
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDEBAR: Properties - Desktop -->
            <aside class="w-72 xl:w-80 bg-white border-l border-slate-200/60 flex-col shrink-0 z-10 hidden lg:flex">
                <div class="flex-1 overflow-y-auto custom-scrollbar p-4 xl:p-5">
                    @include('livewire.admin.form-builder-properties')
                </div>
            </aside>

            <!-- Mobile only: Palette FAB + Drawer (pas de doublon à droite sur desktop) -->
            <div class="lg:hidden">
                @include('livewire.admin.form-builder-palette')
            </div>
        @elseif($activeTab === 'assignations')
            <!-- ASSIGNMENTS TAB : convention cartes + padding -->
            <div class="flex-1 overflow-y-auto custom-scrollbar bg-slate-50/30">
                <div class="w-full max-w-full min-w-0 mx-auto px-4 sm:px-5 md:px-6 lg:px-8 py-6 sm:py-8 space-y-6 sm:space-y-8">
                    @if($canManageForms)
                    {{-- New assignment --}}
                    <div class="rounded-xl sm:rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
                        <div class="px-4 sm:px-6 py-4 sm:py-5">
                            <div class="flex items-center gap-3 mb-5">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white shrink-0" style="background: var(--accent);">
                                    <iconify-icon icon="solar:user-plus-bold" width="18"></iconify-icon>
                                </div>
                                <h3 class="text-sm font-bold text-slate-900">{{ __('forms_builder.new_assignment') }}</h3>
                            </div>

                            <div class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3">
                                {{-- User --}}
                                <div class="flex-1 min-w-0">
                                    <label class="text-[11px] font-semibold text-slate-500 mb-1.5 block">{{ __('forms_builder.user') }}</label>
                                    <select wire:model="assign_user_id"
                                            class="input-builder w-full text-xs py-2.5 rounded-lg border-slate-200 focus:ring-2 focus:ring-[var(--accent)]/20 focus:border-[var(--accent)] transition-all">
                                        <option value="">{{ __('forms_builder.choose') }}</option>
                                        @foreach($members as $m)
                                            <option value="{{ $m->user_id }}">{{ $m->user?->name ?? '—' }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('assign_user_id')" />
                                </div>

                                {{-- Separator --}}
                                <div class="hidden sm:flex items-center justify-center pb-1">
                                    <span class="text-[11px] font-semibold text-slate-300 uppercase tracking-wider">ou</span>
                                </div>
                                <div class="flex items-center gap-3 sm:hidden">
                                    <span class="flex-1 h-px bg-slate-100"></span>
                                    <span class="text-[10px] font-semibold text-slate-300 uppercase tracking-wider">ou</span>
                                    <span class="flex-1 h-px bg-slate-100"></span>
                                </div>

                                {{-- Function --}}
                                <div class="flex-1 min-w-0">
                                    <label class="text-[11px] font-semibold text-slate-500 mb-1.5 block">{{ __('forms_builder.or_function') }}</label>
                                    <select wire:model="assign_function_id"
                                            class="input-builder w-full text-xs py-2.5 rounded-lg border-slate-200 focus:ring-2 focus:ring-[var(--accent)]/20 focus:border-[var(--accent)] transition-all">
                                        <option value="">{{ __('forms_builder.choose') }}</option>
                                        @foreach($organizationFunctions as $fn)
                                            <option value="{{ $fn->id }}">{{ $fn->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('assign_function_id')" />
                                </div>

                                {{-- Button --}}
                                <div class="shrink-0 sm:pb-0">
                                    <button type="button" wire:click="assignForm"
                                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:opacity-90 transition-all"
                                            style="background: var(--accent);">
                                        <iconify-icon icon="solar:user-plus-bold" width="16"></iconify-icon>
                                        {{ __('forms_builder.assign') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Assignments list --}}
                    <div class="rounded-xl sm:rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
                        <div class="px-4 sm:px-6 py-4 border-b border-slate-50 bg-slate-50/50">
                            <h3 class="text-base font-bold text-slate-900">{{ __('forms_builder.assignments_count') }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $assignments->count() }} {{ $assignments->count() === 1 ? 'assignation' : 'assignations' }}</p>
                        </div>
                        <div class="divide-y divide-slate-100">
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
                                @endphp
                                <div class="px-4 sm:px-6 py-3 sm:py-4 flex items-center gap-4 sm:gap-5 hover:bg-slate-50/50 transition-colors group">
                                    <div class="w-11 h-11 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-sm shrink-0" style="font-family: system-ui, sans-serif;">
                                        {{ $initial }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-slate-900 truncate">
                                            {{ $assigneeName }}
                                        </p>
                                        <p class="text-[11px] text-slate-500 mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                            <span>{{ __('forms_builder.by') }} <span class="font-medium text-slate-600">{{ $a->assignedBy?->name ?? '—' }}</span></span>
                                            @if($a->due_date)
                                                <span class="text-slate-400">·</span>
                                                <span class="inline-flex items-center gap-1">
                                                    <iconify-icon icon="solar:calendar-linear" width="12" class="text-slate-400"></iconify-icon>
                                                    {{ __('forms_builder.due') }} {{ $a->due_date->format('d/m/Y') }}
                                                </span>
                                            @endif
                                            @if($a->expires_at)
                                                <span class="text-slate-400">·</span>
                                                <span class="inline-flex items-center gap-1 {{ $aStatus === 'expired' ? 'text-slate-500 font-medium' : '' }}">
                                                    <iconify-icon icon="solar:lock-keyhole-linear" width="12" class="text-slate-400"></iconify-icon>
                                                    {{ __('forms_builder.assign_expires_at') }} {{ $a->expires_at->format('d/m/Y H:i') }}
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0">
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
                                <div class="px-4 sm:px-6 py-16 text-center">
                                    <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                                        <iconify-icon icon="solar:user-check-linear" width="32" class="text-slate-400"></iconify-icon>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-700">{{ __('forms_builder.no_assignments') }}</h4>
                                    <p class="text-xs text-slate-500 mt-1.5 max-w-xs mx-auto">Assignez ce formulaire à un utilisateur ou une fonction pour qu’il puisse le remplir.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @elseif($activeTab === 'reponses')
            <!-- RESPONSES TAB : convention cartes + padding -->
            <div class="flex-1 overflow-y-auto custom-scrollbar bg-slate-50/30">
                <div class="w-full max-w-full min-w-0 mx-auto px-4 sm:px-5 md:px-6 lg:px-8 py-6 sm:py-8">
                    <div class="rounded-xl sm:rounded-2xl border border-slate-100 bg-white shadow-sm p-8 sm:p-10 text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-300 mx-auto mb-4">
                            <iconify-icon icon="solar:chart-2-bold-duotone" width="32"></iconify-icon>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-1.5">{{ __('forms_builder.responses_tab') }}</h3>
                        <p class="text-xs sm:text-sm text-slate-500 mb-6">{{ __('forms_builder.view_responses_hint') }}</p>
                        @if($selectedForm)
                            <a href="{{ route('admin.forms.responses', $selectedForm) }}"
                               class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition-all"
                               style="background: var(--accent);">
                                <iconify-icon icon="solar:eye-bold" width="18"></iconify-icon>
                                {{ __('forms_builder.view_responses') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif
        </div>
    </div>

    <!-- MOBILE SIDEBAR DRAWER (properties only) -->
    <div class="lg:hidden fixed inset-0 z-40" x-show="mobileSidebarOpen" x-cloak style="display:none;">
        <div class="absolute inset-0 bg-black/25 backdrop-blur-[2px]" @click="closeMobile()"></div>
        <div class="absolute right-0 top-0 bottom-0 w-full max-w-[20rem] bg-white shadow-xl border-l border-slate-200/60 flex flex-col"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">
            <div class="h-13 px-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-900">{{ __('forms_builder.properties') }}</h3>
                <button type="button" @click="closeMobile()" class="p-1.5 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition" aria-label="{{ __('forms_builder.close') }}">
                    <iconify-icon icon="solar:close-circle-linear" width="16"></iconify-icon>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto custom-scrollbar p-4">
                @include('livewire.admin.form-builder-properties')
            </div>
        </div>
    </div>
</div>
