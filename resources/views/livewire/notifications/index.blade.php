@php
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

        $icon = 'solar:chat-round-dots-linear';
        if ($nType === 'form_assignment') {
            $icon = 'solar:clipboard-add-linear';
            $senderName = $data['assigned_by_name'] ?? '—';
            $subject = $data['form_name'] ?? __('Formulaire');
            $dueDate = $data['due_date'] ?? null;
            $excerpt = $dueDate ? __('Échéance :') . ' ' . $dueDate : __('Formulaire assigné');
            $assignmentId = $data['assignment_public_id'] ?? $data['assignment_id'] ?? null;
            $notifUrl = $assignmentId ? route('forms.fill', $assignmentId) : route('forms.index');
        } elseif ($nType === 'form_response') {
            $icon = 'solar:clipboard-check-linear';
            $senderName = $data['responder_name'] ?? '—';
            $subject = $data['form_name'] ?? __('Formulaire');
            $excerpt = match ($data['source'] ?? 'assignment') { 'public' => __('Réponse (public)'), 'team' => __('Réponse (équipe)'), default => __('Nouvelle réponse'), };
            $formId = $data['form_public_id'] ?? $data['form_id'] ?? null;
            $notifUrl = $formId ? route('admin.forms.responses', $formId) : '#';
        } elseif ($nType === 'form_overdue') {
            $icon = 'solar:alarm-linear';
            $senderName = __('Système');
            $subject = $data['form_name'] ?? __('Formulaire');
            $dueDate = $data['due_date'] ?? null;
            $excerpt = $dueDate ? __('Échéance :') . ' ' . $dueDate : __('En retard');
            $assignmentId = $data['assignment_public_id'] ?? $data['assignment_id'] ?? null;
            $notifUrl = $assignmentId ? route('forms.fill', $assignmentId) : route('forms.index');
        } elseif ($nType === 'task_report_shared') {
            $icon = 'solar:checklist-linear';
            $senderName = $data['sender_name'] ?? '—';
            $subject = __('task_report.shared_report_title');
            $excerpt = __('task_report.shared_by', ['name' => $senderName]);
            $notifUrl = $data['report_url'] ?? '#';
        } elseif ($nType === 'ticket_assignee') {
            $icon = 'solar:user-check-linear';
            $senderName = $data['assigner_name'] ?? '—';
            $action = $data['action'] ?? 'assigned';
            $excerpt = match($action) { 'assigned' => __('Vous a assigné'), 'unassigned' => __('Vous a retiré'), 'participant_added' => __('Vous a ajouté'), 'participant_removed' => __('Vous a retiré'), default => __('Assignation'), };
            $notifUrl = $ticketRouteKey ? route('tickets.discussion', $ticketRouteKey) : '#';
        } elseif ($nType === 'ticket_mention') {
            $icon = 'solar:mention-circle-linear';
            $senderName = $data['mentioner_name'] ?? '—';
            $excerpt = $data['body_excerpt'] ?? '';
            $messageId = $data['message_id'] ?? null;
            $notifUrl = $ticketRouteKey ? route('tickets.discussion', $ticketRouteKey) : '#';
            if ($messageId && $ticketRouteKey) { $notifUrl .= '#message-' . $messageId; }
        } elseif ($nType === 'discussion_invite') {
            $icon = 'solar:users-group-rounded-linear';
            $senderName = $data['inviter_name'] ?? '—';
            $subject = $data['thread_name'] ?? __('Discussion');
            $excerpt = ($data['is_group'] ?? false) ? __('Vous a ajouté au groupe') : __('Nouvelle conversation');
            $threadId = $data['thread_id'] ?? null;
            $notifUrl = $threadId ? route('discussions.index', ['ticket' => 'd-' . $threadId]) : '#';
        } elseif ($nType === 'discussion_new_message') {
            $icon = 'solar:chat-round-dots-linear';
            $senderName = $data['sender_name'] ?? '—';
            $subject = $data['thread_name'] ?? __('Discussion');
            $excerpt = $data['body_excerpt'] ?? '';
            $threadId = $data['thread_id'] ?? null;
            $notifUrl = $threadId ? route('discussions.index', ['ticket' => 'd-' . $threadId]) : '#';
        } else {
            $senderName = $data['sender_name'] ?? '—';
            $excerpt = $data['body_excerpt'] ?? '';
            $messageId = $data['message_id'] ?? null;
            $notifUrl = $ticketRouteKey ? route('tickets.discussion', $ticketRouteKey) : '#';
            if ($messageId && $ticketRouteKey) { $notifUrl .= '#message-' . $messageId; }
        }
        return compact('isRead', 'senderName', 'subject', 'excerpt', 'notifUrl', 'icon');
    };
@endphp

<div class="mx-auto w-full max-w-2xl py-8 sm:py-12 px-4 sm:px-6">
    <div class="flex items-baseline justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900 tracking-tight">{{ __('pages.notifications.title') }}</h1>
            <p class="mt-0.5 text-sm text-slate-500">{{ __('pages.notifications.subtitle') }}</p>
        </div>
        @if($this->unreadCount > 0)
            <button type="button" wire:click="markAllAsRead" class="text-sm font-medium text-slate-500 hover:text-[var(--accent)] transition-colors whitespace-nowrap">
                {{ __('pages.notifications.mark_all_read') }}
            </button>
        @endif
    </div>

    <div class="flex gap-6 border-b border-slate-200 mb-6">
        <button type="button" wire:click="setFilter('all')" class="pb-3 -mb-px text-sm font-medium transition-colors {{ $filter === 'all' ? 'text-slate-900 border-b-2 border-slate-900' : 'text-slate-500 hover:text-slate-700 border-b-2 border-transparent' }}">
            {{ __('pages.notifications.all') }}
        </button>
        <button type="button" wire:click="setFilter('unread')" class="pb-3 -mb-px text-sm font-medium transition-colors {{ $filter === 'unread' ? 'text-slate-900 border-b-2 border-slate-900' : 'text-slate-500 hover:text-slate-700 border-b-2 border-transparent' }}">
            {{ __('pages.notifications.unread') }}
            @if($this->unreadCount > 0)
                <span class="ml-1 text-slate-400 font-normal">({{ $this->unreadCount }})</span>
            @endif
        </button>
    </div>

    <ul class="space-y-0">
        @forelse($this->notifications as $notification)
            @php $n = $parseNotification($notification); @endphp
            <li wire:key="notif-{{ $notification->id }}" class="group">
                <a href="{{ $n['notifUrl'] }}" wire:navigate wire:click="markAsRead('{{ $notification->id }}')" class="flex gap-3 py-3 px-1 -mx-1 rounded-lg hover:bg-slate-50 transition-colors">
                    <span class="relative mt-0.5 shrink-0 flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700 transition-colors">
                        <iconify-icon icon="{{ $n['icon'] }}" width="18"></iconify-icon>
                        @if(!$n['isRead'])
                            <span class="absolute -right-0.5 -top-0.5 h-2 w-2 rounded-full bg-[var(--accent)] ring-2 ring-white"></span>
                        @endif
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm {{ $n['isRead'] ? 'text-slate-600' : 'text-slate-900 font-medium' }} group-hover:text-slate-900 transition-colors">
                            <span>{{ $n['senderName'] }}</span>
                            <span class="text-slate-400 mx-1">·</span>
                            <span class="truncate">{{ $n['subject'] }}</span>
                        </p>
                        @if($n['excerpt'])
                            <p class="mt-0.5 text-xs text-slate-500 line-clamp-1">{{ $n['excerpt'] }}</p>
                        @endif
                    </div>
                    <time class="shrink-0 text-xs text-slate-400 mt-1.5" datetime="{{ $notification->created_at->toIso8601String() }}">{{ $notification->created_at->diffForHumans() }}</time>
                </a>
            </li>
        @empty
            <li class="pt-16 text-center">
                <p class="text-sm text-slate-500">
                    @if($filter === 'unread')
                        {{ __('pages.notifications.no_unread') }}
                    @else
                        {{ __('pages.notifications.no_notifications') }}
                    @endif
                </p>
                <p class="mt-1 text-xs text-slate-400">{{ __('pages.notifications.no_notifications_hint') }}</p>
            </li>
        @endforelse
    </ul>

    @if($this->notifications->hasPages())
        <div class="mt-8 pt-6 border-t border-slate-100">
            {{ $this->notifications->links('vendor.pagination.manexo') }}
        </div>
    @endif
</div>
