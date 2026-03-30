<?php

namespace App\Services;

use App\Helpers\CacheHelper;
use App\Models\Organization;
use App\Models\SlaPolicy;
use App\Models\Ticket;
use Illuminate\Support\Facades\Cache;

class SlaService
{
    /**
     * Apply SLA policy to a newly created ticket.
     * Called after Ticket::create().
     */
    public static function applyPolicy(Ticket $ticket): void
    {
        if (! $ticket->ticket_priority_id || ! $ticket->organization_id) {
            return;
        }

        $org = Organization::find($ticket->organization_id);
        $settings = is_array($org?->settings) ? $org->settings : [];

        if (! ($settings['sla']['enabled'] ?? false)) {
            return;
        }

        $policy = self::findActivePolicy((int) $ticket->organization_id, (int) $ticket->ticket_priority_id);
        if (! $policy) {
            return;
        }

        $data = ['sla_policy_id' => $policy->id];
        $createdAt = $ticket->created_at ?? now();
        $orgId = (int) $ticket->organization_id;

        if ($policy->first_response_minutes) {
            $data['sla_first_response_deadline'] = BusinessHoursService::addBusinessMinutes($createdAt->copy(), $policy->first_response_minutes, $orgId);
        }
        if ($policy->resolution_minutes) {
            $data['sla_resolution_deadline'] = BusinessHoursService::addBusinessMinutes($createdAt->copy(), $policy->resolution_minutes, $orgId);
        }

        $ticket->update($data);
    }

    /**
     * Recalculate deadlines when ticket priority changes.
     */
    public static function onPriorityChanged(Ticket $ticket, ?int $oldPriorityId): void
    {
        $org = Organization::find($ticket->organization_id);
        $settings = is_array($org?->settings) ? $org->settings : [];

        if (! ($settings['sla']['enabled'] ?? false)) {
            return;
        }

        $newPolicy = self::findActivePolicy((int) $ticket->organization_id, (int) $ticket->ticket_priority_id);

        if (! $newPolicy) {
            // No policy for new priority — clear SLA
            $ticket->update([
                'sla_policy_id' => null,
                'sla_first_response_deadline' => null,
                'sla_resolution_deadline' => null,
            ]);

            return;
        }

        $data = ['sla_policy_id' => $newPolicy->id];
        $createdAt = $ticket->created_at ?? now();
        $orgId = (int) $ticket->organization_id;

        // Recalculate first response deadline (if not already met)
        if (! $ticket->sla_first_response_met_at && $newPolicy->first_response_minutes) {
            $data['sla_first_response_deadline'] = BusinessHoursService::addBusinessMinutes($createdAt->copy(), $newPolicy->first_response_minutes, $orgId);
            $data['sla_first_response_breached'] = false;
        } elseif (! $newPolicy->first_response_minutes) {
            $data['sla_first_response_deadline'] = null;
        }

        // Recalculate resolution deadline accounting for paused time
        if (! $ticket->sla_resolution_met_at && $newPolicy->resolution_minutes) {
            $pausedSeconds = (int) $ticket->sla_paused_seconds;
            $data['sla_resolution_deadline'] = BusinessHoursService::addBusinessMinutes($createdAt->copy(), $newPolicy->resolution_minutes, $orgId)
                ->addSeconds($pausedSeconds);
            $data['sla_resolution_breached'] = false;
        } elseif (! $newPolicy->resolution_minutes) {
            $data['sla_resolution_deadline'] = null;
        }

        $ticket->update($data);
    }

    /**
     * Pause SLA timer when ticket goes to Pending status.
     * Only resolution timer is paused. First response keeps ticking.
     */
    public static function pause(Ticket $ticket): void
    {
        if ($ticket->sla_paused_at) {
            return; // Already paused
        }
        if (! $ticket->sla_resolution_deadline) {
            return; // No resolution SLA
        }
        if ($ticket->sla_resolution_met_at) {
            return; // Already resolved
        }

        $ticket->update(['sla_paused_at' => now()]);
    }

    /**
     * Resume SLA timer when ticket leaves Pending status.
     * Adds pause duration to sla_paused_seconds and extends resolution deadline.
     */
    public static function resume(Ticket $ticket): void
    {
        if (! $ticket->sla_paused_at) {
            return; // Not paused
        }

        $pauseDuration = (int) now()->diffInSeconds($ticket->sla_paused_at);

        $data = [
            'sla_paused_at' => null,
            'sla_paused_seconds' => (int) $ticket->sla_paused_seconds + $pauseDuration,
        ];

        // Extend resolution deadline by the pause duration
        if ($ticket->sla_resolution_deadline && ! $ticket->sla_resolution_met_at) {
            $data['sla_resolution_deadline'] = $ticket->sla_resolution_deadline->copy()->addSeconds($pauseDuration);
        }

        $ticket->update($data);
    }

    /**
     * Record first response when an agent (non-creator) sends a message.
     */
    public static function recordFirstResponse(Ticket $ticket): void
    {
        if ($ticket->sla_first_response_met_at) {
            return; // Already recorded
        }
        if (! $ticket->sla_first_response_deadline) {
            return; // No SLA
        }

        $now = now();
        $data = ['sla_first_response_met_at' => $now];

        if ($now->gt($ticket->sla_first_response_deadline)) {
            $data['sla_first_response_breached'] = true;
        }

        $ticket->update($data);
    }

    /**
     * Record resolution when ticket status goes to Resolved/Closed.
     */
    public static function recordResolution(Ticket $ticket): void
    {
        // If paused, resume first to account for pause duration
        if ($ticket->sla_paused_at) {
            self::resume($ticket);
            $ticket->refresh();
        }

        if ($ticket->sla_resolution_met_at) {
            return; // Already recorded
        }
        if (! $ticket->sla_resolution_deadline) {
            return; // No SLA
        }

        $now = now();
        $data = ['sla_resolution_met_at' => $now];

        if ($now->gt($ticket->sla_resolution_deadline)) {
            $data['sla_resolution_breached'] = true;
        }

        $ticket->update($data);
    }

    /**
     * Handle ticket reopened (leaves Resolved/Closed status).
     * Clears resolution met_at and breached flag.
     */
    public static function onReopened(Ticket $ticket): void
    {
        if (! $ticket->sla_resolution_met_at) {
            return;
        }

        $ticket->update([
            'sla_resolution_met_at' => null,
            'sla_resolution_breached' => false,
        ]);
    }

    /**
     * Find active SLA policy for a given org + priority.
     */
    private static function findActivePolicy(int $orgId, int $priorityId): ?SlaPolicy
    {
        $policies = Cache::remember(
            CacheHelper::slaPoliciesKey($orgId),
            CacheHelper::TTL,
            fn () => SlaPolicy::withoutOrganizationScope()
                ->where('organization_id', $orgId)
                ->where('is_active', true)
                ->get()
                ->keyBy('ticket_priority_id')
        );

        return $policies->get($priorityId);
    }
}
