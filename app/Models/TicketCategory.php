<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
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
}
