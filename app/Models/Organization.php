<?php

namespace App\Models;

use App\Helpers\PermissionSeeder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    protected static function booted(): void
    {
        static::created(function (Organization $org) {
            PermissionSeeder::seedForOrganization((int) $org->id);
        });
    }

    protected $fillable = [
        'name',
        'slug',
        'primary_color',
        'logo_path',
        'settings',
        'created_by',
        'status',
        'suspended_at',
        'suspension_reason',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'suspended_at' => 'datetime',
        ];
    }

    public function isActive(): bool
    {
        return ($this->status ?? 'active') === 'active';
    }

    public function isSuspended(): bool
    {
        return ($this->status ?? 'active') === 'suspended';
    }

    public function isDisabled(): bool
    {
        return ($this->status ?? 'active') === 'disabled';
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(OrganizationMembership::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_memberships')
            ->withPivot(['role', 'organization_function_id'])
            ->withTimestamps();
    }

    /** Fonctions métier de l'organisation (Informaticien, RH, etc.). */
    public function organizationFunctions(): HasMany
    {
        return $this->hasMany(OrganizationFunction::class, 'organization_id')->orderBy('sort_order');
    }

    public function roleDefinitions(): HasMany
    {
        return $this->hasMany(RoleDefinition::class);
    }

    public function ticketCategories(): HasMany
    {
        return $this->hasMany(TicketCategory::class);
    }

    public function ticketPriorities(): HasMany
    {
        return $this->hasMany(TicketPriority::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
