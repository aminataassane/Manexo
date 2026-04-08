
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->isSystem): ?>
    <div id="message-<?php echo e($item->id); ?>" class="message-row message-system flex justify-center py-2 sm:py-3 min-w-0">
        <div class="inline-flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-full bg-slate-100 text-slate-600 text-[11px] sm:text-xs max-w-full">
            <iconify-icon icon="solar:info-circle-linear" width="14" class="shrink-0 text-slate-500"></iconify-icon>
            <span class="break-words min-w-0 max-w-[min(100%,28rem)]"><?php echo $item->body; ?></span>
            <span class="text-slate-400 shrink-0">· <?php echo e($item->timeHuman); ?></span>
        </div>
    </div>
<?php elseif($item->isInternal): ?>
    <div id="message-<?php echo e($item->id); ?>" class="message-row message-note flex gap-2 sm:gap-3 py-2 sm:py-3 min-w-0 max-w-[95%] sm:max-w-[85%]">
        <div class="w-7 h-7 sm:w-8 sm:h-8 shrink-0 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
            <iconify-icon icon="solar:lock-keyhole-linear" width="14"></iconify-icon>
        </div>
        <div class="message-bubble flex-1 min-w-0 rounded-2xl rounded-tl-md bg-amber-50/90 border border-amber-200/80 shadow-sm overflow-hidden">
            <div class="px-3 py-2 sm:px-4 sm:py-2.5 flex flex-wrap items-center justify-between gap-1.5 sm:gap-2 border-b border-amber-200/60 bg-amber-50/50">
                <div class="flex items-center gap-1.5 sm:gap-2">
                    <span class="text-[10px] sm:text-[11px] font-bold text-amber-800 uppercase tracking-wide"><?php echo e(__('Note interne')); ?></span>
                    <span class="text-[10px] text-amber-600"><?php echo e($item->authorName); ?></span>
                </div>
                <span class="text-[10px] text-amber-600/90"><?php echo e($item->timeHuman); ?></span>
            </div>
            <div class="px-3 py-2.5 sm:px-4 sm:py-3 text-[13px] sm:text-sm leading-relaxed text-amber-900 break-words">
                <?php echo $item->body; ?>

            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->hasAttachments): ?>
                <div class="px-3 sm:px-4 pb-3">
                    <?php echo $__env->make('livewire.tickets.partials.attachments-group', ['attachments' => $item->attachments, 'variant' => 'note'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
<?php else: ?>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->isOwn): ?>
        <div id="message-<?php echo e($item->id); ?>" class="message-row message-own flex justify-end py-2 sm:py-3 min-w-0">
            <div class="flex items-end gap-2 sm:gap-3 max-w-[95%] sm:max-w-[85%] min-w-0 flex-row-reverse">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->showAvatarPopover): ?>
                    <?php if (isset($component)) { $__componentOriginalfac35cd68d3e581fb028d29d830dd47b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfac35cd68d3e581fb028d29d830dd47b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.user-avatar-popover','data' => ['initials' => $item->initials,'name' => $item->authorName ?? '—','email' => $item->authorEmail,'mentionTag' => $item->mentionTag,'badges' => $item->popoverBadges,'openLabel' => __('tickets.timeline_user_card_open'),'sizeClass' => 'min-h-11 min-w-11 h-11 w-11 sm:h-9 sm:w-9 sm:min-h-0 sm:min-w-0 ring-2 ring-white shadow-md']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.user-avatar-popover'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['initials' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->initials),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->authorName ?? '—'),'email' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->authorEmail),'mention-tag' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->mentionTag),'badges' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->popoverBadges),'open-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('tickets.timeline_user_card_open')),'size-class' => 'min-h-11 min-w-11 h-11 w-11 sm:h-9 sm:w-9 sm:min-h-0 sm:min-w-0 ring-2 ring-white shadow-md']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfac35cd68d3e581fb028d29d830dd47b)): ?>
<?php $attributes = $__attributesOriginalfac35cd68d3e581fb028d29d830dd47b; ?>
<?php unset($__attributesOriginalfac35cd68d3e581fb028d29d830dd47b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfac35cd68d3e581fb028d29d830dd47b)): ?>
<?php $component = $__componentOriginalfac35cd68d3e581fb028d29d830dd47b; ?>
<?php unset($__componentOriginalfac35cd68d3e581fb028d29d830dd47b); ?>
<?php endif; ?>
                <?php else: ?>
                    <div class="h-11 w-11 sm:h-9 sm:w-9 shrink-0 rounded-full ring-2 ring-white shadow-md overflow-hidden bg-slate-100">
                        <img src="<?php echo e($item->avatarUrl); ?>" class="w-full h-full object-cover" alt="">
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="flex flex-col items-end min-w-0 max-w-full">
                    <div class="mb-1 flex w-full min-w-0 flex-col items-stretch gap-1 sm:mb-1.5 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end sm:gap-x-2 sm:gap-y-1">
                        <div class="flex min-w-0 flex-wrap items-center justify-end gap-1.5 sm:justify-end">
                            <span class="max-w-full text-[11px] font-semibold leading-snug text-slate-800 [overflow-wrap:anywhere] sm:text-xs"><?php echo e($item->authorName ?? '—'); ?></span>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->hasEmailOrigin): ?>
                                <span class="inline-flex max-w-full min-w-0 shrink items-center gap-1 rounded-md border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[9px] font-medium text-emerald-600 sm:text-[10px]">
                                    <iconify-icon icon="solar:letter-linear" width="10" class="shrink-0"></iconify-icon>
                                    <span class="min-w-0 truncate sm:whitespace-normal sm:[overflow-wrap:anywhere]"><?php echo e($item->badgePrimary); ?></span>
                                </span>
                            <?php elseif($item->channel === 'api'): ?>
                                <span class="inline-flex max-w-full items-center gap-1 rounded-md border border-violet-200 bg-violet-50 px-2 py-0.5 text-[9px] font-medium text-violet-600 sm:text-[10px]">
                                    <iconify-icon icon="solar:code-square-linear" width="10" class="shrink-0"></iconify-icon>
                                    <?php echo e($item->badgePrimary); ?>

                                </span>
                            <?php else: ?>
                                <span class="inline-flex max-w-full rounded-md bg-slate-700/10 px-2 py-0.5 text-[9px] font-medium text-slate-600 sm:text-[10px]"><?php echo e($item->roleLabel); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <span class="text-end text-[10px] text-slate-400 sm:ml-auto sm:text-start"><?php echo e($item->timeHuman); ?></span>
                    </div>
                    <div class="message-bubble rounded-2xl rounded-br-md text-[13px] sm:text-sm leading-relaxed shadow-md w-full max-w-full overflow-hidden"
                         style="<?php echo e($item->hasAttachments
                             ? 'background: color-mix(in srgb, var(--accent) 8%, white); border: 1px solid color-mix(in srgb, var(--accent) 18%, #e2e8f0);'
                             : 'background: linear-gradient(135deg, var(--accent) 0%, color-mix(in srgb, var(--accent) 85%, #1e293b) 100%); color: #fff;'); ?>">
                        <div class="px-3 py-2.5 sm:px-4 sm:py-3 text-left break-words <?php echo e($item->hasAttachments ? 'text-slate-800' : 'text-white'); ?>">
                            <?php echo $item->body; ?>

                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->hasAttachments): ?>
                            <div class="px-3 sm:px-4 pb-3">
                                <?php echo $__env->make('livewire.tickets.partials.attachments-group', ['attachments' => $item->attachments, 'variant' => 'mine'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div id="message-<?php echo e($item->id); ?>" class="message-row message-incoming flex gap-2 sm:gap-3 py-2 sm:py-3 min-w-0 max-w-[95%] sm:max-w-[85%]">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->showAvatarPopover): ?>
                <?php if (isset($component)) { $__componentOriginalfac35cd68d3e581fb028d29d830dd47b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfac35cd68d3e581fb028d29d830dd47b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.user-avatar-popover','data' => ['initials' => $item->initials,'name' => $item->authorName ?? '—','email' => $item->authorEmail,'mentionTag' => $item->mentionTag,'badges' => $item->popoverBadges,'openLabel' => __('tickets.timeline_user_card_open'),'sizeClass' => 'min-h-11 min-w-11 h-11 w-11 sm:h-9 sm:w-9 sm:min-h-0 sm:min-w-0 ring-2 ring-white shadow-md']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.user-avatar-popover'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['initials' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->initials),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->authorName ?? '—'),'email' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->authorEmail),'mention-tag' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->mentionTag),'badges' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->popoverBadges),'open-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('tickets.timeline_user_card_open')),'size-class' => 'min-h-11 min-w-11 h-11 w-11 sm:h-9 sm:w-9 sm:min-h-0 sm:min-w-0 ring-2 ring-white shadow-md']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfac35cd68d3e581fb028d29d830dd47b)): ?>
<?php $attributes = $__attributesOriginalfac35cd68d3e581fb028d29d830dd47b; ?>
<?php unset($__attributesOriginalfac35cd68d3e581fb028d29d830dd47b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfac35cd68d3e581fb028d29d830dd47b)): ?>
<?php $component = $__componentOriginalfac35cd68d3e581fb028d29d830dd47b; ?>
<?php unset($__componentOriginalfac35cd68d3e581fb028d29d830dd47b); ?>
<?php endif; ?>
            <?php else: ?>
                <div class="h-11 w-11 sm:h-9 sm:w-9 shrink-0 rounded-full ring-2 ring-white shadow-md overflow-hidden bg-slate-100">
                    <img src="<?php echo e($item->avatarUrl); ?>" class="w-full h-full object-cover" alt="">
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="min-w-0 w-full max-w-full sm:w-fit">
                <div class="mb-1 flex w-full min-w-0 flex-col gap-1 sm:mb-1.5 sm:flex-row sm:flex-wrap sm:items-center sm:gap-x-2 sm:gap-y-1">
                    <div class="flex min-w-0 flex-wrap items-center gap-1.5">
                        <span class="max-w-full text-[11px] font-semibold leading-snug text-slate-800 [overflow-wrap:anywhere] sm:text-xs"><?php echo e($item->authorName ?? '—'); ?></span>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->hasEmailOrigin): ?>
                            <span class="inline-flex max-w-full min-w-0 shrink items-center gap-1 rounded-md px-2 py-0.5 text-[9px] font-medium sm:text-[10px] <?php echo e($item->channel === 'email_inbound' ? 'border border-blue-200 bg-blue-50 text-blue-600' : 'border border-emerald-200 bg-emerald-50 text-emerald-600'); ?>">
                                <iconify-icon icon="solar:letter-linear" width="10" class="shrink-0"></iconify-icon>
                                <span class="min-w-0 truncate sm:whitespace-normal sm:[overflow-wrap:anywhere]"><?php echo e($item->badgePrimary); ?></span>
                            </span>
                        <?php elseif($item->channel === 'api'): ?>
                            <span class="inline-flex max-w-full items-center gap-1 rounded-md border border-violet-200 bg-violet-50 px-2 py-0.5 text-[9px] font-medium text-violet-600 sm:text-[10px]">
                                <iconify-icon icon="solar:code-square-linear" width="10" class="shrink-0"></iconify-icon>
                                <?php echo e($item->badgePrimary); ?>

                            </span>
                        <?php else: ?>
                            <span class="inline-flex max-w-full rounded-md border border-slate-200/80 bg-slate-100 px-2 py-0.5 text-[9px] font-medium text-slate-600 sm:text-[10px]"><?php echo e($item->roleLabel); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <span class="text-[10px] text-slate-400 sm:ml-auto"><?php echo e($item->timeHuman); ?></span>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->hasEmailOrigin && $item->emailFrom): ?>
                    <div class="mb-1 text-[10px] leading-snug text-slate-400 [overflow-wrap:anywhere] break-all">via <?php echo e($item->emailFrom); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="message-bubble rounded-2xl rounded-bl-md text-[13px] sm:text-sm leading-relaxed bg-white border border-slate-200 shadow-sm break-words w-fit max-w-full min-w-0 overflow-hidden">
                    <div class="px-3 py-2.5 sm:px-4 sm:py-3">
                        <?php echo $item->body; ?>

                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->hasAttachments): ?>
                        <div class="px-3 sm:px-4 pb-3">
                            <?php echo $__env->make('livewire.tickets.partials.attachments-group', ['attachments' => $item->attachments, 'variant' => 'theirs'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/livewire/tickets/partials/timeline-item.blade.php ENDPATH**/ ?>