<?php

namespace App\Events;

use App\Enums\TicketMessageType;
use App\Models\TicketMessage;
use App\Support\TicketMessageUserCard;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

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
        $this->message->load([
            'user:id,name,email,mention_tag',
            'ticket' => fn ($q) => $q->select(['id', 'public_id', 'organization_id', 'created_by'])
                ->with(['assignees:id', 'participants:id']),
        ]);

        $ticket = $this->message->ticket;
        $user = $this->message->user;
        $uid = (int) ($this->message->user_id ?? 0);
        $orgRole = ($ticket && $uid)
            ? DB::table('organization_memberships')
                ->where('organization_id', $ticket->organization_id)
                ->where('user_id', $uid)
                ->value('role')
            : null;

        $roleLabels = [
            'owner' => __('Admin'),
            'admin' => __('Admin'),
            'agent' => __('Agent'),
            'member' => __('Membre'),
        ];

        $popoverBadges = ($ticket && $uid && $this->message->type !== TicketMessageType::System && $this->message->type !== TicketMessageType::InternalNote)
            ? TicketMessageUserCard::badgesForTicketUser($ticket, $uid, $orgRole ? (string) $orgRole : null, $roleLabels)
            : [];

        return [
            'id' => $this->message->id,
            'ticket_id' => $this->message->ticket_id,
            'ticket_public_id' => $this->message->ticket?->public_id,
            'user_id' => $this->message->user_id,
            'user_name' => $this->message->user?->name,
            'user_email' => $user?->email,
            'mention_tag' => $user?->mention_tag,
            'popover_badges' => $popoverBadges,
            'type' => $this->message->type->value,
            'body' => $this->message->body,
            'attachments' => $this->message->attachments,
            'meta' => $this->message->meta,
            'created_at' => $this->message->created_at->toIso8601String(),
        ];
    }
}
