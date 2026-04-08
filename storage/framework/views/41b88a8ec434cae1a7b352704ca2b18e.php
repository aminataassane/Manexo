<div class="space-y-0">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fb_selected_field_id): ?>
        <!-- FIELD PROPERTIES -->
        <div x-data="{ activeTab: 'field' }">
            
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center">
                        <iconify-icon icon="solar:settings-linear" width="15" class="text-slate-300"></iconify-icon>
                    </div>
                    <div>
                        <h3 class="text-[13px] font-bold text-slate-900"><?php echo e(__('forms_builder.field_settings')); ?></h3>
                        <span class="text-[10px] font-mono text-slate-300 tracking-wide"><?php echo e($fb_selected_field_type); ?></span>
                    </div>
                </div>
                <button type="button" wire:click="$set('fb_selected_field_id', null)"
                        class="p-1.5 rounded-xl text-slate-300 hover:text-slate-600 hover:bg-slate-50 transition-colors">
                    <iconify-icon icon="solar:close-circle-linear" width="17"></iconify-icon>
                </button>
            </div>

            
            <div class="flex gap-0 mb-6 bg-slate-50/80 rounded-xl p-1">
                <button type="button" @click="activeTab = 'field'"
                        class="flex-1 py-2 text-[11px] font-semibold rounded-lg transition-all text-center"
                        :class="activeTab === 'field' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-400 hover:text-slate-600'">
                    <?php echo e(__('forms_builder.tab_field')); ?>

                </button>
                <button type="button" @click="activeTab = 'appearance'"
                        class="flex-1 py-2 text-[11px] font-semibold rounded-lg transition-all text-center"
                        :class="activeTab === 'appearance' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-400 hover:text-slate-600'">
                    <?php echo e(__('forms_builder.tab_appearance')); ?>

                </button>
            </div>

            
            <div x-show="activeTab === 'field'" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="space-y-5">
                    <div class="space-y-2">
                        <label for="fb_selected_field_key" class="text-[11px] font-semibold text-slate-400"><?php echo e(__('forms_builder.key_name')); ?></label>
                        <input
                            type="text"
                            id="fb_selected_field_key"
                            name="fb_selected_field_key"
                            wire:model.blur="fb_selected_field_key"
                            <?php if(! $canManageForms): echo 'disabled'; endif; ?>
                            class="input-builder font-mono text-xs"
                            placeholder="<?php echo e(__('forms_builder.key_placeholder')); ?>"
                        >
                        <div class="text-[10px] text-slate-300 leading-relaxed"><?php echo e(__('forms_builder.key_help')); ?></div>
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('fb_selected_field_key')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('fb_selected_field_key'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                    </div>

                    <div class="space-y-2">
                        <label for="fb_selected_field_label" class="text-[11px] font-semibold text-slate-400"><?php echo e(__('forms_builder.label')); ?></label>
                        <input type="text" id="fb_selected_field_label" name="fb_selected_field_label" wire:model.blur="fb_selected_field_label" <?php if(! $canManageForms): echo 'disabled'; endif; ?>
                               class="input-builder text-xs">
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('fb_selected_field_label')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('fb_selected_field_label'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! in_array($fb_selected_field_type, ['section', 'checkbox'])): ?>
                    <div class="space-y-2">
                        <label for="fb_selected_field_placeholder" class="text-[11px] font-semibold text-slate-400"><?php echo e(__('forms_builder.placeholder_optional')); ?></label>
                        <input
                            type="text"
                            id="fb_selected_field_placeholder"
                            name="fb_selected_field_placeholder"
                            wire:model.blur="fb_selected_field_placeholder"
                            <?php if(! $canManageForms): echo 'disabled'; endif; ?>
                            class="input-builder text-xs"
                            placeholder="<?php echo e(__('forms_builder.placeholder_example')); ?>"
                        >
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('fb_selected_field_placeholder')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('fb_selected_field_placeholder'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="space-y-2">
                        <label for="fb_selected_field_help_text" class="text-[11px] font-semibold text-slate-400"><?php echo e($fb_selected_field_type === 'section' ? __('forms_builder.section_description') : __('forms_builder.help_text_optional')); ?></label>
                        <textarea
                            id="fb_selected_field_help_text"
                            name="fb_selected_field_help_text"
                            wire:model.blur="fb_selected_field_help_text"
                            rows="2"
                            <?php if(! $canManageForms): echo 'disabled'; endif; ?>
                            class="input-builder text-xs"
                            placeholder="<?php echo e(__('forms_builder.help_text_example')); ?>"
                        ></textarea>
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('fb_selected_field_help_text')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('fb_selected_field_help_text'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($fb_selected_field_type, ['select', 'radio', 'checkbox'])): ?>
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between">
                                <label class="text-[11px] font-semibold text-slate-400"><?php echo e(__('forms_builder.options_list')); ?></label>
                                <button type="button" wire:click="addOption" <?php if(! $canManageForms): echo 'disabled'; endif; ?>
                                        class="text-[11px] font-semibold text-[var(--accent)] hover:opacity-80 disabled:opacity-50 flex items-center gap-1 transition-colors">
                                    <iconify-icon icon="solar:add-circle-bold" width="14"></iconify-icon>
                                    <?php echo e(__('forms_builder.add_option')); ?>

                                </button>
                            </div>
                            <div class="space-y-2 max-h-48 overflow-y-auto custom-scrollbar pr-1">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $fb_selected_field_options_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $optionValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <div class="flex items-center gap-2 group" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processElementKey('option-row-{{ $idx }}', get_defined_vars()); ?>wire:key="option-row-<?php echo e($idx); ?>">
                                        <input type="text"
                                               id="fb_selected_field_option_<?php echo e($idx); ?>"
                                               name="fb_selected_field_option_<?php echo e($idx); ?>"
                                               wire:model.blur="fb_selected_field_options_list.<?php echo e($idx); ?>"
                                               <?php if(! $canManageForms): echo 'disabled'; endif; ?>
                                               class="input-builder flex-1 py-2 text-xs"
                                               placeholder="<?php echo e(__('forms_builder.option_label', ['num' => $idx + 1])); ?>">
                                        <button type="button" wire:click="removeOption(<?php echo e($idx); ?>)" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processElementKey('opt-remove-{{ $idx }}', get_defined_vars()); ?>wire:key="opt-remove-<?php echo e($idx); ?>"
                                                <?php if(! $canManageForms): echo 'disabled'; endif; ?>
                                                class="shrink-0 p-1.5 rounded-lg text-slate-300 hover:text-red-500 hover:bg-red-50 transition-colors disabled:opacity-50"
                                                aria-label="<?php echo e(__('forms_builder.remove_option')); ?>">
                                            <iconify-icon icon="solar:trash-bin-trash-linear" width="14"></iconify-icon>
                                        </button>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <p class="text-[11px] text-slate-300 py-3"><?php echo e(__('forms_builder.no_options_yet')); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('fb_selected_field_options_list')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('fb_selected_field_options_list'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fb_selected_field_type !== 'section'): ?>
                    <div class="flex items-center justify-between py-3.5 px-4 rounded-xl bg-slate-50/60">
                        <span class="text-xs font-medium text-slate-600"><?php echo e(__('forms_builder.required')); ?></span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="fb_selected_field_required" name="fb_selected_field_required" wire:model="fb_selected_field_required" class="sr-only peer" <?php if(! $canManageForms): echo 'disabled'; endif; ?>>
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[var(--accent)]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[var(--accent)]"></div>
                        </label>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div x-show="activeTab === 'appearance'" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="space-y-6">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fb_selected_field_type !== 'section'): ?>
                    
                    <div class="space-y-3">
                        <label class="text-[11px] font-semibold text-slate-400"><?php echo e(__('forms_builder.layout')); ?></label>
                        <div class="grid grid-cols-3 gap-2.5">
                            
                            <button type="button" wire:click="$set('fb_selected_field_layout', 'full')"
                                    class="layout-btn <?php echo e($fb_selected_field_layout === 'full' ? 'active' : ''); ?>">
                                <div class="w-full h-2.5 rounded-md <?php echo e($fb_selected_field_layout === 'full' ? 'bg-[var(--accent)]' : 'bg-slate-200'); ?>"></div>
                                <span class="text-[10px]"><?php echo e(__('forms_builder.layout_full')); ?></span>
                            </button>
                            
                            <button type="button" wire:click="$set('fb_selected_field_layout', 'half')"
                                    class="layout-btn <?php echo e($fb_selected_field_layout === 'half' ? 'active' : ''); ?>">
                                <div class="w-full flex gap-1.5">
                                    <div class="flex-1 h-2.5 rounded-md <?php echo e($fb_selected_field_layout === 'half' ? 'bg-[var(--accent)]' : 'bg-slate-200'); ?>"></div>
                                    <div class="flex-1 h-2.5 rounded-md <?php echo e($fb_selected_field_layout === 'half' ? 'bg-[var(--accent)]/40' : 'bg-slate-100'); ?>"></div>
                                </div>
                                <span class="text-[10px]"><?php echo e(__('forms_builder.layout_half')); ?></span>
                            </button>
                            
                            <button type="button" wire:click="$set('fb_selected_field_layout', 'third')"
                                    class="layout-btn <?php echo e($fb_selected_field_layout === 'third' ? 'active' : ''); ?>">
                                <div class="w-full flex gap-1">
                                    <div class="flex-1 h-2.5 rounded-md <?php echo e($fb_selected_field_layout === 'third' ? 'bg-[var(--accent)]' : 'bg-slate-200'); ?>"></div>
                                    <div class="flex-1 h-2.5 rounded-md <?php echo e($fb_selected_field_layout === 'third' ? 'bg-[var(--accent)]/40' : 'bg-slate-150'); ?>"></div>
                                    <div class="flex-1 h-2.5 rounded-md <?php echo e($fb_selected_field_layout === 'third' ? 'bg-[var(--accent)]/20' : 'bg-slate-100'); ?>"></div>
                                </div>
                                <span class="text-[10px]"><?php echo e(__('forms_builder.layout_third')); ?></span>
                            </button>
                        </div>
                        <p class="text-[10px] text-slate-300 leading-relaxed"><?php echo e(__('forms_builder.layout_help')); ?></p>
                    </div>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($fb_selected_field_type, ['radio', 'checkbox'])): ?>
                    <div class="space-y-3">
                        <label class="text-[11px] font-semibold text-slate-400"><?php echo e(__('forms_builder.display_mode')); ?></label>
                        <div class="grid grid-cols-2 gap-2.5">
                            
                            <button type="button" wire:click="$set('fb_selected_field_display_mode', 'list')"
                                    class="layout-btn <?php echo e($fb_selected_field_display_mode === 'list' ? 'active' : ''); ?>">
                                <div class="w-full space-y-1.5">
                                    <div class="h-1.5 rounded-md w-full <?php echo e($fb_selected_field_display_mode === 'list' ? 'bg-[var(--accent)]' : 'bg-slate-200'); ?>"></div>
                                    <div class="h-1.5 rounded-md w-full <?php echo e($fb_selected_field_display_mode === 'list' ? 'bg-[var(--accent)]/50' : 'bg-slate-150'); ?>"></div>
                                    <div class="h-1.5 rounded-md w-3/4 <?php echo e($fb_selected_field_display_mode === 'list' ? 'bg-[var(--accent)]/30' : 'bg-slate-100'); ?>"></div>
                                </div>
                                <span class="text-[10px]"><?php echo e(__('forms_builder.display_mode_list')); ?></span>
                            </button>
                            
                            <button type="button" wire:click="$set('fb_selected_field_display_mode', 'inline')"
                                    class="layout-btn <?php echo e($fb_selected_field_display_mode === 'inline' ? 'active' : ''); ?>">
                                <div class="w-full flex gap-1.5">
                                    <div class="h-1.5 rounded-md flex-1 <?php echo e($fb_selected_field_display_mode === 'inline' ? 'bg-[var(--accent)]' : 'bg-slate-200'); ?>"></div>
                                    <div class="h-1.5 rounded-md flex-1 <?php echo e($fb_selected_field_display_mode === 'inline' ? 'bg-[var(--accent)]/50' : 'bg-slate-150'); ?>"></div>
                                    <div class="h-1.5 rounded-md flex-1 <?php echo e($fb_selected_field_display_mode === 'inline' ? 'bg-[var(--accent)]/30' : 'bg-slate-100'); ?>"></div>
                                </div>
                                <span class="text-[10px]"><?php echo e(__('forms_builder.display_mode_inline')); ?></span>
                            </button>
                            
                            <button type="button" wire:click="$set('fb_selected_field_display_mode', 'grid')"
                                    class="layout-btn <?php echo e($fb_selected_field_display_mode === 'grid' ? 'active' : ''); ?>">
                                <div class="w-full grid grid-cols-2 gap-1">
                                    <div class="h-1.5 rounded-md <?php echo e($fb_selected_field_display_mode === 'grid' ? 'bg-[var(--accent)]' : 'bg-slate-200'); ?>"></div>
                                    <div class="h-1.5 rounded-md <?php echo e($fb_selected_field_display_mode === 'grid' ? 'bg-[var(--accent)]/50' : 'bg-slate-150'); ?>"></div>
                                    <div class="h-1.5 rounded-md <?php echo e($fb_selected_field_display_mode === 'grid' ? 'bg-[var(--accent)]/30' : 'bg-slate-100'); ?>"></div>
                                    <div class="h-1.5 rounded-md <?php echo e($fb_selected_field_display_mode === 'grid' ? 'bg-[var(--accent)]/20' : 'bg-slate-100'); ?>"></div>
                                </div>
                                <span class="text-[10px]"><?php echo e(__('forms_builder.display_mode_grid')); ?></span>
                            </button>
                            
                            <button type="button" wire:click="$set('fb_selected_field_display_mode', 'card')"
                                    class="layout-btn <?php echo e($fb_selected_field_display_mode === 'card' ? 'active' : ''); ?>">
                                <div class="w-full space-y-1.5">
                                    <div class="h-3.5 rounded-lg <?php echo e($fb_selected_field_display_mode === 'card' ? 'bg-[var(--accent)]/15 border border-[var(--accent)]/30' : 'bg-slate-50 border border-slate-200'); ?>"></div>
                                    <div class="h-3.5 rounded-lg <?php echo e($fb_selected_field_display_mode === 'card' ? 'bg-[var(--accent)]/8 border border-[var(--accent)]/15' : 'bg-slate-50/50 border border-slate-150'); ?>"></div>
                                </div>
                                <span class="text-[10px]"><?php echo e(__('forms_builder.display_mode_card')); ?></span>
                            </button>
                        </div>
                        <p class="text-[10px] text-slate-300 leading-relaxed"><?php echo e(__('forms_builder.display_mode_help')); ?></p>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-10">
                            <iconify-icon icon="solar:pallete-2-linear" width="30" class="text-slate-200"></iconify-icon>
                            <p class="text-xs text-slate-300 mt-3">Les sections occupent toujours la largeur complète.</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="pt-6 mt-6 border-t border-slate-50 flex flex-col gap-2.5">
                <button type="button" wire:click="saveSelectedField" <?php if(! $canManageForms): echo 'disabled'; endif; ?>
                        class="w-full px-4 py-3 text-xs font-bold text-white rounded-xl hover:opacity-90 transition-all disabled:opacity-40 shadow-sm"
                        style="background: var(--accent);">
                    <?php echo e(__('forms_builder.apply_changes')); ?>

                </button>
                <button type="button" @click="$dispatch('confirm-action', { title: '<?php echo e(__('Supprimer')); ?>', message: '<?php echo e(__('Supprimer ce champ du formulaire ?')); ?>', confirmLabel: '<?php echo e(__('Supprimer')); ?>', variant: 'danger', onConfirm: () => $wire.deleteFormField(<?php echo e((int) $fb_selected_field_id); ?>) })" <?php if(! $canManageForms): echo 'disabled'; endif; ?>
                        class="w-full px-3 py-2.5 text-[11px] font-medium text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all disabled:opacity-40">
                    <?php echo e(__('forms_builder.delete_field')); ?>

                </button>
            </div>
        </div>
    <?php elseif($fb_selected_form_id): ?>
        <!-- FORM PROPERTIES (collapsible sections) -->
        <div x-data="{ sections: { general: true, public: false, danger: false } }">

            
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-50">
                <div class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center">
                    <iconify-icon icon="solar:document-text-linear" width="15" class="text-slate-300"></iconify-icon>
                </div>
                <h3 class="text-[13px] font-bold text-slate-900"><?php echo e(__('forms_builder.form_settings')); ?></h3>
            </div>

            <!-- GENERAL -->
            <div class="border-b border-slate-50">
                <button type="button" @click="sections.general = !sections.general"
                        class="w-full flex items-center justify-between py-3.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
                    <span class="flex items-center gap-2.5">
                        <iconify-icon icon="solar:settings-linear" width="15"></iconify-icon>
                        <?php echo e(__('forms_builder.general_section')); ?>

                    </span>
                    <iconify-icon :icon="sections.general ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear'" width="14" class="text-slate-300"></iconify-icon>
                </button>
            </div>
            <div x-show="sections.general" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="space-y-5 pt-5 pb-6">
                    <div class="space-y-2">
                        <label for="fb_selected_form_name" class="text-[11px] font-semibold text-slate-400"><?php echo e(__('forms_builder.name')); ?></label>
                        <input type="text" id="fb_selected_form_name" name="fb_selected_form_name" wire:model.blur="fb_selected_form_name" <?php if(! $canManageForms): echo 'disabled'; endif; ?>
                               class="input-builder text-xs">
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('fb_selected_form_name')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('fb_selected_form_name'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                    </div>

                    <div class="space-y-2">
                        <label for="fb_selected_form_description" class="text-[11px] font-semibold text-slate-400"><?php echo e(__('forms_builder.description_optional')); ?></label>
                        <textarea id="fb_selected_form_description" name="fb_selected_form_description" wire:model.blur="fb_selected_form_description" rows="2" <?php if(! $canManageForms): echo 'disabled'; endif; ?>
                                  class="input-builder text-xs"
                                  placeholder="<?php echo e(__('forms_builder.description_placeholder')); ?>"></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] font-semibold text-slate-400"><?php echo e(__('forms_builder.category')); ?></label>
                        <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['wire:model' => 'fb_selected_form_category_id','wire:loading.attr' => 'disabled','wire:target' => 'saveSelectedForm','disabled' => ! $canManageForms]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'fb_selected_form_category_id','wire:loading.attr' => 'disabled','wire:target' => 'saveSelectedForm','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(! $canManageForms)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <option value=""><?php echo e(__('forms_builder.all_categories')); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option>
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
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('fb_selected_form_category_id')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('fb_selected_form_category_id'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] font-semibold text-slate-400"><?php echo e(__('forms_builder.target_who')); ?></label>
                        <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['wire:model' => 'fb_selected_form_target_user_id','wire:loading.attr' => 'disabled','wire:target' => 'saveSelectedForm','disabled' => ! $canManageForms]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'fb_selected_form_target_user_id','wire:loading.attr' => 'disabled','wire:target' => 'saveSelectedForm','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(! $canManageForms)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <option value=""><?php echo e(__('forms_builder.target_team')); ?></option>
                            <optgroup label="<?php echo e(__('forms_builder.target_one_person')); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <option value="<?php echo e($m->user_id); ?>"><?php echo e($m->user?->name ?? '—'); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </optgroup>
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
                        <p class="text-[10px] text-slate-300 leading-relaxed"><?php echo e(__('forms_builder.target_help')); ?></p>
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('fb_selected_form_target_user_id')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('fb_selected_form_target_user_id'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                    </div>

                    <div class="flex items-center justify-between py-3.5 px-4 rounded-xl bg-slate-50/60">
                        <div>
                            <div class="text-xs font-medium text-slate-700"><?php echo e(__('forms_builder.creates_ticket')); ?></div>
                            <div class="text-[10px] text-slate-300 mt-0.5"><?php echo e(__('forms_builder.creates_ticket_help')); ?></div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0 ml-3">
                            <input type="checkbox" id="fb_selected_form_creates_ticket" name="fb_selected_form_creates_ticket" wire:model.defer="fb_selected_form_creates_ticket" class="sr-only peer" <?php if(! $canManageForms): echo 'disabled'; endif; ?>>
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[var(--accent)]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[var(--accent)]"></div>
                        </label>
                    </div>

                    <div class="space-y-2">
                        <label for="fb_selected_form_due_date" class="text-[11px] font-semibold text-slate-400"><?php echo e(__('forms_builder.due_date')); ?></label>
                        <input type="date" id="fb_selected_form_due_date" name="fb_selected_form_due_date" wire:model.blur="fb_selected_form_due_date" <?php if(! $canManageForms): echo 'disabled'; endif; ?>
                               class="input-builder text-xs">
                        <p class="text-[10px] text-slate-300 leading-relaxed"><?php echo e(__('forms_builder.form_due_date_help')); ?></p>
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('fb_selected_form_due_date')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('fb_selected_form_due_date'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                    </div>

                    <div class="space-y-2">
                        <label for="fb_selected_form_expires_at" class="text-[11px] font-semibold text-slate-400"><?php echo e(__('forms_builder.assign_expires_at')); ?></label>
                        <input type="datetime-local" id="fb_selected_form_expires_at" name="fb_selected_form_expires_at" wire:model.blur="fb_selected_form_expires_at" <?php if(! $canManageForms): echo 'disabled'; endif; ?>
                               class="input-builder text-xs">
                        <p class="text-[10px] text-slate-300 leading-relaxed"><?php echo e(__('forms_builder.assign_expires_at_help')); ?></p>
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('fb_selected_form_expires_at')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('fb_selected_form_expires_at'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                    </div>

                    <div class="flex items-center justify-between py-3.5 px-4 rounded-xl bg-slate-50/60">
                        <span class="text-xs font-medium text-slate-600"><?php echo e(__('forms_builder.status')); ?></span>
                        <div class="flex gap-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fb_selected_form_status === 'published'): ?>
                                <span class="px-3 py-1 rounded-full bg-emerald-50 text-[10px] font-bold text-emerald-600"><?php echo e(__('forms_builder.published')); ?></span>
                            <?php elseif($fb_selected_form_status === 'archived'): ?>
                                <span class="px-3 py-1 rounded-full bg-slate-100 text-[10px] font-bold text-slate-500"><?php echo e(__('forms_builder.archived')); ?></span>
                            <?php else: ?>
                                <span class="px-3 py-1 rounded-full bg-amber-50 text-[10px] font-bold text-amber-600"><?php echo e(__('forms_builder.draft')); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PUBLIC ACCESS -->
            <div class="border-b border-slate-50">
                <button type="button" @click="sections.public = !sections.public"
                        class="w-full flex items-center justify-between py-3.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
                    <span class="flex items-center gap-2.5">
                        <iconify-icon icon="solar:global-linear" width="15"></iconify-icon>
                        <?php echo e(__('forms_builder.public_access_section')); ?>

                    </span>
                    <iconify-icon :icon="sections.public ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear'" width="14" class="text-slate-300"></iconify-icon>
                </button>
            </div>
            <div x-show="sections.public" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="space-y-5 pt-5 pb-6">
                    <div class="flex items-center justify-between py-3.5 px-4 rounded-xl bg-slate-50/60">
                        <div>
                            <div class="text-xs font-medium text-slate-700"><?php echo e(__('forms_builder.public_link')); ?></div>
                            <div class="text-[10px] text-slate-300 mt-0.5"><?php echo e(__('forms_builder.public_link_help')); ?></div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0 ml-3">
                            <input type="checkbox" id="fb_selected_form_public" name="fb_selected_form_public" wire:model.defer="fb_selected_form_public" class="sr-only peer" <?php if(! $canManageForms): echo 'disabled'; endif; ?>>
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[var(--accent)]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[var(--accent)]"></div>
                        </label>
                    </div>

                    <div class="space-y-2">
                        <label for="fb_selected_form_slug" class="text-[11px] font-semibold text-slate-400"><?php echo e(__('forms_builder.public_slug')); ?></label>
                        <div class="flex items-center gap-2">
                        <input type="text" id="fb_selected_form_slug" name="fb_selected_form_slug" wire:model.blur="fb_selected_form_slug" <?php if(! $canManageForms): echo 'disabled'; endif; ?>
                                   placeholder="<?php echo e(__('forms_builder.slug_placeholder')); ?>"
                                   class="input-builder text-xs">
                            <button type="button" wire:click="generatePublicSlug" <?php if(! $canManageForms): echo 'disabled'; endif; ?>
                                    class="h-9 px-3 rounded-xl border border-slate-100 bg-slate-50 text-slate-600 text-[11px] font-semibold hover:bg-slate-100 transition disabled:opacity-50 shrink-0">
                                <?php echo e(__('forms_builder.generate')); ?>

                            </button>
                        </div>
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('fb_selected_form_slug')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('fb_selected_form_slug'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fb_selected_form_public && $fb_selected_form_slug): ?>
                            <div class="mt-3 rounded-xl border border-slate-50 bg-slate-50/40 px-4 py-3">
                                <div class="text-[11px] font-semibold text-slate-600 flex items-center gap-1.5">
                                    <iconify-icon icon="solar:link-linear" width="13"></iconify-icon>
                                    <?php echo e(__('forms_builder.link')); ?>

                                </div>
                                <div class="mt-2 font-mono break-all text-[10px] text-slate-400"><?php echo e(url('/f/' . $fb_selected_form_slug)); ?></div>
                            </div>
                            <a href="<?php echo e(url('/f/' . $fb_selected_form_slug)); ?>" target="_blank"
                               class="mt-2 inline-flex items-center gap-1.5 text-[11px] font-semibold text-[var(--accent)] hover:opacity-80">
                                <iconify-icon icon="solar:eye-linear" width="14"></iconify-icon>
                                <?php echo e(__('forms_builder.preview')); ?>

                            </a>
                            <?php
                                $embedUrl = url('/f/' . $fb_selected_form_slug) . '?embed=1';
                                $embedCode = '<iframe src="' . e($embedUrl) . '" width="100%" height="600" frameborder="0" title="' . e($fb_selected_form_name ?: __('Formulaire')) . '"></iframe>';
                            ?>
                            <div class="mt-4 rounded-xl border border-slate-50 bg-slate-50/30 overflow-hidden" x-data="{ copied: false, embedCode: <?php echo \Illuminate\Support\Js::from($embedCode)->toHtml() ?>, copyLabel: <?php echo \Illuminate\Support\Js::from(__('forms_builder.copy_embed'))->toHtml() ?>, copiedLabel: <?php echo \Illuminate\Support\Js::from(__('forms_builder.embed_copied'))->toHtml() ?> }">
                                <div class="px-4 py-3 border-b border-slate-50">
                                    <div class="text-[11px] font-semibold text-slate-600"><?php echo e(__('forms_builder.embed_iframe_title')); ?></div>
                                    <p class="text-[10px] text-slate-300 mt-0.5"><?php echo e(__('forms_builder.embed_iframe_help')); ?></p>
                                </div>
                                <div class="p-4 space-y-2.5">
                                    <label for="fb_embed_code" class="text-[10px] font-semibold text-slate-400"><?php echo e(__('forms_builder.embed_code')); ?></label>
                                    <textarea id="fb_embed_code" name="fb_embed_code" readonly rows="3" class="input-builder text-[10px] font-mono text-slate-400" x-ref="embedTextarea"><?php echo e($embedCode); ?></textarea>
                                    <button type="button"
                                            @click="navigator.clipboard.writeText(embedCode); copied = true; setTimeout(() => copied = false, 2000)"
                                            class="inline-flex items-center gap-1.5 rounded-xl border border-slate-100 bg-white px-3 py-2 text-[11px] font-semibold text-slate-500 hover:bg-slate-50 transition">
                                        <iconify-icon icon="solar:copy-linear" width="13"></iconify-icon>
                                        <span x-text="copied ? copiedLabel : copyLabel"></span>
                                    </button>
                                </div>
                                <div class="px-4 py-3 border-t border-slate-50">
                                    <div class="text-[10px] font-semibold text-slate-400 mb-2.5"><?php echo e(__('forms_builder.embed_preview')); ?></div>
                                    <iframe src="<?php echo e($embedUrl); ?>" class="w-full rounded-xl border border-slate-50 bg-white" height="320" title="<?php echo e($fb_selected_form_name ?: __('Formulaire')); ?>"></iframe>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$fb_selected_form_target_user_id && $fb_selected_form_slug): ?>
                            <div class="mt-3 rounded-xl border border-[var(--accent)]/10 bg-[var(--accent-soft)]/5 px-4 py-3">
                                <div class="text-[11px] font-semibold text-slate-600"><?php echo e(__('forms_builder.team_link')); ?></div>
                                <p class="mt-0.5 text-slate-300 text-[10px]"><?php echo e(__('forms_builder.team_link_help')); ?></p>
                                <div class="mt-2 font-mono break-all text-[10px] text-[var(--accent)]"><?php echo e(url('/forms/l/' . $fb_selected_form_slug)); ?></div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="space-y-2">
                        <label for="fb_selected_form_public_title" class="text-[11px] font-semibold text-slate-400"><?php echo e(__('forms_builder.public_title_optional')); ?></label>
                        <input type="text" id="fb_selected_form_public_title" name="fb_selected_form_public_title" wire:model.blur="fb_selected_form_public_title" <?php if(! $canManageForms): echo 'disabled'; endif; ?>
                               class="input-builder text-xs">
                    </div>

                    <div class="space-y-2">
                        <label for="fb_selected_form_public_description" class="text-[11px] font-semibold text-slate-400"><?php echo e(__('forms_builder.public_description_optional')); ?></label>
                        <textarea id="fb_selected_form_public_description" name="fb_selected_form_public_description" wire:model.blur="fb_selected_form_public_description" rows="3" <?php if(! $canManageForms): echo 'disabled'; endif; ?>
                                  class="input-builder text-xs"></textarea>
                    </div>

                    <div class="space-y-2">
                        <label for="fb_selected_form_public_thank_you" class="text-[11px] font-semibold text-slate-400"><?php echo e(__('forms_builder.thank_you_optional')); ?></label>
                        <textarea id="fb_selected_form_public_thank_you" name="fb_selected_form_public_thank_you" wire:model.blur="fb_selected_form_public_thank_you" rows="3" <?php if(! $canManageForms): echo 'disabled'; endif; ?>
                                  class="input-builder text-xs"
                                  placeholder="<?php echo e(__('forms_builder.thank_you_placeholder')); ?>"></textarea>
                    </div>
                </div>
            </div>

            <!-- DANGER ZONE -->
            <div class="border-b border-slate-50">
                <button type="button" @click="sections.danger = !sections.danger"
                        class="w-full flex items-center justify-between py-3.5 text-xs font-semibold text-red-300 hover:text-red-500 transition-colors">
                    <span class="flex items-center gap-2.5">
                        <iconify-icon icon="solar:danger-triangle-linear" width="15"></iconify-icon>
                        <?php echo e(__('forms_builder.danger_zone')); ?>

                    </span>
                    <iconify-icon :icon="sections.danger ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear'" width="14" class="text-red-200"></iconify-icon>
                </button>
            </div>
            <div x-show="sections.danger" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="space-y-2.5 pt-5 pb-6">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fb_selected_form_status !== 'archived'): ?>
                        <button type="button" @click="$dispatch('confirm-action', { title: '<?php echo e(__('Archiver')); ?>', message: '<?php echo e(__('Archiver ce formulaire ? Il ne sera plus accessible.')); ?>', confirmLabel: '<?php echo e(__('Archiver')); ?>', variant: 'warning', onConfirm: () => $wire.archiveForm() })" <?php if(! $canManageForms): echo 'disabled'; endif; ?>
                                class="w-full px-4 py-3 text-[11px] font-semibold text-slate-500 bg-slate-50/60 border border-slate-100 rounded-xl hover:bg-slate-100 transition-all disabled:opacity-50">
                            <?php echo e(__('forms_builder.archive')); ?>

                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <button type="button" @click="$dispatch('confirm-action', { title: 'Supprimer', message: '<?php echo e(__('forms_builder.delete_form_confirm')); ?>', confirmLabel: 'Supprimer', variant: 'danger', onConfirm: () => $wire.deleteForm(<?php echo e((int) $fb_selected_form_id); ?>) })" <?php if(! $canManageForms): echo 'disabled'; endif; ?>
                            class="w-full px-4 py-3 text-[11px] font-semibold text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all disabled:opacity-50">
                        <?php echo e(__('forms_builder.delete_form')); ?>

                    </button>
                </div>
            </div>

        </div>
    <?php else: ?>
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center mb-5">
                <iconify-icon icon="solar:cursor-square-linear" class="text-slate-200" width="26"></iconify-icon>
            </div>
            <p class="text-[13px] font-semibold text-slate-500"><?php echo e(__('forms_builder.nothing_selected')); ?></p>
            <p class="text-[11px] text-slate-300 mt-2 max-w-[200px] leading-relaxed"><?php echo e(__('forms_builder.nothing_selected_hint')); ?></p>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\admin\form-builder-properties.blade.php ENDPATH**/ ?>