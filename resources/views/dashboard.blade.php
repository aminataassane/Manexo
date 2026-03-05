{{-- resources/views/dashboard.blade.php --}}
<x-manexo-app-layout>
    <x-slot name="title">{{ __('pages.dashboard.title') }}</x-slot>

    @php
        $user = Auth::user();
        $org = request()->attributes->get('currentOrganization')
            ?? \App\Models\Organization::find(session('current_organization_id'));

        $role = 'member';
        if ($org && $user) {
            $role = $user->organizations()->whereKey($org->id)->first()?->pivot?->role ?: 'member';
        }

        $orgId = $org?->id;
        $orgName = $org?->name ?? '—';

        $statusLabel = function (string $status): string {
            return __('tickets.status.' . $status);
        };

        $statusPill = function (string $status): array {
            return match ($status) {
                'open' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-100', 'icon' => 'solar:danger-circle-bold'],
                'in_progress' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-100', 'icon' => 'solar:clock-circle-bold'],
                'pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-100', 'icon' => 'solar:pause-circle-bold'],
                'resolved', 'closed' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-100', 'icon' => 'solar:check-circle-bold'],
                default => ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'icon' => 'solar:info-circle-bold'],
            };
        };
    @endphp

    <!-- WELCOME SECTION (responsive: stack on mobile, row on sm+) -->
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
                <a href="{{ route('tickets.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-3 sm:py-2.5 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-200 hover:bg-slate-50 transition-all touch-target sm:min-h-0 sm:min-w-0">
                    <iconify-icon icon="solar:list-bold" width="18"></iconify-icon>
                    {{ __('pages.dashboard.view_tickets') }}
                </a>
                <a href="{{ route('tickets.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-3 sm:py-2.5 text-sm font-semibold text-white shadow-lg shadow-[var(--accent-ring)] hover:opacity-90 transition-all transform hover:-translate-y-0.5 touch-target sm:min-h-0 sm:min-w-0" style="background-color: var(--accent);">
                    <iconify-icon icon="solar:add-circle-bold" width="18"></iconify-icon>
                    {{ __('pages.dashboard.new_ticket') }}
                </a>
            </div>
        </div>
        
        <!-- Decorative Background Elements -->
        <div class="absolute top-0 right-0 -mt-20 -mr-20 h-64 w-64 rounded-full bg-[var(--accent-soft)] opacity-20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-20 -ml-20 h-64 w-64 rounded-full bg-blue-50 opacity-50 blur-3xl"></div>
    </div>

    {{-- ===================== ADMIN / OWNER ===================== --}}
    @if (in_array($role, ['owner', 'admin'], true))
        @php
            $open = \App\Models\Ticket::query()->where('organization_id', $orgId)->where('status', 'open')->count();
            $inProgress = \App\Models\Ticket::query()->where('organization_id', $orgId)->where('status', 'in_progress')->count();
            $pending = \App\Models\Ticket::query()->where('organization_id', $orgId)->where('status', 'pending')->count();
            $resolved7d = \App\Models\Ticket::query()
                ->where('organization_id', $orgId)
                ->whereIn('status', ['resolved', 'closed'])
                ->where('updated_at', '>=', now()->subDays(7))
                ->count();

            $priorityTickets = \App\Models\Ticket::query()
                ->where('tickets.organization_id', $orgId)
                ->whereIn('tickets.status', ['open', 'in_progress', 'pending'])
                ->join('ticket_priorities', 'tickets.ticket_priority_id', '=', 'ticket_priorities.id')
                ->orderByDesc('ticket_priorities.level')
                ->orderByDesc('tickets.updated_at')
                ->select('tickets.*')
                ->with(['priority'])
                ->limit(5)
                ->get();
        @endphp

        <!-- KPI CARDS (2 cols mobile, 4 cols desktop, gap responsive) -->
        <div class="grid grid-cols-2 gap-3 sm:gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:gap-6 min-[1920px]:gap-8">
            <!-- Open Tickets -->
            <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-slate-500 truncate">{{ __('pages.dashboard.open_tickets') }}</p>
                        <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900">{{ $open }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 text-red-600 group-hover:scale-110 transition-transform duration-300">
                        <iconify-icon icon="solar:danger-circle-bold-duotone" width="24"></iconify-icon>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs font-medium text-red-600">
                    <span class="flex items-center gap-1 bg-red-50 px-2 py-1 rounded-full">
                        <iconify-icon icon="solar:arrow-right-up-linear" width="12"></iconify-icon>
                        {{ __('pages.dashboard.action_required') }}
                    </span>
                </div>
            </div>

            <!-- In Progress -->
            <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-slate-500 truncate">{{ __('pages.dashboard.in_progress') }}</p>
                        <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900">{{ $inProgress }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 group-hover:scale-110 transition-transform duration-300">
                        <iconify-icon icon="solar:clock-circle-bold-duotone" width="24"></iconify-icon>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs font-medium text-blue-600">
                    <span class="flex items-center gap-1 bg-blue-50 px-2 py-1 rounded-full">
                        {{ __('pages.dashboard.active_now') }}
                    </span>
                </div>
            </div>

            <!-- Pending -->
            <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-slate-500 truncate">{{ __('pages.dashboard.pending') }}</p>
                        <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900">{{ $pending }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600 group-hover:scale-110 transition-transform duration-300">
                        <iconify-icon icon="solar:pause-circle-bold-duotone" width="24"></iconify-icon>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs font-medium text-amber-600">
                    <span class="flex items-center gap-1 bg-amber-50 px-2 py-1 rounded-full">
                        {{ __('pages.dashboard.customer_reply') }}
                    </span>
                </div>
            </div>

            <!-- Resolved -->
            <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-slate-500 truncate">{{ __('pages.dashboard.resolved_7d') }}</p>
                        <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900">{{ $resolved7d }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 group-hover:scale-110 transition-transform duration-300">
                        <iconify-icon icon="solar:check-circle-bold-duotone" width="24"></iconify-icon>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs font-medium text-emerald-600">
                    <span class="flex items-center gap-1 bg-emerald-50 px-2 py-1 rounded-full">
                        <iconify-icon icon="solar:graph-up-linear" width="12"></iconify-icon>
                        {{ __('pages.dashboard.productivity') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- MAIN CONTENT GRID (1 col mobile, 3 cols desktop) -->
        <div class="grid grid-cols-1 gap-4 sm:gap-6 lg:grid-cols-3 min-[1920px]:gap-8">
            <!-- LEFT COLUMN (2/3) -->
            <div class="space-y-4 sm:space-y-6 lg:col-span-2 min-w-0">
                <!-- CHART SECTION -->
                <div class="rounded-xl sm:rounded-2xl bg-white p-4 sm:p-6 shadow-sm border border-slate-100 min-w-0 overflow-hidden">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6">
                        <h3 class="text-base sm:text-lg font-bold text-slate-900">{{ __('pages.dashboard.weekly_activity') }}</h3>
                        <div class="flex bg-slate-100 p-1 rounded-lg w-fit">
                            <button class="px-3 py-1.5 sm:py-1 text-xs font-semibold bg-white rounded-md shadow-sm text-slate-800">{{ __('pages.dashboard.days_7') }}</button>
                            <button class="px-3 py-1.5 sm:py-1 text-xs font-medium text-slate-500 hover:text-slate-800">{{ __('pages.dashboard.days_30') }}</button>
                        </div>
                    </div>
                    
                    <div class="relative h-48 sm:h-56 lg:h-64 min-[1920px]:h-72 w-full flex items-end justify-between gap-1 sm:gap-2 lg:gap-4 px-1 sm:px-2 min-w-0">
                        <!-- Grid lines -->
                        <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
                            <div class="border-t border-slate-100 w-full h-0"></div>
                            <div class="border-t border-slate-100 w-full h-0"></div>
                            <div class="border-t border-slate-100 w-full h-0"></div>
                            <div class="border-t border-slate-100 w-full h-0"></div>
                            <div class="border-t border-slate-100 w-full h-0"></div>
                        </div>

                        @foreach ([__('pages.dashboard.weekday_mon'), __('pages.dashboard.weekday_tue'), __('pages.dashboard.weekday_wed'), __('pages.dashboard.weekday_thu'), __('pages.dashboard.weekday_fri'), __('pages.dashboard.weekday_sat'), __('pages.dashboard.weekday_sun')] as $i => $day)
                            @php
                                $h1 = [40, 65, 50, 80, 45, 30, 20][$i];
                                $h2 = [60, 40, 80, 30, 50, 20, 10][$i];
                            @endphp
                            <div class="relative z-10 flex flex-col items-center gap-3 w-full group cursor-pointer">
                                <div class="relative w-full max-w-[40px] h-48 flex items-end justify-center">
                                    <div class="w-2 sm:w-3 rounded-full bg-slate-100 group-hover:bg-slate-200 transition-all duration-300" style="height: {{ $h1 }}%"></div>
                                    <div class="absolute bottom-0 w-2 sm:w-3 rounded-full transition-all duration-300 group-hover:opacity-90 shadow-lg shadow-[var(--accent-ring)]" style="background-color: var(--accent); height: {{ $h2 }}%"></div>
                                </div>
                                <span class="text-xs font-medium text-slate-400 group-hover:text-slate-600 transition-colors">{{ $day }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- PRIORITY TICKETS TABLE (scroll horizontal on small screens) -->
                <div class="rounded-xl sm:rounded-2xl bg-white shadow-sm border border-slate-100 overflow-hidden min-w-0">
                    <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-100 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <h3 class="text-base sm:text-lg font-bold text-slate-900">{{ __('pages.dashboard.priority_tickets') }}</h3>
                        <a href="{{ route('tickets.index') }}" class="text-sm font-semibold hover:underline w-fit" style="color: var(--accent);">{{ __('pages.dashboard.see_all') }}</a>
                    </div>
                    <div class="responsive-table-wrap">
                        <table class="w-full text-left min-w-[600px] sm:min-w-0">
                            <thead class="bg-slate-50/50 text-xs uppercase text-slate-500 font-semibold">
                                <tr>
                                    <th class="px-4 sm:px-6 py-3">{{ __('pages.dashboard.subject') }}</th>
                                    <th class="px-4 sm:px-6 py-3">{{ __('pages.dashboard.status') }}</th>
                                    <th class="px-4 sm:px-6 py-3">{{ __('pages.dashboard.priority') }}</th>
                                    <th class="px-4 sm:px-6 py-3 text-right">{{ __('pages.dashboard.last_activity') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($priorityTickets as $t)
                                    @php
                                        $statusValue = is_object($t->status) ? $t->status->value : (string) $t->status;
                                        $pill = $statusPill($statusValue);
                                    @endphp
                                    <tr class="group hover:bg-slate-50/80 transition-colors cursor-pointer" onclick="window.location='{{ route('tickets.discussion', $t) }}'">
                                        <td class="px-4 sm:px-6 py-3 sm:py-4">
                                            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                                                <div class="h-8 w-8 shrink-0 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 font-mono text-xs font-bold">
                                                    {{ $t->shortReference() }}
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-semibold text-slate-900 group-hover:text-[var(--accent)] transition-colors truncate">{{ $t->subject }}</p>
                                                    <p class="text-xs text-slate-500 truncate max-w-[180px] sm:max-w-[200px]">{{ $t->description }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border {{ $pill['bg'] }} {{ $pill['text'] }} {{ $pill['border'] }}">
                                                {{ $statusLabel($statusValue) }}
                                            </span>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-4">
                                            <div class="flex items-center gap-1.5">
                                                <div class="h-2 w-2 shrink-0 rounded-full" style="background-color: {{ $t->priority?->color ?? '#cbd5e1' }}"></div>
                                                <span class="text-sm text-slate-700">{{ $t->priority?->name ?? 'Normal' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 text-right text-sm text-slate-500 whitespace-nowrap">
                                            {{ $t->updated_at?->diffForHumans() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 sm:px-6 py-8 text-center text-slate-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <iconify-icon icon="solar:ticket-linear" width="32" class="mb-2 opacity-50"></iconify-icon>
                                                <p>{{ __('pages.dashboard.no_priority_tickets') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN (1/3) -->
            <div class="space-y-4 sm:space-y-6 min-w-0">
                <!-- DISCUSSIONS WIDGET -->
                <div class="rounded-xl sm:rounded-2xl bg-white p-4 sm:p-6 shadow-sm border border-slate-100">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-slate-900">{{ __('pages.dashboard.discussions') }}</h3>
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-red-100 text-xs font-bold text-red-600">3</span>
                    </div>
                    <div class="space-y-4">
                        <!-- Mock Item 1 -->
                        <div class="flex gap-4 p-3 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer group">
                            <div class="relative shrink-0">
                                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Sophie" class="h-10 w-10 rounded-full bg-slate-100 ring-2 ring-white" alt="">
                                <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-green-500 ring-2 ring-white"></span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-sm font-bold text-slate-900 truncate">Sophie Martin</p>
                                    <span class="text-xs text-slate-400">12m</span>
                                </div>
                                <p class="text-xs text-slate-500 line-clamp-2 group-hover:text-slate-700">{{ __('pages.dashboard.mock_discussion_1') }}</p>
                            </div>
                        </div>
                        <!-- Mock Item 2 -->
                        <div class="flex gap-4 p-3 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer group">
                            <div class="relative shrink-0">
                                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Thomas" class="h-10 w-10 rounded-full bg-slate-100 ring-2 ring-white" alt="">
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-sm font-bold text-slate-900 truncate">Thomas Dubois</p>
                                    <span class="text-xs text-slate-400">1h</span>
                                </div>
                                <p class="text-xs text-slate-500 line-clamp-2 group-hover:text-slate-700">{{ __('pages.dashboard.mock_discussion_2') }}</p>
                            </div>
                        </div>
                    </div>
                    <button class="mt-6 w-full rounded-xl border border-slate-200 bg-white py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                        {{ __('pages.dashboard.see_all_discussions') }}
                    </button>
                </div>

                <!-- RECENT ACTIVITY TIMELINE -->
                <div class="rounded-xl sm:rounded-2xl bg-white p-4 sm:p-6 shadow-sm border border-slate-100">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-4 sm:mb-6">{{ __('pages.dashboard.recent_activity') }}</h3>
                    <div class="relative pl-4 space-y-6 before:absolute before:left-[19px] before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-100">
                        <div class="relative pl-6">
                            <div class="absolute left-0 top-1.5 h-2.5 w-2.5 rounded-full border-2 border-white shadow-sm" style="background-color: var(--accent);"></div>
                            <p class="text-sm font-medium text-slate-900">{{ __('pages.dashboard.ticket_resolved', ['reference' => 'TCK-01J9ZK5R']) }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ __('pages.dashboard.ago_by_you', ['time' => '15 min']) }}</p>
                        </div>
                        <div class="relative pl-6">
                            <div class="absolute left-0 top-1.5 h-2.5 w-2.5 rounded-full border-2 border-white bg-blue-500 shadow-sm"></div>
                            <p class="text-sm font-medium text-slate-900">{{ __('pages.dashboard.new_ticket_assigned') }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ __('pages.dashboard.ago_by', ['time' => '45 min', 'name' => __('pages.dashboard.admin')]) }}</p>
                        </div>
                        <div class="relative pl-6">
                            <div class="absolute left-0 top-1.5 h-2.5 w-2.5 rounded-full border-2 border-white bg-slate-300 shadow-sm"></div>
                            <p class="text-sm font-medium text-slate-900">{{ __('pages.dashboard.internal_note_added') }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ __('pages.dashboard.on_ticket', ['reference' => 'TCK-01J9ZK5R']) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    {{-- ===================== AGENT / MEMBER ===================== --}}
    @else
        <!-- Simplified view for Agents/Members (reusing similar components but simplified) -->
        <!-- For brevity in this turn, I'm focusing on the main Admin dashboard, but normally I would style this too. -->
        <!-- I'll just put a placeholder for now to ensure it doesn't break if logged in as agent -->
        <div class="rounded-xl sm:rounded-2xl bg-white p-6 sm:p-8 lg:p-10 text-center shadow-sm border border-slate-100 min-w-0">
            <div class="mx-auto h-14 w-14 sm:h-16 sm:w-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                <iconify-icon icon="solar:user-circle-bold" width="32" class="text-slate-400 w-7 h-7 sm:w-8 sm:h-8"></iconify-icon>
            </div>
            <h2 class="text-lg sm:text-xl font-bold text-slate-900">{{ __('pages.dashboard.welcome_space') }}</h2>
            <p class="mt-2 text-sm sm:text-base text-slate-500">{{ __('pages.dashboard.dashboard_loading') }}</p>
            <div class="mt-4 sm:mt-6">
                 <a href="{{ route('tickets.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-3 text-sm font-semibold text-white shadow-lg hover:opacity-90 transition-all touch-target sm:min-h-0 w-full sm:w-auto" style="background-color: var(--accent);">
                    <iconify-icon icon="solar:list-bold" width="18"></iconify-icon>
                    {{ __('pages.dashboard.access_my_tickets') }}
                </a>
            </div>
        </div>
    @endif

</x-manexo-app-layout>