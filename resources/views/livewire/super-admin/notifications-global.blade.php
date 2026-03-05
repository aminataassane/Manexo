<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-xl font-bold text-slate-800">{{ __('super_admin.notifications.title') }}</h2>
        <p class="mt-1 text-sm text-slate-500">{{ __('super_admin.notifications.subtitle') }}</p>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="rounded-2xl border border-slate-200/60 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50">
                    <iconify-icon icon="solar:bell-bold-duotone" width="20" class="text-indigo-600"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('super_admin.notifications.total') }}</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $this->stats['total'] }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200/60 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50">
                    <iconify-icon icon="solar:check-read-bold-duotone" width="20" class="text-emerald-600"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('super_admin.notifications.read') }}</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ $this->stats['read'] }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200/60 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50">
                    <iconify-icon icon="solar:bell-bing-bold-duotone" width="20" class="text-amber-600"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('super_admin.notifications.unread') }}</p>
                    <p class="text-2xl font-bold text-amber-600">{{ $this->stats['unread'] }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200/60 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50">
                    <iconify-icon icon="solar:calendar-bold-duotone" width="20" class="text-blue-600"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('super_admin.notifications.last_24h') }}</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $this->stats['last_24h'] }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200/60 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-purple-50">
                    <iconify-icon icon="solar:calendar-mark-bold-duotone" width="20" class="text-purple-600"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('super_admin.notifications.last_7d') }}</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $this->stats['last_7d'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- By Type -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-sm font-semibold text-slate-800">{{ __('super_admin.notifications.by_type') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">{{ __('super_admin.notifications.col_type') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider">{{ __('super_admin.notifications.col_count') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->byType as $item)
                        <tr class="transition-colors">
                            <td class="px-5 py-3.5 text-sm font-medium">{{ class_basename($item->type) }}</td>
                            <td class="px-5 py-3.5 text-sm text-right">
                                <span class="inline-flex items-center rounded-full bg-indigo-50 border border-indigo-200 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">
                                    {{ $item->count }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-5 py-8 text-center text-sm text-slate-400">
                                {{ __('super_admin.notifications.empty_types') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Notifications -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-sm font-semibold text-slate-800">{{ __('super_admin.notifications.recent') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">{{ __('super_admin.notifications.col_date') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">{{ __('super_admin.notifications.col_type') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">{{ __('super_admin.notifications.col_user') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">{{ __('super_admin.notifications.col_data') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->recentNotifications as $notif)
                        <tr class="transition-colors">
                            <td class="px-5 py-3.5 text-sm whitespace-nowrap">{{ \Carbon\Carbon::parse($notif->created_at)->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center rounded-full bg-indigo-50 border border-indigo-200 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">
                                    {{ class_basename($notif->type) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-sm">{{ $notif->user_name ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-xs font-mono max-w-xs truncate">
                                @if (is_array($notif->data))
                                    {{ \Illuminate\Support\Str::limit(json_encode($notif->data), 80) }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-sm text-slate-400">
                                {{ __('super_admin.notifications.empty_recent') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
