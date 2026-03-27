@props(['status', 'type' => 'fr'])

@php
    $label = $type === 'fr' ? 'PR' : 'RES';
    $statusValue = $status instanceof \App\Enums\SlaStatus ? $status->value : $status;

    $config = match ($statusValue) {
        'met' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'icon' => 'solar:check-circle-bold', 'iconColor' => 'text-emerald-500'],
        'on_track' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'icon' => 'solar:clock-circle-bold', 'iconColor' => 'text-blue-500'],
        'at_risk' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => 'solar:alarm-bold', 'iconColor' => 'text-amber-500'],
        'breached' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'icon' => 'solar:danger-triangle-bold', 'iconColor' => 'text-red-500'],
        default => null,
    };
@endphp

@if ($config)
    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-semibold border {{ $config['bg'] }} {{ $config['text'] }} {{ $config['border'] }}">
        <iconify-icon icon="{{ $config['icon'] }}" width="12" class="{{ $config['iconColor'] }}"></iconify-icon>
        {{ $label }}
    </span>
@endif
