@props([
    'options' => [],
    'label' => '',
    'modelName' => '',
    'selectedValue' => null,
    'wireMethod' => null,
    'wireTargetId' => null,
    'instanceKey' => null,
    'compact' => false,
    'disabled' => false,
    /** Attributs Livewire (wire:loading, id, …) fusionnés sur le bouton — utilisé par @include depuis select-input */
    'mergeAttributes' => null,
])

@php
    $btnAttrs = $mergeAttributes instanceof \Illuminate\View\ComponentAttributeBag
        ? $mergeAttributes
        : $attributes;

    $wireMethodStr = is_string($wireMethod) && preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $wireMethod) ? $wireMethod : null;
    $useWireMethodDouble = $wireMethodStr !== null && $wireTargetId !== null;
    $useWireMethodSingle = $wireMethodStr !== null && $wireTargetId === null;
    $useWireSet = ! $useWireMethodDouble && ! $useWireMethodSingle && $modelName !== '';
    $keyBase = $instanceKey
        ?? ($modelName !== '' ? $modelName : (
            $wireMethodStr !== null
                ? ($wireTargetId !== null ? $wireMethodStr.'-'.$wireTargetId : $wireMethodStr)
                : 'dropdown'
        ));
@endphp

<div
    x-data="{ open: false }"
    class="relative w-full min-w-0"
    wire:key="dropdown-select-{{ $keyBase }}"
    @click.outside="open = false"
>
    <button
        type="button"
        @if ($disabled)
            disabled
        @else
            @click="open = !open"
        @endif
        @keydown.escape.window="open = false"
        @class([
            'dropdown-manexo-trigger' => true,
            'dropdown-manexo-trigger--compact' => $compact,
            'opacity-50 cursor-not-allowed' => $disabled,
        ])
        x-bind:aria-expanded="open"
        aria-haspopup="listbox"
        {{ $btnAttrs->filter(fn ($v, $k) => is_string($k) && ! str_starts_with($k, 'wire:model')) }}
    >
        <span class="min-w-0 flex-1 truncate">{{ $label }}</span>
        <iconify-icon
            icon="solar:alt-arrow-down-linear"
            width="18"
            class="shrink-0 text-slate-400 transition-transform duration-200"
            x-bind:class="open ? 'rotate-180' : ''"
        ></iconify-icon>
    </button>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-0.5"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-cloak
        @class([
            'dropdown-manexo-panel',
            'dropdown-manexo-panel--compact' => $compact,
        ])
        role="listbox"
    >
        @foreach ($options as $index => $opt)
            @php
                $val = $opt['value'] ?? '';
                $optLabel = $opt['label'] ?? (string) $val;
                $isActive = (string) ($selectedValue ?? '') === (string) $val;
            @endphp
            <button
                type="button"
                role="option"
                aria-selected="{{ $isActive ? 'true' : 'false' }}"
                @if ($disabled)
                    disabled
                @elseif ($useWireMethodDouble)
                    @click='$wire.{{ $wireMethodStr }}({{ (int) $wireTargetId }}, {!! json_encode($val) !!}); open = false'
                @elseif ($useWireMethodSingle)
                    @click='$wire.{{ $wireMethodStr }}({!! json_encode($val) !!}); open = false'
                @elseif ($useWireSet)
                    @click='$wire.set("{{ $modelName }}", {!! json_encode($val) !!}); open = false'
                @endif
                wire:key="dd-opt-{{ $keyBase }}-{{ $index }}"
                @class([
                    'dropdown-manexo-option',
                    'dropdown-manexo-option--compact' => $compact,
                    'dropdown-manexo-option--active' => $isActive,
                ])
            >
                {{ $optLabel }}
            </button>
        @endforeach
    </div>
</div>
