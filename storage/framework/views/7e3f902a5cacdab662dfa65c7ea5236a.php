<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div class="space-y-2">
        <label for="guest_name" class="text-[13px] font-semibold text-slate-700"><?php echo e(__('Votre nom')); ?> <span class="text-red-400">*</span></label>
        <input type="text" name="guest_name" id="guest_name" value="<?php echo e(old('guest_name', auth()->user()?->name ?? '')); ?>"
               class="mnx-input block w-full rounded-xl border border-slate-200 bg-white py-3 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0 transition"
               placeholder="<?php echo e(__('Votre nom complet')); ?>" required>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['guest_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[12px] text-red-500 mt-1 flex items-center gap-1"><iconify-icon icon="solar:danger-circle-bold" width="13"></iconify-icon> <?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <div class="space-y-2">
        <label for="guest_email" class="text-[13px] font-semibold text-slate-700"><?php echo e(__('Votre email')); ?> <span class="text-red-400">*</span></label>
        <input type="email" name="guest_email" id="guest_email" value="<?php echo e(old('guest_email', auth()->user()?->email ?? '')); ?>"
               class="mnx-input block w-full rounded-xl border border-slate-200 bg-white py-3 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0 transition"
               placeholder="email@exemple.com" required>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['guest_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[12px] text-red-500 mt-1 flex items-center gap-1"><iconify-icon icon="solar:danger-circle-bold" width="13"></iconify-icon> <?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form->creates_ticket): ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$form->ticket_category_id): ?>
        <div class="space-y-2">
            <label for="ticket_category_id" class="text-[13px] font-semibold text-slate-700"><?php echo e(__('Catégorie')); ?></label>
            <div class="relative group/select">
                <select name="ticket_category_id" id="ticket_category_id"
                        class="select-manexo-inset block w-full text-[13px] font-medium text-slate-800 transition-colors duration-200">
                    <option value=""><?php echo e(__('Sélectionner…')); ?></option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <option value="<?php echo e($c->id); ?>" <?php if((string) old('ticket_category_id') === (string) $c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 transition-colors duration-200 group-hover/select:text-slate-600">
                    <iconify-icon icon="solar:alt-arrow-down-linear" width="16" class="opacity-90"></iconify-icon>
                </div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['ticket_category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[12px] text-red-500 mt-1 flex items-center gap-1"><iconify-icon icon="solar:danger-circle-bold" width="13"></iconify-icon> <?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php else: ?>
        <input type="hidden" name="ticket_category_id" id="ticket_category_id_fixed" value="<?php echo e((int) $form->ticket_category_id); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<div class="space-y-2">
    <label for="subject" class="text-[13px] font-semibold text-slate-700"><?php echo e(__('Sujet')); ?> <span class="text-red-400">*</span></label>
    <input type="text" name="subject" id="subject" value="<?php echo e(old('subject')); ?>"
           class="mnx-input block w-full rounded-xl border border-slate-200 bg-white py-3 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0 transition"
           placeholder="<?php echo e(__('Résumez votre demande')); ?>" required>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[12px] text-red-500 mt-1 flex items-center gap-1"><iconify-icon icon="solar:danger-circle-bold" width="13"></iconify-icon> <?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<div class="space-y-2">
    <label for="description" class="text-[13px] font-semibold text-slate-700"><?php echo e(__('Description')); ?> <span class="text-red-400">*</span></label>
    <textarea name="description" id="description" rows="4"
              class="mnx-input block w-full rounded-xl border border-slate-200 bg-white py-3 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0 transition resize-none"
              placeholder="<?php echo e(__('Décrivez votre besoin en détail…')); ?>" required><?php echo e(old('description')); ?></textarea>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[12px] text-red-500 mt-1 flex items-center gap-1"><iconify-icon icon="solar:danger-circle-bold" width="13"></iconify-icon> <?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/forms/partials/general-fields.blade.php ENDPATH**/ ?>