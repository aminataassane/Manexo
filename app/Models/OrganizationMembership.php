<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class OrganizationMembership extends Model
{
    protected $fillable = [
        'organization_id',
        'user_id',
        'role',
        'organization_function_id',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Fonction métier dans cette organisation (ex: Informaticien, RH). */
    public function organizationFunction(): BelongsTo
    {
        return $this->belongsTo(OrganizationFunction::class, 'organization_function_id');
    }
}
