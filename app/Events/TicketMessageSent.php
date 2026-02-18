<?php

namespace App\Events;

use App\Enums\TicketMessageType;
use App\Models\TicketMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketMessageSent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(public TicketMessage $message) {}

    public function broadcastOn(): array
    {
        $channel = $this->message->type === TicketMessageType::InternalNote
            ? new PrivateChannel('ticket.staff.' . $this->message->ticket_id)
            : new PrivateChannel('ticket.' . $this->message->ticket_id);

        return [$channel];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        $this->message->load(['user:id,name,email']);
        return [
            'id' => $this->message->id,
            'ticket_id' => $this->message->ticket_id,
            'user_id' => $this->message->user_id,
            'user_name' => $this->message->user?->name,
            'type' => $this->message->type->value,
            'body' => $this->message->body,
            'attachments' => $this->message->attachments,
            'meta' => $this->message->meta,
            'created_at' => $this->message->created_at->toIso8601String(),
        ];
    }
}
