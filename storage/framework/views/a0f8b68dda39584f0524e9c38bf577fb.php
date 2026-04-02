<div class="space-y-6 pb-12">
    
    <header>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900"><?php echo e(__('super_admin.notifications.title')); ?></h1>
        <p class="mt-1 text-sm text-slate-500"><?php echo e(__('super_admin.notifications.subtitle')); ?></p>
    </header>

    
    <section class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F2E3BB]/30 text-[#005F02]">
                    <iconify-icon icon="solar:bell-bold-duotone" width="20"></iconify-icon>
                </div>
                <div>
                    <p class="text-[11px] font-medium text-slate-500"><?php echo e(__('super_admin.notifications.total')); ?></p>
                    <p class="text-2xl font-bold text-slate-900 tabular-nums"><?php echo e($this->stats['total']); ?></p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <iconify-icon icon="solar:check-read-bold-duotone" width="20"></iconify-icon>
                </div>
                <div>
                    <p class="text-[11px] font-medium text-slate-500"><?php echo e(__('super_admin.notifications.read')); ?></p>
                    <p class="text-2xl font-bold text-emerald-600 tabular-nums"><?php echo e($this->stats['read']); ?></p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <iconify-icon icon="solar:bell-bing-bold-duotone" width="20"></iconify-icon>
                </div>
                <div>
                    <p class="text-[11px] font-medium text-slate-500"><?php echo e(__('super_admin.notifications.unread')); ?></p>
                    <p class="text-2xl font-bold text-amber-600 tabular-nums"><?php echo e($this->stats['unread']); ?></p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <iconify-icon icon="solar:calendar-bold-duotone" width="20"></iconify-icon>
                </div>
                <div>
                    <p class="text-[11px] font-medium text-slate-500"><?php echo e(__('super_admin.notifications.last_24h')); ?></p>
                    <p class="text-2xl font-bold text-blue-600 tabular-nums"><?php echo e($this->stats['last_24h']); ?></p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                    <iconify-icon icon="solar:calendar-mark-bold-duotone" width="20"></iconify-icon>
                </div>
                <div>
                    <p class="text-[11px] font-medium text-slate-500"><?php echo e(__('super_admin.notifications.last_7d')); ?></p>
                    <p class="text-2xl font-bold text-violet-600 tabular-nums"><?php echo e($this->stats['last_7d']); ?></p>
                </div>
            </div>
        </div>
    </section>

    
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden" x-data="{ expanded: null }">
        
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-sm font-semibold text-slate-800"><?php echo e(__('super_admin.notifications.recent')); ?></h3>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($this->byType) > 0): ?>
                <div class="flex flex-wrap gap-1.5">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->byType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php
                            $typeName = class_basename($item->type);
                            $typeColor = match(true) {
                                str_contains($typeName, 'Ticket')     => 'bg-violet-50 text-violet-700 border-violet-200',
                                str_contains($typeName, 'Form')       => 'bg-blue-50 text-blue-700 border-blue-200',
                                str_contains($typeName, 'Discussion') => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                str_contains($typeName, 'Checklist')  => 'bg-amber-50 text-amber-700 border-amber-200',
                                str_contains($typeName, 'Task')       => 'bg-rose-50 text-rose-700 border-rose-200',
                                str_contains($typeName, 'Mention')    => 'bg-teal-50 text-teal-700 border-teal-200',
                                default                               => 'bg-slate-50 text-slate-600 border-slate-200',
                            };
                        ?>
                        <span class="inline-flex items-center gap-1 rounded-md border px-2 py-0.5 text-[10px] font-semibold <?php echo e($typeColor); ?>">
                            <?php echo e($typeName); ?>

                            <span class="opacity-60"><?php echo e($item->count); ?></span>
                        </span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/40">
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500 w-[140px]"><?php echo e(__('super_admin.notifications.col_date')); ?></th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500"><?php echo e(__('super_admin.notifications.col_type')); ?></th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500"><?php echo e(__('super_admin.notifications.col_user')); ?></th>
                        <th class="px-5 py-3 w-[40px]"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_5 = true; $__currentLoopData = $this->recentNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_5 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php
                            $notifId = $notif->id;
                            $typeName = class_basename($notif->type);
                            $typeColor = match(true) {
                                str_contains($typeName, 'Ticket')     => 'bg-violet-50 text-violet-700 border-violet-200',
                                str_contains($typeName, 'Form')       => 'bg-blue-50 text-blue-700 border-blue-200',
                                str_contains($typeName, 'Discussion') => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                str_contains($typeName, 'Checklist')  => 'bg-amber-50 text-amber-700 border-amber-200',
                                str_contains($typeName, 'Task')       => 'bg-rose-50 text-rose-700 border-rose-200',
                                str_contains($typeName, 'Mention')    => 'bg-teal-50 text-teal-700 border-teal-200',
                                default                               => 'bg-slate-50 text-slate-600 border-slate-200',
                            };
                            $isRead = $notif->read_at !== null;
                            $hasData = is_array($notif->data) && count($notif->data) > 0;
                        ?>

                        <tr
                            class="border-b border-slate-100 transition-colors <?php echo e($hasData ? 'cursor-pointer' : ''); ?>"
                            :class="expanded === '<?php echo e($notifId); ?>' ? 'bg-slate-50' : 'hover:bg-slate-50/50'"
                            <?php if($hasData): ?> @click="expanded = expanded === '<?php echo e($notifId); ?>' ? null : '<?php echo e($notifId); ?>'" <?php endif; ?>
                        >
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isRead): ?>
                                        <span class="h-2 w-2 shrink-0 rounded-full bg-amber-500"></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <div>
                                        <div class="text-xs font-medium text-slate-800 whitespace-nowrap"><?php echo e(\Carbon\Carbon::parse($notif->created_at)->format('d/m/Y')); ?></div>
                                        <div class="text-[11px] text-slate-400 mt-0.5"><?php echo e(\Carbon\Carbon::parse($notif->created_at)->format('H:i')); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center rounded-md border px-2 py-0.5 text-[11px] font-semibold <?php echo e($typeColor); ?>"><?php echo e($typeName); ?></span>
                            </td>
                            <td class="px-5 py-3 text-sm text-slate-600"><?php echo e($notif->user_name ?? '—'); ?></td>
                            <td class="px-5 py-3 text-center">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasData): ?>
                                    <div
                                        class="inline-flex items-center justify-center h-6 w-6 rounded-md transition-colors"
                                        :class="expanded === '<?php echo e($notifId); ?>' ? 'bg-[#005F02]/10 text-[#005F02]' : 'text-slate-300'"
                                    >
                                        <iconify-icon
                                            :icon="expanded === '<?php echo e($notifId); ?>' ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear'"
                                            width="14"
                                        ></iconify-icon>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                        </tr>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasData): ?>
                            <tr x-show="expanded === '<?php echo e($notifId); ?>'" x-cloak>
                                <td colspan="4" class="px-0 py-0">
                                    <div
                                        x-show="expanded === '<?php echo e($notifId); ?>'"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0"
                                        x-transition:enter-end="opacity-100"
                                        x-transition:leave="transition ease-in duration-100"
                                        x-transition:leave-start="opacity-100"
                                        x-transition:leave-end="opacity-0"
                                        class="border-b border-slate-100 bg-slate-50/50 px-5 py-4"
                                    >
                                        <div class="rounded-xl bg-white border border-slate-200 divide-y divide-slate-100 overflow-hidden">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $notif->data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                <div class="flex items-baseline gap-4 px-4 py-2.5 text-xs">
                                                    <span class="shrink-0 w-[120px] font-mono font-medium text-slate-400"><?php echo e($key); ?></span>
                                                    <span class="text-slate-700 break-all"><?php echo e(is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value); ?></span>
                                                </div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </div>
                                        <div class="mt-2.5 text-[11px] text-slate-400">
                                            <?php echo e(\Carbon\Carbon::parse($notif->created_at)->diffForHumans()); ?>

                                            &middot;
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isRead): ?>
                                                <?php echo e(__('super_admin.notifications.read')); ?>

                                            <?php else: ?>
                                                <span class="text-amber-600 font-medium"><?php echo e(__('super_admin.notifications.unread')); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_5): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center text-slate-400">
                                    <iconify-icon icon="solar:bell-bold-duotone" width="44" class="text-slate-300 mb-2"></iconify-icon>
                                    <p class="text-sm font-medium"><?php echo e(__('super_admin.notifications.empty_recent')); ?></p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\super-admin\notifications-global.blade.php ENDPATH**/ ?>