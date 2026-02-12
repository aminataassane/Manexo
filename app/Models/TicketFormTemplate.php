<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketFormTemplate extends Model
{
    protected $fillable = [
        'organization_id',
        'ticket_category_id',
        'name',
        'request_type',
        'target_user_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'bool',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'ticket_category_id');
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(\App\Models\TicketFormField::class, 'template_id')->orderBy('sort_order')->orderBy('id');
    }
}

