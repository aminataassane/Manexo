{{-- resources/views/dashboard.blade.php --}}
<x-manexo-app-layout>
    <x-slot name="title">Tableau de bord</x-slot>

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
            return match ($status) {
                'open' => 'Ouvert',
                'in_progress' => 'En cours',
                'pending' => 'En attente',
                'resolved' => 'Résolu',
                'closed' => 'Fermé',
                default => ucfirst(str_replace('_', ' ', $status)),
            };
        };

        $statusPill = function (string $status): array {
            return match ($status) {
                'open' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-100'],
                'in_progress' => ['bg' => 'bg-[color:var(--accent-soft)]', 'text' => 'text-[color:var(--accent)]', 'border' => 'border-[color:var(--accent-soft)]'],
                'pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-100'],
                'resolved', 'closed' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-100'],
                default => ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-200'],
            };
        };
    @endphp

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-[#111827] tracking-tight">Tableau de bord</h1>
            <p class="text-sm text-[#6B7280] mt-1">
                Vue d'ensemble de l'activité de support pour
                <span class="font-medium text-[#111827]">{{ $orgName }}</span>.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 w-full sm:w-auto">
            <a href="{{ route('tickets.index') }}"
               class="h-9 sm:h-8 px-3 bg-white border border-[#E5E7EB] text-[#111827] text-[13px] font-medium rounded-md shadow-sm hover:bg-[#F9FAFB] transition-all flex items-center justify-center sm:justify-start gap-2 w-full sm:w-auto">
                <iconify-icon icon="solar:list-linear" width="16"></iconify-icon>
                Voir tous les tickets
            </a>

            <a href="{{ route('tickets.create') }}"
               class="h-9 sm:h-8 px-3 text-white text-[13px] font-medium rounded-md shadow-sm transition-colors flex items-center justify-center sm:justify-start gap-2 w-full sm:w-auto bg-[color:var(--accent)] hover:bg-[color:color-mix(in_srgb,var(--accent)_85%,black)]">
                <iconify-icon icon="solar:add-circle-linear" width="16"></iconify-icon>
                Créer un ticket
            </a>
        </div>
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

            // Postgres-compatible: tri par niveau de priorité (ticket_priorities.level)
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

        <!-- KPI CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white p-4 rounded-lg border border-[#E5E7EB] shadow-[0_2px_8px_rgba(0,0,0,0.02)] flex flex-col justify-between hover:border-red-200 transition-colors group cursor-pointer">
                <div class="flex justify-between items-start">
                    <span class="text-[13px] font-medium text-[#6B7280]">Ouverts</span>
                    <div class="w-6 h-6 rounded bg-red-50 text-red-600 flex items-center justify-center group-hover:bg-red-100 transition-colors">
                        <iconify-icon icon="solar:danger-circle-linear" width="14"></iconify-icon>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl font-semibold text-[#111827] tracking-tight">{{ $open }}</span>
                    <span class="text-[11px] text-[#6B7280] ml-1">à traiter</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg border border-[#E5E7EB] shadow-[0_2px_8px_rgba(0,0,0,0.02)] flex flex-col justify-between transition-colors group cursor-pointer hover:border-[color:var(--accent-soft-2)]">
                <div class="flex justify-between items-start">
                    <span class="text-[13px] font-medium text-[#6B7280]">En cours</span>
                    <div class="w-6 h-6 rounded flex items-center justify-center" style="background: var(--accent-soft); color: var(--accent);">
                        <iconify-icon icon="solar:clock-circle-linear" width="14"></iconify-icon>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl font-semibold text-[#111827] tracking-tight">{{ $inProgress }}</span>
                    <span class="text-[11px] text-[#6B7280] ml-1">actifs</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg border border-[#E5E7EB] shadow-[0_2px_8px_rgba(0,0,0,0.02)] flex flex-col justify-between hover:border-amber-200 transition-colors group cursor-pointer">
                <div class="flex justify-between items-start">
                    <span class="text-[13px] font-medium text-[#6B7280]">En attente</span>
                    <div class="w-6 h-6 rounded bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-100 transition-colors">
                        <iconify-icon icon="solar:pause-circle-linear" width="14"></iconify-icon>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl font-semibold text-[#111827] tracking-tight">{{ $pending }}</span>
                    <span class="text-[11px] text-[#6B7280] ml-1">réponse client</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg border border-[#E5E7EB] shadow-[0_2px_8px_rgba(0,0,0,0.02)] flex flex-col justify-between hover:border-[color:var(--accent)] transition-colors group cursor-pointer">
                <div class="flex justify-between items-start">
                    <span class="text-[13px] font-medium text-[#6B7280]">Résolus (7j)</span>
                    <div class="w-6 h-6 rounded flex items-center justify-center" style="background: var(--accent-soft); color: var(--accent);">
                        <iconify-icon icon="solar:check-circle-linear" width="14"></iconify-icon>
                    </div>
                </div>
                <div class="mt-3 flex items-end justify-between">
                    <div>
                        <span class="text-2xl font-semibold text-[#111827] tracking-tight">{{ $resolved7d }}</span>
                        <span class="text-[11px] font-medium ml-1" style="color: var(--accent);">—</span>
                    </div>
                    <div class="text-[10px] text-[#6B7280] font-medium">Avg. —</div>
                </div>
            </div>
        </div>

        <!-- MAIN SPLIT LAYOUT -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
            <!-- LEFT -->
            <div class="lg:col-span-2 space-y-6">
                <!-- GRAPH -->
                <div class="bg-white rounded-lg border border-[#E5E7EB] p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-sm font-semibold text-[#111827]">Activité des tickets</h3>
                        <div class="flex bg-[#F9FAFB] p-0.5 rounded-md border border-[#E5E7EB]">
                            <button class="px-2 py-0.5 text-[11px] font-medium bg-white rounded shadow-sm text-[#111827]">7 jours</button>
                            <button class="px-2 py-0.5 text-[11px] font-medium text-[#6B7280] hover:text-[#111827]">30 jours</button>
                        </div>
                    </div>

                    <div class="flex gap-2 w-full h-32 pr-2 pl-2 items-end justify-between">
                        @foreach (['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'] as $i => $day)
                            @php
                                $outer = [40,65,50,80,45,30,20][$i];
                                $inner = [60,40,80,30,50,20,10][$i];
                            @endphp
                            <div class="flex flex-col items-center gap-2 w-full group">
                                <div class="w-full max-w-[24px] bg-[#F9FAFB] rounded-sm bar relative" style="height: {{ $outer }}%;">
                                    <div class="absolute bottom-0 w-full rounded-sm" style="background: var(--accent); height: {{ $inner }}%;"></div>
                                </div>
                                <span class="text-[10px] text-[#6B7280]">{{ $day }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- PRIORITY TICKETS -->
                <div class="bg-white rounded-lg border border-[#E5E7EB] shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-[#E5E7EB] flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-[#111827]">Tickets prioritaires</h3>
                        <a class="text-xs font-medium hover:opacity-80" style="color: var(--accent);" href="{{ route('tickets.index') }}">Voir tout</a>
                    </div>

                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full min-w-[640px] text-left border-collapse">
                            <thead class="bg-[#F9FAFB] text-[11px] uppercase text-[#6B7280] font-medium tracking-wider whitespace-nowrap">
                            <tr>
                                <th class="px-4 py-2 font-medium w-24">ID</th>
                                <th class="px-4 py-2 font-medium">Sujet</th>
                                <th class="px-4 py-2 font-medium w-28">Statut</th>
                                <th class="px-4 py-2 font-medium w-28 text-right">Activité</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E5E7EB]">
                            @forelse ($priorityTickets as $t)
                                @php
                                    $statusValue = is_object($t->status) ? $t->status->value : (string) $t->status;
                                    $pill = $statusPill($statusValue);
                                @endphp
                                <tr class="group hover:bg-[#F9FAFB] cursor-pointer transition-colors">
                                    <td class="px-4 py-3 text-xs font-mono text-[#6B7280] group-hover:text-[#111827]">#{{ $t->id }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-medium text-[#111827] truncate max-w-[260px] sm:max-w-xs">{{ $t->subject }}</span>
                                            @if (($t->priority?->level ?? 0) >= 80)
                                                <iconify-icon icon="solar:fire-bold" class="text-red-500" width="12"></iconify-icon>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium border {{ $pill['bg'] }} {{ $pill['text'] }} {{ $pill['border'] }}">
                                            {{ $statusLabel($statusValue) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right text-xs text-[#6B7280]">{{ $t->updated_at?->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-6 text-center text-sm text-[#6B7280]">Aucun ticket.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="space-y-6">
                <!-- DISCUSSIONS (mock) -->
                <div class="bg-white border-[#E5E7EB] border rounded-lg p-4 shadow-sm">
                    <div class="flex mb-4 items-center justify-between">
                        <h3 class="text-sm font-semibold text-[#111827] flex items-center gap-2">
                            Discussions
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                        </h3>
                        <iconify-icon icon="solar:chat-line-linear" class="text-[#6B7280]"></iconify-icon>
                    </div>

                    <div class="space-y-4">
                        <div class="group flex gap-3 cursor-pointer p-2 -mx-2 hover:bg-[#F9FAFB] rounded-md transition-colors">
                            <div class="relative">
                                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Sophie" class="w-8 h-8 rounded-full bg-[#F9FAFB] border border-[#E5E7EB]" alt="">
                                <div class="absolute -bottom-0.5 -right-0.5 bg-blue-500 rounded-full p-[2px] border border-white">
                                    <iconify-icon icon="solar:chat-round-linear" class="text-white text-[8px]"></iconify-icon>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-baseline mb-0.5">
                                    <span class="text-xs font-medium text-[#111827]">Sophie (Client)</span>
                                    <span class="text-[10px] text-[#6B7280]">10m</span>
                                </div>
                                <p class="text-[11px] text-[#6B7280] truncate">Merci, ça fonctionne maintenant !</p>
                                <p class="text-[10px] text-[#6B7280] mt-0.5">Ticket #2940</p>
                            </div>
                        </div>
                    </div>

                    <button class="w-full mt-3 py-2 text-xs font-medium text-[#6B7280] hover:text-[#111827] border border-[#E5E7EB] hover:bg-[#F9FAFB] rounded transition-all">
                        Voir toutes les discussions
                    </button>
                </div>

                <!-- RECENT ACTIVITY (mock) -->
                <div class="bg-white rounded-lg border border-[#E5E7EB] shadow-sm p-4">
                    <h3 class="text-sm font-semibold text-[#111827] mb-4">Activité récente</h3>
                    <div class="relative pl-4 border-l border-[#E5E7EB] space-y-6">
                        <div class="relative">
                            <div class="absolute -left-[21px] top-0.5 w-2.5 h-2.5 rounded-full ring-4 ring-white" style="background: var(--accent);"></div>
                            <p class="text-xs text-[#111827]">Ticket <span class="font-medium">#—</span> résolu</p>
                            <p class="text-[10px] text-[#6B7280] mt-0.5">Il y a 5 min</p>
                        </div>
                        <div class="relative">
                            <div class="absolute -left-[21px] top-0.5 w-2.5 h-2.5 rounded-full bg-[#E5E7EB] ring-4 ring-white"></div>
                            <p class="text-xs text-[#111827]">Nouveau ticket créé</p>
                            <p class="text-[10px] text-[#6B7280] mt-0.5">Il y a 12 min</p>
                        </div>
                        <div class="relative">
                            <div class="absolute -left-[21px] top-0.5 w-2.5 h-2.5 rounded-full ring-4 ring-white" style="background: var(--accent-soft-2);"></div>
                            <p class="text-xs text-[#111827]">Assignation modifiée</p>
                            <p class="text-[10px] text-[#6B7280] mt-0.5">Il y a 1h</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    {{-- ===================== AGENT ===================== --}}
    @elseif ($role === 'agent')
        @php
            $open = \App\Models\Ticket::query()->where('organization_id', $orgId)->where('status', 'open')->count();

            $assignedToMe = \App\Models\Ticket::query()
                ->where('organization_id', $orgId)
                ->where('assigned_to', $user?->id)
                ->whereIn('status', ['open', 'pending', 'in_progress'])
                ->count();

            $maxLevel = \App\Models\TicketPriority::query()->where('organization_id', $orgId)->max('level') ?? 0;

            $urgent = \App\Models\Ticket::query()
                ->where('organization_id', $orgId)
                ->whereIn('status', ['open', 'pending', 'in_progress'])
                ->whereHas('priority', fn ($q) => $q->where('level', '>=', max(0, (int) $maxLevel)))
                ->count();

            $waitingClient = \App\Models\Ticket::query()->where('organization_id', $orgId)->where('status', 'pending')->count();

            $ticketsToTreat = \App\Models\Ticket::query()
                ->where('organization_id', $orgId)
                ->whereIn('status', ['open', 'pending', 'in_progress'])
                ->with(['priority'])
                ->orderByDesc('updated_at')
                ->limit(5)
                ->get();
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white p-4 rounded-lg border border-[#E5E7EB] shadow-[0_2px_8px_rgba(0,0,0,0.02)]">
                <div class="text-[13px] font-medium text-[#6B7280]">Tickets ouverts</div>
                <div class="mt-2 text-2xl font-semibold text-[#111827] tracking-tight">{{ $open }}</div>
            </div>
            <div class="bg-white p-4 rounded-lg border border-[#E5E7EB] shadow-[0_2px_8px_rgba(0,0,0,0.02)]">
                <div class="text-[13px] font-medium text-[#6B7280]">Assignés à moi</div>
                <div class="mt-2 text-2xl font-semibold text-[#111827] tracking-tight">{{ $assignedToMe }}</div>
            </div>
            <div class="bg-white p-4 rounded-lg border border-[#E5E7EB] shadow-[0_2px_8px_rgba(0,0,0,0.02)]">
                <div class="text-[13px] font-medium text-[#6B7280]">Urgents</div>
                <div class="mt-2 text-2xl font-semibold text-[#111827] tracking-tight">{{ $urgent }}</div>
            </div>
            <div class="bg-white p-4 rounded-lg border border-[#E5E7EB] shadow-[0_2px_8px_rgba(0,0,0,0.02)]">
                <div class="text-[13px] font-medium text-[#6B7280]">En attente client</div>
                <div class="mt-2 text-2xl font-semibold text-[#111827] tracking-tight">{{ $waitingClient }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
            <div class="lg:col-span-2 bg-white rounded-lg border border-[#E5E7EB] shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E5E7EB] flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-[#111827]">Tickets à traiter</h3>
                    <a class="text-xs font-medium hover:opacity-80" style="color: var(--accent);" href="{{ route('tickets.index') }}">Voir tout</a>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[640px] text-left border-collapse">
                        <thead class="bg-[#F9FAFB] text-[11px] uppercase text-[#6B7280] font-medium tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="px-4 py-2 font-medium w-24">ID</th>
                            <th class="px-4 py-2 font-medium">Sujet</th>
                            <th class="px-4 py-2 font-medium w-28">Statut</th>
                            <th class="px-4 py-2 font-medium w-28 text-right">Activité</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E5E7EB]">
                        @forelse ($ticketsToTreat as $t)
                            @php
                                $statusValue = is_object($t->status) ? $t->status->value : (string) $t->status;
                                $pill = $statusPill($statusValue);
                            @endphp
                            <tr class="group hover:bg-[#F9FAFB] cursor-pointer transition-colors">
                                <td class="px-4 py-3 text-xs font-mono text-[#6B7280] group-hover:text-[#111827]">#{{ $t->id }}</td>
                                <td class="px-4 py-3">
                                    <span class="text-xs font-medium text-[#111827] truncate max-w-[320px]">{{ $t->subject }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium border {{ $pill['bg'] }} {{ $pill['text'] }} {{ $pill['border'] }}">
                                        {{ $statusLabel($statusValue) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-xs text-[#6B7280]">{{ $t->updated_at?->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-sm text-[#6B7280]">Aucun ticket.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-lg border border-[#E5E7EB] shadow-sm p-4">
                    <h3 class="text-sm font-semibold text-[#111827] mb-4">Raccourcis</h3>
                    <div class="grid gap-3">
                        <a href="{{ route('tickets.index') }}"
                           class="h-9 rounded-md border border-[#E5E7EB] bg-white hover:bg-[#F9FAFB] transition-colors text-[13px] font-medium text-[#111827] flex items-center justify-between px-3">
                            Mes tickets
                            <iconify-icon icon="solar:arrow-right-linear" width="16" class="text-[#6B7280]"></iconify-icon>
                        </a>
                        <a href="{{ route('tickets.create') }}"
                           class="h-9 rounded-md text-white transition-colors text-[13px] font-medium flex items-center justify-between px-3 bg-[color:var(--accent)] hover:bg-[color:color-mix(in_srgb,var(--accent)_85%,black)]">
                            Créer un ticket
                            <iconify-icon icon="solar:add-circle-linear" width="16"></iconify-icon>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    {{-- ===================== MEMBER / CLIENT ===================== --}}
    @else
        @php
            $totalMine = \App\Models\Ticket::query()->where('organization_id', $orgId)->where('created_by', $user?->id)->count();
            $openMine = \App\Models\Ticket::query()->where('organization_id', $orgId)->where('created_by', $user?->id)->where('status', 'open')->count();
            $inProgressMine = \App\Models\Ticket::query()->where('organization_id', $orgId)->where('created_by', $user?->id)->where('status', 'in_progress')->count();
            $resolvedMine = \App\Models\Ticket::query()->where('organization_id', $orgId)->where('created_by', $user?->id)->whereIn('status', ['resolved', 'closed'])->count();

            $lastTickets = \App\Models\Ticket::query()
                ->where('organization_id', $orgId)
                ->where('created_by', $user?->id)
                ->orderByDesc('updated_at')
                ->limit(5)
                ->get();
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white p-4 rounded-lg border border-[#E5E7EB] shadow-[0_2px_8px_rgba(0,0,0,0.02)]">
                <div class="text-[13px] font-medium text-[#6B7280]">Total de mes tickets</div>
                <div class="mt-2 text-2xl font-semibold text-[#111827] tracking-tight">{{ $totalMine }}</div>
            </div>
            <div class="bg-white p-4 rounded-lg border border-[#E5E7EB] shadow-[0_2px_8px_rgba(0,0,0,0.02)]">
                <div class="text-[13px] font-medium text-[#6B7280]">Tickets ouverts</div>
                <div class="mt-2 text-2xl font-semibold text-[#111827] tracking-tight">{{ $openMine }}</div>
            </div>
            <div class="bg-white p-4 rounded-lg border border-[#E5E7EB] shadow-[0_2px_8px_rgba(0,0,0,0.02)]">
                <div class="text-[13px] font-medium text-[#6B7280]">En cours</div>
                <div class="mt-2 text-2xl font-semibold text-[#111827] tracking-tight">{{ $inProgressMine }}</div>
            </div>
            <div class="bg-white p-4 rounded-lg border border-[#E5E7EB] shadow-[0_2px_8px_rgba(0,0,0,0.02)]">
                <div class="text-[13px] font-medium text-[#6B7280]">Résolus</div>
                <div class="mt-2 text-2xl font-semibold text-[#111827] tracking-tight">{{ $resolvedMine }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
            <div class="lg:col-span-2 bg-white rounded-lg border border-[#E5E7EB] shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E5E7EB] flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-[#111827]">Mes derniers tickets</h3>
                    <a class="text-xs font-medium hover:opacity-80" style="color: var(--accent);" href="{{ route('tickets.index') }}">Voir tout</a>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[640px] text-left border-collapse">
                        <thead class="bg-[#F9FAFB] text-[11px] uppercase text-[#6B7280] font-medium tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="px-4 py-2 font-medium w-24">ID</th>
                            <th class="px-4 py-2 font-medium">Sujet</th>
                            <th class="px-4 py-2 font-medium w-28">Statut</th>
                            <th class="px-4 py-2 font-medium w-28 text-right">Maj</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E5E7EB]">
                        @forelse ($lastTickets as $t)
                            @php
                                $statusValue = is_object($t->status) ? $t->status->value : (string) $t->status;
                                $pill = $statusPill($statusValue);
                            @endphp
                            <tr class="group hover:bg-[#F9FAFB] cursor-pointer transition-colors">
                                <td class="px-4 py-3 text-xs font-mono text-[#6B7280] group-hover:text-[#111827]">#{{ $t->id }}</td>
                                <td class="px-4 py-3"><span class="text-xs font-medium text-[#111827]">{{ $t->subject }}</span></td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium border {{ $pill['bg'] }} {{ $pill['text'] }} {{ $pill['border'] }}">
                                        {{ $statusLabel($statusValue) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-xs text-[#6B7280]">{{ $t->updated_at?->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-sm text-[#6B7280]">Aucun ticket.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-lg border border-[#E5E7EB] shadow-sm p-4">
                    <h3 class="text-sm font-semibold text-[#111827] mb-4">Notifications récentes</h3>
                    <div class="space-y-3">
                        <div class="flex gap-3">
                            <span class="mt-1 w-2 h-2 rounded-full" style="background: var(--accent);"></span>
                            <div>
                                <p class="text-xs text-[#111827]">Un agent a répondu à votre ticket.</p>
                                <p class="text-[10px] text-[#6B7280] mt-0.5">—</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <span class="mt-1 w-2 h-2 rounded-full bg-emerald-500"></span>
                            <div>
                                <p class="text-xs text-[#111827]">Ticket résolu.</p>
                                <p class="text-[10px] text-[#6B7280] mt-0.5">—</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg p-4 text-white shadow-sm"
                     style="background: linear-gradient(135deg, color-mix(in srgb, var(--accent) 40%, #111827), #111827);">
                    <h3 class="text-sm font-semibold">Besoin d'aide ?</h3>
                    <p class="mt-1 text-xs text-white/80">Créez un nouveau ticket pour contacter notre équipe support.</p>
                    <a href="{{ route('tickets.create') }}"
                       class="mt-3 inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-[13px] font-semibold text-[#111827] hover:bg-[#F9FAFB] transition-colors w-full">
                        <iconify-icon icon="solar:add-circle-linear" class="mr-2" width="16"></iconify-icon>
                        Nouveau ticket
                    </a>
                </div>
            </div>
        </div>
    @endif
</x-manexo-app-layout>
