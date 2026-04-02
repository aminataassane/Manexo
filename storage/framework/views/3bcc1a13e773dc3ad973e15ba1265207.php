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

        <!-- Header -->
        <div class="text-center mb-4">
            <div class="inline-flex items-center gap-1.5 mb-2 group cursor-pointer">
                <div class="relative flex items-center justify-center h-7 w-7 rounded-md bg-indigo-500/10">
                    <iconify-icon icon="solar:shield-star-bold-duotone" class="text-indigo-600 text-base transition-transform group-hover:rotate-180 duration-700" stroke-width="1.5"></iconify-icon>
                </div>
                <span class="text-base font-semibold tracking-tight text-indigo-900"><?php echo e(config('app.name', 'Manexo')); ?></span>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($error)): ?>
            <!-- Error state -->
            <div class="text-center py-4">
                <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-red-50 mb-3">
                    <iconify-icon icon="solar:danger-triangle-bold-duotone" width="24" class="text-red-500"></iconify-icon>
                </div>
                <h1 class="font-serif text-lg font-medium tracking-tight text-slate-900 mb-2"><?php echo e(__('platform_invitations.accept_title')); ?></h1>
                <p class="text-sm text-slate-600"><?php echo e($error); ?></p>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($showLogout)): ?>
                    <div class="mt-4">
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200 transition-colors">
                                <iconify-icon icon="solar:logout-2-linear" width="16"></iconify-icon>
                                <?php echo e(__('platform_invitations.logout_and_login')); ?>

                            </button>
                        </form>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="mt-4">
                    <a href="<?php echo e(route('home')); ?>" class="text-sm font-medium text-indigo-600 hover:underline"><?php echo e(__('Back')); ?></a>
                </div>
            </div>
        <?php elseif(!empty($invitation)): ?>
            <!-- Accept state -->
            <div class="text-center py-2">
                <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-indigo-50 mb-3">
                    <iconify-icon icon="solar:shield-star-bold-duotone" width="24" class="text-indigo-600"></iconify-icon>
                </div>
                <h1 class="font-serif text-lg font-medium tracking-tight text-indigo-900 mb-2"><?php echo e(__('platform_invitations.accept_title')); ?></h1>
                <p class="text-sm text-slate-600 mb-1">
                    <?php echo e(__('platform_invitations.accept_desc', ['role' => $invitation->platform_role->label()])); ?>

                </p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invitation->inviter): ?>
                    <p class="text-xs text-slate-400"><?php echo e(__('platform_invitations.invited_by', ['name' => $invitation->inviter->name])); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <form method="POST" action="<?php echo e(route('platform-invitations.process-accept', ['token' => $invitation->token])); ?>" class="mt-4">
                <?php echo csrf_field(); ?>
                <button type="submit" class="group flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-500/20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all duration-200 active:scale-[0.98]">
                    <?php echo e(__('platform_invitations.accept_button')); ?>

                    <iconify-icon icon="solar:arrow-right-linear" class="text-white text-sm transition-transform group-hover:translate-x-1" stroke-width="1.5"></iconify-icon>
                </button>
            </form>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\platform-invitations\accept.blade.php ENDPATH**/ ?>