<?php
    $fieldGroups = [
        [
            'label' => __('forms_builder.group_text'),
            'icon' => 'solar:text-bold',
            'types' => [
                ['type' => 'text', 'label' => __('forms_builder.field_short_text'), 'icon' => 'solar:text-field-linear'],
                ['type' => 'textarea', 'label' => __('forms_builder.field_paragraph'), 'icon' => 'solar:text-square-linear'],
                ['type' => 'email', 'label' => __('forms_builder.field_email'), 'icon' => 'solar:letter-linear'],
            ],
        ],
        [
            'label' => __('forms_builder.group_choice'),
            'icon' => 'solar:list-check-bold',
            'types' => [
                ['type' => 'select', 'label' => __('forms_builder.field_dropdown'), 'icon' => 'solar:list-arrow-down-linear'],
                ['type' => 'radio', 'label' => __('forms_builder.field_single_choice'), 'icon' => 'solar:record-circle-linear'],
                ['type' => 'checkbox', 'label' => __('forms_builder.field_checkbox'), 'icon' => 'solar:check-square-linear'],
            ],
        ],
        [
            'label' => __('forms_builder.group_date_number'),
            'icon' => 'solar:calendar-bold',
            'types' => [
                ['type' => 'date', 'label' => __('forms_builder.field_date'), 'icon' => 'solar:calendar-linear'],
                ['type' => 'datetime', 'label' => __('forms_builder.field_datetime'), 'icon' => 'solar:calendar-date-linear'],
                ['type' => 'number', 'label' => __('forms_builder.field_number'), 'icon' => 'solar:ruler-linear'],
            ],
        ],
        [
            'label' => __('forms_builder.group_other'),
            'icon' => 'solar:widget-2-bold',
            'types' => [
                ['type' => 'file', 'label' => __('forms_builder.field_file'), 'icon' => 'solar:upload-linear'],
                ['type' => 'section', 'label' => __('forms_builder.field_section'), 'icon' => 'solar:minus-circle-linear'],
            ],
        ],
    ];
    $paletteDrawerEmbed = $paletteDrawerEmbed ?? false;
?>


<div class="<?php echo e($paletteDrawerEmbed ? 'flex min-h-0 h-full flex-col' : 'hidden h-full flex-col lg:flex'); ?>" x-data="{ paletteSearch: '' }">
    
    <div class="px-4 pt-4 pb-3 min-[1100px]:px-5 min-[1100px]:pt-5 min-[1100px]:pb-4">
        <div class="relative">
            <label for="palette_search" class="sr-only"><?php echo e(__('forms_builder.search_fields')); ?></label>
            <iconify-icon icon="solar:magnifer-linear" width="15" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300"></iconify-icon>
            <input type="text" id="palette_search" name="palette_search" x-model="paletteSearch"
                   placeholder="<?php echo e(__('forms_builder.search_fields')); ?>"
                   class="input-builder w-full text-xs py-2.5 pl-10 pr-3 bg-slate-50/60">
        </div>
    </div>

    
    <div class="flex-1 overflow-y-auto custom-scrollbar px-3 min-[1100px]:px-4 pb-5 space-y-5">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $fieldGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <div x-show="!paletteSearch || <?php echo e(json_encode(collect($group['types'])->pluck('label')->join(' '))); ?>.toLowerCase().includes(paletteSearch.toLowerCase())">
                <h4 class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-300 mb-2 px-2">
                    <?php echo e($group['label']); ?>

                </h4>
                <div class="space-y-0.5">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $group['types']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ft): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <button
                            type="button"
                            wire:click="quickAddField('<?php echo e($ft['type']); ?>')"
                            <?php if(! $canManageForms || ! $fb_selected_form_id): echo 'disabled'; endif; ?>
                            x-show="!paletteSearch || '<?php echo e(strtolower($ft['label'])); ?>'.includes(paletteSearch.toLowerCase())"
                            class="w-full flex items-center gap-3 px-2.5 py-2.5 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all text-[13px] font-medium disabled:opacity-30 disabled:cursor-not-allowed group"
                        >
                            <div class="w-8 h-8 rounded-lg bg-slate-50 group-hover:bg-[var(--accent-soft)] flex items-center justify-center transition-colors shrink-0">
                                <iconify-icon icon="<?php echo e($ft['icon']); ?>" width="16" class="text-slate-300 group-hover:text-[var(--accent)] transition-colors"></iconify-icon>
                            </div>
                            <span><?php echo e($ft['label']); ?></span>
                        </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $paletteDrawerEmbed): ?>

<div class="lg:hidden" x-data="{ paletteOpen: false, mobileSearch: '' }">
    
    <button type="button"
            @click="paletteOpen = true"
            <?php if(! $canManageForms || ! $fb_selected_form_id): echo 'disabled'; endif; ?>
            x-show="!paletteOpen"
            x-cloak
            class="fixed z-[38] flex h-12 w-12 items-center justify-center rounded-2xl text-white shadow-xl shadow-black/20 ring-1 ring-white/20 transition-all hover:scale-105 active:scale-95 disabled:cursor-not-allowed disabled:opacity-40"
            style="background: var(--accent); right: max(0.75rem, env(safe-area-inset-right, 0px)); bottom: max(1rem, env(safe-area-inset-bottom, 0px));">
        <iconify-icon icon="solar:add-circle-bold" width="22"></iconify-icon>
    </button>

    
    <div x-show="paletteOpen" x-cloak class="fixed inset-0 z-[90]" style="display:none;">
        <div
            class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
            x-show="paletteOpen"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="paletteOpen = false"
        ></div>
        <div class="absolute bottom-0 left-0 right-0 flex max-h-[min(78vh,calc(100dvh-8rem))] flex-col overflow-hidden rounded-t-3xl border-t border-slate-100 bg-white shadow-2xl"
             x-show="paletteOpen"
             x-transition:enter="transform transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-300"
             x-transition:enter-start="translate-y-full opacity-95"
             x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="translate-y-0 opacity-100"
             x-transition:leave-end="translate-y-full opacity-100"
             @click.stop
        >
            
            <div class="sticky top-0 z-10 bg-white border-b border-slate-50">
                <div class="flex justify-center pt-2.5 pb-2">
                    <div class="w-10 h-1 rounded-full bg-slate-200"></div>
                </div>
                <div class="px-4 pb-3">
                    <div class="relative">
                        <label for="palette_mobile_search" class="sr-only"><?php echo e(__('forms_builder.search_fields')); ?></label>
                        <iconify-icon icon="solar:magnifer-linear" width="15" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300"></iconify-icon>
                        <input type="text" id="palette_mobile_search" name="palette_mobile_search" x-model="mobileSearch"
                               placeholder="<?php echo e(__('forms_builder.search_fields')); ?>"
                               class="input-builder w-full text-sm py-2.5 pl-10 pr-3 bg-slate-50/60">
                    </div>
                </div>
            </div>
            
            <div class="flex-1 space-y-5 overflow-y-auto overscroll-y-contain bg-slate-50/30 px-4 pb-[max(1.5rem,env(safe-area-inset-bottom))] pt-4 custom-scrollbar">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $fieldGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <div x-show="!mobileSearch || <?php echo e(json_encode(collect($group['types'])->pluck('label')->join(' '))); ?>.toLowerCase().includes(mobileSearch.toLowerCase())">
                        <h4 class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-400 mb-2.5 px-1">
                            <?php echo e($group['label']); ?>

                        </h4>
                        <div class="grid grid-cols-2 gap-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $group['types']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ft): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <button
                                    type="button"
                                    wire:click="quickAddField('<?php echo e($ft['type']); ?>')"
                                    @click="paletteOpen = false"
                                    <?php if(! $canManageForms || ! $fb_selected_form_id): echo 'disabled'; endif; ?>
                                    x-show="!mobileSearch || '<?php echo e(strtolower($ft['label'])); ?>'.includes(mobileSearch.toLowerCase())"
                                    class="group flex items-center gap-2.5 px-3 py-3 rounded-2xl bg-white border border-slate-100 text-slate-600 hover:border-slate-200 hover:text-slate-800 transition-all text-xs font-semibold disabled:opacity-30 disabled:cursor-not-allowed shadow-sm"
                                >
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-50 group-hover:bg-[var(--accent-soft)] transition-colors shrink-0">
                                        <iconify-icon icon="<?php echo e($ft['icon']); ?>" width="17" class="text-slate-400 group-hover:text-[var(--accent)] transition-colors"></iconify-icon>
                                    </span>
                                    <span><?php echo e($ft['label']); ?></span>
                                </button>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/livewire/admin/form-builder-palette.blade.php ENDPATH**/ ?>