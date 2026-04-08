<div class="w-full max-w-full min-w-0 mx-auto" wire:init="loadPage" wire:poll.45s>
    
    <div class="page-header">
        <div class="min-w-0">
            <h1 class="page-title"><?php echo e(__('pages.groups.title')); ?></h1>
            <p class="page-subtitle"><?php echo e(__('pages.groups.subtitle')); ?></p>
        </div>
        <div class="page-actions">
            <a href="<?php echo e(route('tickets.index')); ?>" wire:navigate.hover
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 sm:px-4 text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50 hover:text-slate-900 transition-all">
                <iconify-icon icon="solar:ticket-bold-duotone" width="18"></iconify-icon>
                <span class="hidden sm:inline"><?php echo e(__('pages.groups.all_tickets')); ?></span>
            </a>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $ready): ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 animate-pulse">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 4; $i++): ?>
                <div class="content-card p-5 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="h-4 w-4 rounded-full bg-slate-200"></div>
                        <div class="h-5 w-32 rounded bg-slate-200"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="h-12 rounded-lg bg-slate-100"></div>
                        <div class="h-12 rounded-lg bg-slate-100"></div>
                        <div class="h-12 rounded-lg bg-slate-100"></div>
                    </div>
                    <div class="space-y-2">
                        <div class="h-3 w-full rounded bg-slate-50"></div>
                        <div class="h-3 w-2/3 rounded bg-slate-50"></div>
                    </div>
                </div>
            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php elseif($groups->isEmpty() && !$ungroupedStats): ?>
        
        <div class="content-card">
            <div class="empty-state py-16">
                <div class="empty-state-icon">
                    <iconify-icon icon="solar:folder-with-files-bold-duotone" width="28" class="text-slate-300"></iconify-icon>
                </div>
                <p class="empty-state-title"><?php echo e(__('pages.groups.no_groups')); ?></p>
                <p class="empty-state-text"><?php echo e(__('pages.groups.no_groups_hint')); ?></p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canSeeSettings): ?>
                    <a href="<?php echo e(route('admin.settings')); ?>" class="mt-5 inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:opacity-90 transition-all" style="background-color: var(--accent);">
                        <iconify-icon icon="solar:settings-bold-duotone" width="18"></iconify-icon>
                        <?php echo e(__('menu.settings')); ?>

                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="relative flex flex-col lg:flex-row gap-5 lg:gap-6">
            <div wire:loading.flex wire:target="$refresh" class="absolute inset-0 z-10 items-center justify-center bg-white/50 backdrop-blur-[1px] text-xs text-slate-500 rounded-xl">
                <iconify-icon icon="solar:refresh-linear" width="14" class="animate-spin mr-1"></iconify-icon>
                <?php echo e(__('Chargement...')); ?>

            </div>
            
            <div wire:loading.class="opacity-70 pointer-events-none" wire:target="$refresh" class="flex-1 min-w-0 space-y-4 transition-opacity duration-150">

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <?php
                        $gid = (int) $grp->id;
                        $totalActive = (int) $grp->open_count + (int) $grp->in_progress_count + (int) $grp->pending_count;
                        $color = $grp->color ?? 'var(--accent)';
                        $unassigned = (int) ($grp->unassigned_count ?? 0);
                        $slaBreach = (int) ($grp->sla_breached_count ?? 0);
                        $tickets = $topTicketsByGroup[$gid] ?? [];
                        $agents = $agentsByGroup[$gid] ?? [];
                        $hasAlerts = $unassigned > 0 || $slaBreach > 0;
                    ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($totalActive > 0): ?>
                        
                        <div class="rounded-xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                            
                            <div class="px-5 py-4 flex items-center gap-3">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-white text-xs font-bold" style="background-color: <?php echo e($color); ?>;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($grp->icon): ?>
                                        <iconify-icon icon="<?php echo e($grp->icon); ?>" width="16"></iconify-icon>
                                    <?php else: ?>
                                        <?php echo e(strtoupper(mb_substr($grp->name, 0, 1))); ?>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-sm font-bold text-slate-900"><?php echo e($grp->name); ?></h3>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasAlerts): ?>
                                        <div class="flex items-center gap-2 mt-0.5 text-[11px]">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($slaBreach > 0): ?>
                                                <span class="text-red-600 font-medium"><?php echo e($slaBreach); ?> <?php echo e(__('en retard SLA')); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unassigned > 0): ?>
                                                <span class="text-amber-600 font-medium"><?php echo e($unassigned); ?> <?php echo e(__('non assigné(s)')); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <span class="text-xs text-slate-400 font-medium shrink-0"><?php echo e($totalActive); ?> <?php echo e(__('actif(s)')); ?></span>
                            </div>

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($tickets) > 0): ?>
                                <div class="border-t border-slate-100">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <?php
                                            $pLevel = (int) ($t->priority_level ?? 2);
                                            $age = \Carbon\Carbon::parse($t->created_at)->diffForHumans(short: true);
                                            $isHighPriority = $pLevel >= 3;
                                        ?>
                                        <a href="<?php echo e(route('tickets.discussion', ['ticket' => $t->public_id])); ?>" wire:navigate.hover
                                           class="flex items-center gap-3 px-5 py-2.5 hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-b-0">
                                            <span class="font-mono text-[11px] text-slate-400 shrink-0 w-16"><?php echo e(\Illuminate\Support\Str::limit($t->public_id, 10)); ?></span>
                                            <span class="text-sm text-slate-900 font-medium truncate flex-1 min-w-0"><?php echo e($t->subject); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isHighPriority): ?>
                                                <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium <?php echo e($pLevel >= 4 ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-amber-50 text-amber-700 border border-amber-200'); ?>">
                                                    <?php echo e($t->priority_name ?? ''); ?>

                                                </span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($t->sla_resolution_breached): ?>
                                                <span class="shrink-0 text-[10px] font-medium text-red-600">SLA</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($t->assignee_name): ?>
                                                <span class="shrink-0 text-xs text-slate-500 max-w-[6rem] truncate hidden sm:inline"><?php echo e($t->assignee_name); ?></span>
                                            <?php else: ?>
                                                <span class="shrink-0 text-xs text-red-500 font-medium hidden sm:inline"><?php echo e(__('Non assigné')); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <span class="shrink-0 text-[10px] text-slate-400 w-8 text-right"><?php echo e($age); ?></span>
                                        </a>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            
                            <div class="px-5 py-2.5 border-t border-slate-100 bg-slate-50/50">
                                <a href="<?php echo e(route('tickets.index', ['group' => $grp->id])); ?>" wire:navigate.hover class="text-xs font-medium text-slate-500 hover:text-slate-900 transition-colors">
                                    <?php echo e(__('Voir tous les tickets')); ?> &rarr;
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        
                        <a href="<?php echo e(route('tickets.index', ['group' => $grp->id])); ?>" wire:navigate.hover
                           class="flex items-center gap-3 rounded-xl bg-white border border-slate-200 px-5 py-3 hover:bg-slate-50 transition-colors shadow-sm">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-white text-xs font-bold" style="background-color: <?php echo e($color); ?>; opacity: 0.6;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($grp->icon): ?>
                                    <iconify-icon icon="<?php echo e($grp->icon); ?>" width="14"></iconify-icon>
                                <?php else: ?>
                                    <?php echo e(strtoupper(mb_substr($grp->name, 0, 1))); ?>

                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                            <span class="text-sm font-medium text-slate-600"><?php echo e($grp->name); ?></span>
                            <span class="ml-auto text-xs text-slate-400">0 <?php echo e(__('actif')); ?></span>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ungroupedStats): ?>
                    <?php
                        $ungroupedTotal = (int) $ungroupedStats->open_count + (int) $ungroupedStats->in_progress_count + (int) $ungroupedStats->pending_count;
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ungroupedTotal > 0): ?>
                        <div class="rounded-xl bg-white border border-dashed border-slate-300 shadow-sm overflow-hidden">
                            <div class="px-5 py-4 flex items-center gap-3">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-400 text-xs font-bold">
                                    <iconify-icon icon="solar:minus-circle-linear" width="16"></iconify-icon>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-sm font-bold text-slate-700"><?php echo e(__('pages.groups.ungrouped')); ?></h3>
                                </div>
                                <span class="text-xs text-slate-400 font-medium shrink-0"><?php echo e($ungroupedTotal); ?> <?php echo e(__('actif(s)')); ?></span>
                            </div>
                            <div class="px-5 py-2.5 border-t border-slate-100 bg-slate-50/50">
                                <a href="<?php echo e(route('tickets.index', ['group' => 'none'])); ?>" wire:navigate.hover class="text-xs font-medium text-slate-500 hover:text-slate-900 transition-colors">
                                    <?php echo e(__('Voir tous les tickets')); ?> &rarr;
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo e(route('tickets.index', ['group' => 'none'])); ?>" wire:navigate.hover
                           class="flex items-center gap-3 rounded-xl bg-white border border-dashed border-slate-300 px-5 py-3 hover:bg-slate-50 transition-colors shadow-sm">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-400 text-xs font-bold">
                                <iconify-icon icon="solar:minus-circle-linear" width="14"></iconify-icon>
                            </span>
                            <span class="text-sm font-medium text-slate-500"><?php echo e(__('pages.groups.ungrouped')); ?></span>
                            <span class="ml-auto text-xs text-slate-400">0 <?php echo e(__('actif')); ?></span>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="lg:w-72 xl:w-80 shrink-0 space-y-4">

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($alerts) > 0): ?>
                    <div class="rounded-xl bg-white border border-slate-200 shadow-sm p-4">
                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3"><?php echo e(__('Alertes')); ?></h4>
                        <div class="space-y-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $alerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <div class="flex items-center gap-2.5 text-sm">
                                    <iconify-icon icon="<?php echo e($alert['icon']); ?>" width="16" class="<?php echo e($alert['type'] === 'danger' ? 'text-red-500' : 'text-amber-500'); ?>"></iconify-icon>
                                    <span class="<?php echo e($alert['type'] === 'danger' ? 'text-red-700' : 'text-amber-700'); ?> font-medium"><?php echo e($alert['text']); ?></span>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php
                    $allAgents = collect();
                    foreach ($agentsByGroup as $gAgents) {
                        foreach ($gAgents as $a) {
                            $existing = $allAgents->firstWhere('user_id', $a->user_id);
                            if ($existing) {
                                $existing->ticket_count = (int) $existing->ticket_count + (int) $a->ticket_count;
                            } else {
                                $allAgents->push((object) ['user_id' => $a->user_id, 'user_name' => $a->user_name, 'ticket_count' => (int) $a->ticket_count]);
                            }
                        }
                    }
                    $allAgents = $allAgents->sortByDesc('ticket_count')->values();
                ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($allAgents->isNotEmpty()): ?>
                    <div class="rounded-xl bg-white border border-slate-200 shadow-sm p-4">
                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3"><?php echo e(__('Charge par agent')); ?></h4>
                        <div class="space-y-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $allAgents->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <div class="flex items-center gap-2.5">
                                    <div class="h-7 w-7 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0" style="background: var(--accent-soft); color: var(--accent);">
                                        <?php echo e(strtoupper(mb_substr($agent->user_name, 0, 2))); ?>

                                    </div>
                                    <span class="text-sm text-slate-700 font-medium truncate flex-1 min-w-0"><?php echo e($agent->user_name); ?></span>
                                    <span class="text-xs text-slate-500 font-medium shrink-0 tabular-nums"><?php echo e($agent->ticket_count); ?></span>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php
                    $totalOpen = $groups->sum(fn ($g) => (int) $g->open_count) + (int) ($ungroupedStats->open_count ?? 0);
                    $totalInProgress = $groups->sum(fn ($g) => (int) $g->in_progress_count) + (int) ($ungroupedStats->in_progress_count ?? 0);
                    $totalPending = $groups->sum(fn ($g) => (int) $g->pending_count) + (int) ($ungroupedStats->pending_count ?? 0);
                ?>
                <div class="rounded-xl bg-white border border-slate-200 shadow-sm p-4">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3"><?php echo e(__('Vue d\'ensemble')); ?></h4>
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600"><?php echo e(__('Ouverts')); ?></span>
                            <span class="text-sm font-bold text-slate-900 tabular-nums"><?php echo e($totalOpen); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600"><?php echo e(__('En cours')); ?></span>
                            <span class="text-sm font-bold text-slate-900 tabular-nums"><?php echo e($totalInProgress); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600"><?php echo e(__('En attente')); ?></span>
                            <span class="text-sm font-bold text-slate-900 tabular-nums"><?php echo e($totalPending); ?></span>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                            <span class="text-sm text-slate-700 font-medium"><?php echo e(__('Total actifs')); ?></span>
                            <span class="text-sm font-bold text-slate-900 tabular-nums"><?php echo e($totalOpen + $totalInProgress + $totalPending); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\tickets\groups.blade.php ENDPATH**/ ?>