<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleDefinition extends Model
{
    use BelongsToOrganization;
    protected $table = 'organization_roles';

    protected $fillable = [
        'organization_id',
        'name',
        'slug',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function memberCount(): int
    {
        return OrganizationMembership::query()
            ->where('organization_id', $this->organization_id)
            ->where('role', $this->slug)
            ->count();
    }
}
