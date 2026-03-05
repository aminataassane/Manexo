<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-xl font-bold text-slate-800">{{ __('super_admin.files.title') }}</h2>
        <p class="mt-1 text-sm text-slate-500">{{ __('super_admin.files.subtitle') }}</p>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-slate-200/60 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50">
                    <iconify-icon icon="solar:folder-with-files-bold-duotone" width="20" class="text-indigo-600"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('super_admin.files.total_files') }}</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $this->totalStorage['total_files'] }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200/60 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50">
                    <iconify-icon icon="solar:server-bold-duotone" width="20" class="text-emerald-600"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('super_admin.files.total_size') }}</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ $this->totalStorage['total_size_human'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Storage by Org -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm min-w-0">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-sm font-semibold text-slate-800">{{ __('super_admin.files.storage_by_org') }}</h3>
        </div>
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full divide-y divide-slate-200">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.files.col_org') }}</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.files.col_files') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.files.col_size') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($this->storageByOrg as $org)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5 text-sm font-medium text-slate-700">{{ $org['org_name'] }}</td>
                            <td class="px-5 py-3.5 text-sm text-slate-600 text-center">{{ $org['files_count'] }}</td>
                            <td class="px-5 py-3.5 text-sm text-slate-600 text-right">{{ $org['size_human'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-8 text-center text-sm text-slate-400">
                                {{ __('super_admin.files.empty_orgs') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Files -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm min-w-0">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-sm font-semibold text-slate-800">{{ __('super_admin.files.recent_files') }}</h3>
        </div>
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full divide-y divide-slate-200">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.files.col_filename') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.files.col_size') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.files.col_date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($this->recentFiles as $file)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5 text-sm text-slate-700 font-medium">{{ $file['name'] }}</td>
                            <td class="px-5 py-3.5 text-sm text-slate-500 text-right">{{ $file['size_human'] }}</td>
                            <td class="px-5 py-3.5 text-sm text-slate-500">{{ $file['modified'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-8 text-center text-sm text-slate-400">
                                {{ __('super_admin.files.empty_files') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
