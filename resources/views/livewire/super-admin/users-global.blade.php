<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">{{ __('super_admin.users.title') }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ __('super_admin.users.subtitle') }}</p>
        </div>
        @if(auth()->user()->canPlatformAdminister())
            <button wire:click="openInviteModal" class="sa-btn-primary">
                <iconify-icon icon="solar:letter-bold" width="16"></iconify-icon>
                {{ __('platform_invitations.invite_button') }}
            </button>
        @endif
    </div>

    {{-- Session flash --}}
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Pending Invitations --}}
    @if(auth()->user()->canPlatformAdminister() && $pendingInvitations->count() > 0)
        <div class="rounded-2xl border border-indigo-200/60 bg-indigo-50/30 overflow-hidden">
            <button
                wire:click="$toggle('showPendingInvitations')"
                class="w-full flex items-center justify-between px-5 py-3 text-sm font-semibold text-indigo-800 hover:bg-indigo-50/50 transition-colors"
            >
                <div class="flex items-center gap-2">
                    <iconify-icon icon="solar:letter-opened-bold-duotone" width="18" class="text-indigo-500"></iconify-icon>
                    {{ __('platform_invitations.pending_title') }}
                    <span class="inline-flex items-center rounded-full bg-indigo-100 border border-indigo-200 px-2 py-0.5 text-[10px] font-bold text-indigo-700">
                        {{ $pendingInvitations->count() }}
                    </span>
                </div>
                <iconify-icon icon="{{ $showPendingInvitations ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear' }}" width="14" class="text-indigo-400"></iconify-icon>
            </button>

            @if($showPendingInvitations)
                <div class="border-t border-indigo-200/60 divide-y divide-indigo-100">
                    @foreach($pendingInvitations as $inv)
                        <div class="flex items-center justify-between px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <iconify-icon icon="solar:letter-bold" width="14" class="text-indigo-500"></iconify-icon>
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
                                wire:click="cancelInvitation({{ $inv->id }})"
                                wire:confirm="{{ __('platform_invitations.confirm_cancel') }}"
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

    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <iconify-icon icon="solar:magnifer-linear" width="16" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></iconify-icon>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('super_admin.users.search_placeholder') }}"
                class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-9 pr-4 text-sm text-slate-700 placeholder-slate-400 shadow-sm focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
            />
        </div>
        <select
            wire:model.live="statusFilter"
            class="rounded-xl border border-slate-200 bg-white py-2.5 px-4 text-sm text-slate-700 shadow-sm focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
        >
            <option value="">{{ __('super_admin.users.all_statuses') }}</option>
            <option value="active">{{ __('super_admin.users.status_active') }}</option>
            <option value="deactivated">{{ __('super_admin.users.status_deactivated') }}</option>
        </select>
    </div>

    <!-- Table -->
    <div class="rounded-2xl border border-slate-200/60 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.users.col_name') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.users.col_email') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.users.col_status') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.users.col_last_login') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.users.col_orgs') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.users.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($users as $user)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-slate-800">{{ $user->name }}</span>
                                    @if ($user->platform_role)
                                        @php
                                            $roleBadge = match($user->platform_role->value) {
                                                'super_admin' => 'bg-purple-50 border-purple-200 text-purple-700',
                                                'platform_admin' => 'bg-indigo-50 border-indigo-200 text-indigo-700',
                                                'platform_observer' => 'bg-sky-50 border-sky-200 text-sky-700',
                                                default => 'bg-slate-50 border-slate-200 text-slate-700',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-semibold {{ $roleBadge }}">
                                            {{ $user->platform_role->label() }}
                                        </span>
                                    @elseif ($user->is_super_admin)
                                        <span class="inline-flex items-center rounded-full bg-purple-50 border border-purple-200 px-2 py-0.5 text-[10px] font-semibold text-purple-700">
                                            {{ __('super_admin.users.super_admin_badge') }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-500">
                                {{ $user->email }}
                                @if (! $user->email_verified_at)
                                    <span class="ml-1 inline-flex items-center rounded-full bg-amber-50 border border-amber-200 px-1.5 py-0.5 text-[10px] font-semibold text-amber-700">
                                        {{ __('super_admin.users.not_verified') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @php
                                    $status = $user->status ?? 'active';
                                    $badge = match($status) {
                                        'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'deactivated' => 'bg-red-50 text-red-700 border-red-200',
                                        default => 'bg-slate-50 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $badge }}">
                                    {{ __('super_admin.users.status_' . $status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-500">
                                {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('d/m/Y H:i') : __('super_admin.users.never') }}
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-500">
                                @if ($user->organizations->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($user->organizations->take(3) as $org)
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-600">
                                                {{ $org->name }}
                                            </span>
                                        @endforeach
                                        @if ($user->organizations->count() > 3)
                                            <span class="text-xs text-slate-400">+{{ $user->organizations->count() - 3 }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-300">&mdash;</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @if(auth()->user()->canPlatformManage())
                                        @if (($user->status ?? 'active') === 'active')
                                            <button wire:click="suspendUser({{ $user->id }})" wire:confirm="{{ __('super_admin.users.suspend') }}?" class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-amber-600 hover:bg-amber-50 transition-colors">
                                                <iconify-icon icon="solar:pause-circle-bold" width="14"></iconify-icon>
                                                <span class="hidden sm:inline">{{ __('super_admin.users.suspend') }}</span>
                                            </button>
                                        @else
                                            <button wire:click="activateUser({{ $user->id }})" class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-emerald-600 hover:bg-emerald-50 transition-colors">
                                                <iconify-icon icon="solar:check-circle-bold" width="14"></iconify-icon>
                                                <span class="hidden sm:inline">{{ __('super_admin.users.activate') }}</span>
                                            </button>
                                        @endif

                                        @if (! $user->email_verified_at)
                                            <button wire:click="forceVerifyEmail({{ $user->id }})" class="sa-btn-secondary text-xs py-1.5 px-2.5">
                                                <iconify-icon icon="solar:verified-check-bold" width="14"></iconify-icon>
                                                <span class="hidden sm:inline">{{ __('super_admin.users.force_verify') }}</span>
                                            </button>
                                        @endif
                                    @endif

                                    @if(auth()->user()->canPlatformAdminister() && $user->hasPlatformAccess() && $user->id !== auth()->id())
                                        <button
                                            wire:click="revokePlatformRole({{ $user->id }})"
                                            wire:confirm="{{ __('platform_invitations.confirm_revoke') }}"
                                            class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors"
                                        >
                                            <iconify-icon icon="solar:shield-cross-bold" width="14"></iconify-icon>
                                            <span class="hidden sm:inline">{{ __('platform_invitations.revoke') }}</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-sm text-slate-400">
                                <iconify-icon icon="solar:users-group-rounded-bold-duotone" width="32" class="text-slate-300 mb-2"></iconify-icon>
                                <p>{{ __('super_admin.users.empty') }}</p>
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
    @if($showInviteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data x-init="$el.querySelector('input[type=email]')?.focus()">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="$set('showInviteModal', false)"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl ring-1 ring-slate-200/60 w-full max-w-md p-6 space-y-4">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <iconify-icon icon="solar:letter-bold-duotone" width="20" class="text-indigo-500"></iconify-icon>
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
                            class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-4 text-sm text-slate-700 placeholder-slate-400 shadow-sm focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
                        />
                        @error('inviteEmail')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('platform_invitations.role_label') }}</label>
                        <select
                            wire:model="inviteRole"
                            class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-4 text-sm text-slate-700 shadow-sm focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
                        >
                            <option value="platform_admin">{{ __('platform_invitations.role_platform_admin') }}</option>
                            <option value="platform_observer">{{ __('platform_invitations.role_platform_observer') }}</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button wire:click="$set('showInviteModal', false)" class="sa-btn-secondary">
                        {{ __('super_admin.cancel') }}
                    </button>
                    <button wire:click="sendInvitation" class="sa-btn-primary">
                        <iconify-icon icon="solar:plain-bold" width="16"></iconify-icon>
                        {{ __('platform_invitations.send_button') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
