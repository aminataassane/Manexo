<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiscussionThread extends Model
{
    use BelongsToOrganization, HasFactory;
    protected $fillable = [
        'organization_id',
        'created_by',
        'name',
        'is_group',
        'archived_at',
    ];

    protected $casts = [
        'is_group' => 'boolean',
        'archived_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'discussion_participants', 'thread_id', 'user_id')
            ->withPivot('added_by')
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(DiscussionMessage::class, 'thread_id')->orderBy('created_at');
    }

    public function lastMessage(): ?DiscussionMessage
    {
        return $this->messages()->latest('created_at')->first();
    }

    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }
}

