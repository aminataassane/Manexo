@props(['disabled' => false])

<div class="relative">
    <select
        {{ $disabled ? 'disabled' : '' }}
        {!! $attributes->merge(['class' => 'appearance-none block w-full rounded-xl border-slate-200 bg-white py-2.5 pl-3 pr-10 text-sm text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50 disabled:bg-slate-50 transition-all duration-200 ease-in-out cursor-pointer hover:border-slate-300']) !!}
    >
        {{ $slot }}
    </select>
    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
        <iconify-icon icon="solar:alt-arrow-down-linear" width="16"></iconify-icon>
    </div>
</div>
