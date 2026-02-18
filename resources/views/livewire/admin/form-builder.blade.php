<div
    class="flex flex-col min-h-[32rem] rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden"
    x-data="{
        sidebarTab: 'elements',
        mobileSidebarOpen: false,
        openMobile(tab) { this.sidebarTab = tab; this.mobileSidebarOpen = true },
        closeMobile() { this.mobileSidebarOpen = false },
    }"
    x-on:field-selected.window="sidebarTab = 'properties'"
>
    <!-- HEADER -->
    <div class="h-16 border-b border-slate-200 bg-white px-4 sm:px-6 flex items-center justify-between shrink-0 z-20">
        <div class="flex items-center gap-3 sm:gap-4 min-w-0">
            <a href="{{ route('admin.settings') }}" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors">
                <iconify-icon icon="solar:arrow-left-linear" width="20"></iconify-icon>
            </a>
            <div class="lg:hidden flex items-center gap-2">
                <button type="button" @click="openMobile('elements')" class="h-9 w-9 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition flex items-center justify-center" title="{{ __('Éléments') }}">
                    <iconify-icon icon="solar:widget-2-linear" width="18"></iconify-icon>
                </button>
                <button type="button" @click="openMobile('properties')" class="h-9 w-9 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition flex items-center justify-center" title="{{ __('Propriétés') }}">
                    <iconify-icon icon="solar:slider-minimalistic-horizontal-linear" width="18"></iconify-icon>
                </button>
            </div>
            <div>
                <h1 class="text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    {{ $fb_selected_template_name ?: __('Nouveau formulaire') }}
                    @if($fb_selected_template_id)
                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-[10px] font-medium text-slate-500">#{{ $fb_selected_template_id }}</span>
                    @endif
                </h1>
                <p class="text-xs text-slate-500">{{ __('Éditeur de formulaire') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3 shrink-0">
            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200">
                <span class="h-2 w-2 rounded-full {{ $canManageForms ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                <span class="text-xs font-medium text-slate-600">{{ $canManageForms ? __('Mode Édition') : __('Lecture seule') }}</span>
            </div>
            @if($fb_selected_template_id)
                <button type="button" wire:click="duplicateSelectedTemplate"
                        @disabled(! $canManageForms)
                        class="h-9 px-3 sm:px-4 rounded-lg border border-slate-200 bg-white text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center gap-2">
                    <iconify-icon icon="solar:copy-linear" width="16"></iconify-icon>
                    <span class="hidden sm:inline">{{ __('Dupliquer') }}</span>
                </button>
            @endif
            <button type="button" wire:click="saveSelectedTemplate"
                    @disabled(! $canManageForms || ! $fb_selected_template_id)
                    class="h-9 px-4 text-white text-xs font-bold rounded-lg shadow-sm inline-flex items-center gap-2 bg-[var(--accent)] hover:opacity-90 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                <iconify-icon icon="solar:diskette-bold" width="16"></iconify-icon>
                <span class="hidden sm:inline">{{ __('Sauvegarder') }}</span>
                <span class="sm:hidden">{{ __('Save') }}</span>
            </button>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex overflow-hidden">

        <!-- CANVAS (CENTER) -->
        <div class="flex-1 overflow-y-auto bg-slate-50/50 p-4 sm:p-8 flex justify-center relative">
            <div class="absolute inset-0 pointer-events-none opacity-[0.03]"
                 style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 20px 20px;">
            </div>

            <div class="w-full max-w-3xl flex flex-col h-full">
                @include('livewire.admin.form-builder-canvas')
            </div>
        </div>

        <!-- SIDEBAR (RIGHT) -->
        <div class="w-80 bg-white border-l border-slate-200 flex-col shrink-0 z-10 shadow-sm hidden lg:flex">
            <!-- Sidebar Tabs -->
            <div class="flex border-b border-slate-100">
                <button
                    type="button"
                    class="flex-1 py-3 text-xs font-bold uppercase tracking-wider border-b-2 transition-colors"
                    :class="sidebarTab === 'elements' ? 'border-[var(--accent)] text-[var(--accent)]' : 'border-transparent text-slate-400 hover:text-slate-600'"
                    @click="sidebarTab = 'elements'; $wire.set('fb_selected_field_id', null)"
                >
                    {{ __('Éléments') }}
                </button>
                <button
                    type="button"
                    class="flex-1 py-3 text-xs font-bold uppercase tracking-wider border-b-2 transition-colors"
                    :class="sidebarTab === 'properties' ? 'border-[var(--accent)] text-[var(--accent)]' : 'border-transparent text-slate-400 hover:text-slate-600'"
                    @click="sidebarTab = 'properties'"
                >
                    {{ __('Propriétés') }}
                </button>
            </div>

            <!-- Sidebar Content -->
            <div class="flex-1 overflow-y-auto custom-scrollbar p-5">
                <div x-show="sidebarTab === 'elements'">
                    @include('livewire.admin.form-builder-palette')
                </div>
                <div x-show="sidebarTab === 'properties'">
                    @include('livewire.admin.form-builder-properties')
                </div>
            </div>
        </div>
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
