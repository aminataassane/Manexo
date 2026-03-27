@php
    $statusLabels = [
        'open' => __('tickets.status.open'),
        'in_progress' => __('tickets.status.in_progress'),
        'pending' => __('tickets.status.pending'),
        'resolved' => __('tickets.status.resolved'),
        'closed' => __('tickets.status.closed'),
    ];
    $statusPill = [
        'open' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200'],
        'in_progress' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200'],
        'pending' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'border' => 'border-violet-200'],
        'resolved' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200'],
        'closed' => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200'],
    ];
    $isActive = fn (string $k) => ($viewKey ?? 'all') === $k;
    $chipBase = 'shrink-0 px-3 py-1.5 rounded-full text-[12px] font-semibold transition-all whitespace-nowrap cursor-pointer';
    $chipActive = 'text-white shadow-sm';
    $chipInactive = 'text-[#6B7280] bg-[#F3F4F6] hover:bg-[#E5E7EB]';
@endphp

<div
    class="messaging-full-bleed"
    x-data="{
        showNewDiscussionModal: false,
        showNewGroupModal: false,
        openDiscussion() {
            this.showNewDiscussionModal = true;
            $wire.openNewDiscussionModal();
        },
        openGroup() {
            this.showNewGroupModal = true;
            $wire.openNewGroupModal();
        },
        closeDiscussion() {
            this.showNewDiscussionModal = false;
            $wire.closeNewDiscussionModal();
        },
        closeGroup() {
            this.showNewGroupModal = false;
            $wire.closeNewGroupModal();
        }
    }"
>
    @if($loadStage >= 2)
    <div class="messaging-shell">
        {{-- ═══ SIDEBAR ═══ --}}
        <div class="messaging-sidebar {{ ($selectedThread ?? null) || ($selectedTicket ?? null) ? 'hidden md:flex' : 'flex' }}">
            {{-- Sidebar header --}}
            <div class="shrink-0 px-4 py-3 flex items-center justify-between">
                <h1 class="text-lg font-bold text-[#111827] tracking-tight">{{ __('pages.discussions.title') }}</h1>
                <div class="flex items-center gap-1">
                    <button type="button" @click="openDiscussion()" class="h-8 w-8 rounded-lg text-[#6B7280] hover:bg-[#F3F4F6] hover:text-[var(--accent)] transition-colors inline-flex items-center justify-center" title="{{ __('pages.discussions.new_discussion') }}">
                        <iconify-icon icon="solar:user-plus-linear" width="18"></iconify-icon>
                    </button>
                    <button type="button" @click="openGroup()" class="h-8 w-8 rounded-lg text-[#6B7280] hover:bg-[#F3F4F6] hover:text-[var(--accent)] transition-colors inline-flex items-center justify-center" title="{{ __('pages.discussions.create_group') }}">
                        <iconify-icon icon="solar:users-group-two-rounded-linear" width="18"></iconify-icon>
                    </button>
                </div>
            </div>

            {{-- Search --}}
            <div class="shrink-0 px-3 pb-2">
                <div class="relative">
                    <iconify-icon icon="solar:magnifer-linear" class="absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF]" width="15"></iconify-icon>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('pages.discussions.search') }}"
                        class="w-full h-9 pl-9 pr-3 text-[13px] text-[#111827] placeholder:text-[#9CA3AF] bg-[#F1F5F9] border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-[var(--accent)]/20"
                    />
                </div>
            </div>

            {{-- Scope tabs --}}
            <div class="shrink-0 px-3 pb-2">
                <div class="flex items-center gap-1 rounded-xl bg-[#F1F5F9] p-1">
                    <button type="button" wire:click="setScope('threads')"
                        class="flex-1 rounded-lg px-3 py-1.5 text-[12px] font-bold transition-all text-center {{ ($scope ?? 'tickets') === 'threads' ? 'bg-white text-[#111827] shadow-sm' : 'text-[#6B7280] hover:text-[#111827]' }}">
                        <iconify-icon icon="solar:chat-round-dots-linear" width="14" class="mr-1 align-[-2px]"></iconify-icon>
                        {{ __('pages.discussions.conversations') }}
                    </button>
                    <button type="button" wire:click="setScope('tickets')"
                        class="flex-1 rounded-lg px-3 py-1.5 text-[12px] font-bold transition-all text-center {{ ($scope ?? 'tickets') === 'tickets' ? 'bg-white text-[#111827] shadow-sm' : 'text-[#6B7280] hover:text-[#111827]' }}">
                        <iconify-icon icon="solar:ticket-linear" width="14" class="mr-1 align-[-2px]"></iconify-icon>
                        {{ __('menu.tickets') }}
                    </button>
                </div>
            </div>

            {{-- Filter chips --}}
            <div class="shrink-0 px-3 pb-2 overflow-x-auto custom-scrollbar">
                <div class="flex items-center gap-1.5">
                    @if(($scope ?? 'tickets') === 'threads')
                        <button type="button" wire:click="setView('direct')" class="{{ $chipBase }} {{ $isActive('direct') ? $chipActive : $chipInactive }}" @if($isActive('direct')) style="background-color: var(--accent);" @endif>
                            {{ __('pages.discussions.direct') }} <span class="ml-1 opacity-80">{{ $viewCounts['direct'] ?? 0 }}</span>
                        </button>
                        <button type="button" wire:click="setView('groups')" class="{{ $chipBase }} {{ $isActive('groups') ? $chipActive : $chipInactive }}" @if($isActive('groups')) style="background-color: var(--accent);" @endif>
                            {{ __('pages.discussions.groups') }} <span class="ml-1 opacity-80">{{ $viewCounts['thread_groups'] ?? 0 }}</span>
                        </button>
                        <button type="button" wire:click="setView('all')" class="{{ $chipBase }} {{ $isActive('all') ? $chipActive : $chipInactive }}" @if($isActive('all')) style="background-color: var(--accent);" @endif>
                            {{ __('pages.discussions.all') }} <span class="ml-1 opacity-80">{{ $viewCounts['threads_all'] ?? 0 }}</span>
                        </button>
                    @else
                        <button type="button" wire:click="setView('created_by_me')" class="{{ $chipBase }} {{ $isActive('created_by_me') ? $chipActive : $chipInactive }}" @if($isActive('created_by_me')) style="background-color: var(--accent);" @endif>
                            {{ __('pages.discussions.created_by_me') }} <span class="ml-1 opacity-80">{{ $viewCounts['created_by_me'] ?? 0 }}</span>
                        </button>
                        <button type="button" wire:click="setView('assigned_to_me')" class="{{ $chipBase }} {{ $isActive('assigned_to_me') ? $chipActive : $chipInactive }}" @if($isActive('assigned_to_me')) style="background-color: var(--accent);" @endif>
                            {{ __('pages.discussions.assigned_to_me') }} <span class="ml-1 opacity-80">{{ $viewCounts['assigned_to_me'] ?? 0 }}</span>
                        </button>
                        <button type="button" wire:click="setView('groups')" class="{{ $chipBase }} {{ $isActive('groups') ? $chipActive : $chipInactive }}" @if($isActive('groups')) style="background-color: var(--accent);" @endif>
                            {{ __('pages.discussions.ticket_groups') }} <span class="ml-1 opacity-80">{{ $viewCounts['groups'] ?? 0 }}</span>
                        </button>
                        <button type="button" wire:click="setView('all')" class="{{ $chipBase }} {{ $isActive('all') ? $chipActive : $chipInactive }}" @if($isActive('all')) style="background-color: var(--accent);" @endif>
                            {{ __('pages.discussions.all_tickets') }} <span class="ml-1 opacity-80">{{ $viewCounts['all'] ?? 0 }}</span>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Pending messages banner --}}
            @if(($pendingMessagesCount ?? 0) > 0)
                <div class="shrink-0 mx-3 mb-2 flex items-center gap-2 px-3 py-1.5 rounded-lg bg-amber-50 border border-amber-200">
                    <iconify-icon icon="solar:chat-round-dots-linear" class="text-amber-600" width="16"></iconify-icon>
                    <span class="text-[12px] font-medium text-amber-800">
                        {{ $pendingMessagesCount }} {{ $pendingMessagesCount === 1 ? __('pages.discussions.message_pending') : __('pages.discussions.messages_pending') }}
                    </span>
                </div>
            @endif

            {{-- Conversation list --}}
            <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar">
                @if(($scope ?? 'tickets') === 'threads')
                    @forelse($threads as $thread)
                        @php
                            $lastMsg = $lastThreadMessages->get($thread->id);
                            $isSelected = $selectedThread && (int) $selectedThread->id === (int) $thread->id;
                            $participants = $thread->participants ?? collect();
                            $title = $thread->is_group
                                ? ($thread->name ?: __('pages.discussions.discussion_group'))
                                : ($participants->where('id', '!=', auth()->id())->first()?->name ?: __('pages.discussions.discussion'));
                            $unreadCount = (int) ($unreadCountByThreadId[$thread->id] ?? 0);
                        @endphp
                        <a
                            href="{{ route('discussions.index', ['discussionParam' => 'd-'.$thread->id]) }}"
                            wire:navigate
                            class="messaging-list-item {{ $isSelected ? 'active' : '' }}"
                        >
                            <div class="relative shrink-0">
                                <div class="h-10 w-10 rounded-full flex items-center justify-center text-sm font-semibold" style="background: var(--accent-soft); color: var(--accent);">
                                    <iconify-icon icon="{{ $thread->is_group ? 'solar:users-group-rounded-linear' : 'solar:user-circle-linear' }}" width="20"></iconify-icon>
                                </div>
                                @if($unreadCount > 0)
                                    <span class="absolute -top-0.5 -right-0.5 flex h-[18px] min-w-[18px] items-center justify-center rounded-full px-1 text-[10px] font-bold text-white ring-2 ring-white" style="background-color: var(--accent);">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-semibold text-[#111827] truncate text-[13px] leading-tight">{{ $title }}</span>
                                    @if($lastMsg)
                                        <span class="text-[10px] text-[#9CA3AF] shrink-0 whitespace-nowrap">{{ $lastMsg->created_at->diffForHumans(short: true) }}</span>
                                    @endif
                                </div>
                                @if($lastMsg)
                                    <p class="mt-0.5 text-[12px] text-[#6B7280] truncate">
                                        <span class="font-medium text-[#374151]">{{ $lastMsg->user?->name ?? '—' }}:</span>
                                        {{ \Illuminate\Support\Str::limit(strip_tags($lastMsg->body), 40) }}
                                    </p>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center text-[13px] text-[#6B7280]">
                            {{ __('pages.discussions.no_conversation_started') }}
                            <div class="mt-3 flex items-center justify-center gap-2">
                                <button type="button" @click="openDiscussion()" class="cursor-pointer text-[12px] font-semibold px-3 py-1.5 rounded-lg transition-colors text-white" style="background-color: var(--accent);">
                                    {{ __('pages.discussions.new_short') }}
                                </button>
                                <button type="button" @click="openGroup()" class="cursor-pointer text-[12px] font-semibold px-3 py-1.5 rounded-lg border border-[#E5E7EB] text-[#374151] hover:bg-[#F9FAFB] transition-colors">
                                    {{ __('pages.discussions.group_short') }}
                                </button>
                            </div>
                        </div>
                    @endforelse
                @else
                    @forelse($tickets as $ticket)
                        @php
                            $lastMsg = $lastMessages->get($ticket->id);
                            $statusKey = $ticket->status?->value ?? 'open';
                            $pill = $statusPill[$statusKey] ?? $statusPill['open'];
                            $isSelected = $selectedTicket && (int) $selectedTicket->id === (int) $ticket->id;
                        @endphp
                        <a
                            href="{{ route('discussions.index', ['discussionParam' => $ticket->public_id]) }}"
                            wire:navigate
                            class="messaging-list-item {{ $isSelected ? 'active' : '' }}"
                        >
                            <div class="shrink-0">
                                <div class="h-10 w-10 rounded-full flex items-center justify-center text-sm font-semibold" style="background: var(--accent-soft); color: var(--accent);">
                                    <iconify-icon icon="solar:chat-round-dots-linear" width="20"></iconify-icon>
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-semibold text-[#111827] truncate text-[13px] leading-tight">{{ $ticket->subject }}</span>
                                    @if($lastMsg)
                                        <span class="text-[10px] text-[#9CA3AF] shrink-0 whitespace-nowrap">{{ $lastMsg->created_at->diffForHumans(short: true) }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="inline-flex items-center rounded-full px-1.5 py-0.5 text-[10px] font-medium {{ $pill['bg'] }} {{ $pill['text'] }}">{{ $statusLabels[$statusKey] ?? $statusKey }}</span>
                                    <span class="text-[10px] font-mono text-[#9CA3AF]">{{ $ticket->shortReference() }}</span>
                                </div>
                                @if($lastMsg)
                                    <p class="mt-0.5 text-[12px] text-[#6B7280] truncate">
                                        <span class="font-medium text-[#374151]">{{ $lastMsg->user?->name ?? '—' }}:</span>
                                        {{ \Illuminate\Support\Str::limit(strip_tags($lastMsg->body), 40) }}
                                    </p>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center text-[13px] text-[#6B7280]">
                            {{ __('pages.discussions.no_discussion_in_view') }}
                            <div class="mt-3 flex items-center justify-center gap-2">
                                <button type="button" @click="openDiscussion()" class="cursor-pointer text-[12px] font-semibold px-3 py-1.5 rounded-lg transition-colors text-white" style="background-color: var(--accent);">
                                    {{ __('pages.discussions.new_short') }}
                                </button>
                                <button type="button" @click="openGroup()" class="cursor-pointer text-[12px] font-semibold px-3 py-1.5 rounded-lg border border-[#E5E7EB] text-[#374151] hover:bg-[#F9FAFB] transition-colors">
                                    {{ __('pages.discussions.group_short') }}
                                </button>
                            </div>
                        </div>
                    @endforelse
                @endif
            </div>

            {{-- Pagination --}}
            @if(($scope ?? 'tickets') === 'threads')
                @if($threads->hasPages())
                    <div class="shrink-0 p-2 border-t border-slate-100">
                        {{ $threads->links('vendor.pagination.manexo') }}
                    </div>
                @endif
            @else
                @if($tickets->hasPages())
                    <div class="shrink-0 p-2 border-t border-slate-100">
                        {{ $tickets->links('vendor.pagination.manexo') }}
                    </div>
                @endif
            @endif
        </div>

        {{-- ═══ MAIN AREA ═══ --}}
        <div class="flex-1 min-w-0 flex flex-col overflow-hidden {{ (!$selectedThread && !$selectedTicket) ? 'hidden md:flex' : '' }}">
            @if($selectedThread)
                <div class="flex-1 min-h-0 overflow-hidden flex flex-col" wire:key="thread-{{ $selectedThread->id }}">
                    @livewire('discussions.thread', ['thread' => $selectedThread->id, 'embedded' => true])
                </div>
            @elseif($selectedTicket)
                <div class="flex-1 min-h-0 overflow-hidden" wire:key="discussion-{{ $selectedTicket->id }}">
                    @livewire('tickets.discussion', ['ticket' => $selectedTicket->public_id, 'embedded' => true])
                </div>
            @else
                {{-- Empty state --}}
                <div class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-[#FAFBFC]">
                    <div class="inline-flex h-16 w-16 items-center justify-center rounded-full text-[#C4C9D4] mb-5" style="background: var(--accent-soft);">
                        <iconify-icon icon="solar:chat-round-dots-linear" width="32" style="color: var(--accent); opacity: 0.6;"></iconify-icon>
                    </div>
                    <h2 class="text-base font-semibold text-[#111827]">{{ __('pages.discussions.select_discussion') }}</h2>
                    <p class="mt-1.5 text-sm text-[#6B7280] max-w-xs">{{ __('pages.discussions.select_discussion_help') }}</p>
                    <div class="mt-5 flex items-center gap-2">
                        <button type="button" @click="openDiscussion()" class="cursor-pointer h-9 px-4 text-white text-[13px] font-semibold rounded-xl shadow-sm transition-colors inline-flex items-center gap-2 bg-[color:var(--accent)] hover:bg-[color:color-mix(in_srgb,var(--accent)_85%,black)]">
                            <iconify-icon icon="solar:user-plus-linear" width="15"></iconify-icon>
                            {{ __('pages.discussions.new_discussion') }}
                        </button>
                        <button type="button" @click="openGroup()" class="cursor-pointer h-9 px-4 bg-white border border-[#E5E7EB] text-[#374151] text-[13px] font-semibold rounded-xl hover:bg-[#F9FAFB] transition inline-flex items-center gap-2">
                            <iconify-icon icon="solar:users-group-two-rounded-linear" width="15"></iconify-icon>
                            {{ __('pages.discussions.create_group') }}
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @else
        <div class="messaging-shell items-center justify-center">
            <x-page-skeleton variant="list" />
        </div>
    @endif

    {{-- Modal Nouvelle discussion (1 utilisateur) --}}
    <div x-show="showNewDiscussionModal" x-cloak x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-[9999] overflow-y-auto" aria-modal="true" style="display: none;">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/40 transition-opacity" @click="closeDiscussion()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full border border-[#E5E7EB] overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-start justify-between gap-3">
                <div>
                    <h3 class="text-base font-semibold text-[#111827]">{{ __('pages.discussions.new_discussion') }}</h3>
                    <p class="mt-0.5 text-sm text-[#6B7280]">{{ __('pages.discussions.choose_user') }}</p>
                </div>
                <button type="button" @click="closeDiscussion()" class="h-8 w-8 rounded-lg text-[#6B7280] hover:bg-[#F3F4F6] hover:text-[#111827] transition flex items-center justify-center">
                    <iconify-icon icon="solar:close-circle-linear" width="18"></iconify-icon>
                </button>
            </div>

            <div class="p-5 space-y-4">
                <div class="relative">
                    <iconify-icon icon="solar:magnifer-linear" class="absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF]" width="17"></iconify-icon>
                    <input
                        type="text"
                        wire:model.live.debounce.200ms="newDiscussionSearch"
                        placeholder="{{ __('pages.discussions.search_user') }}"
                        class="w-full h-10 pl-10 pr-3 text-[13px] text-[#111827] placeholder:text-[#9CA3AF] bg-[#F1F5F9] border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-[color:var(--accent)]/20"
                        autofocus
                    />
                </div>

                @php
                    $q = mb_strtolower(trim($newDiscussionSearch ?? ''));
                    $users = $orgUsers->filter(function ($u) use ($q) {
                        if ($q === '') return true;
                        return str_contains(mb_strtolower($u->name ?? ''), $q) || str_contains(mb_strtolower($u->email ?? ''), $q);
                    });
                @endphp

                <div class="max-h-72 overflow-y-auto custom-scrollbar space-y-1">
                    @forelse($users as $u)
                        <label class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-[#F8FAFC] cursor-pointer transition">
                            <input type="radio" name="new_discussion_user" wire:model="newDiscussionUserId" value="{{ $u->id }}" class="h-4 w-4 text-[color:var(--accent)] focus:ring-[color:var(--accent)]/30">
                            <div class="h-9 w-9 rounded-full flex items-center justify-center text-sm font-semibold shrink-0" style="background: var(--accent-soft); color: var(--accent);">
                                {{ strtoupper(mb_substr($u->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-semibold text-[#111827] truncate">{{ $u->name }}</div>
                                <div class="text-xs text-[#6B7280] truncate">{{ $u->email }}</div>
                            </div>
                        </label>
                    @empty
                        <div class="py-10 text-center text-sm text-[#6B7280]">
                            {{ __('pages.discussions.no_user_found') }}
                        </div>
                    @endforelse
                </div>

                @error('newDiscussionUserId')
                    <p class="text-[12px] text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" @click="closeDiscussion()" class="h-9 px-4 text-[#374151] text-[13px] font-semibold rounded-xl hover:bg-[#F3F4F6] transition" wire:loading.attr="disabled">
                    {{ __('pages.discussions.cancel') }}
                </button>
                <button type="button" wire:click="createDiscussionWithUser" wire:loading.attr="disabled" class="h-9 px-4 text-white text-[13px] font-semibold rounded-xl bg-[color:var(--accent)] hover:bg-[color:color-mix(in_srgb,var(--accent)_85%,black)] transition inline-flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed min-w-[100px]">
                    <span wire:loading.remove wire:target="createDiscussionWithUser">{{ __('pages.discussions.start') }}</span>
                    <span wire:loading wire:target="createDiscussionWithUser" class="inline-block h-4 w-4 rounded-full border-2 border-white border-t-transparent animate-spin"></span>
                </button>
            </div>
        </div>
    </div>
    </div>

    {{-- Modal Créer un groupe --}}
    <div x-show="showNewGroupModal" x-cloak x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-[9999] overflow-y-auto" aria-modal="true" style="display: none;">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/40 transition-opacity" @click="closeGroup()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full border border-[#E5E7EB] overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-start justify-between gap-3">
                <div>
                    <h3 class="text-base font-semibold text-[#111827]">{{ __('pages.discussions.create_discussion_group') }}</h3>
                    <p class="mt-0.5 text-sm text-[#6B7280]">{{ __('pages.discussions.create_group_help') }}</p>
                </div>
                <button type="button" @click="closeGroup()" class="h-8 w-8 rounded-lg text-[#6B7280] hover:bg-[#F3F4F6] hover:text-[#111827] transition flex items-center justify-center">
                    <iconify-icon icon="solar:close-circle-linear" width="18"></iconify-icon>
                </button>
            </div>

            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-[13px] font-medium text-[#374151] mb-1">{{ __('pages.discussions.group_name_optional') }}</label>
                    <input type="text" wire:model.live="newGroupName" placeholder="{{ __('pages.discussions.group_name_placeholder') }}" class="w-full h-10 px-3 text-[13px] bg-[#F1F5F9] border-0 rounded-xl focus:ring-2 focus:ring-[color:var(--accent)]/20" />
                </div>

                <div class="relative">
                    <iconify-icon icon="solar:magnifer-linear" class="absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF]" width="17"></iconify-icon>
                    <input
                        type="text"
                        wire:model.live.debounce.200ms="newGroupSearch"
                        placeholder="{{ __('pages.discussions.search_participants') }}"
                        class="w-full h-10 pl-10 pr-3 text-[13px] text-[#111827] placeholder:text-[#9CA3AF] bg-[#F1F5F9] border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-[color:var(--accent)]/20"
                    />
                </div>

                @php
                    $qg = mb_strtolower(trim($newGroupSearch ?? ''));
                    $groupUsers = $orgUsers->filter(function ($u) use ($qg) {
                        if ($qg === '') return true;
                        return str_contains(mb_strtolower($u->name ?? ''), $qg) || str_contains(mb_strtolower($u->email ?? ''), $qg);
                    });
                @endphp

                <div class="max-h-72 overflow-y-auto custom-scrollbar space-y-1">
                    @foreach($groupUsers as $u)
                        <label class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-[#F8FAFC] cursor-pointer transition">
                            <input type="checkbox" wire:model.live="newGroupUserIds" value="{{ $u->id }}" class="h-4 w-4 rounded border-[#E5E7EB] text-[color:var(--accent)] focus:ring-[color:var(--accent)]/30">
                            <div class="h-9 w-9 rounded-full flex items-center justify-center text-sm font-semibold shrink-0" style="background: var(--accent-soft); color: var(--accent);">
                                {{ strtoupper(mb_substr($u->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-semibold text-[#111827] truncate">{{ $u->name }}</div>
                                <div class="text-xs text-[#6B7280] truncate">{{ $u->email }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>

                @error('newGroupUserIds')
                    <p class="text-[12px] text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" @click="closeGroup()" class="h-9 px-4 text-[#374151] text-[13px] font-semibold rounded-xl hover:bg-[#F3F4F6] transition" wire:loading.attr="disabled">
                    {{ __('pages.discussions.cancel') }}
                </button>
                <button type="button" wire:click="createGroupDiscussion" wire:loading.attr="disabled" class="h-9 px-4 text-white text-[13px] font-semibold rounded-xl bg-[color:var(--accent)] hover:bg-[color:color-mix(in_srgb,var(--accent)_85%,black)] transition inline-flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed min-w-[120px]">
                    <span wire:loading.remove wire:target="createGroupDiscussion">{{ __('pages.discussions.create_the_group') }}</span>
                    <span wire:loading wire:target="createGroupDiscussion" class="inline-block h-4 w-4 rounded-full border-2 border-white border-t-transparent animate-spin"></span>
                </button>
            </div>
        </div>
    </div>
    </div>

</div>
