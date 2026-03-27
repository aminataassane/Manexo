<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['disabled' => false]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['disabled' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="relative">
    <select
        <?php echo e($disabled ? 'disabled' : ''); ?>

        <?php echo $attributes->merge(['class' => 'appearance-none block w-full rounded-xl border-slate-200 bg-white py-2.5 pl-3 pr-10 text-sm text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] disabled:opacity-50 disabled:bg-slate-50 transition-all duration-200 ease-in-out cursor-pointer hover:border-slate-300']); ?>

    >
        <?php echo e($slot); ?>

    </select>
    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
        <iconify-icon icon="solar:alt-arrow-down-linear" width="16"></iconify-icon>
    </div>
</div>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/components/select-input.blade.php ENDPATH**/ ?>