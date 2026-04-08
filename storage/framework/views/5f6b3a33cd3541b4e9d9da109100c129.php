
<div class="w-full max-w-full min-w-0 mx-auto">
<?php
    $closedByCategoryTotal = max(1, array_sum(array_column($stats['byCategoryClosed'] ?? [], 'count')));
?>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canViewAll): ?>
<div class="w-full max-w-full min-w-0 mx-auto">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('share_success')): ?>
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition>
            <iconify-icon icon="solar:check-circle-bold" width="18" class="mr-2 align-middle inline-block"></iconify-icon>
            <?php echo e(session('share_success')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="page-header">
        <div class="min-w-0 flex-1">
            <h1 class="page-title"><?php echo e(__('task_report.title')); ?></h1>
            <p class="page-subtitle"><?php echo e(__('task_report.subtitle')); ?></p>
        </div>
        <div class="page-actions reports-page-actions">
            <div class="view-toggle view-toggle--scroll flex-shrink-0">
                <button wire:click="setPeriod('today')" wire:loading.attr="disabled" wire:target="setPeriod,period" type="button" class="px-2.5 sm:px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all whitespace-nowrap <?php echo e($period === 'today' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'); ?>"><?php echo e(__('task_report.today')); ?></button>
                <button wire:click="setPeriod('week')" wire:loading.attr="disabled" wire:target="setPeriod,period" type="button" class="px-2.5 sm:px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all whitespace-nowrap <?php echo e($period === 'week' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'); ?>"><?php echo e(__('task_report.this_week')); ?></button>
                <button wire:click="setPeriod('month')" wire:loading.attr="disabled" wire:target="setPeriod,period" type="button" class="px-2.5 sm:px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all whitespace-nowrap <?php echo e($period === 'month' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'); ?>"><?php echo e(__('task_report.this_month')); ?></button>
                <button wire:click="setPeriod('custom')" wire:loading.attr="disabled" wire:target="setPeriod,period" type="button" class="px-2.5 sm:px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all whitespace-nowrap <?php echo e($period === 'custom' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'); ?>"><?php echo e(__('task_report.custom')); ?></button>
            </div>
            <?php if (isset($component)) { $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown','data' => ['align' => 'right','width' => '48','contentClasses' => 'py-1 bg-white rounded-xl shadow-xl border border-slate-200']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['align' => 'right','width' => '48','contentClasses' => 'py-1 bg-white rounded-xl shadow-xl border border-slate-200']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                 <?php $__env->slot('trigger', null, []); ?> 
                    <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 sm:px-4 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                        <iconify-icon icon="solar:export-linear" width="18"></iconify-icon>
                        <?php echo e(__('task_report.export')); ?>

                    </button>
                 <?php $__env->endSlot(); ?>
                 <?php $__env->slot('content', null, []); ?> 
                    <a href="<?php echo e(route('reports.tasks.export', ['format' => 'csv', 'period' => $period, 'dateFrom' => $dateFrom, 'dateTo' => $dateTo])); ?>" target="_blank" rel="noopener" class="block w-full px-4 py-2.5 text-start text-sm text-slate-700 hover:bg-slate-100 transition-colors flex items-center gap-2 rounded-lg mx-1" x-data="{ loading: false }" @click="loading = true; setTimeout(() => loading = false, 5000)">
                        <iconify-icon x-show="!loading" icon="solar:document-text-bold-duotone" width="16"></iconify-icon>
                        <iconify-icon x-show="loading" x-cloak icon="solar:refresh-linear" width="16" class="animate-spin"></iconify-icon>
                        <span x-show="!loading"><?php echo e(__('task_report.export_csv')); ?></span>
                        <span x-show="loading" x-cloak class="text-slate-400"><?php echo e(__('Génération...')); ?></span>
                    </a>
                    <a href="<?php echo e(route('reports.tasks.export', ['format' => 'pdf', 'period' => $period, 'dateFrom' => $dateFrom, 'dateTo' => $dateTo])); ?>" target="_blank" rel="noopener" class="block w-full px-4 py-2.5 text-start text-sm text-slate-700 hover:bg-slate-100 transition-colors flex items-center gap-2 rounded-lg mx-1" x-data="{ loading: false }" @click="loading = true; setTimeout(() => loading = false, 8000)">
                        <iconify-icon x-show="!loading" icon="solar:file-bold-duotone" width="16"></iconify-icon>
                        <iconify-icon x-show="loading" x-cloak icon="solar:refresh-linear" width="16" class="animate-spin"></iconify-icon>
                        <span x-show="!loading"><?php echo e(__('task_report.export_pdf')); ?></span>
                        <span x-show="loading" x-cloak class="text-slate-400"><?php echo e(__('Génération...')); ?></span>
                    </a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canShare): ?>
                        <div class="border-t border-slate-100 my-1"></div>
                        <button wire:click="openShareModal" wire:loading.attr="disabled" wire:target="openShareModal" type="button" class="block w-full px-4 py-2.5 text-start text-sm text-slate-700 hover:bg-slate-100 transition-colors flex items-center gap-2 rounded-lg mx-1">
                            <iconify-icon icon="solar:share-bold-duotone" width="16"></iconify-icon>
                            <?php echo e(__('task_report.share')); ?>

                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                 <?php $__env->endSlot(); ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $attributes = $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $component = $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($period === 'custom'): ?>
        <div class="mb-6 rounded-xl sm:rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center gap-2 px-4 sm:px-5 py-3 border-b border-slate-50 bg-slate-50/50">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[var(--accent)]/10 text-[var(--accent)]">
                    <iconify-icon icon="solar:calendar-minimalistic-bold-duotone" width="20"></iconify-icon>
                </div>
                <span class="text-sm font-semibold text-slate-700"><?php echo e(__('task_report.custom')); ?></span>
            </div>
            <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-end gap-4 sm:gap-6">
                <div class="flex-1 min-w-0">
                    <label for="dateFrom" class="block text-xs font-medium uppercase tracking-wider text-slate-500 mb-1.5"><?php echo e(__('task_report.from')); ?></label>
                    <input type="date" id="dateFrom" name="date_from" wire:model.blur="dateFrom" wire:loading.attr="disabled" wire:target="dateFrom,dateTo,setPeriod,period" class="block w-full min-w-0 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-[var(--accent)] focus:outline-none focus:ring-2 focus:ring-[var(--accent)]/20 transition-colors" />
                </div>
                <div class="hidden sm:flex items-center pb-2.5 text-slate-300 shrink-0" aria-hidden="true">
                    <iconify-icon icon="solar:arrow-right-linear" width="20"></iconify-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <label for="dateTo" class="block text-xs font-medium uppercase tracking-wider text-slate-500 mb-1.5"><?php echo e(__('task_report.to')); ?></label>
                    <input type="date" id="dateTo" name="date_to" wire:model.blur="dateTo" wire:loading.attr="disabled" wire:target="dateFrom,dateTo,setPeriod,period" class="block w-full min-w-0 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-[var(--accent)] focus:outline-none focus:ring-2 focus:ring-[var(--accent)]/20 transition-colors" />
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6 sm:mb-8 lg:grid-cols-3 min-[1920px]:gap-6">
        <div class="stat-card">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label"><?php echo e(__('task_report.tickets_closed')); ?></span>
                    <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900"><?php echo e(number_format($stats['closedTicketsCount'] ?? 0, 0, ',', ' ')); ?></div>
                </div>
                <div class="stat-card-icon bg-emerald-50 text-emerald-600">
                    <iconify-icon icon="solar:check-circle-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label"><?php echo e(__('task_report.top_category')); ?></span>
                    <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900 truncate"><?php echo e($stats['topCategoryClosed']['name'] ?? '—'); ?></div>
                    <p class="text-xs text-slate-500 mt-0.5"><?php echo e($stats['topCategoryClosed']['count'] ?? 0); ?> <?php echo e(trans_choice('task_report.tickets_count', $stats['topCategoryClosed']['count'] ?? 0)); ?></p>
                </div>
                <div class="stat-card-icon bg-amber-50 text-amber-600">
                    <iconify-icon icon="solar:tag-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label"><?php echo e(__('task_report.top_user')); ?></span>
                    <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900 truncate"><?php echo e($stats['topUserClosed']['name'] ?? '—'); ?></div>
                    <p class="text-xs text-slate-500 mt-0.5"><?php echo e($stats['topUserClosed']['count'] ?? 0); ?> <?php echo e(trans_choice('task_report.tickets_count', $stats['topUserClosed']['count'] ?? 0)); ?></p>
                </div>
                <div class="stat-card-icon bg-emerald-50 text-emerald-600">
                    <iconify-icon icon="solar:user-check-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <p class="text-xs text-slate-500 mb-5 mt-0"><?php echo e(__('task_report.period_label', ['from' => $from->format('d/m/Y'), 'to' => $to->format('d/m/Y')])); ?></p>

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8 min-w-0">
        <div class="content-card min-w-0">
            <div class="px-4 sm:px-6 py-4 border-b border-slate-50 bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-900"><?php echo e(__('task_report.closed_by_category')); ?></h3>
            </div>
            <div class="p-4 sm:p-6 space-y-5">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $stats['byCategoryClosed'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php $pct = round(($cat['count'] / $closedByCategoryTotal) * 100); ?>
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-slate-700"><?php echo e($cat['name']); ?></span>
                                <span class="text-sm font-bold text-slate-900"><?php echo e($cat['count']); ?> <span class="text-slate-400 font-normal">(<?php echo e($pct); ?>%)</span></span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                <div class="h-full rounded-full bg-slate-800 transition-all duration-500" style="width: <?php echo e($pct); ?>%"></div>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <p class="text-center text-sm text-slate-500 py-8"><?php echo e(__('task_report.no_data')); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <div class="content-card min-w-0">
            <div class="px-4 sm:px-6 py-4 border-b border-slate-50 bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-900"><?php echo e(__('task_report.closed_by_user')); ?></h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[240px]">
                        <tbody class="divide-y divide-slate-100">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $stats['byUserClosed'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <?php $total = $stats['closedTicketsCount'] ?? 1; $pct = $total > 0 ? round(($row['count'] / $total) * 100) : 0; ?>
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-4 sm:px-6 py-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <span class="h-8 w-8 rounded-full shrink-0 ring-2 ring-white shadow bg-slate-100 text-slate-700 inline-flex items-center justify-center text-xs font-semibold">
                                                <?php echo e(\Illuminate\Support\Str::of($row['name'] ?? 'U')->explode(' ')->take(2)->map(fn ($p) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($p, 0, 1)))->implode('')); ?>

                                            </span>
                                            <span class="text-sm font-medium text-slate-900 truncate"><?php echo e($row['name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-3 text-right">
                                        <span class="text-sm font-semibold text-slate-700"><?php echo e($row['count']); ?></span>
                                        <span class="text-xs text-slate-400 ml-1">(<?php echo e($pct); ?>%)</span>
                                        <div class="w-14 h-1.5 bg-slate-100 rounded-full mt-1 ml-auto overflow-hidden max-w-[60px]">
                                            <div class="h-full rounded-full bg-[var(--accent)]" style="width: <?php echo e(min(100, $pct)); ?>%"></div>
                                        </div>
                                    </td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr><td colspan="2" class="px-4 sm:px-6 py-8 text-center text-sm text-slate-500"><?php echo e(__('task_report.no_data')); ?></td></tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div class="relative rounded-xl sm:rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden min-w-0">
        <div wire:loading.flex wire:target="setPeriod,period,dateFrom,dateTo,nextPage,previousPage,gotoPage,setPage" class="absolute inset-0 z-10 items-center justify-center bg-white/60 backdrop-blur-[1px] text-xs text-slate-500">
            <?php echo e(__('reports.menu_refresh')); ?>...
        </div>
        <div class="px-4 sm:px-6 py-4 border-b border-slate-50 bg-slate-50/50">
            <h3 class="text-base font-bold text-slate-900"><?php echo e(__('task_report.closed_tickets')); ?></h3>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($stats['closedTicketsCount'] ?? 0) > 0): ?>
                <p class="text-xs text-slate-500 mt-0.5"><?php echo e($stats['closedTicketsCount']); ?> <?php echo e(trans_choice('task_report.tickets_count', $stats['closedTicketsCount'])); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[640px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"><?php echo e(__('task_report.ticket')); ?></th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"><?php echo e(__('task_report.category')); ?></th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"><?php echo e(__('task_report.closed_at')); ?></th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"><?php echo e(__('task_report.closed_by')); ?></th>
                            <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"><?php echo e(__('task_report.checklist_progress')); ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->closedTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 sm:px-6 py-3 sm:py-4">
                                    <a href="<?php echo e(route('tickets.discussion', $ticket)); ?>" wire:navigate class="text-sm font-medium text-[var(--accent)] hover:underline truncate max-w-[200px] sm:max-w-[280px] inline-block"><?php echo e(Str::limit($ticket->subject, 50)); ?></a>
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-sm text-slate-600"><?php echo e($ticket->category?->name ?? '—'); ?></td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-sm text-slate-500 whitespace-nowrap"><?php echo e($ticket->updated_at->format('d/m/Y H:i')); ?></td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-sm text-slate-600"><?php echo e($ticket->closedByUser?->name ?? $ticket->assignees->first()?->name ?? $ticket->creator?->name ?? '—'); ?></td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4">
                                    <?php $done = (int) ($ticket->checklist_done_count ?? 0); $total = (int) ($ticket->checklist_items_count ?? 0); ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($total > 0): ?>
                                        <span class="inline-flex items-center justify-center min-w-[2.5rem] px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 text-sm font-medium"><?php echo e($done); ?>/<?php echo e($total); ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-400">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="5" class="px-4 sm:px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                            <iconify-icon icon="solar:inbox-line-linear" width="28"></iconify-icon>
                                        </div>
                                        <p class="text-sm font-medium text-slate-600"><?php echo e(__('task_report.no_data')); ?></p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
            </table>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->closedTickets->hasPages()): ?>
            <div class="px-4 sm:px-6 py-4 border-t border-slate-100 bg-slate-50/30">
                <?php echo e($this->closedTickets->links('vendor.pagination.manexo')); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canShare && $showShareModal): ?>
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4" x-data @keydown.escape.window="$wire.closeShareModal()">
            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl p-6" @click.outside="$wire.closeShareModal()">
                    <button type="button" wire:click="closeShareModal" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition-colors rounded-lg p-1">
                        <iconify-icon icon="solar:close-circle-linear" width="24"></iconify-icon>
                    </button>
                    <h3 class="text-lg font-bold text-slate-900 mb-1"><?php echo e(__('task_report.share')); ?></h3>
                    <p class="text-sm text-slate-500 mb-6"><?php echo e(__('task_report.link_valid_7_days')); ?></p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$lastSignedUrl): ?>
                        <button wire:click="generateSignedUrl" wire:loading.attr="disabled" wire:target="generateSignedUrl" type="button" class="w-full rounded-xl bg-[var(--accent)] px-4 py-3 text-sm font-medium text-white shadow-lg hover:opacity-90 transition-opacity">
                            <iconify-icon icon="solar:link-bold" width="16" class="mr-1 align-text-bottom"></iconify-icon>
                            <?php echo e(__('task_report.generate_link')); ?>

                        </button>
                    <?php else: ?>
                        <div class="mb-5" x-data="{ copied: false }">
                            <label for="share-link-input" class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1.5 block"><?php echo e(__('task_report.copy_link')); ?></label>
                            <div class="flex items-center gap-2">
                                <input id="share-link-input" name="share_link" type="text" readonly value="<?php echo e($lastSignedUrl); ?>" class="flex-1 rounded-xl border-slate-200 bg-slate-50 text-xs text-slate-600 px-3 py-2.5" />
                                <button type="button" @click="navigator.clipboard.writeText('<?php echo e($lastSignedUrl); ?>'); copied = true; setTimeout(() => copied = false, 2000)" class="shrink-0 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                                    <span x-show="!copied"><iconify-icon icon="solar:copy-linear" width="14"></iconify-icon></span>
                                    <span x-show="copied" x-cloak class="text-emerald-600"><?php echo e(__('task_report.link_copied')); ?></span>
                                </button>
                            </div>
                        </div>
                        <div class="border-t border-slate-100 pt-5">
                            <label for="shareUser" class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1.5 block"><?php echo e(__('task_report.send_to')); ?></label>
                            <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['id' => 'shareUser','name' => 'share_user_id','wire:model' => 'shareUserId']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'shareUser','name' => 'share_user_id','wire:model' => 'shareUserId']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <option value="">—</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->staffMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <option value="<?php echo e($member->id); ?>"><?php echo e($member->name); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36)): ?>
<?php $attributes = $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36; ?>
<?php unset($__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36)): ?>
<?php $component = $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36; ?>
<?php unset($__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36); ?>
<?php endif; ?>
                            <button wire:click="sendShareNotification" wire:loading.attr="disabled" wire:target="sendShareNotification" type="button" class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-medium text-white hover:bg-slate-800 transition-colors disabled:opacity-50" <?php echo e(!$shareUserId ? 'disabled' : ''); ?>>
                                <iconify-icon icon="solar:plain-bold" width="16" class="mr-1 align-text-bottom"></iconify-icon>
                                <?php echo e(__('task_report.send_notification')); ?>

                            </button>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<?php else: ?>
<div class="w-full max-w-full min-w-0 mx-auto">
    
    <div class="page-header">
        <div class="min-w-0 flex-1">
            <h1 class="page-title"><?php echo e(__('task_report.my_tasks_title')); ?></h1>
            <p class="page-subtitle"><?php echo e(__('task_report.my_tasks_subtitle')); ?></p>
        </div>
        <div class="page-actions reports-page-actions">
            <div class="view-toggle view-toggle--scroll flex-shrink-0">
                <button wire:click="setPeriod('week')" wire:loading.attr="disabled" wire:target="setPeriod,period" type="button" class="px-2.5 sm:px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all whitespace-nowrap <?php echo e($period === 'week' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'); ?>"><?php echo e(__('task_report.this_week')); ?></button>
                <button wire:click="setPeriod('month')" wire:loading.attr="disabled" wire:target="setPeriod,period" type="button" class="px-2.5 sm:px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all whitespace-nowrap <?php echo e($period === 'month' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'); ?>"><?php echo e(__('task_report.this_month')); ?></button>
                <button wire:click="setPeriod('custom')" wire:loading.attr="disabled" wire:target="setPeriod,period" type="button" class="px-2.5 sm:px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all whitespace-nowrap <?php echo e($period === 'custom' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'); ?>"><?php echo e(__('task_report.custom')); ?></button>
            </div>
            <?php if (isset($component)) { $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown','data' => ['align' => 'right','width' => '48','contentClasses' => 'py-1 bg-white rounded-xl shadow-xl border border-slate-200']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['align' => 'right','width' => '48','contentClasses' => 'py-1 bg-white rounded-xl shadow-xl border border-slate-200']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                 <?php $__env->slot('trigger', null, []); ?> 
                    <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 sm:px-4 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                        <iconify-icon icon="solar:export-linear" width="18"></iconify-icon>
                        <?php echo e(__('task_report.export')); ?>

                    </button>
                 <?php $__env->endSlot(); ?>
                 <?php $__env->slot('content', null, []); ?> 
                    <a href="<?php echo e(route('reports.tasks.export', ['format' => 'csv', 'period' => $period, 'dateFrom' => $dateFrom, 'dateTo' => $dateTo])); ?>" target="_blank" rel="noopener" class="block w-full px-4 py-2.5 text-start text-sm text-slate-700 hover:bg-slate-100 transition-colors flex items-center gap-2 rounded-lg mx-1" x-data="{ loading: false }" @click="loading = true; setTimeout(() => loading = false, 5000)">
                        <iconify-icon x-show="!loading" icon="solar:document-text-bold-duotone" width="16"></iconify-icon>
                        <iconify-icon x-show="loading" x-cloak icon="solar:refresh-linear" width="16" class="animate-spin"></iconify-icon>
                        <span x-show="!loading"><?php echo e(__('task_report.export_csv')); ?></span>
                        <span x-show="loading" x-cloak class="text-slate-400"><?php echo e(__('Génération...')); ?></span>
                    </a>
                    <a href="<?php echo e(route('reports.tasks.export', ['format' => 'pdf', 'period' => $period, 'dateFrom' => $dateFrom, 'dateTo' => $dateTo])); ?>" target="_blank" rel="noopener" class="block w-full px-4 py-2.5 text-start text-sm text-slate-700 hover:bg-slate-100 transition-colors flex items-center gap-2 rounded-lg mx-1" x-data="{ loading: false }" @click="loading = true; setTimeout(() => loading = false, 8000)">
                        <iconify-icon x-show="!loading" icon="solar:file-bold-duotone" width="16"></iconify-icon>
                        <iconify-icon x-show="loading" x-cloak icon="solar:refresh-linear" width="16" class="animate-spin"></iconify-icon>
                        <span x-show="!loading"><?php echo e(__('task_report.export_pdf')); ?></span>
                        <span x-show="loading" x-cloak class="text-slate-400"><?php echo e(__('Génération...')); ?></span>
                    </a>
                 <?php $__env->endSlot(); ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $attributes = $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $component = $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($period === 'custom'): ?>
        <div class="mb-6 rounded-xl sm:rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center gap-2 px-4 sm:px-5 py-3 border-b border-slate-50 bg-slate-50/50">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[var(--accent)]/10 text-[var(--accent)]">
                    <iconify-icon icon="solar:calendar-minimalistic-bold-duotone" width="20"></iconify-icon>
                </div>
                <span class="text-sm font-semibold text-slate-700"><?php echo e(__('task_report.custom')); ?></span>
            </div>
            <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-end gap-4 sm:gap-6">
                <div class="flex-1 min-w-0">
                    <label for="dateFromMember" class="block text-xs font-medium uppercase tracking-wider text-slate-500 mb-1.5"><?php echo e(__('task_report.from')); ?></label>
                    <input type="date" id="dateFromMember" name="date_from_member" wire:model.blur="dateFrom" wire:loading.attr="disabled" wire:target="dateFrom,dateTo,setPeriod,period" class="block w-full min-w-0 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-[var(--accent)] focus:outline-none focus:ring-2 focus:ring-[var(--accent)]/20 transition-colors" />
                </div>
                <div class="hidden sm:flex items-center pb-2.5 text-slate-300 shrink-0" aria-hidden="true">
                    <iconify-icon icon="solar:arrow-right-linear" width="20"></iconify-icon>
                </div>
                <div class="flex-1 min-w-0">
                    <label for="dateToMember" class="block text-xs font-medium uppercase tracking-wider text-slate-500 mb-1.5"><?php echo e(__('task_report.to')); ?></label>
                    <input type="date" id="dateToMember" name="date_to_member" wire:model.blur="dateTo" wire:loading.attr="disabled" wire:target="dateFrom,dateTo,setPeriod,period" class="block w-full min-w-0 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-[var(--accent)] focus:outline-none focus:ring-2 focus:ring-[var(--accent)]/20 transition-colors" />
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <div class="stat-card">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label"><?php echo e(__('task_report.tickets_closed')); ?></span>
                    <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900"><?php echo e($stats['closedTicketsCount'] ?? 0); ?></div>
                </div>
                <div class="stat-card-icon bg-emerald-50 text-emerald-600">
                    <iconify-icon icon="solar:check-circle-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label"><?php echo e(__('task_report.sub_tasks_completed')); ?></span>
                    <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900"><?php echo e($stats['tasksTotal'] ?? 0); ?></div>
                </div>
                <div class="stat-card-icon bg-slate-100 text-slate-600">
                    <iconify-icon icon="solar:list-check-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <p class="text-xs text-slate-500 mb-5 mt-0"><?php echo e(__('task_report.period_label', ['from' => $from->format('d/m'), 'to' => $to->format('d/m')])); ?></p>

    
    <div class="relative rounded-xl sm:rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden min-w-0">
        <div wire:loading.flex wire:target="setPeriod,period,dateFrom,dateTo,nextPage,previousPage,gotoPage,setPage" class="absolute inset-0 z-10 items-center justify-center bg-white/60 backdrop-blur-[1px] text-xs text-slate-500">
            <?php echo e(__('reports.menu_refresh')); ?>...
        </div>
        <div class="px-4 sm:px-6 py-4 border-b border-slate-50 bg-slate-50/50">
            <h3 class="text-base font-bold text-slate-900"><?php echo e(__('task_report.closed_tickets')); ?></h3>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($stats['closedTicketsCount'] ?? 0) > 0): ?>
                <p class="text-xs text-slate-500 mt-0.5"><?php echo e($stats['closedTicketsCount']); ?> <?php echo e(trans_choice('task_report.tickets_count', $stats['closedTicketsCount'])); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($stats['closedTicketsCount'] ?? 0) > 0): ?>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full min-w-[520px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-4 sm:px-6 py-3 text-left text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500"><?php echo e(__('task_report.ticket')); ?></th>
                            <th class="px-4 sm:px-6 py-3 text-left text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500 hidden sm:table-cell"><?php echo e(__('task_report.category')); ?></th>
                            <th class="px-4 sm:px-6 py-3 text-left text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500"><?php echo e(__('task_report.checklist_progress')); ?></th>
                            <th class="px-4 sm:px-6 py-3 text-right text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500"><?php echo e(__('task_report.closed_at')); ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->closedTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 sm:px-6 py-3 sm:py-4">
                                    <a href="<?php echo e(route('tickets.discussion', $ticket)); ?>" wire:navigate class="group flex items-center gap-3 min-w-0">
                                        <span class="flex h-8 w-8 sm:h-9 sm:w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 group-hover:bg-[var(--accent)]/10 group-hover:text-[var(--accent)] transition-colors">
                                            <iconify-icon icon="solar:ticket-bold" width="16"></iconify-icon>
                                        </span>
                                        <span class="text-sm font-medium text-slate-900 group-hover:text-[var(--accent)] truncate"><?php echo e(Str::limit($ticket->subject, 45)); ?></span>
                                    </a>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->category?->name): ?>
                                        <p class="text-xs text-slate-500 mt-0.5 sm:hidden pl-11"><?php echo e($ticket->category->name); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-sm text-slate-600 hidden sm:table-cell">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->category?->name): ?>
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600"><?php echo e($ticket->category->name); ?></span>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4">
                                    <?php $done = (int) ($ticket->checklist_done_count ?? 0); $total = (int) ($ticket->checklist_items_count ?? 0); ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($total > 0): ?>
                                        <span class="inline-flex items-center justify-center min-w-[2.25rem] px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 text-xs sm:text-sm font-medium"><?php echo e($done); ?>/<?php echo e($total); ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-400 text-sm">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-right text-sm text-slate-500 whitespace-nowrap"><?php echo e($ticket->updated_at->format('d/m/Y')); ?></td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="4" class="px-4 sm:px-6 py-12 text-center text-sm text-slate-500"><?php echo e(__('task_report.no_data')); ?></td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->closedTickets->hasPages()): ?>
                <div class="px-4 sm:px-6 py-4 border-t border-slate-100 bg-slate-50/30">
                    <?php echo e($this->closedTickets->links('vendor.pagination.manexo')); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php else: ?>
            <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-300 mb-3">
                    <iconify-icon icon="solar:ticket-linear" width="32"></iconify-icon>
                </div>
                <p class="text-sm font-semibold text-slate-700"><?php echo e(__('task_report.no_data')); ?></p>
                <p class="text-xs text-slate-500 mt-1"><?php echo e(__('task_report.my_tasks_empty_hint')); ?></p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\reports\task-report.blade.php ENDPATH**/ ?>