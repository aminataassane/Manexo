<?php
    $snapshot = $response->field_snapshot ?? [];
    $answers = $response->responses ?? [];
    $baseFields = $response->base_fields ?? [];
    $sourceBadge = match($response->submitted_from) {
        'public' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'label' => __('forms_builder.source_public')],
        'internal_assignment' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-600', 'label' => __('forms_builder.source_internal_assignment')],
        'internal_team' => ['bg' => 'bg-slate-50', 'text' => 'text-slate-500', 'label' => __('forms_builder.source_internal_team')],
        'internal_team_slug' => ['bg' => 'bg-slate-50', 'text' => 'text-slate-500', 'label' => __('forms_builder.source_internal_team_slug')],
        default => ['bg' => 'bg-slate-50', 'text' => 'text-slate-500', 'label' => __('forms_builder.source_internal')],
    };
?>

<div class="p-5 lg:p-6 xl:p-8 space-y-6">
    <!-- Header -->
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <h3 class="text-base font-semibold text-slate-900">
                <?php echo e($response->respondent_name ?? $response->user?->name ?? __('Anonyme')); ?>

            </h3>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($response->respondent_email ?? $response->user?->email): ?>
                <p class="text-xs text-slate-500 mt-0.5"><?php echo e($response->respondent_email ?? $response->user?->email); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <span class="shrink-0 px-2 py-0.5 rounded-md text-[10px] font-medium <?php echo e($sourceBadge['bg']); ?> <?php echo e($sourceBadge['text']); ?>">
            <?php echo e($sourceBadge['label']); ?>

        </span>
    </div>

    <!-- Metadata -->
    <div>
        <h4 class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-2.5 flex items-center gap-1.5">
            <iconify-icon icon="solar:info-circle-linear" width="13"></iconify-icon>
            <?php echo e(__('forms_builder.metadata_section')); ?>

        </h4>
        <div class="grid grid-cols-2 gap-x-4 gap-y-2.5 text-xs">
            <div>
                <span class="text-slate-400 text-[10px]"><?php echo e(__('forms_builder.submitted_at')); ?></span>
                <p class="font-medium text-slate-800 mt-0.5"><?php echo e($response->created_at->format('d/m/Y H:i')); ?></p>
            </div>
            <div>
                <span class="text-slate-400 text-[10px]"><?php echo e(__('forms_builder.form_version_label')); ?></span>
                <p class="font-medium text-slate-800 mt-0.5">v<?php echo e($response->form_version); ?></p>
            </div>
            <div>
                <span class="text-slate-400 text-[10px]"><?php echo e(__('forms_builder.ip_address')); ?></span>
                <p class="font-medium text-slate-800 mt-0.5 font-mono text-[11px]"><?php echo e($response->ip_address ?? '—'); ?></p>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($response->public_form_slug): ?>
            <div>
                <span class="text-slate-400 text-[10px]">Slug</span>
                <p class="font-medium text-slate-800 mt-0.5 font-mono text-[11px]"><?php echo e($response->public_form_slug); ?></p>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <!-- Divider -->
    <div class="border-t border-slate-100"></div>

    <!-- Base fields (subject, description, category) -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($baseFields)): ?>
    <div>
        <h4 class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-2.5 flex items-center gap-1.5">
            <iconify-icon icon="solar:document-text-linear" width="13"></iconify-icon>
            <?php echo e(__('forms_builder.base_fields_section')); ?>

        </h4>
        <div class="space-y-3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($baseFields['subject'])): ?>
                <div>
                    <span class="text-[10px] font-medium text-slate-400"><?php echo e(__('forms_builder.subject')); ?></span>
                    <p class="text-sm text-slate-800 mt-0.5"><?php echo e($baseFields['subject']); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($baseFields['description'])): ?>
                <div>
                    <span class="text-[10px] font-medium text-slate-400"><?php echo e(__('forms_builder.description')); ?></span>
                    <p class="text-sm text-slate-800 mt-0.5 whitespace-pre-line"><?php echo e($baseFields['description']); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($baseFields['category_name'])): ?>
                <div>
                    <span class="text-[10px] font-medium text-slate-400"><?php echo e(__('forms_builder.category')); ?></span>
                    <p class="text-sm text-slate-800 mt-0.5"><?php echo e($baseFields['category_name']); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($baseFields['guest_name'])): ?>
                <div>
                    <span class="text-[10px] font-medium text-slate-400"><?php echo e(__('forms_builder.respondent')); ?> (guest)</span>
                    <p class="text-sm text-slate-800 mt-0.5"><?php echo e($baseFields['guest_name']); ?> — <?php echo e($baseFields['guest_email'] ?? ''); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <div class="border-t border-slate-100"></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Custom fields (from snapshot) -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($snapshot) > 0): ?>
    <div>
        <h4 class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-2.5 flex items-center gap-1.5">
            <iconify-icon icon="solar:list-check-linear" width="13"></iconify-icon>
            <?php echo e(__('forms_builder.custom_fields_section')); ?>

        </h4>
        <div class="space-y-2.5">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $snapshot; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sf): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($sf['type'] ?? '') === 'section'): ?>
                    <div class="pt-3 mt-1">
                        <h5 class="text-xs font-semibold text-slate-700 border-b border-slate-100 pb-2"><?php echo e($sf['label'] ?? ''); ?></h5>
                    </div>
                <?php else: ?>
                    <?php
                        $val = $answers[$sf['key'] ?? ''] ?? null;
                        $isFile = is_array($val) && ($val['type'] ?? '') === 'file';
                    ?>
                    <div class="py-1.5">
                        <span class="text-[10px] font-medium text-slate-400"><?php echo e($sf['label'] ?? $sf['key'] ?? '?'); ?></span>
                        <div class="text-sm text-slate-800 mt-0.5">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isFile): ?>
                                <a href="<?php echo e(route('admin.forms.responses.file', ['response' => $response->id, 'fieldKey' => $sf['key']])); ?>"
                                   target="_blank"
                                   class="inline-flex items-center gap-1.5 text-[var(--accent)] hover:opacity-80 text-xs font-medium">
                                    <iconify-icon icon="solar:download-minimalistic-linear" width="14"></iconify-icon>
                                    <?php echo e($val['original_name'] ?? __('forms_builder.download_file')); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($val['size'])): ?>
                                        <span class="text-[10px] text-slate-400">(<?php echo e(number_format($val['size'] / 1024, 0)); ?> KB)</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </a>
                            <?php elseif($val === null || $val === ''): ?>
                                <span class="text-slate-300">—</span>
                            <?php elseif(is_bool($val)): ?>
                                <?php echo e($val ? __('Oui') : __('Non')); ?>

                            <?php elseif(is_array($val)): ?>
                                <?php echo e(implode(', ', $val)); ?>

                            <?php else: ?>
                                <?php echo e($val); ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </div>
    <?php elseif(count($answers) > 0): ?>
    
    <div>
        <h4 class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-2.5"><?php echo e(__('forms_builder.custom_fields_section')); ?></h4>
        <div class="space-y-2.5">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $answers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <?php $isFile = is_array($val) && ($val['type'] ?? '') === 'file'; ?>
                <div class="py-1.5">
                    <span class="text-[10px] font-medium text-slate-400 font-mono"><?php echo e($key); ?></span>
                    <div class="text-sm text-slate-800 mt-0.5">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isFile): ?>
                            <a href="<?php echo e(route('admin.forms.responses.file', ['response' => $response->id, 'fieldKey' => $key])); ?>"
                               target="_blank"
                               class="inline-flex items-center gap-1.5 text-[var(--accent)] hover:opacity-80 text-xs font-medium">
                                <iconify-icon icon="solar:download-minimalistic-linear" width="14"></iconify-icon>
                                <?php echo e($val['original_name'] ?? __('forms_builder.download_file')); ?>

                            </a>
                        <?php elseif(is_bool($val)): ?>
                            <?php echo e($val ? __('Oui') : __('Non')); ?>

                        <?php elseif(is_array($val)): ?>
                            <?php echo e(implode(', ', $val)); ?>

                        <?php else: ?>
                            <?php echo e($val); ?>

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Ticket link -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($response->ticket_id): ?>
    <div class="pt-2 border-t border-slate-100">
        <?php $responseTicket = $response->ticket; ?>
        <a href="<?php echo e($responseTicket ? route('tickets.discussion', $responseTicket) : '#'); ?>"
           class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-white rounded-lg hover:opacity-90 transition-all"
           style="background: var(--accent);">
            <iconify-icon icon="solar:ticket-linear" width="15"></iconify-icon>
            <?php echo e(__('forms_builder.open_ticket', ['reference' => $responseTicket?->shortReference() ?? $response->ticket_id])); ?>

        </a>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\admin\partials\form-response-detail.blade.php ENDPATH**/ ?>