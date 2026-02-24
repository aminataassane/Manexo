<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/**
 * Notification en base uniquement (sans file) pour que le badge et la cloche
 * affichent les invitations même sans queue worker.
 */
class DiscussionInviteNotification extends Notification
{
    public function __construct(
        public int $threadId,
        public string $threadName,
        public bool $isGroup,
        public int $inviterId,
        public string $inviterName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'discussion_invite',
            'thread_id' => $this->threadId,
            'thread_name' => $this->threadName,
            'is_group' => $this->isGroup,
            'inviter_id' => $this->inviterId,
            'inviter_name' => $this->inviterName,
        ];
    }
}
