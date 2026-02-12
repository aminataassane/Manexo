@php
    $roleBadge = function (string $role): array {
        return match ($role) {
            'owner' => ['label' => 'Owner', 'bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'icon' => 'solar:crown-linear'],
            'admin' => ['label' => 'Admin', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'icon' => 'solar:shield-check-linear'],
            'agent' => ['label' => 'Agent', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => 'solar:headphones-round-sound-linear'],
            default => ['label' => 'Member', 'bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200', 'icon' => 'solar:user-linear'],
        };
    };
@endphp

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-[#111827] tracking-tight">Équipe</h1>
            <p class="mt-1 text-sm text-[#6B7280]">Gérez les membres de l'entreprise.</p>
        </div>
    </div>

    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white p-4 rounded-lg border border-[#E5E7EB] shadow-[0_2px_8px_rgba(0,0,0,0.02)] flex flex-col justify-between transition-colors group cursor-pointer hover:border-purple-200">
            <div class="flex justify-between items-start">
                <span class="text-[13px] font-medium text-[#6B7280]">Owners</span>
                <div class="w-6 h-6 rounded bg-purple-50 text-purple-700 flex items-center justify-center group-hover:bg-purple-100 transition-colors">
                    <iconify-icon icon="solar:crown-linear" width="14"></iconify-icon>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-semibold text-[#111827] tracking-tight">{{ $stats['owners'] ?? 0 }}</span>
                <span class="text-[11px] text-[#6B7280] ml-1">dans l’équipe</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg border border-[#E5E7EB] shadow-[0_2px_8px_rgba(0,0,0,0.02)] flex flex-col justify-between transition-colors group cursor-pointer hover:border-blue-200">
            <div class="flex justify-between items-start">
                <span class="text-[13px] font-medium text-[#6B7280]">Admins</span>
                <div class="w-6 h-6 rounded bg-blue-50 text-blue-700 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                    <iconify-icon icon="solar:shield-check-linear" width="14"></iconify-icon>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-semibold text-[#111827] tracking-tight">{{ $stats['admins'] ?? 0 }}</span>
                <span class="text-[11px] text-[#6B7280] ml-1">avec accès admin</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg border border-[#E5E7EB] shadow-[0_2px_8px_rgba(0,0,0,0.02)] flex flex-col justify-between transition-colors group cursor-pointer hover:border-amber-200">
            <div class="flex justify-between items-start">
                <span class="text-[13px] font-medium text-[#6B7280]">Agents</span>
                <div class="w-6 h-6 rounded bg-amber-50 text-amber-700 flex items-center justify-center group-hover:bg-amber-100 transition-colors">
                    <iconify-icon icon="solar:headphones-round-sound-linear" width="14"></iconify-icon>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-semibold text-[#111827] tracking-tight">{{ $stats['agents'] ?? 0 }}</span>
                <span class="text-[11px] text-[#6B7280] ml-1">support</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg border border-[#E5E7EB] shadow-[0_2px_8px_rgba(0,0,0,0.02)] flex flex-col justify-between transition-colors group cursor-pointer hover:border-gray-200">
            <div class="flex justify-between items-start">
                <span class="text-[13px] font-medium text-[#6B7280]">Members</span>
                <div class="w-6 h-6 rounded bg-gray-50 text-gray-700 flex items-center justify-center group-hover:bg-gray-100 transition-colors">
                    <iconify-icon icon="solar:user-linear" width="14"></iconify-icon>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-semibold text-[#111827] tracking-tight">{{ $stats['members'] ?? 0 }}</span>
                <span class="text-[11px] text-[#6B7280] ml-1">membres</span>
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-xl border border-[#E5E7EB] bg-white shadow-sm overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-[#E5E7EB]">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex-1 flex flex-col sm:flex-row gap-2">
                    <div class="relative flex-1">
                        <iconify-icon icon="solar:magnifer-linear" class="absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF]" width="16"></iconify-icon>
                        <input
                            type="text"
                            class="w-full h-10 pl-9 pr-3 rounded-md border border-[#E5E7EB] bg-white text-[13px] text-[#111827] placeholder:text-[#9CA3AF] focus:outline-none focus:border-[color:var(--accent)] focus:ring-2 focus:ring-[color:var(--accent-ring)] transition"
                            placeholder="Rechercher (nom, email)…"
                            wire:model.live="search"
                        />
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative">
                        <select wire:model.live="role" class="h-10 min-w-[180px] rounded-md border border-[#E5E7EB] bg-white text-[13px] text-[#111827] shadow-sm focus:outline-none focus:border-[color:var(--accent)] focus:ring-2 focus:ring-[color:var(--accent-ring)] transition appearance-none pr-9">
                            <option value="">Tous les rôles</option>
                            <option value="owner">Owner</option>
                            <option value="admin">Admin</option>
                            <option value="agent">Agent</option>
                            <option value="member">Member</option>
                        </select>
                        <iconify-icon icon="solar:alt-arrow-down-linear" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#6B7280] pointer-events-none" width="14"></iconify-icon>
                    </div>

                    <div class="relative">
                        <select wire:model.live="perPage" class="h-10 min-w-[120px] rounded-md border border-[#E5E7EB] bg-white text-[13px] text-[#111827] shadow-sm focus:outline-none focus:border-[color:var(--accent)] focus:ring-2 focus:ring-[color:var(--accent-ring)] transition appearance-none pr-9">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <iconify-icon icon="solar:alt-arrow-down-linear" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#6B7280] pointer-events-none" width="14"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="min-w-[980px] w-full">
                <thead class="bg-[#F9FAFB] border-b border-[#E5E7EB]">
                    <tr class="text-left text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider">
                        <th class="px-4 py-3">Membre</th>
                        <th class="px-4 py-3 w-44">Rôle</th>
                        <th class="px-4 py-3 w-56">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB]">
                    @forelse ($memberships as $m)
                        @php($b = $roleBadge($m->role))
                        <tr class="group hover:bg-[#F9FAFB] transition-colors">
                            <td class="px-4 py-3">
                                <div class="text-[13px] font-medium text-[#111827]">{{ $m->user?->name ?? '—' }}</div>
                                <div class="mt-1 text-[12px] text-[#6B7280]">{{ $m->user?->email ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium border {{ $b['bg'] }} {{ $b['text'] }} {{ $b['border'] }}">
                                    <iconify-icon icon="{{ $b['icon'] }}" width="12"></iconify-icon>
                                    {{ $b['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="relative">
                                        <select
                                            class="h-9 rounded-md border border-[#E5E7EB] bg-white text-[13px] text-[#111827] shadow-sm focus:outline-none focus:border-[color:var(--accent)] focus:ring-2 focus:ring-[color:var(--accent-ring)] transition appearance-none pr-9 pl-3"
                                            wire:change="updateRole({{ (int) $m->id }}, $event.target.value)"
                                        >
                                            <option value="owner" @selected($m->role === 'owner')>Owner</option>
                                            <option value="admin" @selected($m->role === 'admin')>Admin</option>
                                            <option value="agent" @selected($m->role === 'agent')>Agent</option>
                                            <option value="member" @selected($m->role === 'member')>Member</option>
                                        </select>
                                        <iconify-icon icon="solar:alt-arrow-down-linear" class="absolute right-2 top-1/2 -translate-y-1/2 text-[#6B7280] pointer-events-none" width="14"></iconify-icon>
                                    </div>

                                    <button
                                        type="button"
                                        class="h-9 px-3 bg-white border border-[#E5E7EB] text-[#111827] text-[13px] font-medium rounded-md shadow-sm hover:bg-[#F9FAFB] transition"
                                        wire:click="removeMember({{ (int) $m->id }})"
                                    >
                                        Retirer
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-10 text-center text-[13px] text-[#6B7280]">
                                Aucun membre.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 sm:px-6 py-4 border-t border-[#E5E7EB] bg-white">
            {{ $memberships->links() }}
        </div>
    </div>
</div>

