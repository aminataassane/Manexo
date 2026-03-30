<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
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
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
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
?>

<div
    x-data="{ open: false }"
    class="relative w-full min-w-0"
    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processElementKey('dropdown-select-{{ $keyBase }}', get_defined_vars()); ?>wire:key="dropdown-select-<?php echo e($keyBase); ?>"
    @click.outside="open = false"
>
    <button
        type="button"
        <?php if($disabled): ?>
            disabled
        <?php else: ?>
            @click="open = !open"
        <?php endif; ?>
        @keydown.escape.window="open = false"
        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            'dropdown-manexo-trigger' => true,
            'dropdown-manexo-trigger--compact' => $compact,
            'opacity-50 cursor-not-allowed' => $disabled,
        ]); ?>"
        x-bind:aria-expanded="open"
        aria-haspopup="listbox"
        <?php echo e($btnAttrs->filter(fn ($v, $k) => is_string($k) && ! str_starts_with($k, 'wire:model'))); ?>

    >
        <span class="min-w-0 flex-1 truncate"><?php echo e($label); ?></span>
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
        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            'dropdown-manexo-panel',
            'dropdown-manexo-panel--compact' => $compact,
        ]); ?>"
        role="listbox"
    >
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <?php
                $val = $opt['value'] ?? '';
                $optLabel = $opt['label'] ?? (string) $val;
                $isActive = (string) ($selectedValue ?? '') === (string) $val;
            ?>
            <button
                type="button"
                role="option"
                aria-selected="<?php echo e($isActive ? 'true' : 'false'); ?>"
                <?php if($disabled): ?>
                    disabled
                <?php elseif($useWireMethodDouble): ?>
                    @click='$wire.<?php echo e($wireMethodStr); ?>(<?php echo e((int) $wireTargetId); ?>, <?php echo json_encode($val); ?>); open = false'
                <?php elseif($useWireMethodSingle): ?>
                    @click='$wire.<?php echo e($wireMethodStr); ?>(<?php echo json_encode($val); ?>); open = false'
                <?php elseif($useWireSet): ?>
                    @click='$wire.set("<?php echo e($modelName); ?>", <?php echo json_encode($val); ?>); open = false'
                <?php endif; ?>
                <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processElementKey('dd-opt-{{ $keyBase }}-{{ $index }}', get_defined_vars()); ?>wire:key="dd-opt-<?php echo e($keyBase); ?>-<?php echo e($index); ?>"
                class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                    'dropdown-manexo-option',
                    'dropdown-manexo-option--compact' => $compact,
                    'dropdown-manexo-option--active' => $isActive,
                ]); ?>"
            >
                <?php echo e($optLabel); ?>

            </button>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
</div>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/components/dropdown-select.blade.php ENDPATH**/ ?>