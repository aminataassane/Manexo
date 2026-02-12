@php
    $statusLabel = function (string $status): array {
        return match ($status) {
            'open' => [__('Ouvert'), 'solar:bolt-circle-linear'],
            'in_progress' => [__('En cours'), 'solar:clock-circle-linear'],
            'pending' => [__('En attente'), 'solar:hourglass-linear'],
            'resolved' => [__('Résolu'), 'solar:check-circle-linear'],
            'closed' => [__('Fermé'), 'solar:lock-keyhole-linear'],
            default => [ucfirst(str_replace('_', ' ', $status)), 'solar:question-circle-linear'],
        };
    };

    $statusPill = function (string $status): array {
        return match ($status) {
            'open' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200'],
            'in_progress' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200'],
            'pending' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'border' => 'border-violet-200'],
            'resolved' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200'],
            'closed' => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200'],
            default => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200'],
        };
    };

    $priorityMeta = function (?int $level): array {
        if ($level === null) {
            return ['label' => '—', 'dot' => 'bg-gray-300', 'text' => 'text-gray-600'];
        }

        return match (true) {
            $level >= 4 => ['label' => __('Critique'), 'dot' => 'bg-red-500', 'text' => 'text-red-700'],
            $level === 3 => ['label' => __('Haute'), 'dot' => 'bg-amber-500', 'text' => 'text-amber-700'],
            $level === 2 => ['label' => __('Moyenne'), 'dot' => 'bg-blue-500', 'text' => 'text-blue-700'],
            default => ['label' => __('Basse'), 'dot' => 'bg-green-500', 'text' => 'text-green-700'],
        };
    };
@endphp

<div
    class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8"
    x-data="{ open: @entangle('showCreateDrawer').live }"
>
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-[#111827] tracking-tight">{{ __('Tickets') }}</h1>
            <p class="mt-1 text-sm text-[#6B7280]">{{ __('Gérez et suivez les demandes.') }}</p>
        </div>

        <button
            type="button"
            class="h-10 px-4 text-white text-[13px] font-semibold rounded-md shadow-sm transition-colors flex items-center gap-2 bg-[color:var(--accent)] hover:bg-[color:color-mix(in_srgb,var(--accent)_85%,black)]"
            wire:click="openCreateDrawer"
        >
            <iconify-icon icon="solar:add-circle-linear" width="16"></iconify-icon>
            {{ __('Créer un ticket') }}
        </button>
    </div>

    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white p-4 rounded-lg border border-[#E5E7EB] shadow-[0_2px_8px_rgba(0,0,0,0.02)] flex flex-col justify-between transition-colors group cursor-pointer hover:border-blue-200">
            <div class="flex justify-between items-start">
                <span class="text-[13px] font-medium text-[#6B7280]">{{ __('Ouverts') }}</span>
                <div class="w-6 h-6 rounded bg-blue-50 text-blue-700 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                    <iconify-icon icon="solar:bolt-circle-linear" width="14"></iconify-icon>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-semibold text-[#111827] tracking-tight">{{ $stats['open'] ?? 0 }}</span>
                <span class="text-[11px] text-[#6B7280] ml-1">{{ __('à traiter') }}</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg border border-[#E5E7EB] shadow-[0_2px_8px_rgba(0,0,0,0.02)] flex flex-col justify-between transition-colors group cursor-pointer hover:border-amber-200">
            <div class="flex justify-between items-start">
                <span class="text-[13px] font-medium text-[#6B7280]">{{ __('En cours') }}</span>
                <div class="w-6 h-6 rounded bg-amber-50 text-amber-700 flex items-center justify-center group-hover:bg-amber-100 transition-colors">
                    <iconify-icon icon="solar:clock-circle-linear" width="14"></iconify-icon>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-semibold text-[#111827] tracking-tight">{{ $stats['in_progress'] ?? 0 }}</span>
                <span class="text-[11px] text-[#6B7280] ml-1">{{ __('actifs') }}</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg border border-[#E5E7EB] shadow-[0_2px_8px_rgba(0,0,0,0.02)] flex flex-col justify-between transition-colors group cursor-pointer hover:border-violet-200">
            <div class="flex justify-between items-start">
                <span class="text-[13px] font-medium text-[#6B7280]">{{ __('En attente') }}</span>
                <div class="w-6 h-6 rounded bg-violet-50 text-violet-700 flex items-center justify-center group-hover:bg-violet-100 transition-colors">
                    <iconify-icon icon="solar:hourglass-linear" width="14"></iconify-icon>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-semibold text-[#111827] tracking-tight">{{ $stats['pending'] ?? 0 }}</span>
                <span class="text-[11px] text-[#6B7280] ml-1">{{ __('réponse client') }}</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg border border-[#E5E7EB] shadow-[0_2px_8px_rgba(0,0,0,0.02)] flex flex-col justify-between transition-colors group cursor-pointer hover:border-emerald-200">
            <div class="flex justify-between items-start">
                <span class="text-[13px] font-medium text-[#6B7280]">{{ __('Résolus (7j)') }}</span>
                <div class="w-6 h-6 rounded bg-emerald-50 text-emerald-700 flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                    <iconify-icon icon="solar:check-circle-linear" width="14"></iconify-icon>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-semibold text-[#111827] tracking-tight">{{ $stats['resolved_7d'] ?? 0 }}</span>
                <span class="text-[11px] text-[#6B7280] ml-1">{{ __('récents') }}</span>
            </div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- LEFT: fixed ticket views -->
        <div class="lg:col-span-1">
            <div class="sticky top-20">
                <div class="rounded-xl border border-[#E5E7EB] bg-white shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-[#E5E7EB] bg-[#F9FAFB]">
                        <div class="text-[11px] uppercase tracking-wider text-[#6B7280] font-semibold">{{ __('Ticket views') }}</div>
                    </div>
                    <div class="p-2 space-y-1">
                        @php
                            $itemBase = 'w-full flex items-center justify-between gap-3 px-3 py-2 rounded-md text-[13px] font-medium transition border';
                            $badgeBase = 'min-w-[28px] h-6 px-2 rounded-md text-[12px] font-semibold flex items-center justify-center';
                            $isActive = fn (string $k) => ($viewKey ?? 'all') === $k;
                        @endphp

                        <button type="button" wire:click="setView('my')"
                            class="{{ $itemBase }} {{ $isActive('my') ? 'border-[color:var(--accent-soft)] bg-[color:var(--accent-soft)] text-[color:var(--accent)]' : 'border-transparent text-[#111827] hover:bg-[#F9FAFB]' }}">
                            <span class="flex items-center gap-2">
                                <iconify-icon icon="solar:user-linear" width="16" class="text-[#6B7280]"></iconify-icon>
                                {{ __('My Tickets') }}
                            </span>
                            <span class="{{ $badgeBase }} {{ $isActive('my') ? 'bg-white/70 text-[#111827]' : 'bg-[#F3F4F6] text-[#111827]' }}">{{ $viewCounts['my'] ?? 0 }}</span>
                        </button>

                        <button type="button" wire:click="setView('past_due')"
                            class="{{ $itemBase }} {{ $isActive('past_due') ? 'border-[color:var(--accent-soft)] bg-[color:var(--accent-soft)] text-[color:var(--accent)]' : 'border-transparent text-[#111827] hover:bg-[#F9FAFB]' }}">
                            <span class="flex items-center gap-2">
                                <iconify-icon icon="solar:calendar-search-linear" width="16" class="text-[#6B7280]"></iconify-icon>
                                {{ __('Past Due') }}
                            </span>
                            <span class="{{ $badgeBase }} {{ $isActive('past_due') ? 'bg-white/70 text-[#111827]' : 'bg-[#F3F4F6] text-[#111827]' }}">{{ $viewCounts['past_due'] ?? 0 }}</span>
                        </button>

                        <button type="button" wire:click="setView('high_priority')"
                            class="{{ $itemBase }} {{ $isActive('high_priority') ? 'border-[color:var(--accent-soft)] bg-[color:var(--accent-soft)] text-[color:var(--accent)]' : 'border-transparent text-[#111827] hover:bg-[#F9FAFB]' }}">
                            <span class="flex items-center gap-2">
                                <iconify-icon icon="solar:danger-triangle-linear" width="16" class="text-[#6B7280]"></iconify-icon>
                                {{ __('High Priority') }}
                            </span>
                            <span class="{{ $badgeBase }} {{ $isActive('high_priority') ? 'bg-white/70 text-[#111827]' : 'bg-[#F3F4F6] text-[#111827]' }}">{{ $viewCounts['high_priority'] ?? 0 }}</span>
                        </button>

                        <button type="button" wire:click="setView('unassigned')"
                            class="{{ $itemBase }} {{ $isActive('unassigned') ? 'border-[color:var(--accent-soft)] bg-[color:var(--accent-soft)] text-[color:var(--accent)]' : 'border-transparent text-[#111827] hover:bg-[#F9FAFB]' }}">
                            <span class="flex items-center gap-2">
                                <iconify-icon icon="solar:user-minus-linear" width="16" class="text-[#6B7280]"></iconify-icon>
                                {{ __('Unassigned') }}
                            </span>
                            <span class="{{ $badgeBase }} {{ $isActive('unassigned') ? 'bg-white/70 text-[#111827]' : 'bg-[#F3F4F6] text-[#111827]' }}">{{ $viewCounts['unassigned'] ?? 0 }}</span>
                        </button>

                        <button type="button" wire:click="setView('all')"
                            class="{{ $itemBase }} {{ $isActive('all') ? 'border-[color:var(--accent-soft)] bg-[color:var(--accent-soft)] text-[color:var(--accent)]' : 'border-transparent text-[#111827] hover:bg-[#F9FAFB]' }}">
                            <span class="flex items-center gap-2">
                                <iconify-icon icon="solar:layers-linear" width="16" class="text-[#6B7280]"></iconify-icon>
                                {{ __('All Tickets') }}
                            </span>
                            <span class="{{ $badgeBase }} {{ $isActive('all') ? 'bg-white/70 text-[#111827]' : 'bg-[#F3F4F6] text-[#111827]' }}">{{ $viewCounts['all'] ?? 0 }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: list -->
        <div class="lg:col-span-3">
            <div class="rounded-xl border border-[#E5E7EB] bg-white shadow-sm overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-[#E5E7EB]">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex-1 flex flex-col sm:flex-row gap-2">
                            <div class="relative flex-1">
                                <iconify-icon icon="solar:magnifer-linear" class="absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF]" width="16"></iconify-icon>
                                <input
                                    type="text"
                                    class="w-full h-10 pl-9 pr-3 rounded-md border border-[#E5E7EB] bg-white text-[13px] text-[#111827] placeholder:text-[#9CA3AF] focus:outline-none focus:border-[color:var(--accent)] focus:ring-2 focus:ring-[color:var(--accent-ring)] transition"
                                    placeholder="{{ __('Rechercher (id, sujet, auteur, assigné)…') }}"
                                    wire:model.live="search"
                                />
                            </div>

                            <button
                                type="button"
                                class="h-10 px-3 bg-white border border-[#E5E7EB] text-[#111827] text-[13px] font-medium rounded-md shadow-sm hover:bg-[#F9FAFB] transition"
                                wire:click="resetFilters"
                            >
                                {{ __('Réinitialiser') }}
                            </button>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-2">
                            <div class="relative">
                                <select wire:model.live="status" class="h-10 min-w-[170px] rounded-md border border-[#E5E7EB] bg-white text-[13px] text-[#111827] shadow-sm focus:outline-none focus:border-[color:var(--accent)] focus:ring-2 focus:ring-[color:var(--accent-ring)] transition appearance-none pr-9">
                                    <option value="">{{ __('Tous les statuts') }}</option>
                                    <option value="open">{{ __('Ouvert') }}</option>
                                    <option value="in_progress">{{ __('En cours') }}</option>
                                    <option value="pending">{{ __('En attente') }}</option>
                                    <option value="resolved">{{ __('Résolu') }}</option>
                                    <option value="closed">{{ __('Fermé') }}</option>
                                </select>
                                <iconify-icon icon="solar:alt-arrow-down-linear" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#6B7280] pointer-events-none" width="14"></iconify-icon>
                            </div>

                            <div class="relative">
                                <select wire:model.live="priority" class="h-10 min-w-[170px] rounded-md border border-[#E5E7EB] bg-white text-[13px] text-[#111827] shadow-sm focus:outline-none focus:border-[color:var(--accent)] focus:ring-2 focus:ring-[color:var(--accent-ring)] transition appearance-none pr-9">
                                    <option value="">{{ __('Toutes les priorités') }}</option>
                                    @foreach ($priorities as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                                <iconify-icon icon="solar:alt-arrow-down-linear" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#6B7280] pointer-events-none" width="14"></iconify-icon>
                            </div>

                            <div class="relative">
                                <select wire:model.live="assignee" class="h-10 min-w-[190px] rounded-md border border-[#E5E7EB] bg-white text-[13px] text-[#111827] shadow-sm focus:outline-none focus:border-[color:var(--accent)] focus:ring-2 focus:ring-[color:var(--accent-ring)] transition appearance-none pr-9">
                                    <option value="">{{ __('Tous les assignés') }}</option>
                                    <option value="unassigned">{{ __('Non assigné') }}</option>
                                    @foreach ($assignees as $a)
                                        <option value="{{ $a->id }}">{{ $a->name }}</option>
                                    @endforeach
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
                                <th class="px-4 py-3 w-16">{{ __('ID') }}</th>
                                <th class="px-4 py-3">{{ __('Sujet') }}</th>
                                <th class="px-4 py-3 w-40">{{ __('Catégorie') }}</th>
                                <th class="px-4 py-3 w-40">{{ __('Priorité') }}</th>
                                <th class="px-4 py-3 w-36">{{ __('Statut') }}</th>
                                <th class="px-4 py-3 w-44">{{ __('Dernière activité') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E5E7EB]">
                            @forelse ($tickets as $t)
                                @php
                                    [$label, $icon] = $statusLabel($t->status->value);
                                    $pill = $statusPill($t->status->value);
                                    $prio = $priorityMeta($t->priority?->level);
                                @endphp
                                <tr class="group hover:bg-[#F9FAFB] transition-colors">
                                    <td class="px-4 py-3 text-xs font-mono text-[#6B7280] group-hover:text-[#111827]">#{{ $t->id }}</td>
                                    <td class="px-4 py-3">
                                        <div class="text-[13px] font-medium text-[#111827]">{{ $t->subject }}</div>
                                        <div class="mt-1 text-[11px] text-[#6B7280]">
                                            {{ __('Par') }} <span class="font-medium text-[#111827]">{{ $t->creator?->name ?? '—' }}</span>
                                            @if ($t->assignee)
                                                <span class="mx-2">•</span>
                                                {{ __('Assigné à') }} <span class="font-medium text-[#111827]">{{ $t->assignee->name }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-[12px] text-[#111827]">{{ $t->category?->name ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="inline-flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full {{ $prio['dot'] }}"></span>
                                            <span class="text-[12px] font-medium {{ $prio['text'] }}">{{ $t->priority?->name ?? $prio['label'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium border {{ $pill['bg'] }} {{ $pill['text'] }} {{ $pill['border'] }}">
                                            <iconify-icon icon="{{ $icon }}" width="12"></iconify-icon>
                                            {{ $label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-[12px] text-[#6B7280]">
                                        {{ $t->updated_at?->diffForHumans() }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-[13px] text-[#6B7280]">
                                        {{ __('Aucun ticket.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 sm:px-6 py-4 border-t border-[#E5E7EB] bg-white flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-[12px] text-[#6B7280]">{{ __('Par page') }}</span>
                        <div class="relative">
                            <select wire:model.live="perPage" class="h-9 rounded-md border border-[#E5E7EB] bg-white text-[13px] text-[#111827] shadow-sm focus:outline-none focus:border-[color:var(--accent)] focus:ring-2 focus:ring-[color:var(--accent-ring)] transition appearance-none pr-8 pl-3">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                            <iconify-icon icon="solar:alt-arrow-down-linear" class="absolute right-2 top-1/2 -translate-y-1/2 text-[#6B7280] pointer-events-none" width="14"></iconify-icon>
                        </div>
                    </div>

                    <div class="text-[12px] text-[#6B7280]">
                        {{ $tickets->links() }}
                    </div>
                </div>
            </div>
    </div>

    <div
        x-cloak
        class="fixed inset-0 z-50"
        x-show="open"
        x-transition.opacity
        @keydown.escape.window="open = false"
    >
        <div class="absolute inset-0 bg-black/30" @click="open = false"></div>

        <div class="absolute inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl border-l border-[#E5E7EB] flex flex-col"
             x-transition:enter="transform transition ease-out duration-200"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
        >
            <div class="px-4 sm:px-6 py-4 border-b border-[#E5E7EB] flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-[15px] font-semibold text-[#111827]">Créer un ticket</h2>
                    <p class="mt-1 text-[12px] text-[#6B7280]">Remplis le formulaire pour envoyer une nouvelle demande.</p>
                </div>
                <button type="button" class="w-9 h-9 rounded-md border border-[#E5E7EB] bg-white hover:bg-[#F9FAFB] text-[#111827] flex items-center justify-center transition" @click="open = false">
                    <iconify-icon icon="solar:close-circle-linear" width="18"></iconify-icon>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar p-4 sm:p-6">
                <livewire:tickets.create-form />
            </div>
        </div>
    </div>
</div>
