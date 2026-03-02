<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-xl font-bold text-slate-800">{{ __('super_admin.dashboard.title') }}</h2>
        <p class="mt-1 text-sm text-slate-500">{{ __('super_admin.dashboard.subtitle') }}</p>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        {{-- Total Organizations --}}
        <div class="rounded-2xl border border-slate-200/60 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50">
                    <iconify-icon icon="solar:buildings-bold-duotone" width="20" class="text-indigo-600"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('super_admin.dashboard.total_orgs') }}</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $this->stats['total_orgs'] }}</p>
                </div>
            </div>
        </div>

        {{-- Active --}}
        <div class="rounded-2xl border border-slate-200/60 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50">
                    <iconify-icon icon="solar:check-circle-bold-duotone" width="20" class="text-emerald-600"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('super_admin.dashboard.active_orgs') }}</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ $this->stats['active_orgs'] }}</p>
                </div>
            </div>
        </div>

        {{-- Suspended --}}
        <div class="rounded-2xl border border-slate-200/60 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50">
                    <iconify-icon icon="solar:pause-circle-bold-duotone" width="20" class="text-amber-600"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('super_admin.dashboard.suspended_orgs') }}</p>
                    <p class="text-2xl font-bold text-amber-600">{{ $this->stats['suspended_orgs'] }}</p>
                </div>
            </div>
        </div>

        {{-- Disabled --}}
        <div class="rounded-2xl border border-slate-200/60 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50">
                    <iconify-icon icon="solar:close-circle-bold-duotone" width="20" class="text-red-600"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('super_admin.dashboard.disabled_orgs') }}</p>
                    <p class="text-2xl font-bold text-red-600">{{ $this->stats['disabled_orgs'] }}</p>
                </div>
            </div>
        </div>

        {{-- Total Users --}}
        <div class="rounded-2xl border border-slate-200/60 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50">
                    <iconify-icon icon="solar:users-group-rounded-bold-duotone" width="20" class="text-blue-600"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('super_admin.dashboard.total_users') }}</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $this->stats['total_users'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <a href="{{ route('platform-admin.organizations') }}" class="group rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-50 group-hover:bg-indigo-100 transition-colors">
                    <iconify-icon icon="solar:buildings-bold-duotone" width="24" class="text-indigo-600"></iconify-icon>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ __('super_admin.dashboard.manage_orgs') }}</h3>
                    <p class="text-sm text-slate-500">{{ __('super_admin.dashboard.manage_orgs_desc') }}</p>
                </div>
            </div>
        </a>

        <a href="{{ route('platform-admin.audit-log') }}" class="group rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-50 group-hover:bg-indigo-100 transition-colors">
                    <iconify-icon icon="solar:document-text-bold-duotone" width="24" class="text-indigo-600"></iconify-icon>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ __('super_admin.dashboard.view_audit') }}</h3>
                    <p class="text-sm text-slate-500">{{ __('super_admin.dashboard.view_audit_desc') }}</p>
                </div>
            </div>
        </a>
    </div>
</div>
