<?php
    $parseNotification = function ($notification) {
        $data = $notification->data;
        $nType = $data['type'] ?? 'ticket_new_message';
        $ticketId = $data['ticket_id'] ?? null;
        $ticketPublicId = $data['ticket_public_id'] ?? null;
        $ticketRouteKey = $ticketPublicId ?? $ticketId;
        $subject = $data['ticket_subject'] ?? __('Ticket');
        $isRead = !is_null($notification->read_at);
        $excerpt = '';
        $senderName = '—';
        $notifUrl = '#';

        $icon = 'solar:chat-round-dots-bold-duotone';
        $iconBg = 'bg-blue-50 text-blue-600';
        $category = 'tickets';
        $categoryLabel = __('Tickets');
        $categoryIcon = 'solar:ticket-bold-duotone';

        if ($nType === 'form_assignment') {
            $category = 'forms';
            $categoryLabel = __('Formulaires');
            $categoryIcon = 'solar:clipboard-bold-duotone';
            $icon = 'solar:clipboard-add-bold-duotone';
            $iconBg = 'bg-violet-50 text-violet-600';
            $senderName = $data['assigned_by_name'] ?? '—';
            $subject = $data['form_name'] ?? __('Formulaire');
            $dueDate = $data['due_date'] ?? null;
            $excerpt = $dueDate ? __('Échéance :') . ' ' . $dueDate : __('Formulaire assigné');
            $assignmentId = $data['assignment_public_id'] ?? $data['assignment_id'] ?? null;
            $notifUrl = $assignmentId ? route('forms.fill', $assignmentId) : route('forms.index');
        } elseif ($nType === 'form_response') {
            $category = 'forms';
            $categoryLabel = __('Formulaires');
            $categoryIcon = 'solar:clipboard-bold-duotone';
            $icon = 'solar:clipboard-check-bold-duotone';
            $iconBg = 'bg-emerald-50 text-emerald-600';
            $senderName = $data['responder_name'] ?? '—';
            $subject = $data['form_name'] ?? __('Formulaire');
            $excerpt = match ($data['source'] ?? 'assignment') { 'public' => __('Réponse (public)'), 'team' => __('Réponse (équipe)'), default => __('Nouvelle réponse'), };
            $formId = $data['form_public_id'] ?? $data['form_id'] ?? null;
            $notifUrl = $formId ? route('admin.forms.responses', $formId) : '#';
        } elseif ($nType === 'form_overdue') {
            $category = 'forms';
            $categoryLabel = __('Formulaires');
            $categoryIcon = 'solar:clipboard-bold-duotone';
            $icon = 'solar:alarm-bold-duotone';
            $iconBg = 'bg-red-50 text-red-600';
            $senderName = __('Système');
            $subject = $data['form_name'] ?? __('Formulaire');
            $dueDate = $data['due_date'] ?? null;
            $excerpt = $dueDate ? __('Échéance :') . ' ' . $dueDate : __('En retard');
            $assignmentId = $data['assignment_public_id'] ?? $data['assignment_id'] ?? null;
            $notifUrl = $assignmentId ? route('forms.fill', $assignmentId) : route('forms.index');
        } elseif ($nType === 'task_report_shared') {
            $category = 'reports';
            $categoryLabel = __('Rapports');
            $categoryIcon = 'solar:checklist-bold-duotone';
            $icon = 'solar:checklist-bold-duotone';
            $iconBg = 'bg-amber-50 text-amber-600';
            $senderName = $data['sender_name'] ?? '—';
            $subject = __('task_report.shared_report_title');
            $excerpt = __('task_report.shared_by', ['name' => $senderName]);
            $notifUrl = $data['report_url'] ?? '#';
        } elseif ($nType === 'ticket_assignee') {
            $category = 'tickets';
            $categoryLabel = __('Tickets');
            $categoryIcon = 'solar:ticket-bold-duotone';
            $icon = 'solar:user-check-bold-duotone';
            $iconBg = 'bg-emerald-50 text-emerald-600';
            $senderName = $data['assigner_name'] ?? '—';
            $action = $data['action'] ?? 'assigned';
            $excerpt = match($action) { 'assigned' => __('Vous a assigné'), 'unassigned' => __('Vous a retiré'), 'participant_added' => __('Vous a ajouté'), 'participant_removed' => __('Vous a retiré'), default => __('Assignation'), };
            $notifUrl = $ticketRouteKey ? route('tickets.discussion', $ticketRouteKey) : '#';
        } elseif ($nType === 'ticket_mention') {
            $category = 'tickets';
            $categoryLabel = __('Tickets');
            $categoryIcon = 'solar:ticket-bold-duotone';
            $icon = 'solar:mention-circle-bold-duotone';
            $iconBg = 'bg-orange-50 text-orange-600';
            $senderName = $data['mentioner_name'] ?? '—';
            $excerpt = $data['body_excerpt'] ?? '';
            $messageId = $data['message_id'] ?? null;
            $notifUrl = $ticketRouteKey ? route('tickets.discussion', $ticketRouteKey) : '#';
            if ($messageId && $ticketRouteKey) { $notifUrl .= '#message-' . $messageId; }
        } elseif ($nType === 'discussion_invite') {
            $category = 'discussions';
            $categoryLabel = __('Discussions');
            $categoryIcon = 'solar:chat-round-dots-bold-duotone';
            $icon = 'solar:users-group-rounded-bold-duotone';
            $iconBg = 'bg-indigo-50 text-indigo-600';
            $senderName = $data['inviter_name'] ?? '—';
            $subject = $data['thread_name'] ?? __('Discussion');
            $excerpt = ($data['is_group'] ?? false) ? __('Vous a ajouté au groupe') : __('Nouvelle conversation');
            $threadId = $data['thread_id'] ?? null;
            $notifUrl = $threadId ? route('discussions.index', ['discussionParam' => 'd-' . $threadId]) : '#';
        } elseif ($nType === 'discussion_new_message') {
            $category = 'discussions';
            $categoryLabel = __('Discussions');
            $categoryIcon = 'solar:chat-round-dots-bold-duotone';
            $icon = 'solar:chat-round-dots-bold-duotone';
            $iconBg = 'bg-blue-50 text-blue-600';
            $senderName = $data['sender_name'] ?? '—';
            $subject = $data['thread_name'] ?? __('Discussion');
            $excerpt = $data['body_excerpt'] ?? '';
            $threadId = $data['thread_id'] ?? null;
            $notifUrl = $threadId ? route('discussions.index', ['discussionParam' => 'd-' . $threadId]) : '#';
        } elseif ($nType === 'discussion_removed') {
            $category = 'discussions';
            $categoryLabel = __('Discussions');
            $categoryIcon = 'solar:chat-round-dots-bold-duotone';
            $icon = 'solar:user-cross-rounded-bold-duotone';
            $iconBg = 'bg-red-50 text-red-600';
            $senderName = $data['actor_name'] ?? '—';
            $subject = $data['thread_name'] ?? __('Discussion');
            $excerpt = $data['body_excerpt'] ?? __('Vous avez été retiré de cette discussion.');
            $notifUrl = route('discussions.index');
        } elseif ($nType === 'organization_invitation' || $nType === 'invitation_accepted') {
            $category = 'team';
            $categoryLabel = __('Équipe');
            $categoryIcon = 'solar:users-group-rounded-bold-duotone';
            $icon = 'solar:users-group-rounded-bold-duotone';
            $iconBg = 'bg-cyan-50 text-cyan-600';
            $senderName = $data['sender_name'] ?? __('Système');
            $excerpt = $data['message'] ?? ($data['body_excerpt'] ?? '');
        } elseif ($nType === 'team_role_changed') {
            $category = 'team';
            $categoryLabel = __('Équipe');
            $categoryIcon = 'solar:users-group-rounded-bold-duotone';
            $icon = 'solar:shield-user-bold-duotone';
            $iconBg = 'bg-indigo-50 text-indigo-600';
            $senderName = $data['changed_by_name'] ?? __('Système');
            $subject = $data['organization_name'] ?? __('Équipe');
            $excerpt = $data['body_excerpt'] ?? __('Votre rôle dans l’équipe a été mis à jour.');
        } elseif ($nType === 'ticket_reopened') {
            $category = 'tickets';
            $categoryLabel = __('Tickets');
            $categoryIcon = 'solar:ticket-bold-duotone';
            $icon = 'solar:restart-circle-bold-duotone';
            $iconBg = 'bg-blue-50 text-blue-600';
            $senderName = $data['actor_name'] ?? '—';
            $excerpt = $data['body_excerpt'] ?? __('Le ticket a été rouvert.');
            $notifUrl = $ticketRouteKey ? route('tickets.discussion', $ticketRouteKey) : '#';
        } elseif ($nType === 'approval_requested') {
            $category = 'tickets';
            $categoryLabel = __('Tickets');
            $categoryIcon = 'solar:ticket-bold-duotone';
            $icon = 'solar:shield-check-bold-duotone';
            $iconBg = 'bg-amber-50 text-amber-600';
            $senderName = $data['requester_name'] ?? '—';
            $excerpt = __('Approbation demandée');
            $notifUrl = $ticketRouteKey ? route('tickets.discussion', $ticketRouteKey) : '#';
        } elseif ($nType === 'approval_decision') {
            $category = 'tickets';
            $categoryLabel = __('Tickets');
            $categoryIcon = 'solar:ticket-bold-duotone';
            $decision = $data['decision'] ?? 'approved';
            $icon = $decision === 'approved' ? 'solar:shield-check-bold-duotone' : 'solar:shield-cross-bold-duotone';
            $iconBg = $decision === 'approved' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600';
            $senderName = $data['approver_name'] ?? '—';
            $excerpt = $decision === 'approved' ? __('Approuvé') : __('Rejeté');
            $notifUrl = $ticketRouteKey ? route('tickets.discussion', $ticketRouteKey) : '#';
        } elseif ($nType === 'sla_at_risk' || $nType === 'sla_breached') {
            $category = 'tickets';
            $categoryLabel = __('Tickets');
            $categoryIcon = 'solar:ticket-bold-duotone';
            $icon = $nType === 'sla_breached' ? 'solar:danger-triangle-bold-duotone' : 'solar:clock-circle-bold-duotone';
            $iconBg = $nType === 'sla_breached' ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600';
            $senderName = __('SLA');
            $excerpt = $data['body_excerpt'] ?? ($nType === 'sla_breached' ? __('SLA dépassé') : __('SLA à risque'));
            $notifUrl = $ticketRouteKey ? route('tickets.discussion', $ticketRouteKey) : '#';
        } else {
            $category = 'tickets';
            $categoryLabel = __('Tickets');
            $categoryIcon = 'solar:ticket-bold-duotone';
            $icon = 'solar:chat-round-dots-bold-duotone';
            $iconBg = 'bg-blue-50 text-blue-600';
            $senderName = $data['sender_name'] ?? '—';
            $excerpt = $data['body_excerpt'] ?? '';
            $messageId = $data['message_id'] ?? null;
            $notifUrl = $ticketRouteKey ? route('tickets.discussion', $ticketRouteKey) : '#';
            if ($messageId && $ticketRouteKey) { $notifUrl .= '#message-' . $messageId; }
        }
        $notifInternal = is_string($notifUrl) && str_starts_with($notifUrl, url('/'));
        $notifActionable = is_string($notifUrl) && $notifUrl !== '#';

        return compact('isRead', 'senderName', 'subject', 'excerpt', 'notifUrl', 'notifInternal', 'notifActionable', 'icon', 'iconBg', 'category', 'categoryLabel', 'categoryIcon');
    };

    $categoryFilters = [
        'all'         => ['label' => 'Tous',        'icon' => 'solar:layers-bold-duotone'],
        'tickets'     => ['label' => 'Tickets',     'icon' => 'solar:ticket-bold-duotone'],
        'discussions' => ['label' => 'Discussions', 'icon' => 'solar:chat-round-dots-bold-duotone'],
        'forms'       => ['label' => 'Formulaires', 'icon' => 'solar:clipboard-bold-duotone'],
        'reports'     => ['label' => 'Rapports',    'icon' => 'solar:checklist-bold-duotone'],
        'team'        => ['label' => 'Équipe',      'icon' => 'solar:users-group-rounded-bold-duotone'],
    ];
    $activeCategory = is_string($category ?? null) ? $category : 'all';
?>

<div class="w-full max-w-full min-w-0 mx-auto" wire:poll.30s>

    
    <div class="page-header">
        <div class="min-w-0">
            <h1 class="page-title"><?php echo e(__('pages.notifications.title')); ?></h1>
            <p class="page-subtitle"><?php echo e(__('pages.notifications.subtitle')); ?></p>
        </div>
        <div class="page-actions">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadCount > 0): ?>
                <span class="inline-flex items-center gap-1.5 rounded-xl border border-[var(--accent-soft-2)] bg-[var(--accent-soft)] px-3 py-2 text-xs font-bold text-[var(--accent)]">
                    <iconify-icon icon="solar:bell-bold-duotone" width="16"></iconify-icon>
                    <?php echo e($unreadCount); ?> <?php echo e(__('non lues')); ?>

                </span>
                <button
                    type="button"
                    wire:click="markAllAsRead"
                    wire:loading.attr="disabled"
                    wire:target="markAllAsRead"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 hover:border-[var(--accent)] hover:text-[var(--accent)] transition-all"
                >
                    <iconify-icon icon="solar:check-read-linear" width="16"></iconify-icon>
                    <?php echo e(__('pages.notifications.mark_all_read')); ?>

                </button>
            <?php else: ?>
                <span class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-500">
                    <iconify-icon icon="solar:check-read-bold-duotone" width="16"></iconify-icon>
                    <?php echo e(__('Tout est lu')); ?>

                </span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6 sm:mb-8 lg:grid-cols-4">
        <div class="stat-card">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label"><?php echo e(__('Non lues')); ?></span>
                    <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-slate-900"><?php echo e($unreadCount); ?></div>
                </div>
                <div class="stat-card-icon bg-red-50 text-red-600">
                    <iconify-icon icon="solar:bell-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label"><?php echo e(__('Total')); ?></span>
                    <div class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-slate-900"><?php echo e($notifications->total()); ?></div>
                </div>
                <div class="stat-card-icon bg-blue-50 text-blue-600">
                    <iconify-icon icon="solar:inbox-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label"><?php echo e(__('Filtre')); ?></span>
                    <div class="mt-1 sm:mt-2 text-lg sm:text-xl font-bold text-slate-900 truncate"><?php echo e($filter === 'unread' ? __('Non lues') : __('Toutes')); ?></div>
                </div>
                <div class="stat-card-icon bg-amber-50 text-amber-600">
                    <iconify-icon icon="solar:filter-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label"><?php echo e(__('Catégorie')); ?></span>
                    <div class="mt-1 sm:mt-2 text-lg sm:text-xl font-bold text-slate-900 truncate"><?php echo e($categoryFilters[$activeCategory]['label'] ?? 'Tous'); ?></div>
                </div>
                <div class="stat-card-icon bg-emerald-50 text-emerald-600">
                    <iconify-icon icon="<?php echo e($categoryFilters[$activeCategory]['icon'] ?? 'solar:layers-bold-duotone'); ?>" width="20"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 gap-4 sm:gap-6 lg:gap-8 lg:grid-cols-4">

        
        <div class="lg:col-span-1">
            <div class="sticky top-24 space-y-4 sm:space-y-6">

                
                <div class="sidebar-panel">
                    <div class="sidebar-panel-header">
                        <h3><?php echo e(__('Statut de lecture')); ?></h3>
                    </div>
                    <div class="sidebar-panel-body">
                        <button type="button" wire:click="setFilter('all')" wire:loading.attr="disabled" wire:target="setFilter,setCategory,nextPage,previousPage,gotoPage,setPage,markAllAsRead,markAsRead"
                            class="sidebar-item <?php echo e($filter === 'all' ? 'sidebar-item-active' : 'sidebar-item-default'); ?>">
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:inbox-bold-duotone" width="18" class="<?php echo e($filter === 'all' ? 'text-[var(--accent)]' : 'text-slate-400'); ?>"></iconify-icon>
                                <?php echo e(__('pages.notifications.all')); ?>

                            </span>
                        </button>
                        <button type="button" wire:click="setFilter('unread')" wire:loading.attr="disabled" wire:target="setFilter,setCategory,nextPage,previousPage,gotoPage,setPage,markAllAsRead,markAsRead"
                            class="sidebar-item <?php echo e($filter === 'unread' ? 'sidebar-item-active' : 'sidebar-item-default'); ?>">
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:bell-bold-duotone" width="18" class="<?php echo e($filter === 'unread' ? 'text-[var(--accent)]' : 'text-slate-400'); ?>"></iconify-icon>
                                <?php echo e(__('pages.notifications.unread')); ?>

                            </span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadCount > 0): ?>
                                <span class="sidebar-badge <?php echo e($filter === 'unread' ? 'sidebar-badge-active' : 'sidebar-badge-default'); ?>">
                                    <?php echo e($unreadCount); ?>

                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </button>
                    </div>
                </div>

                
                <div class="sidebar-panel">
                    <div class="sidebar-panel-header">
                        <h3><?php echo e(__('Catégories')); ?></h3>
                    </div>
                    <div class="sidebar-panel-body">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $categoryFilters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $catKey => $catInfo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <button type="button" wire:click="setCategory('<?php echo e($catKey); ?>')" wire:loading.attr="disabled" wire:target="setFilter,setCategory,nextPage,previousPage,gotoPage,setPage,markAllAsRead,markAsRead"
                                class="sidebar-item <?php echo e($category === $catKey ? 'sidebar-item-active' : 'sidebar-item-default'); ?>">
                                <span class="flex items-center gap-2.5">
                                    <iconify-icon icon="<?php echo e($catInfo['icon']); ?>" width="18" class="<?php echo e($category === $catKey ? 'text-[var(--accent)]' : 'text-slate-400'); ?>"></iconify-icon>
                                    <?php echo e($catInfo['label']); ?>

                                </span>
                            </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="lg:col-span-3">
            <div class="content-card relative">
                <div wire:loading.flex wire:target="setFilter,setCategory,nextPage,previousPage,gotoPage,setPage,markAllAsRead,markAsRead" class="absolute inset-0 z-10 items-center justify-center bg-white/60 backdrop-blur-[1px] text-xs text-slate-500">
                    <?php echo e(__('pages.notifications.title')); ?>...
                </div>

                
                <div class="flex items-center justify-between gap-3 p-3 sm:p-4" style="border-bottom: 1px solid #f1f5f9;">
                    <div class="flex items-center gap-2 text-sm font-medium text-slate-700">
                        <iconify-icon icon="solar:sort-from-top-to-bottom-linear" width="16" class="text-slate-400"></iconify-icon>
                        <?php echo e(__('Les plus récentes')); ?>

                    </div>
                    <div class="text-xs text-slate-400">
                        <?php echo e($notifications->total()); ?> <?php echo e(__('notification(s)')); ?>

                    </div>
                </div>

                
                <div class="divide-y divide-slate-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php $n = $parseNotification($notification); ?>
                        <a
                            href="<?php echo e($n['notifActionable'] ? $n['notifUrl'] : 'javascript:void(0)'); ?>"
                            <?php if($n['notifInternal']): ?> wire:navigate <?php endif; ?>
                            wire:click="markAsRead('<?php echo e($notification->id); ?>')"
                            wire:loading.attr="disabled"
                            wire:target="markAsRead"
                            <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processElementKey('notif-{{ $notification->id }}', get_defined_vars()); ?>wire:key="notif-<?php echo e($notification->id); ?>"
                            x-data="{ optimisticRead: false }"
                            @click="optimisticRead = true"
                            :class="optimisticRead ? 'hover:bg-slate-50/70 bg-slate-50/40' : ''"
                            class="group flex items-start gap-4 px-4 py-4 transition-all duration-200 sm:px-6 sm:py-5 <?php echo e($n['isRead'] ? 'hover:bg-slate-50/70' : 'bg-[var(--accent-soft)]/20 hover:bg-[var(--accent-soft)]/30'); ?>"
                        >
                            
                            <span class="relative mt-0.5 shrink-0 inline-flex h-11 w-11 items-center justify-center rounded-xl transition <?php echo e($n['isRead'] ? $n['iconBg'] : $n['iconBg']); ?>">
                                <iconify-icon icon="<?php echo e($n['icon']); ?>" width="20"></iconify-icon>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $n['isRead']): ?>
                                    <span x-show="!optimisticRead" x-transition.opacity.duration.200ms class="absolute -right-0.5 -top-0.5 h-2.5 w-2.5 rounded-full bg-[var(--accent)] ring-2 ring-white"></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>

                            
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between sm:gap-3">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm sm:text-[14px] leading-snug break-words <?php echo e($n['isRead'] ? 'font-medium text-slate-700 group-hover:text-slate-900' : 'font-semibold text-slate-900'); ?>">
                                            <span class="break-words"><?php echo e($n['senderName']); ?></span>
                                            <span class="mx-1.5 text-slate-300">&middot;</span>
                                            <span class="text-slate-600 break-words"><?php echo e($n['subject']); ?></span>
                                        </p>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($n['excerpt']): ?>
                                            <p class="mt-1 line-clamp-2 break-all text-[13px] leading-relaxed <?php echo e($n['isRead'] ? 'text-slate-400' : 'text-slate-500'); ?>"><?php echo e($n['excerpt']); ?></p>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0 sm:flex-col sm:items-end sm:gap-1.5">
                                        <time class="text-[11px] font-medium text-slate-400 whitespace-nowrap" datetime="<?php echo e($notification->created_at->toIso8601String()); ?>">
                                            <?php echo e($notification->created_at->diffForHumans()); ?>

                                        </time>
                                        <div class="flex items-center gap-1.5">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $n['isRead']): ?>
                                                <span x-show="!optimisticRead" x-transition.opacity.duration.200ms class="inline-flex items-center rounded-lg bg-[var(--accent-soft)] px-2 py-0.5 text-[10px] font-bold text-[var(--accent)] ring-1 ring-[var(--accent)]/10">
                                                    <?php echo e(__('Nouveau')); ?>

                                                </span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-500">
                                                <iconify-icon icon="<?php echo e($n['categoryIcon']); ?>" width="10"></iconify-icon>
                                                <?php echo e($n['categoryLabel']); ?>

                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <iconify-icon icon="solar:bell-off-bold-duotone" width="28" class="text-slate-300"></iconify-icon>
                            </div>
                            <p class="empty-state-title">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filter === 'unread'): ?>
                                    <?php echo e(__('pages.notifications.no_unread')); ?>

                                <?php else: ?>
                                    <?php echo e(__('pages.notifications.no_notifications')); ?>

                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                            <p class="empty-state-text"><?php echo e(__('pages.notifications.no_notifications_hint')); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notifications->hasPages()): ?>
                    <div class="overflow-x-auto px-4 py-4 sm:px-6" style="border-top: 1px solid #f1f5f9;">
                        <?php echo e($notifications->links('vendor.pagination.manexo')); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

</div>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/livewire/notifications/index.blade.php ENDPATH**/ ?>