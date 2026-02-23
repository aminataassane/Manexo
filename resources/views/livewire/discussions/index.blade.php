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
    $itemBase = 'w-full flex items-center justify-between gap-3 px-3 py-2 rounded-md text-[13px] font-medium transition border';
    $badgeBase = 'min-w-[28px] h-6 px-2 rounded-md text-[12px] font-semibold flex items-center justify-center';
    $isActive = fn (string $k) => ($viewKey ?? 'all') === $k;
@endphp

<div
    class="w-full max-w-full min-w-0 mx-auto py-3 sm:py-4 md:py-5 lg:py-4 xl:py-5 2xl:py-6 flex flex-col overflow-hidden px-0 sm:px-0 min-h-0"
    style="height: calc(100dvh - 3.5rem); min-height: 280px; padding-bottom: env(safe-area-inset-bottom, 0);"
    x-data="{
        viewsOpen: true,
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
    <div class="flex flex-col gap-3 shrink-0 sm:flex-row sm:items-center sm:justify-between px-0">
        <div class="min-w-0">
            <h1 class="text-xl font-semibold text-[#111827] tracking-tight truncate sm:text-2xl lg:text-2xl xl:text-3xl 2xl:text-3xl min-[1920px]:text-4xl">{{ __('pages.discussions.title') }}</h1>
            <p class="mt-0.5 text-xs text-[#6B7280] sm:text-sm lg:text-base">{{ __('pages.discussions.subtitle') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 min-w-0">
            <button
                type="button"
                class="h-10 px-3 bg-white border border-[#E5E7EB] text-[#111827] text-[13px] font-semibold rounded-xl shadow-sm hover:bg-[#F9FAFB] transition inline-flex items-center gap-2"
                @click="viewsOpen = !viewsOpen"
            >
                <iconify-icon icon="solar:sidebar-minimalistic-linear" width="16"></iconify-icon>
                <span x-show="viewsOpen">{{ __('pages.discussions.hide_views') }}</span>
                <span x-show="!viewsOpen">{{ __('pages.discussions.show_views') }}</span>
            </button>
            <button
                type="button"
                @click="openDiscussion()"
                class="cursor-pointer h-9 sm:h-10 px-3 sm:px-4 text-white text-[12px] sm:text-[13px] font-semibold rounded-xl shadow-sm transition-colors inline-flex items-center gap-1.5 sm:gap-2 bg-[color:var(--accent)] hover:bg-[color:color-mix(in_srgb,var(--accent)_85%,black)] shrink-0"
            >
                <iconify-icon icon="solar:user-plus-linear" width="16"></iconify-icon>
                <span class="hidden sm:inline">{{ __('pages.discussions.new_discussion') }}</span>
                <span class="sm:hidden">{{ __('pages.discussions.new_short') }}</span>
            </button>
            <button
                type="button"
                @click="openGroup()"
                class="cursor-pointer h-9 sm:h-10 px-3 sm:px-4 bg-white border border-[#E5E7EB] text-[#111827] text-[12px] sm:text-[13px] font-semibold rounded-xl shadow-sm hover:bg-[#F9FAFB] transition inline-flex items-center gap-1.5 sm:gap-2 shrink-0"
            >
                <iconify-icon icon="solar:users-group-two-rounded-linear" width="16"></iconify-icon>
                <span class="hidden sm:inline">{{ __('pages.discussions.create_group') }}</span>
                <span class="sm:hidden">{{ __('pages.discussions.group_short') }}</span>
            </button>
        </div>
    </div>

    <div class="mt-3 sm:mt-4 md:mt-5 flex-1 grid grid-cols-1 gap-3 min-h-0 min-w-0 sm:gap-4 md:gap-5 lg:grid-cols-4 lg:gap-5 xl:gap-6 2xl:gap-8 min-[1920px]:gap-10 overflow-hidden px-0">
        <!-- LEFT: Vues + liste. Sur mobile masqué quand une discussion est sélectionnée (on affiche uniquement le détail). -->
        <div class="lg:col-span-1 flex flex-col min-h-0 min-w-0 {{ ($selectedThread ?? null) || ($selectedTicket ?? null) ? 'hidden lg:flex' : '' }}">
            {{-- 1. Panneau "Vues" --}}
            <div class="rounded-xl border border-[#E5E7EB] bg-white shadow-sm overflow-hidden shrink-0" x-show="viewsOpen" x-transition:enter.duration.200ms x-transition:leave.duration.150ms>
                <div class="w-full px-4 py-3 border-b border-[#E5E7EB] bg-[#F9FAFB] flex items-center justify-between">
                    <div class="text-[11px] uppercase tracking-wider text-[#6B7280] font-semibold">{{ __('pages.discussions.views') }}</div>
                    <button
                        type="button"
                        class="h-8 px-2 rounded-md text-[12px] font-semibold text-[#111827] hover:bg-white/70 transition inline-flex items-center gap-1.5"
                        @click="viewsOpen = false"
                    >
                        <iconify-icon icon="solar:double-alt-arrow-left-linear" width="14" class="text-[#6B7280]"></iconify-icon>
                        {{ __('pages.discussions.hide') }}
                    </button>
                </div>
                <div class="p-2 space-y-1">
                    <div class="px-1 pb-2">
                        <div class="flex items-center gap-1 rounded-xl bg-[#F3F4F6] p-1">
                                <button type="button" wire:click="setScope('threads')"
                                class="flex-1 rounded-lg px-3 py-2 text-[13px] font-bold transition-all {{ ($scope ?? 'tickets') === 'threads' ? 'bg-white text-[#111827] shadow-sm' : 'text-[#6B7280] hover:text-[#111827]' }}">
                                <span class="inline-flex items-center justify-center gap-2 w-full">
                                    <iconify-icon icon="solar:chat-round-dots-linear" width="16"></iconify-icon>
                                    {{ __('pages.discussions.conversations') }}
                                </span>
                            </button>
                            <button type="button" wire:click="setScope('tickets')"
                                class="flex-1 rounded-lg px-3 py-2 text-[13px] font-bold transition-all {{ ($scope ?? 'tickets') === 'tickets' ? 'bg-white text-[#111827] shadow-sm' : 'text-[#6B7280] hover:text-[#111827]' }}">
                                <span class="inline-flex items-center justify-center gap-2 w-full">
                                    <iconify-icon icon="solar:ticket-linear" width="16"></iconify-icon>
                                    {{ __('menu.tickets') }}
                                </span>
                            </button>
                        </div>
                    </div>

                    @if(($scope ?? 'tickets') === 'threads')
                        <button type="button" wire:click="setView('direct')"
                            class="{{ $itemBase }} {{ $isActive('direct') ? 'border-[color:var(--accent-soft)] bg-[color:var(--accent-soft)] text-[color:var(--accent)]' : 'border-transparent text-[#111827] hover:bg-[#F9FAFB]' }}">
                            <span class="flex items-center gap-2">
                                <iconify-icon icon="solar:user-circle-linear" width="16" class="text-[#6B7280]"></iconify-icon>
                                {{ __('pages.discussions.direct') }}
                            </span>
                            <span class="{{ $badgeBase }} {{ $isActive('direct') ? 'bg-white/70 text-[#111827]' : 'bg-[#F3F4F6] text-[#111827]' }}">{{ $viewCounts['direct'] ?? 0 }}</span>
                        </button>
                        <button type="button" wire:click="setView('groups')"
                            class="{{ $itemBase }} {{ $isActive('groups') ? 'border-[color:var(--accent-soft)] bg-[color:var(--accent-soft)] text-[color:var(--accent)]' : 'border-transparent text-[#111827] hover:bg-[#F9FAFB]' }}">
                            <span class="flex items-center gap-2">
                                <iconify-icon icon="solar:users-group-two-rounded-linear" width="16" class="text-[#6B7280]"></iconify-icon>
                                {{ __('pages.discussions.groups') }}
                            </span>
                            <span class="{{ $badgeBase }} {{ $isActive('groups') ? 'bg-white/70 text-[#111827]' : 'bg-[#F3F4F6] text-[#111827]' }}">{{ $viewCounts['thread_groups'] ?? 0 }}</span>
                        </button>
                        <button type="button" wire:click="setView('all')"
                            class="{{ $itemBase }} {{ $isActive('all') ? 'border-[color:var(--accent-soft)] bg-[color:var(--accent-soft)] text-[color:var(--accent)]' : 'border-transparent text-[#111827] hover:bg-[#F9FAFB]' }}">
                            <span class="flex items-center gap-2">
                                <iconify-icon icon="solar:layers-linear" width="16" class="text-[#6B7280]"></iconify-icon>
                                {{ __('pages.discussions.all') }}
                            </span>
                            <span class="{{ $badgeBase }} {{ $isActive('all') ? 'bg-white/70 text-[#111827]' : 'bg-[#F3F4F6] text-[#111827]' }}">{{ $viewCounts['threads_all'] ?? 0 }}</span>
                        </button>
                    @else
                        <button type="button" wire:click="setView('created_by_me')"
                            class="{{ $itemBase }} {{ $isActive('created_by_me') ? 'border-[color:var(--accent-soft)] bg-[color:var(--accent-soft)] text-[color:var(--accent)]' : 'border-transparent text-[#111827] hover:bg-[#F9FAFB]' }}">
                            <span class="flex items-center gap-2">
                                <iconify-icon icon="solar:pen-linear" width="16" class="text-[#6B7280]"></iconify-icon>
                                {{ __('pages.discussions.created_by_me') }}
                            </span>
                            <span class="{{ $badgeBase }} {{ $isActive('created_by_me') ? 'bg-white/70 text-[#111827]' : 'bg-[#F3F4F6] text-[#111827]' }}">{{ $viewCounts['created_by_me'] ?? 0 }}</span>
                        </button>
                        <button type="button" wire:click="setView('assigned_to_me')"
                            class="{{ $itemBase }} {{ $isActive('assigned_to_me') ? 'border-[color:var(--accent-soft)] bg-[color:var(--accent-soft)] text-[color:var(--accent)]' : 'border-transparent text-[#111827] hover:bg-[#F9FAFB]' }}">
                            <span class="flex items-center gap-2">
                                <iconify-icon icon="solar:user-check-linear" width="16" class="text-[#6B7280]"></iconify-icon>
                                {{ __('pages.discussions.assigned_to_me') }}
                            </span>
                            <span class="{{ $badgeBase }} {{ $isActive('assigned_to_me') ? 'bg-white/70 text-[#111827]' : 'bg-[#F3F4F6] text-[#111827]' }}">{{ $viewCounts['assigned_to_me'] ?? 0 }}</span>
                        </button>
                        <button type="button" wire:click="setView('groups')"
                            class="{{ $itemBase }} {{ $isActive('groups') ? 'border-[color:var(--accent-soft)] bg-[color:var(--accent-soft)] text-[color:var(--accent)]' : 'border-transparent text-[#111827] hover:bg-[#F9FAFB]' }}">
                            <span class="flex items-center gap-2">
                                <iconify-icon icon="solar:users-group-two-rounded-linear" width="16" class="text-[#6B7280]"></iconify-icon>
                                {{ __('pages.discussions.ticket_groups') }}
                            </span>
                            <span class="{{ $badgeBase }} {{ $isActive('groups') ? 'bg-white/70 text-[#111827]' : 'bg-[#F3F4F6] text-[#111827]' }}">{{ $viewCounts['groups'] ?? 0 }}</span>
                        </button>
                        <button type="button" wire:click="setView('all')"
                            class="{{ $itemBase }} {{ $isActive('all') ? 'border-[color:var(--accent-soft)] bg-[color:var(--accent-soft)] text-[color:var(--accent)]' : 'border-transparent text-[#111827] hover:bg-[#F9FAFB]' }}">
                            <span class="flex items-center gap-2">
                                <iconify-icon icon="solar:layers-linear" width="16" class="text-[#6B7280]"></iconify-icon>
                                {{ __('pages.discussions.all_tickets') }}
                            </span>
                            <span class="{{ $badgeBase }} {{ $isActive('all') ? 'bg-white/70 text-[#111827]' : 'bg-[#F3F4F6] text-[#111827]' }}">{{ $viewCounts['all'] ?? 0 }}</span>
                        </button>
                    @endif
                </div>
            </div>

            {{-- 2. Liste des discussions (toujours visible, pas de bouton fermer — s’agrandit quand les vues sont masquées) --}}
            <div class="flex-1 min-h-[200px] sm:min-h-[280px] flex flex-col rounded-xl border border-[#E5E7EB] bg-white shadow-sm overflow-hidden" :class="viewsOpen ? 'mt-2' : 'mt-0'">
                <div class="p-2 border-b border-[#E5E7EB] shrink-0 space-y-2">
                    @if(($pendingMessagesCount ?? 0) > 0)
                        <div class="flex items-center gap-2 px-2 py-1.5 rounded-lg bg-amber-50 border border-amber-200">
                            <iconify-icon icon="solar:chat-round-dots-linear" class="text-amber-600" width="18"></iconify-icon>
                            <span class="text-[13px] font-medium text-amber-800">
                                {{ $pendingMessagesCount }} {{ $pendingMessagesCount === 1 ? __('pages.discussions.message_pending') : __('pages.discussions.messages_pending') }}
                            </span>
                        </div>
                    @endif
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <iconify-icon icon="solar:magnifer-linear" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[#9CA3AF]" width="16"></iconify-icon>
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="search"
                                placeholder="{{ __('pages.discussions.search') }}"
                                class="w-full h-9 pl-8 pr-3 text-[13px] text-[#111827] placeholder:text-[#9CA3AF] bg-[#F9FAFB] border border-[#E5E7EB] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#005F02]/20 focus:border-[#005F02]"
                            />
                        </div>
                        <button
                            type="button"
                            x-show="!viewsOpen"
                            x-cloak
                            class="h-9 px-2.5 rounded-lg border border-[#E5E7EB] bg-white text-[#6B7280] hover:bg-[#F9FAFB] transition inline-flex items-center gap-1.5 shrink-0 text-[12px] font-medium"
                            @click="viewsOpen = true"
                            title="{{ __('pages.discussions.show_discussion_views') }}"
                        >
                            <iconify-icon icon="solar:sidebar-minimalistic-linear" width="14"></iconify-icon>
                            <span class="hidden sm:inline">{{ __('pages.discussions.views') }}</span>
                        </button>
                    </div>
                </div>
                <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar p-2 space-y-1">
                    @if(($scope ?? 'tickets') === 'threads')
                        @forelse($threads as $thread)
                            @php
                                $lastMsg = $lastThreadMessages->get($thread->id);
                                $isSelected = $selectedThread && (int) $selectedThread->id === (int) $thread->id;
                                $participants = $thread->participants ?? collect();
                                $title = $thread->is_group
                                    ? ($thread->name ?: __('pages.discussions.discussion_group'))
                                    : ($participants->where('id', '!=', auth()->id())->first()?->name ?: __('pages.discussions.discussion'));
                            @endphp
                            <a
                                href="{{ route('discussions.index', ['ticket' => 'd-'.$thread->id]) }}"
                                class="block rounded-lg p-3 min-h-[52px] sm:min-h-0 transition-all touch-manipulation {{ $isSelected ? 'bg-[color:var(--accent-soft)] border border-[color:var(--accent-soft)]' : 'bg-[#F9FAFB] border border-transparent hover:bg-[#F3F4F6] hover:border-[#E5E7EB] active:bg-[#E5E7EB]' }}"
                            >
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#E5E7EB] text-[#6B7280]">
                                        <iconify-icon icon="{{ $thread->is_group ? 'solar:users-group-rounded-linear' : 'solar:user-circle-linear' }}" width="20"></iconify-icon>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <span class="font-medium text-[#111827] truncate text-[13px]">{{ $title }}</span>
                                            <span class="text-[10px] font-mono text-[#9CA3AF] shrink-0">#{{ $thread->id }}</span>
                                        </div>
                                        @if($lastMsg)
                                            <p class="mt-0.5 text-[12px] text-[#6B7280] line-clamp-1">
                                                <span class="text-[#111827] font-medium">{{ $lastMsg->user?->name ?? '—' }}</span>
                                                <span class="text-[#9CA3AF]">·</span>
                                                <span>{{ \Illuminate\Support\Str::limit(strip_tags($lastMsg->body), 45) }}</span>
                                            </p>
                                            <p class="mt-0.5 text-[10px] text-[#9CA3AF]">{{ $lastMsg->created_at->diffForHumans() }}</p>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="p-6 text-center text-[13px] text-[#6B7280]">
                                {{ __('pages.discussions.no_conversation_started') }}
                                <div class="mt-2">
                                    <button type="button" @click="openDiscussion()" class="cursor-pointer text-[13px] font-medium transition-colors" style="color: var(--accent);">
                                        {{ __('pages.discussions.new_discussion') }}
                                    </button>
                                    <span class="text-[#9CA3AF]"> {{ __('pages.discussions.or') }} </span>
                                    <button type="button" @click="openGroup()" class="cursor-pointer text-[13px] font-medium transition-colors" style="color: var(--accent);">
                                        {{ __('pages.discussions.create_group') }}
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
                            href="{{ route('discussions.index', ['ticket' => $ticket->id]) }}"
                            class="block rounded-lg p-3 min-h-[52px] sm:min-h-0 transition-all touch-manipulation {{ $isSelected ? 'bg-[color:var(--accent-soft)] border border-[color:var(--accent-soft)]' : 'bg-[#F9FAFB] border border-transparent hover:bg-[#F3F4F6] hover:border-[#E5E7EB] active:bg-[#E5E7EB]' }}"
                        >
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#E5E7EB] text-[#6B7280]">
                                    <iconify-icon icon="solar:chat-round-dots-linear" width="20"></iconify-icon>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="font-medium text-[#111827] truncate text-[13px]">{{ $ticket->subject }}</span>
                                        <span class="text-[10px] font-mono text-[#9CA3AF] shrink-0">#{{ $ticket->id }}</span>
                                    </div>
                                    @if($lastMsg)
                                        <p class="mt-0.5 text-[12px] text-[#6B7280] line-clamp-1">
                                            <span class="text-[#111827] font-medium">{{ $lastMsg->user?->name ?? '—' }}</span>
                                            <span class="text-[#9CA3AF]">·</span>
                                            <span>{{ \Illuminate\Support\Str::limit(strip_tags($lastMsg->body), 45) }}</span>
                                        </p>
                                        <p class="mt-0.5 text-[10px] text-[#9CA3AF]">{{ $lastMsg->created_at->diffForHumans() }}</p>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-6 text-center text-[13px] text-[#6B7280]">
                            {{ __('pages.discussions.no_discussion_in_view') }}
                            <div class="mt-2">
                                <button type="button" wire:click="$set('showNewDiscussionModal', true)" class="cursor-pointer text-[13px] font-medium transition-colors" style="color: var(--accent);">
                                    {{ __('pages.discussions.new_discussion') }}
                                </button>
                                <span class="text-[#9CA3AF]"> {{ __('pages.discussions.or') }} </span>
                                <button type="button" wire:click="$set('showNewGroupModal', true)" class="cursor-pointer text-[13px] font-medium transition-colors" style="color: var(--accent);">
                                    {{ __('pages.discussions.create_group') }}
                                </button>
                            </div>
                        </div>
                    @endforelse
                    @endif
                </div>
                @if(($scope ?? 'tickets') === 'threads')
                    @if($threads->hasPages())
                        <div class="p-3 border-t border-[#E5E7EB] shrink-0 bg-[#F9FAFB]">
                            {{ $threads->links('vendor.pagination.manexo') }}
                        </div>
                    @endif
                @else
                    @if($tickets->hasPages())
                        <div class="p-3 border-t border-[#E5E7EB] shrink-0 bg-[#F9FAFB]">
                            {{ $tickets->links('vendor.pagination.manexo') }}
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- RIGHT: zone vide ou discussion. Sur mobile masqué quand rien n'est sélectionné (on affiche uniquement la liste). -->
        <div class="lg:col-span-3 min-h-0 flex flex-col rounded-xl border border-[#E5E7EB] bg-[#F9FAFB] overflow-hidden {{ (!$selectedThread && !$selectedTicket) ? 'hidden lg:flex' : '' }}">
            @if($selectedThread)
                <div class="flex-1 min-h-0 overflow-hidden flex flex-col" wire:key="thread-{{ $selectedThread->id }}">
                    <a href="{{ route('discussions.index') }}" class="lg:hidden shrink-0 flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 bg-white border-b border-slate-100">
                        <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
                        {{ __('pages.discussions.back_to_list') }}
                    </a>
                    <div class="flex-1 min-h-0 overflow-hidden flex flex-col">
                        @livewire('discussions.thread', ['thread' => $selectedThread->id, 'embedded' => true])
                    </div>
                </div>
            @elseif($selectedTicket)
                <div class="flex-1 min-h-0 overflow-hidden flex flex-col" wire:key="discussion-{{ $selectedTicket->id }}">
                    <a href="{{ route('discussions.index') }}" class="lg:hidden shrink-0 flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 bg-white border-b border-slate-100">
                        <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
                        {{ __('pages.discussions.back_to_list') }}
                    </a>
                    <div class="flex-1 min-h-0 overflow-hidden">
                        @livewire('tickets.discussion', ['ticket' => $selectedTicket->id, 'embedded' => true])
                    </div>
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center p-8 text-center">
                    <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-[#E5E7EB] text-[#9CA3AF] mb-6">
                        <iconify-icon icon="solar:chat-round-dots-linear" width="36"></iconify-icon>
                    </div>
                    <h2 class="text-lg font-semibold text-[#111827]">{{ __('pages.discussions.select_discussion') }}</h2>
                    <p class="mt-2 text-sm text-[#6B7280] max-w-sm">{{ __('pages.discussions.select_discussion_help') }}</p>
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                        <button type="button" @click="openDiscussion()" class="cursor-pointer h-10 px-4 text-white text-[13px] font-semibold rounded-xl shadow-sm transition-colors inline-flex items-center gap-2 bg-[color:var(--accent)] hover:bg-[color:color-mix(in_srgb,var(--accent)_85%,black)]">
                            <iconify-icon icon="solar:user-plus-linear" width="16"></iconify-icon>
                            {{ __('pages.discussions.new_discussion') }}
                        </button>
                        <button type="button" @click="openGroup()" class="cursor-pointer h-10 px-4 bg-white border border-[#E5E7EB] text-[#111827] text-[13px] font-semibold rounded-xl shadow-sm hover:bg-[#F9FAFB] transition inline-flex items-center gap-2">
                            <iconify-icon icon="solar:users-group-two-rounded-linear" width="16"></iconify-icon>
                            {{ __('pages.discussions.create_group') }}
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Nouvelle discussion (1 utilisateur) — ouvert par Alpine pour réaction immédiate au clic --}}
    <div x-show="showNewDiscussionModal" x-cloak x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-[9999] overflow-y-auto" aria-modal="true" style="display: none;">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/40 transition-opacity" @click="closeDiscussion()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full border border-[#E5E7EB] overflow-hidden">
            <div class="p-6 border-b border-[#E5E7EB] bg-[#F9FAFB] flex items-start justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-[#111827]">{{ __('pages.discussions.new_discussion') }}</h3>
                    <p class="mt-1 text-sm text-[#6B7280]">{{ __('pages.discussions.choose_user') }}</p>
                </div>
                <button type="button" @click="closeDiscussion()" class="h-9 w-9 rounded-xl border border-[#E5E7EB] bg-white text-[#6B7280] hover:bg-[#F3F4F6] hover:text-[#111827] transition flex items-center justify-center">
                    <iconify-icon icon="solar:close-circle-linear" width="18"></iconify-icon>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div class="relative">
                    <iconify-icon icon="solar:magnifer-linear" class="absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF]" width="18"></iconify-icon>
                    <input
                        type="text"
                        wire:model.live.debounce.200ms="newDiscussionSearch"
                        placeholder="{{ __('pages.discussions.search_user') }}"
                        class="w-full h-11 pl-10 pr-3 text-[13px] text-[#111827] placeholder:text-[#9CA3AF] bg-[#F9FAFB] border border-[#E5E7EB] rounded-xl focus:outline-none focus:ring-2 focus:ring-[color:var(--accent)]/20 focus:border-[color:var(--accent)]"
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

                <div class="max-h-72 overflow-y-auto custom-scrollbar space-y-2">
                    @forelse($users as $u)
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-[#E5E7EB] bg-white hover:bg-[#F9FAFB] cursor-pointer transition">
                            <input type="radio" name="new_discussion_user" wire:model="newDiscussionUserId" value="{{ $u->id }}" class="h-4 w-4 text-[color:var(--accent)] focus:ring-[color:var(--accent)]/30">
                            <div class="h-10 w-10 rounded-full flex items-center justify-center text-sm font-semibold shrink-0" style="background: var(--accent-soft); color: var(--accent);">
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

            <div class="px-6 py-4 border-t border-[#E5E7EB] bg-[#F9FAFB] flex items-center justify-end gap-2">
                <button type="button" @click="closeDiscussion()" class="h-10 px-4 border border-[#E5E7EB] text-[#111827] text-[13px] font-semibold rounded-xl hover:bg-white transition" wire:loading.attr="disabled">
                    {{ __('pages.discussions.cancel') }}
                </button>
                <button type="button" wire:click="createDiscussionWithUser" wire:loading.attr="disabled" class="h-10 px-4 text-white text-[13px] font-semibold rounded-xl bg-[color:var(--accent)] hover:bg-[color:color-mix(in_srgb,var(--accent)_85%,black)] transition inline-flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed min-w-[100px]">
                    <span wire:loading.remove wire:target="createDiscussionWithUser">{{ __('pages.discussions.start') }}</span>
                    <span wire:loading wire:target="createDiscussionWithUser" class="inline-block h-4 w-4 rounded-full border-2 border-white border-t-transparent animate-spin"></span>
                </button>
            </div>
        </div>
    </div>
    </div>

    {{-- Modal Créer un groupe — ouvert par Alpine pour réaction immédiate au clic --}}
    <div x-show="showNewGroupModal" x-cloak x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-[9999] overflow-y-auto" aria-modal="true" style="display: none;">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/40 transition-opacity" @click="closeGroup()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full border border-[#E5E7EB] overflow-hidden">
            <div class="p-6 border-b border-[#E5E7EB] bg-[#F9FAFB] flex items-start justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-[#111827]">{{ __('pages.discussions.create_discussion_group') }}</h3>
                    <p class="mt-1 text-sm text-[#6B7280]">{{ __('pages.discussions.create_group_help') }}</p>
                </div>
                <button type="button" @click="closeGroup()" class="h-9 w-9 rounded-xl border border-[#E5E7EB] bg-white text-[#6B7280] hover:bg-[#F3F4F6] hover:text-[#111827] transition flex items-center justify-center">
                    <iconify-icon icon="solar:close-circle-linear" width="18"></iconify-icon>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-[13px] font-medium text-[#374151] mb-1">{{ __('pages.discussions.group_name_optional') }}</label>
                    <input type="text" wire:model.live="newGroupName" placeholder="{{ __('pages.discussions.group_name_placeholder') }}" class="w-full h-11 px-3 text-[13px] border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[color:var(--accent)]/20 focus:border-[color:var(--accent)]" />
                </div>

                <div class="relative">
                    <iconify-icon icon="solar:magnifer-linear" class="absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF]" width="18"></iconify-icon>
                    <input
                        type="text"
                        wire:model.live.debounce.200ms="newGroupSearch"
                        placeholder="{{ __('pages.discussions.search_participants') }}"
                        class="w-full h-11 pl-10 pr-3 text-[13px] text-[#111827] placeholder:text-[#9CA3AF] bg-[#F9FAFB] border border-[#E5E7EB] rounded-xl focus:outline-none focus:ring-2 focus:ring-[color:var(--accent)]/20 focus:border-[color:var(--accent)]"
                    />
                </div>

                @php
                    $qg = mb_strtolower(trim($newGroupSearch ?? ''));
                    $groupUsers = $orgUsers->filter(function ($u) use ($qg) {
                        if ($qg === '') return true;
                        return str_contains(mb_strtolower($u->name ?? ''), $qg) || str_contains(mb_strtolower($u->email ?? ''), $qg);
                    });
                @endphp

                <div class="max-h-72 overflow-y-auto custom-scrollbar space-y-2">
                    @foreach($groupUsers as $u)
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-[#E5E7EB] bg-white hover:bg-[#F9FAFB] cursor-pointer transition">
                            <input type="checkbox" wire:model.live="newGroupUserIds" value="{{ $u->id }}" class="h-4 w-4 rounded border-[#E5E7EB] text-[color:var(--accent)] focus:ring-[color:var(--accent)]/30">
                            <div class="h-10 w-10 rounded-full flex items-center justify-center text-sm font-semibold shrink-0" style="background: var(--accent-soft); color: var(--accent);">
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

            <div class="px-6 py-4 border-t border-[#E5E7EB] bg-[#F9FAFB] flex items-center justify-end gap-2">
                <button type="button" @click="closeGroup()" class="h-10 px-4 border border-[#E5E7EB] text-[#111827] text-[13px] font-semibold rounded-xl hover:bg-white transition" wire:loading.attr="disabled">
                    {{ __('pages.discussions.cancel') }}
                </button>
                <button type="button" wire:click="createGroupDiscussion" wire:loading.attr="disabled" class="h-10 px-4 text-white text-[13px] font-semibold rounded-xl bg-[color:var(--accent)] hover:bg-[color:color-mix(in_srgb,var(--accent)_85%,black)] transition inline-flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed min-w-[120px]">
                    <span wire:loading.remove wire:target="createGroupDiscussion">{{ __('pages.discussions.create_the_group') }}</span>
                    <span wire:loading wire:target="createGroupDiscussion" class="inline-block h-4 w-4 rounded-full border-2 border-white border-t-transparent animate-spin"></span>
                </button>
            </div>
        </div>
    </div>
    </div>
</div>
