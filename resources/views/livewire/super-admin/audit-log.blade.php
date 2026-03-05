<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">{{ __('super_admin.audit.title') }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ __('super_admin.audit.subtitle') }}</p>
        </div>
        <a href="{{ route('platform-admin.audit-log.export', array_filter(['actionFilter' => $actionFilter, 'targetTypeFilter' => $targetTypeFilter, 'dateFrom' => $dateFrom, 'dateTo' => $dateTo])) }}" class="sa-btn-secondary">
            <iconify-icon icon="solar:download-minimalistic-bold" width="16"></iconify-icon>
            {{ __('super_admin.audit.export_csv') }}
        </a>
    </div>

    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row gap-3 flex-wrap">
        <select
            wire:model.live="actionFilter"
            class="rounded-xl border border-slate-200 bg-white py-2.5 px-4 text-sm text-slate-700 shadow-sm focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
        >
            <option value="">{{ __('super_admin.audit.all_actions') }}</option>
            @foreach ($actions as $action)
                @php
                    $actionKey = 'super_admin.audit.action_' . str_replace('.', '_', $action);
                    $actionLabel = __($actionKey) !== $actionKey ? __($actionKey) : $action;
                @endphp
                <option value="{{ $action }}">{{ $actionLabel }}</option>
            @endforeach
        </select>

        <select
            wire:model.live="targetTypeFilter"
            class="rounded-xl border border-slate-200 bg-white py-2.5 px-4 text-sm text-slate-700 shadow-sm focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
        >
            <option value="">{{ __('super_admin.audit.all_targets') }}</option>
            @foreach ($targetTypes as $type)
                <option value="{{ $type }}">{{ class_basename($type) }}</option>
            @endforeach
        </select>

        <input
            type="date"
            wire:model.live="dateFrom"
            class="rounded-xl border border-slate-200 bg-white py-2.5 px-4 text-sm text-slate-700 shadow-sm focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
            placeholder="{{ __('super_admin.audit.date_from') }}"
        />

        <input
            type="date"
            wire:model.live="dateTo"
            class="rounded-xl border border-slate-200 bg-white py-2.5 px-4 text-sm text-slate-700 shadow-sm focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
            placeholder="{{ __('super_admin.audit.date_to') }}"
        />
    </div>

    <!-- Table -->
    <div class="rounded-2xl border border-slate-200/60 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.audit.col_date') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.audit.col_admin') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.audit.col_action') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.audit.col_target') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.audit.col_ip') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.audit.col_details') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5 text-sm text-slate-500 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-3.5 text-sm font-medium text-slate-700">{{ $log->user?->name ?? '—' }}</td>
                            <td class="px-5 py-3.5">
                                @php
                                    $actionKey = 'super_admin.audit.action_' . str_replace('.', '_', $log->action);
                                    $actionLabel = __($actionKey) !== $actionKey ? __($actionKey) : $log->action;
                                @endphp
                                <span class="inline-flex items-center rounded-full bg-indigo-50 border border-indigo-200 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">
                                    {{ $actionLabel }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-500">
                                @if ($log->target_type)
                                    <span class="text-slate-400">{{ class_basename($log->target_type) }}</span>
                                    @if ($log->metadata)
                                        @if (!empty($log->metadata['name']))
                                            — {{ $log->metadata['name'] }}
                                        @elseif (!empty($log->metadata['org_name']))
                                            — {{ $log->metadata['org_name'] }}
                                        @elseif (!empty($log->metadata['email']))
                                            — {{ $log->metadata['email'] }}
                                        @endif
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-400 font-mono">{{ $log->ip_address }}</td>
                            <td class="px-5 py-3.5 text-sm text-slate-500">
                                @if ($log->metadata)
                                    <button
                                        x-data="{ open: false }"
                                        @click="open = !open"
                                        class="text-indigo-600 hover:text-indigo-800 text-xs font-medium"
                                    >
                                        <span x-show="!open">{{ __('super_admin.audit.show_details') }}</span>
                                        <span x-show="open" x-cloak>{{ __('super_admin.audit.hide_details') }}</span>
                                    </button>
                                    <div x-show="open" x-cloak x-transition class="mt-2 rounded-lg bg-slate-50 p-2 text-xs font-mono text-slate-600 max-w-xs overflow-auto">
                                        @foreach ($log->metadata as $key => $value)
                                            <div><span class="text-slate-400">{{ $key }}:</span> {{ is_array($value) ? json_encode($value) : $value }}</div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-sm text-slate-400">
                                <iconify-icon icon="solar:document-text-bold-duotone" width="32" class="text-slate-300 mb-2"></iconify-icon>
                                <p>{{ __('super_admin.audit.empty') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="border-t border-slate-100 px-5 py-3">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
