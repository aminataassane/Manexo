<?php
    $statusLabel = function (string $status): string {
        return __('tickets.status.' . $status);
    };
    $statusOrder = ['open', 'in_progress', 'pending', 'resolved', 'closed'];
    $statusPill = function (string $status): array {
        return match ($status) {
            'open' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-100'],
            'in_progress' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-100'],
            'pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-100'],
            'resolved' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-100'],
            'closed' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'border' => 'border-slate-200'],
            'soumis' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'border' => 'border-violet-100'],
            default => ['bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'border' => 'border-slate-200'],
        };
    };
?>

<div class="space-y-6 sm:space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight"><?php echo e(__('pages.history.heading')); ?></h1>
            <p class="text-sm text-slate-500 mt-1"><?php echo e(__('pages.history.subheading')); ?></p>
        </div>
        <a href="<?php echo e(route('profile')); ?>"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
            <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
            <?php echo e(__('pages.history.back_to_profile')); ?>

        </a>
    </div>

    <!-- Stats cards -->
    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-5 lg:gap-6">
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-slate-500 truncate"><?php echo e(__('pages.history.stat_total_created')); ?></p>
                    <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-slate-900"><?php echo e($stats['total_created']); ?></p>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 group-hover:scale-105 transition-transform">
                    <iconify-icon icon="solar:document-add-bold-duotone" width="22"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-slate-500 truncate"><?php echo e(__('pages.history.stat_total_assigned')); ?></p>
                    <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-slate-900"><?php echo e($stats['total_assigned']); ?></p>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[var(--accent-soft)] text-[var(--accent)] group-hover:scale-105 transition-transform">
                    <iconify-icon icon="solar:user-check-bold-duotone" width="22"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-slate-500 truncate"><?php echo e(__('pages.history.stat_total_forms')); ?></p>
                    <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-slate-900"><?php echo e($stats['total_forms']); ?></p>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-600 group-hover:scale-105 transition-transform">
                    <iconify-icon icon="solar:clipboard-text-bold-duotone" width="22"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-slate-500 truncate"><?php echo e(__('pages.history.stat_this_week')); ?></p>
                    <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-slate-900"><?php echo e($stats['this_week']); ?></p>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 group-hover:scale-105 transition-transform">
                    <iconify-icon icon="solar:calendar-bold-duotone" width="22"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl sm:rounded-2xl bg-white p-4 sm:p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-300 min-w-0">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-slate-500 truncate"><?php echo e(__('pages.history.stat_this_month')); ?></p>
                    <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-slate-900"><?php echo e($stats['this_month']); ?></p>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 group-hover:scale-105 transition-transform">
                    <iconify-icon icon="solar:calendar-minimalistic-bold-duotone" width="22"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <!-- Main: filters + timeline -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-base font-semibold text-slate-900"><?php echo e(__('pages.profile.recent_activity')); ?></h2>
                    <p class="text-xs text-slate-500 mt-0.5"><?php echo e(__('pages.profile.recent_activity_subtitle')); ?></p>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="flex flex-wrap gap-2 mb-6">
                        <?php
                            $filters = [
                                ['all', __('pages.history.filter_all')],
                                ['tickets', __('pages.history.filter_tickets_created')],
                                ['assignations', __('pages.history.filter_assigned_to_me')],
                                ['forms', __('pages.history.filter_forms')],
                            ];
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $filters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$key, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <?php $isActive = $type === $key; ?>
                            <button
                                type="button"
                                wire:click="setType('<?php echo e($key); ?>')"
                                wire:loading.attr="disabled"
                                wire:target="setType"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all <?php echo e($isActive ? 'bg-slate-900 text-white shadow-md' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:text-slate-900'); ?>"
                            ><?php echo e($label); ?></button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>

                    <div class="relative pl-4 space-y-6 before:absolute before:left-[19px] before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-100">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_4 = true; $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_4 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <?php
                                $eventType = $e->event_type ?? 'tickets';
                                $statusValue = (string) ($e->status ?? '');
                                $pill = $statusPill($statusValue);
                                $label = $eventType === 'tickets' ? __('pages.history.label_ticket_created') : ($eventType === 'assignations' ? __('pages.history.label_ticket_assigned') : __('pages.history.label_form_submitted'));
                                $href = $e->ticket_id ? route('tickets.discussion', $e->ticket_id) : route('forms.index');
                            ?>
                            <a href="<?php echo e($href); ?>" wire:navigate.hover class="relative pl-6 block group">
                                <div class="absolute left-0 top-1.5 h-2.5 w-2.5 rounded-full border-2 border-white shadow-sm transition-transform group-hover:scale-110" style="background-color: var(--accent);"></div>
                                <p class="text-sm font-medium text-slate-900 group-hover:text-[var(--accent)] transition-colors">
                                    <?php echo e($label); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($e->ticket_id): ?>
                                        <span class="text-slate-500 font-normal">· #<?php echo e($e->ticket_id); ?></span>
                                    <?php elseif($e->form_response_id): ?>
                                        <span class="text-slate-500 font-normal">· #<?php echo e($e->form_response_id); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </p>
                                <p class="text-sm text-slate-600 mt-0.5 truncate" title="<?php echo e($e->subject); ?>"><?php echo e($e->subject); ?></p>
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border <?php echo e($pill['bg']); ?> <?php echo e($pill['text']); ?> <?php echo e($pill['border']); ?>">
                                        <?php echo e($statusValue === 'soumis' ? __('pages.forms.status_submitted') : $statusLabel($statusValue)); ?>

                                    </span>
                                    <span class="text-xs text-slate-400"><?php echo e(\Illuminate\Support\Carbon::parse($e->at)->diffForHumans()); ?></span>
                                </div>
                            </a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_4): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center text-sm text-slate-500">
                                <iconify-icon icon="solar:history-linear" width="32" class="mx-auto mb-2 opacity-50"></iconify-icon>
                                <?php echo e(__('pages.history.no_events')); ?>

                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($events->hasPages()): ?>
                        <div class="mt-6 pt-4 border-t border-slate-100">
                            <?php echo e($events->links()); ?>

                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar: by status -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-base font-semibold text-slate-900"><?php echo e(__('pages.history.stat_by_status')); ?></h2>
                    <p class="text-xs text-slate-500 mt-0.5"><?php echo e(__('pages.history.stat_total_created')); ?> + <?php echo e(__('pages.history.stat_total_assigned')); ?> + <?php echo e(__('pages.history.stat_total_forms')); ?></p>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="space-y-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $statusOrder; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($stats['by_status'][$s])): ?>
                                <?php $pill = $statusPill($s); ?>
                                <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-100 px-3 py-2.5 hover:bg-slate-50/50 transition-colors">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-xs font-medium <?php echo e($pill['bg']); ?> <?php echo e($pill['text']); ?> <?php echo e($pill['border']); ?> border">
                                        <?php echo e($statusLabel($s)); ?>

                                    </span>
                                    <span class="text-sm font-bold text-slate-900"><?php echo e($stats['by_status'][$s]); ?></span>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <?php $otherStatuses = array_diff_key($stats['by_status'], array_flip($statusOrder)); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $otherStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s => $cnt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <?php $pill = $statusPill($s); ?>
                            <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-100 px-3 py-2.5 hover:bg-slate-50/50 transition-colors">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-xs font-medium <?php echo e($pill['bg']); ?> <?php echo e($pill['text']); ?> <?php echo e($pill['border']); ?> border">
                                    <?php echo e($statusLabel($s)); ?>

                                </span>
                                <span class="text-sm font-bold text-slate-900"><?php echo e($cnt); ?></span>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($stats['by_status'])): ?>
                            <p class="text-sm text-slate-500 py-4 text-center"><?php echo e(__('pages.history.no_events')); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\profile\history.blade.php ENDPATH**/ ?>