{{-- Un seul élément racine pour Livewire (évite "Snapshot missing" / "Component not found") --}}
<div class="w-full max-w-full min-w-0 mx-auto">
{{-- =====================================================================
     STAFF VIEW: même layout que Tickets — largeur pleine, titre gauche, actions droite
     ===================================================================== --}}
@if($canViewAll)
<div class="w-full max-w-full min-w-0 mx-auto">
    @if(session('share_success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition>
            <iconify-icon icon="solar:check-circle-bold" width="18" class="mr-2 align-middle inline-block"></iconify-icon>
            {{ session('share_success') }}
        </div>
    @endif

    {{-- Header : titre à gauche, période + export à droite (comme Tickets) --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6 sm:mb-8">
        <div class="min-w-0">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight sm:text-2xl lg:text-3xl min-[1920px]:text-4xl">{{ __('task_report.title') }}</h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">{{ __('task_report.subtitle') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 min-w-0">
            <div class="inline-flex items-center rounded-xl border border-slate-200 bg-white p-1 shadow-sm flex-shrink-0">
                <button wire:click="setPeriod('today')" type="button" class="px-2.5 sm:px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all whitespace-nowrap {{ $period === 'today' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">{{ __('task_report.today') }}</button>
                <button wire:click="setPeriod('week')" type="button" class="px-2.5 sm:px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all whitespace-nowrap {{ $period === 'week' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">{{ __('task_report.this_week') }}</button>
                <button wire:click="setPeriod('month')" type="button" class="px-2.5 sm:px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all whitespace-nowrap {{ $period === 'month' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">{{ __('task_report.this_month') }}</button>
                <button wire:click="setPeriod('custom')" type="button" class="px-2.5 sm:px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all whitespace-nowrap {{ $period === 'custom' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">{{ __('task_report.custom') }}</button>
            </div>
            <x-dropdown align="right" width="48" contentClasses="py-1 bg-white rounded-xl shadow-xl border border-slate-200">
                <x-slot name="trigger">
                    <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 sm:px-4 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                        <iconify-icon icon="solar:export-linear" width="18"></iconify-icon>
                        {{ __('task_report.export') }}
                    </button>
                </x-slot>
                <x-slot name="content">
                    <a href="{{ route('reports.tasks.export', ['format' => 'csv', 'period' => $period, 'dateFrom' => $dateFrom, 'dateTo' => $dateTo]) }}" target="_blank" rel="noopener" class="block w-full px-4 py-2.5 text-start text-sm text-slate-700 hover:bg-slate-100 transition-colors flex items-center gap-2 rounded-lg mx-1">
                        <iconify-icon icon="solar:document-text-bold-duotone" width="16"></iconify-icon>
                        {{ __('task_report.export_csv') }}
                    </a>
                    <a href="{{ route('reports.tasks.export', ['format' => 'pdf', 'period' => $period, 'dateFrom' => $dateFrom, 'dateTo' => $dateTo]) }}" target="_blank" rel="noopener" class="block w-full px-4 py-2.5 text-start text-sm text-slate-700 hover:bg-slate-100 transition-colors flex items-center gap-2 rounded-lg mx-1">
                        <iconify-icon icon="solar:file-bold-duotone" width="16"></iconify-icon>
                        {{ __('task_report.export_pdf') }}
                    </a>
                    @if($canShare)
                        <div class="border-t border-slate-100 my-1"></div>
                        <button wire:click="$set('showShareModal', true)" type="button" class="block w-full px-4 py-2.5 text-start text-sm text-slate-700 hover:bg-slate-100 transition-colors flex items-center gap-2 rounded-lg mx-1">
                            <iconify-icon icon="solar:share-bold-duotone" width="16"></iconify-icon>
                            {{ __('task_report.share') }}
                        </button>
                    @endif
                </x-slot>
            </x-dropdown>
        </div>
    </div>

    @if($period === 'custom')
        <div class="mb-6 rounded-xl sm:rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center gap-2 px-4 sm:px-5 py-3 border-b border-slate-50 bg-slate-50/50">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[var(--accent)]/10 text-[var(--accent)]">
                    <iconify-icon icon="solar:calendar-minimalistic-bold-duotone" width="20"></iconify-icon>
                </div>
                <span class="text-sm font-semibold text-slate-700">{{ __('task_report.custom') }}</span>
            </div>
            <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-end gap-4 sm:gap-6">
                <div class="flex-1 min-w-0">
                    <label for="dateFrom" class="block text-xs font-medium uppercase tracking-wider text-slate-500 mb-1.5">{{ __('task_report.from') }}</label>
                    <input type="date" id="dateFrom" wire:model.live="dateFrom" class="block w-full min-w-0 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-[var(--accent)] focus:outline-none focus:ring-2 focus:ring-[var(--accent)]/20 transition-colors" />
                </div>
                <div class="hidden sm:flex items-center pb-2.5 text-slate-300 shrink-0" aria-hidden="true">
                    <iconify-icon icon="solar:arrow-right-linear" width="20"></iconify-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <label for="dateTo" class="block text-xs font-medium uppercase tracking-wider text-slate-500 mb-1.5">{{ __('task_report.to') }}</label>
                    <input type="date" id="dateTo" wire:model.live="dateTo" class="block w-full min-w-0 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-[var(--accent)] focus:outline-none focus:ring-2 focus:ring-[var(--accent)]/20 transition-colors" />
                </div>
            </div>
        </div>
    @endif

    {{-- KPI cards — même style que Tickets (grille large) --}}
    <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6 sm:mb-8 lg:grid-cols-3 min-[1920px]:gap-6">
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500 truncate block">{{ __('task_report.tickets_closed') }}</span>
                    <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900">{{ number_format($stats['closedTicketsCount'] ?? 0, 0, ',', ' ') }}</div>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 group-hover:scale-110 transition-transform shrink-0">
                    <iconify-icon icon="solar:check-circle-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500 truncate block">{{ __('task_report.top_category') }}</span>
                    <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900 truncate">{{ $stats['topCategoryClosed']['name'] ?? '—' }}</div>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $stats['topCategoryClosed']['count'] ?? 0 }} {{ trans_choice('task_report.tickets_count', $stats['topCategoryClosed']['count'] ?? 0) }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600 group-hover:scale-110 transition-transform shrink-0">
                    <iconify-icon icon="solar:tag-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500 truncate block">{{ __('task_report.top_user') }}</span>
                    <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900 truncate">{{ $stats['topUserClosed']['name'] ?? '—' }}</div>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $stats['topUserClosed']['count'] ?? 0 }} {{ trans_choice('task_report.tickets_count', $stats['topUserClosed']['count'] ?? 0) }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 group-hover:scale-110 transition-transform shrink-0">
                    <iconify-icon icon="solar:user-check-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <p class="text-xs text-slate-500 mb-5 mt-0">{{ __('task_report.period_label', ['from' => $from->format('d/m/Y'), 'to' => $to->format('d/m/Y')]) }}</p>

    {{-- Répartition (catégorie / utilisateur) — 2 colonnes pleine largeur --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8 min-w-0">
        <div class="rounded-xl sm:rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden min-w-0">
            <div class="px-4 sm:px-6 py-4 border-b border-slate-50 bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-900">{{ __('task_report.closed_by_category') }}</h3>
            </div>
            <div class="p-4 sm:p-6 space-y-5">
                    @forelse($stats['byCategoryClosed'] ?? [] as $cat)
                        @php $total = array_sum(array_column($stats['byCategoryClosed'] ?? [], 'count')) ?: 1; $pct = round(($cat['count'] / $total) * 100); @endphp
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-slate-700">{{ $cat['name'] }}</span>
                                <span class="text-sm font-bold text-slate-900">{{ $cat['count'] }} <span class="text-slate-400 font-normal">({{ $pct }}%)</span></span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                <div class="h-full rounded-full bg-slate-800 transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-sm text-slate-500 py-8">{{ __('task_report.no_data') }}</p>
                    @endforelse
            </div>
        </div>
        <div class="rounded-xl sm:rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden min-w-0">
            <div class="px-4 sm:px-6 py-4 border-b border-slate-50 bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-900">{{ __('task_report.closed_by_user') }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[240px]">
                        <tbody class="divide-y divide-slate-100">
                            @forelse($stats['byUserClosed'] ?? [] as $row)
                                @php $total = $stats['closedTicketsCount'] ?? 1; $pct = $total > 0 ? round(($row['count'] / $total) * 100) : 0; @endphp
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-4 sm:px-6 py-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($row['name']) }}&background=random&color=fff&size=36" class="h-8 w-8 rounded-full shrink-0 ring-2 ring-white shadow" alt="">
                                            <span class="text-sm font-medium text-slate-900 truncate">{{ $row['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-3 text-right">
                                        <span class="text-sm font-semibold text-slate-700">{{ $row['count'] }}</span>
                                        <span class="text-xs text-slate-400 ml-1">({{ $pct }}%)</span>
                                        <div class="w-14 h-1.5 bg-slate-100 rounded-full mt-1 ml-auto overflow-hidden max-w-[60px]">
                                            <div class="h-full rounded-full bg-[var(--accent)]" style="width: {{ min(100, $pct) }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="px-4 sm:px-6 py-8 text-center text-sm text-slate-500">{{ __('task_report.no_data') }}</td></tr>
                            @endforelse
                        </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Tableau : Tickets clôturés — pleine largeur, style Tickets --}}
    <div class="rounded-xl sm:rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden min-w-0">
        <div class="px-4 sm:px-6 py-4 border-b border-slate-50 bg-slate-50/50">
            <h3 class="text-base font-bold text-slate-900">{{ __('task_report.closed_tickets') }}</h3>
            @if(($stats['closedTicketsCount'] ?? 0) > 0)
                <p class="text-xs text-slate-500 mt-0.5">{{ $stats['closedTicketsCount'] }} {{ trans_choice('task_report.tickets_count', $stats['closedTicketsCount']) }}</p>
            @endif
        </div>
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[640px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('task_report.ticket') }}</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('task_report.category') }}</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('task_report.closed_at') }}</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('task_report.closed_by') }}</th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('task_report.checklist_progress') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->closedTickets as $ticket)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 sm:px-6 py-3 sm:py-4">
                                    <a href="{{ route('tickets.discussion', $ticket) }}" wire:navigate class="text-sm font-medium text-[var(--accent)] hover:underline truncate max-w-[200px] sm:max-w-[280px] inline-block">{{ Str::limit($ticket->subject, 50) }}</a>
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-sm text-slate-600">{{ $ticket->category?->name ?? '—' }}</td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-sm text-slate-500 whitespace-nowrap">{{ $ticket->updated_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-sm text-slate-600">{{ $ticket->closedByUser?->name ?? $ticket->assignees->first()?->name ?? $ticket->creator?->name ?? '—' }}</td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4">
                                    @php $done = (int) ($ticket->checklist_done_count ?? 0); $total = (int) ($ticket->checklist_items_count ?? 0); @endphp
                                    @if($total > 0)
                                        <span class="inline-flex items-center justify-center min-w-[2.5rem] px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 text-sm font-medium">{{ $done }}/{{ $total }}</span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 sm:px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                            <iconify-icon icon="solar:inbox-line-linear" width="28"></iconify-icon>
                                        </div>
                                        <p class="text-sm font-medium text-slate-600">{{ __('task_report.no_data') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
            </table>
        </div>
        @if($this->closedTickets->hasPages())
            <div class="px-4 sm:px-6 py-4 border-t border-slate-100 bg-slate-50/30">
                {{ $this->closedTickets->links('vendor.pagination.manexo') }}
            </div>
        @endif
    </div>

    @if($canShare && $showShareModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4" x-data @keydown.escape.window="$wire.set('showShareModal', false)">
            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl p-6" @click.outside="$wire.set('showShareModal', false)">
                    <button type="button" wire:click="$set('showShareModal', false)" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition-colors rounded-lg p-1">
                        <iconify-icon icon="solar:close-circle-linear" width="24"></iconify-icon>
                    </button>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">{{ __('task_report.share') }}</h3>
                    <p class="text-sm text-slate-500 mb-6">{{ __('task_report.link_valid_7_days') }}</p>
                    @if(!$lastSignedUrl)
                        <button wire:click="generateSignedUrl" type="button" class="w-full rounded-xl bg-[var(--accent)] px-4 py-3 text-sm font-medium text-white shadow-lg hover:opacity-90 transition-opacity">
                            <iconify-icon icon="solar:link-bold" width="16" class="mr-1 align-text-bottom"></iconify-icon>
                            {{ __('task_report.generate_link') }}
                        </button>
                    @else
                        <div class="mb-5" x-data="{ copied: false }">
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1.5 block">{{ __('task_report.copy_link') }}</label>
                            <div class="flex items-center gap-2">
                                <input type="text" readonly value="{{ $lastSignedUrl }}" class="flex-1 rounded-xl border-slate-200 bg-slate-50 text-xs text-slate-600 px-3 py-2.5" />
                                <button type="button" @click="navigator.clipboard.writeText('{{ $lastSignedUrl }}'); copied = true; setTimeout(() => copied = false, 2000)" class="shrink-0 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                                    <span x-show="!copied"><iconify-icon icon="solar:copy-linear" width="14"></iconify-icon></span>
                                    <span x-show="copied" x-cloak class="text-emerald-600">{{ __('task_report.link_copied') }}</span>
                                </button>
                            </div>
                        </div>
                        <div class="border-t border-slate-100 pt-5">
                            <label for="shareUser" class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1.5 block">{{ __('task_report.send_to') }}</label>
                            <select id="shareUser" wire:model="shareUserId" class="w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] mb-3 py-2.5">
                                <option value="">—</option>
                                @foreach($this->staffMembers as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                            <button wire:click="sendShareNotification" type="button" class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-medium text-white hover:bg-slate-800 transition-colors disabled:opacity-50" {{ !$shareUserId ? 'disabled' : '' }}>
                                <iconify-icon icon="solar:plain-bold" width="16" class="mr-1 align-text-bottom"></iconify-icon>
                                {{ __('task_report.send_notification') }}
                            </button>
                        </div>
                    @endif
            </div>
        </div>
    @endif
</div>

{{-- =====================================================================
     MEMBER VIEW: "Mes tâches terminées" — même layout que Tickets (large, titre gauche, actions droite)
     ===================================================================== --}}
@else
<div class="w-full max-w-full min-w-0 mx-auto">
    {{-- Header : titre à gauche, période + export à droite (comme la page Tickets) --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6 sm:mb-8">
        <div class="min-w-0">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight sm:text-2xl lg:text-3xl min-[1920px]:text-4xl">{{ __('task_report.my_tasks_title') }}</h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">{{ __('task_report.my_tasks_subtitle') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 min-w-0">
            <div class="inline-flex items-center rounded-xl border border-slate-200 bg-white p-1 shadow-sm flex-shrink-0">
                <button wire:click="setPeriod('week')" type="button" class="px-2.5 sm:px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all whitespace-nowrap {{ $period === 'week' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">{{ __('task_report.this_week') }}</button>
                <button wire:click="setPeriod('month')" type="button" class="px-2.5 sm:px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all whitespace-nowrap {{ $period === 'month' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">{{ __('task_report.this_month') }}</button>
                <button wire:click="setPeriod('custom')" type="button" class="px-2.5 sm:px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all whitespace-nowrap {{ $period === 'custom' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">{{ __('task_report.custom') }}</button>
            </div>
            <x-dropdown align="right" width="48" contentClasses="py-1 bg-white rounded-xl shadow-xl border border-slate-200">
                <x-slot name="trigger">
                    <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 sm:px-4 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                        <iconify-icon icon="solar:export-linear" width="18"></iconify-icon>
                        {{ __('task_report.export') }}
                    </button>
                </x-slot>
                <x-slot name="content">
                    <a href="{{ route('reports.tasks.export', ['format' => 'csv', 'period' => $period, 'dateFrom' => $dateFrom, 'dateTo' => $dateTo]) }}" target="_blank" rel="noopener" class="block w-full px-4 py-2.5 text-start text-sm text-slate-700 hover:bg-slate-100 transition-colors flex items-center gap-2 rounded-lg mx-1">
                        <iconify-icon icon="solar:document-text-bold-duotone" width="16"></iconify-icon>
                        {{ __('task_report.export_csv') }}
                    </a>
                    <a href="{{ route('reports.tasks.export', ['format' => 'pdf', 'period' => $period, 'dateFrom' => $dateFrom, 'dateTo' => $dateTo]) }}" target="_blank" rel="noopener" class="block w-full px-4 py-2.5 text-start text-sm text-slate-700 hover:bg-slate-100 transition-colors flex items-center gap-2 rounded-lg mx-1">
                        <iconify-icon icon="solar:file-bold-duotone" width="16"></iconify-icon>
                        {{ __('task_report.export_pdf') }}
                    </a>
                </x-slot>
            </x-dropdown>
        </div>
    </div>

    @if($period === 'custom')
        <div class="mb-6 rounded-xl sm:rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center gap-2 px-4 sm:px-5 py-3 border-b border-slate-50 bg-slate-50/50">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[var(--accent)]/10 text-[var(--accent)]">
                    <iconify-icon icon="solar:calendar-minimalistic-bold-duotone" width="20"></iconify-icon>
                </div>
                <span class="text-sm font-semibold text-slate-700">{{ __('task_report.custom') }}</span>
            </div>
            <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-end gap-4 sm:gap-6">
                <div class="flex-1 min-w-0">
                    <label for="dateFromMember" class="block text-xs font-medium uppercase tracking-wider text-slate-500 mb-1.5">{{ __('task_report.from') }}</label>
                    <input type="date" id="dateFromMember" wire:model.live="dateFrom" class="block w-full min-w-0 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-[var(--accent)] focus:outline-none focus:ring-2 focus:ring-[var(--accent)]/20 transition-colors" />
                </div>
                <div class="hidden sm:flex items-center pb-2.5 text-slate-300 shrink-0" aria-hidden="true">
                    <iconify-icon icon="solar:arrow-right-linear" width="20"></iconify-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <label for="dateToMember" class="block text-xs font-medium uppercase tracking-wider text-slate-500 mb-1.5">{{ __('task_report.to') }}</label>
                    <input type="date" id="dateToMember" wire:model.live="dateTo" class="block w-full min-w-0 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-[var(--accent)] focus:outline-none focus:ring-2 focus:ring-[var(--accent)]/20 transition-colors" />
                </div>
            </div>
        </div>
    @endif

    {{-- KPI : 2 cartes simples (comme la page Tickets) --}}
    <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500 truncate block">{{ __('task_report.tickets_closed') }}</span>
                    <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900">{{ $stats['closedTicketsCount'] ?? 0 }}</div>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 group-hover:scale-110 transition-transform shrink-0">
                    <iconify-icon icon="solar:check-circle-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500 truncate block">{{ __('task_report.sub_tasks_completed') }}</span>
                    <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900">{{ $stats['tasksTotal'] ?? 0 }}</div>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 group-hover:scale-110 transition-transform shrink-0">
                    <iconify-icon icon="solar:list-check-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <p class="text-xs text-slate-500 mb-5 mt-0">{{ __('task_report.period_label', ['from' => $from->format('d/m'), 'to' => $to->format('d/m')]) }}</p>

    {{-- Tableau : Tickets clôturés — pleine largeur, style Tickets --}}
    <div class="rounded-xl sm:rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden min-w-0">
        <div class="px-4 sm:px-6 py-4 border-b border-slate-50 bg-slate-50/50">
            <h3 class="text-base font-bold text-slate-900">{{ __('task_report.closed_tickets') }}</h3>
            @if(($stats['closedTicketsCount'] ?? 0) > 0)
                <p class="text-xs text-slate-500 mt-0.5">{{ $stats['closedTicketsCount'] }} {{ trans_choice('task_report.tickets_count', $stats['closedTicketsCount']) }}</p>
            @endif
        </div>
        @if(($stats['closedTicketsCount'] ?? 0) > 0)
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full min-w-[520px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-4 sm:px-6 py-3 text-left text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('task_report.ticket') }}</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500 hidden sm:table-cell">{{ __('task_report.category') }}</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('task_report.checklist_progress') }}</th>
                            <th class="px-4 sm:px-6 py-3 text-right text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('task_report.closed_at') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->closedTickets as $ticket)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 sm:px-6 py-3 sm:py-4">
                                    <a href="{{ route('tickets.discussion', $ticket) }}" wire:navigate class="group flex items-center gap-3 min-w-0">
                                        <span class="flex h-8 w-8 sm:h-9 sm:w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 group-hover:bg-[var(--accent)]/10 group-hover:text-[var(--accent)] transition-colors">
                                            <iconify-icon icon="solar:ticket-bold" width="16"></iconify-icon>
                                        </span>
                                        <span class="text-sm font-medium text-slate-900 group-hover:text-[var(--accent)] truncate">{{ Str::limit($ticket->subject, 45) }}</span>
                                    </a>
                                    @if($ticket->category?->name)
                                        <p class="text-xs text-slate-500 mt-0.5 sm:hidden pl-11">{{ $ticket->category->name }}</p>
                                    @endif
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-sm text-slate-600 hidden sm:table-cell">
                                    @if($ticket->category?->name)
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">{{ $ticket->category->name }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4">
                                    @php $done = (int) ($ticket->checklist_done_count ?? 0); $total = (int) ($ticket->checklist_items_count ?? 0); @endphp
                                    @if($total > 0)
                                        <span class="inline-flex items-center justify-center min-w-[2.25rem] px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 text-xs sm:text-sm font-medium">{{ $done }}/{{ $total }}</span>
                                    @else
                                        <span class="text-slate-400 text-sm">—</span>
                                    @endif
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-right text-sm text-slate-500 whitespace-nowrap">{{ $ticket->updated_at->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($this->closedTickets->hasPages())
                <div class="px-4 sm:px-6 py-4 border-t border-slate-100 bg-slate-50/30">
                    {{ $this->closedTickets->links('vendor.pagination.manexo') }}
                </div>
            @endif
        @else
            <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-300 mb-3">
                    <iconify-icon icon="solar:ticket-linear" width="32"></iconify-icon>
                </div>
                <p class="text-sm font-semibold text-slate-700">{{ __('task_report.no_data') }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ __('task_report.my_tasks_empty_hint') }}</p>
            </div>
        @endif
    </div>
</div>
@endif
</div>
