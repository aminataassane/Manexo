<?php

namespace App\Events;

use App\Enums\TicketMessageType;
use App\Models\TicketMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(public TicketMessage $message) {}

    public function broadcastOn(): array
    {
        $this->message->loadMissing('ticket');
        $publicId = $this->message->ticket?->public_id ?? $this->message->ticket_id;

        $channel = $this->message->type === TicketMessageType::InternalNote
            ? new PrivateChannel('ticket.staff.'.$publicId)
            : new PrivateChannel('ticket.'.$publicId);

        return [$channel];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        $this->message->load(['user:id,name,email', 'ticket:id,public_id']);

        return [
            'id' => $this->message->id,
            'ticket_id' => $this->message->ticket_id,
            'ticket_public_id' => $this->message->ticket?->public_id,
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
