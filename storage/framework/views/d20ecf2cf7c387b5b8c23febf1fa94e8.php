<div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4 lg:gap-6">
    <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
        <div class="flex items-center justify-between gap-2">
            <div class="min-w-0">
                <p class="text-xs sm:text-sm font-medium text-slate-500 truncate"><?php echo e(__('pages.dashboard.my_open_tickets')); ?></p>
                <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-slate-900"><?php echo e($this->myKpis['my_open']); ?></p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 text-red-600 shrink-0">
                <iconify-icon icon="solar:danger-circle-bold-duotone" width="24"></iconify-icon>
            </div>
        </div>
    </div>
    <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
        <div class="flex items-center justify-between gap-2">
            <div class="min-w-0">
                <p class="text-xs sm:text-sm font-medium text-slate-500 truncate"><?php echo e(__('pages.dashboard.my_in_progress')); ?></p>
                <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-slate-900"><?php echo e($this->myKpis['my_in_progress']); ?></p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 shrink-0">
                <iconify-icon icon="solar:clock-circle-bold-duotone" width="24"></iconify-icon>
            </div>
        </div>
    </div>
    <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
        <div class="flex items-center justify-between gap-2">
            <div class="min-w-0">
                <p class="text-xs sm:text-sm font-medium text-slate-500 truncate"><?php echo e(__('pages.dashboard.my_total_tickets')); ?></p>
                <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-slate-900"><?php echo e($this->myKpis['my_total']); ?></p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-600 shrink-0">
                <iconify-icon icon="solar:ticket-bold-duotone" width="24"></iconify-icon>
            </div>
        </div>
    </div>
    <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
        <div class="flex items-center justify-between gap-2">
            <div class="min-w-0">
                <p class="text-xs sm:text-sm font-medium text-slate-500 truncate"><?php echo e(__('pages.dashboard.my_resolved')); ?></p>
                <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-slate-900"><?php echo e($this->myKpis['my_resolved']); ?></p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 shrink-0">
                <iconify-icon icon="solar:check-circle-bold-duotone" width="24"></iconify-icon>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/livewire/dashboard/member-kpi-cards.blade.php ENDPATH**/ ?>