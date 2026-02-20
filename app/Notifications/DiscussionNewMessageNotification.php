<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DiscussionNewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

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
