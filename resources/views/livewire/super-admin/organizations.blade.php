<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">{{ __('super_admin.organizations.title') }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ __('super_admin.organizations.subtitle') }}</p>
        </div>
        @if(auth()->user()->canPlatformManage())
        <button wire:click="openCreateModal" class="sa-btn-primary">
            <iconify-icon icon="solar:add-circle-bold" width="16"></iconify-icon>
            {{ __('super_admin.organizations.create') }}
        </button>
        @endif
    </div>

    {{-- Session flash --}}
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <iconify-icon icon="solar:magnifer-linear" width="16" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></iconify-icon>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('super_admin.organizations.search_placeholder') }}"
                class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-9 pr-4 text-sm text-slate-700 placeholder-slate-400 shadow-sm focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
            />
        </div>
        <select
            wire:model.live="statusFilter"
            class="rounded-xl border border-slate-200 bg-white py-2.5 px-4 text-sm text-slate-700 shadow-sm focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
        >
            <option value="">{{ __('super_admin.organizations.all_statuses') }}</option>
            <option value="active">{{ __('super_admin.organizations.status_active') }}</option>
            <option value="suspended">{{ __('super_admin.organizations.status_suspended') }}</option>
            <option value="disabled">{{ __('super_admin.organizations.status_disabled') }}</option>
        </select>
    </div>

    <!-- Table -->
    <div class="rounded-2xl border border-slate-200/60 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.organizations.col_name') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.organizations.col_slug') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.organizations.col_status') }}</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.organizations.col_members') }}</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.organizations.col_tickets') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.organizations.col_created') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.organizations.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($organizations as $org)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('platform-admin.organizations.show', $org) }}" class="font-medium text-slate-800 hover:text-indigo-600 transition-colors">
                                    {{ $org->name }}
                                </a>
                                @if ($org->isArchived())
                                    <span class="ml-1 inline-flex items-center rounded-full bg-slate-100 border border-slate-200 px-1.5 py-0.5 text-[10px] font-semibold text-slate-500">
                                        {{ __('super_admin.organizations.status_archived') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-500">{{ $org->slug }}</td>
                            <td class="px-5 py-3.5">
                                @php
                                    $status = $org->status ?? 'active';
                                    $badge = match($status) {
                                        'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'suspended' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'disabled' => 'bg-red-50 text-red-700 border-red-200',
                                        default => 'bg-slate-50 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $badge }}">
                                    {{ __('super_admin.organizations.status_' . $status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center text-sm text-slate-600">{{ $org->memberships_count }}</td>
                            <td class="px-5 py-3.5 text-center text-sm text-slate-600">{{ $org->tickets_count }}</td>
                            <td class="px-5 py-3.5 text-sm text-slate-500">{{ $org->created_at?->format('d/m/Y') }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('platform-admin.organizations.show', $org) }}" class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-100 transition-colors" title="{{ __('super_admin.organizations.view') }}">
                                        <iconify-icon icon="solar:eye-bold" width="14"></iconify-icon>
                                    </a>

                                    @if(auth()->user()->canPlatformManage())
                                        @if (($org->status ?? 'active') === 'active')
                                            <button wire:click="openEnterModal({{ $org->id }})" class="sa-btn-secondary text-xs py-1.5 px-2.5">
                                                <iconify-icon icon="solar:login-bold" width="14"></iconify-icon>
                                                <span class="hidden sm:inline">{{ __('super_admin.organizations.enter') }}</span>
                                            </button>
                                        @endif

                                        <a href="{{ route('platform-admin.support-sessions', ['org' => $org->id]) }}" class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-violet-600 hover:bg-violet-50 transition-colors">
                                            <iconify-icon icon="solar:headphones-round-bold" width="14"></iconify-icon>
                                            <span class="hidden sm:inline">{{ __('super_admin.organizations.support') }}</span>
                                        </a>

                                        @if (($org->status ?? 'active') !== 'active')
                                            <button wire:click="activateOrg({{ $org->id }})" wire:confirm="{{ __('super_admin.organizations.confirm_activate') }}" class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-emerald-600 hover:bg-emerald-50 transition-colors">
                                                <iconify-icon icon="solar:check-circle-bold" width="14"></iconify-icon>
                                                <span class="hidden sm:inline">{{ __('super_admin.organizations.activate') }}</span>
                                            </button>
                                        @endif

                                        @if (($org->status ?? 'active') !== 'suspended')
                                            <button wire:click="openSuspendModal({{ $org->id }})" class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-amber-600 hover:bg-amber-50 transition-colors">
                                                <iconify-icon icon="solar:pause-circle-bold" width="14"></iconify-icon>
                                                <span class="hidden sm:inline">{{ __('super_admin.organizations.suspend') }}</span>
                                            </button>
                                        @endif

                                        @if (! $org->isArchived())
                                            <button wire:click="openArchiveModal({{ $org->id }})" class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-100 transition-colors">
                                                <iconify-icon icon="solar:archive-bold" width="14"></iconify-icon>
                                                <span class="hidden sm:inline">{{ __('super_admin.organizations.archive') }}</span>
                                            </button>
                                        @endif

                                        @if (($org->status ?? 'active') !== 'disabled')
                                            <button wire:click="openDisableModal({{ $org->id }})" class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">
                                                <iconify-icon icon="solar:close-circle-bold" width="14"></iconify-icon>
                                                <span class="hidden sm:inline">{{ __('super_admin.organizations.disable') }}</span>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400">
                                <iconify-icon icon="solar:buildings-bold-duotone" width="32" class="text-slate-300 mb-2"></iconify-icon>
                                <p>{{ __('super_admin.organizations.empty') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($organizations->hasPages())
            <div class="border-t border-slate-100 px-5 py-3">
                {{ $organizations->links() }}
            </div>
        @endif
    </div>

    <!-- Create Modal -->
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm" wire:keydown.escape="$set('showCreateModal', false)">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" @click.outside="$wire.set('showCreateModal', false)">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50">
                        <iconify-icon icon="solar:buildings-bold-duotone" width="20" class="text-indigo-600"></iconify-icon>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800">{{ __('super_admin.organizations.create_title') }}</h3>
                </div>

                <div class="space-y-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('super_admin.organizations.create_name') }}</label>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="createName"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 shadow-sm focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
                        />
                        @error('createName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('super_admin.organizations.create_slug') }}</label>
                        <input
                            type="text"
                            wire:model="createSlug"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 shadow-sm focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
                        />
                        @error('createSlug') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('super_admin.organizations.create_owner_email') }}</label>
                        <input
                            type="email"
                            wire:model="createOwnerEmail"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 shadow-sm focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
                        />
                        @error('createOwnerEmail') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <button wire:click="$set('showCreateModal', false)" class="sa-btn-secondary">
                        {{ __('super_admin.cancel') }}
                    </button>
                    <button wire:click="confirmCreate" class="sa-btn-primary">
                        {{ __('super_admin.organizations.create') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Archive Modal -->
    @if ($showArchiveModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm" wire:keydown.escape="$set('showArchiveModal', false)">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" @click.outside="$wire.set('showArchiveModal', false)">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100">
                        <iconify-icon icon="solar:archive-bold-duotone" width="20" class="text-slate-600"></iconify-icon>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800">{{ __('super_admin.organizations.archive_title') }}</h3>
                </div>
                <p class="text-sm text-slate-500 mb-4">{{ __('super_admin.organizations.archive_desc') }}</p>
                <div class="flex justify-end gap-2">
                    <button wire:click="$set('showArchiveModal', false)" class="sa-btn-secondary">
                        {{ __('super_admin.cancel') }}
                    </button>
                    <button wire:click="confirmArchive" class="rounded-xl bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 transition-colors">
                        {{ __('super_admin.organizations.confirm_archive') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Suspend Modal -->
    @if ($showSuspendModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm" wire:keydown.escape="$set('showSuspendModal', false)">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" @click.outside="$wire.set('showSuspendModal', false)">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50">
                        <iconify-icon icon="solar:pause-circle-bold-duotone" width="20" class="text-amber-600"></iconify-icon>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800">{{ __('super_admin.organizations.suspend_modal_title') }}</h3>
                </div>

                <p class="text-sm text-slate-500 mb-4">{{ __('super_admin.organizations.suspend_modal_desc') }}</p>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('super_admin.organizations.suspend_reason') }}</label>
                    <textarea
                        wire:model="suspendReason"
                        rows="3"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 shadow-sm focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
                        placeholder="{{ __('super_admin.organizations.suspend_reason_placeholder') }}"
                    ></textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <button wire:click="$set('showSuspendModal', false)" class="sa-btn-secondary">
                        {{ __('super_admin.cancel') }}
                    </button>
                    <button wire:click="confirmSuspend" class="rounded-xl bg-amber-500 px-4 py-2 text-sm font-medium text-white hover:bg-amber-600 transition-colors">
                        {{ __('super_admin.organizations.confirm_suspend') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Enter organization modal -->
    @if ($showEnterModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm" wire:keydown.escape="$set('showEnterModal', false)">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" @click.outside="$wire.set('showEnterModal', false)">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50">
                        <iconify-icon icon="solar:login-bold-duotone" width="20" class="text-indigo-600"></iconify-icon>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800">{{ __('super_admin.organizations.enter') }}</h3>
                </div>
                <p class="text-sm text-slate-500 mb-4">{{ __('super_admin.organizations.confirm_enter') }}</p>
                <div class="flex justify-end gap-2">
                    <button wire:click="$set('showEnterModal', false)" class="sa-btn-secondary">
                        {{ __('super_admin.cancel') }}
                    </button>
                    <button wire:click="confirmEnter" class="sa-btn-primary">
                        {{ __('super_admin.organizations.enter') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Activate organization modal -->
    @if ($showActivateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm" wire:keydown.escape="$set('showActivateModal', false)">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" @click.outside="$wire.set('showActivateModal', false)">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50">
                        <iconify-icon icon="solar:check-circle-bold-duotone" width="20" class="text-emerald-600"></iconify-icon>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800">{{ __('super_admin.organizations.activate') }}</h3>
                </div>
                <p class="text-sm text-slate-500 mb-4">{{ __('super_admin.organizations.confirm_activate') }}</p>
                <div class="flex justify-end gap-2">
                    <button wire:click="$set('showActivateModal', false)" class="sa-btn-secondary">
                        {{ __('super_admin.cancel') }}
                    </button>
                    <button wire:click="confirmActivate" class="rounded-xl bg-emerald-500 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-600 transition-colors">
                        {{ __('super_admin.organizations.activate') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Disable organization modal -->
    @if ($showDisableModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm" wire:keydown.escape="$set('showDisableModal', false)">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" @click.outside="$wire.set('showDisableModal', false)">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50">
                        <iconify-icon icon="solar:close-circle-bold-duotone" width="20" class="text-red-600"></iconify-icon>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800">{{ __('super_admin.organizations.disable') }}</h3>
                </div>
                <p class="text-sm text-slate-500 mb-4">{{ __('super_admin.organizations.confirm_disable') }}</p>
                <div class="flex justify-end gap-2">
                    <button wire:click="$set('showDisableModal', false)" class="sa-btn-secondary">
                        {{ __('super_admin.cancel') }}
                    </button>
                    <button wire:click="confirmDisable" class="rounded-xl bg-red-500 px-4 py-2 text-sm font-medium text-white hover:bg-red-600 transition-colors">
                        {{ __('super_admin.organizations.disable') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
