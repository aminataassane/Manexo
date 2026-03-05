<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TicketChecklistItemAssignee extends Pivot
{
    protected $table = 'ticket_checklist_item_assignees';

    public $incrementing = true;

    protected $fillable = [
        'checklist_item_id',
        'user_id',
        'role',
        'assigned_by',
    ];

    public function checklistItem(): BelongsTo
    {
        return $this->belongsTo(TicketChecklistItem::class, 'checklist_item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
