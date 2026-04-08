<div class="space-y-6 pb-12">
    
    <header>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900"><?php echo e(__('super_admin.files.title')); ?></h1>
        <p class="mt-1 text-sm text-slate-500"><?php echo e(__('super_admin.files.subtitle')); ?></p>
    </header>

    
    <div class="flex items-center gap-6 rounded-2xl border border-slate-200 bg-white px-6 py-5 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#F2E3BB]/30 text-[#005F02]">
                <iconify-icon icon="solar:folder-with-files-bold-duotone" width="20"></iconify-icon>
            </div>
            <div>
                <p class="text-[11px] font-medium text-slate-500"><?php echo e(__('super_admin.files.total_files')); ?></p>
                <p class="text-xl font-bold text-slate-900 tabular-nums"><?php echo e($this->totalStorage['total_files']); ?></p>
            </div>
        </div>
        <div class="h-10 w-px bg-slate-200"></div>
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                <iconify-icon icon="solar:server-bold-duotone" width="20"></iconify-icon>
            </div>
            <div>
                <p class="text-[11px] font-medium text-slate-500"><?php echo e(__('super_admin.files.total_size')); ?></p>
                <p class="text-xl font-bold text-emerald-600 tabular-nums"><?php echo e($this->totalStorage['total_size_human']); ?></p>
            </div>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($this->totalStorage['extensions']) > 0): ?>
            <div class="h-10 w-px bg-slate-200"></div>
            <div class="flex flex-wrap gap-1.5">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->totalStorage['extensions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ext => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <span class="inline-flex items-center rounded-md bg-slate-50 border border-slate-200 px-1.5 py-0.5 text-[10px] font-mono text-slate-500">
                        .<?php echo e($ext); ?> <span class="ml-0.5 text-slate-400"><?php echo e($count); ?></span>
                    </span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden" x-data="{ expanded: null }">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-sm font-semibold text-slate-800"><?php echo e(__('super_admin.files.storage_by_org')); ?></h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/40">
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500"><?php echo e(__('super_admin.files.col_org')); ?></th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500 text-center w-[100px]"><?php echo e(__('super_admin.files.col_files')); ?></th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500 text-right w-[120px]"><?php echo e(__('super_admin.files.col_size')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->storageByOrg; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $org): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3 text-sm font-medium text-slate-700"><?php echo e($org['org_name']); ?></td>
                            <td class="px-5 py-3 text-sm text-slate-600 text-center tabular-nums"><?php echo e($org['files_count']); ?></td>
                            <td class="px-5 py-3 text-sm font-medium text-slate-800 text-right tabular-nums"><?php echo e($org['size_human']); ?></td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center text-slate-400">
                                    <iconify-icon icon="solar:buildings-bold-duotone" width="36" class="text-slate-300 mb-2"></iconify-icon>
                                    <p class="text-sm"><?php echo e(__('super_admin.files.empty_orgs')); ?></p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden" x-data="{ expanded: null }">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-sm font-semibold text-slate-800"><?php echo e(__('super_admin.files.recent_files')); ?></h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/40">
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500"><?php echo e(__('super_admin.files.col_filename')); ?></th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500 text-right w-[100px]"><?php echo e(__('super_admin.files.col_size')); ?></th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500 w-[140px]"><?php echo e(__('super_admin.files.col_date')); ?></th>
                        <th class="px-5 py-3 w-[40px]"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->recentFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php
                            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                            $path = $file['path'] ?? '';
                        ?>

                        <tr
                            class="border-b border-slate-100 transition-colors cursor-pointer"
                            :class="expanded === <?php echo e($index); ?> ? 'bg-slate-50' : 'hover:bg-slate-50/50'"
                            @click="expanded = expanded === <?php echo e($index); ?> ? null : <?php echo e($index); ?>"
                        >
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="inline-flex items-center rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-mono font-medium text-slate-500 uppercase"><?php echo e($ext ?: '?'); ?></span>
                                    <span class="text-sm font-medium text-slate-700 truncate max-w-[300px]"><?php echo e($file['name']); ?></span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-sm text-slate-600 text-right tabular-nums"><?php echo e($file['size_human']); ?></td>
                            <td class="px-5 py-3">
                                <div class="text-xs text-slate-600"><?php echo e(\Illuminate\Support\Carbon::parse($file['modified'])->format('d/m/Y')); ?></div>
                                <div class="text-[11px] text-slate-400"><?php echo e(\Illuminate\Support\Carbon::parse($file['modified'])->format('H:i')); ?></div>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <div
                                    class="inline-flex items-center justify-center h-6 w-6 rounded-md transition-colors"
                                    :class="expanded === <?php echo e($index); ?> ? 'bg-[#005F02]/10 text-[#005F02]' : 'text-slate-300'"
                                >
                                    <iconify-icon
                                        :icon="expanded === <?php echo e($index); ?> ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear'"
                                        width="14"
                                    ></iconify-icon>
                                </div>
                            </td>
                        </tr>

                        
                        <tr x-show="expanded === <?php echo e($index); ?>" x-cloak>
                            <td colspan="4" class="px-0 py-0">
                                <div
                                    x-show="expanded === <?php echo e($index); ?>"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="border-b border-slate-100 bg-slate-50/50 px-5 py-4"
                                >
                                    <div class="rounded-xl bg-white border border-slate-200 divide-y divide-slate-100 overflow-hidden">
                                        <div class="flex items-baseline gap-4 px-4 py-2.5 text-xs">
                                            <span class="shrink-0 w-[100px] font-medium text-slate-400"><?php echo e(__('super_admin.files.col_filename')); ?></span>
                                            <span class="text-slate-700 font-medium break-all"><?php echo e($file['name']); ?></span>
                                        </div>
                                        <div class="flex items-baseline gap-4 px-4 py-2.5 text-xs">
                                            <span class="shrink-0 w-[100px] font-medium text-slate-400">Chemin</span>
                                            <span class="text-slate-600 font-mono text-[11px] break-all"><?php echo e($path); ?></span>
                                        </div>
                                        <div class="flex items-baseline gap-4 px-4 py-2.5 text-xs">
                                            <span class="shrink-0 w-[100px] font-medium text-slate-400"><?php echo e(__('super_admin.files.col_size')); ?></span>
                                            <span class="text-slate-700 tabular-nums"><?php echo e($file['size_human']); ?></span>
                                        </div>
                                        <div class="flex items-baseline gap-4 px-4 py-2.5 text-xs">
                                            <span class="shrink-0 w-[100px] font-medium text-slate-400"><?php echo e(__('super_admin.files.col_date')); ?></span>
                                            <span class="text-slate-700"><?php echo e($file['modified']); ?></span>
                                        </div>
                                        <div class="flex items-baseline gap-4 px-4 py-2.5 text-xs">
                                            <span class="shrink-0 w-[100px] font-medium text-slate-400">Extension</span>
                                            <span class="inline-flex items-center rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-mono font-medium text-slate-500 uppercase"><?php echo e($ext ?: '—'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center text-slate-400">
                                    <iconify-icon icon="solar:file-bold-duotone" width="36" class="text-slate-300 mb-2"></iconify-icon>
                                    <p class="text-sm"><?php echo e(__('super_admin.files.empty_files')); ?></p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\super-admin\files-storage.blade.php ENDPATH**/ ?>