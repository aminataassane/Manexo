<?php

namespace App\Models;

use App\Enums\FormAssignmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FormAssignment extends Model
{
    protected $fillable = [
        'form_id',
        'user_id',
        'organization_function_id',
        'assigned_by',
        'status',
        'due_date',
        'submitted_at',
        'form_version',
    ];

    protected function casts(): array
    {
        return [
            'status' => FormAssignmentStatus::class,
            'due_date' => 'date',
            'submitted_at' => 'datetime',
            'form_version' => 'int',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function organizationFunction(): BelongsTo
    {
        return $this->belongsTo(OrganizationFunction::class, 'organization_function_id');
    }

    public function response(): HasOne
    {
        return $this->hasOne(FormResponse::class, 'assignment_id');
    }

    /**
     * Scope: assignments for a given user (direct or via function membership).
     */
    public function scopeForUser($query, int $userId, ?int $orgId = null)
    {
        return $query->where(function ($q) use ($userId, $orgId) {
            $q->where('user_id', $userId);

            if ($orgId) {
                $functionIds = OrganizationMembership::query()
                    ->where('user_id', $userId)
                    ->where('organization_id', $orgId)
                    ->whereNotNull('organization_function_id')
                    ->pluck('organization_function_id');

                if ($functionIds->isNotEmpty()) {
                    $q->orWhere(function ($sub) use ($functionIds) {
                        $sub->whereNull('user_id')
                            ->whereIn('organization_function_id', $functionIds);
                    });
                }
            }
        });
    }
}
