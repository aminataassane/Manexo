<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-xl font-bold text-slate-800">{{ __('super_admin.monitoring.title') }}</h2>
        <p class="mt-1 text-sm text-slate-500">{{ __('super_admin.monitoring.subtitle') }}</p>
    </div>

    {{-- Session flash --}}
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <!-- System Info -->
    <div class="rounded-2xl border border-slate-200/60 bg-white p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">{{ __('super_admin.monitoring.system_info') }}</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach ([
                'php_version' => $this->systemInfo['php_version'],
                'laravel_version' => $this->systemInfo['laravel_version'],
                'db_version' => \Illuminate\Support\Str::limit($this->systemInfo['db_version'], 30),
                'cache_driver' => $this->systemInfo['cache_driver'],
                'queue_driver' => $this->systemInfo['queue_driver'],
                'session_driver' => $this->systemInfo['session_driver'],
            ] as $label => $value)
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('super_admin.monitoring.' . $label) }}</p>
                    <p class="text-sm font-medium text-slate-700 mt-0.5">{{ $value }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-slate-200/60 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50">
                    <iconify-icon icon="solar:clock-circle-bold-duotone" width="20" class="text-blue-600"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('super_admin.monitoring.pending_jobs') }}</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $this->jobCounts['pending'] }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200/60 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50">
                    <iconify-icon icon="solar:danger-triangle-bold-duotone" width="20" class="text-red-600"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('super_admin.monitoring.failed_jobs') }}</p>
                    <p class="text-2xl font-bold text-red-600">{{ $this->jobCounts['failed'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Failed Jobs Table -->
    <div class="rounded-2xl border border-slate-200/60 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.monitoring.col_id') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.monitoring.col_queue') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.monitoring.col_exception') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.monitoring.col_failed_at') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.monitoring.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($failedJobs as $job)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5 text-xs font-mono text-slate-500">{{ \Illuminate\Support\Str::limit($job->uuid, 12) }}</td>
                            <td class="px-5 py-3.5 text-sm text-slate-600">{{ $job->queue }}</td>
                            <td class="px-5 py-3.5 text-sm text-slate-500">
                                <div x-data="{ open: false }">
                                    <button @click="open = !open" class="text-left text-xs text-red-600 hover:text-red-800 font-medium">
                                        {{ \Illuminate\Support\Str::limit($job->exception, 80) }}
                                    </button>
                                    <div x-show="open" x-cloak x-transition class="mt-2 rounded-lg bg-red-50 p-3 text-xs font-mono text-red-700 max-h-40 overflow-auto">
                                        {{ $job->exception }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-500 whitespace-nowrap">{{ $job->failed_at }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="retryJob('{{ $job->uuid }}')" class="sa-btn-secondary text-xs py-1.5 px-2.5">
                                        <iconify-icon icon="solar:refresh-bold" width="14"></iconify-icon>
                                        {{ __('super_admin.monitoring.retry') }}
                                    </button>
                                    <button wire:click="deleteFailedJob('{{ $job->uuid }}')" wire:confirm="Are you sure?" class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">
                                        <iconify-icon icon="solar:trash-bin-trash-bold" width="14"></iconify-icon>
                                        {{ __('super_admin.monitoring.delete') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-sm text-slate-400">
                                <iconify-icon icon="solar:check-circle-bold-duotone" width="32" class="text-emerald-300 mb-2"></iconify-icon>
                                <p>{{ __('super_admin.monitoring.empty') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($failedJobs->hasPages())
            <div class="border-t border-slate-100 px-5 py-3">
                {{ $failedJobs->links() }}
            </div>
        @endif
    </div>
</div>
