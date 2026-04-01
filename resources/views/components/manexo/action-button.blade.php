{{--
    Bouton d'action MANEXO : retour immédiat + désactivation + libellé/spinner pendant requête Livewire.
    Exiger wireTarget identique à la méthode ciblée (wire:click="save" → wireTarget="save", ou submit → nom de la méthode wire:submit).

    @props variant: primary | primary-sm | secondary | super | neutral | warning | success | danger-solid | danger | ghost | custom
    @props iconOnly: bouton icône seule (spinner à la place de l’icône en chargement)
    @props spinnerSize: md | sm | none — sm = spinner discret ; none = texte seul + léger style (pas de cercle)
--}}
@props([
    'variant' => 'primary',
    'wireTarget' => null,
    'loadingLabel' => null,
    'iconOnly' => false,
    'type' => 'button',
    'spinnerSize' => 'md',
])

@php
    $loadingLabel = $loadingLabel ?? __('ui.action.loading');

    $baseClass = match ($variant) {
        'primary' => 'inline-flex items-center justify-center gap-2 rounded-xl bg-[var(--accent)] px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:opacity-90 transition-all disabled:opacity-60 disabled:cursor-not-allowed',
        'primary-sm' => 'inline-flex items-center justify-center gap-1.5 rounded-lg bg-[var(--accent)] px-3 h-9 text-xs font-bold text-white shadow-sm hover:opacity-90 transition-all disabled:opacity-60 disabled:cursor-not-allowed',
        'secondary' => 'inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all disabled:opacity-60 disabled:cursor-not-allowed',
        'super' => 'sa-btn-primary',
        'neutral' => 'inline-flex items-center justify-center gap-2 rounded-xl bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 transition-colors disabled:opacity-60 disabled:cursor-not-allowed',
        'warning' => 'inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 py-2 text-sm font-medium text-white hover:bg-amber-600 transition-colors disabled:opacity-60 disabled:cursor-not-allowed',
        'success' => 'inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-600 transition-colors disabled:opacity-60 disabled:cursor-not-allowed',
        'danger-solid' => 'inline-flex items-center justify-center gap-2 rounded-xl bg-red-500 px-4 py-2 text-sm font-medium text-white hover:bg-red-600 transition-colors disabled:opacity-60 disabled:cursor-not-allowed',
        'danger' => 'inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-red-700 transition-all disabled:opacity-60 disabled:cursor-not-allowed',
        'ghost' => 'inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition-all disabled:opacity-60 disabled:cursor-not-allowed',
        'custom' => 'inline-flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed',
        default => 'inline-flex items-center justify-center gap-2 rounded-xl bg-[var(--accent)] px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:opacity-90 transition-all disabled:opacity-60 disabled:cursor-not-allowed',
    };

    $spinnerLight = match ($variant) {
        'secondary', 'ghost', 'custom' => false,
        default => true,
    };
    $spinnerClass = $spinnerLight
        ? 'border-white/35 border-t-white'
        : 'border-slate-300 border-t-slate-600';

    $spinnerWrapClass = match ($spinnerSize) {
        'sm' => 'h-3 w-3 shrink-0 rounded-full border-2 '.$spinnerClass.' animate-spin',
        'none' => '',
        default => 'inline-block h-4 w-4 shrink-0 rounded-full border-2 '.$spinnerClass.' animate-spin',
    };
@endphp

<button
    {{ $attributes->merge(['class' => $baseClass, 'type' => $type]) }}
    @if($wireTarget)
        wire:loading.attr="disabled"
        wire:target="{{ $wireTarget }}"
        wire:loading.class="cursor-wait opacity-90 scale-[0.99] transition-transform duration-75"
    @endif
>
@if($wireTarget)
    @if($iconOnly)
        <span wire:loading.remove wire:target="{{ $wireTarget }}" class="inline-flex items-center justify-center">
            {{ $slot }}
        </span>
        <span wire:loading wire:target="{{ $wireTarget }}" class="inline-flex items-center justify-center gap-1.5" role="status" aria-live="polite">
            @if($spinnerSize !== 'none')
                <span class="{{ $spinnerWrapClass }}" aria-hidden="true"></span>
            @endif
            <span class="{{ $spinnerSize === 'none' ? 'text-xs font-semibold' : 'sr-only' }}">{{ $loadingLabel }}</span>
        </span>
    @else
        <span wire:loading.remove wire:target="{{ $wireTarget }}" class="inline-flex items-center justify-center gap-2">
            {{ $slot }}
        </span>
        <span wire:loading wire:target="{{ $wireTarget }}" class="inline-flex items-center justify-center gap-2" role="status" aria-live="polite">
            @if($spinnerSize !== 'none')
                <span class="{{ $spinnerWrapClass }}" aria-hidden="true"></span>
            @endif
            <span>{{ $loadingLabel }}</span>
        </span>
    @endif
@else
    {{ $slot }}
@endif
</button>
