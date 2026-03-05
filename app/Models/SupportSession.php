<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportSession extends Model
{
    protected $fillable = [
        'user_id',
        'organization_id',
        'reason',
        'duration_minutes',
        'started_at',
        'expires_at',
        'ended_at',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'expires_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function isActive(): bool
    {
        return is_null($this->ended_at) && $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return is_null($this->ended_at) && $this->expires_at->isPast();
    }

    public function scopeActiveForUser($query, int $userId)
    {
        return $query->where('user_id', $userId)
            ->whereNull('ended_at')
            ->where('expires_at', '>', now());
    }
}
