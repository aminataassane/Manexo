<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'assigned_to_function_id',
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

    public function assignedToFunction(): BelongsTo
    {
        return $this->belongsTo(OrganizationFunction::class, 'assigned_to_function_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TicketChecklistItemLog::class);
    }

    public function markDone(int $userId): void
    {
        $this->update([
            'is_done' => true,
            'done_at' => now(),
            'done_by' => $userId,
            'status' => self::STATUS_DONE,
        ]);

        $this->logAction($userId, 'done');
    }

    public function markUndone(int $userId): void
    {
        $this->update([
            'is_done' => false,
            'done_at' => null,
            'done_by' => null,
            'status' => self::STATUS_TODO,
        ]);

        $this->logAction($userId, 'undone');
    }

    public function claim(int $userId): void
    {
        $this->update(['assigned_to' => $userId]);

        $this->logAction($userId, 'claimed');
    }

    private function logAction(int $userId, string $action): void
    {
        try {
            $this->logs()->create(['user_id' => $userId, 'action' => $action]);
        } catch (\Throwable) {
            // Audit logging must never block core toggle functionality
        }
    }
}
