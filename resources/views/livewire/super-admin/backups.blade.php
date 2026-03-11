<div class="space-y-6 pb-12">
    {{-- Header --}}
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ __('super_admin.backups.title') }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ __('super_admin.backups.subtitle') }}</p>
        </div>
        <button
            wire:click="createBackup"
            wire:loading.attr="disabled"
            class="inline-flex items-center gap-2 rounded-xl bg-[#005F02] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#004A02] transition-colors disabled:opacity-50"
        >
            <iconify-icon icon="solar:database-bold-duotone" width="16" wire:loading.remove wire:target="createBackup"></iconify-icon>
            <iconify-icon icon="solar:refresh-bold" width="16" class="animate-spin" wire:loading wire:target="createBackup"></iconify-icon>
            {{ __('super_admin.backups.create') }}
        </button>
    </header>

    {{-- Session flash --}}
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-center gap-2">
            <iconify-icon icon="solar:check-circle-bold" width="18"></iconify-icon>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 flex items-center gap-2">
            <iconify-icon icon="solar:danger-triangle-bold" width="18"></iconify-icon>
            {{ session('error') }}
        </div>
    @endif

    {{-- KPI Cards --}}
    @php
        $allBackups = $this->backups;
        $totalCount = count($allBackups);
        $totalSize = array_sum(array_column($allBackups, 'size'));
        $encryptedCount = count(array_filter($allBackups, fn($b) => $b['encrypted']));
        $latestDate = $totalCount > 0 ? $allBackups[0]['created_at'] : null;

        // Human-readable total size
        if ($totalSize >= 1073741824) {
            $totalSizeHuman = number_format($totalSize / 1073741824, 2) . ' Go';
        } elseif ($totalSize >= 1048576) {
            $totalSizeHuman = number_format($totalSize / 1048576, 2) . ' Mo';
        } elseif ($totalSize >= 1024) {
            $totalSizeHuman = number_format($totalSize / 1024, 2) . ' Ko';
        } else {
            $totalSizeHuman = $totalSize . ' o';
        }
    @endphp
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F2E3BB]/30 text-[#005F02]">
                    <iconify-icon icon="solar:database-bold-duotone" width="20"></iconify-icon>
                </div>
                <div>
                    <p class="text-[11px] font-medium text-slate-500">{{ __('super_admin.backups.total_backups') }}</p>
                    <p class="text-2xl font-bold text-slate-900 tabular-nums">{{ $totalCount }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <iconify-icon icon="solar:server-bold-duotone" width="20"></iconify-icon>
                </div>
                <div>
                    <p class="text-[11px] font-medium text-slate-500">{{ __('super_admin.backups.total_size') }}</p>
                    <p class="text-2xl font-bold text-emerald-600 tabular-nums">{{ $totalSizeHuman }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <iconify-icon icon="solar:lock-bold-duotone" width="20"></iconify-icon>
                </div>
                <div>
                    <p class="text-[11px] font-medium text-slate-500">{{ __('super_admin.backups.encrypted') }}</p>
                    <p class="text-2xl font-bold text-amber-600 tabular-nums">{{ $encryptedCount }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <iconify-icon icon="solar:calendar-bold-duotone" width="20"></iconify-icon>
                </div>
                <div>
                    <p class="text-[11px] font-medium text-slate-500">{{ __('super_admin.backups.latest') }}</p>
                    <p class="text-sm font-bold text-blue-600">
                        @if ($latestDate)
                            {{ \Illuminate\Support\Carbon::parse($latestDate)->format('d/m/Y H:i') }}
                        @else
                            —
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Backups table + expandable detail --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden" x-data="{ expanded: null }">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-sm font-semibold text-slate-800">{{ __('super_admin.backups.list_title') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/40">
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.backups.col_filename') }}</th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500 text-right w-[100px]">{{ __('super_admin.backups.col_size') }}</th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500 w-[140px]">{{ __('super_admin.backups.col_date') }}</th>
                        <th class="px-5 py-3 w-[40px]"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($allBackups as $index => $backup)
                        @php
                            $ext = $backup['encrypted'] ? 'enc' : 'sql';
                            $isEncrypted = $backup['encrypted'];
                        @endphp

                        <tr
                            class="border-b border-slate-100 transition-colors cursor-pointer"
                            :class="expanded === {{ $index }} ? 'bg-slate-50' : 'hover:bg-slate-50/50'"
                            @click="expanded = expanded === {{ $index }} ? null : {{ $index }}"
                        >
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="inline-flex items-center rounded-md {{ $isEncrypted ? 'bg-amber-50 text-amber-600 border-amber-200' : 'bg-slate-100 text-slate-500 border-slate-200' }} border px-1.5 py-0.5 text-[10px] font-mono font-medium uppercase">{{ $ext }}</span>
                                    <span class="text-sm font-medium text-slate-700 truncate max-w-[300px]">{{ $backup['name'] }}</span>
                                    @if ($isEncrypted)
                                        <iconify-icon icon="solar:lock-bold" width="12" class="text-amber-500 shrink-0"></iconify-icon>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3 text-sm text-slate-600 text-right tabular-nums">{{ $backup['size_human'] }}</td>
                            <td class="px-5 py-3">
                                <div class="text-xs text-slate-600">{{ \Illuminate\Support\Carbon::parse($backup['created_at'])->format('d/m/Y') }}</div>
                                <div class="text-[11px] text-slate-400">{{ \Illuminate\Support\Carbon::parse($backup['created_at'])->format('H:i') }}</div>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <div
                                    class="inline-flex items-center justify-center h-6 w-6 rounded-md transition-colors"
                                    :class="expanded === {{ $index }} ? 'bg-[#005F02]/10 text-[#005F02]' : 'text-slate-300'"
                                >
                                    <iconify-icon
                                        :icon="expanded === {{ $index }} ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear'"
                                        width="14"
                                    ></iconify-icon>
                                </div>
                            </td>
                        </tr>

                        {{-- Detail panel --}}
                        <tr x-show="expanded === {{ $index }}" x-cloak>
                            <td colspan="4" class="px-0 py-0">
                                <div
                                    x-show="expanded === {{ $index }}"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="border-b border-slate-100 bg-slate-50/50 px-5 py-4"
                                >
                                    <div class="flex flex-col lg:flex-row gap-4">
                                        {{-- Detail info --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="rounded-xl bg-white border border-slate-200 divide-y divide-slate-100 overflow-hidden">
                                                <div class="flex items-baseline gap-4 px-4 py-2.5 text-xs">
                                                    <span class="shrink-0 w-[100px] font-medium text-slate-400">{{ __('super_admin.backups.col_filename') }}</span>
                                                    <span class="text-slate-700 font-mono text-[11px] break-all">{{ $backup['name'] }}</span>
                                                </div>
                                                <div class="flex items-baseline gap-4 px-4 py-2.5 text-xs">
                                                    <span class="shrink-0 w-[100px] font-medium text-slate-400">{{ __('super_admin.backups.col_size') }}</span>
                                                    <span class="text-slate-700 tabular-nums">{{ $backup['size_human'] }}</span>
                                                </div>
                                                <div class="flex items-baseline gap-4 px-4 py-2.5 text-xs">
                                                    <span class="shrink-0 w-[100px] font-medium text-slate-400">{{ __('super_admin.backups.col_date') }}</span>
                                                    <span class="text-slate-700">{{ $backup['created_at'] }} &middot; {{ \Illuminate\Support\Carbon::parse($backup['created_at'])->diffForHumans() }}</span>
                                                </div>
                                                <div class="flex items-baseline gap-4 px-4 py-2.5 text-xs">
                                                    <span class="shrink-0 w-[100px] font-medium text-slate-400">{{ __('super_admin.backups.status') }}</span>
                                                    @if ($isEncrypted)
                                                        <span class="inline-flex items-center gap-1 rounded-md bg-amber-50 border border-amber-200 px-2 py-0.5 text-[11px] font-semibold text-amber-700">
                                                            <iconify-icon icon="solar:lock-bold" width="11"></iconify-icon>
                                                            {{ __('super_admin.backups.encrypted') }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 rounded-md bg-slate-50 border border-slate-200 px-2 py-0.5 text-[11px] font-semibold text-slate-600">
                                                            {{ __('super_admin.backups.not_encrypted') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="lg:w-[180px] shrink-0 space-y-2" @click.stop>
                                            <button
                                                wire:click="downloadBackup('{{ $backup['name'] }}')"
                                                class="flex w-full items-center gap-2.5 rounded-xl px-3.5 py-2.5 text-xs font-medium text-[#005F02] bg-white border border-slate-200 hover:bg-emerald-50 hover:border-emerald-200 transition-colors"
                                            >
                                                <iconify-icon icon="solar:download-minimalistic-bold-duotone" width="16" class="text-[#005F02]"></iconify-icon>
                                                {{ __('super_admin.backups.download') }}
                                            </button>
                                            <button
                                                @click="$dispatch('confirm-action', { title: '{{ __('super_admin.backups.delete') }}', message: '{{ __('super_admin.backups.confirm_delete') }}', confirmLabel: '{{ __('super_admin.backups.delete') }}', variant: 'danger', onConfirm: () => $wire.deleteBackup('{{ $backup['name'] }}') })"
                                                class="flex w-full items-center gap-2.5 rounded-xl px-3.5 py-2.5 text-xs font-medium text-red-600 bg-white border border-slate-200 hover:bg-red-50 hover:border-red-200 transition-colors"
                                            >
                                                <iconify-icon icon="solar:trash-bin-trash-bold-duotone" width="16" class="text-red-500"></iconify-icon>
                                                {{ __('super_admin.backups.delete') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center text-slate-400">
                                    <iconify-icon icon="solar:database-bold-duotone" width="44" class="text-slate-300 mb-3"></iconify-icon>
                                    <p class="text-sm font-medium">{{ __('super_admin.backups.empty') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
