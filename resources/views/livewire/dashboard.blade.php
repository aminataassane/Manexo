{{-- Dashboard : KPI cards chargés en premier, panneaux secondaires en lazy --}}
@php
    $user = Auth::user();
    $org = $this->organization;
    $orgName = $org?->name ?? '—';
@endphp

<div class="space-y-4 sm:space-y-6">
    <!-- WELCOME SECTION (pas de requête lourde) -->
    <div class="relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 shadow-sm border border-slate-100 sm:p-6 lg:p-8 xl:p-10 min-[1920px]:p-12">
        <div class="relative z-10 flex flex-col items-start justify-between gap-4 sm:gap-6 sm:flex-row sm:items-center">
            <div class="min-w-0 w-full sm:w-auto">
                <h1 class="text-xl font-bold text-slate-900 sm:text-2xl lg:text-3xl min-[1920px]:text-4xl tracking-tight break-words">
                    {{ __('pages.dashboard.hello') }}, <span class="text-transparent bg-clip-text bg-gradient-to-r from-[var(--accent)] to-[var(--accent-dark)]">{{ $user->name }}</span> 👋
                </h1>
                <p class="mt-1 sm:mt-2 text-sm sm:text-base text-slate-500 max-w-2xl">
                    {{ __('pages.dashboard.intro', ['org' => $orgName]) }}
                </p>
            </div>
            <div class="flex flex-col gap-2 w-full sm:w-auto sm:flex-row sm:flex-shrink-0">
                <a href="{{ route('tickets.index') }}" wire:navigate class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-3 sm:py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-200 hover:bg-slate-50 transition-all touch-target sm:min-h-0 sm:min-w-0">
                    <iconify-icon icon="solar:list-bold" width="18"></iconify-icon>
                    {{ __('pages.dashboard.view_tickets') }}
                </a>
                <a href="{{ route('tickets.create') }}" wire:navigate class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-3 sm:py-2.5 text-sm font-semibold text-white shadow-lg shadow-[var(--accent-ring)] hover:opacity-90 transition-all transform hover:-translate-y-0.5 touch-target sm:min-h-0 sm:min-w-0" style="background-color: var(--accent);">
                    <iconify-icon icon="solar:add-circle-bold" width="18"></iconify-icon>
                    {{ __('pages.dashboard.new_ticket') }}
                </a>
            </div>
        </div>
        <div class="absolute top-0 right-0 -mt-20 -mr-20 h-64 w-64 rounded-full bg-[var(--accent-soft)] opacity-20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-20 -ml-20 h-64 w-64 rounded-full bg-blue-50 opacity-50 blur-3xl"></div>
    </div>

    @if ($this->isAdminView)
        <livewire:dashboard.admin-kpi-cards lazy />

        <div class="grid grid-cols-1 gap-4 sm:gap-6 lg:grid-cols-3 min-[1920px]:gap-8">
            <div class="space-y-4 sm:space-y-6 lg:col-span-2 min-w-0">
                <livewire:dashboard.activity-chart lazy />
                <livewire:dashboard.priority-tickets-table lazy />
            </div>

            <div class="space-y-4 sm:space-y-6 min-w-0">
                <livewire:dashboard.discussions-panel lazy />
                <livewire:dashboard.pending-forms-panel lazy />
                <livewire:dashboard.recent-activity-panel lazy />
            </div>
        </div>
    @else
        <livewire:dashboard.member-kpi-cards lazy />

        <div class="grid grid-cols-1 gap-4 sm:gap-6 lg:grid-cols-3">
            <div class="space-y-4 sm:space-y-6 lg:col-span-2 min-w-0">
                <livewire:dashboard.my-recent-tickets-table lazy />
            </div>

            <div class="space-y-4 sm:space-y-6 min-w-0">
                <livewire:dashboard.pending-forms-panel lazy />
                <livewire:dashboard.discussions-panel lazy />
                <livewire:dashboard.recent-activity-panel lazy />
            </div>
        </div>
    @endif
</div>
