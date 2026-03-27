<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class DiscussionRemovedNotification extends Notification
{
    public function __construct(
        public int $threadId,
        public string $threadName,
        public string $actorName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'discussion_removed',
            'thread_id' => $this->threadId,
            'thread_name' => $this->threadName,
            'actor_name' => $this->actorName,
            'body_excerpt' => __('Vous avez été retiré de cette discussion.'),
        ];
    }
}
