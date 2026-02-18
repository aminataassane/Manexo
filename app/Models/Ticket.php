<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'organization_id',
        'created_by',
        'ticket_category_id',
        'ticket_priority_id',
        'assigned_to',
        'status',
        'subject',
        'description',
        'custom_fields',
        'attachments',
        'start_date',
        'due_date',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class,
            'custom_fields' => 'array',
            'attachments' => 'array',
            'start_date' => 'date',
            'due_date' => 'date',
            'archived_at' => 'datetime',
        ];
    }

    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    public function isArchived(): bool
    {
        return (bool) $this->archived_at;
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'ticket_category_id');
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(TicketPriority::class, 'ticket_priority_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class)->orderBy('created_at');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ticket_participants', 'ticket_id', 'user_id')
            ->withPivot('added_by')
            ->withTimestamps();
    }

    public function participantRecords(): HasMany
    {
        return $this->hasMany(TicketParticipant::class);
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(TicketChecklistItem::class)->orderBy('sort_order');
    }

    /** Nombre d’items cochés / total (0–100). */
    public function checklistProgress(): int
    {
        $total = $this->checklistItems()->count();
        if ($total === 0) {
            return 0;
        }
        $done = $this->checklistItems()->where('is_done', true)->count();

        return (int) round(100 * $done / $total);
    }

    /** Scope : tickets où l'utilisateur participe (créateur, assigné ou participant). */
    public function scopeWhereUserParticipates($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('created_by', $userId)
                ->orWhere('assigned_to', $userId)
                ->orWhereHas('participants', fn ($p) => $p->where('user_id', $userId));
        });
    }

    /** Vérifie si l'utilisateur a accès à la discussion (créateur, assigné, participant ou membre org). */
    public function hasDiscussionAccess(int $userId): bool
    {
        if ((int) $this->created_by === $userId || (int) $this->assigned_to === $userId) {
            return true;
        }
        if ($this->participants()->where('user_id', $userId)->exists()) {
            return true;
        }
        return User::find($userId)?->organizations()->where('organization_id', $this->organization_id)->exists() ?? false;
    }
}
