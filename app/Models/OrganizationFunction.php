<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Fonction métier au sein d'une organisation (ex: Informaticien, RH, Comptable).
 * Distincte du rôle système (owner/admin/agent/member) qui définit les permissions.
 */
class OrganizationFunction extends Model
{
    use BelongsToOrganization;
    protected $fillable = [
        'organization_id',
        'name',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /** Membres de l'organisation qui ont cette fonction. */
    public function memberships(): HasMany
    {
        return $this->hasMany(OrganizationMembership::class, 'organization_function_id');
    }

    /** Tickets assignés à cette fonction (pool : tous les membres avec cette fonction peuvent les voir). */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to_function_id');
    }
}
