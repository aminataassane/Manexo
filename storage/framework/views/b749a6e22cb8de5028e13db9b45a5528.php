<?php
    $avgFirstResp = $summary['avg_first_response_seconds'] ?? null;
    $avgResolution = $summary['avg_resolution_seconds'] ?? null;

    $fmtDuration = function (?float $seconds) {
        if ($seconds === null || $seconds <= 0) return '—';
        $h = (int) floor($seconds / 3600);
        $m = (int) round(($seconds % 3600) / 60);
        if ($h > 0) return $h . __('daily_report.hours_short') . ' ' . $m . __('daily_report.minutes_short');
        return $m . __('daily_report.minutes_short');
    };

    $atRisk = $this->atRiskTickets;
    $activeTickets = $this->activeTickets;
    $agentPerf = $this->agentPerformance;
    $distCategory = $this->distributionByCategory;
    $distPriority = $this->distributionByPriority;
    $totalCat = collect($distCategory)->sum('count') ?: 1;
    $totalPri = collect($distPriority)->sum('count') ?: 1;
    $maxAgentHandled = collect($agentPerf)->max('handled') ?: 1;

    $backlogTotal = ($summary['backlog_open'] ?? 0) + ($summary['backlog_in_progress'] ?? 0) + ($summary['backlog_pending'] ?? 0);
    $backlogTotal = $backlogTotal ?: 1;

    $dailyReportGroupOptions = [['value' => '', 'label' => __('daily_report.all')]];
    foreach ($groups ?? [] as $g) {
        $dailyReportGroupOptions[] = ['value' => (string) data_get($g, 'id'), 'label' => (string) data_get($g, 'name')];
    }
    $dailyReportAgentOptions = [['value' => '', 'label' => __('daily_report.all')]];
    foreach ($agents ?? [] as $a) {
        $dailyReportAgentOptions[] = ['value' => (string) data_get($a, 'id'), 'label' => (string) data_get($a, 'name')];
    }
    $dailyReportCategoryOptions = [['value' => '', 'label' => __('daily_report.all')]];
    foreach ($categories ?? [] as $c) {
        $dailyReportCategoryOptions[] = ['value' => (string) data_get($c, 'id'), 'label' => (string) data_get($c, 'name')];
    }
    $dailyReportPriorityOptions = [['value' => '', 'label' => __('daily_report.all')]];
    foreach ($priorities ?? [] as $p) {
        $dailyReportPriorityOptions[] = ['value' => (string) data_get($p, 'id'), 'label' => (string) data_get($p, 'name')];
    }
?>

<div class="daily-report w-full max-w-full min-w-0 mx-auto space-y-6 sm:space-y-8">

    
    <div class="page-header !mb-2">
        <div class="min-w-0 flex-1">
            <h1 class="page-title"><?php echo e(__('daily_report.title')); ?></h1>
            <p class="page-subtitle"><?php echo e(__('daily_report.subtitle')); ?></p>
        </div>
        <div class="page-actions reports-page-actions">
            <input
                type="date"
                wire:model.live="date"
                class="h-10 w-full min-w-0 sm:w-auto sm:min-w-[10.5rem] rounded-xl border border-slate-200 bg-white px-3.5 text-sm font-semibold text-slate-700 shadow-sm focus:border-[var(--accent)] focus:ring-2 focus:ring-[var(--accent)]/20 transition-colors"
            />

            <?php if (isset($component)) { $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown','data' => ['align' => 'right','width' => '72','contentClasses' => 'py-3 bg-white rounded-xl shadow-xl border border-slate-200']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['align' => 'right','width' => '72','contentClasses' => 'py-3 bg-white rounded-xl shadow-xl border border-slate-200']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                 <?php $__env->slot('trigger', null, []); ?> 
                    <button type="button" class="relative inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 h-10 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                        <iconify-icon icon="solar:tuning-2-linear" width="17"></iconify-icon>
                        <span class="hidden sm:inline">Filtres</span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterGroup || $filterAgent || $filterCategory || $filterPriority): ?>
                            <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-[var(--accent)] text-[9px] font-black text-white">
                                <?php echo e(collect([$filterGroup, $filterAgent, $filterCategory, $filterPriority])->filter()->count()); ?>

                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </button>
                 <?php $__env->endSlot(); ?>
                 <?php $__env->slot('content', null, []); ?> 
                    <div class="space-y-3.5 px-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1"><?php echo e(__('daily_report.filter_group')); ?></label>
                            <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['options' => $dailyReportGroupOptions,'label' => collect($dailyReportGroupOptions)->firstWhere('value', (string) ($filterGroup ?? ''))['label'] ?? __('daily_report.all'),'selectedValue' => (string) ($filterGroup ?? ''),'wire:model.live' => 'filterGroup']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($dailyReportGroupOptions),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect($dailyReportGroupOptions)->firstWhere('value', (string) ($filterGroup ?? ''))['label'] ?? __('daily_report.all')),'selected-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((string) ($filterGroup ?? '')),'wire:model.live' => 'filterGroup']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

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
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1"><?php echo e(__('daily_report.filter_agent')); ?></label>
                            <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['options' => $dailyReportAgentOptions,'label' => collect($dailyReportAgentOptions)->firstWhere('value', (string) ($filterAgent ?? ''))['label'] ?? __('daily_report.all'),'selectedValue' => (string) ($filterAgent ?? ''),'wire:model.live' => 'filterAgent']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($dailyReportAgentOptions),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect($dailyReportAgentOptions)->firstWhere('value', (string) ($filterAgent ?? ''))['label'] ?? __('daily_report.all')),'selected-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((string) ($filterAgent ?? '')),'wire:model.live' => 'filterAgent']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

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
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1"><?php echo e(__('daily_report.filter_category')); ?></label>
                            <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['options' => $dailyReportCategoryOptions,'label' => collect($dailyReportCategoryOptions)->firstWhere('value', (string) ($filterCategory ?? ''))['label'] ?? __('daily_report.all'),'selectedValue' => (string) ($filterCategory ?? ''),'wire:model.live' => 'filterCategory']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($dailyReportCategoryOptions),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect($dailyReportCategoryOptions)->firstWhere('value', (string) ($filterCategory ?? ''))['label'] ?? __('daily_report.all')),'selected-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((string) ($filterCategory ?? '')),'wire:model.live' => 'filterCategory']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

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
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1"><?php echo e(__('daily_report.filter_priority')); ?></label>
                            <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['options' => $dailyReportPriorityOptions,'label' => collect($dailyReportPriorityOptions)->firstWhere('value', (string) ($filterPriority ?? ''))['label'] ?? __('daily_report.all'),'selectedValue' => (string) ($filterPriority ?? ''),'wire:model.live' => 'filterPriority']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($dailyReportPriorityOptions),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect($dailyReportPriorityOptions)->firstWhere('value', (string) ($filterPriority ?? ''))['label'] ?? __('daily_report.all')),'selected-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((string) ($filterPriority ?? '')),'wire:model.live' => 'filterPriority']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

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
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterGroup || $filterAgent || $filterCategory || $filterPriority): ?>
                            <button type="button" wire:click="$set('filterGroup', ''); $set('filterAgent', ''); $set('filterCategory', ''); $set('filterPriority', '')" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-500 hover:bg-slate-50 transition-colors">
                                <?php echo e(__('daily_report.reset_filters')); ?>

                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
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
                    <button wire:click="exportCsv" type="button" class="block w-full px-4 py-2.5 text-start text-sm text-slate-700 hover:bg-slate-100 transition-colors flex items-center gap-2 rounded-lg mx-1 disabled:opacity-50" wire:loading.attr="disabled" wire:target="exportCsv,exportPdf">
                        <span wire:loading.remove wire:target="exportCsv">
                            <iconify-icon icon="solar:document-text-bold-duotone" width="16"></iconify-icon>
                        </span>
                        <span wire:loading wire:target="exportCsv" class="animate-spin">
                            <iconify-icon icon="solar:refresh-linear" width="16"></iconify-icon>
                        </span>
                        <span wire:loading.remove wire:target="exportCsv"><?php echo e(__('task_report.export_csv')); ?></span>
                        <span wire:loading wire:target="exportCsv" class="text-slate-400">Génération...</span>
                    </button>
                    <button wire:click="exportPdf" type="button" class="block w-full px-4 py-2.5 text-start text-sm text-slate-700 hover:bg-slate-100 transition-colors flex items-center gap-2 rounded-lg mx-1 disabled:opacity-50" wire:loading.attr="disabled" wire:target="exportCsv,exportPdf">
                        <span wire:loading.remove wire:target="exportPdf">
                            <iconify-icon icon="solar:file-bold-duotone" width="16"></iconify-icon>
                        </span>
                        <span wire:loading wire:target="exportPdf" class="animate-spin">
                            <iconify-icon icon="solar:refresh-linear" width="16"></iconify-icon>
                        </span>
                        <span wire:loading.remove wire:target="exportPdf"><?php echo e(__('task_report.export_pdf')); ?></span>
                        <span wire:loading wire:target="exportPdf" class="text-slate-400">Génération...</span>
                    </button>
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

    
    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4 min-[1920px]:gap-6">
        
        <div class="stat-card">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label"><?php echo e(__('daily_report.created_today')); ?></span>
                    <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900 tabular-nums"><?php echo e($summary['created_total']); ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($summary['created_total'] > 0): ?>
                        <p class="text-xs text-slate-400 mt-0.5 tabular-nums">
                            <?php echo e($summary['created_open']); ?> <?php echo e(__('daily_report.open')); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($summary['created_in_progress'] > 0): ?>, <?php echo e($summary['created_in_progress']); ?> <?php echo e(__('daily_report.in_progress')); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="stat-card-icon bg-[var(--accent)]/10 text-[var(--accent)]">
                    <iconify-icon icon="solar:add-circle-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>

        
        <div class="stat-card">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label"><?php echo e(__('daily_report.resolved_today')); ?></span>
                    <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold text-slate-900 tabular-nums"><?php echo e($summary['resolved_today']); ?></div>
                </div>
                <div class="stat-card-icon bg-emerald-50 text-emerald-600">
                    <iconify-icon icon="solar:check-circle-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>

        
        <div class="stat-card">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label"><?php echo e(__('daily_report.avg_first_response')); ?></span>
                    <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold tabular-nums <?php echo e($avgFirstResp !== null ? 'text-slate-900' : 'text-slate-300'); ?>"><?php echo e($fmtDuration($avgFirstResp)); ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($avgFirstResp === null): ?>
                        <p class="text-[10px] text-slate-400 mt-0.5">Aucune réponse ce jour</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="stat-card-icon bg-amber-50 text-amber-600">
                    <iconify-icon icon="solar:clock-circle-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>

        
        <div class="stat-card">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label"><?php echo e(__('daily_report.avg_resolution_time')); ?></span>
                    <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl min-[1920px]:text-4xl font-bold tabular-nums <?php echo e($avgResolution !== null ? 'text-slate-900' : 'text-slate-300'); ?>"><?php echo e($fmtDuration($avgResolution)); ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($avgResolution === null): ?>
                        <p class="text-[10px] text-slate-400 mt-0.5">Aucun ticket résolu ce jour</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="stat-card-icon bg-blue-50 text-blue-600">
                    <iconify-icon icon="solar:stopwatch-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    
    <section class="rounded-xl sm:rounded-2xl border border-slate-100 bg-white p-5 sm:p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <iconify-icon icon="solar:layers-bold-duotone" width="18" class="text-[var(--accent)]"></iconify-icon>
                <?php echo e(__('daily_report.backlog')); ?>

            </h2>
            <span class="text-xs font-semibold text-slate-400 tabular-nums"><?php echo e($summary['backlog_open'] + $summary['backlog_in_progress'] + $summary['backlog_pending']); ?> tickets</span>
        </div>

        
        <div class="w-full h-3 rounded-full bg-slate-100 flex overflow-hidden mb-5">
            <?php
                $bOpen = round(($summary['backlog_open'] / $backlogTotal) * 100, 1);
                $bProg = round(($summary['backlog_in_progress'] / $backlogTotal) * 100, 1);
                $bPend = round(($summary['backlog_pending'] / $backlogTotal) * 100, 1);
            ?>
            <div class="h-full bg-blue-500 transition-all duration-500" style="width:<?php echo e($bOpen); ?>%"></div>
            <div class="h-full bg-amber-400 transition-all duration-500" style="width:<?php echo e($bProg); ?>%"></div>
            <div class="h-full bg-slate-300 transition-all duration-500" style="width:<?php echo e($bPend); ?>%"></div>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-4">
            <div class="flex items-center gap-3">
                <span class="h-3 w-3 rounded-full bg-blue-500 shrink-0"></span>
                <div>
                    <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider"><?php echo e(__('daily_report.open')); ?></p>
                    <p class="text-lg font-extrabold text-slate-900 tabular-nums leading-tight"><?php echo e($summary['backlog_open']); ?></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="h-3 w-3 rounded-full bg-amber-400 shrink-0"></span>
                <div>
                    <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider"><?php echo e(__('daily_report.in_progress')); ?></p>
                    <p class="text-lg font-extrabold text-slate-900 tabular-nums leading-tight"><?php echo e($summary['backlog_in_progress']); ?></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="h-3 w-3 rounded-full bg-slate-300 shrink-0"></span>
                <div>
                    <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider"><?php echo e(__('daily_report.pending')); ?></p>
                    <p class="text-lg font-extrabold text-slate-900 tabular-nums leading-tight"><?php echo e($summary['backlog_pending']); ?></p>
                </div>
            </div>
        </div>
    </section>

    
    <section>
        <div class="flex items-center gap-2 mb-4">
            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-50 text-red-500">
                <iconify-icon icon="solar:danger-triangle-bold-duotone" width="16"></iconify-icon>
            </div>
            <h2 class="text-sm font-bold text-slate-800"><?php echo e(__('daily_report.at_risk')); ?></h2>
            <?php $riskCount = $atRisk['overdue']->count() + $atRisk['due_soon']->count(); ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($riskCount > 0): ?>
                <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-100 px-1.5 text-[10px] font-bold text-red-700"><?php echo e($riskCount); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="content-card">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($atRisk['overdue']->isEmpty() && $atRisk['due_soon']->isEmpty()): ?>
                <div class="px-6 py-14 text-center">
                    <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-400 mb-3">
                        <iconify-icon icon="solar:shield-check-bold-duotone" width="28"></iconify-icon>
                    </div>
                    <p class="text-sm font-medium text-slate-500"><?php echo e(__('daily_report.at_risk_empty')); ?></p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[700px]">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/60">
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?php echo e(__('daily_report.status')); ?></th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?php echo e(__('daily_report.reference')); ?></th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?php echo e(__('daily_report.subject')); ?></th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?php echo e(__('daily_report.group')); ?></th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?php echo e(__('daily_report.priority')); ?></th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?php echo e(__('daily_report.assignees')); ?></th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?php echo e(__('daily_report.due_date')); ?></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $atRisk['overdue']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <tr class="hover:bg-red-50/40 transition-colors border-l-[3px] border-l-red-400">
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-1.5 rounded-md bg-red-50 px-2 py-1 text-[11px] font-bold text-red-600">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                            <?php echo e(__('daily_report.overdue')); ?>

                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs font-mono font-semibold text-slate-500"><?php echo e($ticket->public_id); ?></td>
                                    <td class="px-4 py-3 text-sm font-medium text-slate-900 max-w-[200px]">
                                        <a href="<?php echo e(route('tickets.discussion', $ticket)); ?>" wire:navigate class="hover:text-[var(--accent)] transition-colors truncate block"><?php echo e($ticket->subject); ?></a>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-500"><?php echo e($ticket->group?->name ?? __('daily_report.no_group_label')); ?></td>
                                    <td class="px-4 py-3">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->priority): ?>
                                            <span class="text-xs font-semibold text-slate-600"><?php echo e($ticket->priority->name); ?></span>
                                        <?php else: ?>
                                            <span class="text-xs text-slate-300"><?php echo e(__('daily_report.no_priority')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-500"><?php echo e($ticket->assignees->isNotEmpty() ? $ticket->assignees->pluck('name')->join(', ') : __('daily_report.no_assignee')); ?></td>
                                    <td class="px-4 py-3 text-xs font-bold text-red-600 tabular-nums"><?php echo e($ticket->due_date?->format('d/m/Y')); ?></td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $atRisk['due_soon']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <tr class="hover:bg-amber-50/40 transition-colors border-l-[3px] border-l-amber-400">
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-1.5 rounded-md bg-amber-50 px-2 py-1 text-[11px] font-bold text-amber-600">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                            <?php echo e(__('daily_report.due_soon')); ?>

                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs font-mono font-semibold text-slate-500"><?php echo e($ticket->public_id); ?></td>
                                    <td class="px-4 py-3 text-sm font-medium text-slate-900 max-w-[200px]">
                                        <a href="<?php echo e(route('tickets.discussion', $ticket)); ?>" wire:navigate class="hover:text-[var(--accent)] transition-colors truncate block"><?php echo e($ticket->subject); ?></a>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-500"><?php echo e($ticket->group?->name ?? __('daily_report.no_group_label')); ?></td>
                                    <td class="px-4 py-3">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->priority): ?>
                                            <span class="text-xs font-semibold text-slate-600"><?php echo e($ticket->priority->name); ?></span>
                                        <?php else: ?>
                                            <span class="text-xs text-slate-300"><?php echo e(__('daily_report.no_priority')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-500"><?php echo e($ticket->assignees->isNotEmpty() ? $ticket->assignees->pluck('name')->join(', ') : __('daily_report.no_assignee')); ?></td>
                                    <td class="px-4 py-3 text-xs font-bold text-amber-600 tabular-nums"><?php echo e($ticket->due_date?->format('d/m/Y')); ?></td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>

    
    <section>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
            <div class="flex items-center gap-2">
                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-[var(--accent)]/10 text-[var(--accent)]">
                    <iconify-icon icon="solar:ticket-bold-duotone" width="16"></iconify-icon>
                </div>
                <h2 class="text-sm font-bold text-slate-800"><?php echo e(__('daily_report.active_tickets')); ?></h2>
                <span class="text-xs font-semibold text-slate-400 tabular-nums">(<?php echo e($activeTickets->count()); ?>)</span>
            </div>
            <div class="inline-flex w-full max-w-full min-w-0 items-stretch overflow-x-auto rounded-xl border border-slate-200 bg-white p-0.5 shadow-sm [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden sm:w-auto sm:inline-flex">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                    ['key' => 'in_progress', 'label' => __('daily_report.tab_in_progress')],
                    ['key' => 'pending', 'label' => __('daily_report.tab_pending')],
                    ['key' => 'all_active', 'label' => __('daily_report.tab_all_active')],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <button wire:click="$set('activeTab', '<?php echo e($tab['key']); ?>')" type="button" class="flex-1 min-w-0 shrink-0 px-2.5 sm:px-3 py-1.5 text-[11px] sm:text-xs font-semibold rounded-[10px] transition-all whitespace-nowrap <?php echo e($activeTab === $tab['key'] ? 'bg-slate-900 text-white shadow' : 'text-slate-500 hover:text-slate-700'); ?>">
                        <?php echo e($tab['label']); ?>

                    </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>

        <div class="content-card">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTickets->isEmpty()): ?>
                <div class="px-6 py-14 text-center">
                    <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-3">
                        <iconify-icon icon="solar:inbox-line-bold-duotone" width="28"></iconify-icon>
                    </div>
                    <p class="text-sm font-medium text-slate-500"><?php echo e(__('daily_report.active_empty')); ?></p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[750px]">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/60">
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?php echo e(__('daily_report.reference')); ?></th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?php echo e(__('daily_report.subject')); ?></th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?php echo e(__('daily_report.group')); ?></th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?php echo e(__('daily_report.priority')); ?></th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?php echo e(__('daily_report.assignees')); ?></th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?php echo e(__('daily_report.updated_at')); ?></th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?php echo e(__('daily_report.due_date')); ?></th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider"><?php echo e(__('daily_report.status')); ?></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $activeTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <?php
                                    $sc = match($ticket->status) {
                                        \App\Enums\TicketStatus::Open => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'dot' => 'bg-blue-500'],
                                        \App\Enums\TicketStatus::InProgress => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'dot' => 'bg-amber-500'],
                                        \App\Enums\TicketStatus::Pending => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'dot' => 'bg-slate-400'],
                                        default => ['bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'dot' => 'bg-slate-400'],
                                    };
                                    $sl = match($ticket->status) {
                                        \App\Enums\TicketStatus::Open => __('daily_report.open'),
                                        \App\Enums\TicketStatus::InProgress => __('daily_report.in_progress'),
                                        \App\Enums\TicketStatus::Pending => __('daily_report.pending'),
                                        default => $ticket->status->value,
                                    };
                                ?>
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="px-4 py-3 text-xs font-mono font-semibold text-slate-500"><?php echo e($ticket->public_id); ?></td>
                                    <td class="px-4 py-3 text-sm font-medium text-slate-900 max-w-[200px]">
                                        <a href="<?php echo e(route('tickets.discussion', $ticket)); ?>" wire:navigate class="hover:text-[var(--accent)] transition-colors truncate block"><?php echo e($ticket->subject); ?></a>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-500"><?php echo e($ticket->group?->name ?? __('daily_report.no_group_label')); ?></td>
                                    <td class="px-4 py-3">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->priority): ?>
                                            <span class="text-xs font-semibold text-slate-600"><?php echo e($ticket->priority->name); ?></span>
                                        <?php else: ?>
                                            <span class="text-xs text-slate-300"><?php echo e(__('daily_report.no_priority')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-500"><?php echo e($ticket->assignees->isNotEmpty() ? $ticket->assignees->pluck('name')->join(', ') : __('daily_report.no_assignee')); ?></td>
                                    <td class="px-4 py-3 text-xs text-slate-400 tabular-nums"><?php echo e($ticket->updated_at?->format('d/m/Y H:i')); ?></td>
                                    <td class="px-4 py-3 text-xs text-slate-400"><?php echo e($ticket->due_date?->format('d/m/Y') ?? '—'); ?></td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-1 rounded-md <?php echo e($sc['bg']); ?> px-2 py-1 text-[11px] font-bold <?php echo e($sc['text']); ?>">
                                            <span class="h-1.5 w-1.5 rounded-full <?php echo e($sc['dot']); ?>"></span>
                                            <?php echo e($sl); ?>

                                        </span>
                                    </td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>

    
    <section class="grid grid-cols-1 lg:grid-cols-5 gap-4 sm:gap-6">

        
        <div class="lg:col-span-3 content-card">
            <div class="px-4 sm:px-6 py-4 border-b border-slate-50 bg-slate-50/50 flex items-center gap-2">
                <iconify-icon icon="solar:users-group-two-rounded-bold-duotone" width="18" class="text-[var(--accent)]"></iconify-icon>
                <h3 class="text-base font-bold text-slate-900"><?php echo e(__('daily_report.agent_performance')); ?></h3>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($agentPerf)): ?>
                <div class="px-6 py-14 text-center">
                    <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-3">
                        <iconify-icon icon="solar:user-id-bold-duotone" width="28"></iconify-icon>
                    </div>
                    <p class="text-sm font-medium text-slate-500"><?php echo e(__('daily_report.no_agent_data')); ?></p>
                </div>
            <?php else: ?>
                <div class="p-4 sm:p-6 space-y-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $agentPerf; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php $pctBar = round(($agent['handled'] / $maxAgentHandled) * 100); ?>
                        <div class="flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode($agent['name'])); ?>&background=random&color=fff&size=36" class="h-9 w-9 rounded-xl shrink-0 ring-1 ring-slate-200/60" alt="">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-sm font-semibold text-slate-800 truncate"><?php echo e($agent['name']); ?></span>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="text-xs font-bold text-slate-600 tabular-nums"><?php echo e($agent['handled']); ?></span>
                                        <span class="inline-flex items-center rounded-md bg-emerald-50 px-1.5 py-0.5 text-[10px] font-bold text-emerald-700 tabular-nums"><?php echo e($agent['resolved']); ?> <?php echo e(__('daily_report.resolved')); ?></span>
                                    </div>
                                </div>
                                <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full bg-[var(--accent)] transition-all duration-500" style="width:<?php echo e($pctBar); ?>%"></div>
                                </div>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="lg:col-span-2 content-card">
            <div class="px-4 sm:px-6 py-4 border-b border-slate-50 bg-slate-50/50 flex items-center gap-2">
                <iconify-icon icon="solar:pie-chart-2-bold-duotone" width="18" class="text-[var(--accent)]"></iconify-icon>
                <h3 class="text-base font-bold text-slate-900"><?php echo e(__('daily_report.distribution')); ?></h3>
            </div>
            <div class="p-4 sm:p-6 space-y-6">
                
                <div>
                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3"><?php echo e(__('daily_report.by_category')); ?></h4>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $distCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php $pct = $totalCat > 0 ? round(($cat['count'] / $totalCat) * 100) : 0; ?>
                        <div class="flex items-center gap-3 mb-2.5 last:mb-0">
                            <span class="text-xs font-medium text-slate-600 w-24 truncate shrink-0"><?php echo e($cat['name']); ?></span>
                            <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-slate-500 to-slate-700 transition-all duration-500" style="width:<?php echo e($pct); ?>%"></div>
                            </div>
                            <span class="text-[11px] font-bold text-slate-700 tabular-nums w-8 text-right shrink-0"><?php echo e($cat['count']); ?></span>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <p class="text-center text-xs text-slate-400 py-3">Aucune donnée</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="border-t border-slate-100"></div>

                
                <div>
                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3"><?php echo e(__('daily_report.by_priority')); ?></h4>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $distPriority; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php $pct = $totalPri > 0 ? round(($pri['count'] / $totalPri) * 100) : 0; ?>
                        <div class="flex items-center gap-3 mb-2.5 last:mb-0">
                            <span class="text-xs font-medium text-slate-600 w-24 truncate shrink-0 flex items-center gap-1.5">
                                <span class="h-2 w-2 rounded-full shrink-0" style="background-color:<?php echo e($pri['color']); ?>"></span>
                                <?php echo e($pri['name']); ?>

                            </span>
                            <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500" style="width:<?php echo e($pct); ?>%;background-color:<?php echo e($pri['color']); ?>"></div>
                            </div>
                            <span class="text-[11px] font-bold text-slate-700 tabular-nums w-8 text-right shrink-0"><?php echo e($pri['count']); ?></span>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <p class="text-center text-xs text-slate-400 py-3">Aucune donnée</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </section>


</div><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\reports\daily-report.blade.php ENDPATH**/ ?>