<?php

namespace App\Models;

use App\Enums\PlatformRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformInvitation extends Model
{
    public const EXPIRY_HOURS = 48;

    protected $fillable = [
        'email',
        'platform_role',
        'token',
        'invited_by',
        'status',
        'accepted_at',
        'cancelled_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'platform_role' => PlatformRole::class,
            'accepted_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending' && $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->status === 'pending' && $this->expires_at->isPast();
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending')->where('expires_at', '>', now());
    }
}
