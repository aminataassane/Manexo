<?php

namespace App\Models;

use App\Enums\FormStatus;
use App\Traits\BelongsToOrganization;
use App\Traits\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends Model
{
    use SoftDeletes, HasPublicId, BelongsToOrganization, HasFactory;

    public static string $publicIdPrefix = 'FRM';

    protected $fillable = [
        'public_id',
        'organization_id',
        'name',
        'slug',
        'description',
        'status',
        'is_public',
        'creates_ticket',
        'public_title',
        'public_description',
        'public_thank_you',
        'ticket_category_id',
        'target_user_id',
        'current_version',
    ];

    protected function casts(): array
    {
        return [
            'status' => FormStatus::class,
            'is_public' => 'bool',
            'creates_ticket' => 'bool',
            'current_version' => 'int',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────

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
        return $this->hasMany(FormField::class)->orderBy('sort_order')->orderBy('id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(FormAssignment::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(FormResponse::class);
    }

    // ─── Scopes ─────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('status', FormStatus::Published);
    }

    public function scopeForOrg($query, int $orgId)
    {
        return $query->where('organization_id', $orgId);
    }

    /** Team forms only (whole team can respond, shareable by link). */
    public function scopeTeamForm($query)
    {
        return $query->whereNull('target_user_id');
    }

    // ─── Helpers ────────────────────────────────────────────────────

    public function incrementVersion(): void
    {
        $this->increment('current_version');
        $this->fields()->update(['form_version' => $this->current_version]);
    }

    public function snapshotFields(): array
    {
        return $this->fields()
            ->get(['id', 'type', 'label', 'key', 'required', 'sort_order', 'configuration'])
            ->toArray();
    }
}
