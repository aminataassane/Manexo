<div
    wire:key="onboarding-step-{{ $key }}"
    @class([
        'relative rounded-xl border p-4 transition-all duration-200',
        'border-slate-100 bg-slate-50/50' => $isDone,
        'border-[color:var(--accent-soft-2)] bg-white shadow-sm ring-1 ring-[color:var(--accent-soft)]' => $isCurrent && ! $isDone,
        'border-slate-200 bg-white hover:border-slate-300' => ! $isCurrent && ! $isDone,
    ])
>
    {{-- Top row: number + status --}}
    <div class="flex items-center justify-between mb-3">
        <div @class([
            'flex h-6 w-6 items-center justify-center rounded-full text-[10px] font-bold',
            'bg-emerald-500 text-white' => $isDone,
            'bg-[color:var(--accent)] text-white' => $isCurrent && ! $isDone,
            'bg-slate-100 text-slate-400' => ! $isCurrent && ! $isDone,
        ])>
            @if ($isDone)
                <iconify-icon icon="solar:check-read-linear" width="12"></iconify-icon>
            @else
                {{ $number }}
            @endif
        </div>

        @if ($isDone)
            <span class="text-[10px] font-semibold text-emerald-600">{{ __('onboarding.done') }}</span>
        @elseif ($isCurrent)
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background-color: var(--accent);"></span>
                <span class="relative inline-flex rounded-full h-2 w-2" style="background-color: var(--accent);"></span>
            </span>
        @endif
    </div>

    {{-- Icon --}}
    <iconify-icon
        icon="{{ $meta['icon'] }}"
        width="22"
        @class([
            'mb-2 block',
            'text-slate-300' => $isDone,
            'text-[color:var(--accent)]' => $isCurrent && ! $isDone,
            'text-slate-400' => ! $isCurrent && ! $isDone,
        ])
    ></iconify-icon>

    {{-- Text --}}
    <h3 @class([
        'text-[13px] font-semibold leading-snug',
        'text-slate-400 line-through decoration-slate-300' => $isDone,
        'text-slate-900' => ! $isDone,
    ])>
        {{ $meta['title'] }}
    </h3>
    <p @class([
        'mt-1 text-[11px] leading-relaxed',
        'text-slate-400' => $isDone,
        'text-slate-500' => ! $isDone,
    ])>
        {{ $meta['desc'] }}
    </p>

    {{-- Action --}}
    @if (! $isDone && $step['route'])
        <a
            href="{{ isset($step['route_params']) ? route($step['route'], $step['route_params']) : route($step['route']) }}"
            wire:navigate.hover
            @class([
                'mt-3 inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-[11px] font-semibold transition-all',
                'text-white shadow-sm hover:opacity-90' => $isCurrent,
                'text-slate-600 bg-slate-100 hover:bg-slate-200' => ! $isCurrent,
            ])
            @if($isCurrent) style="background-color: var(--accent);" @endif
        >
            {{ $meta['btn'] }}
            <iconify-icon icon="solar:arrow-right-linear" width="12"></iconify-icon>
        </a>
    @endif
</div>
