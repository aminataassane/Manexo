<x-manexo-app-layout>
    <x-slot name="title">Profil</x-slot>

    @php
        $user = Auth::user();
        $currentSessionId = session()->getId();

        $organizations = $user?->organizations()
            ->withPivot(['role'])
            ->orderBy('name')
            ->get() ?? collect();

        $sessions = \Illuminate\Support\Facades\DB::table('sessions')
            ->where('user_id', $user?->id)
            ->orderByDesc('last_activity')
            ->limit(10)
            ->get();

        $activityFilter = request('activity', 'all'); // all | tickets | assignations | commentaires

        $ticketsCreated = \App\Models\Ticket::query()
            ->where('created_by', $user?->id)
            ->latest('created_at')
            ->limit(50)
            ->get(['id', 'subject', 'status', 'created_at', 'updated_at']);

        $ticketsAssigned = \App\Models\Ticket::query()
            ->where('assigned_to', $user?->id)
            ->latest('updated_at')
            ->limit(50)
            ->get(['id', 'subject', 'status', 'created_at', 'updated_at']);

        $events = collect();
        if (in_array($activityFilter, ['all', 'tickets'], true)) {
            foreach ($ticketsCreated as $t) {
                $events->push([
                    'type' => 'ticket',
                    'label' => 'Ticket créé',
                    'ticket_id' => $t->id,
                    'subject' => $t->subject,
                    'status' => (string) $t->status,
                    'at' => $t->created_at,
                ]);
            }
        }
        if (in_array($activityFilter, ['all', 'assignations'], true)) {
            foreach ($ticketsAssigned as $t) {
                $events->push([
                    'type' => 'ticket',
                    'label' => 'Ticket assigné à vous',
                    'ticket_id' => $t->id,
                    'subject' => $t->subject,
                    'status' => (string) $t->status,
                    'at' => $t->updated_at,
                ]);
            }
        }

        // Commentaires: pas encore de table/modèle dans le projet → filtre affiché mais non disponible
        if ($activityFilter === 'commentaires') {
            $events = collect();
        }

        $events = $events
            ->filter(fn ($e) => filled($e['at'] ?? null))
            ->sortByDesc('at')
            ->take(50)
            ->values();

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
    @endphp

    @if (session('profile_status'))
        <div class="rounded-lg border border-[#E5E7EB] bg-white p-4 text-sm text-[#111827] shadow-sm">
            <span class="font-medium" style="color: var(--accent);">OK.</span>
            {{ session('profile_status') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold text-[#111827] tracking-tight">Profil</h1>
            <p class="text-sm text-[#6B7280] mt-1">Gérez vos informations, vos entreprises, et votre sécurité.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- LEFT: main -->
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <!-- Informations personnelles -->
            <div class="bg-white rounded-lg border border-[#E5E7EB] shadow-sm p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-sm font-semibold text-[#111827]">Informations personnelles</h2>
                        <p class="text-xs text-[#6B7280] mt-1">Nom, email et vérification.</p>
                    </div>
                </div>
                <livewire:profile.update-profile-information-form />
            </div>

            <!-- Entreprises & rôles -->
            <div class="bg-white rounded-lg border border-[#E5E7EB] shadow-sm p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-sm font-semibold text-[#111827]">Entreprises & rôles</h2>
                        <p class="text-xs text-[#6B7280] mt-1">Vos accès et vos permissions.</p>
                    </div>
                    <a href="{{ route('organizations.select') }}"
                       class="text-xs font-medium hover:opacity-80"
                       style="color: var(--accent);">
                        Changer
                    </a>
                </div>

                <div class="space-y-2">
                    @forelse ($organizations as $org)
                        <div class="flex items-center gap-3 rounded-md border border-[#E5E7EB] bg-[#F9FAFB] p-3">
                            <div class="h-9 w-9 rounded-md flex items-center justify-center text-xs font-bold"
                                 style="background: {{ $org->primary_color ? 'color-mix(in srgb, '.$org->primary_color.' 18%, white)' : 'var(--accent-soft)' }}; color: {{ $org->primary_color ?: 'var(--accent)' }};">
                                {{ mb_strtoupper(mb_substr($org->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-semibold text-[#111827] truncate">{{ $org->name }}</p>
                                    @if ($org->primary_color)
                                        <span class="h-2 w-2 rounded-full" style="background: {{ $org->primary_color }};"></span>
                                    @endif
                                </div>
                                <p class="text-xs text-[#6B7280] mt-0.5">Rôle : <span class="font-medium text-[#111827]">{{ ucfirst((string) $org->pivot->role) }}</span></p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-md border border-[#E5E7EB] bg-[#F9FAFB] p-4 text-sm text-[#6B7280]">
                            Vous n’êtes rattaché à aucune entreprise pour le moment.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Préférences & notifications -->
            <div class="bg-white rounded-lg border border-[#E5E7EB] shadow-sm p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-sm font-semibold text-[#111827]">Préférences & notifications</h2>
                        <p class="text-xs text-[#6B7280] mt-1">Personnalisez votre expérience.</p>
                    </div>
                    <span class="text-[10px] font-semibold px-2 py-1 rounded-full bg-[#F9FAFB] border border-[#E5E7EB] text-[#6B7280]">Bientôt</span>
                </div>

                <div class="grid sm:grid-cols-2 gap-3">
                    <label class="flex items-center justify-between gap-4 rounded-md border border-[#E5E7EB] bg-[#F9FAFB] p-3 opacity-70">
                        <div>
                            <p class="text-sm font-medium text-[#111827]">Email : réponses tickets</p>
                            <p class="text-xs text-[#6B7280] mt-0.5">Recevoir un email quand un agent répond.</p>
                        </div>
                        <input type="checkbox" class="h-4 w-4" disabled>
                    </label>

                    <label class="flex items-center justify-between gap-4 rounded-md border border-[#E5E7EB] bg-[#F9FAFB] p-3 opacity-70">
                        <div>
                            <p class="text-sm font-medium text-[#111827]">Push : tickets urgents</p>
                            <p class="text-xs text-[#6B7280] mt-0.5">Alerte en temps réel (agents/admins).</p>
                        </div>
                        <input type="checkbox" class="h-4 w-4" disabled>
                    </label>
                </div>
            </div>

            <!-- Historique d’activité (accordéon) -->
            <div class="bg-white rounded-lg border border-[#E5E7EB] shadow-sm overflow-hidden" x-data="{ open: true }">
                <button type="button" class="w-full px-4 sm:px-6 py-4 flex items-center justify-between hover:bg-[#F9FAFB] transition-colors" @click="open = !open">
                    <div class="text-left">
                        <h2 class="text-sm font-semibold text-[#111827]">Historique d’activité</h2>
                        <p class="text-xs text-[#6B7280] mt-1">Journal centré sur vous (limité à 50 événements).</p>
                    </div>
                    <iconify-icon :icon="open ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear'" class="text-[#6B7280]" width="16"></iconify-icon>
                </button>

                <div x-show="open" x-transition.opacity class="px-4 sm:px-6 pb-5">
                    <div class="flex flex-wrap gap-2 mb-4">
                        @php $filters = [['all','Tous'], ['tickets','Tickets'], ['commentaires','Commentaires'], ['assignations','Assignations']]; @endphp
                        @foreach ($filters as [$key, $label])
                            @php
                                $isActive = $activityFilter === $key;
                                $disabled = $key === 'commentaires';
                            @endphp
                            <a
                                href="{{ route('profile', array_filter(['activity' => $key !== 'all' ? $key : null])) }}"
                                class="px-2.5 py-1 rounded-full text-[11px] font-medium border transition-colors {{ $isActive ? 'bg-white text-[#111827] border-[#E5E7EB] shadow-sm' : 'bg-[#F9FAFB] text-[#6B7280] border-[#E5E7EB] hover:text-[#111827]' }} {{ $disabled ? 'pointer-events-none opacity-50' : '' }}"
                                title="{{ $disabled ? 'À venir' : '' }}"
                            >{{ $label }}</a>
                        @endforeach
                    </div>

                    <div class="relative pl-4 border-l border-[#E5E7EB] space-y-5">
                        @forelse ($events as $e)
                            <div class="relative">
                                <div class="absolute -left-[21px] top-0.5 w-2.5 h-2.5 rounded-full ring-4 ring-white" style="background: var(--accent);"></div>
                                <p class="text-xs text-[#111827]">
                                    <span class="font-medium">{{ $e['label'] }}</span>
                                    <span class="text-[#6B7280]">• Ticket #{{ $e['ticket_id'] }}</span>
                                </p>
                                <p class="text-[11px] text-[#6B7280] mt-1 truncate">{{ $e['subject'] }}</p>
                                <p class="text-[10px] text-[#6B7280] mt-1">
                                    Statut : <span class="font-medium text-[#111827]">{{ $statusLabel($e['status']) }}</span>
                                    • {{ \Illuminate\Support\Carbon::parse($e['at'])->diffForHumans() }}
                                </p>
                            </div>
                        @empty
                            <div class="rounded-md border border-[#E5E7EB] bg-[#F9FAFB] p-4 text-sm text-[#6B7280]">
                                Aucun événement pour ce filtre.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: security -->
        <div class="space-y-4 sm:space-y-6">
            <!-- Sécurité & connexions -->
            <div class="bg-white rounded-lg border border-[#E5E7EB] shadow-sm p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-sm font-semibold text-[#111827]">Sécurité & connexions</h2>
                        <p class="text-xs text-[#6B7280] mt-1">Dernières sessions (IP / appareil).</p>
                    </div>
                </div>

                <div class="space-y-2">
                    @forelse ($sessions as $s)
                        @php
                            $isCurrent = (string) $s->id === (string) $currentSessionId;
                            $dt = \Illuminate\Support\Carbon::createFromTimestamp((int) $s->last_activity);
                        @endphp
                        <div class="rounded-md border border-[#E5E7EB] p-3 {{ $isCurrent ? 'bg-[color:var(--accent-soft)]' : 'bg-[#F9FAFB]' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-[#111827]">
                                        {{ $isCurrent ? 'Session actuelle' : 'Session' }}
                                        <span class="text-[#6B7280] font-normal">• {{ $dt->diffForHumans() }}</span>
                                    </p>
                                    <p class="text-[11px] text-[#6B7280] mt-1 truncate" title="{{ (string) ($s->user_agent ?? '') }}">
                                        {{ (string) ($s->user_agent ?? 'Appareil inconnu') }}
                                    </p>
                                    <p class="text-[11px] text-[#6B7280] mt-1">IP : <span class="font-medium text-[#111827]">{{ $s->ip_address ?? '—' }}</span></p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-md border border-[#E5E7EB] bg-[#F9FAFB] p-4 text-sm text-[#6B7280]">
                            Aucune session enregistrée.
                        </div>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('profile.sessions.logout_all') }}" class="mt-4">
                    @csrf
                    <button type="submit"
                            class="w-full h-9 rounded-md text-white text-[13px] font-semibold flex items-center justify-center gap-2 bg-[color:var(--accent)] hover:bg-[color:color-mix(in_srgb,var(--accent)_85%,black)] transition-colors">
                        <iconify-icon icon="solar:logout-2-linear" width="16"></iconify-icon>
                        Se déconnecter des autres sessions
                    </button>
                    <p class="text-[11px] text-[#6B7280] mt-2">Vous restez connecté sur cet appareil.</p>
                </form>
            </div>

            <!-- Mot de passe -->
            <div class="bg-white rounded-lg border border-[#E5E7EB] shadow-sm p-4 sm:p-6">
                <div class="mb-4">
                    <h2 class="text-sm font-semibold text-[#111827]">Mot de passe</h2>
                    <p class="text-xs text-[#6B7280] mt-1">Modifiez votre mot de passe.</p>
                </div>
                <livewire:profile.update-password-form />
            </div>

            <!-- Danger zone (accordéon) -->
            <div class="bg-white rounded-lg border border-[#E5E7EB] shadow-sm overflow-hidden" x-data="{ open: false }">
                <button type="button" class="w-full px-4 sm:px-6 py-4 flex items-center justify-between hover:bg-[#F9FAFB] transition-colors" @click="open = !open">
                    <div class="text-left">
                        <h2 class="text-sm font-semibold text-red-600">Zone sensible</h2>
                        <p class="text-xs text-[#6B7280] mt-1">Suppression de compte.</p>
                    </div>
                    <iconify-icon :icon="open ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear'" class="text-[#6B7280]" width="16"></iconify-icon>
                </button>
                <div x-show="open" x-transition.opacity class="px-4 sm:px-6 pb-5">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-manexo-app-layout>
