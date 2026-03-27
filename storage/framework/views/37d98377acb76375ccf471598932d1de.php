<div
    class="builder-full-bleed flex flex-col"
    style="height: calc(100dvh - 3.5rem);"
    x-data="{
        paletteDrawer: false,
        propsDrawer: false,
        shareCopied: false,
    }"
    x-on:field-selected.window="if (window.innerWidth < 1024) { propsDrawer = true; paletteDrawer = false; }"
>
    <style>
        @media (min-width: 640px) {
            [style*='height: calc(100dvh - 3.5rem)'] { height: calc(100dvh - 4rem) !important; }
        }
    </style>

    
    <header class="shrink-0 z-30 bg-white border-b border-slate-100">
        <div class="h-14 px-3 sm:px-5 lg:px-6 flex items-center gap-3">
            
            <div class="flex items-center gap-2 min-w-0 flex-1">
                
                <button type="button" @click="paletteDrawer = !paletteDrawer; propsDrawer = false"
                        class="lg:hidden shrink-0 h-9 w-9 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors inline-flex items-center justify-center"
                        title="<?php echo e(__('forms_builder.search_fields')); ?>">
                    <iconify-icon icon="solar:widget-add-linear" width="18"></iconify-icon>
                </button>

                
                <div x-data="{ formDropdownOpen: false }" class="relative min-w-0 flex-1 max-w-md">
                    <button type="button" @click="formDropdownOpen = !formDropdownOpen"
                            class="flex items-center gap-2 min-w-0 w-full group py-1.5 px-2 rounded-xl hover:bg-slate-50 transition-colors text-left">
                        <div class="min-w-0 flex-1 flex items-center gap-2.5">
                            <span class="text-sm font-bold text-slate-900 truncate">
                                <?php echo e($fb_selected_form_name ?: __('forms_builder.select_form')); ?>

                            </span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fb_selected_form_id): ?>
                                <?php
                                    $statusBadge = match($fb_selected_form_status) {
                                        'published' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'dot' => 'bg-emerald-500', 'label' => __('forms_builder.published')],
                                        'archived' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-500', 'dot' => 'bg-slate-400', 'label' => __('forms_builder.archived')],
                                        default => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'dot' => 'bg-amber-500', 'label' => __('forms_builder.draft')],
                                    };
                                ?>
                                <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-full <?php echo e($statusBadge['bg']); ?> <?php echo e($statusBadge['text']); ?> text-[10px] font-bold shrink-0">
                                    <span class="h-1.5 w-1.5 rounded-full <?php echo e($statusBadge['dot']); ?>"></span>
                                    <?php echo e($statusBadge['label']); ?>

                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <iconify-icon icon="solar:alt-arrow-down-linear" width="14" class="shrink-0 text-slate-300 group-hover:text-slate-500 transition-colors"></iconify-icon>
                    </button>

                    
                    <div x-show="formDropdownOpen" @click.away="formDropdownOpen = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute left-0 top-full mt-1.5 w-72 bg-white rounded-2xl border border-slate-100 shadow-xl shadow-slate-200/40 z-50 overflow-hidden"
                         x-cloak>
                        <div class="p-3 border-b border-slate-50">
                            <div class="relative">
                                <input type="text" wire:model.live.debounce.150ms="fb_form_name"
                                       placeholder="<?php echo e(__('forms_builder.new_form_placeholder')); ?>"
                                       <?php if(! $canManageForms): echo 'disabled'; endif; ?>
                                       class="input-builder w-full text-xs py-2 pl-3 pr-8">
                                <button type="button" wire:click="createForm" @click="formDropdownOpen = false"
                                        <?php if(! $canManageForms || trim($fb_form_name) === ''): echo 'disabled'; endif; ?>
                                        class="absolute right-1.5 top-1/2 -translate-y-1/2 p-1 rounded text-slate-400 hover:text-[var(--accent)] disabled:opacity-40 disabled:pointer-events-none transition">
                                    <iconify-icon icon="solar:add-circle-bold" width="16"></iconify-icon>
                                </button>
                            </div>
                            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('fb_form_name')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('fb_form_name'))]); ?>
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
                        <div class="max-h-60 overflow-y-auto custom-scrollbar">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $forms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <?php
                                    $fStatus = $f->status instanceof \App\Enums\FormStatus ? $f->status->value : (string) $f->status;
                                    $isSelected = (int) $fb_selected_form_id === (int) $f->id;
                                ?>
                                <button type="button" wire:click="selectForm(<?php echo e($f->id); ?>)" @click="formDropdownOpen = false"
                                        class="w-full text-left px-3.5 py-2.5 flex items-center justify-between gap-2 text-xs transition-colors <?php echo e($isSelected ? 'bg-slate-50 text-slate-900 font-semibold' : 'hover:bg-slate-50 text-slate-600'); ?>">
                                    <span class="truncate"><?php echo e($f->name); ?></span>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fStatus === 'published'): ?>
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" title="<?php echo e(__('forms_builder.published')); ?>"></span>
                                        <?php elseif($fStatus === 'archived'): ?>
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400" title="<?php echo e(__('forms_builder.archived')); ?>"></span>
                                        <?php else: ?>
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500" title="<?php echo e(__('forms_builder.draft')); ?>"></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isSelected): ?>
                                            <iconify-icon icon="solar:check-circle-bold" width="14" class="text-[var(--accent)]"></iconify-icon>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </button>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($forms->isEmpty()): ?>
                                <div class="px-4 py-8 text-center text-xs text-slate-400">
                                    <?php echo e(__('forms_builder.no_form_selected_hint')); ?>

                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="flex items-center gap-1.5 shrink-0">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fb_selected_form_id): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fb_selected_form_slug): ?>
                        <a href="<?php echo e(url('/f/' . $fb_selected_form_slug)); ?>" target="_blank"
                           class="hidden sm:inline-flex h-8 items-center gap-1.5 rounded-lg border border-slate-100 bg-white px-2.5 text-xs font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-800 transition-all">
                            <iconify-icon icon="solar:eye-linear" width="15"></iconify-icon>
                            <span class="hidden md:inline"><?php echo e(__('forms_builder.preview')); ?></span>
                        </a>
                        <button type="button"
                                @click="navigator.clipboard.writeText('<?php echo e(url('/f/' . $fb_selected_form_slug)); ?>'); shareCopied = true; setTimeout(() => shareCopied = false, 2000)"
                                class="hidden sm:inline-flex h-8 items-center gap-1.5 rounded-lg border border-slate-100 bg-white px-2.5 text-xs font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-800 transition-all">
                            <iconify-icon icon="solar:share-linear" width="15"></iconify-icon>
                            <span class="hidden md:inline" x-text="shareCopied ? '<?php echo e(__('forms_builder.share_copied')); ?>' : '<?php echo e(__('forms_builder.share')); ?>'"></span>
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="hidden sm:block w-px h-5 bg-slate-100 mx-0.5" aria-hidden="true"></div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fb_selected_form_status !== 'published'): ?>
                        <button type="button" wire:click="publishForm" <?php if(! $canManageForms): echo 'disabled'; endif; ?>
                                class="hidden sm:inline-flex h-8 items-center gap-1.5 rounded-lg border border-emerald-100 bg-white px-2.5 text-xs font-medium text-emerald-600 hover:bg-emerald-50 transition-all disabled:opacity-50">
                            <iconify-icon icon="solar:check-circle-bold" width="15"></iconify-icon>
                            <span class="hidden md:inline"><?php echo e(__('forms_builder.publish')); ?></span>
                        </button>
                    <?php else: ?>
                        <button type="button" wire:click="unpublishForm" <?php if(! $canManageForms): echo 'disabled'; endif; ?>
                                class="hidden sm:inline-flex h-8 items-center gap-1.5 rounded-lg border border-slate-100 bg-white px-2.5 text-xs font-medium text-slate-500 hover:bg-slate-50 transition-all disabled:opacity-50">
                            <iconify-icon icon="solar:pause-circle-linear" width="15"></iconify-icon>
                            <span class="hidden md:inline"><?php echo e(__('forms_builder.draft')); ?></span>
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <button type="button" wire:click="duplicateSelectedForm" <?php if(! $canManageForms): echo 'disabled'; endif; ?>
                            class="hidden lg:inline-flex h-8 items-center gap-1.5 rounded-lg border border-slate-100 bg-white px-2.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-all disabled:opacity-50">
                        <iconify-icon icon="solar:copy-linear" width="15"></iconify-icon>
                        <?php echo e(__('forms_builder.duplicate')); ?>

                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <button type="button" @click="propsDrawer = !propsDrawer; paletteDrawer = false"
                        class="lg:hidden shrink-0 h-9 w-9 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors inline-flex items-center justify-center"
                        title="<?php echo e(__('forms_builder.properties')); ?>">
                    <iconify-icon icon="solar:settings-linear" width="18"></iconify-icon>
                </button>

                
                <button type="button" wire:click="saveSelectedForm"
                        <?php if(! $canManageForms || ! $fb_selected_form_id): echo 'disabled'; endif; ?>
                        class="h-9 px-3 sm:px-4 rounded-xl text-xs font-bold text-white shadow-sm transition-all disabled:opacity-40 hover:opacity-90 inline-flex items-center gap-1.5"
                        style="background: var(--accent);">
                    <iconify-icon icon="solar:diskette-bold" width="16"></iconify-icon>
                    <span class="hidden sm:inline"><?php echo e(__('forms_builder.save')); ?></span>
                </button>
            </div>
        </div>

        
        <?php
            $formSaveErrorKeys = ['fb_selected_form_name', 'fb_selected_form_category_id', 'fb_selected_form_target_user_id', 'fb_selected_form_slug', 'fb_selected_form_public_title', 'fb_selected_form_public_description', 'fb_selected_form_public_thank_you', 'fb_selected_form_description'];
            $viewErrors = isset($errors) ? $errors : new \Illuminate\Support\ViewErrorBag();
            $formSaveErrors = collect($formSaveErrorKeys)->flatMap(fn ($key) => $viewErrors->get($key))->filter()->values();
        ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($formSaveErrors->isNotEmpty()): ?>
            <div class="mx-4 sm:mx-5 lg:mx-6 mb-0 mt-0 py-2.5 px-3 border-b border-amber-100 bg-amber-50/40">
                <div class="flex items-start gap-2.5">
                    <iconify-icon icon="solar:danger-triangle-bold" width="16" class="text-amber-500 shrink-0 mt-0.5"></iconify-icon>
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-bold text-amber-800"><?php echo e(__('forms_builder.save_error')); ?></p>
                        <ul class="mt-0.5 list-inside list-disc text-[10px] text-amber-600 space-y-0.5">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $formSaveErrors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <li><?php echo e($msg); ?></li>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </ul>
                    </div>
                    <button type="button" @click="propsDrawer = true" class="shrink-0 rounded-lg border border-amber-200 bg-white px-2 py-1 text-[10px] font-bold text-amber-700 hover:bg-amber-50 transition-colors lg:hidden">
                        <?php echo e(__('forms_builder.open_properties')); ?>

                    </button>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fb_selected_form_id): ?>
        <nav class="px-3 sm:px-5 lg:px-6" aria-label="<?php echo e(__('forms_builder.tabs_label')); ?>">
            <div class="flex gap-0">
                <button type="button" wire:click="$set('activeTab', 'champs')"
                        class="relative px-3 py-2.5 text-xs font-semibold transition-colors <?php echo e($activeTab === 'champs' ? 'text-slate-900' : 'text-slate-400 hover:text-slate-600'); ?>">
                    <?php echo e(__('forms_builder.fields_tab')); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'champs'): ?>
                        <span class="absolute bottom-0 left-0 right-0 h-[2px] rounded-full" style="background: var(--accent);"></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </button>
                <button type="button" wire:click="$set('activeTab', 'assignations')"
                        class="relative px-3 py-2.5 text-xs font-semibold transition-colors flex items-center gap-1.5 <?php echo e($activeTab === 'assignations' ? 'text-slate-900' : 'text-slate-400 hover:text-slate-600'); ?>">
                    <?php echo e(__('forms_builder.assignments_tab')); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($assignments->where('status.value', 'pending')->count() > 0): ?>
                        <span class="inline-flex items-center justify-center h-4 min-w-[16px] rounded-full bg-amber-100 px-1 text-[9px] font-bold text-amber-700"><?php echo e($assignments->where('status.value', 'pending')->count()); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'assignations'): ?>
                        <span class="absolute bottom-0 left-0 right-0 h-[2px] rounded-full" style="background: var(--accent);"></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </button>
                <button type="button" wire:click="$set('activeTab', 'reponses')"
                        class="relative px-3 py-2.5 text-xs font-semibold transition-colors <?php echo e($activeTab === 'reponses' ? 'text-slate-900' : 'text-slate-400 hover:text-slate-600'); ?>">
                    <?php echo e(__('forms_builder.responses_tab')); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'reponses'): ?>
                        <span class="absolute bottom-0 left-0 right-0 h-[2px] rounded-full" style="background: var(--accent);"></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </button>
            </div>
        </nav>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </header>

    
    <div class="flex-1 flex min-h-0 overflow-hidden">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'champs'): ?>
        
        <aside class="hidden lg:flex fb-sidebar-palette bg-white border-r border-slate-100 shrink-0 flex-col overflow-hidden transition-[width] duration-200 ease-out">
            <?php echo $__env->make('livewire.admin.form-builder-palette', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </aside>

        
        <div class="flex-1 flex flex-col overflow-hidden min-w-0 bg-[#fafbfc]">
            <div class="flex-1 overflow-y-auto custom-scrollbar px-4 py-8 sm:px-8 sm:py-10 lg:px-12 lg:py-12 xl:px-16 flex justify-center">
                <div class="w-full max-w-2xl xl:max-w-3xl flex flex-col">
                    <?php echo $__env->make('livewire.admin.form-builder-canvas', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
        </div>

        
        <aside class="hidden lg:flex fb-sidebar-properties bg-white border-l border-slate-100 flex-col shrink-0 z-10 transition-[width] duration-200 ease-out">
            <div class="flex-1 overflow-y-auto custom-scrollbar p-4 min-[1100px]:p-5 xl:p-6">
                <?php echo $__env->make('livewire.admin.form-builder-properties', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </aside>

        
        <div class="lg:hidden">
            <?php echo $__env->make('livewire.admin.form-builder-palette', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    <?php elseif($activeTab === 'assignations'): ?>
        
        <div class="flex-1 overflow-y-auto custom-scrollbar bg-[#fafbfc]">
            <div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManageForms): ?>
                
                <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden" x-data="{ assignMode: 'user' }">
                    <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-slate-50 bg-slate-50/30">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white shrink-0" style="background: var(--accent);">
                                <iconify-icon icon="solar:user-plus-bold" width="18"></iconify-icon>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900"><?php echo e(__('forms_builder.new_assignment')); ?></h3>
                                <p class="text-[11px] text-slate-500 mt-0.5">Choisissez un destinataire pour ce formulaire</p>
                            </div>
                        </div>
                    </div>

                    <div class="px-5 sm:px-6 py-5">
                        
                        <div class="flex gap-0 mb-5 bg-slate-100/60 rounded-xl p-0.5">
                            <button type="button" @click="assignMode = 'user'"
                                    class="flex-1 py-2 text-xs font-semibold rounded-lg transition-all text-center flex items-center justify-center gap-1.5"
                                    :class="assignMode === 'user' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'">
                                <iconify-icon icon="solar:user-linear" width="15"></iconify-icon>
                                <?php echo e(__('forms_builder.user')); ?>

                            </button>
                            <button type="button" @click="assignMode = 'function'"
                                    class="flex-1 py-2 text-xs font-semibold rounded-lg transition-all text-center flex items-center justify-center gap-1.5"
                                    :class="assignMode === 'function' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'">
                                <iconify-icon icon="solar:users-group-rounded-linear" width="15"></iconify-icon>
                                <?php echo e(__('forms_builder.or_function')); ?>

                            </button>
                        </div>

                        
                        <div x-show="assignMode === 'user'" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3">
                                <div class="flex-1 min-w-0">
                                    <label class="text-[11px] font-semibold text-slate-500 mb-1.5 block"><?php echo e(__('forms_builder.user')); ?></label>
                                    <select wire:model="assign_user_id"
                                            class="input-builder w-full text-xs py-2.5">
                                        <option value=""><?php echo e(__('forms_builder.choose')); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                            <option value="<?php echo e($m->user_id); ?>"><?php echo e($m->user?->name ?? '—'); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </select>
                                    <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('assign_user_id')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('assign_user_id'))]); ?>
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
                                <div class="shrink-0">
                                    <button type="button" wire:click="assignForm"
                                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:opacity-90 transition-all"
                                            style="background: var(--accent);">
                                        <iconify-icon icon="solar:user-plus-bold" width="16"></iconify-icon>
                                        <?php echo e(__('forms_builder.assign')); ?>

                                    </button>
                                </div>
                            </div>
                        </div>

                        
                        <div x-show="assignMode === 'function'" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3">
                                <div class="flex-1 min-w-0">
                                    <label class="text-[11px] font-semibold text-slate-500 mb-1.5 block"><?php echo e(__('forms_builder.or_function')); ?></label>
                                    <select wire:model="assign_function_id"
                                            class="input-builder w-full text-xs py-2.5">
                                        <option value=""><?php echo e(__('forms_builder.choose')); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $organizationFunctions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                            <option value="<?php echo e($fn->id); ?>"><?php echo e($fn->name); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </select>
                                    <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('assign_function_id')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('assign_function_id'))]); ?>
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
                                <div class="shrink-0">
                                    <button type="button" wire:click="assignForm"
                                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:opacity-90 transition-all"
                                            style="background: var(--accent);">
                                        <iconify-icon icon="solar:users-group-rounded-bold" width="16"></iconify-icon>
                                        <?php echo e(__('forms_builder.assign')); ?>

                                    </button>
                                </div>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($organizationFunctions->isEmpty()): ?>
                                <p class="mt-3 text-[11px] text-slate-400 flex items-center gap-1.5">
                                    <iconify-icon icon="solar:info-circle-linear" width="14"></iconify-icon>
                                    Aucune fonction n'est configurée. Ajoutez-en dans les paramètres.
                                </p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
                    <div class="px-5 sm:px-6 py-4 border-b border-slate-50 bg-slate-50/30 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900"><?php echo e(__('forms_builder.assignments_count')); ?></h3>
                            <p class="text-xs text-slate-500 mt-0.5"><?php echo e($assignments->count()); ?> <?php echo e($assignments->count() <= 1 ? 'assignation' : 'assignations'); ?></p>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($assignments->isNotEmpty()): ?>
                            <?php
                                $pendingCount = $assignments->filter(fn($a) => ($a->status instanceof \App\Enums\FormAssignmentStatus ? $a->status->value : (string) $a->status) === 'pending')->count();
                                $submittedCount = $assignments->filter(fn($a) => ($a->status instanceof \App\Enums\FormAssignmentStatus ? $a->status->value : (string) $a->status) === 'submitted')->count();
                            ?>
                            <div class="flex items-center gap-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendingCount > 0): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-amber-50 text-[10px] font-bold text-amber-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                        <?php echo e($pendingCount); ?> en attente
                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($submittedCount > 0): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-emerald-50 text-[10px] font-bold text-emerald-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        <?php echo e($submittedCount); ?> soumis
                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="divide-y divide-slate-50">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <?php
                                $aStatus = $a->status instanceof \App\Enums\FormAssignmentStatus ? $a->status->value : (string) $a->status;
                                $aBadge = match($aStatus) {
                                    'submitted' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'icon' => 'solar:check-circle-bold', 'label' => __('forms_builder.submitted')],
                                    'overdue' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'icon' => 'solar:clock-circle-bold', 'label' => __('forms_builder.overdue')],
                                    'expired' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-500', 'icon' => 'solar:lock-keyhole-bold', 'label' => __('forms_builder.expired')],
                                    default => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'icon' => 'solar:clock-circle-linear', 'label' => __('forms_builder.pending')],
                                };
                                $assigneeName = $a->user?->name ?? $a->organizationFunction?->name ?? '—';
                                $initial = mb_substr($assigneeName, 0, 1);
                                $isFunction = ! $a->user_id && $a->organization_function_id;
                            ?>
                            <div class="px-5 sm:px-6 py-3.5 flex items-center gap-4 hover:bg-slate-50/40 transition-colors group">
                                <div class="w-10 h-10 rounded-xl <?php echo e($isFunction ? 'bg-blue-50 text-blue-600' : 'bg-slate-50 text-slate-600'); ?> flex items-center justify-center font-bold text-sm shrink-0">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isFunction): ?>
                                        <iconify-icon icon="solar:users-group-rounded-bold" width="18"></iconify-icon>
                                    <?php else: ?>
                                        <?php echo e($initial); ?>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-semibold text-slate-900 truncate"><?php echo e($assigneeName); ?></p>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isFunction): ?>
                                            <span class="text-[9px] font-bold uppercase tracking-wider text-blue-500 bg-blue-50 px-1.5 py-0.5 rounded">Fonction</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                        <span><?php echo e(__('forms_builder.by')); ?> <span class="font-medium text-slate-600"><?php echo e($a->assignedBy?->name ?? '—'); ?></span></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($a->due_date): ?>
                                            <span class="text-slate-300">·</span>
                                            <span class="inline-flex items-center gap-1">
                                                <iconify-icon icon="solar:calendar-linear" width="12" class="text-slate-400"></iconify-icon>
                                                <?php echo e(__('forms_builder.due')); ?> <?php echo e($a->due_date->format('d/m/Y')); ?>

                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($a->expires_at): ?>
                                            <span class="text-slate-300">·</span>
                                            <span class="inline-flex items-center gap-1 <?php echo e($aStatus === 'expired' ? 'text-slate-500 font-medium' : ''); ?>">
                                                <iconify-icon icon="solar:lock-keyhole-linear" width="12" class="text-slate-400"></iconify-icon>
                                                <?php echo e(__('forms_builder.assign_expires_at')); ?> <?php echo e($a->expires_at->format('d/m/Y H:i')); ?>

                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </p>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-[11px] font-bold <?php echo e($aBadge['bg']); ?> <?php echo e($aBadge['text']); ?>">
                                        <iconify-icon icon="<?php echo e($aBadge['icon']); ?>" width="14"></iconify-icon>
                                        <?php echo e($aBadge['label']); ?>

                                    </span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManageForms && $aStatus === 'pending'): ?>
                                        <button type="button" @click="$dispatch('confirm-action', { title: 'Supprimer', message: '<?php echo e(__('forms_builder.delete_assignment_confirm')); ?>', confirmLabel: 'Supprimer', variant: 'danger', onConfirm: () => $wire.deleteAssignment(<?php echo e($a->id); ?>) })"
                                                class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors opacity-0 group-hover:opacity-100 sm:opacity-100">
                                            <iconify-icon icon="solar:trash-bin-trash-linear" width="16"></iconify-icon>
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div class="px-5 sm:px-6 py-16 text-center">
                                <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto mb-4">
                                    <iconify-icon icon="solar:user-check-linear" width="28" class="text-slate-300"></iconify-icon>
                                </div>
                                <h4 class="text-sm font-bold text-slate-700"><?php echo e(__('forms_builder.no_assignments')); ?></h4>
                                <p class="text-xs text-slate-400 mt-1.5 max-w-xs mx-auto">Assignez ce formulaire à un utilisateur ou une fonction pour qu'il puisse le remplir.</p>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif($activeTab === 'reponses'): ?>
        
        <div class="flex-1 overflow-y-auto custom-scrollbar bg-[#fafbfc]">
            <div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
                <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-8 sm:p-10 text-center">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-50 text-slate-300 mx-auto mb-4">
                        <iconify-icon icon="solar:chart-2-bold-duotone" width="28"></iconify-icon>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-1.5"><?php echo e(__('forms_builder.responses_tab')); ?></h3>
                    <p class="text-xs sm:text-sm text-slate-400 mb-6"><?php echo e(__('forms_builder.view_responses_hint')); ?></p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedForm): ?>
                        <a href="<?php echo e(route('admin.forms.responses', $selectedForm)); ?>"
                           class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition-all"
                           style="background: var(--accent);">
                            <iconify-icon icon="solar:eye-bold" width="18"></iconify-icon>
                            <?php echo e(__('forms_builder.view_responses')); ?>

                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="lg:hidden fixed inset-0 z-40" x-show="paletteDrawer" x-cloak style="display:none;">
        <div class="absolute inset-0 bg-slate-900/30 backdrop-blur-[2px]" @click="paletteDrawer = false"></div>
        <div class="absolute left-0 top-0 bottom-0 fb-drawer-palette bg-white shadow-2xl border-r border-slate-100 flex flex-col rounded-r-2xl overflow-hidden"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full">
            <div class="h-12 px-4 border-b border-slate-50 flex items-center justify-between shrink-0">
                <h3 class="text-xs font-bold text-slate-900"><?php echo e(__('forms_builder.search_fields')); ?></h3>
                <button type="button" @click="paletteDrawer = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition" aria-label="<?php echo e(__('forms_builder.close')); ?>">
                    <iconify-icon icon="solar:close-circle-linear" width="16"></iconify-icon>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                <?php echo $__env->make('livewire.admin.form-builder-palette', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </div>
    </div>

    
    <div class="lg:hidden fixed inset-0 z-40" x-show="propsDrawer" x-cloak style="display:none;">
        <div class="absolute inset-0 bg-slate-900/30 backdrop-blur-[2px]" @click="propsDrawer = false"></div>
        <div class="absolute right-0 top-0 bottom-0 fb-drawer-properties bg-white shadow-2xl border-l border-slate-100 flex flex-col rounded-l-2xl overflow-hidden"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">
            <div class="h-12 px-4 border-b border-slate-50 flex items-center justify-between shrink-0">
                <h3 class="text-xs font-bold text-slate-900"><?php echo e(__('forms_builder.properties')); ?></h3>
                <button type="button" @click="propsDrawer = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition" aria-label="<?php echo e(__('forms_builder.close')); ?>">
                    <iconify-icon icon="solar:close-circle-linear" width="16"></iconify-icon>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto custom-scrollbar p-5">
                <?php echo $__env->make('livewire.admin.form-builder-properties', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/livewire/admin/form-builder.blade.php ENDPATH**/ ?>