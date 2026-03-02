<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketChecklistItemLog extends Model
{
    protected $fillable = [
        'ticket_checklist_item_id',
        'user_id',
        'action',
    ];

    public function checklistItem(): BelongsTo
    {
        return $this->belongsTo(TicketChecklistItem::class, 'ticket_checklist_item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
