<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class DiscussionParticipantChanged implements ShouldBroadcastNow
{
    use Dispatchable;

    public function __construct(
        public int $threadId,
        public int $userId,
        public string $action,
        public string $actorName,
        public string $userName,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('discussion.'.$this->threadId)];
    }

    public function broadcastAs(): string
    {
        return 'discussion.participant.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'thread_id' => $this->threadId,
            'user_id' => $this->userId,
            'action' => $this->action,
            'actor_name' => $this->actorName,
            'user_name' => $this->userName,
        ];
    }
}
