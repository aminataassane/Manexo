@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-[11px] text-emerald-800 flex items-start gap-2']) }}>
        <svg class="mt-[1px] h-3.5 w-3.5 flex-none text-emerald-600" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z" stroke="currentColor" stroke-width="1.5"/>
            <path d="m8 12.5 2.5 2.5L16.5 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="leading-relaxed">{{ $status }}</span>
    </div>
@endif
