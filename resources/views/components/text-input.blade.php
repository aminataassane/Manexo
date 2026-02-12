@props(['disabled' => false])

<input
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => 'block w-full rounded-md border-0 bg-slate-50 py-2 px-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-[color:var(--accent)] text-sm transition-all duration-200 disabled:bg-slate-100 disabled:text-slate-500 disabled:cursor-not-allowed',
    ]) }}
>
