@php
    $statusLabel = function (string $status): array {
        return match ($status) {
            'open' => [__('Ouvert'), 'solar:bolt-circle-bold-duotone'],
            'in_progress' => [__('En cours'), 'solar:clock-circle-bold-duotone'],
            'pending' => [__('En attente'), 'solar:hourglass-bold-duotone'],
            'resolved' => [__('Résolu'), 'solar:check-circle-bold-duotone'],
            'closed' => [__('Fermé'), 'solar:lock-keyhole-bold-duotone'],
            default => [ucfirst(str_replace('_', ' ', $status)), 'solar:question-circle-bold-duotone'],
        };
    };

    $statusPill = function (string $status): array {
        return match ($status) {
            'open' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-100'],
            'in_progress' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-100'],
            'pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-100'],
            'resolved' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-100'],
            'closed' => ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-100'],
            default => ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-100'],
        };
    };

    $priorityMeta = function (?int $level): array {
        if ($level === null) {
            return ['label' => '—', 'dot' => 'bg-slate-300', 'text' => 'text-slate-600'];
        }

        return match (true) {
            $level >= 4 => ['label' => __('Critique'), 'dot' => 'bg-red-500', 'text' => 'text-red-700'],
            $level === 3 => ['label' => __('Haute'), 'dot' => 'bg-amber-500', 'text' => 'text-amber-700'],
            $level === 2 => ['label' => __('Moyenne'), 'dot' => 'bg-blue-500', 'text' => 'text-blue-700'],
            default => ['label' => __('Basse'), 'dot' => 'bg-emerald-500', 'text' => 'text-emerald-700'],
        };
    };
@endphp

<div
    class="w-full max-w-full min-w-0 mx-auto"
    x-data="{ dragId: null, viewsOpen: true }"
>
    <!-- HEADER (stack on mobile) -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6 sm:mb-8">
        <div class="min-w-0">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight sm:text-2xl lg:text-3xl min-[1920px]:text-4xl">{{ __('Tickets') }}</h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                @if(($box ?? 'active') === 'trash')
                    {{ __('Tickets supprimés. Restaurez-les pour les remettre dans les listes.') }}
                @elseif(($box ?? 'active') === 'archived')
                    {{ __('Tickets archivés (lecture / restauration).') }}
                @else
                    {{ __('Gérez et suivez les demandes de support.') }}
                @endif
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 sm:px-4 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all touch-target sm:min-h-0 sm:min-w-0"
                @click="viewsOpen = !viewsOpen"
            >
                <iconify-icon icon="solar:sidebar-minimalistic-bold-duotone" width="18"></iconify-icon>
                <span x-show="viewsOpen" class="hidden sm:inline">{{ __('Masquer vues') }}</span>
                <span x-show="!viewsOpen" class="hidden sm:inline">{{ __('Afficher vues') }}</span>
            </button>

            <a href="{{ route('tickets.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2.5 sm:px-4 text-sm font-semibold text-white shadow-lg shadow-[var(--accent-ring)] hover:opacity-90 transition-all transform hover:-translate-y-0.5 touch-target sm:min-h-0 sm:min-w-0" style="background-color: var(--accent);">
                <iconify-icon icon="solar:add-circle-bold" width="18"></iconify-icon>
                {{ __('Nouveau ticket') }}
            </a>
        </div>
    </div>

    <!-- STATS CARDS (2 cols mobile, 4 cols lg, responsive gap) -->
    <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6 sm:mb-8 lg:grid-cols-4 min-[1920px]:gap-6">
        <!-- Open -->
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500 truncate block">{{ __('Ouverts') }}</span>
                    <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900">{{ $stats['open'] ?? 0 }}</div>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-red-600 group-hover:scale-110 transition-transform">
                    <iconify-icon icon="solar:bolt-circle-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500 truncate block">{{ __('En cours') }}</span>
                    <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900">{{ $stats['in_progress'] ?? 0 }}</div>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 group-hover:scale-110 transition-transform">
                    <iconify-icon icon="solar:clock-circle-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500 truncate block">{{ __('En attente') }}</span>
                    <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900">{{ $stats['pending'] ?? 0 }}</div>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600 group-hover:scale-110 transition-transform">
                    <iconify-icon icon="solar:hourglass-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>

        <!-- Resolved -->
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500 truncate block">{{ __('Résolus (7j)') }}</span>
                    <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900">{{ $stats['resolved_7d'] ?? 0 }}</div>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 group-hover:scale-110 transition-transform">
                    <iconify-icon icon="solar:check-circle-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT (1 col mobile, 4 when sidebar open) -->
    <div class="grid grid-cols-1 gap-4 sm:gap-6 lg:gap-8 transition-all duration-300 min-w-0" :class="viewsOpen ? 'lg:grid-cols-4' : 'lg:grid-cols-1'">

        <!-- SIDEBAR FILTERS -->
        <div class="lg:col-span-1" x-cloak x-show="viewsOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
            <div class="sticky top-24 space-y-6">
                <!-- Views Menu -->
                <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-50 bg-slate-50/50">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ __('Vues rapides') }}</h3>
                    </div>
                    <div class="p-2 space-y-1">
                        @php
                            $itemBase = 'w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200';
                            $badgeBase = 'min-w-[24px] h-6 px-1.5 rounded-lg text-xs font-bold flex items-center justify-center';
                            $isActive = fn (string $k) => ($viewKey ?? 'all') === $k;
                            $boxKey = $box ?? 'active';
                            $boxBtn = 'w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200';
                        @endphp

                        <div class="px-1 pb-2">
                            <div class="flex flex-wrap items-stretch gap-1 rounded-xl bg-slate-100 p-1">
                                <button type="button" wire:click="setBox('active')"
                                    class="flex-1 min-w-0 rounded-lg px-2 py-2 sm:px-3 text-xs sm:text-sm font-bold transition-all {{ $boxKey === 'active' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900' }}">
                                    <span class="inline-flex items-center gap-1 sm:gap-2 justify-center w-full truncate">
                                        <iconify-icon icon="solar:ticket-bold-duotone" width="16" class="sm:w-[18px] shrink-0"></iconify-icon>
                                        <span class="truncate">{{ __('Actifs') }}</span>
                                    </span>
                                </button>
                                <button type="button" wire:click="setBox('archived')"
                                    class="flex-1 min-w-0 rounded-lg px-2 py-2 sm:px-3 text-xs sm:text-sm font-bold transition-all {{ $boxKey === 'archived' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900' }}">
                                    <span class="inline-flex items-center gap-1 sm:gap-2 justify-center w-full truncate">
                                        <iconify-icon icon="solar:archive-bold-duotone" width="16" class="sm:w-[18px] shrink-0"></iconify-icon>
                                        <span class="truncate">{{ __('Archivés') }}</span>
                                        <span class="ml-0.5 sm:ml-1 px-1 sm:px-1.5 py-0.5 rounded-full text-[9px] sm:text-[10px] shrink-0 {{ $boxKey === 'archived' ? 'bg-slate-100 text-slate-700' : 'bg-white/60 text-slate-500' }}">{{ $viewCounts['archived'] ?? 0 }}</span>
                                    </span>
                                </button>
                                @if($isStaff ?? false)
                                <button type="button" wire:click="setBox('trash')"
                                    class="flex-1 min-w-0 rounded-lg px-2 py-2 sm:px-3 text-xs sm:text-sm font-bold transition-all {{ $boxKey === 'trash' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900' }}">
                                    <span class="inline-flex items-center gap-1 sm:gap-2 justify-center w-full truncate">
                                        <iconify-icon icon="solar:trash-bin-trash-bold-duotone" width="16" class="sm:w-[18px] shrink-0"></iconify-icon>
                                        <span class="truncate">{{ __('Corbeille') }}</span>
                                        <span class="ml-0.5 sm:ml-1 px-1 sm:px-1.5 py-0.5 rounded-full text-[9px] sm:text-[10px] shrink-0 {{ $boxKey === 'trash' ? 'bg-slate-100 text-slate-700' : 'bg-white/60 text-slate-500' }}">{{ $viewCounts['trash'] ?? 0 }}</span>
                                    </span>
                                </button>
                                @endif
                            </div>
                        </div>

                        <button type="button" wire:click="setView('all')"
                            class="{{ $itemBase }} {{ $isActive('all') ? 'bg-[var(--accent-soft)] text-[var(--accent)]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:layers-bold-duotone" width="18"></iconify-icon>
                                {{ __('Tous les tickets') }}
                            </span>
                            <span class="{{ $badgeBase }} {{ $isActive('all') ? 'bg-white/50 text-[var(--accent)]' : 'bg-slate-100 text-slate-500' }}">{{ $viewCounts['all'] ?? 0 }}</span>
                        </button>

                        <button type="button" wire:click="setView('created_by_me')"
                            class="{{ $itemBase }} {{ $isActive('created_by_me') ? 'bg-[var(--accent-soft)] text-[var(--accent)]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:pen-bold-duotone" width="18"></iconify-icon>
                                {{ __('Créés par moi') }}
                            </span>
                            <span class="{{ $badgeBase }} {{ $isActive('created_by_me') ? 'bg-white/50 text-[var(--accent)]' : 'bg-slate-100 text-slate-500' }}">{{ $viewCounts['created_by_me'] ?? 0 }}</span>
                        </button>

                        <button type="button" wire:click="setView('assigned_to_me')"
                            class="{{ $itemBase }} {{ $isActive('assigned_to_me') ? 'bg-[var(--accent-soft)] text-[var(--accent)]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:user-check-bold-duotone" width="18"></iconify-icon>
                                {{ __('Assignés à moi') }}
                            </span>
                            <span class="{{ $badgeBase }} {{ $isActive('assigned_to_me') ? 'bg-white/50 text-[var(--accent)]' : 'bg-slate-100 text-slate-500' }}">{{ $viewCounts['assigned_to_me'] ?? 0 }}</span>
                        </button>

                        <button type="button" wire:click="setView('high_priority')"
                            class="{{ $itemBase }} {{ $isActive('high_priority') ? 'bg-[var(--accent-soft)] text-[var(--accent)]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:danger-triangle-bold-duotone" width="18"></iconify-icon>
                                {{ __('Haute priorité') }}
                            </span>
                            <span class="{{ $badgeBase }} {{ $isActive('high_priority') ? 'bg-white/50 text-[var(--accent)]' : 'bg-slate-100 text-slate-500' }}">{{ $viewCounts['high_priority'] ?? 0 }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- TICKET LIST / KANBAN -->
        <div :class="viewsOpen ? 'lg:col-span-3' : 'lg:col-span-1'">

            <!-- Reopen Button (Mobile/Hidden state) -->
            <div x-cloak x-show="!viewsOpen" class="mb-4">
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all"
                    @click="viewsOpen = true"
                >
                    <iconify-icon icon="solar:sidebar-minimalistic-bold-duotone" width="18"></iconify-icon>
                    {{ __('Afficher le menu') }}
                </button>
            </div>

            @if (($displayMode ?? 'list') === 'kanban' && ($box ?? 'active') !== 'trash')
                <!-- KANBAN VIEW (horizontal scroll on mobile) -->
                <div class="rounded-xl sm:rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden h-[calc(100vh-14rem)] sm:h-[calc(100vh-12rem)] min-h-[400px]">
                    <div class="p-3 sm:p-4 border-b border-slate-100 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between bg-slate-50/50">
                        <h2 class="text-sm font-bold text-slate-900">{{ __('Tableau Kanban') }}</h2>
                        <div class="text-xs text-slate-500 hidden sm:block">{{ __('Glisser-déposer pour changer le statut') }}</div>
                    </div>
                    <div class="p-2 sm:p-4 h-full overflow-x-auto overflow-y-hidden custom-scrollbar scroll-touch">
                        <div class="flex gap-3 sm:gap-4 h-full min-w-max">
                            @foreach (($statusColumns ?? []) as $colStatus)
                                @php
                                    [$colLabel, $colIcon] = $statusLabel($colStatus);
                                    $colPill = $statusPill($colStatus);
                                    $cards = $kanbanTickets[$colStatus] ?? [];
                                @endphp
                                <div
                                    class="w-[280px] min-[400px]:w-[300px] sm:w-[320px] shrink-0 rounded-xl bg-slate-50/50 border border-slate-100 flex flex-col h-full min-h-0"
                                    @dragover.prevent
                                    @drop.prevent="if (dragId) { $wire.moveTicket(dragId, '{{ $colStatus }}'); dragId = null; }"
                                >
                                    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border {{ $colPill['bg'] }} {{ $colPill['text'] }} {{ $colPill['border'] }}">
                                            {{ $colLabel }}
                                        </span>
                                        <span class="text-xs font-bold text-slate-400">{{ count($cards) }}</span>
                                    </div>
                                    <div class="p-3 space-y-3 overflow-y-auto custom-scrollbar flex-1">
                                        @forelse ($cards as $t)
                                            @php
                                                $prio = $priorityMeta($t->priority?->level);
                                                $prog = $checklistProgress[$t->id] ?? null;
                                                $pct = $prog && (int) $prog->total > 0 ? (int) round(100 * (int) $prog->done / (int) $prog->total) : null;
                                            @endphp
                                            <div
                                                class="rounded-xl border border-slate-100 bg-white p-4 shadow-sm hover:shadow-md hover:border-[var(--accent-soft)] transition-all cursor-grab active:cursor-grabbing group"
                                                draggable="true"
                                                @dragstart="dragId = {{ (int) $t->id }}"
                                                @dragend="dragId = null"
                                            >
                                                <div class="flex justify-between items-start mb-2">
                                                    <span class="text-xs font-mono font-bold text-slate-400">#{{ $t->id }}</span>
                                                    <span class="h-2 w-2 rounded-full {{ $prio['dot'] }}" title="{{ $prio['label'] }}"></span>
                                                </div>
                                                <h4 class="text-sm font-bold text-slate-900 mb-1 line-clamp-2 group-hover:text-[var(--accent)] transition-colors">{{ $t->subject }}</h4>
                                                @if ($pct !== null)
                                                    <div class="mb-2">
                                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold {{ $pct >= 100 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                                            <iconify-icon icon="solar:checklist-minimalistic-linear" width="10"></iconify-icon>
                                                            {{ $pct }}%
                                                        </span>
                                                    </div>
                                                @endif
                                                <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-50">
                                                    <div class="flex items-center gap-2">
                                                        <x-avatar :name="$t->creator?->name ?? 'U'" size="h-5 w-5" class="ring-1 ring-white shadow-sm" />
                                                        <span class="text-xs text-slate-500 truncate max-w-[100px]">{{ $t->creator?->name }}</span>
                                                    </div>
                                                    <span class="text-[10px] text-slate-400">{{ $t->updated_at?->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="py-8 text-center text-xs text-slate-400 italic">{{ __('Vide') }}</div>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <!-- LIST VIEW -->
                <div class="rounded-xl sm:rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden min-w-0">
                    <!-- Filters Toolbar -->
                    <div class="p-3 sm:p-4 border-b border-slate-100 bg-slate-50/30 flex flex-col gap-3 sm:gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex-1 min-w-0 relative">
                            <iconify-icon icon="solar:magnifer-linear" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" width="18"></iconify-icon>
                            <input
                                type="text"
                                class="w-full h-11 pl-10 pr-4 rounded-xl border-slate-200 bg-white text-sm text-slate-900 placeholder:text-slate-400 focus:border-[var(--accent)] focus:ring-[var(--accent)] transition-shadow shadow-sm"
                                placeholder="{{ __('Rechercher un ticket...') }}"
                                wire:model.live="search"
                            />
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <div class="w-full min-w-0 sm:w-40 flex-1 sm:flex-none">
                                <x-select-input wire:model.live="status">
                                    <option value="">{{ __('Statut') }}</option>
                                    <option value="open">{{ __('Ouvert') }}</option>
                                    <option value="in_progress">{{ __('En cours') }}</option>
                                    <option value="pending">{{ __('En attente') }}</option>
                                    <option value="resolved">{{ __('Résolu') }}</option>
                                    <option value="closed">{{ __('Fermé') }}</option>
                                </x-select-input>
                            </div>
                            <div class="w-full min-w-0 sm:w-40 flex-1 sm:flex-none">
                                <x-select-input wire:model.live="priority">
                                    <option value="">{{ __('Priorité') }}</option>
                                    @foreach ($priorities as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </x-select-input>
                            </div>
                            <button wire:click="resetFilters" class="h-11 min-h-[44px] px-4 rounded-xl border border-slate-200 bg-white text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors shadow-sm touch-target sm:min-h-0">
                                {{ __('Reset') }}
                            </button>
                        </div>
                    </div>

                    <!-- Table (horizontal scroll on small screens) -->
                    <div class="responsive-table-wrap scroll-touch -mx-2 sm:mx-0 px-2 sm:px-0">
                        <table class="w-full text-left min-w-[640px]">
                            <thead class="bg-slate-50 text-[10px] sm:text-xs uppercase font-bold text-slate-500 tracking-wider">
                                <tr>
                                    <th class="px-3 sm:px-6 py-2.5 sm:py-4">{{ __('Sujet') }}</th>
                                    <th class="px-3 sm:px-6 py-2.5 sm:py-4">{{ __('Catégorie') }}</th>
                                    <th class="px-3 sm:px-6 py-2.5 sm:py-4">{{ __('Priorité') }}</th>
                                    <th class="px-3 sm:px-6 py-2.5 sm:py-4">{{ __('Statut') }}</th>
                                    <th class="px-3 sm:px-6 py-2.5 sm:py-4 text-right">{{ __('Activité') }}</th>
                                    @if(($box ?? 'active') === 'trash')
                                        <th class="px-3 sm:px-6 py-2.5 sm:py-4 text-right">{{ __('Actions') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($tickets as $t)
                                    @php
                                        [$label, $icon] = $statusLabel($t->status->value);
                                        $pill = $statusPill($t->status->value);
                                        $prio = $priorityMeta($t->priority?->level);
                                    @endphp
                                    <tr class="group hover:bg-slate-50/80 transition-colors {{ ($box ?? 'active') !== 'trash' ? 'cursor-pointer' : '' }}" @if(($box ?? 'active') !== 'trash') onclick="window.location='{{ route('tickets.discussion', $t->id) }}'" @endif>
                                        <td class="px-3 sm:px-6 py-2.5 sm:py-4">
                                            <div class="flex items-center gap-2 sm:gap-4 min-w-0">
                                                <span class="flex h-9 w-9 sm:h-10 sm:w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-500 font-mono">
                                                    #{{ $t->id }}
                                                </span>
                                                <div class="min-w-0">
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <span class="text-sm font-bold text-slate-900 group-hover:text-[var(--accent)] transition-colors break-words">{{ $t->subject }}</span>
                                                        @php $prog = $checklistProgress[$t->id] ?? null; @endphp
                                                        @if ($prog && (int) $prog->total > 0)
                                                            @php $pct = (int) round(100 * (int) $prog->done / (int) $prog->total); @endphp
                                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold {{ $pct >= 100 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                                                <iconify-icon icon="solar:checklist-minimalistic-linear" width="12"></iconify-icon>
                                                                {{ $pct }}%
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-1 flex-wrap">
                                                        <span>{{ $t->creator?->name ?? 'Inconnu' }}</span>
                                                        @if($t->assignees->isNotEmpty())
                                                            <span class="text-slate-300">|</span>
                                                            <span class="flex items-center gap-1">
                                                                <span class="flex -space-x-1.5">
                                                                    @foreach($t->assignees->take(3) as $a)
                                                                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[9px] font-bold ring-2 ring-white shrink-0" style="background: var(--accent-soft); color: var(--accent);" title="{{ $a->name }}">{{ strtoupper(mb_substr($a->name, 0, 1)) }}</span>
                                                                    @endforeach
                                                                </span>
                                                                @if($t->assignees->count() > 3)
                                                                    <span class="text-[10px] font-bold text-slate-500">+{{ $t->assignees->count() - 3 }}</span>
                                                                @endif
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 sm:px-6 py-2.5 sm:py-4 text-sm text-slate-600 whitespace-nowrap">
                                            {{ $t->category?->name ?? '—' }}
                                        </td>
                                        <td class="px-3 sm:px-6 py-2.5 sm:py-4">
                                            <div class="flex items-center gap-2">
                                                <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $prio['dot'] }}"></span>
                                                <span class="text-sm font-medium {{ $prio['text'] }}">{{ $t->priority?->name ?? $prio['label'] }}</span>
                                            </div>
                                        </td>
                                        <td class="px-3 sm:px-6 py-2.5 sm:py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border {{ $pill['bg'] }} {{ $pill['text'] }} {{ $pill['border'] }}">
                                                <iconify-icon icon="{{ $icon }}" width="14"></iconify-icon>
                                                {{ $label }}
                                            </span>
                                        </td>
                                        <td class="px-3 sm:px-6 py-2.5 sm:py-4 text-right text-sm text-slate-500 whitespace-nowrap">
                                            {{ ($box ?? 'active') === 'trash' ? ($t->deleted_at?->diffForHumans() ?? '—') : $t->updated_at?->diffForHumans() }}
                                        </td>
                                        @if(($box ?? 'active') === 'trash')
                                            <td class="px-3 sm:px-6 py-2.5 sm:py-4 text-right whitespace-nowrap">
                                                <button type="button" wire:click="restoreFromTrash({{ $t->id }})" data-restore class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 min-h-[44px] sm:min-h-0 sm:py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors touch-manipulation">
                                                    <iconify-icon icon="solar:restart-bold-duotone" width="14"></iconify-icon>
                                                    {{ __('Restaurer') }}
                                                </button>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ ($box ?? 'active') === 'trash' ? 6 : 5 }}" class="px-4 sm:px-6 py-8 sm:py-12 text-center text-slate-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="h-16 w-16 rounded-full bg-slate-50 flex items-center justify-center mb-4">
                                                    <iconify-icon icon="solar:ticket-linear" width="32" class="text-slate-400"></iconify-icon>
                                                </div>
                                                <p class="font-medium text-slate-900">{{ __('Aucun ticket trouvé') }}</p>
                                                <p class="text-sm text-slate-500 mt-1">{{ __('Essayez de modifier vos filtres ou créez un nouveau ticket.') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-4 sm:px-6 py-4 border-t border-slate-100 bg-slate-50/30 overflow-x-auto">
                        {{ $tickets->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
