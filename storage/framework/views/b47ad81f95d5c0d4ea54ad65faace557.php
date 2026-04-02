<?php if (isset($component)) { $__componentOriginal69dc84650370d1d4dc1b42d016d7226b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b = $attributes; } ?>
<?php $component = App\View\Components\GuestLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\GuestLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="fade-in shadow-slate-200/50 sm:p-6 sm:w-[120%] sm:-ml-[10%] bg-white w-full border-slate-100 border rounded-xl p-5 shadow-2xl">
        <div class="text-center mb-4">
            <div class="inline-flex items-center gap-1.5 mb-2">
                <div class="relative flex items-center justify-center h-7 w-7 rounded-md bg-[#005F02]/5">
                    <iconify-icon icon="solar:key-minimalistic-square-3-linear" class="text-[#005F02] text-base" stroke-width="1.5"></iconify-icon>
                </div>
                <span class="text-base font-semibold tracking-tight text-[#002e01]"><?php echo e(config('app.name', 'Manexo')); ?></span>
            </div>
            <h1 class="font-serif text-lg font-medium tracking-tight text-slate-900"><?php echo e(__('invitations.code_title')); ?></h1>
            <p class="text-sm text-slate-600 mt-1"><?php echo e(__('invitations.code_desc')); ?></p>
        </div>

        <form method="POST" action="<?php echo e(route('invitations.process-code')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label for="code" class="text-[11px] font-semibold text-slate-700"><?php echo e(__('invitations.code_label')); ?></label>
                <input
                    id="code"
                    name="code"
                    type="text"
                    value="<?php echo e(old('code')); ?>"
                    maxlength="16"
                    class="mt-1 block w-full rounded-xl bg-white py-2.5 px-3 text-sm shadow-sm uppercase tracking-wider focus:border-[#005F02] focus:ring-[#005F02]"
                    style="border: 1px solid #e2e8f0;"
                    placeholder="<?php echo e(__('invitations.code_placeholder')); ?>"
                    required
                >
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <button type="submit" class="group flex w-full items-center justify-center gap-2 rounded-lg bg-[#005F02] px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-[#004d02] transition-all duration-200">
                <?php echo e(__('invitations.code_submit')); ?>

                <iconify-icon icon="solar:arrow-right-linear" class="text-white text-sm transition-transform group-hover:translate-x-1" stroke-width="1.5"></iconify-icon>
            </button>
        </form>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $attributes = $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $component = $__componentOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\invitations\code.blade.php ENDPATH**/ ?>