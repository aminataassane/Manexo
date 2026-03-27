<div class="rounded-xl sm:rounded-2xl bg-white p-4 sm:p-6 shadow-sm border border-slate-100">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-bold text-slate-900">{{ __('pages.dashboard.discussions') }}</h3>
        @if ($this->discussionsUnreadCount > 0)
            <span class="flex h-6 min-w-[24px] px-1.5 items-center justify-center rounded-full bg-red-100 text-xs font-bold text-red-600">{{ $this->discussionsUnreadCount }}</span>
        @endif
    </div>
    <div class="space-y-4">
        @forelse ($this->recentDiscussions as $d)
            <a href="{{ $d->url }}" class="flex gap-4 p-3 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer group block">
                <div class="relative shrink-0">
                    <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-sm font-semibold text-slate-600 ring-2 ring-white" style="background: var(--accent-soft); color: var(--accent);">{{ strtoupper(mb_substr($d->name ?: 'D', 0, 1)) }}</div>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-sm font-bold text-slate-900 truncate">{{ $d->name ?: __('pages.dashboard.discussions') }}</p>
                        <span class="text-xs text-slate-400 shrink-0">{{ $d->last_at?->diffForHumans() ?: '—' }}</span>
                    </div>
                    <p class="text-xs text-slate-500 line-clamp-2 group-hover:text-slate-700">{{ $d->last_body ?: __('pages.dashboard.no_message') }}</p>
                </div>
            </a>
        @empty
            <p class="text-sm text-slate-500 py-4 text-center">{{ __('pages.dashboard.no_recent_discussion') }}</p>
        @endforelse
    </div>
    <a href="{{ route('discussions.index') }}" class="mt-6 w-full flex rounded-xl border border-slate-200 bg-white py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors justify-center" wire:navigate>
        {{ __('pages.dashboard.see_all_discussions') }}
    </a>
</div>
