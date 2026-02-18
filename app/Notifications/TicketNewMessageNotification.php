<?php

namespace App\Notifications;

use App\Models\TicketMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TicketNewMessageNotification extends Notification
{
    public function __construct(
        public TicketMessage $message
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $this->message->loadMissing(['ticket', 'user']);
        $ticket = $this->message->ticket;
        $sender = $this->message->user;

        return [
            'type' => 'ticket_new_message',
            'ticket_id' => $this->message->ticket_id,
            'ticket_subject' => $ticket?->subject,
            'message_id' => $this->message->id,
            'sender_id' => $this->message->user_id,
            'sender_name' => $sender?->name,
            'body_excerpt' => \Illuminate\Support\Str::limit(strip_tags($this->message->body), 80),
            'is_internal_note' => $this->message->type->value === 'internal_note',
        ];
    }
}
