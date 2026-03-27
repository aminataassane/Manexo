<div class="rounded-xl sm:rounded-2xl bg-white p-4 sm:p-6 shadow-sm border border-slate-100">
    <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-4 sm:mb-6">{{ __('pages.dashboard.recent_activity') }}</h3>
    <div class="relative pl-4 space-y-6 before:absolute before:left-[19px] before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-100">
        @forelse ($this->recentActivity as $a)
            <a href="{{ $a->url }}" class="relative pl-6 block group" wire:navigate>
                <div class="absolute left-0 top-1.5 h-2.5 w-2.5 rounded-full border-2 border-white shadow-sm group-hover:scale-110 transition-transform" style="background-color: var(--accent);"></div>
                <p class="text-sm font-medium text-slate-900 group-hover:text-[var(--accent)] truncate" title="{{ $a->subject }}">{{ $a->subject }}</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ $a->created_at->diffForHumans() }} {{ __('pages.dashboard.by') }} <span class="font-medium text-slate-700">{{ $a->user_name }}</span></p>
            </a>
        @empty
            <p class="text-sm text-slate-500 py-2">{{ __('pages.dashboard.no_recent_activity') }}</p>
        @endforelse
    </div>
</div>
