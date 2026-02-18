@props([
    'name' => 'U',
    'size' => 'h-9 w-9',
    'class' => '',
])

@php
    $raw = trim((string) ($name ?? ''));
    $raw = $raw !== '' ? $raw : 'U';
    $parts = preg_split('/\s+/', $raw) ?: [];
    $first = (string) ($parts[0] ?? 'U');
    $last = (string) ($parts[count($parts) - 1] ?? '');
    $initials = mb_strtoupper(mb_substr($first, 0, 1));
    if ($last !== '' && $last !== $first) {
        $initials .= mb_strtoupper(mb_substr($last, 0, 1));
    }
    $initials = trim($initials) !== '' ? $initials : 'U';
@endphp

<div {{ $attributes->merge(['class' => trim("{$size} rounded-full flex items-center justify-center font-extrabold {$class}")]) }}
     style="background: var(--accent-soft); color: var(--accent);">
    <span class="text-[11px] sm:text-xs leading-none">{{ $initials }}</span>
</div>

