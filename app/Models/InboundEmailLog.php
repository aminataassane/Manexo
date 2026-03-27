<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InboundEmailLog extends Model
{
    protected $fillable = [
        'organization_mailbox_id',
        'imap_uid',
        'message_id',
        'from_email',
        'from_name',
        'subject',
        'status',
        'ticket_id',
        'ticket_message_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'imap_uid' => 'integer',
        ];
    }

    public function mailbox(): BelongsTo
    {
        return $this->belongsTo(OrganizationMailbox::class, 'organization_mailbox_id');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function ticketMessage(): BelongsTo
    {
        return $this->belongsTo(TicketMessage::class);
    }
}
