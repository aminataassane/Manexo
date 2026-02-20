<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class DiscussionMessageSent implements ShouldBroadcast
{
    use Dispatchable;

    public function __construct(
        public int $messageId,
        public int $threadId,
        public int $userId,
        public string $userName,
        public string $body,
        public ?array $attachments,
        public ?array $meta,
        public string $createdAt,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('discussion.' . $this->threadId)];
    }

    public function broadcastAs(): string
    {
        return 'discussion.message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->messageId,
            'thread_id' => $this->threadId,
            'user_id' => $this->userId,
            'user_name' => $this->userName,
            'body' => $this->body,
            'attachments' => $this->attachments,
            'meta' => $this->meta,
            'created_at' => $this->createdAt,
        ];
    }
}
