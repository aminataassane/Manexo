<div class="flex flex-col min-h-0 flex-1">
    <div class="flex-1 min-h-0" x-data="{ dragFieldId: null, dropTargetId: null }">
        <?php
            $selected = $selectedForm ?? ($fb_selected_form_id ? $forms->firstWhere('id', (int) $fb_selected_form_id) : null);
            $allFields = $selected?->fields ?? collect();
        ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selected): ?>
            
            <div class="mb-8 sm:mb-12">
                <h2 class="text-xl font-bold leading-tight tracking-tight text-slate-900 sm:text-2xl lg:text-3xl"><?php echo e($selected->name); ?></h2>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selected->description): ?>
                    <p class="text-sm text-slate-400 mt-3 leading-relaxed max-w-xl"><?php echo e($selected->description); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($allFields->isNotEmpty()): ?>
                    <div class="mt-8 flex items-center gap-3">
                        <div class="flex-1 h-px bg-gradient-to-r from-slate-200/80 via-slate-100/60 to-transparent"></div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-medium text-slate-400 bg-white border border-slate-100 shadow-sm">
                            <iconify-icon icon="solar:layers-linear" width="13"></iconify-icon>
                            <?php echo e(trans_choice('forms_builder.field_count', $allFields->count(), ['count' => $allFields->count()])); ?>

                        </span>
                        <div class="flex-1 h-px bg-gradient-to-l from-slate-200/80 via-slate-100/60 to-transparent"></div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($allFields->isNotEmpty()): ?>
                <div class="rounded-2xl border-2 border-dashed transition-all duration-200 -mb-2 min-h-[6px]"
                     :class="{ 'min-h-[48px] border-[var(--accent)] bg-[var(--accent-soft)]/15': dragFieldId && dropTargetId === 'top', 'border-transparent': !dragFieldId || dropTargetId !== 'top' }"
                     @dragover.prevent="if (dragFieldId) dropTargetId = 'top'"
                     @dragleave="dropTargetId = null"
                     @drop.prevent="if (dragFieldId) { $wire.reorderFormField(dragFieldId, 0); dragFieldId = null; dropTargetId = null; }"
                ></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div class="canvas-grid">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_4 = true; $__currentLoopData = $allFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_4 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <?php
                        $isActive = (int) $fb_selected_field_id === (int) $f->id;
                        $isRequired = (bool) $f->required;
                        $label = (string) $f->label;
                        $type = (string) $f->type;
                        $config = is_array($f->configuration) ? $f->configuration : [];
                        $placeholder = (string) ($config['placeholder'] ?? '');
                        $help = (string) ($config['help_text'] ?? '');
                        $options = $config['options'] ?? [];
                        $displayMode = $config['display_mode'] ?? 'list';
                        $fieldLayout = $config['layout'] ?? 'full';
                        $layoutClass = match($fieldLayout) {
                            'half' => 'canvas-field-half',
                            'third' => 'canvas-field-third',
                            default => 'canvas-field-full',
                        };
                    ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'section'): ?>
                        
                        <div
                            draggable="true"
                            wire:click="selectField(<?php echo e((int) $f->id); ?>)"
                            class="canvas-field-full canvas-section group px-4 py-5 sm:px-6 sm:py-6 <?php echo e($isActive ? 'is-active' : ''); ?>"
                            :class="{ 'is-drop-target': dropTargetId === <?php echo e((int) $f->id); ?> }"
                            @dragstart="dragFieldId = <?php echo e((int) $f->id); ?>"
                            @dragend="dragFieldId = null; dropTargetId = null"
                            @dragover.prevent="if (dragFieldId && dragFieldId !== <?php echo e((int) $f->id); ?>) dropTargetId = <?php echo e((int) $f->id); ?>"
                            @dragleave="if (dropTargetId === <?php echo e((int) $f->id); ?>) dropTargetId = null"
                            @drop.prevent="if (dragFieldId && dragFieldId !== <?php echo e((int) $f->id); ?>) { $wire.reorderFormField(dragFieldId, <?php echo e((int) $f->id); ?>); dragFieldId = null; dropTargetId = null; }"
                        >
                            <div class="flex items-start gap-3">
                                <div class="canvas-drag-handle w-5 h-5 mt-0.5 -ml-1" title="<?php echo e(__('forms_builder.drag_to_reorder')); ?>">
                                    <iconify-icon icon="solar:widget-6-bold" width="12"></iconify-icon>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-1 h-5 rounded-full shrink-0 transition-colors <?php echo e($isActive ? 'bg-[var(--accent)]' : 'bg-slate-200'); ?>"></span>
                                        <h3 class="text-base font-bold text-slate-800 truncate"><?php echo e($label); ?></h3>
                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($help !== ''): ?>
                                        <p class="text-[13px] text-slate-400 mt-2 ml-[18px] leading-relaxed"><?php echo e($help); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click.stop="deleteFormField(<?php echo e((int) $f->id); ?>)"
                                            class="p-1.5 rounded-xl text-slate-300 hover:text-red-500 hover:bg-red-50/80 transition-colors"
                                            aria-label="<?php echo e(__('forms_builder.delete')); ?>">
                                        <iconify-icon icon="solar:trash-bin-trash-linear" width="14"></iconify-icon>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        
                        <div
                            draggable="true"
                            wire:click="selectField(<?php echo e((int) $f->id); ?>)"
                            class="<?php echo e($layoutClass); ?> canvas-card group px-5 py-5 sm:px-6 sm:py-6 <?php echo e($isActive ? 'is-active' : ''); ?>"
                            :class="{ 'is-drop-target': dropTargetId === <?php echo e((int) $f->id); ?> }"
                            @dragstart="dragFieldId = <?php echo e((int) $f->id); ?>"
                            @dragend="dragFieldId = null; dropTargetId = null"
                            @dragover.prevent="if (dragFieldId && dragFieldId !== <?php echo e((int) $f->id); ?>) dropTargetId = <?php echo e((int) $f->id); ?>"
                            @dragleave="if (dropTargetId === <?php echo e((int) $f->id); ?>) dropTargetId = null"
                            @drop.prevent="if (dragFieldId && dragFieldId !== <?php echo e((int) $f->id); ?>) { $wire.reorderFormField(dragFieldId, <?php echo e((int) $f->id); ?>); dragFieldId = null; dropTargetId = null; }"
                        >
                            
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <div class="canvas-drag-handle w-5 h-5" title="<?php echo e(__('forms_builder.drag_to_reorder')); ?>">
                                        <iconify-icon icon="solar:widget-6-bold" width="12"></iconify-icon>
                                    </div>
                                    <span class="text-[10px] font-semibold text-slate-300 uppercase tracking-widest">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php switch($type):
                                            case ('text'): ?> Texte <?php break; ?>
                                            <?php case ('textarea'): ?> Zone texte <?php break; ?>
                                            <?php case ('number'): ?> Nombre <?php break; ?>
                                            <?php case ('select'): ?> Liste <?php break; ?>
                                            <?php case ('radio'): ?> Choix unique <?php break; ?>
                                            <?php case ('checkbox'): ?> Cases <?php break; ?>
                                            <?php case ('date'): ?> Date <?php break; ?>
                                            <?php case ('datetime'): ?> Date/Heure <?php break; ?>
                                            <?php case ('file'): ?> Fichier <?php break; ?>
                                            <?php default: ?> <?php echo e($type); ?> <?php break; ?>
                                        <?php endswitch; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </span>
                                </div>
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click.stop="deleteFormField(<?php echo e((int) $f->id); ?>)"
                                            class="p-1.5 rounded-xl text-slate-300 hover:text-red-500 hover:bg-red-50/80 transition-colors"
                                            aria-label="<?php echo e(__('forms_builder.delete')); ?>">
                                        <iconify-icon icon="solar:trash-bin-trash-linear" width="13"></iconify-icon>
                                    </button>
                                </div>
                            </div>

                            
                            <label class="block text-[14px] font-semibold text-slate-700 mb-3">
                                <?php echo e($label); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isRequired): ?>
                                    <span class="text-red-300 text-xs font-normal ml-0.5">*</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </label>

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'select'): ?>
                                <div class="canvas-input flex justify-between items-center pointer-events-none">
                                    <span><?php echo e($placeholder !== '' ? $placeholder : __('forms_builder.select_option')); ?></span>
                                    <iconify-icon icon="solar:alt-arrow-down-linear" width="16" class="text-slate-300"></iconify-icon>
                                </div>
                            <?php elseif($type === 'radio'): ?>
                                <?php $radioOpts = (array) $options; $showOpts = array_slice($radioOpts, 0, 5); ?>
                                <div class="pointer-events-none
                                    <?php echo e($displayMode === 'inline' ? 'flex flex-wrap gap-2' : ''); ?>

                                    <?php echo e($displayMode === 'grid' ? 'grid grid-cols-2 gap-2' : ''); ?>

                                    <?php echo e($displayMode === 'card' ? 'grid grid-cols-1 gap-2' : ''); ?>

                                    <?php echo e($displayMode === 'list' ? 'space-y-2' : ''); ?>

                                ">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $showOpts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($displayMode === 'card'): ?>
                                            <div class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl border <?php echo e($idx === 0 ? 'border-[var(--accent)]/30 bg-[var(--accent-soft)]/8' : 'border-slate-100 bg-white'); ?> transition-all">
                                                <span class="shrink-0 w-[18px] h-[18px] rounded-full border-2 flex items-center justify-center <?php echo e($idx === 0 ? 'border-[var(--accent)]' : 'border-slate-200'); ?>">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($idx === 0): ?>
                                                        <span class="w-2.5 h-2.5 rounded-full bg-[var(--accent)]"></span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </span>
                                                <span class="text-sm font-medium <?php echo e($idx === 0 ? 'text-slate-800' : 'text-slate-400'); ?>"><?php echo e($opt); ?></span>
                                            </div>
                                        <?php elseif($displayMode === 'inline'): ?>
                                            <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border <?php echo e($idx === 0 ? 'border-[var(--accent)]/30 bg-[var(--accent-soft)]/8' : 'border-slate-100 bg-white'); ?> transition-all">
                                                <span class="shrink-0 w-[14px] h-[14px] rounded-full border-2 flex items-center justify-center <?php echo e($idx === 0 ? 'border-[var(--accent)]' : 'border-slate-200'); ?>">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($idx === 0): ?>
                                                        <span class="w-1.5 h-1.5 rounded-full bg-[var(--accent)]"></span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </span>
                                                <span class="text-[13px] font-medium <?php echo e($idx === 0 ? 'text-slate-800' : 'text-slate-400'); ?>"><?php echo e($opt); ?></span>
                                            </div>
                                        <?php elseif($displayMode === 'grid'): ?>
                                            <div class="flex items-center gap-3 px-4 py-3 rounded-xl border <?php echo e($idx === 0 ? 'border-[var(--accent)]/30 bg-[var(--accent-soft)]/8' : 'border-slate-100 bg-white'); ?> transition-all">
                                                <span class="shrink-0 w-[16px] h-[16px] rounded-full border-2 flex items-center justify-center <?php echo e($idx === 0 ? 'border-[var(--accent)]' : 'border-slate-200'); ?>">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($idx === 0): ?>
                                                        <span class="w-2 h-2 rounded-full bg-[var(--accent)]"></span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </span>
                                                <span class="text-sm font-medium <?php echo e($idx === 0 ? 'text-slate-800' : 'text-slate-400'); ?>"><?php echo e($opt); ?></span>
                                            </div>
                                        <?php else: ?>
                                            <div class="flex items-center gap-3.5 px-4 py-3 rounded-xl border <?php echo e($idx === 0 ? 'border-[var(--accent)]/30 bg-[var(--accent-soft)]/6' : 'border-slate-100 bg-white'); ?> transition-all">
                                                <span class="shrink-0 w-[16px] h-[16px] rounded-full border-2 flex items-center justify-center <?php echo e($idx === 0 ? 'border-[var(--accent)]' : 'border-slate-200'); ?>">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($idx === 0): ?>
                                                        <span class="w-2 h-2 rounded-full bg-[var(--accent)]"></span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </span>
                                                <span class="text-sm font-medium <?php echo e($idx === 0 ? 'text-slate-800' : 'text-slate-400'); ?>"><?php echo e($opt); ?></span>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($radioOpts) > 5): ?>
                                        <span class="text-[13px] text-slate-300 pl-1">+<?php echo e(count($radioOpts) - 5); ?> <?php echo e(__('forms_builder.other_options')); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php elseif($type === 'textarea'): ?>
                                <div class="canvas-input h-24 pointer-events-none resize-none rounded-xl">
                                    <?php echo e($placeholder !== '' ? $placeholder : __('forms_builder.text_area_placeholder')); ?>

                                </div>
                            <?php elseif($type === 'checkbox'): ?>
                                <?php $cbOpts = count((array) $options) > 0 ? (array) $options : [$label]; $showCbOpts = array_slice($cbOpts, 0, 5); ?>
                                <div class="pointer-events-none
                                    <?php echo e($displayMode === 'inline' ? 'flex flex-wrap gap-2' : ''); ?>

                                    <?php echo e($displayMode === 'grid' ? 'grid grid-cols-2 gap-2' : ''); ?>

                                    <?php echo e($displayMode === 'card' ? 'grid grid-cols-1 gap-2' : ''); ?>

                                    <?php echo e($displayMode === 'list' ? 'space-y-2' : ''); ?>

                                ">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $showCbOpts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($displayMode === 'card'): ?>
                                            <div class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl border <?php echo e($idx === 0 ? 'border-[var(--accent)]/30 bg-[var(--accent-soft)]/8' : 'border-slate-100 bg-white'); ?> transition-all">
                                                <span class="shrink-0 w-[18px] h-[18px] rounded-md flex items-center justify-center border-2 <?php echo e($idx === 0 ? 'border-[var(--accent)] bg-[var(--accent)]' : 'border-slate-200 bg-white'); ?>">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($idx === 0): ?>
                                                        <iconify-icon icon="solar:check-read-linear" width="12" class="text-white"></iconify-icon>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </span>
                                                <span class="text-sm font-medium <?php echo e($idx === 0 ? 'text-slate-800' : 'text-slate-400'); ?>"><?php echo e($opt); ?></span>
                                            </div>
                                        <?php elseif($displayMode === 'inline'): ?>
                                            <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border <?php echo e($idx === 0 ? 'border-[var(--accent)]/30 bg-[var(--accent-soft)]/8' : 'border-slate-100 bg-white'); ?> transition-all">
                                                <span class="shrink-0 w-[14px] h-[14px] rounded-sm flex items-center justify-center border-2 <?php echo e($idx === 0 ? 'border-[var(--accent)] bg-[var(--accent)]' : 'border-slate-200 bg-white'); ?>">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($idx === 0): ?>
                                                        <iconify-icon icon="solar:check-read-linear" width="10" class="text-white"></iconify-icon>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </span>
                                                <span class="text-[13px] font-medium <?php echo e($idx === 0 ? 'text-slate-800' : 'text-slate-400'); ?>"><?php echo e($opt); ?></span>
                                            </div>
                                        <?php elseif($displayMode === 'grid'): ?>
                                            <div class="flex items-center gap-3 px-4 py-3 rounded-xl border <?php echo e($idx === 0 ? 'border-[var(--accent)]/30 bg-[var(--accent-soft)]/8' : 'border-slate-100 bg-white'); ?> transition-all">
                                                <span class="shrink-0 w-[16px] h-[16px] rounded-sm flex items-center justify-center border-2 <?php echo e($idx === 0 ? 'border-[var(--accent)] bg-[var(--accent)]' : 'border-slate-200 bg-white'); ?>">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($idx === 0): ?>
                                                        <iconify-icon icon="solar:check-read-linear" width="11" class="text-white"></iconify-icon>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </span>
                                                <span class="text-sm font-medium <?php echo e($idx === 0 ? 'text-slate-800' : 'text-slate-400'); ?>"><?php echo e($opt); ?></span>
                                            </div>
                                        <?php else: ?>
                                            <div class="flex items-center gap-3.5 px-4 py-3 rounded-xl border <?php echo e($idx === 0 ? 'border-[var(--accent)]/30 bg-[var(--accent-soft)]/6' : 'border-slate-100 bg-white'); ?> transition-all">
                                                <span class="shrink-0 w-[16px] h-[16px] rounded-sm flex items-center justify-center border-2 <?php echo e($idx === 0 ? 'border-[var(--accent)] bg-[var(--accent)]' : 'border-slate-200 bg-white'); ?>">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($idx === 0): ?>
                                                        <iconify-icon icon="solar:check-read-linear" width="11" class="text-white"></iconify-icon>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </span>
                                                <span class="text-sm font-medium <?php echo e($idx === 0 ? 'text-slate-800' : 'text-slate-400'); ?>"><?php echo e($opt); ?></span>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($cbOpts) > 5): ?>
                                        <span class="text-[13px] text-slate-300 pl-1">+<?php echo e(count($cbOpts) - 5); ?> <?php echo e(__('forms_builder.other_options')); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php elseif($type === 'file'): ?>
                                <div class="rounded-2xl border-2 border-dashed border-slate-100 bg-white/60 p-7 text-center pointer-events-none">
                                    <iconify-icon icon="solar:cloud-upload-linear" width="28" class="text-slate-200"></iconify-icon>
                                    <p class="text-[13px] text-slate-300 mt-2.5 font-medium"><?php echo e(__('forms_builder.drag_or_click')); ?></p>
                                </div>
                            <?php elseif($type === 'date'): ?>
                                <div class="canvas-input flex items-center justify-between pointer-events-none">
                                    <span><?php echo e(__('forms_builder.date_placeholder')); ?></span>
                                    <iconify-icon icon="solar:calendar-linear" width="17" class="text-slate-300"></iconify-icon>
                                </div>
                            <?php elseif($type === 'datetime'): ?>
                                <div class="canvas-input flex items-center justify-between pointer-events-none">
                                    <span><?php echo e(__('forms_builder.datetime_placeholder')); ?></span>
                                    <iconify-icon icon="solar:calendar-date-linear" width="17" class="text-slate-300"></iconify-icon>
                                </div>
                            <?php elseif($type === 'number'): ?>
                                <div class="canvas-input pointer-events-none">
                                    <?php echo e($placeholder !== '' ? $placeholder : '0'); ?>

                                </div>
                            <?php else: ?>
                                <div class="canvas-input pointer-events-none">
                                    <?php echo e($placeholder !== '' ? $placeholder : __('forms_builder.short_text_placeholder')); ?>

                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($help !== ''): ?>
                                <p class="mt-3 text-[13px] text-slate-300 leading-relaxed"><?php echo e($help); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_4): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($allFields->isEmpty()): ?>
                <div class="flex flex-col items-center justify-center text-center py-24 sm:py-32 px-8 rounded-2xl border-2 border-dashed border-slate-100 bg-white/50">
                    <div class="w-16 h-16 rounded-2xl bg-white flex items-center justify-center mb-6 shadow-sm border border-slate-50">
                        <iconify-icon icon="solar:clipboard-add-linear" class="text-slate-200" width="32"></iconify-icon>
                    </div>
                    <h3 class="text-base font-bold text-slate-700"><?php echo e(__('forms_builder.form_empty')); ?></h3>
                    <p class="text-sm text-slate-400 mt-2 max-w-sm leading-relaxed"><?php echo e(__('forms_builder.form_empty_hint')); ?></p>
                    <p class="text-[13px] text-slate-300 mt-6"><?php echo e(__('forms_builder.click_to_add')); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php else: ?>
            
            <div class="flex flex-col items-center justify-center text-center py-28 sm:py-36 px-8">
                <div class="w-20 h-20 rounded-3xl bg-white flex items-center justify-center mb-7 shadow-sm border border-slate-50">
                    <iconify-icon icon="solar:document-add-bold-duotone" class="text-slate-200" width="40"></iconify-icon>
                </div>
                <h3 class="text-lg font-bold text-slate-700"><?php echo e(__('forms_builder.no_form_selected')); ?></h3>
                <p class="text-sm text-slate-400 mt-2 max-w-sm leading-relaxed"><?php echo e(__('forms_builder.no_form_selected_hint')); ?></p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\admin\form-builder-canvas.blade.php ENDPATH**/ ?>