<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class DiscussionMessageSent implements ShouldBroadcastNow
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
        public ?string $userEmail = null,
        public ?string $mentionTag = null,
        public bool $isThreadCreator = false,
        public ?string $orgRoleLabel = null,
        public bool $threadIsGroup = false,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('discussion.'.$this->threadId)];
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
            'user_email' => $this->userEmail,
            'mention_tag' => $this->mentionTag,
            'is_thread_creator' => $this->isThreadCreator,
            'org_role_label' => $this->orgRoleLabel,
            'thread_is_group' => $this->threadIsGroup,
        ];
    }
}
