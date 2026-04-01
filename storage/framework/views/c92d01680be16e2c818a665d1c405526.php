
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'primary',
    'wireTarget' => null,
    'loadingLabel' => null,
    'iconOnly' => false,
    'type' => 'button',
    'spinnerSize' => 'md',
]));

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

foreach (array_filter(([
    'variant' => 'primary',
    'wireTarget' => null,
    'loadingLabel' => null,
    'iconOnly' => false,
    'type' => 'button',
    'spinnerSize' => 'md',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
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
?>

<button
    <?php echo e($attributes->merge(['class' => $baseClass, 'type' => $type])); ?>

    <?php if($wireTarget): ?>
        wire:loading.attr="disabled"
        wire:target="<?php echo e($wireTarget); ?>"
        wire:loading.class="cursor-wait opacity-90 scale-[0.99] transition-transform duration-75"
    <?php endif; ?>
>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($wireTarget): ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($iconOnly): ?>
        <span wire:loading.remove wire:target="<?php echo e($wireTarget); ?>" class="inline-flex items-center justify-center">
            <?php echo e($slot); ?>

        </span>
        <span wire:loading wire:target="<?php echo e($wireTarget); ?>" class="inline-flex items-center justify-center gap-1.5" role="status" aria-live="polite">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($spinnerSize !== 'none'): ?>
                <span class="<?php echo e($spinnerWrapClass); ?>" aria-hidden="true"></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <span class="<?php echo e($spinnerSize === 'none' ? 'text-xs font-semibold' : 'sr-only'); ?>"><?php echo e($loadingLabel); ?></span>
        </span>
    <?php else: ?>
        <span wire:loading.remove wire:target="<?php echo e($wireTarget); ?>" class="inline-flex items-center justify-center gap-2">
            <?php echo e($slot); ?>

        </span>
        <span wire:loading wire:target="<?php echo e($wireTarget); ?>" class="inline-flex items-center justify-center gap-2" role="status" aria-live="polite">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($spinnerSize !== 'none'): ?>
                <span class="<?php echo e($spinnerWrapClass); ?>" aria-hidden="true"></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <span><?php echo e($loadingLabel); ?></span>
        </span>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php else: ?>
    <?php echo e($slot); ?>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</button>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/components/manexo/action-button.blade.php ENDPATH**/ ?>