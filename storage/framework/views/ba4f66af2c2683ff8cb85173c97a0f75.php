<?php
    $auditActionOptions = [['value' => '', 'label' => __('super_admin.audit.all_actions')]];
    foreach ($actions as $action) {
        $actionKey = 'super_admin.audit.action_' . str_replace('.', '_', $action);
        $actionLabel = __($actionKey) !== $actionKey ? __($actionKey) : $action;
        $auditActionOptions[] = ['value' => $action, 'label' => $actionLabel];
    }
    $auditTargetTypeOptions = [['value' => '', 'label' => __('super_admin.audit.all_targets')]];
    foreach ($targetTypes as $type) {
        $auditTargetTypeOptions[] = ['value' => $type, 'label' => class_basename($type)];
    }
?>
<div class="space-y-6 pb-12">
    
    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900"><?php echo e(__('super_admin.audit.title')); ?></h1>
            <p class="mt-1 text-sm text-slate-500"><?php echo e(__('super_admin.audit.subtitle')); ?></p>
        </div>
        <a href="<?php echo e(route('platform-admin.audit-log.export', array_filter(['actionFilter' => $actionFilter, 'targetTypeFilter' => $targetTypeFilter, 'dateFrom' => $dateFrom, 'dateTo' => $dateTo]))); ?>" class="sa-btn-secondary self-start">
            <iconify-icon icon="solar:download-minimalistic-bold" width="16"></iconify-icon>
            <?php echo e(__('super_admin.audit.export_csv')); ?>

        </a>
    </header>

    
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end flex-wrap">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1.5"><?php echo e(__('super_admin.audit.col_action')); ?></label>
                <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['wire:model.live' => 'actionFilter']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'actionFilter']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <option value=""><?php echo e(__('super_admin.audit.all_actions')); ?></option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php
                            $actionKey = 'super_admin.audit.action_' . str_replace('.', '_', $action);
                            $actionLabel = __($actionKey) !== $actionKey ? __($actionKey) : $action;
                        ?>
                        <option value="<?php echo e($action); ?>"><?php echo e($actionLabel); ?></option>
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
            </div>
            <div class="min-w-[160px]">
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1.5"><?php echo e(__('super_admin.audit.col_target')); ?></label>
                <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['options' => $auditTargetTypeOptions,'label' => collect($auditTargetTypeOptions)->firstWhere('value', (string) ($targetTypeFilter ?? ''))['label'] ?? __('super_admin.audit.all_targets'),'selectedValue' => $targetTypeFilter ?? '','wire:model.live' => 'targetTypeFilter']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($auditTargetTypeOptions),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect($auditTargetTypeOptions)->firstWhere('value', (string) ($targetTypeFilter ?? ''))['label'] ?? __('super_admin.audit.all_targets')),'selected-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($targetTypeFilter ?? ''),'wire:model.live' => 'targetTypeFilter']); ?>
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
            <div class="min-w-[150px]">
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1.5"><?php echo e(__('super_admin.audit.date_from')); ?></label>
                <input type="date" wire:model.live="dateFrom" class="w-full rounded-xl border border-slate-200 bg-white py-2 px-3 text-sm text-slate-700 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all" />
            </div>
            <div class="min-w-[150px]">
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1.5"><?php echo e(__('super_admin.audit.date_to')); ?></label>
                <input type="date" wire:model.live="dateTo" class="w-full rounded-xl border border-slate-200 bg-white py-2 px-3 text-sm text-slate-700 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all" />
            </div>
        </div>
    </div>

    
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden" x-data="{ expanded: null }">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80">
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500 w-[140px]"><?php echo e(__('super_admin.audit.col_date')); ?></th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500"><?php echo e(__('super_admin.audit.col_admin')); ?></th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500"><?php echo e(__('super_admin.audit.col_action')); ?></th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500"><?php echo e(__('super_admin.audit.col_target')); ?></th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500 w-[120px]"><?php echo e(__('super_admin.audit.col_ip')); ?></th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500 w-[40px]"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php
                            $actionRaw = $log->action;
                            $actionKey = 'super_admin.audit.action_' . str_replace('.', '_', $actionRaw);
                            $actionLabel = __($actionKey) !== $actionKey ? __($actionKey) : $actionRaw;

                            $actionColor = match(true) {
                                str_contains($actionRaw, 'activate') || str_contains($actionRaw, 'create')  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                str_contains($actionRaw, 'suspend')                                          => 'bg-amber-50 text-amber-700 border-amber-200',
                                str_contains($actionRaw, 'disable')                                          => 'bg-red-50 text-red-700 border-red-200',
                                str_contains($actionRaw, 'security') || str_contains($actionRaw, 'role')     => 'bg-violet-50 text-violet-700 border-violet-200',
                                str_contains($actionRaw, 'invitation')                                       => 'bg-[#F2E3BB]/40 text-[#005F02] border-[#005F02]/15',
                                str_contains($actionRaw, 'backup')                                           => 'bg-sky-50 text-sky-700 border-sky-200',
                                str_contains($actionRaw, 'support')                                          => 'bg-purple-50 text-purple-700 border-purple-200',
                                str_contains($actionRaw, 'enter')                                            => 'bg-blue-50 text-blue-700 border-blue-200',
                                str_contains($actionRaw, 'archive')                                          => 'bg-slate-100 text-slate-600 border-slate-200',
                                str_contains($actionRaw, 'verify')                                           => 'bg-teal-50 text-teal-700 border-teal-200',
                                str_contains($actionRaw, 'job') || str_contains($actionRaw, 'retry') || str_contains($actionRaw, 'delete') => 'bg-orange-50 text-orange-700 border-orange-200',
                                default                                                                      => 'bg-slate-50 text-slate-600 border-slate-200',
                            };

                            $targetLabel = null;
                            if ($log->metadata) {
                                $targetLabel = $log->metadata['name'] ?? $log->metadata['org_name'] ?? $log->metadata['email'] ?? null;
                            }

                            $hasDetails = $log->metadata && count($log->metadata) > 0;
                        ?>

                        
                        <tr
                            class="border-b border-slate-100 transition-colors cursor-pointer"
                            :class="expanded === <?php echo e($log->id); ?> ? 'bg-slate-50' : 'hover:bg-slate-50/50'"
                            @click="<?php echo e($hasDetails ? "expanded = expanded === {$log->id} ? null : {$log->id}" : ''); ?>"
                        >
                            <td class="px-5 py-3">
                                <div class="text-xs font-medium text-slate-800 whitespace-nowrap"><?php echo e($log->created_at->format('d/m/Y')); ?></div>
                                <div class="text-[11px] text-slate-400 mt-0.5"><?php echo e($log->created_at->format('H:i:s')); ?></div>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 text-[10px] font-bold text-slate-600">
                                        <?php echo e($log->user ? mb_strtoupper(mb_substr($log->user->name, 0, 1)) : '?'); ?>

                                    </div>
                                    <span class="text-sm font-medium text-slate-700 truncate"><?php echo e($log->user?->name ?? '—'); ?></span>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center rounded-md border px-2 py-0.5 text-[11px] font-semibold <?php echo e($actionColor); ?>">
                                    <?php echo e($actionLabel); ?>

                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log->target_type): ?>
                                    <span class="text-xs text-slate-500">
                                        <span class="font-medium text-slate-600"><?php echo e(class_basename($log->target_type)); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($targetLabel): ?>
                                            <span class="text-slate-400 mx-1">&middot;</span>
                                            <span class="text-slate-500"><?php echo e(Str::limit($targetLabel, 30)); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-slate-300">&mdash;</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-xs font-mono text-slate-400"><?php echo e($log->ip_address); ?></span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasDetails): ?>
                                    <div
                                        class="inline-flex items-center justify-center h-6 w-6 rounded-md transition-colors"
                                        :class="expanded === <?php echo e($log->id); ?> ? 'bg-[#005F02]/10 text-[#005F02]' : 'text-slate-300 group-hover:text-slate-500'"
                                    >
                                        <iconify-icon
                                            :icon="expanded === <?php echo e($log->id); ?> ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear'"
                                            width="14"
                                        ></iconify-icon>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                        </tr>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasDetails): ?>
                            <tr x-show="expanded === <?php echo e($log->id); ?>" x-cloak>
                                <td colspan="6" class="px-0 py-0">
                                    <div
                                        x-show="expanded === <?php echo e($log->id); ?>"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0"
                                        x-transition:enter-end="opacity-100"
                                        x-transition:leave="transition ease-in duration-100"
                                        x-transition:leave-start="opacity-100"
                                        x-transition:leave-end="opacity-0"
                                        class="border-b border-slate-100 bg-slate-50/50 px-5 py-4"
                                    >
                                        <div class="ml-[0px] sm:ml-[56px]">
                                            <div class="flex items-center gap-2 mb-3">
                                                <iconify-icon icon="solar:info-circle-bold-duotone" width="16" class="text-[#005F02]"></iconify-icon>
                                                <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider"><?php echo e(__('super_admin.audit.col_details')); ?></span>
                                            </div>
                                            <div class="rounded-xl bg-white border border-slate-200 divide-y divide-slate-100 overflow-hidden">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $log->metadata; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                    <div class="flex items-baseline gap-4 px-4 py-2.5 text-xs">
                                                        <span class="shrink-0 w-[120px] font-mono font-medium text-slate-400"><?php echo e($key); ?></span>
                                                        <span class="text-slate-700 break-all"><?php echo e(is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value); ?></span>
                                                    </div>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            </div>
                                            <div class="mt-2.5 text-[11px] text-slate-400">
                                                <?php echo e($log->created_at->diffForHumans()); ?>

                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <iconify-icon icon="solar:document-text-bold-duotone" width="44" class="mb-3 text-slate-300"></iconify-icon>
                                    <p class="text-sm font-medium"><?php echo e(__('super_admin.audit.empty')); ?></p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($logs->hasPages()): ?>
            <div class="border-t border-slate-100 px-5 py-3">
                <?php echo e($logs->links()); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\super-admin\audit-log.blade.php ENDPATH**/ ?>