<?php

namespace App\Models;

use App\Enums\SlaStatus;
use App\Enums\TicketSource;
use App\Enums\TicketStatus;
use App\Traits\BelongsToOrganization;
use App\Traits\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use BelongsToOrganization, HasFactory, HasPublicId, SoftDeletes;

    public static string $publicIdPrefix = 'TCK';

    protected $fillable = [
        'public_id',
        'organization_id',
        'created_by',
        'ticket_category_id',
        'ticket_priority_id',
        'ticket_group_id',
        'assigned_to',
        'assigned_by',
        'assigned_at',
        'assigned_to_function_id',
        'status',
        'source',
        'closed_by',
        'closed_at',
        'subject',
        'description',
        'custom_fields',
        'attachments',
        'start_date',
        'due_date',
        'archived_at',
        'sla_policy_id',
        'sla_first_response_deadline',
        'sla_resolution_deadline',
        'sla_first_response_met_at',
        'sla_resolution_met_at',
        'sla_first_response_breached',
        'sla_resolution_breached',
        'sla_paused_at',
        'sla_paused_seconds',
        'requires_approval',
        'approval_status',
        'approval_policy_approver_type',
        'approval_policy_approver_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class,
            'source' => TicketSource::class,
            'custom_fields' => 'array',
            'attachments' => 'array',
            'start_date' => 'date',
            'due_date' => 'date',
            'archived_at' => 'datetime',
            'deleted_at' => 'datetime',
            'assigned_at' => 'datetime',
            'closed_at' => 'datetime',
            'sla_first_response_deadline' => 'datetime',
            'sla_resolution_deadline' => 'datetime',
            'sla_first_response_met_at' => 'datetime',
            'sla_resolution_met_at' => 'datetime',
            'sla_first_response_breached' => 'boolean',
            'sla_resolution_breached' => 'boolean',
            'sla_paused_at' => 'datetime',
            'sla_paused_seconds' => 'integer',
            'requires_approval' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    public function isArchived(): bool
    {
        return (bool) $this->archived_at;
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'ticket_category_id');
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(TicketPriority::class, 'ticket_priority_id');
    }

    public function slaPolicy(): BelongsTo
    {
        return $this->belongsTo(SlaPolicy::class);
    }

    /** Compute SLA first response status (none/on_track/at_risk/breached/met). */
    public function slaFirstResponseStatus(): SlaStatus
    {
        if (! $this->sla_first_response_deadline) {
            return SlaStatus::None;
        }
        if ($this->sla_first_response_met_at) {
            return SlaStatus::Met;
        }
        if ($this->sla_first_response_breached) {
            return SlaStatus::Breached;
        }

        $remaining = $this->slaFirstResponseRemainingSeconds();
        if ($remaining !== null && $remaining <= 0) {
            return SlaStatus::Breached;
        }

        // Check at-risk threshold
        $org = $this->relationLoaded('organization') ? $this->organization : Organization::find($this->organization_id);
        $settings = is_array($org?->settings) ? $org->settings : [];
        $threshold = (int) ($settings['sla']['at_risk_threshold_percent'] ?? 80);
        $totalSeconds = $this->sla_first_response_deadline->diffInSeconds($this->created_at);
        $elapsed = now()->diffInSeconds($this->created_at);

        if ($totalSeconds > 0 && ($elapsed / $totalSeconds) * 100 >= $threshold) {
            return SlaStatus::AtRisk;
        }

        return SlaStatus::OnTrack;
    }

    /** Compute SLA resolution status (none/on_track/at_risk/breached/met). */
    public function slaResolutionStatus(): SlaStatus
    {
        if (! $this->sla_resolution_deadline) {
            return SlaStatus::None;
        }
        if ($this->sla_resolution_met_at) {
            return SlaStatus::Met;
        }
        if ($this->sla_resolution_breached) {
            return SlaStatus::Breached;
        }
        if ($this->sla_paused_at) {
            return SlaStatus::OnTrack; // Paused = timer frozen, no risk
        }

        $remaining = $this->slaResolutionRemainingSeconds();
        if ($remaining !== null && $remaining <= 0) {
            return SlaStatus::Breached;
        }

        $org = $this->relationLoaded('organization') ? $this->organization : Organization::find($this->organization_id);
        $settings = is_array($org?->settings) ? $org->settings : [];
        $threshold = (int) ($settings['sla']['at_risk_threshold_percent'] ?? 80);

        // Total allowed seconds = resolution_minutes * 60 (from policy)
        $policy = $this->relationLoaded('slaPolicy') ? $this->slaPolicy : SlaPolicy::find($this->sla_policy_id);
        if (! $policy || ! $policy->resolution_minutes) {
            return SlaStatus::OnTrack;
        }
        $totalAllowed = $policy->resolution_minutes * 60;
        $elapsed = now()->diffInSeconds($this->created_at) - $this->sla_paused_seconds;

        if ($totalAllowed > 0 && ($elapsed / $totalAllowed) * 100 >= $threshold) {
            return SlaStatus::AtRisk;
        }

        return SlaStatus::OnTrack;
    }

    /** Remaining seconds before first response deadline, or null if no deadline. */
    public function slaFirstResponseRemainingSeconds(): ?int
    {
        if (! $this->sla_first_response_deadline || $this->sla_first_response_met_at) {
            return null;
        }

        return (int) max(0, now()->diffInSeconds($this->sla_first_response_deadline, false));
    }

    /** Remaining seconds before resolution deadline, or null if no deadline. */
    public function slaResolutionRemainingSeconds(): ?int
    {
        if (! $this->sla_resolution_deadline || $this->sla_resolution_met_at || $this->sla_paused_at) {
            return null;
        }

        return (int) max(0, now()->diffInSeconds($this->sla_resolution_deadline, false));
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(TicketGroup::class, 'ticket_group_id');
    }

    /** @deprecated Use assignees() instead. Kept for backward compatibility. */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /** Qui a clôturé le ticket. */
    public function closedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    /** Qui a effectué la dernière assignation (audit). */
    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /** Assignation à une fonction métier (pool : tous les membres avec cette fonction voient le ticket). */
    public function assignedToFunction(): BelongsTo
    {
        return $this->belongsTo(OrganizationFunction::class, 'assigned_to_function_id');
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ticket_assignees', 'ticket_id', 'user_id')
            ->using(TicketAssignee::class)
            ->withPivot('assigned_by', 'role')
            ->withTimestamps();
    }

    /** The assignee with role 'responsible' (primary assignee). */
    public function responsible(): ?User
    {
        return $this->assignees->first(fn ($u) => $u->pivot->role === 'responsible');
    }

    /** All assignees with role 'collaborator'. */
    public function collaborators(): \Illuminate\Support\Collection
    {
        return $this->assignees->filter(fn ($u) => $u->pivot->role === 'collaborator');
    }

    /** A closed ticket is locked for non-admin users. */
    public function isLocked(): bool
    {
        return $this->status === TicketStatus::Closed;
    }

    /** Check if a user can bypass the lock (owner/admin in the org). */
    public function canBypassLock(User $user): bool
    {
        $membership = $user->organizations()->where('organization_id', $this->organization_id)->first();

        return $membership && in_array($membership->pivot->role ?? '', ['owner', 'admin'], true);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class)->orderBy('created_at');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ticket_participants', 'ticket_id', 'user_id')
            ->withPivot('added_by')
            ->withTimestamps();
    }

    public function participantRecords(): HasMany
    {
        return $this->hasMany(TicketParticipant::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(TicketApproval::class);
    }

    public function latestApproval(): HasOne
    {
        return $this->hasOne(TicketApproval::class)->latestOfMany();
    }

    public function pendingApproval(): HasOne
    {
        return $this->hasOne(TicketApproval::class)->where('status', 'pending');
    }

    public function requiresApproval(): bool
    {
        return (bool) $this->requires_approval;
    }

    public function isPendingApproval(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    /** @return HasMany<TicketChecklistItem, $this> */
    public function checklistItems(): HasMany
    {
        return $this->hasMany(TicketChecklistItem::class)->orderBy('sort_order');
    }

    /** Ticket créé à partir d'une soumission de formulaire (1 réponse = 1 ticket). */
    public function formResponse(): HasOne
    {
        return $this->hasOne(FormResponse::class);
    }

    public function satisfactionRating(): HasOne
    {
        return $this->hasOne(TicketSatisfactionRating::class);
    }

    public function mergedIntoTicket(): BelongsTo
    {
        return $this->belongsTo(self::class, 'merged_into_ticket_id');
    }

    public function mergedTickets(): HasMany
    {
        return $this->hasMany(self::class, 'merged_into_ticket_id');
    }

    public function links(): HasMany
    {
        return $this->hasMany(TicketLink::class);
    }

    public function isMerged(): bool
    {
        return $this->merged_into_ticket_id !== null;
    }

    /** Vérifie si le ticket provient d'un email. */
    public function isFromEmail(): bool
    {
        return $this->source === TicketSource::Email;
    }

    /** Vérifie si le ticket provient d'un formulaire. */
    public function isFromForm(): bool
    {
        return $this->relationLoaded('formResponse')
            ? $this->formResponse !== null
            : $this->formResponse()->exists();
    }

    /** Nombre d'items cochés / total (0–100). */
    public function checklistProgress(): int
    {
        $row = $this->checklistItems()
            ->selectRaw('count(*) as total, sum(case when is_done then 1 else 0 end) as done')
            ->first();

        $total = (int) ($row->total ?? 0);
        if ($total === 0) {
            return 0;
        }

        return (int) round(100 * (int) $row->done / $total);
    }

    /** Scope : tickets où l'utilisateur participe (créateur, assigné, participant, fonction assignée, ou a posté un message). */
    public function scopeWhereUserParticipates($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('created_by', $userId)
                ->orWhereHas('assignees', fn ($a) => $a->where('users.id', $userId))
                ->orWhereHas('participants', fn ($p) => $p->where('user_id', $userId))
                ->orWhereHas('assignedToFunction', fn ($f) => $f->whereHas('memberships', fn ($m) => $m->where('user_id', $userId)))
                ->orWhereExists(function ($sub) use ($userId) {
                    $sub->selectRaw('1')
                        ->from('ticket_messages')
                        ->whereColumn('ticket_messages.ticket_id', 'tickets.id')
                        ->where('ticket_messages.user_id', $userId)
                        ->where('ticket_messages.type', '!=', 'system');
                });
        });
    }

    /** Vérifie si l'utilisateur a accès à la discussion (créateur, assigné, participant, fonction assignée, ou membre org). */
    public function hasDiscussionAccess(int $userId): bool
    {
        // Créateur du ticket : accès garanti (cast explicite pour éviter les comparaisons strictes)
        if ($this->created_by !== null && (int) $this->created_by === (int) $userId) {
            return true;
        }
        if ($this->assignees()->where('users.id', $userId)->exists()) {
            return true;
        }
        if ($this->participants()->where('user_id', $userId)->exists()) {
            return true;
        }
        if ($this->assigned_to_function_id && $this->assignedToFunction) {
            if ($this->assignedToFunction->memberships()->where('user_id', $userId)->exists()) {
                return true;
            }
        }

        // Tout membre de l'organisation du ticket peut voir la discussion (page déjà protégée par ensure.organization)
        return \App\Models\OrganizationMembership::query()
            ->where('organization_id', $this->organization_id)
            ->where('user_id', $userId)
            ->exists();
    }
}
