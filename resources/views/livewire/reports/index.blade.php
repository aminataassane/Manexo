@php
    $max7d = max(1, max(array_map(fn ($x) => (int) ($x['count'] ?? 0), $createdLast7d ?? [])) ?: 1);
    $max30d = max(1, max(array_map(fn ($x) => (int) ($x['count'] ?? 0), $createdLast30d ?? [])) ?: 1);

    $pill = function (string $status): array {
        return match ($status) {
            'open' => ['label' => 'Ouvert', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200'],
            'in_progress' => ['label' => 'En cours', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200'],
            'pending' => ['label' => 'En attente', 'bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'border' => 'border-violet-200'],
            'resolved' => ['label' => 'Résolu', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200'],
            'closed' => ['label' => 'Fermé', 'bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200'],
            default => ['label' => ucfirst(str_replace('_', ' ', $status)), 'bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200'],
        };
    };
@endphp

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-[#111827] tracking-tight">Rapports</h1>
            <p class="mt-1 text-sm text-[#6B7280]">Vue globale sur l’activité et la performance.</p>
        </div>

        <a
            href="{{ route('tickets.index') }}"
            class="h-10 px-4 bg-white border border-[#E5E7EB] text-[#111827] text-[13px] font-medium rounded-md shadow-sm hover:bg-[#F9FAFB] transition flex items-center gap-2"
        >
            <iconify-icon icon="solar:ticket-linear" width="16"></iconify-icon>
            Voir les tickets
        </a>
    </div>

    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white p-4 rounded-lg border border-[#E5E7EB] shadow-[0_2px_8px_rgba(0,0,0,0.02)] flex flex-col justify-between transition-colors group cursor-pointer hover:border-gray-200">
            <div class="flex justify-between items-start">
                <span class="text-[13px] font-medium text-[#6B7280]">Total</span>
                <div class="w-6 h-6 rounded bg-gray-50 text-gray-700 flex items-center justify-center group-hover:bg-gray-100 transition-colors">
                    <iconify-icon icon="solar:layers-linear" width="14"></iconify-icon>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-semibold text-[#111827] tracking-tight">{{ $kpis['total'] ?? 0 }}</span>
                <span class="text-[11px] text-[#6B7280] ml-1">tickets</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg border border-[#E5E7EB] shadow-[0_2px_8px_rgba(0,0,0,0.02)] flex flex-col justify-between transition-colors group cursor-pointer hover:border-blue-200">
            <div class="flex justify-between items-start">
                <span class="text-[13px] font-medium text-[#6B7280]">Ouverts</span>
                <div class="w-6 h-6 rounded bg-blue-50 text-blue-700 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                    <iconify-icon icon="solar:bolt-circle-linear" width="14"></iconify-icon>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-semibold text-[#111827] tracking-tight">{{ $kpis['open'] ?? 0 }}</span>
                <span class="text-[11px] text-[#6B7280] ml-1">à traiter</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg border border-[#E5E7EB] shadow-[0_2px_8px_rgba(0,0,0,0.02)] flex flex-col justify-between transition-colors group cursor-pointer hover:border-emerald-200">
            <div class="flex justify-between items-start">
                <span class="text-[13px] font-medium text-[#6B7280]">Résolus (7j)</span>
                <div class="w-6 h-6 rounded bg-emerald-50 text-emerald-700 flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                    <iconify-icon icon="solar:check-circle-linear" width="14"></iconify-icon>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-semibold text-[#111827] tracking-tight">{{ $kpis['done_7d'] ?? 0 }}</span>
                <span class="text-[11px] text-[#6B7280] ml-1">sur 7 jours</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg border border-[#E5E7EB] shadow-[0_2px_8px_rgba(0,0,0,0.02)] flex flex-col justify-between transition-colors group cursor-pointer hover:border-amber-200">
            <div class="flex justify-between items-start">
                <span class="text-[13px] font-medium text-[#6B7280]">Âge moyen (ouvert)</span>
                <div class="w-6 h-6 rounded bg-amber-50 text-amber-700 flex items-center justify-center group-hover:bg-amber-100 transition-colors">
                    <iconify-icon icon="solar:clock-circle-linear" width="14"></iconify-icon>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-semibold text-[#111827] tracking-tight">{{ $kpis['avg_open_age_hours'] ?? 0 }}</span>
                <span class="text-[11px] text-[#6B7280] ml-1">heures</span>
            </div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="lg:col-span-2 rounded-xl border border-[#E5E7EB] bg-white shadow-sm overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-[#E5E7EB] flex items-center justify-between">
                <div>
                    <h2 class="text-[13px] font-semibold text-[#111827]">Créations (7 jours)</h2>
                    <p class="mt-1 text-[12px] text-[#6B7280]">Nombre de tickets créés par jour.</p>
                </div>
            </div>

            <div class="p-4 sm:p-6">
                @if (empty($createdLast7d))
                    <div class="text-[13px] text-[#6B7280]">Aucune donnée sur les 7 derniers jours.</div>
                @else
                    <div class="flex items-end gap-2 h-32">
                        @foreach ($createdLast7d as $p)
                            @php($h = max(6, (int) round(((int) $p['count'] / $max7d) * 100)))
                            <div class="flex-1 group">
                                <div class="w-full rounded-md bg-[color:var(--accent-soft)] overflow-hidden border border-[color:var(--accent-soft)]">
                                    <div class="w-full" style="height: {{ $h }}px; background: var(--accent);"></div>
                                </div>
                                <div class="mt-2 text-[10px] text-[#6B7280] flex justify-between">
                                    <span>{{ \Illuminate\Support\Carbon::parse($p['date'])->format('d/m') }}</span>
                                    <span class="text-[#111827] font-medium">{{ (int) $p['count'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="rounded-xl border border-[#E5E7EB] bg-white shadow-sm overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-[#E5E7EB]">
                <h2 class="text-[13px] font-semibold text-[#111827]">Répartition des statuts</h2>
                <p class="mt-1 text-[12px] text-[#6B7280]">Vue rapide des tickets par statut.</p>
            </div>

            <div class="p-4 sm:p-6 space-y-2">
                @forelse ($byStatus as $row)
                    @php($b = $pill((string) $row['status']))
                    <div class="flex items-center justify-between gap-3">
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium border {{ $b['bg'] }} {{ $b['text'] }} {{ $b['border'] }}">
                            {{ $b['label'] }}
                        </span>
                        <span class="text-[12px] text-[#111827] font-semibold">{{ (int) $row['count'] }}</span>
                    </div>
                @empty
                    <div class="text-[13px] text-[#6B7280]">Aucun ticket.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="rounded-xl border border-[#E5E7EB] bg-white shadow-sm overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-[#E5E7EB]">
                <h2 class="text-[13px] font-semibold text-[#111827]">Top catégories</h2>
                <p class="mt-1 text-[12px] text-[#6B7280]">Catégories les plus fréquentes.</p>
            </div>
            <div class="p-4 sm:p-6 space-y-2">
                @forelse ($topCategories as $c)
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-[12px] text-[#111827] font-medium truncate">{{ $c['name'] }}</span>
                        <span class="text-[12px] text-[#6B7280] font-semibold">{{ (int) $c['count'] }}</span>
                    </div>
                @empty
                    <div class="text-[13px] text-[#6B7280]">Aucune donnée.</div>
                @endforelse
            </div>
        </div>

        <div class="rounded-xl border border-[#E5E7EB] bg-white shadow-sm overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-[#E5E7EB]">
                <h2 class="text-[13px] font-semibold text-[#111827]">Top assignés</h2>
                <p class="mt-1 text-[12px] text-[#6B7280]">Agents avec le plus de tickets assignés.</p>
            </div>
            <div class="p-4 sm:p-6 space-y-2">
                @forelse ($topAssignees as $a)
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-[12px] text-[#111827] font-medium truncate">{{ $a['name'] }}</span>
                        <span class="text-[12px] text-[#6B7280] font-semibold">{{ (int) $a['count'] }}</span>
                    </div>
                @empty
                    <div class="text-[13px] text-[#6B7280]">Aucune donnée.</div>
                @endforelse
            </div>
        </div>

        <div class="rounded-xl border border-[#E5E7EB] bg-white shadow-sm overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-[#E5E7EB]">
                <h2 class="text-[13px] font-semibold text-[#111827]">Équipe</h2>
                <p class="mt-1 text-[12px] text-[#6B7280]">Répartition des rôles.</p>
            </div>
            <div class="p-4 sm:p-6 space-y-2">
                <div class="flex items-center justify-between"><span class="text-[12px] text-[#6B7280]">Owners</span><span class="text-[12px] font-semibold text-[#111827]">{{ $team['owners'] ?? 0 }}</span></div>
                <div class="flex items-center justify-between"><span class="text-[12px] text-[#6B7280]">Admins</span><span class="text-[12px] font-semibold text-[#111827]">{{ $team['admins'] ?? 0 }}</span></div>
                <div class="flex items-center justify-between"><span class="text-[12px] text-[#6B7280]">Agents</span><span class="text-[12px] font-semibold text-[#111827]">{{ $team['agents'] ?? 0 }}</span></div>
                <div class="flex items-center justify-between"><span class="text-[12px] text-[#6B7280]">Members</span><span class="text-[12px] font-semibold text-[#111827]">{{ $team['members'] ?? 0 }}</span></div>
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-xl border border-[#E5E7EB] bg-white shadow-sm overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-[#E5E7EB]">
            <h2 class="text-[13px] font-semibold text-[#111827]">Créations (30 jours)</h2>
            <p class="mt-1 text-[12px] text-[#6B7280]">Tendance sur les 30 derniers jours.</p>
        </div>
        <div class="p-4 sm:p-6">
            @if (empty($createdLast30d))
                <div class="text-[13px] text-[#6B7280]">Aucune donnée sur les 30 derniers jours.</div>
            @else
                <div class="grid grid-cols-10 sm:grid-cols-15 lg:grid-cols-30 gap-1 items-end">
                    @foreach ($createdLast30d as $p)
                        @php($h = max(4, (int) round(((int) $p['count'] / $max30d) * 44)))
                        <div class="rounded bg-[color:var(--accent)]/20 overflow-hidden border border-[color:var(--accent)]/15" title="{{ $p['date'] }}: {{ (int) $p['count'] }}">
                            <div style="height: {{ $h }}px; background: var(--accent);"></div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

