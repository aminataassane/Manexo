@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li class="flex items-start gap-1.5 text-[11px] text-red-600">
                <svg class="mt-[1px] h-3.5 w-3.5 flex-none text-red-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M12 7v6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M12 17h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span>{{ $message }}</span>
            </li>
        @endforeach
    </ul>
@endif
