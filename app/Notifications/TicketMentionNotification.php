<?php

namespace App\Notifications;

use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TicketMentionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TicketMessage $message,
        public User $mentioner,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $this->message->loadMissing('ticket');
        $ticket = $this->message->ticket;

        return [
            'type' => 'ticket_mention',
            'ticket_id' => $this->message->ticket_id,
            'ticket_subject' => $ticket?->subject,
            'message_id' => $this->message->id,
            'mentioner_id' => $this->mentioner->id,
            'mentioner_name' => $this->mentioner->name,
            'body_excerpt' => \Illuminate\Support\Str::limit(strip_tags($this->message->body), 80),
        ];
    }
}
