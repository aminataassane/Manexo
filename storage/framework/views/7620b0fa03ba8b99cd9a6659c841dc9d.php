<div class="grid grid-cols-2 gap-3 sm:gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:gap-6 min-[1920px]:gap-8">
    <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
        <div class="flex items-center justify-between gap-2">
            <div class="min-w-0">
                <p class="text-xs sm:text-sm font-medium text-slate-500 truncate"><?php echo e(__('pages.dashboard.open_tickets')); ?></p>
                <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900"><?php echo e($this->kpis['open']); ?></p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 text-red-600 group-hover:scale-110 transition-transform duration-300">
                <iconify-icon icon="solar:danger-circle-bold-duotone" width="24"></iconify-icon>
            </div>
        </div>
        <div class="mt-4 flex items-center text-xs font-medium text-red-600">
            <span class="flex items-center gap-1 bg-red-50 px-2 py-1 rounded-full">
                <iconify-icon icon="solar:arrow-right-up-linear" width="12"></iconify-icon>
                <?php echo e(__('pages.dashboard.action_required')); ?>

            </span>
        </div>
    </div>
    <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
        <div class="flex items-center justify-between gap-2">
            <div class="min-w-0">
                <p class="text-xs sm:text-sm font-medium text-slate-500 truncate"><?php echo e(__('pages.dashboard.in_progress')); ?></p>
                <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900"><?php echo e($this->kpis['in_progress']); ?></p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 group-hover:scale-110 transition-transform duration-300">
                <iconify-icon icon="solar:clock-circle-bold-duotone" width="24"></iconify-icon>
            </div>
        </div>
        <div class="mt-4 flex items-center text-xs font-medium text-blue-600">
            <span class="flex items-center gap-1 bg-blue-50 px-2 py-1 rounded-full"><?php echo e(__('pages.dashboard.active_now')); ?></span>
        </div>
    </div>
    <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
        <div class="flex items-center justify-between gap-2">
            <div class="min-w-0">
                <p class="text-xs sm:text-sm font-medium text-slate-500 truncate"><?php echo e(__('pages.dashboard.pending')); ?></p>
                <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900"><?php echo e($this->kpis['pending']); ?></p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600 group-hover:scale-110 transition-transform duration-300">
                <iconify-icon icon="solar:pause-circle-bold-duotone" width="24"></iconify-icon>
            </div>
        </div>
        <div class="mt-4 flex items-center text-xs font-medium text-amber-600">
            <span class="flex items-center gap-1 bg-amber-50 px-2 py-1 rounded-full"><?php echo e(__('pages.dashboard.customer_reply')); ?></span>
        </div>
    </div>
    <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 lg:p-6 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
        <div class="flex items-center justify-between gap-2">
            <div class="min-w-0">
                <p class="text-xs sm:text-sm font-medium text-slate-500 truncate"><?php echo e(__('pages.dashboard.resolved_7d')); ?></p>
                <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900"><?php echo e($this->kpis['resolved7d']); ?></p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 group-hover:scale-110 transition-transform duration-300">
                <iconify-icon icon="solar:check-circle-bold-duotone" width="24"></iconify-icon>
            </div>
        </div>
        <div class="mt-4 flex items-center text-xs font-medium text-emerald-600">
            <span class="flex items-center gap-1 bg-emerald-50 px-2 py-1 rounded-full">
                <iconify-icon icon="solar:graph-up-linear" width="12"></iconify-icon>
                <?php echo e(__('pages.dashboard.productivity')); ?>

            </span>
        </div>
    </div>
</div><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\dashboard\admin-kpi-cards.blade.php ENDPATH**/ ?>