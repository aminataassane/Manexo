<div
    class="flex flex-col min-h-[32rem] rounded-2xl border border-slate-200/80 bg-white shadow-lg shadow-slate-200/50 overflow-hidden"
    x-data="{
        sidebarTab: 'elements',
        mobileSidebarOpen: false,
        openMobile(tab) { this.sidebarTab = tab; this.mobileSidebarOpen = true },
        closeMobile() { this.mobileSidebarOpen = false },
    }"
    x-on:field-selected.window="sidebarTab = 'properties'"
>
    <!-- HEADER -->
    <header class="shrink-0 z-20 border-b border-slate-100 bg-gradient-to-b from-white to-slate-50/30">
        <div class="h-[72px] px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <a href="{{ route('admin.settings') }}" class="p-2.5 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-white/80 transition-colors shadow-sm border border-transparent hover:border-slate-200" aria-label="{{ __('Retour') }}">
                    <iconify-icon icon="solar:arrow-left-linear" width="20"></iconify-icon>
                </a>
                <div class="lg:hidden flex items-center gap-1.5">
                    <button type="button" @click="openMobile('elements')" class="h-10 w-10 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition flex items-center justify-center shadow-sm" title="{{ __('Éléments') }}">
                        <iconify-icon icon="solar:widget-2-linear" width="20"></iconify-icon>
                    </button>
                    <button type="button" @click="openMobile('properties')" class="h-10 w-10 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition flex items-center justify-center shadow-sm" title="{{ __('Propriétés') }}">
                        <iconify-icon icon="solar:slider-minimalistic-horizontal-linear" width="20"></iconify-icon>
                    </button>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate flex items-center gap-2.5 flex-wrap">
                        <span class="truncate">{{ $fb_selected_form_name ?: __('Nouveau formulaire') }}</span>
                        @if($fb_selected_form_id)
                            @php
                                $statusBadge = match($fb_selected_form_status) {
                                    'published' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'label' => 'Publié'],
                                    'archived' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-500', 'label' => 'Archivé'],
                                    default => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'label' => 'Brouillon'],
                                };
                            @endphp
                            <span class="shrink-0 px-2.5 py-1 rounded-full {{ $statusBadge['bg'] }} text-[11px] font-semibold {{ $statusBadge['text'] }}">{{ $statusBadge['label'] }}</span>
                        @endif
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">{{ __('Éditeur de formulaire') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                @if($fb_selected_form_id)
                    @if($fb_selected_form_status !== 'published')
                        <button type="button" wire:click="publishForm"
                                @disabled(! $canManageForms)
                                class="h-10 px-4 rounded-xl border border-emerald-200 bg-emerald-50 text-xs font-bold text-emerald-700 hover:bg-emerald-100 transition disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center gap-2 shadow-sm">
                            <iconify-icon icon="solar:check-circle-bold" width="18"></iconify-icon>
                            <span class="hidden sm:inline">{{ __('Publier') }}</span>
                        </button>
                    @else
                        <button type="button" wire:click="unpublishForm"
                                @disabled(! $canManageForms)
                                class="h-10 px-4 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-600 hover:bg-slate-50 transition disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center gap-2 shadow-sm">
                            <iconify-icon icon="solar:pause-circle-linear" width="18"></iconify-icon>
                            <span class="hidden sm:inline">{{ __('Brouillon') }}</span>
                        </button>
                    @endif
                    <button type="button" wire:click="duplicateSelectedForm"
                            @disabled(! $canManageForms)
                            class="h-10 px-4 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center gap-2">
                        <iconify-icon icon="solar:copy-linear" width="18"></iconify-icon>
                        <span class="hidden sm:inline">{{ __('Dupliquer') }}</span>
                    </button>
                @endif
                <button type="button" wire:click="saveSelectedForm"
                        @disabled(! $canManageForms || ! $fb_selected_form_id)
                        class="h-10 px-5 rounded-xl text-white text-sm font-bold shadow-md shadow-[var(--accent)]/20 inline-flex items-center gap-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed hover:opacity-95"
                        style="background: linear-gradient(135deg, var(--accent) 0%, color-mix(in srgb, var(--accent) 85%, black) 100%);">
                    <iconify-icon icon="solar:diskette-bold" width="18"></iconify-icon>
                    <span class="hidden sm:inline">{{ __('Sauvegarder') }}</span>
                    <span class="sm:hidden">{{ __('Save') }}</span>
                </button>
            </div>
        </div>

        {{-- Erreurs de sauvegarde du formulaire (visibles même sans ouvrir l'onglet Propriétés) --}}
        @php
            $formSaveErrorKeys = ['fb_selected_form_name', 'fb_selected_form_category_id', 'fb_selected_form_target_user_id', 'fb_selected_form_slug', 'fb_selected_form_public_title', 'fb_selected_form_public_description', 'fb_selected_form_public_thank_you', 'fb_selected_form_description'];
            $viewErrors = isset($errors) ? $errors : new \Illuminate\Support\ViewErrorBag();
            $formSaveErrors = collect($formSaveErrorKeys)->flatMap(fn ($key) => $viewErrors->get($key))->filter()->values();
        @endphp
        @if($formSaveErrors->isNotEmpty())
            <div class="mx-4 sm:mx-6 lg:mx-8 mb-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="shrink-0 flex h-9 w-9 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                        <iconify-icon icon="solar:danger-triangle-bold" width="20"></iconify-icon>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-amber-800">{{ __('Impossible d\'enregistrer le formulaire') }}</p>
                        <ul class="mt-1 list-inside list-disc text-xs text-amber-700 space-y-0.5">
                            @foreach($formSaveErrors as $msg)
                                <li>{{ $msg }}</li>
                            @endforeach
                        </ul>
                        <p class="mt-2 text-xs text-amber-600">
                            {{ __('Ouvrez l\'onglet') }} <strong>{{ __('Propriétés') }}</strong> {{ __('(panneau droit) pour corriger le nom, la catégorie ou le slug public.') }}
                        </p>
                    </div>
                    <button type="button" @click="sidebarTab = 'properties'; mobileSidebarOpen = true" class="shrink-0 rounded-lg border border-amber-300 bg-white px-3 py-1.5 text-xs font-semibold text-amber-800 hover:bg-amber-100 transition-colors">
                        {{ __('Ouvrir Propriétés') }}
                    </button>
                </div>
            </div>
        @endif

        <!-- TABS -->
        @if($fb_selected_form_id)
        <nav class="flex gap-1 px-4 sm:px-6 lg:px-8 pb-0" aria-label="{{ __('Onglets') }}">
            <button type="button" wire:click="$set('activeTab', 'champs')"
                    class="px-4 py-3.5 text-sm font-semibold rounded-t-xl transition-colors {{ $activeTab === 'champs' ? 'bg-white text-[var(--accent)] shadow-sm border border-b-0 border-slate-200 -mb-px' : 'text-slate-500 hover:text-slate-700 hover:bg-white/50' }}">
                {{ __('Champs') }}
            </button>
            <button type="button" wire:click="$set('activeTab', 'assignations')"
                    class="px-4 py-3.5 text-sm font-semibold rounded-t-xl transition-colors flex items-center gap-1.5 {{ $activeTab === 'assignations' ? 'bg-white text-[var(--accent)] shadow-sm border border-b-0 border-slate-200 -mb-px' : 'text-slate-500 hover:text-slate-700 hover:bg-white/50' }}">
                {{ __('Assignations') }}
                @if($assignments->where('status.value', 'pending')->count() > 0)
                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">{{ $assignments->where('status.value', 'pending')->count() }}</span>
                @endif
            </button>
            <button type="button" wire:click="$set('activeTab', 'reponses')"
                    class="px-4 py-3.5 text-sm font-semibold rounded-t-xl transition-colors {{ $activeTab === 'reponses' ? 'bg-white text-[var(--accent)] shadow-sm border border-b-0 border-slate-200 -mb-px' : 'text-slate-500 hover:text-slate-700 hover:bg-white/50' }}">
                {{ __('Réponses') }}
            </button>
        </nav>
        @endif
    </header>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex overflow-hidden bg-slate-50/60">
        @if($activeTab === 'champs')
            <!-- CANVAS (CENTER) -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 flex justify-center relative">
                <div class="absolute inset-0 pointer-events-none opacity-[0.04]"
                     style="background-image: radial-gradient(circle at 1px 1px, rgb(148 163 184 / 0.4) 1px, transparent 0); background-size: 24px 24px;">
                </div>
                <div class="w-full max-w-3xl flex flex-col h-full relative z-0">
                    @include('livewire.admin.form-builder-canvas')
                </div>
            </div>

            <!-- SIDEBAR (RIGHT) -->
            <aside class="w-80 bg-white border-l border-slate-200/80 flex-col shrink-0 z-10 hidden lg:flex shadow-sm">
                <div class="flex gap-1 p-2 border-b border-slate-100 bg-slate-50/50">
                    <button type="button"
                            class="flex-1 py-2.5 px-3 text-sm font-semibold rounded-lg transition-colors"
                            :class="sidebarTab === 'elements' ? 'bg-white text-[var(--accent)] shadow-sm border border-slate-200' : 'text-slate-500 hover:text-slate-700 hover:bg-white/70'"
                            @click="sidebarTab = 'elements'; $wire.set('fb_selected_field_id', null)">
                        {{ __('Éléments') }}
                    </button>
                    <button type="button"
                            class="flex-1 py-2.5 px-3 text-sm font-semibold rounded-lg transition-colors"
                            :class="sidebarTab === 'properties' ? 'bg-white text-[var(--accent)] shadow-sm border border-slate-200' : 'text-slate-500 hover:text-slate-700 hover:bg-white/70'"
                            @click="sidebarTab = 'properties'">
                        {{ __('Propriétés') }}
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto custom-scrollbar p-5">
                    <div x-show="sidebarTab === 'elements'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        @include('livewire.admin.form-builder-palette')
                    </div>
                    <div x-show="sidebarTab === 'properties'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        @include('livewire.admin.form-builder-properties')
                    </div>
                </div>
            </aside>
        @elseif($activeTab === 'assignations')
            <!-- ASSIGNMENTS TAB -->
            <div class="flex-1 overflow-y-auto bg-slate-50/50 p-4 sm:p-8">
                <div class="max-w-3xl mx-auto space-y-6">
                    <!-- Assign form -->
                    @if($canManageForms)
                    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                        <h3 class="text-sm font-bold text-slate-900 mb-4">{{ __('Nouvelle assignation') }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="text-[11px] font-medium text-slate-700 mb-1 block">{{ __('Utilisateur') }}</label>
                                <select wire:model="assign_user_id" class="w-full rounded-lg border-slate-200 text-xs py-2 focus:ring-[var(--accent)] focus:border-[var(--accent)]">
                                    <option value="">{{ __('— Choisir —') }}</option>
                                    @foreach($members as $m)
                                        <option value="{{ $m->user_id }}">{{ $m->user?->name ?? '—' }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('assign_user_id')" />
                            </div>
                            <div>
                                <label class="text-[11px] font-medium text-slate-700 mb-1 block">{{ __('Ou fonction') }}</label>
                                <select wire:model="assign_function_id" class="w-full rounded-lg border-slate-200 text-xs py-2 focus:ring-[var(--accent)] focus:border-[var(--accent)]">
                                    <option value="">{{ __('— Choisir —') }}</option>
                                    @foreach($organizationFunctions as $fn)
                                        <option value="{{ $fn->id }}">{{ $fn->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('assign_function_id')" />
                            </div>
                            <div>
                                <label class="text-[11px] font-medium text-slate-700 mb-1 block">{{ __('Date limite') }}</label>
                                <input type="date" wire:model="assign_due_date" class="w-full rounded-lg border-slate-200 text-xs py-2 focus:ring-[var(--accent)] focus:border-[var(--accent)]">
                            </div>
                        </div>
                        <button type="button" wire:click="assignForm"
                                class="mt-4 h-9 px-4 text-white text-xs font-bold rounded-lg shadow-sm inline-flex items-center gap-2 bg-[var(--accent)] hover:opacity-90 transition-all">
                            <iconify-icon icon="solar:user-plus-bold" width="16"></iconify-icon>
                            {{ __('Assigner') }}
                        </button>
                    </div>
                    @endif

                    <!-- Assignments list -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100">
                            <h3 class="text-sm font-bold text-slate-900">{{ __('Assignations') }} ({{ $assignments->count() }})</h3>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @forelse($assignments as $a)
                                @php
                                    $aStatus = $a->status instanceof \App\Enums\FormAssignmentStatus ? $a->status->value : (string) $a->status;
                                    $aBadge = match($aStatus) {
                                        'submitted' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'label' => 'Soumis'],
                                        'overdue' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'label' => 'En retard'],
                                        default => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'label' => 'En attente'],
                                    };
                                @endphp
                                <div class="px-6 py-3 flex items-center justify-between">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-slate-900">
                                            {{ $a->user?->name ?? $a->organizationFunction?->name ?? '—' }}
                                        </p>
                                        <p class="text-xs text-slate-500">
                                            {{ __('Par') }} {{ $a->assignedBy?->name ?? '—' }}
                                            @if($a->due_date) · {{ __('Échéance') }}: {{ $a->due_date->format('d/m/Y') }} @endif
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-medium {{ $aBadge['bg'] }} {{ $aBadge['text'] }}">{{ $aBadge['label'] }}</span>
                                        @if($canManageForms && $aStatus === 'pending')
                                            <button type="button" wire:click="deleteAssignment({{ $a->id }})" wire:confirm="Supprimer cette assignation ?"
                                                    class="p-1 text-slate-400 hover:text-red-600 transition-colors">
                                                <iconify-icon icon="solar:trash-bin-trash-linear" width="14"></iconify-icon>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="px-6 py-8 text-center text-sm text-slate-500">
                                    {{ __('Aucune assignation pour ce formulaire.') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @elseif($activeTab === 'reponses')
            <!-- RESPONSES TAB -->
            <div class="flex-1 overflow-y-auto bg-slate-50/50 p-4 sm:p-8">
                <div class="max-w-3xl mx-auto">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8 text-center">
                        <iconify-icon icon="solar:chart-2-bold-duotone" width="48" class="text-slate-300 mb-4"></iconify-icon>
                        <h3 class="text-sm font-bold text-slate-900 mb-1">{{ __('Réponses') }}</h3>
                        <p class="text-xs text-slate-500 mb-4">{{ __('Consultez les réponses détaillées sur la page dédiée.') }}</p>
                        @if($fb_selected_form_id)
                            <a href="{{ route('admin.forms.responses', $fb_selected_form_id) }}"
                               class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white rounded-lg bg-[var(--accent)] hover:opacity-90 transition-all">
                                <iconify-icon icon="solar:eye-bold" width="16"></iconify-icon>
                                {{ __('Voir les réponses') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- MOBILE SIDEBAR DRAWER -->
    <div class="lg:hidden fixed inset-0 z-40" x-show="mobileSidebarOpen" x-cloak style="display:none;">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="closeMobile()"></div>
        <div class="absolute right-0 top-0 bottom-0 w-full max-w-[22rem] bg-white shadow-2xl border-l border-slate-200 flex flex-col">
            <div class="h-16 px-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <button type="button" class="px-3 py-1.5 rounded-lg text-xs font-bold" :class="sidebarTab === 'elements' ? 'bg-[var(--accent-soft)] text-[var(--accent)]' : 'text-slate-600 hover:bg-slate-50'" @click="sidebarTab='elements'; $wire.set('fb_selected_field_id', null)">
                        {{ __('Éléments') }}
                    </button>
                    <button type="button" class="px-3 py-1.5 rounded-lg text-xs font-bold" :class="sidebarTab === 'properties' ? 'bg-[var(--accent-soft)] text-[var(--accent)]' : 'text-slate-600 hover:bg-slate-50'" @click="sidebarTab='properties'">
                        {{ __('Propriétés') }}
                    </button>
                </div>
                <button type="button" @click="closeMobile()" class="h-9 w-9 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition flex items-center justify-center" aria-label="{{ __('Fermer') }}">
                    <iconify-icon icon="solar:close-circle-linear" width="18"></iconify-icon>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto custom-scrollbar p-4">
                <div x-show="sidebarTab === 'elements'">
                    @include('livewire.admin.form-builder-palette')
                </div>
                <div x-show="sidebarTab === 'properties'">
                    @include('livewire.admin.form-builder-properties')
                </div>
            </div>
        </div>
    </div>
</div>
