{{--
    Avatar cliquable → fiche (nom, email, @tag, badges).
    API Popover (manual) + resources/js/user-card-popover.js — évite <details> + fixed qui se referment au 1er clic.
--}}
@props([
    'initials' => '?',
    'name' => '',
    'email' => null,
    'mentionTag' => null,
    'badges' => [],
    'openLabel' => null,
    'popoverId' => null,
    /** Cible ~44px au toucher sur mobile ; plus compact sur sm+ */
    'sizeClass' => 'min-h-11 min-w-11 h-11 w-11 sm:h-9 sm:w-9 sm:min-h-0 sm:min-w-0',
])

@php
    $badges = is_array($badges ?? null) ? $badges : [];
    $openLabel = $openLabel ?? __('pages.discussions.user_card_open');
    $pid = $popoverId ?? 'muc-' . bin2hex(random_bytes(8));
@endphp

<div class="relative isolate shrink-0" data-manexo-user-card-root>
    <button
        type="button"
        popovertarget="{{ $pid }}"
        class="list-none cursor-pointer touch-manipulation select-none {{ $sizeClass }} rounded-full border border-slate-100 bg-slate-100 text-slate-700 inline-flex items-center justify-center text-[10px] sm:text-[10px] font-semibold shrink-0 hover:ring-2 hover:ring-[var(--accent)]/35 hover:bg-slate-50 active:scale-[0.98] transition-all"
        title="{{ $openLabel }}"
        aria-label="{{ $openLabel }}"
        aria-haspopup="dialog"
    >{{ $initials }}</button>
    <div
        id="{{ $pid }}"
        popover="manual"
        data-manexo-user-card
        role="dialog"
        aria-label="{{ $openLabel }}"
        class="pointer-events-auto m-0 w-[min(24rem,calc(100vw-2rem))] max-w-[calc(100vw-2rem)] rounded-2xl border border-slate-200/90 bg-white p-4 text-left shadow-2xl shadow-slate-900/15 ring-1 ring-slate-900/[0.06]"
        style="padding-left: max(1rem, env(safe-area-inset-left, 0px)); padding-right: max(1rem, env(safe-area-inset-right, 0px));"
        onclick="event.stopPropagation()"
    >
        <div class="text-[15px] font-semibold leading-snug text-slate-900 [overflow-wrap:anywhere] break-words">{{ $name }}</div>
        @if($email)
            <div class="mt-2 text-xs leading-relaxed text-slate-500 [overflow-wrap:anywhere] break-all">{{ $email }}</div>
        @endif
        @if($mentionTag)
            <div class="mt-2 text-sm font-medium text-slate-600 [overflow-wrap:anywhere] break-all">
                <span class="text-slate-400">@</span>{{ $mentionTag }}
            </div>
        @endif
        @if(count($badges) > 0)
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($badges as $badge)
                    <span class="inline-flex max-w-full items-center rounded-full bg-[var(--accent-soft)] px-2.5 py-1 text-[10px] font-bold leading-tight text-[var(--accent)] ring-1 ring-[var(--accent)]/15 sm:text-[11px] [overflow-wrap:anywhere]">
                        {{ $badge }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>
</div>
