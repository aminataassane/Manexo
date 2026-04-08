<?php
    $stepMeta = [
        'categories' => [
            'icon' => 'solar:tag-horizontal-bold-duotone',
            'title' => __('onboarding.steps.categories.title'),
            'desc' => __('onboarding.steps.categories.desc'),
            'btn' => __('onboarding.steps.categories.btn'),
        ],
        'priorities' => [
            'icon' => 'solar:flag-bold-duotone',
            'title' => __('onboarding.steps.priorities.title'),
            'desc' => __('onboarding.steps.priorities.desc'),
            'btn' => __('onboarding.steps.priorities.btn'),
        ],
        'functions' => [
            'icon' => 'solar:users-group-two-rounded-bold-duotone',
            'title' => __('onboarding.steps.functions.title'),
            'desc' => __('onboarding.steps.functions.desc'),
            'btn' => __('onboarding.steps.functions.btn'),
        ],
        'invite_team' => [
            'icon' => 'solar:user-plus-bold-duotone',
            'title' => __('onboarding.steps.invite_team.title'),
            'desc' => __('onboarding.steps.invite_team.desc'),
            'btn' => __('onboarding.steps.invite_team.btn'),
        ],
        'customize' => [
            'icon' => 'solar:palette-bold-duotone',
            'title' => __('onboarding.steps.customize.title'),
            'desc' => __('onboarding.steps.customize.desc'),
            'btn' => __('onboarding.steps.customize.btn'),
        ],
        'create_form' => [
            'icon' => 'solar:clipboard-text-bold-duotone',
            'title' => __('onboarding.steps.create_form.title'),
            'desc' => __('onboarding.steps.create_form.desc'),
            'btn' => __('onboarding.steps.create_form.btn'),
        ],
        'first_test' => [
            'icon' => 'solar:rocket-bold-duotone',
            'title' => __('onboarding.steps.first_test.title'),
            'desc' => __('onboarding.steps.first_test.desc'),
            'btn' => __('onboarding.steps.first_test.btn'),
        ],
    ];

    $steps = $this->steps;
    $stepKeys = array_keys($steps);

    // Find first incomplete step
    $firstIncomplete = null;
    foreach ($stepKeys as $k) {
        if (! $steps[$k]['completed']) {
            $firstIncomplete = $k;
            break;
        }
    }

    // Split into phases
    $phase1Keys = ['categories', 'priorities', 'functions', 'invite_team'];
    $phase2Keys = ['customize', 'create_form', 'first_test'];
?>

<div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['hidden' => ! $this->shouldShow]); ?>" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processElementKey('onboarding-checklist-root', get_defined_vars()); ?>wire:key="onboarding-checklist-root">
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->shouldShow): ?>
<div x-data="{ expanded: true }">

    
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
        <div class="min-w-0">
            <h2 class="text-base font-bold text-slate-900 tracking-tight"><?php echo e(__('onboarding.title')); ?></h2>
            <p class="mt-1 text-sm text-slate-500"><?php echo e(__('onboarding.subtitle')); ?></p>
        </div>

        <div class="flex items-center gap-4 shrink-0">
            
            <div class="flex items-center gap-2.5">
                <div class="flex gap-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'h-1.5 rounded-full transition-all duration-500',
                            'w-6 bg-[color:var(--accent)]' => $step['completed'],
                            'w-4 bg-slate-200' => ! $step['completed'],
                        ]); ?>"></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
                <span class="text-xs font-semibold text-slate-400"><?php echo e($this->completedCount); ?>/<?php echo e($this->totalSteps); ?></span>
            </div>

            <div class="flex items-center gap-1">
                <button type="button" @click="expanded = !expanded" class="h-7 w-7 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition flex items-center justify-center">
                    <iconify-icon :icon="expanded ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear'" width="14"></iconify-icon>
                </button>
                <button type="button" wire:click="dismiss" wire:loading.attr="disabled" class="h-7 w-7 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition flex items-center justify-center" title="<?php echo e(__('onboarding.dismiss')); ?>">
                    <iconify-icon icon="solar:close-circle-linear" width="14"></iconify-icon>
                </button>
            </div>
        </div>
    </div>

    
    <div x-show="expanded" x-collapse>
        <div class="space-y-5">

            
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400"><?php echo e(__('onboarding.phase1')); ?></span>
                    <div class="flex-1 h-px bg-slate-100"></div>
                </div>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $phase1Keys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($steps[$key], $stepMeta[$key])): ?>
                            <?php echo $__env->make('livewire.dashboard.partials.onboarding-step-card', [
                                'key' => $key,
                                'step' => $steps[$key],
                                'meta' => $stepMeta[$key],
                                'isCurrent' => $key === $firstIncomplete,
                                'isDone' => $steps[$key]['completed'],
                                'number' => array_search($key, $stepKeys) + 1,
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>

            
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400"><?php echo e(__('onboarding.phase2')); ?></span>
                    <div class="flex-1 h-px bg-slate-100"></div>
                </div>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $phase2Keys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($steps[$key], $stepMeta[$key])): ?>
                            <?php echo $__env->make('livewire.dashboard.partials.onboarding-step-card', [
                                'key' => $key,
                                'step' => $steps[$key],
                                'meta' => $stepMeta[$key],
                                'isCurrent' => $key === $firstIncomplete,
                                'isDone' => $steps[$key]['completed'],
                                'number' => array_search($key, $stepKeys) + 1,
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/livewire/dashboard/onboarding-checklist.blade.php ENDPATH**/ ?>