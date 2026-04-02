<?php
    /** @var \App\Models\FormField $f */
    $type        = (string) $f->type;
    $config      = is_array($f->configuration) ? $f->configuration : [];
    $placeholder = $config['placeholder'] ?? '';
    $helpText    = $config['help_text'] ?? '';
    $options     = $config['options'] ?? $f->options ?? [];
    $displayMode = $config['display_mode'] ?? 'list';
    $fieldLayout = $config['layout'] ?? 'full';
    $name        = "custom[{$f->key}]";
    $oldVal      = old("custom.{$f->key}");
    $fieldId     = 'field-' . ((string) ($f->id ?? $f->key)) . '-' . $f->key;
    $hasChoiceOptions = in_array($type, ['radio', 'checkbox'], true) && is_array($options) && count($options) > 0;
    $labelForId = $hasChoiceOptions ? $fieldId . '-opt-0' : $fieldId;
?>

<div class="space-y-2 <?php echo e($fieldLayout === 'half' ? 'col-span-6 sm:col-span-3' : ($fieldLayout === 'third' ? 'col-span-6 sm:col-span-2' : 'col-span-6')); ?>">
    <label for="<?php echo e($labelForId); ?>" class="text-[13px] font-semibold text-slate-700 block">
        <?php echo e($f->label); ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($f->required): ?><span class="text-red-400 ml-0.5">*</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </label>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'textarea'): ?>
        <textarea name="<?php echo e($name); ?>" id="<?php echo e($fieldId); ?>" rows="4"
                  class="mnx-input block w-full rounded-xl border border-slate-200 bg-white py-3 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0 transition resize-none"
                  placeholder="<?php echo e($placeholder); ?>"
                  <?php if($f->required): echo 'required'; endif; ?>><?php echo e($oldVal); ?></textarea>

    <?php elseif($type === 'select'): ?>
        <div class="relative group/select">
            <select name="<?php echo e($name); ?>" id="<?php echo e($fieldId); ?>"
                    class="select-manexo-inset block w-full text-[13px] font-medium text-slate-800 transition-colors duration-200"
                    <?php if($f->required): echo 'required'; endif; ?>>
                <option value=""><?php echo e($placeholder ?: __('Sélectionner…')); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = (array) $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <option value="<?php echo e($opt); ?>" <?php if((string) $oldVal === (string) $opt): echo 'selected'; endif; ?>><?php echo e($opt); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 transition-colors duration-200 group-hover/select:text-slate-600">
                <iconify-icon icon="solar:alt-arrow-down-linear" width="16" class="opacity-90"></iconify-icon>
            </div>
        </div>

    <?php elseif($type === 'radio'): ?>
        <?php $radioOpts = (array) $options; ?>
        <div class="pt-1
            <?php echo e($displayMode === 'inline' ? 'flex flex-wrap gap-2.5' : ''); ?>

            <?php echo e($displayMode === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 gap-2.5' : ''); ?>

            <?php echo e($displayMode === 'card' ? 'space-y-2.5' : ''); ?>

            <?php echo e($displayMode === 'list' ? 'space-y-2' : ''); ?>

        ">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $radioOpts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <?php $optionId = $fieldId . '-opt-' . $loop->index; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($displayMode === 'card'): ?>
                    <label class="mnx-option-card flex items-center gap-4 px-4 py-3.5 rounded-xl border border-slate-200 bg-white cursor-pointer group"
                           :class="{ 'selected': false }">
                        <input type="radio" name="<?php echo e($name); ?>" id="<?php echo e($optionId); ?>" value="<?php echo e($opt); ?>"
                               class="mnx-radio"
                               <?php if((string) $oldVal === (string) $opt): echo 'checked'; endif; ?>
                               <?php if($f->required): echo 'required'; endif; ?>>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition"><?php echo e($opt); ?></span>
                    </label>
                <?php elseif($displayMode === 'inline'): ?>
                    <label class="mnx-option-card inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl border border-slate-200 bg-white cursor-pointer group">
                        <input type="radio" name="<?php echo e($name); ?>" id="<?php echo e($optionId); ?>" value="<?php echo e($opt); ?>"
                               class="mnx-radio" style="width:16px;height:16px;"
                               <?php if((string) $oldVal === (string) $opt): echo 'checked'; endif; ?>
                               <?php if($f->required): echo 'required'; endif; ?>>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition"><?php echo e($opt); ?></span>
                    </label>
                <?php elseif($displayMode === 'grid'): ?>
                    <label class="mnx-option-card flex items-center gap-3.5 px-4 py-3 rounded-xl border border-slate-200 bg-white cursor-pointer group">
                        <input type="radio" name="<?php echo e($name); ?>" id="<?php echo e($optionId); ?>" value="<?php echo e($opt); ?>"
                               class="mnx-radio" style="width:18px;height:18px;"
                               <?php if((string) $oldVal === (string) $opt): echo 'checked'; endif; ?>
                               <?php if($f->required): echo 'required'; endif; ?>>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition"><?php echo e($opt); ?></span>
                    </label>
                <?php else: ?>
                    
                    <label class="mnx-option-card flex items-center gap-3.5 px-4 py-3 rounded-xl border border-slate-200 bg-white cursor-pointer group">
                        <input type="radio" name="<?php echo e($name); ?>" id="<?php echo e($optionId); ?>" value="<?php echo e($opt); ?>"
                               class="mnx-radio"
                               <?php if((string) $oldVal === (string) $opt): echo 'checked'; endif; ?>
                               <?php if($f->required): echo 'required'; endif; ?>>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition"><?php echo e($opt); ?></span>
                    </label>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

    <?php elseif($type === 'checkbox'): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_array($options) && count($options) > 0): ?>
            
            <div class="pt-1
                <?php echo e($displayMode === 'inline' ? 'flex flex-wrap gap-2.5' : ''); ?>

                <?php echo e($displayMode === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 gap-2.5' : ''); ?>

                <?php echo e($displayMode === 'card' ? 'space-y-2.5' : ''); ?>

                <?php echo e($displayMode === 'list' ? 'space-y-2' : ''); ?>

            ">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = (array) $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <?php $optionId = $fieldId . '-opt-' . $loop->index; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($displayMode === 'card'): ?>
                        <label class="mnx-option-card flex items-center gap-4 px-4 py-3.5 rounded-xl border border-slate-200 bg-white cursor-pointer group">
                            <input type="checkbox" name="<?php echo e($name); ?>[]" id="<?php echo e($optionId); ?>" value="<?php echo e($opt); ?>"
                                   class="mnx-checkbox"
                                   <?php if(is_array($oldVal) && in_array($opt, $oldVal)): echo 'checked'; endif; ?>
                                   <?php if($f->required && $loop->first): ?> required <?php endif; ?>>
                            <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition"><?php echo e($opt); ?></span>
                        </label>
                    <?php elseif($displayMode === 'inline'): ?>
                        <label class="mnx-option-card inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl border border-slate-200 bg-white cursor-pointer group">
                            <input type="checkbox" name="<?php echo e($name); ?>[]" id="<?php echo e($optionId); ?>" value="<?php echo e($opt); ?>"
                                   class="mnx-checkbox" style="width:16px;height:16px;border-radius:4px;"
                                   <?php if(is_array($oldVal) && in_array($opt, $oldVal)): echo 'checked'; endif; ?>
                                   <?php if($f->required && $loop->first): ?> required <?php endif; ?>>
                            <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition"><?php echo e($opt); ?></span>
                        </label>
                    <?php elseif($displayMode === 'grid'): ?>
                        <label class="mnx-option-card flex items-center gap-3.5 px-4 py-3 rounded-xl border border-slate-200 bg-white cursor-pointer group">
                            <input type="checkbox" name="<?php echo e($name); ?>[]" id="<?php echo e($optionId); ?>" value="<?php echo e($opt); ?>"
                                   class="mnx-checkbox" style="width:18px;height:18px;"
                                   <?php if(is_array($oldVal) && in_array($opt, $oldVal)): echo 'checked'; endif; ?>
                                   <?php if($f->required && $loop->first): ?> required <?php endif; ?>>
                            <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition"><?php echo e($opt); ?></span>
                        </label>
                    <?php else: ?>
                        
                        <label class="mnx-option-card flex items-center gap-3.5 px-4 py-3 rounded-xl border border-slate-200 bg-white cursor-pointer group">
                            <input type="checkbox" name="<?php echo e($name); ?>[]" id="<?php echo e($optionId); ?>" value="<?php echo e($opt); ?>"
                                   class="mnx-checkbox"
                                   <?php if(is_array($oldVal) && in_array($opt, $oldVal)): echo 'checked'; endif; ?>
                                   <?php if($f->required && $loop->first): ?> required <?php endif; ?>>
                            <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition"><?php echo e($opt); ?></span>
                        </label>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        <?php else: ?>
            
            <label class="mnx-option-card flex items-center gap-3.5 px-4 py-3 rounded-xl border border-slate-200 bg-white cursor-pointer group">
                <input type="hidden" name="<?php echo e($name); ?>" id="<?php echo e($fieldId); ?>-hidden" value="0">
                <input type="checkbox" name="<?php echo e($name); ?>" id="<?php echo e($fieldId); ?>" value="1"
                       class="mnx-checkbox"
                       <?php if((bool) $oldVal): echo 'checked'; endif; ?>>
                <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition"><?php echo e(__('Oui')); ?></span>
            </label>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php elseif($type === 'file'): ?>
        <div x-data="{ fileName: null, dragging: false }" class="relative">
            <input type="file" name="<?php echo e($name); ?>" id="<?php echo e($fieldId); ?>"
                   class="peer absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                   <?php if(!empty($config['accept'])): ?> accept="<?php echo e($config['accept']); ?>" <?php endif; ?>
                   <?php if($f->required): echo 'required'; endif; ?>
                   @change="fileName = $event.target.files[0]?.name || null"
                   @dragover.prevent="dragging = true"
                   @dragleave="dragging = false"
                   @drop="dragging = false">
            <div class="flex items-center gap-4 px-4 py-4 rounded-xl border-2 border-dashed transition-all"
                 :class="{
                     'border-[var(--accent)] bg-[var(--accent-soft)]': fileName || dragging,
                     'border-slate-200 bg-white hover:border-slate-300': !fileName && !dragging
                 }">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl transition"
                     :class="fileName ? 'bg-white text-[var(--accent)] shadow-sm' : 'bg-slate-50 text-slate-400'">
                    <iconify-icon :icon="fileName ? 'solar:file-check-bold-duotone' : 'solar:cloud-upload-linear'" width="22"></iconify-icon>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-sm font-medium truncate transition"
                         :class="fileName ? 'text-slate-900' : 'text-slate-500'"
                         x-text="fileName || '<?php echo e(__('Glissez un fichier ou cliquez pour parcourir')); ?>'"></div>
                    <div class="text-[11px] mt-0.5 transition"
                         :class="fileName ? 'text-[var(--accent)]' : 'text-slate-400'">
                        <span x-show="!fileName"><?php echo e(!empty($config['accept']) ? $config['accept'] . ' · ' : ''); ?><?php echo e(__('Max 10 Mo')); ?></span>
                        <span x-show="fileName" x-cloak><?php echo e(__('Fichier sélectionné')); ?></span>
                    </div>
                </div>
                <div x-show="fileName" x-cloak class="shrink-0">
                    <iconify-icon icon="solar:check-circle-bold" width="20" class="text-[var(--accent)]"></iconify-icon>
                </div>
            </div>
        </div>

    <?php else: ?>
        <?php
            $inputType = in_array($type, ['email', 'date', 'datetime', 'number'], true)
                ? ($type === 'datetime' ? 'datetime-local' : $type)
                : 'text';
        ?>
        <input type="<?php echo e($inputType); ?>" name="<?php echo e($name); ?>" id="<?php echo e($fieldId); ?>" value="<?php echo e($oldVal); ?>"
               class="mnx-input block w-full rounded-xl border border-slate-200 bg-white py-3 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0 transition"
               placeholder="<?php echo e($placeholder); ?>"
               <?php if($f->required): echo 'required'; endif; ?>>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($helpText): ?>
        <p class="text-[12px] text-slate-400 leading-relaxed"><?php echo e($helpText); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ["custom.{$f->key}"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[12px] text-red-500 mt-1 flex items-center gap-1"><iconify-icon icon="solar:danger-circle-bold" width="13"></iconify-icon> <?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\forms\partials\custom-field.blade.php ENDPATH**/ ?>