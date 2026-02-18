<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketChecklistItem extends Model
{
    public const STATUS_TODO = 'todo';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_BLOCKED = 'blocked';
    public const STATUS_DONE = 'done';

    protected $fillable = [
        'ticket_id',
        'title',
        'assigned_to',
        'due_date',
        'sort_order',
        'is_done',
        'done_at',
        'done_by',
        'status',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'is_done' => 'boolean',
            'done_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function doneByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'done_by');
    }

    public function markDone(int $userId): void
    {
        $this->update([
            'is_done' => true,
            'done_at' => now(),
            'done_by' => $userId,
            'status' => self::STATUS_DONE,
        ]);
    }

    public function markUndone(): void
    {
        $this->update([
            'is_done' => false,
            'done_at' => null,
            'done_by' => null,
            'status' => self::STATUS_TODO,
        ]);
    }
}
