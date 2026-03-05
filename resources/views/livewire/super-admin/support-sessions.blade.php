<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">{{ __('super_admin.support.title') }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ __('super_admin.support.subtitle') }}</p>
        </div>
        @if(auth()->user()->canPlatformManage())
        <button wire:click="openCreateModal" class="sa-btn-primary">
            <iconify-icon icon="solar:headphones-round-bold" width="16"></iconify-icon>
            {{ __('super_admin.support.start') }}
        </button>
        @endif
    </div>

    {{-- Session flash --}}
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <!-- Active Session Banner -->
    @if ($this->activeSession)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100">
                        <iconify-icon icon="solar:headphones-round-bold-duotone" width="20" class="text-amber-600"></iconify-icon>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-amber-800">{{ __('super_admin.support.active_banner') }}</p>
                        <p class="text-xs text-amber-600">
                            {{ __('super_admin.support.active_banner_org', ['org' => $this->activeSession->organization?->name]) }}
                            {{ __('super_admin.support.active_banner_expires', ['time' => $this->activeSession->expires_at->diffForHumans()]) }}
                        </p>
                    </div>
                </div>
                @if(auth()->user()->canPlatformManage())
                <button wire:click="endSession({{ $this->activeSession->id }})" class="rounded-xl bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700 transition-colors">
                    {{ __('super_admin.support.end') }}
                </button>
                @endif
            </div>
        </div>
    @endif

    <!-- History -->
    <div class="rounded-2xl border border-slate-200/60 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-700">{{ __('super_admin.support.history') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.support.col_admin') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.support.col_org') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.support.col_reason') }}</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.support.col_duration') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.support.col_started') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.support.col_status') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('super_admin.support.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($sessions as $session)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5 text-sm font-medium text-slate-700">{{ $session->user?->name ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-sm text-slate-600">{{ $session->organization?->name ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-sm text-slate-500 max-w-xs truncate">{{ $session->reason }}</td>
                            <td class="px-5 py-3.5 text-sm text-slate-500 text-center">{{ __('super_admin.support.minutes', ['count' => $session->duration_minutes]) }}</td>
                            <td class="px-5 py-3.5 text-sm text-slate-500 whitespace-nowrap">{{ $session->started_at->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-3.5">
                                @if ($session->isActive())
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">
                                        {{ __('super_admin.support.status_active') }}
                                    </span>
                                @elseif ($session->ended_at && $session->ended_at->eq($session->expires_at))
                                    <span class="inline-flex items-center rounded-full bg-amber-50 border border-amber-200 px-2.5 py-0.5 text-xs font-semibold text-amber-700">
                                        {{ __('super_admin.support.status_expired') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-slate-50 border border-slate-200 px-2.5 py-0.5 text-xs font-semibold text-slate-600">
                                        {{ __('super_admin.support.status_ended') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                @if ($session->isActive() && auth()->user()->canPlatformManage())
                                    <button wire:click="endSession({{ $session->id }})" class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">
                                        <iconify-icon icon="solar:stop-circle-bold" width="14"></iconify-icon>
                                        {{ __('super_admin.support.end') }}
                                    </button>
                                @else
                                    <span class="text-xs text-slate-400">
                                        {{ $session->ended_at?->format('d/m/Y H:i') ?? '—' }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400">
                                <iconify-icon icon="solar:headphones-round-bold-duotone" width="32" class="text-slate-300 mb-2"></iconify-icon>
                                <p>{{ __('super_admin.support.empty') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($sessions->hasPages())
            <div class="border-t border-slate-100 px-5 py-3">
                {{ $sessions->links() }}
            </div>
        @endif
    </div>

    <!-- Create Modal -->
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm" wire:keydown.escape="$set('showCreateModal', false)">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" @click.outside="$wire.set('showCreateModal', false)">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50">
                        <iconify-icon icon="solar:headphones-round-bold-duotone" width="20" class="text-indigo-600"></iconify-icon>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800">{{ __('super_admin.support.start_title') }}</h3>
                </div>

                <div class="space-y-4 mb-4">
                    <!-- Organization -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('super_admin.support.select_org') }}</label>
                        <select
                            wire:model="selectedOrgId"
                            class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-4 text-sm text-slate-700 shadow-sm focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
                        >
                            <option value="">{{ __('super_admin.support.select_org_placeholder') }}</option>
                            @foreach ($this->organizations as $org)
                                <option value="{{ $org->id }}">{{ $org->name }}</option>
                            @endforeach
                        </select>
                        @error('selectedOrgId') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Reason -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('super_admin.support.reason') }}</label>
                        <textarea
                            wire:model="reason"
                            rows="3"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 shadow-sm focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
                            placeholder="{{ __('super_admin.support.reason_placeholder') }}"
                        ></textarea>
                        @error('reason') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Duration -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('super_admin.support.duration') }}</label>
                        <select
                            wire:model="duration"
                            class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-4 text-sm text-slate-700 shadow-sm focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
                        >
                            <option value="15">{{ __('super_admin.support.duration_15') }}</option>
                            <option value="30">{{ __('super_admin.support.duration_30') }}</option>
                            <option value="60">{{ __('super_admin.support.duration_60') }}</option>
                        </select>
                        @error('duration') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <button wire:click="$set('showCreateModal', false)" class="sa-btn-secondary">
                        {{ __('super_admin.cancel') }}
                    </button>
                    <button wire:click="startSession" class="sa-btn-primary">
                        {{ __('super_admin.support.confirm_start') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
