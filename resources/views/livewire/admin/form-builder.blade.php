<div
    class="builder-full-bleed h-builder flex flex-col bg-white overflow-hidden"
    x-data="{
        mobileSidebarOpen: false,
        closeMobile() { this.mobileSidebarOpen = false },
        shareCopied: false,
    }"
    x-on:field-selected.window="if (window.innerWidth < 1024) mobileSidebarOpen = true"
>
    <!-- HEADER -->
    <header class="shrink-0 z-20 border-b border-slate-200/60 bg-white">
        <div class="h-14 px-4 sm:px-5 lg:px-6 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2 min-w-0">
                <a href="{{ route('admin.settings') }}" class="p-2 -ml-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors" aria-label="{{ __('forms_builder.back') }}">
                    <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
                </a>
                <!-- Mobile: Properties sidebar button -->
                <div class="lg:hidden">
                    <button type="button" @click="mobileSidebarOpen = true" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors" title="{{ __('forms_builder.properties') }}">
                        <iconify-icon icon="solar:slider-minimalistic-horizontal-linear" width="18"></iconify-icon>
                    </button>
                </div>
                <!-- Form Dropdown Selector -->
                <div x-data="{ formDropdownOpen: false }" class="relative min-w-0">
                    <button type="button" @click="formDropdownOpen = !formDropdownOpen"
                            class="flex items-center gap-2 min-w-0 group py-1 px-2 rounded-lg hover:bg-slate-50 transition-colors">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2.5">
                                <h1 class="text-sm font-bold text-slate-900 truncate">
                                    {{ $fb_selected_form_name ?: __('forms_builder.select_form') }}
                                </h1>
                                @if($fb_selected_form_id)
                                    @php
                                        $statusBadge = match($fb_selected_form_status) {
                                            'published' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'dot' => 'bg-emerald-500', 'label' => __('forms_builder.published')],
                                            'archived' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-500', 'dot' => 'bg-slate-400', 'label' => __('forms_builder.archived')],
                                            default => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'dot' => 'bg-amber-500', 'label' => __('forms_builder.draft')],
                                        };
                                    @endphp
                                    <span class="shrink-0 inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full {{ $statusBadge['bg'] }} {{ $statusBadge['text'] }} text-[10px] font-bold">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $statusBadge['dot'] }}"></span>
                                        {{ $statusBadge['label'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <iconify-icon icon="solar:alt-arrow-down-linear" width="14" class="shrink-0 text-slate-400 group-hover:text-slate-600 transition-colors"></iconify-icon>
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

            <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                @if($fb_selected_form_id)
                    @if($fb_selected_form_slug)
                        <a href="{{ url('/f/' . $fb_selected_form_slug) }}" target="_blank"
                           class="h-8 px-2.5 rounded-lg text-xs font-medium text-slate-500 hover:text-slate-700 hover:bg-slate-50 transition inline-flex items-center gap-1.5">
                            <iconify-icon icon="solar:eye-linear" width="16"></iconify-icon>
                            <span class="hidden sm:inline">{{ __('forms_builder.preview') }}</span>
                        </a>
                        <button type="button"
                                @click="navigator.clipboard.writeText('{{ url('/f/' . $fb_selected_form_slug) }}'); shareCopied = true; setTimeout(() => shareCopied = false, 2000)"
                                class="h-8 px-2.5 rounded-lg text-xs font-medium text-slate-500 hover:text-slate-700 hover:bg-slate-50 transition inline-flex items-center gap-1.5">
                            <iconify-icon icon="solar:share-linear" width="16"></iconify-icon>
                            <span class="hidden sm:inline" x-text="shareCopied ? '{{ __('forms_builder.share_copied') }}' : '{{ __('forms_builder.share') }}'"></span>
                        </button>
                    @endif

                    <div class="hidden sm:block w-px h-5 bg-slate-200 mx-0.5"></div>

                    @if($fb_selected_form_status !== 'published')
                        <button type="button" wire:click="publishForm"
                                @disabled(! $canManageForms)
                                class="h-8 px-3 rounded-lg border border-emerald-200 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 transition disabled:opacity-50 inline-flex items-center gap-1.5">
                            <iconify-icon icon="solar:check-circle-bold" width="15"></iconify-icon>
                            <span class="hidden sm:inline">{{ __('forms_builder.publish') }}</span>
                        </button>
                    @else
                        <button type="button" wire:click="unpublishForm"
                                @disabled(! $canManageForms)
                                class="h-8 px-3 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition disabled:opacity-50 inline-flex items-center gap-1.5">
                            <iconify-icon icon="solar:pause-circle-linear" width="15"></iconify-icon>
                            <span class="hidden sm:inline">{{ __('forms_builder.draft') }}</span>
                        </button>
                    @endif
                    <button type="button" wire:click="duplicateSelectedForm"
                            @disabled(! $canManageForms)
                            class="hidden sm:inline-flex h-8 px-2.5 rounded-lg text-xs font-medium text-slate-500 hover:text-slate-700 hover:bg-slate-50 transition disabled:opacity-50 items-center gap-1.5">
                        <iconify-icon icon="solar:copy-linear" width="15"></iconify-icon>
                        <span class="hidden md:inline">{{ __('forms_builder.duplicate') }}</span>
                    </button>
                @endif
                <button type="button" wire:click="saveSelectedForm"
                        @disabled(! $canManageForms || ! $fb_selected_form_id)
                        class="h-8 px-4 rounded-lg text-white text-xs font-bold inline-flex items-center gap-1.5 transition-all disabled:opacity-40 hover:opacity-90"
                        style="background: var(--accent);">
                    <iconify-icon icon="solar:diskette-bold" width="15"></iconify-icon>
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
            <div class="mx-4 sm:mx-5 lg:mx-6 mb-0 mt-2 rounded-xl border border-amber-200 bg-amber-50/60 px-3 py-2.5">
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
        <nav class="flex gap-0 px-4 sm:px-5 lg:px-6" aria-label="{{ __('forms_builder.tabs_label') }}">
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
    </header>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex overflow-hidden">
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
            <!-- ASSIGNMENTS TAB -->
            <div class="flex-1 overflow-y-auto custom-scrollbar bg-gradient-to-b from-slate-50/80 to-white">
                <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-8">
                    @if($canManageForms)
                    {{-- New assignment card --}}
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm shadow-slate-200/30 overflow-hidden">
                        <div class="px-6 sm:px-8 pt-6 pb-5 border-b border-slate-100 bg-gradient-to-b from-white to-slate-50/30">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white shadow-sm" style="background: var(--accent);">
                                    <iconify-icon icon="solar:user-plus-bold" width="20"></iconify-icon>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">{{ __('forms_builder.new_assignment') }}</h3>
                                    <p class="text-[11px] text-slate-500 mt-0.5">{{ __('forms_builder.assign') }} ce formulaire à une personne ou une fonction</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 sm:p-8">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 sm:gap-6">
                                <div class="sm:col-span-2 space-y-4">
                                    <div>
                                        <label class="text-[11px] font-semibold text-slate-600 mb-2 block">{{ __('forms_builder.user') }}</label>
                                        <select wire:model="assign_user_id" class="input-builder w-full text-xs py-2.5 rounded-lg border-slate-200 focus:ring-2 focus:ring-[var(--accent)]/20 focus:border-[var(--accent)] transition-all">
                                            <option value="">{{ __('forms_builder.choose') }}</option>
                                            @foreach($members as $m)
                                                <option value="{{ $m->user_id }}">{{ $m->user?->name ?? '—' }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('assign_user_id')" />
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="flex-1 h-px bg-slate-200"></span>
                                        <span class="text-[11px] font-medium text-slate-400">ou</span>
                                        <span class="flex-1 h-px bg-slate-200"></span>
                                    </div>
                                    <div>
                                        <label class="text-[11px] font-semibold text-slate-600 mb-2 block">{{ __('forms_builder.or_function') }}</label>
                                        <select wire:model="assign_function_id" class="input-builder w-full text-xs py-2.5 rounded-lg border-slate-200 focus:ring-2 focus:ring-[var(--accent)]/20 focus:border-[var(--accent)] transition-all">
                                            <option value="">{{ __('forms_builder.choose') }}</option>
                                            @foreach($organizationFunctions as $fn)
                                                <option value="{{ $fn->id }}">{{ $fn->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('assign_function_id')" />
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-[11px] font-semibold text-slate-600 mb-2 block">{{ __('forms_builder.due_date') }}</label>
                                    <input type="date" wire:model="assign_due_date" class="input-builder w-full text-xs py-2.5 rounded-lg border-slate-200 focus:ring-2 focus:ring-[var(--accent)]/20 focus:border-[var(--accent)] transition-all flex-1 min-h-[38px]">
                                </div>
                            </div>
                            <div class="mt-6 pt-5 border-t border-slate-100 flex flex-wrap items-center gap-3">
                                <button type="button" wire:click="assignForm"
                                        class="h-10 px-5 text-white text-xs font-bold rounded-xl inline-flex items-center gap-2.5 shadow-sm hover:opacity-95 active:scale-[0.98] transition-all"
                                        style="background: var(--accent);">
                                    <iconify-icon icon="solar:user-plus-bold" width="16"></iconify-icon>
                                    {{ __('forms_builder.assign') }}
                                </button>
                                <span class="text-[11px] text-slate-400">Uniquement utilisateur <strong>ou</strong> fonction</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Assignments list --}}
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm shadow-slate-200/30 overflow-hidden">
                        <div class="px-6 sm:px-8 py-4 sm:py-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center">
                                    <iconify-icon icon="solar:list-check-bold" width="18" class="text-slate-600"></iconify-icon>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">{{ __('forms_builder.assignments_count') }}</h3>
                                    <p class="text-[11px] text-slate-500">{{ $assignments->count() }} {{ $assignments->count() === 1 ? 'assignation' : 'assignations' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @forelse($assignments as $a)
                                @php
                                    $aStatus = $a->status instanceof \App\Enums\FormAssignmentStatus ? $a->status->value : (string) $a->status;
                                    $aBadge = match($aStatus) {
                                        'submitted' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'icon' => 'solar:check-circle-bold', 'label' => __('forms_builder.submitted')],
                                        'overdue' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'icon' => 'solar:clock-circle-bold', 'label' => __('forms_builder.overdue')],
                                        default => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'icon' => 'solar:clock-circle-linear', 'label' => __('forms_builder.pending')],
                                    };
                                    $assigneeName = $a->user?->name ?? $a->organizationFunction?->name ?? '—';
                                    $initial = mb_substr($assigneeName, 0, 1);
                                @endphp
                                <div class="px-6 sm:px-8 py-4 sm:py-4.5 flex items-center gap-4 sm:gap-5 hover:bg-slate-50/50 transition-colors group">
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
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-[11px] font-bold {{ $aBadge['bg'] }} {{ $aBadge['text'] }}">
                                            <iconify-icon icon="{{ $aBadge['icon'] }}" width="14"></iconify-icon>
                                            {{ $aBadge['label'] }}
                                        </span>
                                        @if($canManageForms && $aStatus === 'pending')
                                            <button type="button" wire:click="deleteAssignment({{ $a->id }})" wire:confirm="{{ __('forms_builder.delete_assignment_confirm') }}"
                                                    class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors opacity-0 group-hover:opacity-100 sm:opacity-100">
                                                <iconify-icon icon="solar:trash-bin-trash-linear" width="16"></iconify-icon>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="px-6 sm:px-8 py-16 sm:py-20 text-center">
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
            <!-- RESPONSES TAB -->
            <div class="flex-1 overflow-y-auto custom-scrollbar bg-slate-50/20 p-4 sm:p-8">
                <div class="max-w-3xl mx-auto">
                    <div class="bg-white rounded-xl border border-slate-200/80 p-10 text-center">
                        <iconify-icon icon="solar:chart-2-bold-duotone" width="44" class="text-slate-300 mb-4"></iconify-icon>
                        <h3 class="text-sm font-bold text-slate-900 mb-1.5">{{ __('forms_builder.responses_tab') }}</h3>
                        <p class="text-xs text-slate-500 mb-5">{{ __('forms_builder.view_responses_hint') }}</p>
                        @if($fb_selected_form_id)
                            <a href="{{ route('admin.forms.responses', $fb_selected_form_id) }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold text-white rounded-lg hover:opacity-90 transition-all"
                               style="background: var(--accent);">
                                <iconify-icon icon="solar:eye-bold" width="15"></iconify-icon>
                                {{ __('forms_builder.view_responses') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif
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
