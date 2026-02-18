<?php

namespace App\Events;

use App\Models\DiscussionMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DiscussionMessageSent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(public DiscussionMessage $message) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('discussion.' . $this->message->thread_id)];
    }

    public function broadcastAs(): string
    {
        return 'discussion.message.sent';
    }

    public function broadcastWith(): array
    {
        $this->message->loadMissing(['user:id,name,email']);

        return [
            'id' => $this->message->id,
            'thread_id' => $this->message->thread_id,
            'user_id' => $this->message->user_id,
            'user_name' => $this->message->user?->name,
            'body' => $this->message->body,
            'attachments' => $this->message->attachments,
            'meta' => $this->message->meta,
            'created_at' => $this->message->created_at->toIso8601String(),
        ];
    }
}

