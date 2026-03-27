<?php

namespace App\Models;

use App\Helpers\PermissionSeeder;
use App\Traits\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Organization extends Model
{
    use HasFactory, HasPublicId;

    public static string $publicIdPrefix = 'ORG';

    protected static function booted(): void
    {
        static::created(function (Organization $org) {
            PermissionSeeder::seedForOrganization((int) $org->id);
        });
    }

    protected $fillable = [
        'public_id',
        'name',
        'slug',
        'primary_color',
        'logo_path',
        'settings',
        'created_by',
        'status',
        'suspended_at',
        'suspension_reason',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'suspended_at' => 'datetime',
            'archived_at' => 'datetime',
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

    public function isArchived(): bool
    {
        return ! is_null($this->archived_at);
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

    public function slaPolicies(): HasMany
    {
        return $this->hasMany(SlaPolicy::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function mailbox(): HasOne
    {
        return $this->hasOne(OrganizationMailbox::class);
    }
}
