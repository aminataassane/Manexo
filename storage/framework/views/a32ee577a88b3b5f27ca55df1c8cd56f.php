<div class="max-w-md mx-auto py-10 px-4">
    <h2 class="text-2xl font-bold mb-6"><?php echo e(__('Vérification en deux étapes')); ?></h2>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$useRecoveryCode): ?>
        <p class="text-gray-700 mb-4"><?php echo e(__('Entrez le code à 6 chiffres de votre application d\'authentification.')); ?></p>

        <form wire:submit="verify">
            <input
                type="text"
                wire:model.defer="code"
                wire:loading.attr="disabled"
                wire:target="verify"
                maxlength="6"
                inputmode="numeric"
                autocomplete="one-time-code"
                class="w-full px-3 py-2 border rounded-lg mb-2 text-center text-2xl tracking-widest"
                placeholder="000000"
                autofocus
            >
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm mb-2"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['type' => 'submit','wireTarget' => 'verify','variant' => 'primary','class' => '!w-full !rounded-lg !bg-blue-600 hover:!bg-blue-700 mt-2','loadingLabel' => __('ui.action.loading')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','wire-target' => 'verify','variant' => 'primary','class' => '!w-full !rounded-lg !bg-blue-600 hover:!bg-blue-700 mt-2','loading-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.action.loading'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php echo e(__('Vérifier')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7)): ?>
<?php $attributes = $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7; ?>
<?php unset($__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7)): ?>
<?php $component = $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7; ?>
<?php unset($__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7); ?>
<?php endif; ?>
        </form>

        <button wire:click="toggleRecoveryMode" wire:loading.attr="disabled" wire:target="toggleRecoveryMode,verify" class="mt-4 text-sm text-blue-600 hover:underline">
            <?php echo e(__('Utiliser un code de récupération')); ?>

        </button>
    <?php else: ?>
        <p class="text-gray-700 mb-4"><?php echo e(__('Entrez un de vos codes de récupération.')); ?></p>

        <form wire:submit="verify">
            <input
                type="text"
                wire:model.defer="recoveryCode"
                wire:loading.attr="disabled"
                wire:target="verify"
                class="w-full px-3 py-2 border rounded-lg mb-2"
                placeholder="<?php echo e(__('Code de récupération')); ?>"
                autofocus
            >
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['recoveryCode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm mb-2"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['type' => 'submit','wireTarget' => 'verify','variant' => 'primary','class' => '!w-full !rounded-lg !bg-blue-600 hover:!bg-blue-700 mt-2','loadingLabel' => __('ui.action.loading')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','wire-target' => 'verify','variant' => 'primary','class' => '!w-full !rounded-lg !bg-blue-600 hover:!bg-blue-700 mt-2','loading-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.action.loading'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php echo e(__('Vérifier')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7)): ?>
<?php $attributes = $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7; ?>
<?php unset($__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7)): ?>
<?php $component = $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7; ?>
<?php unset($__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7); ?>
<?php endif; ?>
        </form>

        <button wire:click="toggleRecoveryMode" wire:loading.attr="disabled" wire:target="toggleRecoveryMode,verify" class="mt-4 text-sm text-blue-600 hover:underline">
            <?php echo e(__('Utiliser le code de l\'application')); ?>

        </button>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\auth\two-factor-challenge.blade.php ENDPATH**/ ?>