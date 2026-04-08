<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;

/**
 * Restricts Laravel database notifications to the active organization.
 */
final class NotificationOrganizationScope
{
    /**
     * @param  Builder<DatabaseNotification>|Relation  $query
     * @return Builder<DatabaseNotification>|Relation
     */
    public static function apply(Builder|Relation $query, int $orgId): Builder|Relation
    {
        if ($orgId <= 0) {
            $query->whereRaw('1 = 0');

            return $query;
        }

        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            return $query->where(function ($q) use ($orgId) {
                $s = (string) $orgId;
                $q->whereRaw(
                    "(json_extract(notifications.data, '$.organization_id') IN (?, ?) OR json_extract(notifications.data, '$.org_id') IN (?, ?))",
                    [$orgId, $s, $orgId, $s]
                );
            });
        }

        if ($driver !== 'pgsql') {
            return $query->where(function ($q) use ($orgId) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(notifications.data, '$.organization_id')) = ?", [(string) $orgId])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(notifications.data, '$.org_id')) = ?", [(string) $orgId]);
            });
        }

        $ticketTypes = [
            'ticket_new_message', 'ticket_assignee', 'ticket_mention', 'ticket_reopened',
            'ticket_created', 'ticket_status_changed', 'ticket_satisfaction_request',
            'ticket_client_routing', 'sla_at_risk', 'sla_breached',
            'approval_requested', 'approval_decision', 'checklist_item_assigned',
        ];

        return $query->where(function ($scope) use ($orgId, $ticketTypes) {
            $scope->whereRaw("((notifications.data::jsonb->>'organization_id') ~ '^[0-9]+$' and ((notifications.data::jsonb->>'organization_id')::bigint = ?))", [$orgId])
                ->orWhereRaw("((notifications.data::jsonb->>'org_id') ~ '^[0-9]+$' and ((notifications.data::jsonb->>'org_id')::bigint = ?))", [$orgId]);

            $scope->orWhere(function ($q) use ($orgId, $ticketTypes) {
                $q->whereIn(DB::raw("(notifications.data::jsonb->>'type')"), $ticketTypes)
                    ->whereRaw("(notifications.data::jsonb->>'ticket_id') ~ '^[0-9]+$'")
                    ->whereExists(function ($sub) use ($orgId) {
                        $sub->selectRaw('1')
                            ->from('tickets')
                            ->whereRaw("tickets.id = ((notifications.data::jsonb->>'ticket_id')::bigint)")
                            ->where('tickets.organization_id', $orgId);
                    });
            });

            $scope->orWhere(function ($q) use ($orgId) {
                $q->whereIn(DB::raw("(notifications.data::jsonb->>'type')"), ['discussion_new_message', 'discussion_invite', 'discussion_removed'])
                    ->whereRaw("(notifications.data::jsonb->>'thread_id') ~ '^[0-9]+$'")
                    ->whereExists(function ($sub) use ($orgId) {
                        $sub->selectRaw('1')
                            ->from('discussion_threads')
                            ->whereRaw("discussion_threads.id = ((notifications.data::jsonb->>'thread_id')::bigint)")
                            ->where('discussion_threads.organization_id', $orgId);
                    });
            });

            $scope->orWhere(function ($q) use ($orgId) {
                $q->whereIn(DB::raw("(notifications.data::jsonb->>'type')"), ['form_assignment', 'form_overdue'])
                    ->whereRaw("(notifications.data::jsonb->>'assignment_id') ~ '^[0-9]+$'")
                    ->whereExists(function ($sub) use ($orgId) {
                        $sub->selectRaw('1')
                            ->from('form_assignments')
                            ->whereRaw("form_assignments.id = ((notifications.data::jsonb->>'assignment_id')::bigint)")
                            ->where('form_assignments.organization_id', $orgId);
                    });
            });

            $scope->orWhere(function ($q) use ($orgId) {
                $q->whereRaw("(notifications.data::jsonb->>'type') = 'form_response'")
                    ->whereRaw("(notifications.data::jsonb->>'form_id') ~ '^[0-9]+$'")
                    ->whereExists(function ($sub) use ($orgId) {
                        $sub->selectRaw('1')
                            ->from('forms')
                            ->whereRaw("forms.id = ((notifications.data::jsonb->>'form_id')::bigint)")
                            ->where('forms.organization_id', $orgId);
                    });
            });

            $scope->orWhere(function ($q) use ($orgId) {
                $q->whereRaw("(notifications.data::jsonb->>'type') = 'organization_invitation'")
                    ->whereRaw("coalesce((notifications.data::jsonb->>'invitation_token'), '') <> ''")
                    ->whereExists(function ($sub) use ($orgId) {
                        $sub->selectRaw('1')
                            ->from('organization_invitations')
                            ->whereRaw("organization_invitations.token = (notifications.data::jsonb->>'invitation_token')")
                            ->where('organization_invitations.organization_id', $orgId);
                    });
            });
        });
    }
}
