<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/**
 * Notification en base uniquement (sans file) pour que le badge et la cloche
 * affichent les nouveaux messages même sans queue worker.
 */
class DiscussionNewMessageNotification extends Notification
{
    public function __construct(
        public int $threadId,
        public string $threadName,
        public bool $isGroup,
        public int $messageId,
        public int $senderId,
        public string $senderName,
        public string $bodyExcerpt,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'discussion_new_message',
            'thread_id' => $this->threadId,
            'thread_name' => $this->threadName,
            'is_group' => $this->isGroup,
            'message_id' => $this->messageId,
            'sender_id' => $this->senderId,
            'sender_name' => $this->senderName,
            'body_excerpt' => $this->bodyExcerpt,
        ];
    }
}
