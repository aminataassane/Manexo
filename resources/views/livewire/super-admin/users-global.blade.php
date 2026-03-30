<div class="space-y-6 pb-12">
    {{-- Header --}}
    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ __('super_admin.users.title') }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ __('super_admin.users.subtitle') }}</p>
        </div>
        @if(auth()->user()->canPlatformAdminister())
            <button wire:click="openInviteModal" class="sa-btn-primary self-start">
                <iconify-icon icon="solar:letter-bold" width="16"></iconify-icon>
                {{ __('platform_invitations.invite_button') }}
            </button>
        @endif
    </header>

    {{-- Session flash --}}
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-center gap-2">
            <iconify-icon icon="solar:check-circle-bold" width="18"></iconify-icon>
            {{ session('success') }}
        </div>
    @endif

    {{-- Pending Invitations --}}
    @if(auth()->user()->canPlatformAdminister() && $pendingInvitations->count() > 0)
        <div class="rounded-2xl border border-[#005F02]/20 bg-[#F2E3BB]/20 overflow-hidden">
            <button
                wire:click="$toggle('showPendingInvitations')"
                class="w-full flex items-center justify-between px-5 py-3 text-sm font-semibold text-[#005F02] hover:bg-[#F2E3BB]/20 transition-colors"
            >
                <div class="flex items-center gap-2">
                    <iconify-icon icon="solar:letter-opened-bold-duotone" width="18" class="text-[#005F02]"></iconify-icon>
                    {{ __('platform_invitations.pending_title') }}
                    <span class="inline-flex items-center rounded-full bg-[#F2E3BB]/30 border border-[#005F02]/20 px-2 py-0.5 text-[10px] font-bold text-[#005F02]">
                        {{ $pendingInvitations->count() }}
                    </span>
                </div>
                <iconify-icon icon="{{ $showPendingInvitations ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear' }}" width="14" class="text-[#005F02]/70"></iconify-icon>
            </button>

            @if($showPendingInvitations)
                <div class="border-t border-[#005F02]/20 divide-y divide-[#005F02]/10">
                    @foreach($pendingInvitations as $inv)
                        <div class="flex items-center justify-between px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-full bg-[#F2E3BB]/30 flex items-center justify-center">
                                    <iconify-icon icon="solar:letter-bold" width="14" class="text-[#005F02]"></iconify-icon>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-slate-800">{{ $inv->email }}</div>
                                    <div class="text-xs text-slate-500">
                                        {{ $inv->platform_role->label() }}
                                        &middot; {{ __('platform_invitations.invited_by', ['name' => $inv->inviter?->name ?? '—']) }}
                                        &middot; {{ __('platform_invitations.expires_in', ['time' => $inv->expires_at->diffForHumans()]) }}
                                    </div>
                                </div>
                            </div>
                            <button
                                @click="$dispatch('confirm-action', { title: 'Annuler', message: 'Annuler cette invitation ?', confirmLabel: 'Annuler', variant: 'danger', onConfirm: () => $wire.cancelInvitation({{ $inv->id }}) })"
                                class="sa-btn-ghost text-red-600 hover:!bg-red-50"
                            >
                                <iconify-icon icon="solar:close-circle-bold" width="14"></iconify-icon>
                                {{ __('super_admin.cancel') }}
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    @php
        $globalUsersStatusOptions = [
            ['value' => '', 'label' => __('super_admin.users.all_statuses')],
            ['value' => 'active', 'label' => __('super_admin.users.status_active')],
            ['value' => 'deactivated', 'label' => __('super_admin.users.status_deactivated')],
        ];
        $globalUsersStatusLabel = collect($globalUsersStatusOptions)->firstWhere('value', (string) ($statusFilter ?? ''))['label'] ?? __('super_admin.users.all_statuses');
        $platformInviteRoleOptions = [
            ['value' => 'platform_admin', 'label' => __('platform_invitations.role_platform_admin')],
            ['value' => 'platform_observer', 'label' => __('platform_invitations.role_platform_observer')],
        ];
        $platformInviteRoleLabel = collect($platformInviteRoleOptions)->firstWhere('value', (string) ($inviteRole ?? ''))['label'] ?? '';
    @endphp
    {{-- Toolbar --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <iconify-icon icon="solar:magnifer-linear" width="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></iconify-icon>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('super_admin.users.search_placeholder') }}"
                class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-700 placeholder-slate-400 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all"
            />
        </div>
        <x-select-input
            :options="$globalUsersStatusOptions"
            :label="$globalUsersStatusLabel"
            :selected-value="$statusFilter ?? ''"
            wire:model.live="statusFilter"
        />
    </div>

    {{-- Table + expandable detail panel --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden" x-data="{ expanded: null }">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80">
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.users.col_name') }}</th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.users.col_email') }}</th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.users.col_status') }}</th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.users.col_orgs') }}</th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500 w-[130px]">{{ __('super_admin.users.col_last_login') }}</th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500 w-[40px]"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        @php
                            $status = $user->status ?? 'active';
                            $statusConf = match($status) {
                                'active'      => ['dot' => 'bg-emerald-500', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                                'deactivated' => ['dot' => 'bg-red-400',     'badge' => 'bg-red-50 text-red-700 border-red-200'],
                                default       => ['dot' => 'bg-slate-400',   'badge' => 'bg-slate-50 text-slate-700 border-slate-200'],
                            };

                            $roleBadge = null;
                            if ($user->platform_role) {
                                $roleBadge = match($user->platform_role->value) {
                                    'super_admin'       => 'bg-purple-50 border-purple-200 text-purple-700',
                                    'platform_admin'    => 'bg-[#F2E3BB]/30 border-[#005F02]/20 text-[#005F02]',
                                    'platform_observer' => 'bg-sky-50 border-sky-200 text-sky-700',
                                    default             => null,
                                };
                            } elseif ($user->is_super_admin) {
                                $roleBadge = 'bg-purple-50 border-purple-200 text-purple-700';
                            }

                            $initials = collect(explode(' ', $user->name))->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->join('');
                            $avatarColors = ['bg-blue-100 text-blue-700', 'bg-emerald-100 text-emerald-700', 'bg-violet-100 text-violet-700', 'bg-amber-100 text-amber-700', 'bg-rose-100 text-rose-700', 'bg-teal-100 text-teal-700'];
                            $avatarColor = $avatarColors[$user->id % count($avatarColors)];
                        @endphp

                        {{-- Main row --}}
                        <tr
                            class="border-b border-slate-100 transition-colors cursor-pointer"
                            :class="expanded === {{ $user->id }} ? 'bg-slate-50' : 'hover:bg-slate-50/50'"
                            @click="expanded = expanded === {{ $user->id }} ? null : {{ $user->id }}"
                        >
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="relative shrink-0">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full text-[10px] font-bold {{ $avatarColor }}">
                                            {{ $initials }}
                                        </div>
                                        <span class="absolute -bottom-0.5 -right-0.5 h-2.5 w-2.5 rounded-full {{ $statusConf['dot'] }} ring-2 ring-white"></span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-sm font-medium text-slate-800 truncate">{{ $user->name }}</span>
                                            @if ($roleBadge)
                                                <span class="inline-flex items-center rounded-md border px-1.5 py-0.5 text-[9px] font-semibold {{ $roleBadge }}">
                                                    {{ $user->platform_role?->label() ?? __('super_admin.users.super_admin_badge') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-sm text-slate-500">{{ $user->email }}</span>
                                @if (! $user->email_verified_at)
                                    <span class="ml-1 inline-flex items-center rounded-md bg-amber-50 border border-amber-200 px-1.5 py-0.5 text-[9px] font-semibold text-amber-700">
                                        {{ __('super_admin.users.not_verified') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 rounded-full border px-2 py-0.5 text-[11px] font-semibold {{ $statusConf['badge'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $statusConf['dot'] }}"></span>
                                    {{ __('super_admin.users.status_' . $status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-sm text-slate-500">
                                @if ($user->organizations->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($user->organizations->take(2) as $org)
                                            <span class="inline-flex items-center rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium text-slate-600">
                                                {{ Str::limit($org->name, 15) }}
                                            </span>
                                        @endforeach
                                        @if ($user->organizations->count() > 2)
                                            <span class="text-[10px] text-slate-400 font-medium">+{{ $user->organizations->count() - 2 }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-300">&mdash;</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                @if ($user->last_login_at)
                                    <div class="text-xs text-slate-600">{{ \Carbon\Carbon::parse($user->last_login_at)->format('d/m/Y') }}</div>
                                    <div class="text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($user->last_login_at)->format('H:i') }}</div>
                                @else
                                    <span class="text-xs text-slate-300">{{ __('super_admin.users.never') }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                <div
                                    class="inline-flex items-center justify-center h-6 w-6 rounded-md transition-colors"
                                    :class="expanded === {{ $user->id }} ? 'bg-[#005F02]/10 text-[#005F02]' : 'text-slate-300'"
                                >
                                    <iconify-icon
                                        :icon="expanded === {{ $user->id }} ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear'"
                                        width="14"
                                    ></iconify-icon>
                                </div>
                            </td>
                        </tr>

                        {{-- Detail panel --}}
                        <tr x-show="expanded === {{ $user->id }}" x-cloak>
                            <td colspan="6" class="px-0 py-0">
                                <div
                                    x-show="expanded === {{ $user->id }}"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="border-b border-slate-100 bg-slate-50/50 px-5 py-5"
                                >
                                    <div class="flex flex-col lg:flex-row gap-6">
                                        {{-- User info --}}
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-3">
                                                <iconify-icon icon="solar:user-id-bold-duotone" width="16" class="text-[#005F02]"></iconify-icon>
                                                <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider">{{ __('super_admin.audit.col_details') }}</span>
                                            </div>
                                            <div class="rounded-xl bg-white border border-slate-200 divide-y divide-slate-100 overflow-hidden">
                                                <div class="flex items-baseline gap-4 px-4 py-2.5 text-xs">
                                                    <span class="shrink-0 w-[100px] font-medium text-slate-400">{{ __('super_admin.users.col_name') }}</span>
                                                    <span class="text-slate-700 font-medium">{{ $user->name }}</span>
                                                </div>
                                                <div class="flex items-baseline gap-4 px-4 py-2.5 text-xs">
                                                    <span class="shrink-0 w-[100px] font-medium text-slate-400">{{ __('super_admin.users.col_email') }}</span>
                                                    <span class="text-slate-700">{{ $user->email }}</span>
                                                    @if (! $user->email_verified_at)
                                                        <span class="inline-flex items-center rounded-md bg-amber-50 border border-amber-200 px-1.5 py-0.5 text-[9px] font-semibold text-amber-700">{{ __('super_admin.users.not_verified') }}</span>
                                                    @endif
                                                </div>
                                                <div class="flex items-baseline gap-4 px-4 py-2.5 text-xs">
                                                    <span class="shrink-0 w-[100px] font-medium text-slate-400">{{ __('super_admin.users.col_status') }}</span>
                                                    <span class="inline-flex items-center gap-1.5 rounded-full border px-2 py-0.5 text-[10px] font-semibold {{ $statusConf['badge'] }}">
                                                        <span class="h-1.5 w-1.5 rounded-full {{ $statusConf['dot'] }}"></span>
                                                        {{ __('super_admin.users.status_' . $status) }}
                                                    </span>
                                                </div>
                                                @if ($user->platform_role || $user->is_super_admin)
                                                    <div class="flex items-baseline gap-4 px-4 py-2.5 text-xs">
                                                        <span class="shrink-0 w-[100px] font-medium text-slate-400">{{ __('platform_invitations.role_label') }}</span>
                                                        <span class="text-slate-700">{{ $user->platform_role?->label() ?? __('super_admin.users.super_admin_badge') }}</span>
                                                    </div>
                                                @endif
                                                <div class="flex items-baseline gap-4 px-4 py-2.5 text-xs">
                                                    <span class="shrink-0 w-[100px] font-medium text-slate-400">{{ __('super_admin.users.col_last_login') }}</span>
                                                    <span class="text-slate-700">
                                                        {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('d/m/Y H:i') . ' — ' . \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : __('super_admin.users.never') }}
                                                    </span>
                                                </div>
                                                @if ($user->organizations->count() > 0)
                                                    <div class="flex items-start gap-4 px-4 py-2.5 text-xs">
                                                        <span class="shrink-0 w-[100px] font-medium text-slate-400 pt-0.5">{{ __('super_admin.users.col_orgs') }}</span>
                                                        <div class="flex flex-wrap gap-1.5">
                                                            @foreach ($user->organizations as $org)
                                                                <span class="inline-flex items-center rounded-md bg-slate-100 border border-slate-200 px-2 py-0.5 text-[10px] font-medium text-slate-600">
                                                                    {{ $org->name }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Actions panel --}}
                                        <div class="lg:w-[220px] shrink-0">
                                            <div class="flex items-center gap-2 mb-3">
                                                <iconify-icon icon="solar:settings-bold-duotone" width="16" class="text-[#005F02]"></iconify-icon>
                                                <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider">{{ __('super_admin.users.col_actions') }}</span>
                                            </div>
                                            <div class="space-y-1.5" @click.stop>
                                                @if(auth()->user()->canPlatformManage())
                                                    @if ($status === 'active')
                                                        <button
                                                            @click="$dispatch('confirm-action', { title: 'Suspendre', message: 'Suspendre cet utilisateur ?', confirmLabel: 'Suspendre', variant: 'danger', onConfirm: () => $wire.suspendUser({{ $user->id }}) })"
                                                            class="flex w-full items-center gap-2.5 rounded-xl px-3.5 py-2.5 text-xs font-medium text-amber-700 bg-white border border-slate-200 hover:bg-amber-50 hover:border-amber-200 transition-colors"
                                                        >
                                                            <iconify-icon icon="solar:pause-circle-bold-duotone" width="16" class="text-amber-500"></iconify-icon>
                                                            {{ __('super_admin.users.suspend') }}
                                                        </button>
                                                    @else
                                                        <button
                                                            wire:click="activateUser({{ $user->id }})"
                                                            class="flex w-full items-center gap-2.5 rounded-xl px-3.5 py-2.5 text-xs font-medium text-emerald-700 bg-white border border-slate-200 hover:bg-emerald-50 hover:border-emerald-200 transition-colors"
                                                        >
                                                            <iconify-icon icon="solar:check-circle-bold-duotone" width="16" class="text-emerald-500"></iconify-icon>
                                                            {{ __('super_admin.users.activate') }}
                                                        </button>
                                                    @endif

                                                    @if (! $user->email_verified_at)
                                                        <button
                                                            wire:click="forceVerifyEmail({{ $user->id }})"
                                                            class="flex w-full items-center gap-2.5 rounded-xl px-3.5 py-2.5 text-xs font-medium text-teal-700 bg-white border border-slate-200 hover:bg-teal-50 hover:border-teal-200 transition-colors"
                                                        >
                                                            <iconify-icon icon="solar:verified-check-bold-duotone" width="16" class="text-teal-500"></iconify-icon>
                                                            {{ __('super_admin.users.force_verify') }}
                                                        </button>
                                                    @endif
                                                @endif

                                                @if(auth()->user()->canPlatformAdminister() && $user->hasPlatformAccess() && $user->id !== auth()->id())
                                                    {{-- Role change --}}
                                                    <div class="pt-2 mt-2 border-t border-slate-200">
                                                        <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1.5 px-1">{{ __('platform_invitations.change_role') }}</div>
                                                        @foreach(\App\Enums\PlatformRole::cases() as $role)
                                                            @php
                                                                $isCurrent = $user->platform_role === $role || ($role === \App\Enums\PlatformRole::SuperAdmin && $user->is_super_admin && !$user->platform_role);
                                                            @endphp
                                                            <button
                                                                wire:click="changePlatformRole({{ $user->id }}, '{{ $role->value }}')"
                                                                class="flex w-full items-center gap-2 rounded-lg px-3 py-1.5 text-xs transition-colors {{ $isCurrent ? 'bg-[#005F02]/5 text-[#005F02] font-semibold' : 'text-slate-600 hover:bg-slate-100' }}"
                                                                {{ $isCurrent ? 'disabled' : '' }}
                                                            >
                                                                @if($isCurrent)
                                                                    <iconify-icon icon="solar:check-circle-bold" width="13" class="text-[#005F02]"></iconify-icon>
                                                                @else
                                                                    <iconify-icon icon="solar:shield-linear" width="13" class="text-slate-400"></iconify-icon>
                                                                @endif
                                                                {{ $role->label() }}
                                                            </button>
                                                        @endforeach
                                                    </div>

                                                    {{-- Revoke --}}
                                                    <div class="pt-2 mt-2 border-t border-slate-200">
                                                        <button
                                                            @click="$dispatch('confirm-action', { title: 'Révoquer', message: 'Révoquer l\u0027accès plateforme de cet utilisateur ?', confirmLabel: 'Révoquer', variant: 'danger', onConfirm: () => $wire.revokePlatformRole({{ $user->id }}) })"
                                                            class="flex w-full items-center gap-2.5 rounded-xl px-3.5 py-2.5 text-xs font-medium text-red-600 bg-white border border-slate-200 hover:bg-red-50 hover:border-red-200 transition-colors"
                                                        >
                                                            <iconify-icon icon="solar:shield-cross-bold-duotone" width="16" class="text-red-500"></iconify-icon>
                                                            {{ __('platform_invitations.revoke') }}
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <iconify-icon icon="solar:users-group-rounded-bold-duotone" width="44" class="mb-3 text-slate-300"></iconify-icon>
                                    <p class="text-sm font-medium">{{ __('super_admin.users.empty') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="border-t border-slate-100 px-5 py-3">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- Invite Modal --}}
    <div x-data="{ open: $wire.$entangle('showInviteModal') }" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div
            x-show="open"
            x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="open = false"
        ></div>
        <div
            x-show="open"
            x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="relative bg-white rounded-2xl shadow-2xl ring-1 ring-slate-200/60 w-full max-w-md p-6 space-y-4"
            @click.stop
            x-init="$watch('open', v => { if (v) $nextTick(() => $el.querySelector('input[type=email]')?.focus()) })"
        >
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <iconify-icon icon="solar:letter-bold-duotone" width="20" class="text-[#005F02]"></iconify-icon>
                {{ __('platform_invitations.invite_title') }}
            </h3>
            <p class="text-sm text-slate-500">{{ __('platform_invitations.invite_desc') }}</p>

            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('platform_invitations.email_label') }}</label>
                    <input
                        type="email"
                        wire:model="inviteEmail"
                        placeholder="{{ __('platform_invitations.email_placeholder') }}"
                        class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-4 text-sm text-slate-700 placeholder-slate-400 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all"
                    />
                    @error('inviteEmail')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('platform_invitations.role_label') }}</label>
                    <x-select-input
                        :options="$platformInviteRoleOptions"
                        :label="$platformInviteRoleLabel"
                        :selected-value="$inviteRole"
                        wire:model="inviteRole"
                    />
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button @click="open = false" class="sa-btn-secondary">
                    {{ __('super_admin.cancel') }}
                </button>
                <button wire:click="sendInvitation" class="sa-btn-primary">
                    <iconify-icon icon="solar:plain-bold" width="16"></iconify-icon>
                    {{ __('platform_invitations.send_button') }}
                </button>
            </div>
        </div>
    </div>
</div>
