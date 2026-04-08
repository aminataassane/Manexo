<?php
    $participants = $thread->participants ?? collect();
    $title = $thread->is_group
        ? ($thread->name ?: __('pages.discussions.discussion_group'))
        : ($participants->where('id', '!=', auth()->id())->first()?->name ?: __('pages.discussions.discussion'));
    $participantIds = $participants->pluck('id')->all();
    $orgRolesByUserId = $orgRolesByUserId ?? [];
    $roleLabels = $roleLabels ?? [
        'owner' => __('Admin'),
        'admin' => __('Admin'),
        'agent' => __('Agent'),
        'member' => __('Membre'),
    ];
    $discussionCardTranslations = [
        'open' => __('pages.discussions.user_card_open'),
        'creator' => __('pages.discussions.badge_thread_creator'),
        'inGroup' => __('pages.discussions.badge_in_group'),
    ];
?>

<div
    class="flex flex-col h-full min-h-0 overflow-hidden bg-white"
    x-data="threadWebSocket(<?php echo e($thread->id); ?>, <?php echo e(auth()->id() ?? 'null'); ?>)"
    @keydown.enter.window="if (document.activeElement?.closest('[data-composer]') && !$event.shiftKey) { $event.preventDefault(); $refs.submitBtn?.click() }"
>
    <div class="flex flex-1 min-h-0 overflow-hidden" x-data="{ infoOpen: false, mobileInfoOpen: false, toggleInfo(){ if (window.innerWidth >= 768) this.infoOpen = !this.infoOpen; else this.mobileInfoOpen = !this.mobileInfoOpen; } }">
        
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <header class="shrink-0 bg-white border-b border-slate-100 px-4 py-2" style="padding-top: max(0.375rem, env(safe-area-inset-top));">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($embedded ?? false): ?>
                            <a href="<?php echo e(route('discussions.index')); ?>" wire:navigate
                               class="md:hidden shrink-0 h-8 w-8 rounded-lg text-slate-400 hover:bg-slate-50 hover:text-slate-700 transition-colors inline-flex items-center justify-center mr-1">
                                <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="h-8 w-8 rounded-full flex items-center justify-center text-sm font-semibold shrink-0" style="background: var(--accent-soft); color: var(--accent);">
                            <iconify-icon icon="<?php echo e($thread->is_group ? 'solar:users-group-rounded-bold-duotone' : 'solar:user-circle-bold-duotone'); ?>" width="18"></iconify-icon>
                        </div>
                        <div class="min-w-0">
                            <h1 class="text-sm font-bold text-slate-900 truncate"><?php echo e($title); ?></h1>
                            <p class="text-[11px] text-slate-400 truncate">
                                <?php echo e($participants->count()); ?> <?php echo e($participants->count() > 1 ? __('pages.discussions.participants') : __('pages.discussions.participant')); ?>

                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <button type="button" @click="toggleInfo()" class="h-8 w-8 rounded-lg text-slate-400 hover:bg-slate-50 hover:text-slate-700 transition-colors inline-flex items-center justify-center" aria-label="<?php echo e(__('pages.discussions.info')); ?>">
                            <iconify-icon icon="solar:sidebar-minimalistic-linear" width="18"></iconify-icon>
                        </button>
                        <button type="button" wire:click="$refresh" class="h-8 w-8 rounded-lg text-slate-400 hover:bg-slate-50 hover:text-slate-700 transition-colors inline-flex items-center justify-center" title="<?php echo e(__('pages.discussions.refresh')); ?>">
                            <iconify-icon icon="solar:refresh-linear" width="16" class="wire-loading:animate-spin"></iconify-icon>
                        </button>
                    </div>
                </div>
            </header>

            
            <div id="thread-messages" class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden custom-scrollbar overscroll-contain messaging-chat-scroll">
                <div class="mx-auto w-full max-w-3xl px-4 py-6" style="padding-left: max(1rem, env(safe-area-inset-left)); padding-right: max(1rem, env(safe-area-inset-right));">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($hasMoreMessages ?? false)): ?>
                        <div class="mb-4 flex justify-center">
                            <button
                                type="button"
                                wire:click="loadMoreMessages"
                                wire:loading.attr="disabled"
                                wire:target="loadMoreMessages"
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 disabled:opacity-60"
                            >
                                <iconify-icon icon="solar:alt-arrow-up-linear" width="14"></iconify-icon>
                                <?php echo e(__('Charger les messages plus anciens')); ?>

                            </button>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div id="thread-timeline" class="space-y-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $thread->messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php
                            $isOwn = $msg->user_id && (int) $msg->user_id === (int) auth()->id();
                            $avatarInitials = \Illuminate\Support\Str::of($msg->user?->name ?? 'U')
                                ->explode(' ')
                                ->take(2)
                                ->map(fn ($p) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($p, 0, 1)))
                                ->implode('');
                            $time = $msg->created_at->diffForHumans();
                            $bodyEscaped = e($msg->body);
                            $bodyFormatted = nl2br($bodyEscaped);
                            $messageAttachments = is_array($msg->attachments) ? $msg->attachments : [];
                            $senderBadges = [];
                            if ($msg->user_id && (int) $msg->user_id === (int) $thread->created_by) {
                                $senderBadges[] = __('pages.discussions.badge_thread_creator');
                            } elseif ($thread->is_group && $msg->user_id) {
                                $senderBadges[] = __('pages.discussions.badge_in_group');
                            }
                            $senderOrgRole = $orgRolesByUserId[(int) $msg->user_id] ?? null;
                            if ($senderOrgRole && $senderOrgRole !== 'member') {
                                $senderBadges[] = $roleLabels[$senderOrgRole] ?? $senderOrgRole;
                            }
                        ?>

                        <div <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processElementKey('disc-msg-{{ $msg->id }}', get_defined_vars()); ?>wire:key="disc-msg-<?php echo e($msg->id); ?>" data-discussion-message-id="<?php echo e($msg->id); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOwn): ?>
                            <div class="flex justify-end">
                                <div class="max-w-[85%] sm:max-w-[80%] md:max-w-[75%] min-w-0">
                                    <div class="flex items-center justify-end gap-2 mb-1">
                                        <span class="text-[10px] text-slate-400"><?php echo e($time); ?></span>
                                        <span class="text-[11px] font-semibold text-slate-600"><?php echo e($msg->user?->name ?? '—'); ?></span>
                                    </div>
                                    <div class="messaging-bubble-own rounded-2xl rounded-br-sm px-4 py-2.5 text-[0.88rem] leading-relaxed text-white" style="background-color: var(--accent);">
                                        <?php echo $bodyFormatted; ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($messageAttachments) > 0): ?>
                                            <div class="mt-2 pt-2 border-t border-white/20 space-y-1.5">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $messageAttachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                    <?php echo $__env->make('livewire.tickets.partials.attachment-link', ['att' => $att, 'variant' => 'mine'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="flex items-end gap-2">
                                <?php if (isset($component)) { $__componentOriginalfac35cd68d3e581fb028d29d830dd47b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfac35cd68d3e581fb028d29d830dd47b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.user-avatar-popover','data' => ['initials' => $avatarInitials,'name' => $msg->user?->name ?? '—','email' => $msg->user?->email,'mentionTag' => $msg->user?->mention_tag,'badges' => $senderBadges]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.user-avatar-popover'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['initials' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($avatarInitials),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($msg->user?->name ?? '—'),'email' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($msg->user?->email),'mention-tag' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($msg->user?->mention_tag),'badges' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($senderBadges)]); ?>
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
                                <div class="max-w-[85%] sm:max-w-[80%] md:max-w-[75%] min-w-0">
                                    <div class="mb-1 flex flex-col gap-0.5 min-[380px]:flex-row min-[380px]:flex-wrap min-[380px]:items-baseline min-[380px]:gap-x-2">
                                        <span class="text-[11px] font-semibold leading-snug text-slate-600 [overflow-wrap:anywhere]"><?php echo e($msg->user?->name ?? '—'); ?></span>
                                        <span class="text-[10px] text-slate-400"><?php echo e($time); ?></span>
                                    </div>
                                    <div class="messaging-bubble-incoming rounded-2xl rounded-bl-sm px-4 py-2.5 text-[0.88rem] leading-relaxed bg-white border border-slate-100 text-slate-700">
                                        <?php echo $bodyFormatted; ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($messageAttachments) > 0): ?>
                                            <div class="mt-2 pt-2 border-t border-slate-100 space-y-1.5">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $messageAttachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                    <?php echo $__env->make('livewire.tickets.partials.attachment-link', ['att' => $att, 'variant' => 'theirs'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="py-16 text-center" data-empty-thread>
                            <div class="mx-auto mb-4 inline-flex h-12 w-12 items-center justify-center rounded-full text-slate-300" style="background: var(--accent-soft);">
                                <iconify-icon icon="solar:chat-round-dots-linear" width="24" style="color: var(--accent); opacity: 0.5;"></iconify-icon>
                            </div>
                            <p class="text-sm text-slate-400"><?php echo e(__('pages.discussions.empty_message')); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div class="shrink-0 messaging-composer border-t border-slate-100 p-2 sm:p-3 z-10" data-composer style="padding-bottom: max(0.5rem, env(safe-area-inset-bottom));">
                <div class="mx-auto w-full max-w-3xl">
                    <form wire:submit="sendMessage" wire:loading.class="pointer-events-none opacity-90" wire:target="sendMessage" class="flex items-end gap-2 bg-[#F1F5F9]/80 rounded-2xl border border-slate-200 p-1.5 focus-within:ring-2 focus-within:ring-[var(--accent)]/30 focus-within:border-[var(--accent)]/30 transition-all"
                        x-data="{
                            draftKey: 'discussion-draft-<?php echo e($thread->id); ?>',
                            draftTimer: null,
                            init() {
                                try {
                                    const saved = localStorage.getItem(this.draftKey);
                                    if (saved && !this.$wire.get('body')) this.$wire.set('body', saved);
                                } catch (e) {}
                            },
                            saveDraft(val) {
                                clearTimeout(this.draftTimer);
                                this.draftTimer = setTimeout(() => {
                                    try {
                                        if (val && val.trim()) localStorage.setItem(this.draftKey, val);
                                        else localStorage.removeItem(this.draftKey);
                                    } catch (e) {}
                                }, 500);
                            },
                            autoGrow(el) {
                                el.style.height = 'auto';
                                el.style.height = Math.min(el.scrollHeight, 128) + 'px';
                            }
                        }"
                        x-on:submit="
                            const txt = ($wire.get('body') || '').trim();
                            if (txt.length > 0) {
                                window.dispatchEvent(new CustomEvent('discussion:pending-send', {
                                    detail: { body: txt, userName: '<?php echo e(addslashes(auth()->user()?->name ?? '')); ?>' }
                                }));
                            }
                            localStorage.removeItem('discussion-draft-<?php echo e($thread->id); ?>');
                        "
                    >
                        
                        <input
                            type="file"
                            multiple
                            class="hidden"
                            id="thread-file-input"
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.gif,.webp,image/*"
                            x-on:change="async (e) => {
                                const list = e.target.files;
                                if (!list?.length) return;
                                const ready = await window.manexoCompressFilesForUpload(list);
                                e.target.value = '';
                                $wire.uploadMultiple('attachmentFiles', ready);
                            }"
                        >
                        <button type="button" onclick="document.getElementById('thread-file-input').click()" class="shrink-0 h-10 w-10 sm:h-9 sm:w-9 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-white/70 transition-colors inline-flex items-center justify-center" title="<?php echo e(__('pages.discussions.attach_file')); ?>">
                            <iconify-icon icon="solar:paperclip-linear" width="19"></iconify-icon>
                        </button>

                        
                        <textarea
                            wire:model="body"
                            rows="1"
                            wire:loading.attr="disabled"
                            wire:target="sendMessage"
                            class="flex-1 bg-transparent border-0 text-slate-900 placeholder:text-slate-400 focus:ring-0 resize-none text-sm py-2 px-1 max-h-32"
                            style="min-height: 2.25rem;"
                            placeholder="<?php echo e(__('pages.discussions.write_message')); ?>"
                            @input="saveDraft($event.target.value); autoGrow($event.target)"
                            @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); $el.closest('form').requestSubmit(); }"
                        ></textarea>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($attachmentFiles ?? []) > 0): ?>
                            <span class="shrink-0 text-[11px] font-semibold px-2 py-1 rounded-lg" style="background: var(--accent-soft); color: var(--accent);"><?php echo e(count($attachmentFiles)); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['type' => 'submit','wireTarget' => 'sendMessage','variant' => 'primary','iconOnly' => true,'xRef' => 'submitBtn','class' => '!rounded-xl shrink-0 h-10 w-10 sm:h-9 sm:w-9 p-0 !px-0','style' => 'background-color: var(--accent);','loadingLabel' => __('ui.action.sending')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','wire-target' => 'sendMessage','variant' => 'primary','icon-only' => true,'x-ref' => 'submitBtn','class' => '!rounded-xl shrink-0 h-10 w-10 sm:h-9 sm:w-9 p-0 !px-0','style' => 'background-color: var(--accent);','loading-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.action.sending'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <iconify-icon icon="solar:plain-bold" width="16"></iconify-icon>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7)): ?>
<?php $attributes = $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7; ?>
<?php unset($__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7)): ?>
<?php $component = $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7; ?>
<?php unset($__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7); ?>
<?php endif; ?>
                    </form>
                    <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('body'),'class' => 'mt-1.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('body')),'class' => 'mt-1.5']); ?>
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
            </div>
        </div>

        
        <aside class="hidden md:flex shrink-0 flex-col bg-white border-l border-slate-100 overflow-hidden transition-[width] duration-300 ease-in-out" :class="infoOpen ? 'w-60 lg:w-72' : 'w-0 border-l-0'">
            <div class="flex flex-col flex-1 min-w-0 min-h-0 w-60 lg:w-72">
                <div class="flex h-12 shrink-0 items-center justify-between border-b border-slate-100 px-4">
                    <span class="text-sm font-bold text-slate-900"><?php echo e(__('pages.discussions.participants')); ?> (<?php echo e($participants->count()); ?>)</span>
                    <button type="button" @click="infoOpen = false" class="text-slate-400 hover:text-slate-700 transition-colors">
                        <iconify-icon icon="solar:close-circle-linear" width="18"></iconify-icon>
                    </button>
                </div>
                <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar p-4 space-y-2">
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($thread->is_group && $canManageParticipants): ?>
                        <div x-data="{ showAdd: false, search: '' }" class="mb-2">
                            <button type="button" @click="showAdd = !showAdd; if(showAdd) $nextTick(() => $refs.addSearch?.focus())" class="flex items-center gap-2 w-full rounded-lg border border-dashed border-slate-200 px-3 py-2 text-xs font-medium text-slate-400 hover:border-[var(--accent)] hover:text-[var(--accent)] transition-colors">
                                <iconify-icon icon="solar:user-plus-linear" width="15"></iconify-icon>
                                <?php echo e(__('pages.discussions.add_member')); ?>

                            </button>
                            <div x-show="showAdd" x-cloak x-transition class="mt-2">
                                <input
                                    type="text"
                                    x-ref="addSearch"
                                    x-model="search"
                                    placeholder="<?php echo e(__('pages.discussions.search')); ?>"
                                    class="w-full rounded-lg bg-[#F1F5F9] border-0 px-3 py-2 text-xs focus:ring-2 focus:ring-[var(--accent)]/20"
                                >
                                <div class="mt-1 max-h-40 overflow-y-auto rounded-lg border border-slate-100 bg-white shadow-sm">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $orgUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ou): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! in_array($ou->id, $participantIds)): ?>
                                            <button
                                                type="button"
                                                x-show="!search || '<?php echo e(strtolower(e($ou->name))); ?>'.includes(search.toLowerCase()) || '<?php echo e(strtolower(e($ou->email))); ?>'.includes(search.toLowerCase())"
                                                wire:click="addParticipant(<?php echo e($ou->id); ?>)"
                                                class="flex w-full items-center gap-2 px-3 py-2 text-xs hover:bg-slate-50 transition-colors"
                                            >
                                                <div class="h-6 w-6 rounded-full flex items-center justify-center text-[10px] font-semibold shrink-0" style="background: var(--accent-soft); color: var(--accent);">
                                                    <?php echo e(strtoupper(mb_substr($ou->name, 0, 1))); ?>

                                                </div>
                                                <div class="min-w-0 text-left">
                                                    <div class="font-medium text-slate-900 truncate"><?php echo e($ou->name); ?></div>
                                                    <div class="text-slate-400 truncate"><?php echo e($ou->email); ?></div>
                                                </div>
                                            </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $participants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <div class="flex items-center gap-2.5 group py-1">
                            <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-semibold shrink-0" style="background: var(--accent-soft); color: var(--accent);">
                                <?php echo e(strtoupper(mb_substr($p->name ?? '?', 0, 1))); ?>

                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-[13px] font-semibold text-slate-900 truncate">
                                    <?php echo e($p->name); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((int) $p->id === (int) $thread->created_by): ?>
                                        <span class="text-[10px] text-slate-400 font-normal ml-1"><?php echo e(__('pages.discussions.creator')); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="text-[11px] text-slate-400 truncate"><?php echo e($p->email); ?></div>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($thread->is_group && $canManageParticipants && (int) $p->id !== (int) $thread->created_by): ?>
                                <button
                                    type="button"
                                    @click="$dispatch('confirm-action', { title: 'Retirer', message: '<?php echo e(__('pages.discussions.remove_participant_confirm')); ?>', confirmLabel: 'Retirer', variant: 'danger', onConfirm: () => $wire.removeParticipant(<?php echo e($p->id); ?>) })"
                                    class="opacity-0 group-hover:opacity-100 shrink-0 p-1 rounded text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all"
                                    title="<?php echo e(__('pages.discussions.remove')); ?>"
                                >
                                    <iconify-icon icon="solar:close-circle-linear" width="15"></iconify-icon>
                                </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
        </aside>

        
        <div x-show="mobileInfoOpen" x-cloak class="md:hidden fixed inset-0 z-50">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="mobileInfoOpen = false" x-transition.opacity></div>
            <div class="absolute right-0 top-0 bottom-0 w-full max-w-[min(100%,20rem)] bg-white shadow-2xl flex flex-col rounded-l-2xl overflow-hidden"
                 x-show="mobileInfoOpen"
                 x-transition:enter="transform transition ease-out duration-300"
                 x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in duration-300"
                 x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
                <div class="flex h-12 shrink-0 items-center justify-between border-b border-slate-100 px-4">
                    <span class="text-sm font-bold text-slate-900"><?php echo e(__('pages.discussions.participants')); ?> (<?php echo e($participants->count()); ?>)</span>
                    <button type="button" @click="mobileInfoOpen = false" class="text-slate-400 hover:text-slate-700 transition-colors">
                        <iconify-icon icon="solar:close-circle-linear" width="18"></iconify-icon>
                    </button>
                </div>
                <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar p-4 space-y-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($thread->is_group && $canManageParticipants): ?>
                        <div x-data="{ showAdd: false, search: '' }" class="mb-2">
                            <button type="button" @click="showAdd = !showAdd; if(showAdd) $nextTick(() => $refs.mobileAddSearch?.focus())" class="flex items-center gap-2 w-full rounded-lg border border-dashed border-slate-200 px-3 py-2 text-xs font-medium text-slate-400 hover:border-[var(--accent)] hover:text-[var(--accent)] transition-colors">
                                <iconify-icon icon="solar:user-plus-linear" width="15"></iconify-icon>
                                <?php echo e(__('pages.discussions.add_member')); ?>

                            </button>
                            <div x-show="showAdd" x-cloak x-transition class="mt-2">
                                <input
                                    type="text"
                                    x-ref="mobileAddSearch"
                                    x-model="search"
                                    placeholder="<?php echo e(__('pages.discussions.search')); ?>"
                                    class="w-full rounded-lg bg-[#F1F5F9] border-0 px-3 py-2 text-xs focus:ring-2 focus:ring-[var(--accent)]/20"
                                >
                                <div class="mt-1 max-h-40 overflow-y-auto rounded-lg border border-slate-100 bg-white shadow-sm">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $orgUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ou): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! in_array($ou->id, $participantIds)): ?>
                                            <button
                                                type="button"
                                                x-show="!search || '<?php echo e(strtolower(e($ou->name))); ?>'.includes(search.toLowerCase()) || '<?php echo e(strtolower(e($ou->email))); ?>'.includes(search.toLowerCase())"
                                                wire:click="addParticipant(<?php echo e($ou->id); ?>)"
                                                class="flex w-full items-center gap-2 px-3 py-2 text-xs hover:bg-slate-50 transition-colors"
                                            >
                                                <div class="h-6 w-6 rounded-full flex items-center justify-center text-[10px] font-semibold shrink-0" style="background: var(--accent-soft); color: var(--accent);">
                                                    <?php echo e(strtoupper(mb_substr($ou->name, 0, 1))); ?>

                                                </div>
                                                <div class="min-w-0 text-left">
                                                    <div class="font-medium text-slate-900 truncate"><?php echo e($ou->name); ?></div>
                                                    <div class="text-slate-400 truncate"><?php echo e($ou->email); ?></div>
                                                </div>
                                            </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $participants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <div class="flex items-center gap-2.5 py-1">
                            <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-semibold shrink-0" style="background: var(--accent-soft); color: var(--accent);">
                                <?php echo e(strtoupper(mb_substr($p->name ?? '?', 0, 1))); ?>

                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-[13px] font-semibold text-slate-900 truncate">
                                    <?php echo e($p->name); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((int) $p->id === (int) $thread->created_by): ?>
                                        <span class="text-[10px] text-slate-400 font-normal ml-1"><?php echo e(__('pages.discussions.creator')); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="text-[11px] text-slate-400 truncate"><?php echo e($p->email); ?></div>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($thread->is_group && $canManageParticipants && (int) $p->id !== (int) $thread->created_by): ?>
                                <button
                                    type="button"
                                    @click="$dispatch('confirm-action', { title: 'Retirer', message: '<?php echo e(__('pages.discussions.remove_participant_confirm')); ?>', confirmLabel: 'Retirer', variant: 'danger', onConfirm: () => $wire.removeParticipant(<?php echo e($p->id); ?>) })"
                                    class="shrink-0 p-1 rounded text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all"
                                    title="<?php echo e(__('pages.discussions.remove')); ?>"
                                >
                                    <iconify-icon icon="solar:close-circle-linear" width="15"></iconify-icon>
                                </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

    <?php
        $__scriptKey = '511689649-3';
        ob_start();
    ?>
<script>
    window.__manexoDiscussionCard = <?php echo json_encode($discussionCardTranslations, 15, 512) ?>;
    Alpine.data('threadWebSocket', (threadId, currentUserId) => ({
        threadId,
        currentUserId,
        init() {
            window.addEventListener('discussion:pending-send', (evt) => {
                const body = evt?.detail?.body || '';
                const userName = evt?.detail?.userName || '';
                if (body) this.appendPendingMessage(body, userName);
            });

            const tryConnect = () => {
                if (typeof window.Echo !== 'undefined') {
                    window.Echo.private('discussion.' + this.threadId)
                        .listen('.discussion.message.sent', (e) => this.appendMessage(e))
                        .listen('.discussion.participant.changed', () => {
                            this.$wire.$refresh();
                        });
                    return;
                }
                setTimeout(tryConnect, 300);
            };
            tryConnect();
        },
        appendMessage(e) {
            const timeline = document.getElementById('thread-timeline');
            const scroll = document.getElementById('thread-messages');
            if (!timeline || !scroll) return;

            const messageId = e.id != null && String(e.id).match(/^\d+$/) ? String(e.id) : '';
            if (messageId !== '' && timeline.querySelector('[data-discussion-message-id="' + messageId + '"]')) {
                timeline.querySelectorAll('[data-pending-own="1"]').forEach((el) => el.remove());
                return;
            }

            timeline.querySelectorAll('[data-pending-own="1"]').forEach((el) => el.remove());

            const empty = timeline.querySelector('[data-empty-thread]');
            if (empty) empty.remove();

            const isOwn = e.user_id && parseInt(e.user_id, 10) === parseInt(this.currentUserId, 10);
            const time = e.created_at ? new Date(e.created_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }) : '';
            const rawName = e.user_name || '';
            const name = this.escapeHtml(rawName);
            const body = this.escapeHtml(e.body || '').replace(/\n/g, '<br>');
            const initials = rawName
                .split(' ')
                .filter(Boolean)
                .slice(0, 2)
                .map((part) => part.charAt(0).toUpperCase())
                .join('') || 'U';

            const attachments = Array.isArray(e.attachments) ? e.attachments : [];
            const attachmentsHtml = attachments.length
                ? `<div class="mt-2 pt-2 ${isOwn ? 'border-t border-white/20' : 'border-t border-slate-100'} space-y-1.5">` +
                    attachments.map(a => {
                        const url = a?.url || (a?.path ? (window.location.origin + '/storage/' + a.path) : '#');
                        const n = this.escapeHtml(a?.name || 'Fichier');
                        return `<a href="${url}" target="_blank" rel="noopener" class="block text-xs ${isOwn ? 'text-white/90' : 'text-slate-600'} hover:underline flex items-center gap-1"><iconify-icon icon="solar:file-linear" width="12"></iconify-icon> ${n}</a>`;
                    }).join('') +
                  `</div>`
                : '';

            const incomingAvatar = !isOwn ? this.buildIncomingUserCard(e, initials, name) : '';
            const html = isOwn
                ? `<div class="flex justify-end"><div class="max-w-[85%] sm:max-w-[80%] md:max-w-[75%] min-w-0"><div class="flex items-center justify-end gap-2 mb-1"><span class="text-[10px] text-slate-400">${time}</span><span class="text-[11px] font-semibold text-slate-600">${name}</span></div><div class="messaging-bubble-own rounded-2xl rounded-br-sm px-4 py-2.5 text-[0.88rem] leading-relaxed text-white" style="background-color: var(--accent);">${body}${attachmentsHtml}</div></div></div>`
                : `<div class="flex items-end gap-2">${incomingAvatar}<div class="max-w-[85%] sm:max-w-[80%] md:max-w-[75%] min-w-0"><div class="flex items-center gap-2 mb-1"><span class="text-[11px] font-semibold text-slate-600">${name}</span><span class="text-[10px] text-slate-400">${time}</span></div><div class="messaging-bubble-incoming rounded-2xl rounded-bl-sm px-4 py-2.5 text-[0.88rem] leading-relaxed bg-white border border-slate-100 text-slate-700">${body}${attachmentsHtml}</div></div></div>`;

            const div = document.createElement('div');
            div.className = 'animate-enter';
            if (messageId !== '') div.setAttribute('data-discussion-message-id', messageId);
            div.innerHTML = html;
            timeline.appendChild(div);
            scroll.scrollTop = scroll.scrollHeight;
        },
        appendPendingMessage(rawBody, rawName) {
            const timeline = document.getElementById('thread-timeline');
            const scroll = document.getElementById('thread-messages');
            if (!timeline || !scroll) return;

            timeline.querySelectorAll('[data-pending-own="1"]').forEach((el) => el.remove());

            const empty = timeline.querySelector('[data-empty-thread]');
            if (empty) empty.remove();

            const body = this.escapeHtml(rawBody || '').replace(/\n/g, '<br>');
            const name = this.escapeHtml(rawName || '');
            const html = `<div data-pending-own="1" class="flex justify-end opacity-70"><div class="max-w-[85%] sm:max-w-[80%] md:max-w-[75%] min-w-0"><div class="flex items-center justify-end gap-2 mb-1"><span class="text-[10px] text-slate-400"><?php echo e(__('ui.action.sending')); ?></span><span class="text-[11px] font-semibold text-slate-600">${name}</span></div><div class="messaging-bubble-own rounded-2xl rounded-br-sm px-4 py-2.5 text-[0.88rem] leading-relaxed text-white" style="background-color: var(--accent);">${body}</div></div></div>`;

            const div = document.createElement('div');
            div.className = 'animate-enter';
            div.innerHTML = html;
            timeline.appendChild(div);
            scroll.scrollTop = scroll.scrollHeight;
        },
        buildIncomingUserCard(e, initials, nameEscaped) {
            const cfg = window.__manexoDiscussionCard || {};
            const esc = (s) => this.escapeHtml(s ?? '');
            const badges = [];
            if (e.is_thread_creator) badges.push(cfg.creator || '');
            else if (e.thread_is_group) badges.push(cfg.inGroup || '');
            if (e.org_role_label) badges.push(e.org_role_label);
            const badgeHtml = badges.filter(Boolean).map((b) =>
                `<span class="inline-flex max-w-full items-center rounded-full bg-[var(--accent-soft)] px-2 py-0.5 text-[9px] font-bold leading-tight text-[var(--accent)] ring-1 ring-[var(--accent)]/15 sm:text-[10px] [overflow-wrap:anywhere]">${esc(b)}</span>`
            ).join('');
            const emailLine = e.user_email ? `<div class="mt-1 text-[11px] leading-snug text-slate-500 sm:text-xs [overflow-wrap:anywhere] break-all">${esc(e.user_email)}</div>` : '';
            const tagLine = e.mention_tag ? `<div class="mt-1.5 text-xs font-medium text-slate-600 [overflow-wrap:anywhere] break-all"><span class="text-slate-400">@</span>${esc(e.mention_tag)}</div>` : '';
            const badgesBlock = badgeHtml ? `<div class="mt-2 flex flex-wrap gap-1.5">${badgeHtml}</div>` : '';
            const t = esc(cfg.open || '');
            return `<details class="relative isolate shrink-0 group/avatar-pop">
<summary class="list-none cursor-pointer touch-manipulation select-none min-h-11 min-w-11 h-11 w-11 sm:h-9 sm:w-9 sm:min-h-0 sm:min-w-0 rounded-full border border-slate-100 bg-slate-100 text-slate-700 inline-flex items-center justify-center text-[10px] font-semibold shrink-0 hover:ring-2 hover:ring-[var(--accent)]/35 hover:bg-slate-50 active:scale-[0.98] transition-all [details[open]_&]:ring-2 [details[open]_&]:ring-[var(--accent)]/40 [&::-webkit-details-marker]:hidden" title="${t}" aria-label="${t}">${esc(initials)}</summary>
<div class="absolute bottom-full left-1/2 z-[100] mb-2 w-[min(20rem,calc(100vw-1.25rem))] max-w-[calc(100vw-1.25rem)] -translate-x-1/2 rounded-xl border border-slate-200 bg-white p-3 text-left shadow-xl shadow-slate-900/15 ring-1 ring-slate-900/5 sm:left-0 sm:translate-x-0 sm:max-w-[min(20rem,calc(100vw-2rem))]" style="padding-left:max(0.75rem,env(safe-area-inset-left,0px));padding-right:max(0.75rem,env(safe-area-inset-right,0px))" onclick="event.stopPropagation()">
<div class="text-sm font-semibold leading-snug text-slate-900 [overflow-wrap:anywhere] break-words">${nameEscaped}</div>
${emailLine}
${tagLine}
${badgesBlock}
</div>
</details>`;
        },
        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text ?? '';
            return div.innerHTML;
        }
    }));
</script>
    <?php
        $__output = ob_get_clean();

        \Livewire\store($this)->push('scripts', $__output, $__scriptKey)
    ?><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\discussions\thread.blade.php ENDPATH**/ ?>