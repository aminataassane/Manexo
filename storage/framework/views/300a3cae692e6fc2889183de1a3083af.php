<div
    class="relative"
    x-data="{ open: false }"
    @click.window="if (open && ! $event.target.closest('.manexo-notifications-bell-scope')) open = false"
    @keydown.escape.window="open = false"
    wire:poll.50s.visible="refreshBellAndSidebarBadges"
>
    
    <div class="manexo-notifications-bell-scope">
        <button
            type="button"
            @click="open = !open; if (open) $wire.loadNotifications()"
            :aria-expanded="open"
            aria-haspopup="dialog"
            aria-controls="manexo-notifications-panel"
            class="relative flex h-9 w-9 items-center justify-center rounded-full text-slate-500 transition-all hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-[var(--accent)] focus:ring-offset-2"
            title="<?php echo e(__('Notifications')); ?>"
        >
            <iconify-icon icon="solar:bell-linear" width="20"></iconify-icon>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->unreadCount > 0): ?>
                <span class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                </span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </button>
    </div>

    <template x-teleport="body">
        <div class="manexo-notifications-bell-scope" x-show="open" x-cloak>
            <div class="fixed inset-0 z-[260]">
                <div
                    class="absolute inset-0 bg-slate-900/45 backdrop-blur-[2px] motion-safe:transition-opacity"
                    @click="open = false"
                    aria-hidden="true"
                ></div>

                
                <div
                    class="pointer-events-none absolute inset-0 z-10 flex max-md:items-end md:items-start md:justify-end md:px-3 md:pt-[calc(4rem+env(safe-area-inset-top,0px)+0.5rem)] lg:px-5"
                >
                    <div
                        id="manexo-notifications-panel"
                        role="dialog"
                        aria-modal="true"
                        aria-labelledby="manexo-notifications-panel-title"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-8 md:translate-y-2 md:scale-[0.98]"
                        x-transition:enter-end="opacity-100 translate-y-0 md:scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-6 md:translate-y-1"
                        class="pointer-events-auto flex max-h-[min(88dvh,calc(100dvh-env(safe-area-inset-top,0px)-env(safe-area-inset-bottom,0px)-0.5rem))] w-full min-w-0 flex-col overflow-hidden border border-slate-200 bg-white shadow-2xl ring-1 ring-black/5 max-md:max-h-[min(88dvh,calc(100dvh-env(safe-area-inset-bottom,0px)-0.5rem))] max-md:rounded-b-none max-md:rounded-t-2xl md:max-h-[min(80vh,calc(100dvh-5rem))] md:w-[min(26rem,calc(100vw-1.5rem-env(safe-area-inset-left,0px)-env(safe-area-inset-right,0px)))] md:rounded-2xl"
                    >
                        <div class="flex shrink-0 flex-col gap-2 border-b border-slate-100 bg-slate-50/80 px-3 py-3 backdrop-blur-sm sm:px-4 sm:py-3.5">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0 flex flex-1 flex-wrap items-center gap-2">
                                    <h2 id="manexo-notifications-panel-title" class="text-sm font-bold text-slate-900"><?php echo e(__('Notifications')); ?></h2>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->unreadCount > 0): ?>
                                        <span class="inline-flex items-center rounded-full bg-[var(--accent-soft)] px-2 py-0.5 text-xs font-semibold text-[var(--accent)]">
                                            <?php echo e($this->unreadCount); ?>

                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <button
                                    type="button"
                                    @click="open = false"
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                                    aria-label="<?php echo e(__('Fermer')); ?>"
                                >
                                    <iconify-icon icon="solar:close-circle-linear" width="22"></iconify-icon>
                                </button>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->unreadCount > 0): ?>
                                    <button
                                        type="button"
                                        wire:click="markAllAsRead"
                                        wire:loading.attr="disabled"
                                        class="inline-flex items-center rounded-lg bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-600 shadow-sm ring-1 ring-slate-200/80 transition hover:bg-slate-50 hover:text-[var(--accent)]"
                                    >
                                        <?php echo e(__('Tout lire')); ?>

                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <a
                                    href="<?php echo e(route('notifications.index')); ?>"
                                    wire:navigate
                                    @click="open = false"
                                    class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-[var(--accent)] ring-1 ring-[var(--accent-soft-2)] transition hover:bg-[var(--accent-soft)]"
                                >
                                    <iconify-icon icon="solar:list-linear" width="16"></iconify-icon>
                                    <?php echo e(__('pages.profile.view_full_history')); ?>

                                </a>
                            </div>
                        </div>

                        <div class="min-h-0 flex-1 overflow-y-auto overscroll-y-contain custom-scrollbar">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->notifications->isEmpty()): ?>
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-50 text-slate-300 mb-3">
                            <iconify-icon icon="solar:bell-off-linear" width="32"></iconify-icon>
                        </div>
                        <p class="text-sm font-medium text-slate-900"><?php echo e(__('Aucune notification')); ?></p>
                        <p class="text-xs text-slate-500 mt-1 max-w-[200px]"><?php echo e(__('Vous êtes à jour ! Profitez de votre journée.')); ?></p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <?php
                        $data = is_array($notification->data) ? $notification->data : [];
                        $nType = $data['type'] ?? 'ticket_new_message';
                        $ticketId = $data['ticket_id'] ?? null;
                        $ticketPublicId = $data['ticket_public_id'] ?? null;
                        $ticketReference = $data['ticket_reference'] ?? null;
                        $subject = $data['ticket_subject'] ?? __('Ticket');
                        $isRead = !is_null($notification->read_at);

                        // Flags
                        $isNote = false;
                        $isMention = false;
                        $isAssignee = false;
                        $isDiscussionInvite = false;
                        $isDiscussionMessage = false;
                        $isFormAssignment = false;
                        $isFormResponse = false;
                        $isFormOverdue = false;
                        $isOrgInvitation = false;
                        $isInvitationAccepted = false;
                        $messageId = null;
                        $excerpt = '';
                        $senderName = '—';
                        $notifUrl = '#';

                        // Determine display values based on notification type
                        if ($nType === 'form_assignment') {
                            $senderName = $data['assigned_by_name'] ?? '—';
                            $subject = $data['form_name'] ?? __('Formulaire');
                            $dueDate = $data['due_date'] ?? null;
                            $excerpt = $dueDate
                                ? __('Vous a assigné un formulaire — échéance :') . ' ' . $dueDate
                                : __('Vous a assigné un formulaire');
                            $isFormAssignment = true;
                            $assignmentId = $data['assignment_public_id'] ?? $data['assignment_id'] ?? null;
                            $notifUrl = $assignmentId ? route('forms.fill', $assignmentId) : route('forms.index');
                        } elseif ($nType === 'form_response') {
                            $senderName = $data['responder_name'] ?? '—';
                            $subject = $data['form_name'] ?? __('Formulaire');
                            $excerpt = match ($data['source'] ?? 'assignment') {
                                'public' => __('forms_builder.notif_response_public'),
                                'team' => __('forms_builder.notif_response_team'),
                                default => __('forms_builder.notif_response_assignment'),
                            };
                            $isFormResponse = true;
                            $formId = $data['form_public_id'] ?? $data['form_id'] ?? null;
                            $notifUrl = $formId ? route('admin.forms.responses', $formId) : '#';
                        } elseif ($nType === 'form_overdue') {
                            $senderName = __('Système');
                            $subject = $data['form_name'] ?? __('Formulaire');
                            $dueDate = $data['due_date'] ?? null;
                            $excerpt = $dueDate
                                ? __('Formulaire en retard — échéance :') . ' ' . $dueDate
                                : __('Formulaire en retard');
                            $isFormOverdue = true;
                            $assignmentId = $data['assignment_public_id'] ?? $data['assignment_id'] ?? null;
                            $notifUrl = $assignmentId ? route('forms.fill', $assignmentId) : route('forms.index');
                        } elseif ($nType === 'task_report_shared') {
                            $senderName = $data['sender_name'] ?? '—';
                            $subject = __('task_report.shared_report_title');
                            $excerpt = __('task_report.shared_by', ['name' => $senderName]);
                            $notifUrl = $data['report_url'] ?? '#';
                        } elseif ($nType === 'ticket_assignee') {
                            $senderName = $data['assigner_name'] ?? '—';
                            $action = $data['action'] ?? 'assigned';
                            $excerpt = match($action) {
                                'assigned' => __('Vous a assigné au ticket'),
                                'unassigned' => __('Vous a retiré du ticket'),
                                'participant_added' => __('Vous a ajouté à la discussion'),
                                'participant_removed' => __('Vous a retiré de la discussion'),
                                default => __('Vous a assigné au ticket'),
                            };
                            $isAssignee = true;
                            $notifUrl = ($ticketPublicId ?? $ticketId) ? route('tickets.discussion', $ticketPublicId ?? $ticketId) : '#';
                        } elseif ($nType === 'ticket_mention') {
                            $senderName = $data['mentioner_name'] ?? '—';
                            $excerpt = $data['body_excerpt'] ?? '';
                            $isMention = true;
                            $messageId = $data['message_id'] ?? null;
                            $notifUrl = ($ticketPublicId ?? $ticketId) ? route('tickets.discussion', $ticketPublicId ?? $ticketId) : '#';
                            if ($messageId && ($ticketPublicId ?? $ticketId)) {
                                $notifUrl .= '#message-' . $messageId;
                            }
                        } elseif ($nType === 'discussion_invite') {
                            $senderName = $data['inviter_name'] ?? '—';
                            $threadName = $data['thread_name'] ?? __('Discussion');
                            $isGroup = $data['is_group'] ?? false;
                            $subject = $threadName;
                            $excerpt = $isGroup
                                ? __('Vous a ajouté au groupe')
                                : __('A démarré une conversation');
                            $isDiscussionInvite = true;
                            $threadId = $data['thread_id'] ?? null;
                            $notifUrl = $threadId ? route('discussions.index', ['discussionParam' => 'd-' . $threadId]) : '#';
                        } elseif ($nType === 'discussion_new_message') {
                            $senderName = $data['sender_name'] ?? '—';
                            $threadName = $data['thread_name'] ?? __('Discussion');
                            $subject = $threadName;
                            $excerpt = $data['body_excerpt'] ?? '';
                            $isDiscussionMessage = true;
                            $threadId = $data['thread_id'] ?? null;
                            $messageId = $data['message_id'] ?? null;
                            $notifUrl = $threadId ? route('discussions.index', ['discussionParam' => 'd-' . $threadId]) : '#';
                        } elseif ($nType === 'organization_invitation') {
                            $senderName = $data['inviter_name'] ?? '—';
                            $subject = $data['organization_name'] ?? __('Organisation');
                            $role = $data['role'] ?? 'member';
                            $excerpt = __('invitations.notif_excerpt', ['role' => $role]);
                            $isOrgInvitation = true;
                            $token = $data['invitation_token'] ?? null;
                            $notifUrl = $token ? route('invitations.accept', ['token' => $token]) : '#';
                        } elseif ($nType === 'invitation_accepted') {
                            $senderName = $data['accepted_by_name'] ?? '—';
                            $subject = $data['organization_name'] ?? __('Organisation');
                            $role = $data['role'] ?? 'member';
                            $excerpt = __('invitations.accepted_notif_excerpt', ['role' => $role, 'org' => $subject]);
                            $isInvitationAccepted = true;
                            $notifUrl = route('admin.users');
                        } else {
                            $senderName = $data['sender_name'] ?? '—';
                            $excerpt = $data['body_excerpt'] ?? '';
                            $isNote = $data['is_internal_note'] ?? false;
                            $messageId = $data['message_id'] ?? null;
                            $notifUrl = ($ticketPublicId ?? $ticketId) ? route('tickets.discussion', $ticketPublicId ?? $ticketId) : '#';
                            if ($messageId && ($ticketPublicId ?? $ticketId)) {
                                $notifUrl .= '#message-' . $messageId;
                            }
                        }
                    ?>
                    <a
                        <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processElementKey('notif-{{ $notification->id }}', get_defined_vars()); ?>wire:key="notif-<?php echo e($notification->id); ?>"
                        href="<?php echo e($notifUrl); ?>"
                        wire:click="markAsRead('<?php echo e($notification->id); ?>')"
                        @click="open = false"
                        class="group block border-b border-slate-50 px-3 py-3.5 transition-all hover:bg-slate-50 sm:px-4 <?php echo e($isRead ? 'opacity-60 hover:opacity-100' : 'bg-white'); ?>"
                    >
                        <div class="flex gap-3.5">
                            <div class="relative mt-1 shrink-0">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($nType === 'task_report_shared'): ?>
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                                        <iconify-icon icon="solar:checklist-bold-duotone" width="18"></iconify-icon>
                                    </div>
                                <?php elseif($isFormAssignment): ?>
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 ring-1 ring-indigo-100">
                                        <iconify-icon icon="solar:clipboard-add-bold-duotone" width="18"></iconify-icon>
                                    </div>
                                <?php elseif($isFormResponse): ?>
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-teal-50 text-teal-600 ring-1 ring-teal-100">
                                        <iconify-icon icon="solar:clipboard-check-bold-duotone" width="18"></iconify-icon>
                                    </div>
                                <?php elseif($isFormOverdue): ?>
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-orange-50 text-orange-600 ring-1 ring-orange-100">
                                        <iconify-icon icon="solar:alarm-bold-duotone" width="18"></iconify-icon>
                                    </div>
                                <?php elseif($isNote): ?>
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-50 text-amber-600 ring-1 ring-amber-100">
                                        <iconify-icon icon="solar:lock-keyhole-bold-duotone" width="18"></iconify-icon>
                                    </div>
                                <?php elseif($isMention): ?>
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-violet-50 text-violet-600 ring-1 ring-violet-100">
                                        <span class="text-sm font-bold">@</span>
                                    </div>
                                <?php elseif($isAssignee): ?>
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                                        <iconify-icon icon="solar:user-check-bold-duotone" width="18"></iconify-icon>
                                    </div>
                                <?php elseif($isDiscussionInvite): ?>
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-50 text-blue-600 ring-1 ring-blue-100">
                                        <iconify-icon icon="solar:users-group-rounded-bold-duotone" width="18"></iconify-icon>
                                    </div>
                                <?php elseif($isDiscussionMessage): ?>
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-sky-50 text-sky-600 ring-1 ring-sky-100">
                                        <iconify-icon icon="solar:chat-round-dots-bold-duotone" width="18"></iconify-icon>
                                    </div>
                                <?php elseif($isOrgInvitation): ?>
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-cyan-50 text-cyan-600 ring-1 ring-cyan-100">
                                        <iconify-icon icon="solar:letter-bold-duotone" width="18"></iconify-icon>
                                    </div>
                                <?php elseif($isInvitationAccepted): ?>
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                                        <iconify-icon icon="solar:user-plus-bold-duotone" width="18"></iconify-icon>
                                    </div>
                                <?php else: ?>
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-[var(--accent-soft)] text-[var(--accent)] ring-1 ring-[var(--accent-soft)]">
                                        <iconify-icon icon="solar:chat-round-dots-bold-duotone" width="18"></iconify-icon>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isRead): ?>
                                    <span class="absolute -top-0.5 -right-0.5 h-2.5 w-2.5 rounded-full border-2 border-white bg-blue-500"></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-baseline justify-between gap-2">
                                    <p class="text-sm font-semibold text-slate-900 truncate">
                                        <?php echo e($senderName); ?>

                                    </p>
                                    <span class="text-[10px] text-slate-400 shrink-0"><?php echo e($notification->created_at->diffForHumans(short: true)); ?></span>
                                </div>

                                <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1.5">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($nType === 'task_report_shared'): ?>
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100"><?php echo e(__('Rapport')); ?></span>
                                    <?php elseif($isFormAssignment): ?>
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium bg-indigo-50 text-indigo-700 border border-indigo-100"><?php echo e(__('Formulaire')); ?></span>
                                    <?php elseif($isFormResponse): ?>
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium bg-teal-50 text-teal-700 border border-teal-100"><?php echo e(__('Réponse')); ?></span>
                                    <?php elseif($isFormOverdue): ?>
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium bg-orange-50 text-orange-700 border border-orange-100"><?php echo e(__('En retard')); ?></span>
                                    <?php elseif($isNote): ?>
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100"><?php echo e(__('Note interne')); ?></span>
                                    <?php elseif($isMention): ?>
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium bg-violet-50 text-violet-700 border border-violet-100"><?php echo e(__('Mention')); ?></span>
                                    <?php elseif($isAssignee): ?>
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100"><?php echo e(__('Assignation')); ?></span>
                                    <?php elseif($isDiscussionInvite): ?>
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-100"><?php echo e(__('Discussion')); ?></span>
                                    <?php elseif($isDiscussionMessage): ?>
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-100"><?php echo e(__('Message')); ?></span>
                                    <?php elseif($isOrgInvitation): ?>
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium bg-cyan-50 text-cyan-700 border border-cyan-100"><?php echo e(__('Invitation')); ?></span>
                                    <?php elseif($isInvitationAccepted): ?>
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100"><?php echo e(__('invitations.accepted_badge')); ?></span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200"><?php echo e($ticketReference ?? 'Ticket #' . $ticketId); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <span class="truncate font-medium text-slate-700"><?php echo e($subject); ?></span>
                                </p>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($excerpt): ?>
                                    <p class="mt-1.5 text-xs text-slate-600 line-clamp-2 leading-relaxed group-hover:text-slate-900 transition-colors">
                                        <?php echo e($excerpt); ?>

                                    </p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
            
                        <div class="shrink-0 border-t border-slate-100 bg-slate-50/95 p-2 sm:p-3">
                            <a
                                href="<?php echo e(route('notifications.index')); ?>"
                                wire:navigate
                                @click="open = false"
                                class="flex w-full items-center justify-center gap-2 rounded-xl py-2.5 text-xs font-semibold text-slate-700 transition hover:bg-white hover:text-[var(--accent)] hover:shadow-sm"
                            >
                                <iconify-icon icon="solar:history-linear" width="18" class="text-slate-400"></iconify-icon>
                                <?php echo e(__('pages.profile.view_full_history')); ?>

                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\notifications-bell.blade.php ENDPATH**/ ?>