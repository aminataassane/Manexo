<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'options' => null,
    'selectedValue' => null,
    'label' => null,
    'disabled' => false,
    'compact' => false,
    'wireMethod' => null,
    'wireTargetId' => null,
    'instanceKey' => null,
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
    'options' => null,
    'selectedValue' => null,
    'label' => null,
    'disabled' => false,
    'compact' => false,
    'wireMethod' => null,
    'wireTargetId' => null,
    'instanceKey' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $useDropdown = is_array($options) && count($options) > 0;
    $modelName = '';
    foreach (['wire:model.live', 'wire:model', 'wire:model.defer', 'wire:model.lazy'] as $attr) {
        if ($attributes->has($attr)) {
            $modelName = (string) $attributes->get($attr);
            break;
        }
    }
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($useDropdown): ?>
    <?php
        if ($label === null) {
            $label = collect($options)->firstWhere(fn ($o) => (string) ($o['value'] ?? '') === (string) ($selectedValue ?? ''))['label'] ?? '';
        }
        $ddKey = $instanceKey ?? str_replace(['.', ' ', '[', ']'], '_', $modelName !== '' ? $modelName : (is_string($wireMethod) ? $wireMethod : 'dropdown'));
        $ddAttrs = $attributes->filter(fn ($v, $k) => is_string($k) && ! str_starts_with($k, 'wire:model') && $k !== 'class');
        $ddModelName = $wireMethod ? '' : $modelName;
    ?>
    <div <?php echo e($attributes->only('class')); ?>>
        <?php if (isset($component)) { $__componentOriginal67b35608722bcee218637ec24a02b934 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67b35608722bcee218637ec24a02b934 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-select','data' => ['options' => $options,'label' => $label,'selectedValue' => $selectedValue,'modelName' => $ddModelName,'wireMethod' => $wireMethod,'wireTargetId' => $wireTargetId,'instanceKey' => $ddKey,'disabled' => $disabled,'compact' => $compact,'mergeAttributes' => $ddAttrs]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($options),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($label),'selected-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedValue),'model-name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ddModelName),'wire-method' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($wireMethod),'wire-target-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($wireTargetId),'instance-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ddKey),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($disabled),'compact' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($compact),'merge-attributes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ddAttrs)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal67b35608722bcee218637ec24a02b934)): ?>
<?php $attributes = $__attributesOriginal67b35608722bcee218637ec24a02b934; ?>
<?php unset($__attributesOriginal67b35608722bcee218637ec24a02b934); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal67b35608722bcee218637ec24a02b934)): ?>
<?php $component = $__componentOriginal67b35608722bcee218637ec24a02b934; ?>
<?php unset($__componentOriginal67b35608722bcee218637ec24a02b934); ?>
<?php endif; ?>
    </div>
<?php else: ?>
    <div class="relative group/select">
        <select
            <?php echo e($disabled ? 'disabled' : ''); ?>

            <?php echo $attributes->merge(['class' => 'select-manexo-inset block w-full text-[13px] font-medium text-slate-800 disabled:cursor-not-allowed transition-colors duration-200']); ?>

        >
            <?php echo e($slot); ?>

        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 transition-colors duration-200 group-hover/select:text-slate-600">
            <iconify-icon icon="solar:alt-arrow-down-linear" width="16" class="opacity-90"></iconify-icon>
        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\components\select-input.blade.php ENDPATH**/ ?>