<?php

namespace App\Models;

use App\Enums\TicketMessageType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketMessage extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'type',
        'body',
        'attachments',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'type' => TicketMessageType::class,
            'attachments' => 'array',
            'meta' => 'array',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isInternalNote(): bool
    {
        return $this->type === TicketMessageType::InternalNote;
    }

    public function isSystem(): bool
    {
        return $this->type === TicketMessageType::System;
    }
}
