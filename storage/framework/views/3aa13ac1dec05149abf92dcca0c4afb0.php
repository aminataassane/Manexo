<div class="rounded-xl sm:rounded-2xl bg-white p-4 sm:p-6 shadow-sm border border-slate-100 min-w-0 overflow-hidden">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6">
        <h3 class="text-base sm:text-lg font-bold text-slate-900"><?php echo e(__('pages.dashboard.weekly_activity')); ?></h3>
        <div class="flex bg-slate-100 p-1 rounded-lg w-fit">
            <button type="button" wire:click="setChartDays(7)" class="px-3 py-1.5 sm:py-1 text-xs font-semibold rounded-md transition-colors <?php echo e($chartDays === 7 ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-800'); ?>"><?php echo e(__('pages.dashboard.days_7')); ?></button>
            <button type="button" wire:click="setChartDays(30)" class="px-3 py-1.5 sm:py-1 text-xs font-medium rounded-md transition-colors <?php echo e($chartDays === 30 ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-800'); ?>"><?php echo e(__('pages.dashboard.days_30')); ?></button>
        </div>
    </div>
    <div class="relative h-48 sm:h-56 lg:h-64 min-[1920px]:h-72 w-full flex items-end justify-between gap-0.5 sm:gap-1 px-0 min-w-0 overflow-x-auto">
        <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 5; $i++): ?>
                <div class="border-t border-slate-100 w-full h-0"></div>
            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->chartData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <div class="relative z-10 flex flex-col items-center gap-2 flex-1 min-w-0 group cursor-default">
                <div class="relative w-full max-w-[32px] sm:max-w-[40px] h-40 sm:h-44 flex items-end justify-center mx-auto">
                    <div class="w-1.5 sm:w-2.5 rounded-full bg-slate-100 transition-all" style="height: 100%"></div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-1.5 sm:w-2.5 rounded-full transition-all shadow-lg shadow-[var(--accent-ring)]" style="background-color: var(--accent); height: <?php echo e(max(4, $bar['pct'])); ?>%"></div>
                </div>
                <span class="text-[10px] sm:text-xs font-medium text-slate-400 truncate w-full text-center" title="<?php echo e($bar['count']); ?>"><?php echo e($bar['label']); ?></span>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
</div><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\dashboard\activity-chart.blade.php ENDPATH**/ ?>