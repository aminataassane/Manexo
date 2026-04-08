<div
    <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processElementKey('onboarding-step-{{ $key }}', get_defined_vars()); ?>wire:key="onboarding-step-<?php echo e($key); ?>"
    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
        'relative rounded-xl border p-4 transition-all duration-200',
        'border-slate-100 bg-slate-50/50' => $isDone,
        'border-[color:var(--accent-soft-2)] bg-white shadow-sm ring-1 ring-[color:var(--accent-soft)]' => $isCurrent && ! $isDone,
        'border-slate-200 bg-white hover:border-slate-300' => ! $isCurrent && ! $isDone,
    ]); ?>"
>
    
    <div class="flex items-center justify-between mb-3">
        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            'flex h-6 w-6 items-center justify-center rounded-full text-[10px] font-bold',
            'bg-emerald-500 text-white' => $isDone,
            'bg-[color:var(--accent)] text-white' => $isCurrent && ! $isDone,
            'bg-slate-100 text-slate-400' => ! $isCurrent && ! $isDone,
        ]); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isDone): ?>
                <iconify-icon icon="solar:check-read-linear" width="12"></iconify-icon>
            <?php else: ?>
                <?php echo e($number); ?>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isDone): ?>
            <span class="text-[10px] font-semibold text-emerald-600"><?php echo e(__('onboarding.done')); ?></span>
        <?php elseif($isCurrent): ?>
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background-color: var(--accent);"></span>
                <span class="relative inline-flex rounded-full h-2 w-2" style="background-color: var(--accent);"></span>
            </span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <iconify-icon
        icon="<?php echo e($meta['icon']); ?>"
        width="22"
        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            'mb-2 block',
            'text-slate-300' => $isDone,
            'text-[color:var(--accent)]' => $isCurrent && ! $isDone,
            'text-slate-400' => ! $isCurrent && ! $isDone,
        ]); ?>"
    ></iconify-icon>

    
    <h3 class="<?php echo \Illuminate\Support\Arr::toCssClasses([
        'text-[13px] font-semibold leading-snug',
        'text-slate-400 line-through decoration-slate-300' => $isDone,
        'text-slate-900' => ! $isDone,
    ]); ?>">
        <?php echo e($meta['title']); ?>

    </h3>
    <p class="<?php echo \Illuminate\Support\Arr::toCssClasses([
        'mt-1 text-[11px] leading-relaxed',
        'text-slate-400' => $isDone,
        'text-slate-500' => ! $isDone,
    ]); ?>">
        <?php echo e($meta['desc']); ?>

    </p>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $isDone && $step['route']): ?>
        <a
            href="<?php echo e(isset($step['route_params']) ? route($step['route'], $step['route_params']) : route($step['route'])); ?>"
            wire:navigate.hover
            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                'mt-3 inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-[11px] font-semibold transition-all',
                'text-white shadow-sm hover:opacity-90' => $isCurrent,
                'text-slate-600 bg-slate-100 hover:bg-slate-200' => ! $isCurrent,
            ]); ?>"
            <?php if($isCurrent): ?> style="background-color: var(--accent);" <?php endif; ?>
        >
            <?php echo e($meta['btn']); ?>

            <iconify-icon icon="solar:arrow-right-linear" width="12"></iconify-icon>
        </a>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\dashboard\partials\onboarding-step-card.blade.php ENDPATH**/ ?>