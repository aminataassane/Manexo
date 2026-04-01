<?php
    $statusLabels = [
        'open' => __('tickets.status.open'),
        'in_progress' => __('tickets.status.in_progress'),
        'pending' => __('tickets.status.pending'),
        'resolved' => __('tickets.status.resolved'),
        'closed' => __('tickets.status.closed'),
    ];
    $statusPill = [
        'open' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200'],
        'in_progress' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200'],
        'pending' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'border' => 'border-violet-200'],
        'resolved' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200'],
        'closed' => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200'],
    ];
    $isActive = fn (string $k) => ($viewKey ?? 'all') === $k;
    $chipBase = 'shrink-0 px-3 py-1.5 rounded-full text-[12px] font-semibold transition-all whitespace-nowrap cursor-pointer';
    $chipActive = 'text-white shadow-sm';
    $chipInactive = 'text-[#6B7280] bg-[#F3F4F6] hover:bg-[#E5E7EB]';
?>

<div
    class="messaging-full-bleed"
    x-data="{
        openDiscussion() { $wire.openNewDiscussionModal(); },
        openGroup()      { $wire.openNewGroupModal(); },
    }"
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($loadStage >= 2): ?>
    <div class="messaging-shell">
        
        <div class="messaging-sidebar <?php echo e(($selectedThread ?? null) || ($selectedTicket ?? null) ? 'hidden md:flex' : 'flex'); ?>">
            
            <div class="shrink-0 px-4 py-3 flex items-center justify-between">
                <h1 class="text-lg font-bold text-[#111827] tracking-tight"><?php echo e(__('pages.discussions.title')); ?></h1>
                <div class="flex items-center gap-1">
                    <button type="button" @click="openDiscussion()" class="h-8 w-8 rounded-lg text-[#6B7280] hover:bg-[#F3F4F6] hover:text-[var(--accent)] transition-colors inline-flex items-center justify-center" title="<?php echo e(__('pages.discussions.new_discussion')); ?>">
                        <iconify-icon icon="solar:user-plus-linear" width="18"></iconify-icon>
                    </button>
                    <button type="button" @click="openGroup()" class="h-8 w-8 rounded-lg text-[#6B7280] hover:bg-[#F3F4F6] hover:text-[var(--accent)] transition-colors inline-flex items-center justify-center" title="<?php echo e(__('pages.discussions.create_group')); ?>">
                        <iconify-icon icon="solar:users-group-two-rounded-linear" width="18"></iconify-icon>
                    </button>
                </div>
            </div>

            
            <div class="shrink-0 px-3 pb-2">
                <div class="relative">
                    <iconify-icon icon="solar:magnifer-linear" class="absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF]" width="15"></iconify-icon>
                    <input
                        type="text"
                        wire:model.live.debounce.500ms="search"
                        placeholder="<?php echo e(__('pages.discussions.search')); ?>"
                        wire:loading.attr="disabled"
                        wire:target="search,setScope,setView"
                        class="w-full h-9 pl-9 pr-3 text-[13px] text-[#111827] placeholder:text-[#9CA3AF] bg-[#F1F5F9] border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-[var(--accent)]/20"
                    />
                </div>
            </div>

            
            <div class="shrink-0 px-3 pb-2">
                <div class="flex items-center gap-1 rounded-xl bg-[#F1F5F9] p-1">
                    <button type="button" wire:click="setScope('threads')" wire:loading.attr="disabled" wire:target="setScope,setView,search"
                        class="flex-1 rounded-lg px-3 py-1.5 text-[12px] font-bold transition-all text-center <?php echo e(($scope ?? 'tickets') === 'threads' ? 'bg-white text-[#111827] shadow-sm' : 'text-[#6B7280] hover:text-[#111827]'); ?>">
                        <iconify-icon icon="solar:chat-round-dots-linear" width="14" class="mr-1 align-[-2px]"></iconify-icon>
                        <?php echo e(__('pages.discussions.conversations')); ?>

                    </button>
                    <button type="button" wire:click="setScope('tickets')" wire:loading.attr="disabled" wire:target="setScope,setView,search"
                        class="flex-1 rounded-lg px-3 py-1.5 text-[12px] font-bold transition-all text-center <?php echo e(($scope ?? 'tickets') === 'tickets' ? 'bg-white text-[#111827] shadow-sm' : 'text-[#6B7280] hover:text-[#111827]'); ?>">
                        <iconify-icon icon="solar:ticket-linear" width="14" class="mr-1 align-[-2px]"></iconify-icon>
                        <?php echo e(__('menu.tickets')); ?>

                    </button>
                </div>
            </div>

            
            <div class="shrink-0 px-3 pb-2 overflow-x-auto custom-scrollbar">
                <div class="flex items-center gap-1.5">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($scope ?? 'tickets') === 'threads'): ?>
                        <button type="button" wire:click="setView('direct')" wire:loading.attr="disabled" wire:target="setView,setScope,search" class="<?php echo e($chipBase); ?> <?php echo e($isActive('direct') ? $chipActive : $chipInactive); ?>" <?php if($isActive('direct')): ?> style="background-color: var(--accent);" <?php endif; ?>>
                            <?php echo e(__('pages.discussions.direct')); ?> <span class="ml-1 opacity-80"><?php echo e($viewCounts['direct'] ?? 0); ?></span>
                        </button>
                        <button type="button" wire:click="setView('groups')" wire:loading.attr="disabled" wire:target="setView,setScope,search" class="<?php echo e($chipBase); ?> <?php echo e($isActive('groups') ? $chipActive : $chipInactive); ?>" <?php if($isActive('groups')): ?> style="background-color: var(--accent);" <?php endif; ?>>
                            <?php echo e(__('pages.discussions.groups')); ?> <span class="ml-1 opacity-80"><?php echo e($viewCounts['thread_groups'] ?? 0); ?></span>
                        </button>
                        <button type="button" wire:click="setView('all')" wire:loading.attr="disabled" wire:target="setView,setScope,search" class="<?php echo e($chipBase); ?> <?php echo e($isActive('all') ? $chipActive : $chipInactive); ?>" <?php if($isActive('all')): ?> style="background-color: var(--accent);" <?php endif; ?>>
                            <?php echo e(__('pages.discussions.all')); ?> <span class="ml-1 opacity-80"><?php echo e($viewCounts['threads_all'] ?? 0); ?></span>
                        </button>
                    <?php else: ?>
                        <button type="button" wire:click="setView('created_by_me')" wire:loading.attr="disabled" wire:target="setView,setScope,search" class="<?php echo e($chipBase); ?> <?php echo e($isActive('created_by_me') ? $chipActive : $chipInactive); ?>" <?php if($isActive('created_by_me')): ?> style="background-color: var(--accent);" <?php endif; ?>>
                            <?php echo e(__('pages.discussions.created_by_me')); ?> <span class="ml-1 opacity-80"><?php echo e($viewCounts['created_by_me'] ?? 0); ?></span>
                        </button>
                        <button type="button" wire:click="setView('assigned_to_me')" wire:loading.attr="disabled" wire:target="setView,setScope,search" class="<?php echo e($chipBase); ?> <?php echo e($isActive('assigned_to_me') ? $chipActive : $chipInactive); ?>" <?php if($isActive('assigned_to_me')): ?> style="background-color: var(--accent);" <?php endif; ?>>
                            <?php echo e(__('pages.discussions.assigned_to_me')); ?> <span class="ml-1 opacity-80"><?php echo e($viewCounts['assigned_to_me'] ?? 0); ?></span>
                        </button>
                        <button type="button" wire:click="setView('groups')" wire:loading.attr="disabled" wire:target="setView,setScope,search" class="<?php echo e($chipBase); ?> <?php echo e($isActive('groups') ? $chipActive : $chipInactive); ?>" <?php if($isActive('groups')): ?> style="background-color: var(--accent);" <?php endif; ?>>
                            <?php echo e(__('pages.discussions.ticket_groups')); ?> <span class="ml-1 opacity-80"><?php echo e($viewCounts['groups'] ?? 0); ?></span>
                        </button>
                        <button type="button" wire:click="setView('all')" wire:loading.attr="disabled" wire:target="setView,setScope,search" class="<?php echo e($chipBase); ?> <?php echo e($isActive('all') ? $chipActive : $chipInactive); ?>" <?php if($isActive('all')): ?> style="background-color: var(--accent);" <?php endif; ?>>
                            <?php echo e(__('pages.discussions.all_tickets')); ?> <span class="ml-1 opacity-80"><?php echo e($viewCounts['all'] ?? 0); ?></span>
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($pendingMessagesCount ?? 0) > 0): ?>
                <div class="shrink-0 mx-3 mb-2 flex items-center gap-2 px-3 py-1.5 rounded-lg bg-amber-50 border border-amber-200">
                    <iconify-icon icon="solar:chat-round-dots-linear" class="text-amber-600" width="16"></iconify-icon>
                    <span class="text-[12px] font-medium text-amber-800">
                        <?php echo e($pendingMessagesCount); ?> <?php echo e($pendingMessagesCount === 1 ? __('pages.discussions.message_pending') : __('pages.discussions.messages_pending')); ?>

                    </span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div class="relative flex-1 min-h-0 overflow-y-auto custom-scrollbar" wire:poll.15s.visible>
                <div wire:loading.flex wire:target="search,setScope,setView,nextPage,previousPage,gotoPage,setPage" class="absolute inset-0 z-10 items-center justify-center bg-white/55 backdrop-blur-[1px] text-xs text-slate-500">
                    <?php echo e(__('pages.discussions.search')); ?>...
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($scope ?? 'tickets') === 'threads'): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $threads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $thread): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php
                            $lastMsg = $lastThreadMessages->get($thread->id);
                            $isSelected = $selectedThread && (int) $selectedThread->id === (int) $thread->id;
                            $participants = $thread->participants ?? collect();
                            $title = $thread->is_group
                                ? ($thread->name ?: __('pages.discussions.discussion_group'))
                                : ($participants->where('id', '!=', auth()->id())->first()?->name ?: __('pages.discussions.discussion'));
                            $unreadCount = (int) ($unreadCountByThreadId[$thread->id] ?? 0);
                        ?>
                        <a
                            href="<?php echo e(route('discussions.index', ['discussionParam' => 'd-'.$thread->id])); ?>"
                            wire:navigate
                            class="messaging-list-item <?php echo e($isSelected ? 'active' : ''); ?>"
                        >
                            <div class="relative shrink-0">
                                <div class="h-10 w-10 rounded-full flex items-center justify-center text-sm font-semibold" style="background: var(--accent-soft); color: var(--accent);">
                                    <iconify-icon icon="<?php echo e($thread->is_group ? 'solar:users-group-rounded-linear' : 'solar:user-circle-linear'); ?>" width="20"></iconify-icon>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadCount > 0): ?>
                                    <span class="absolute -top-0.5 -right-0.5 flex h-[18px] min-w-[18px] items-center justify-center rounded-full px-1 text-[10px] font-bold text-white ring-2 ring-white" style="background-color: var(--accent);"><?php echo e($unreadCount > 99 ? '99+' : $unreadCount); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-semibold text-[#111827] truncate text-[13px] leading-tight"><?php echo e($title); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lastMsg): ?>
                                        <span class="text-[10px] text-[#9CA3AF] shrink-0 whitespace-nowrap"><?php echo e($lastMsg->created_at->diffForHumans(short: true)); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lastMsg): ?>
                                    <p class="mt-0.5 text-[12px] text-[#6B7280] truncate">
                                        <span class="font-medium text-[#374151]"><?php echo e($lastMsg->user?->name ?? '—'); ?>:</span>
                                        <?php echo e(\Illuminate\Support\Str::limit(strip_tags($lastMsg->body), 40)); ?>

                                    </p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="p-8 text-center text-[13px] text-[#6B7280]">
                            <?php echo e(__('pages.discussions.no_conversation_started')); ?>

                            <div class="mt-3 flex items-center justify-center gap-2">
                                <button type="button" @click="openDiscussion()" class="cursor-pointer text-[12px] font-semibold px-3 py-1.5 rounded-lg transition-colors text-white" style="background-color: var(--accent);">
                                    <?php echo e(__('pages.discussions.new_short')); ?>

                                </button>
                                <button type="button" @click="openGroup()" class="cursor-pointer text-[12px] font-semibold px-3 py-1.5 rounded-lg border border-[#E5E7EB] text-[#374151] hover:bg-[#F9FAFB] transition-colors">
                                    <?php echo e(__('pages.discussions.group_short')); ?>

                                </button>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php else: ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php
                            $lastMsg = $lastMessages->get($ticket->id);
                            $statusKey = $ticket->status?->value ?? 'open';
                            $pill = $statusPill[$statusKey] ?? $statusPill['open'];
                            $isSelected = $selectedTicket && (int) $selectedTicket->id === (int) $ticket->id;
                        ?>
                        <?php
                            $sourceVal = $ticket->source?->value ?? ($ticket->source ?? 'platform');
                            $sourceIcon = match($sourceVal) {
                                'email' => 'solar:letter-bold-duotone',
                                'api' => 'solar:code-square-bold-duotone',
                                'form' => 'solar:document-text-bold-duotone',
                                default => 'solar:chat-round-dots-bold-duotone',
                            };
                            $sourceBg = match($sourceVal) {
                                'email' => 'background: #eff6ff; color: #2563eb;',
                                'api' => 'background: #f5f3ff; color: #7c3aed;',
                                'form' => 'background: #fefce8; color: #ca8a04;',
                                default => 'background: var(--accent-soft); color: var(--accent);',
                            };
                        ?>
                        <a
                            href="<?php echo e(route('discussions.index', ['discussionParam' => $ticket->public_id])); ?>"
                            wire:navigate
                            class="messaging-list-item <?php echo e($isSelected ? 'active' : ''); ?>"
                        >
                            <div class="shrink-0 relative">
                                <div class="h-10 w-10 rounded-full flex items-center justify-center text-sm font-semibold" style="<?php echo e($sourceBg); ?>">
                                    <iconify-icon icon="<?php echo e($sourceIcon); ?>" width="20"></iconify-icon>
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-semibold text-[#111827] truncate text-[13px] leading-tight"><?php echo e($ticket->subject); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lastMsg): ?>
                                        <span class="text-[10px] text-[#9CA3AF] shrink-0 whitespace-nowrap"><?php echo e($lastMsg->created_at->diffForHumans(short: true)); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="inline-flex items-center rounded-full px-1.5 py-0.5 text-[10px] font-medium <?php echo e($pill['bg']); ?> <?php echo e($pill['text']); ?>"><?php echo e($statusLabels[$statusKey] ?? $statusKey); ?></span>
                                    <span class="text-[10px] font-mono text-[#9CA3AF]"><?php echo e($ticket->shortReference()); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sourceVal !== 'platform'): ?>
                                        <span class="text-[9px] font-medium uppercase tracking-wide <?php echo e(match($sourceVal) { 'email' => 'text-blue-500', 'api' => 'text-violet-500', 'form' => 'text-yellow-600', default => 'text-slate-400' }); ?>"><?php echo e($sourceVal); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lastMsg): ?>
                                    <p class="mt-0.5 text-[12px] text-[#6B7280] truncate">
                                        <span class="font-medium text-[#374151]"><?php echo e($lastMsg->user?->name ?? '—'); ?>:</span>
                                        <?php echo e(\Illuminate\Support\Str::limit(strip_tags($lastMsg->body), 40)); ?>

                                    </p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="p-8 text-center text-[13px] text-[#6B7280]">
                            <?php echo e(__('pages.discussions.no_discussion_in_view')); ?>

                            <div class="mt-3 flex items-center justify-center gap-2">
                                <button type="button" @click="openDiscussion()" class="cursor-pointer text-[12px] font-semibold px-3 py-1.5 rounded-lg transition-colors text-white" style="background-color: var(--accent);">
                                    <?php echo e(__('pages.discussions.new_short')); ?>

                                </button>
                                <button type="button" @click="openGroup()" class="cursor-pointer text-[12px] font-semibold px-3 py-1.5 rounded-lg border border-[#E5E7EB] text-[#374151] hover:bg-[#F9FAFB] transition-colors">
                                    <?php echo e(__('pages.discussions.group_short')); ?>

                                </button>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($scope ?? 'tickets') === 'threads'): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($threads->hasPages()): ?>
                    <div class="shrink-0 p-2 border-t border-slate-100">
                        <?php echo e($threads->links('vendor.pagination.manexo')); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php else: ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tickets->hasPages()): ?>
                    <div class="shrink-0 p-2 border-t border-slate-100">
                        <?php echo e($tickets->links('vendor.pagination.manexo')); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="flex-1 min-w-0 flex flex-col overflow-hidden <?php echo e((!$selectedThread && !$selectedTicket) ? 'hidden md:flex' : ''); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedThread): ?>
                <div class="flex-1 min-h-0 overflow-hidden flex flex-col" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processElementKey('thread-{{ $selectedThread->id }}', get_defined_vars()); ?>wire:key="thread-<?php echo e($selectedThread->id); ?>">
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('discussions.thread', ['thread' => $selectedThread->id, 'embedded' => true]);

$key = null;
$__componentSlots = [];

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-4167591828-0', $key);

$__html = app('livewire')->mount($__name, $__params, $key, $__componentSlots);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
                </div>
            <?php elseif($selectedTicket): ?>
                <div class="flex-1 min-h-0 overflow-hidden" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processElementKey('discussion-{{ $selectedTicket->id }}', get_defined_vars()); ?>wire:key="discussion-<?php echo e($selectedTicket->id); ?>">
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('tickets.discussion', ['ticket' => $selectedTicket->public_id, 'embedded' => true]);

$key = null;
$__componentSlots = [];

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-4167591828-1', $key);

$__html = app('livewire')->mount($__name, $__params, $key, $__componentSlots);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
                </div>
            <?php else: ?>
                
                <div class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-[#FAFBFC]">
                    <div class="inline-flex h-16 w-16 items-center justify-center rounded-full text-[#C4C9D4] mb-5" style="background: var(--accent-soft);">
                        <iconify-icon icon="solar:chat-round-dots-linear" width="32" style="color: var(--accent); opacity: 0.6;"></iconify-icon>
                    </div>
                    <h2 class="text-base font-semibold text-[#111827]"><?php echo e(__('pages.discussions.select_discussion')); ?></h2>
                    <p class="mt-1.5 text-sm text-[#6B7280] max-w-xs"><?php echo e(__('pages.discussions.select_discussion_help')); ?></p>
                    <div class="mt-5 flex items-center gap-2">
                        <button type="button" @click="openDiscussion()" class="cursor-pointer h-9 px-4 text-white text-[13px] font-semibold rounded-xl shadow-sm transition-colors inline-flex items-center gap-2 bg-[color:var(--accent)] hover:bg-[color:color-mix(in_srgb,var(--accent)_85%,black)]">
                            <iconify-icon icon="solar:user-plus-linear" width="15"></iconify-icon>
                            <?php echo e(__('pages.discussions.new_discussion')); ?>

                        </button>
                        <button type="button" @click="openGroup()" class="cursor-pointer h-9 px-4 bg-white border border-[#E5E7EB] text-[#374151] text-[13px] font-semibold rounded-xl hover:bg-[#F9FAFB] transition inline-flex items-center gap-2">
                            <iconify-icon icon="solar:users-group-two-rounded-linear" width="15"></iconify-icon>
                            <?php echo e(__('pages.discussions.create_group')); ?>

                        </button>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php else: ?>
        <div class="messaging-shell items-center justify-center">
            <?php if (isset($component)) { $__componentOriginalaf396ce572a47c4e4be638fe5b46798c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaf396ce572a47c4e4be638fe5b46798c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-skeleton','data' => ['variant' => 'list']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-skeleton'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'list']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalaf396ce572a47c4e4be638fe5b46798c)): ?>
<?php $attributes = $__attributesOriginalaf396ce572a47c4e4be638fe5b46798c; ?>
<?php unset($__attributesOriginalaf396ce572a47c4e4be638fe5b46798c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalaf396ce572a47c4e4be638fe5b46798c)): ?>
<?php $component = $__componentOriginalaf396ce572a47c4e4be638fe5b46798c; ?>
<?php unset($__componentOriginalaf396ce572a47c4e4be638fe5b46798c); ?>
<?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showNewDiscussionModal): ?>
    <div class="fixed inset-0 z-[9999] overflow-y-auto" aria-modal="true" x-data="{ s: '' }">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/40 transition-opacity" wire:click="closeNewDiscussionModal"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full border border-[#E5E7EB] overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-start justify-between gap-3">
                <div>
                    <h3 class="text-base font-semibold text-[#111827]"><?php echo e(__('pages.discussions.new_discussion')); ?></h3>
                    <p class="mt-0.5 text-sm text-[#6B7280]"><?php echo e(__('pages.discussions.choose_user')); ?></p>
                </div>
                <button type="button" wire:click="closeNewDiscussionModal" class="h-8 w-8 rounded-lg text-[#6B7280] hover:bg-[#F3F4F6] hover:text-[#111827] transition flex items-center justify-center">
                    <iconify-icon icon="solar:close-circle-linear" width="18"></iconify-icon>
                </button>
            </div>

            <div class="p-5 space-y-4">
                <div class="relative">
                    <iconify-icon icon="solar:magnifer-linear" class="absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF]" width="17"></iconify-icon>
                    <input
                        type="text"
                        x-model="s"
                        placeholder="<?php echo e(__('pages.discussions.search_user')); ?>"
                        class="w-full h-10 pl-10 pr-3 text-[13px] text-[#111827] placeholder:text-[#9CA3AF] bg-[#F1F5F9] border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-[color:var(--accent)]/20"
                        autofocus
                    />
                </div>

                <div class="max-h-72 overflow-y-auto custom-scrollbar space-y-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orgUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <label x-show="!s || '<?php echo e(strtolower(e($u->name ?? ''))); ?>'.includes(s.toLowerCase()) || '<?php echo e(strtolower(e($u->email ?? ''))); ?>'.includes(s.toLowerCase())" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-[#F8FAFC] cursor-pointer transition">
                            <input type="radio" name="new_discussion_user" wire:model="newDiscussionUserId" value="<?php echo e($u->id); ?>" class="h-4 w-4 text-[color:var(--accent)] focus:ring-[color:var(--accent)]/30">
                            <div class="h-9 w-9 rounded-full flex items-center justify-center text-sm font-semibold shrink-0" style="background: var(--accent-soft); color: var(--accent);">
                                <?php echo e(strtoupper(mb_substr($u->name ?? '?', 0, 1))); ?>

                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-semibold text-[#111827] truncate"><?php echo e($u->name); ?></div>
                                <div class="text-xs text-[#6B7280] truncate"><?php echo e($u->email); ?></div>
                            </div>
                        </label>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="py-10 text-center text-sm text-[#6B7280]">
                            <?php echo e(__('pages.discussions.no_user_found')); ?>

                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newDiscussionUserId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-[12px] text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" wire:click="closeNewDiscussionModal" class="h-9 px-4 text-[#374151] text-[13px] font-semibold rounded-xl hover:bg-[#F3F4F6] transition" wire:loading.attr="disabled">
                    <?php echo e(__('pages.discussions.cancel')); ?>

                </button>
                <button type="button" wire:click="createDiscussionWithUser" wire:loading.attr="disabled" class="h-9 px-4 text-white text-[13px] font-semibold rounded-xl bg-[color:var(--accent)] hover:bg-[color:color-mix(in_srgb,var(--accent)_85%,black)] transition inline-flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed min-w-[100px]">
                    <span wire:loading.remove wire:target="createDiscussionWithUser"><?php echo e(__('pages.discussions.start')); ?></span>
                    <span wire:loading wire:target="createDiscussionWithUser" class="inline-block h-4 w-4 rounded-full border-2 border-white border-t-transparent animate-spin"></span>
                </button>
            </div>
        </div>
    </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showNewGroupModal): ?>
    <div class="fixed inset-0 z-[9999] overflow-y-auto" aria-modal="true" x-data="{ s: '' }">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/40 transition-opacity" wire:click="closeNewGroupModal"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full border border-[#E5E7EB] overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-start justify-between gap-3">
                <div>
                    <h3 class="text-base font-semibold text-[#111827]"><?php echo e(__('pages.discussions.create_discussion_group')); ?></h3>
                    <p class="mt-0.5 text-sm text-[#6B7280]"><?php echo e(__('pages.discussions.create_group_help')); ?></p>
                </div>
                <button type="button" wire:click="closeNewGroupModal" class="h-8 w-8 rounded-lg text-[#6B7280] hover:bg-[#F3F4F6] hover:text-[#111827] transition flex items-center justify-center">
                    <iconify-icon icon="solar:close-circle-linear" width="18"></iconify-icon>
                </button>
            </div>

            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-[13px] font-medium text-[#374151] mb-1"><?php echo e(__('pages.discussions.group_name_optional')); ?></label>
                    <input type="text" wire:model.defer="newGroupName" placeholder="<?php echo e(__('pages.discussions.group_name_placeholder')); ?>" class="w-full h-10 px-3 text-[13px] bg-[#F1F5F9] border-0 rounded-xl focus:ring-2 focus:ring-[color:var(--accent)]/20" />
                </div>

                <div class="relative">
                    <iconify-icon icon="solar:magnifer-linear" class="absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF]" width="17"></iconify-icon>
                    <input
                        type="text"
                        x-model="s"
                        placeholder="<?php echo e(__('pages.discussions.search_participants')); ?>"
                        class="w-full h-10 pl-10 pr-3 text-[13px] text-[#111827] placeholder:text-[#9CA3AF] bg-[#F1F5F9] border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-[color:var(--accent)]/20"
                    />
                </div>

                <div class="max-h-72 overflow-y-auto custom-scrollbar space-y-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $orgUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <label x-show="!s || '<?php echo e(strtolower(e($u->name ?? ''))); ?>'.includes(s.toLowerCase()) || '<?php echo e(strtolower(e($u->email ?? ''))); ?>'.includes(s.toLowerCase())" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-[#F8FAFC] cursor-pointer transition">
                            <input type="checkbox" wire:model="newGroupUserIds" value="<?php echo e($u->id); ?>" class="h-4 w-4 rounded border-[#E5E7EB] text-[color:var(--accent)] focus:ring-[color:var(--accent)]/30">
                            <div class="h-9 w-9 rounded-full flex items-center justify-center text-sm font-semibold shrink-0" style="background: var(--accent-soft); color: var(--accent);">
                                <?php echo e(strtoupper(mb_substr($u->name ?? '?', 0, 1))); ?>

                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-semibold text-[#111827] truncate"><?php echo e($u->name); ?></div>
                                <div class="text-xs text-[#6B7280] truncate"><?php echo e($u->email); ?></div>
                            </div>
                        </label>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newGroupUserIds'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-[12px] text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" wire:click="closeNewGroupModal" class="h-9 px-4 text-[#374151] text-[13px] font-semibold rounded-xl hover:bg-[#F3F4F6] transition" wire:loading.attr="disabled">
                    <?php echo e(__('pages.discussions.cancel')); ?>

                </button>
                <button type="button" wire:click="createGroupDiscussion" wire:loading.attr="disabled" class="h-9 px-4 text-white text-[13px] font-semibold rounded-xl bg-[color:var(--accent)] hover:bg-[color:color-mix(in_srgb,var(--accent)_85%,black)] transition inline-flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed min-w-[120px]">
                    <span wire:loading.remove wire:target="createGroupDiscussion"><?php echo e(__('pages.discussions.create_the_group')); ?></span>
                    <span wire:loading wire:target="createGroupDiscussion" class="inline-block h-4 w-4 rounded-full border-2 border-white border-t-transparent animate-spin"></span>
                </button>
            </div>
        </div>
    </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/livewire/discussions/index.blade.php ENDPATH**/ ?>