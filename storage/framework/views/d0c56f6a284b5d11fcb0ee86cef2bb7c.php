<div class="space-y-6">
    <!-- Back link -->
    <a href="<?php echo e(route('platform-admin.organizations')); ?>" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-[#005F02] transition-colors">
        <iconify-icon icon="solar:arrow-left-linear" width="16"></iconify-icon>
        <?php echo e(__('super_admin.org_detail.back')); ?>

    </a>

    <!-- Org Info Card -->
    <div class="rounded-2xl border border-slate-200/60 bg-white p-6 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl text-xl font-bold text-white" style="background: <?php echo e($organization->primary_color ?: '#4f46e5'); ?>">
                <?php echo e(mb_substr($organization->name, 0, 1)); ?>

            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-xl font-bold text-slate-800"><?php echo e($organization->name); ?></h2>
                <p class="text-sm text-slate-500 mt-0.5"><?php echo e($organization->slug); ?></p>
            </div>
            <div>
                <?php
                    $status = $organization->status ?? 'active';
                    $badge = match($status) {
                        'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'suspended' => 'bg-amber-50 text-amber-700 border-amber-200',
                        'disabled' => 'bg-red-50 text-red-700 border-red-200',
                        default => 'bg-slate-50 text-slate-700 border-slate-200',
                    };
                ?>
                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold <?php echo e($badge); ?>">
                    <?php echo e(__('super_admin.organizations.status_' . $status)); ?>

                </span>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400"><?php echo e(__('super_admin.org_detail.creator')); ?></p>
                <p class="text-sm font-medium text-slate-700 mt-0.5"><?php echo e($organization->creator?->name ?? '—'); ?></p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400"><?php echo e(__('super_admin.org_detail.created_at')); ?></p>
                <p class="text-sm font-medium text-slate-700 mt-0.5"><?php echo e($organization->created_at?->format('d/m/Y H:i')); ?></p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400"><?php echo e(__('super_admin.org_detail.color')); ?></p>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="h-5 w-5 rounded-md border border-slate-200" style="background: <?php echo e($organization->primary_color ?: '#ccc'); ?>"></span>
                    <span class="text-sm text-slate-700"><?php echo e($organization->primary_color ?: '—'); ?></span>
                </div>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400"><?php echo e(__('super_admin.org_detail.members_count')); ?></p>
                <p class="text-sm font-medium text-slate-700 mt-0.5"><?php echo e($organization->memberships()->count()); ?></p>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($organization->isSuspended() && $organization->suspension_reason): ?>
            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-amber-600 mb-1"><?php echo e(__('super_admin.org_detail.suspension_reason')); ?></p>
                <p class="text-sm text-amber-800"><?php echo e($organization->suspension_reason); ?></p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- Members Table -->
    <div class="rounded-2xl border border-slate-200/60 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <h3 class="font-semibold text-slate-800"><?php echo e(__('super_admin.org_detail.members')); ?></h3>
            <div class="relative flex-1 max-w-xs">
                <iconify-icon icon="solar:magnifer-linear" width="16" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></iconify-icon>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="memberSearch"
                    placeholder="<?php echo e(__('super_admin.org_detail.search_members')); ?>"
                    class="w-full rounded-xl border border-slate-200 bg-white py-2 pl-9 pr-4 text-sm text-slate-700 placeholder-slate-400 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all"
                />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"><?php echo e(__('super_admin.org_detail.col_name')); ?></th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"><?php echo e(__('super_admin.org_detail.col_email')); ?></th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"><?php echo e(__('super_admin.org_detail.col_role')); ?></th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"><?php echo e(__('super_admin.org_detail.col_joined')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#F2E3BB]/30 text-xs font-bold text-[#005F02]">
                                        <?php echo e(mb_substr($member->name, 0, 1)); ?>

                                    </div>
                                    <span class="text-sm font-medium text-slate-800"><?php echo e($member->name); ?></span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-500"><?php echo e($member->email); ?></td>
                            <td class="px-5 py-3.5">
                                <?php
                                    $role = $member->pivot->role ?? 'member';
                                    $roleBadge = match($role) {
                                        'owner' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'admin' => 'bg-[#F2E3BB]/30 text-[#005F02] border-[#005F02]/20',
                                        default => 'bg-slate-50 text-slate-700 border-slate-200',
                                    };
                                ?>
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold <?php echo e($roleBadge); ?>">
                                    <?php echo e(ucfirst($role)); ?>

                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-500"><?php echo e($member->pivot->created_at ? \Carbon\Carbon::parse($member->pivot->created_at)->format('d/m/Y') : '—'); ?></td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center text-sm text-slate-400">
                                <?php echo e(__('super_admin.org_detail.no_members')); ?>

                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($members->hasPages()): ?>
            <div class="border-t border-slate-100 px-5 py-3">
                <?php echo e($members->links()); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\super-admin\organization-detail.blade.php ENDPATH**/ ?>