@props([
    'options' => null,
    'selectedValue' => null,
    'label' => null,
    'disabled' => false,
    'compact' => false,
    'wireMethod' => null,
    'wireTargetId' => null,
    'instanceKey' => null,
])

@php
    $useDropdown = is_array($options) && count($options) > 0;
    $modelName = '';
    foreach (['wire:model.live', 'wire:model', 'wire:model.defer', 'wire:model.lazy'] as $attr) {
        if ($attributes->has($attr)) {
            $modelName = (string) $attributes->get($attr);
            break;
        }
    }
@endphp

@if ($useDropdown)
    @php
        if ($label === null) {
            $label = collect($options)->firstWhere(fn ($o) => (string) ($o['value'] ?? '') === (string) ($selectedValue ?? ''))['label'] ?? '';
        }
        $ddKey = $instanceKey ?? str_replace(['.', ' ', '[', ']'], '_', $modelName !== '' ? $modelName : (is_string($wireMethod) ? $wireMethod : 'dropdown'));
        $ddAttrs = $attributes->filter(fn ($v, $k) => is_string($k) && ! str_starts_with($k, 'wire:model') && $k !== 'class');
        $ddModelName = $wireMethod ? '' : $modelName;
    @endphp
    <div {{ $attributes->only('class') }}>
        <x-dropdown-select
            :options="$options"
            :label="$label"
            :selected-value="$selectedValue"
            :model-name="$ddModelName"
            :wire-method="$wireMethod"
            :wire-target-id="$wireTargetId"
            :instance-key="$ddKey"
            :disabled="$disabled"
            :compact="$compact"
            :merge-attributes="$ddAttrs"
        />
    </div>
@else
    <div class="relative group/select">
        <select
            {{ $disabled ? 'disabled' : '' }}
            {!! $attributes->merge(['class' => 'select-manexo-inset block w-full text-[13px] font-medium text-slate-800 disabled:cursor-not-allowed transition-colors duration-200']) !!}
        >
            {{ $slot }}
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 transition-colors duration-200 group-hover/select:text-slate-600">
            <iconify-icon icon="solar:alt-arrow-down-linear" width="16" class="opacity-90"></iconify-icon>
        </div>
    </div>
@endif
