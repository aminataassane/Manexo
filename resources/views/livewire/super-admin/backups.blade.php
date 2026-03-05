<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">{{ __('super_admin.backups.title') }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ __('super_admin.backups.subtitle') }}</p>
        </div>
        <button wire:click="createBackup" wire:loading.attr="disabled" class="sa-btn-primary">
            <iconify-icon icon="solar:database-bold-duotone" width="16" wire:loading.remove wire:target="createBackup"></iconify-icon>
            <iconify-icon icon="solar:refresh-bold" width="16" class="animate-spin" wire:loading wire:target="createBackup"></iconify-icon>
            {{ __('super_admin.backups.create') }}
        </button>
    </div>

    {{-- Session flash --}}
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <!-- Table -->
    <div class="rounded-2xl border border-slate-200/60 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.backups.col_filename') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.backups.col_size') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.backups.col_date') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.backups.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($this->backups as $backup)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5 text-sm font-medium text-slate-700">
                                <div class="flex items-center gap-2">
                                    <iconify-icon icon="solar:database-bold-duotone" width="16" class="text-slate-400"></iconify-icon>
                                    {{ $backup['name'] }}
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-500 text-right">{{ $backup['size_human'] }}</td>
                            <td class="px-5 py-3.5 text-sm text-slate-500">{{ $backup['created_at'] }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <button wire:click="downloadBackup('{{ $backup['name'] }}')" class="sa-btn-secondary text-xs py-1.5 px-2.5">
                                    <iconify-icon icon="solar:download-minimalistic-bold" width="14"></iconify-icon>
                                    {{ __('super_admin.backups.download') }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center text-sm text-slate-400">
                                <iconify-icon icon="solar:database-bold-duotone" width="32" class="text-slate-300 mb-2"></iconify-icon>
                                <p>{{ __('super_admin.backups.empty') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
