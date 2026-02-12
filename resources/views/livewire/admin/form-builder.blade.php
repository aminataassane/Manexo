<div class="max-w-full mx-auto py-6 px-4">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-semibold text-[#111827] tracking-tight">{{ __('Éditeur de Formulaire') }}</h1>
            <p class="mt-1 text-sm text-[#6B7280]">{{ __("Crée des champs par entreprise + catégorie, et optionnellement réservé à une personne.") }}</p>
        </div>
        <a href="{{ route('admin.settings') }}"
           class="h-10 px-4 bg-white border border-[#E5E7EB] text-[#111827] text-[13px] font-medium rounded-md shadow-sm hover:bg-[#F9FAFB] transition flex items-center gap-2">
            <iconify-icon icon="solar:arrow-left-linear" width="16"></iconify-icon>
            {{ __('Retour Paramètres') }}
        </a>
    </div>

    <div class="rounded-xl border border-[#E5E7EB] bg-white shadow-sm overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-[#E5E7EB] flex items-start justify-between gap-4">
            <div>
                <span class="text-[12px] text-[#6B7280] flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full {{ $canManageForms ? 'bg-emerald-400' : 'bg-slate-300' }}"></span>
                    {{ $canManageForms ? __('Modifiable') : __('Lecture seule') }}
                </span>
            </div>
            <button type="button" wire:click="saveSelectedTemplate"
                    @disabled(! $canManageForms || ! $fb_selected_template_id)
                    class="h-9 px-3 text-white text-[12px] font-semibold rounded-lg shadow-sm inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed">
                <iconify-icon icon="solar:diskette-linear" width="16"></iconify-icon>
                {{ __('Sauvegarder') }}
            </button>
        </div>

        <div class="h-[700px] bg-white flex overflow-hidden">
            @include('livewire.admin.form-builder-palette')
            @include('livewire.admin.form-builder-canvas')
            @include('livewire.admin.form-builder-properties')
        </div>
    </div>
</div>
