<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketCategory extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'name',
        'slug',
        'is_active',
        'default_ticket_group_id',
        'default_form_id',
        'requires_approval',
        'approval_type',
        'approval_user_id',
        'approval_role',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'requires_approval' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'ticket_category_id');
    }

    public function defaultGroup(): BelongsTo
    {
        return $this->belongsTo(TicketGroup::class, 'default_ticket_group_id');
    }

    public function defaultForm(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'default_form_id');
    }

    public function approvalUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approval_user_id');
    }
}
