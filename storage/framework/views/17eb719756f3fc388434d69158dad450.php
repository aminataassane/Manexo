<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => 'U',
    'size' => 'h-9 w-9',
    'class' => '',
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
    'name' => 'U',
    'size' => 'h-9 w-9',
    'class' => '',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
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
?>

<div <?php echo e($attributes->merge(['class' => trim("{$size} rounded-full flex items-center justify-center font-extrabold {$class}")])); ?>

     style="background: var(--accent-soft); color: var(--accent);">
    <span class="text-[11px] sm:text-xs leading-none"><?php echo e($initials); ?></span>
</div>

<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/components/avatar.blade.php ENDPATH**/ ?>