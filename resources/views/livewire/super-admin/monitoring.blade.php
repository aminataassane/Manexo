<div class="space-y-6 pb-12">
    {{-- Header --}}
    <header>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ __('super_admin.monitoring.title') }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ __('super_admin.monitoring.subtitle') }}</p>
    </header>

    {{-- Session flash --}}
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-center gap-2">
            <iconify-icon icon="solar:check-circle-bold" width="18"></iconify-icon>
            {{ session('success') }}
        </div>
    @endif

    {{-- System info --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-sm font-semibold text-slate-800">{{ __('super_admin.monitoring.system_info') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <tbody>
                    @foreach ([
                        'php_version' => $this->systemInfo['php_version'],
                        'laravel_version' => $this->systemInfo['laravel_version'],
                        'db_version' => \Illuminate\Support\Str::limit($this->systemInfo['db_version'], 40),
                        'cache_driver' => $this->systemInfo['cache_driver'],
                        'queue_driver' => $this->systemInfo['queue_driver'],
                        'session_driver' => $this->systemInfo['session_driver'],
                    ] as $label => $value)
                        <tr class="border-b border-slate-100 last:border-b-0">
                            <td class="px-5 py-3 text-xs font-medium text-slate-400 w-[180px]">{{ __('super_admin.monitoring.' . $label) }}</td>
                            <td class="px-5 py-3 text-sm font-medium text-slate-700 font-mono">{{ $value }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Job KPI cards --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <iconify-icon icon="solar:clock-circle-bold-duotone" width="20"></iconify-icon>
                </div>
                <div>
                    <p class="text-[11px] font-medium text-slate-500">{{ __('super_admin.monitoring.pending_jobs') }}</p>
                    <p class="text-2xl font-bold text-slate-900 tabular-nums">{{ $this->jobCounts['pending'] }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600">
                    <iconify-icon icon="solar:danger-triangle-bold-duotone" width="20"></iconify-icon>
                </div>
                <div>
                    <p class="text-[11px] font-medium text-slate-500">{{ __('super_admin.monitoring.failed_jobs') }}</p>
                    <p class="text-2xl font-bold text-red-600 tabular-nums">{{ $this->jobCounts['failed'] }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Failed jobs — tableau + detail expandable --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden" x-data="{ expanded: null }">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-sm font-semibold text-slate-800">{{ __('super_admin.monitoring.failed_jobs') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/40">
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500 w-[120px]">{{ __('super_admin.monitoring.col_id') }}</th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.monitoring.col_queue') }}</th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.monitoring.col_exception') }}</th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500 w-[140px]">{{ __('super_admin.monitoring.col_failed_at') }}</th>
                        <th class="px-5 py-3 w-[40px]"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($failedJobs as $job)
                        <tr
                            class="border-b border-slate-100 transition-colors cursor-pointer"
                            :class="expanded === '{{ $job->uuid }}' ? 'bg-slate-50' : 'hover:bg-slate-50/50'"
                            @click="expanded = expanded === '{{ $job->uuid }}' ? null : '{{ $job->uuid }}'"
                        >
                            <td class="px-5 py-3 text-xs font-mono text-slate-500">{{ \Illuminate\Support\Str::limit($job->uuid, 12) }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center rounded-md bg-slate-100 border border-slate-200 px-2 py-0.5 text-[11px] font-medium text-slate-600">{{ $job->queue }}</span>
                            </td>
                            <td class="px-5 py-3 text-xs text-red-600 font-medium truncate max-w-[300px]">{{ \Illuminate\Support\Str::limit($job->exception, 70) }}</td>
                            <td class="px-5 py-3">
                                <div class="text-xs text-slate-600">{{ \Illuminate\Support\Carbon::parse($job->failed_at)->format('d/m/Y') }}</div>
                                <div class="text-[11px] text-slate-400">{{ \Illuminate\Support\Carbon::parse($job->failed_at)->format('H:i') }}</div>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <div
                                    class="inline-flex items-center justify-center h-6 w-6 rounded-md transition-colors"
                                    :class="expanded === '{{ $job->uuid }}' ? 'bg-[#005F02]/10 text-[#005F02]' : 'text-slate-300'"
                                >
                                    <iconify-icon
                                        :icon="expanded === '{{ $job->uuid }}' ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear'"
                                        width="14"
                                    ></iconify-icon>
                                </div>
                            </td>
                        </tr>

                        {{-- Detail panel --}}
                        <tr x-show="expanded === '{{ $job->uuid }}'" x-cloak>
                            <td colspan="5" class="px-0 py-0">
                                <div
                                    x-show="expanded === '{{ $job->uuid }}'"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="border-b border-slate-100 bg-slate-50/50 px-5 py-4"
                                >
                                    <div class="flex flex-col lg:flex-row gap-4">
                                        {{-- Exception detail --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="rounded-xl bg-white border border-slate-200 divide-y divide-slate-100 overflow-hidden">
                                                <div class="flex items-baseline gap-4 px-4 py-2.5 text-xs">
                                                    <span class="shrink-0 w-[80px] font-medium text-slate-400">UUID</span>
                                                    <span class="text-slate-700 font-mono text-[11px]">{{ $job->uuid }}</span>
                                                </div>
                                                <div class="flex items-baseline gap-4 px-4 py-2.5 text-xs">
                                                    <span class="shrink-0 w-[80px] font-medium text-slate-400">{{ __('super_admin.monitoring.col_queue') }}</span>
                                                    <span class="text-slate-700">{{ $job->queue }}</span>
                                                </div>
                                                <div class="flex items-baseline gap-4 px-4 py-2.5 text-xs">
                                                    <span class="shrink-0 w-[80px] font-medium text-slate-400">{{ __('super_admin.monitoring.col_failed_at') }}</span>
                                                    <span class="text-slate-700">{{ $job->failed_at }} &middot; {{ \Illuminate\Support\Carbon::parse($job->failed_at)->diffForHumans() }}</span>
                                                </div>
                                            </div>

                                            {{-- Full exception --}}
                                            <div class="mt-3 rounded-xl bg-red-50 border border-red-100 p-4 max-h-48 overflow-auto">
                                                <pre class="text-[11px] font-mono text-red-700 whitespace-pre-wrap break-all">{{ $job->exception }}</pre>
                                            </div>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="lg:w-[180px] shrink-0 space-y-2" @click.stop>
                                            <button
                                                wire:click="retryJob('{{ $job->uuid }}')"
                                                class="flex w-full items-center gap-2.5 rounded-xl px-3.5 py-2.5 text-xs font-medium text-blue-700 bg-white border border-slate-200 hover:bg-blue-50 hover:border-blue-200 transition-colors"
                                            >
                                                <iconify-icon icon="solar:refresh-bold-duotone" width="16" class="text-blue-500"></iconify-icon>
                                                {{ __('super_admin.monitoring.retry') }}
                                            </button>
                                            <button
                                                @click="$dispatch('confirm-action', { title: 'Supprimer', message: 'Supprimer ce job en erreur ?', confirmLabel: 'Supprimer', variant: 'danger', onConfirm: () => $wire.deleteFailedJob('{{ $job->uuid }}') })"
                                                class="flex w-full items-center gap-2.5 rounded-xl px-3.5 py-2.5 text-xs font-medium text-red-600 bg-white border border-slate-200 hover:bg-red-50 hover:border-red-200 transition-colors"
                                            >
                                                <iconify-icon icon="solar:trash-bin-trash-bold-duotone" width="16" class="text-red-500"></iconify-icon>
                                                {{ __('super_admin.monitoring.delete') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center text-slate-400">
                                    <iconify-icon icon="solar:check-circle-bold-duotone" width="44" class="text-emerald-300 mb-3"></iconify-icon>
                                    <p class="text-sm font-medium">{{ __('super_admin.monitoring.empty') }}</p>
                                </div>
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
