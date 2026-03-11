<x-manexo-app-layout>
    <x-slot name="title">{{ __('pages.profile.title') }}</x-slot>

    @php
        $user = Auth::user();
        $currentSessionId = session()->getId();
        $currentLocale = strtoupper((string) app()->getLocale());

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

        $currentOrgId = (int) session('current_organization_id');

        $ticketsCreated = $currentOrgId ? \App\Models\Ticket::query()
            ->where('organization_id', $currentOrgId)
            ->where('created_by', $user?->id)
            ->latest('created_at')
            ->limit(25)
            ->get(['id', 'subject', 'status', 'created_at', 'updated_at']) : collect();

        $ticketsAssigned = $currentOrgId ? \App\Models\Ticket::query()
            ->where('organization_id', $currentOrgId)
            ->where('assigned_to', $user?->id)
            ->latest('updated_at')
            ->limit(25)
            ->get(['id', 'subject', 'status', 'created_at', 'updated_at']) : collect();

        $events = collect();
        if (in_array($activityFilter, ['all', 'tickets'], true)) {
            foreach ($ticketsCreated as $t) {
                $events->push([
                    'type' => 'ticket',
                    'label' => __('pages.profile.ticket_created'),
                    'ticket_id' => $t->id,
                    'subject' => $t->subject,
                    'status' => is_object($t->status) ? $t->status->value : (string) $t->status,
                    'at' => $t->created_at,
                ]);
            }
        }
        if (in_array($activityFilter, ['all', 'assignations'], true)) {
            foreach ($ticketsAssigned as $t) {
                $events->push([
                    'type' => 'ticket',
                    'label' => __('pages.profile.ticket_assigned_to_you'),
                    'ticket_id' => $t->id,
                    'subject' => $t->subject,
                    'status' => is_object($t->status) ? $t->status->value : (string) $t->status,
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
            ->take(5)
            ->values();

        $statusLabel = function (string $status): string {
            return __('tickets.status.' . $status);
        };

        $pendingInvitations = $user
            ? \App\Models\OrganizationInvitation::withoutOrganizationScope()
                ->where('email', $user->email)
                ->pending()
                ->with(['organization', 'inviter'])
                ->get()
            : collect();
    @endphp

    @if (session('profile_status'))
        <div class="mb-6 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-sm flex items-center gap-3">
            <iconify-icon icon="solar:check-circle-bold" width="20"></iconify-icon>
            {{ session('profile_status') }}
        </div>
    @endif

    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ __('pages.profile.heading') }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ __('pages.profile.subheading') }}</p>
        </div>
        @if (Auth::user()?->hasPlatformAccess())
            <a href="{{ route('platform-admin.dashboard') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 hover:text-slate-900 transition-colors">
                <iconify-icon icon="solar:arrow-left-linear" width="16"></iconify-icon>
                {{ __('pages.profile.back_to_platform') }}
            </a>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- LEFT COLUMN -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Personal Info -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-base font-semibold text-slate-900">{{ __('pages.profile.personal_info_title') }}</h2>
                    <p class="text-xs text-slate-500 mt-0.5">{{ __('pages.profile.personal_info_subtitle') }}</p>
                </div>
                <div class="p-6">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <!-- Pending Invitations -->
            @if($pendingInvitations->isNotEmpty())
                <div class="rounded-2xl border border-cyan-200 bg-gradient-to-br from-cyan-50/60 to-white shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-cyan-100 bg-cyan-50/50 flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-100 text-cyan-600">
                            <iconify-icon icon="solar:letter-bold-duotone" width="20"></iconify-icon>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">{{ __('invitations.pending_title') }}</h2>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $pendingInvitations->count() }} {{ trans_choice('invitations.pending_count_label', $pendingInvitations->count()) }}</p>
                        </div>
                    </div>
                    <div class="divide-y divide-cyan-100">
                        @foreach($pendingInvitations as $inv)
                            @php
                                $invRoleLabel = match ($inv->role) {
                                    'owner' => __('pages.team.role_owner'),
                                    'admin' => __('pages.team.role_admin'),
                                    'agent' => __('pages.team.role_agent'),
                                    default => __('pages.team.role_member'),
                                };
                                $invInitial = mb_strtoupper(mb_substr((string) ($inv->organization?->name ?? '?'), 0, 1));
                                $daysLeft = (int) now()->diffInDays($inv->expires_at, false);
                            @endphp
                            <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-4">
                                <div class="flex items-center gap-4 min-w-0 flex-1">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl font-bold text-sm shadow-sm"
                                         style="background: {{ $inv->organization?->primary_color ? 'color-mix(in srgb, '.$inv->organization->primary_color.' 15%, white)' : '#e0f2fe' }}; color: {{ $inv->organization?->primary_color ?: '#0891b2' }};">
                                        {{ $invInitial }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-900 truncate">{{ $inv->organization?->name ?? '—' }}</p>
                                        <div class="flex flex-wrap items-center gap-2 mt-1">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-cyan-100 text-cyan-700 border border-cyan-200">
                                                <iconify-icon icon="solar:shield-user-bold-duotone" width="11"></iconify-icon>
                                                {{ $invRoleLabel }}
                                            </span>
                                            @if($inv->inviter)
                                                <span class="text-[11px] text-slate-500">{{ __('invitations.invited_by', ['name' => $inv->inviter->name]) }}</span>
                                            @endif
                                            <span class="text-[11px] text-cyan-600 font-medium">{{ __('invitations.expires_in', ['days' => $daysLeft]) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ route('invitations.accept', ['token' => $inv->token]) }}"
                                       class="inline-flex items-center gap-1.5 rounded-xl px-4 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition-all">
                                        <iconify-icon icon="solar:check-circle-bold" width="14"></iconify-icon>
                                        {{ __('invitations.accept_button') }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Organizations -->
            <div id="organizations" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">{{ __('pages.profile.my_companies') }}</h2>
                        <p class="text-xs text-slate-500 mt-0.5">{{ __('pages.profile.companies_subtitle') }}</p>
                    </div>
                    <a href="{{ route('organizations.select', ['mode' => 'switch']) }}" class="text-xs font-semibold text-[var(--accent)] hover:text-slate-900 transition-colors bg-white px-3 py-1.5 rounded-lg border border-slate-200 shadow-sm hover:bg-slate-50">
                        {{ __('pages.profile.manage_change') }}
                    </a>
                </div>
                <div class="p-6">
                    <div class="grid gap-3 sm:grid-cols-2">
                        @forelse ($organizations as $org)
                            <div class="group flex items-center gap-4 rounded-xl border border-slate-200 p-4 transition-all hover:border-[var(--accent)] hover:bg-[var(--accent-soft)]/10">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-sm font-bold shadow-sm"
                                     style="background: {{ $org->primary_color ? 'color-mix(in srgb, '.$org->primary_color.' 15%, white)' : '#F3F4F6' }}; color: {{ $org->primary_color ?: '#4B5563' }};">
                                    {{ mb_strtoupper(mb_substr($org->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold text-slate-900 truncate">{{ $org->name }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1.5">
                                        <iconify-icon icon="solar:shield-user-linear" width="12"></iconify-icon>
                                        {{ ucfirst((string) $org->pivot->role) }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 rounded-xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center text-sm text-slate-500">
                                {{ __('pages.profile.no_organization') }}
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Activity History -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" x-data="{ open: true }">
                <button type="button" class="w-full px-6 py-4 flex items-center justify-between bg-slate-50/50 border-b border-slate-100 hover:bg-slate-100 transition-colors" @click="open = !open">
                    <div class="text-left">
                        <h2 class="text-base font-semibold text-slate-900">{{ __('pages.profile.recent_activity') }}</h2>
                        <p class="text-xs text-slate-500 mt-0.5">{{ __('pages.profile.recent_activity_subtitle') }}</p>
                    </div>
                    <iconify-icon :icon="open ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear'" class="text-slate-400" width="20"></iconify-icon>
                </button>

                <div x-show="open" x-collapse>
                    <div class="p-6">
                        <div class="flex flex-wrap gap-2 mb-6">
                            @php $filters = [['all', 'filter_all'], ['tickets', 'filter_tickets'], ['commentaires', 'filter_comments'], ['assignations', 'filter_assignations']]; @endphp
                            @foreach ($filters as [$key, $labelKey])
                                @php
                                    $isActive = $activityFilter === $key;
                                    $disabled = $key === 'commentaires';
                                @endphp
                                <a
                                    href="{{ route('profile', array_filter(['activity' => $key !== 'all' ? $key : null])) }}"
                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $isActive ? 'bg-slate-900 text-white shadow-md' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:text-slate-900' }} {{ $disabled ? 'pointer-events-none opacity-50' : '' }}"
                                >{{ __('pages.profile.' . $labelKey) }}</a>
                            @endforeach
                        </div>

                        <div class="relative pl-4 space-y-6 before:absolute before:left-[19px] before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-100">
                            @forelse ($events as $e)
                                <div class="relative pl-8">
                                    <div class="absolute left-0 top-1.5 h-2.5 w-2.5 rounded-full border-2 border-white shadow-sm" style="background: var(--accent);"></div>
                                    <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-1">
                                        <p class="text-sm font-medium text-slate-900">{{ $e['label'] }}</p>
                                        <span class="text-xs text-slate-400">{{ \Illuminate\Support\Carbon::parse($e['at'])->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1">
                                        <span class="font-mono text-slate-400">#{{ $e['ticket_id'] }}</span> · {{ $e['subject'] }}
                                    </p>
                                    <div class="mt-2">
                                        <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-1 text-[10px] font-medium text-slate-600 ring-1 ring-inset ring-slate-500/10">
                                            {{ $statusLabel($e['status']) }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="py-8 text-center text-sm text-slate-500 italic">
                                    {{ __('pages.profile.no_activity_found') }}
                                </div>
                            @endforelse
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-slate-100 text-center">
                            <a href="{{ route('profile.history') }}" class="text-sm font-semibold text-[var(--accent)] hover:text-slate-900 transition-colors inline-flex items-center gap-1">
                                {{ __('pages.profile.view_full_history') }}
                                <iconify-icon icon="solar:arrow-right-linear" width="16"></iconify-icon>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="space-y-8">
            <!-- Language -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-slate-900 mb-4">{{ __('pages.profile.language') }}</h2>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('locale.switch', 'fr') }}" 
                       class="flex items-center justify-center gap-2 rounded-xl border p-2 text-sm font-medium transition-all {{ $currentLocale === 'FR' ? 'border-[var(--accent)] bg-[var(--accent-soft)]/10 text-[var(--accent)]' : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50' }}">
                        <span class="text-lg">🇫🇷</span> {{ __('pages.profile.french') }}
                    </a>
                    <a href="{{ route('locale.switch', 'en') }}" 
                       class="flex items-center justify-center gap-2 rounded-xl border p-2 text-sm font-medium transition-all {{ $currentLocale === 'EN' ? 'border-[var(--accent)] bg-[var(--accent-soft)]/10 text-[var(--accent)]' : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50' }}">
                        <span class="text-lg">🇬🇧</span> {{ __('pages.profile.english') }}
                    </a>
                </div>
            </div>

            <!-- Security -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-slate-900 mb-4">{{ __('pages.profile.security') }}</h2>
                <div class="space-y-4">
                    <div class="rounded-xl bg-slate-50 p-4 border border-slate-100">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">{{ __('pages.profile.active_sessions') }}</h3>
                        <div class="space-y-3">
                            @foreach ($sessions as $s)
                                @php
                                    $isCurrent = (string) $s->id === (string) $currentSessionId;
                                    $dt = \Illuminate\Support\Carbon::createFromTimestamp((int) $s->last_activity);
                                @endphp
                                <div class="flex items-start gap-3 text-xs">
                                    <div class="mt-0.5">
                                        @if($isCurrent)
                                            <div class="h-2 w-2 rounded-full bg-emerald-500 ring-2 ring-emerald-100"></div>
                                        @else
                                            <div class="h-2 w-2 rounded-full bg-slate-300"></div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-medium text-slate-900">
                                            {{ $s->ip_address ?? __('pages.profile.unknown_ip') }}
                                            @if($isCurrent) <span class="text-emerald-600 ml-1">({{ __('pages.profile.current_session') }})</span> @endif
                                        </p>
                                        <p class="text-slate-500 truncate">{{ $s->user_agent }}</p>
                                        <p class="text-slate-400 mt-0.5">{{ $dt->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <form method="POST" action="{{ route('profile.sessions.logout_all') }}" class="mt-4 pt-4 border-t border-slate-200">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-white border border-slate-200 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-sm">
                                {{ __('pages.profile.logout_other_sessions') }}
                            </button>
                        </form>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">{{ __('pages.profile.password') }}</h3>
                        <livewire:profile.update-password-form />
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-red-50 rounded-2xl border border-red-100 p-6">
                <h2 class="text-sm font-semibold text-red-900 mb-2">{{ __('pages.profile.danger_zone') }}</h2>
                <p class="text-xs text-red-700 mb-4">{{ __('pages.profile.danger_zone_text') }}</p>
                <div x-data="{ open: false }">
                    <button @click="open = !open" type="button" class="text-xs font-bold text-red-600 hover:text-red-800 underline">
                        {{ __('pages.profile.delete_my_account') }}
                    </button>
                    <div x-show="open" x-collapse class="mt-4">
                        <livewire:profile.delete-user-form />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-manexo-app-layout>
