<div class="relative" x-data="{ open: false }" @click.outside="open = false" wire:poll.45s>
    <button
        type="button"
        @click="open = !open"
        class="relative flex h-9 w-9 items-center justify-center rounded-full text-slate-500 transition-all hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-[var(--accent)] focus:ring-offset-2"
        title="{{ __('Notifications') }}"
    >
        <iconify-icon icon="solar:bell-linear" width="20"></iconify-icon>
        @if($this->unreadCount > 0)
            <span class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
            </span>
        @endif
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="absolute right-0 top-full z-50 mt-3 w-96 max-h-[80vh] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl ring-1 ring-black/5"
    >
            <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3 bg-slate-50/50 backdrop-blur-sm">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-slate-900">{{ __('Notifications') }}</span>
                    @if($this->unreadCount > 0)
                        <span class="inline-flex items-center rounded-full bg-[var(--accent-soft)] px-2 py-0.5 text-xs font-medium text-[var(--accent)]">
                            {{ $this->unreadCount }}
                        </span>
                    @endif
                </div>
                @if($this->unreadCount > 0)
                    <button type="button" wire:click="markAllAsRead" class="text-xs font-medium text-slate-500 hover:text-[var(--accent)] transition-colors">
                        {{ __('Tout lire') }}
                    </button>
                @endif
            </div>

            <div class="max-h-[65vh] overflow-y-auto custom-scrollbar">
                @forelse($this->notifications as $notification)
                    @php
                        $data = $notification->data;
                        $ticketId = $data['ticket_id'] ?? null;
                        $subject = $data['ticket_subject'] ?? __('Ticket');
                        $senderName = $data['sender_name'] ?? '—';
                        $excerpt = $data['body_excerpt'] ?? '';
                        $isNote = $data['is_internal_note'] ?? false;
                        $isRead = !is_null($notification->read_at);
                    @endphp
                    <a
                        href="{{ $ticketId ? route('tickets.discussion', $ticketId) : '#' }}"
                        wire:click="markAsRead('{{ $notification->id }}')"
                        class="group block border-b border-slate-50 px-4 py-3.5 transition-all hover:bg-slate-50 {{ $isRead ? 'opacity-60 hover:opacity-100' : 'bg-white' }}"
                    >
                        <div class="flex gap-3.5">
                            <div class="relative mt-1 shrink-0">
                                @if($isNote)
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-50 text-amber-600 ring-1 ring-amber-100">
                                        <iconify-icon icon="solar:lock-keyhole-bold-duotone" width="18"></iconify-icon>
                                    </div>
                                @else
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-[var(--accent-soft)] text-[var(--accent)] ring-1 ring-[var(--accent-soft)]">
                                        <iconify-icon icon="solar:chat-round-dots-bold-duotone" width="18"></iconify-icon>
                                    </div>
                                @endif
                                @if(!$isRead)
                                    <span class="absolute -top-0.5 -right-0.5 h-2.5 w-2.5 rounded-full border-2 border-white bg-blue-500"></span>
                                @endif
                            </div>
                            
                            <div class="min-w-0 flex-1">
                                <div class="flex items-baseline justify-between gap-2">
                                    <p class="text-sm font-semibold text-slate-900 truncate">
                                        {{ $senderName }}
                                    </p>
                                    <span class="text-[10px] text-slate-400 shrink-0">{{ $notification->created_at->diffForHumans(short: true) }}</span>
                                </div>
                                
                                <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1.5">
                                    @if($isNote)
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">Note interne</span>
                                    @else
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200">Ticket #{{ $ticketId }}</span>
                                    @endif
                                    <span class="truncate font-medium text-slate-700">{{ $subject }}</span>
                                </p>

                                @if($excerpt)
                                    <p class="mt-1.5 text-xs text-slate-600 line-clamp-2 leading-relaxed group-hover:text-slate-900 transition-colors">
                                        {{ $excerpt }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-50 text-slate-300 mb-3">
                            <iconify-icon icon="solar:bell-off-linear" width="32"></iconify-icon>
                        </div>
                        <p class="text-sm font-medium text-slate-900">{{ __('Aucune notification') }}</p>
                        <p class="text-xs text-slate-500 mt-1 max-w-[200px]">{{ __('Vous êtes à jour ! Profitez de votre journée.') }}</p>
                    </div>
                @endforelse
            </div>
            
            @if($this->notifications->count() > 0)
                <div class="border-t border-slate-100 bg-slate-50 p-2 text-center">
                    <a href="#" class="block w-full rounded-lg py-2 text-xs font-medium text-slate-600 hover:bg-white hover:text-[var(--accent)] hover:shadow-sm transition-all">
                        {{ __('Voir tout l\'historique') }}
                    </a>
                </div>
            @endif
        </div>
</div>