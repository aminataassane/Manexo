<?php

namespace App\Services;

use App\Enums\Permission;
use App\Enums\TicketMessageType;
use App\Events\UserNotificationReceived;
use App\Models\Ticket;
use App\Models\TicketApproval;
use App\Models\TicketCategory;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ApprovalService
{
    /**
     * Apply approval policy to a newly created ticket (called after Ticket::create).
     */
    public static function applyPolicy(Ticket $ticket): void
    {
        $category = $ticket->ticket_category_id
            ? TicketCategory::find($ticket->ticket_category_id)
            : null;

        if (! $category || ! $category->requires_approval) {
            return;
        }

        $ticket->update([
            'requires_approval' => true,
            'approval_status' => 'pending',
            'approval_policy_approver_type' => $category->approval_type,
            'approval_policy_approver_id' => $category->approval_user_id,
        ]);

        TicketApproval::create([
            'ticket_id' => $ticket->id,
            'requested_by' => $ticket->created_by,
            'status' => 'pending',
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $ticket->created_by,
            'type' => TicketMessageType::System,
            'body' => 'Ce ticket nécessite une approbation avant traitement.',
        ]);

        // Notify approvers
        $approvers = self::getApprovers($ticket);
        $creatorName = User::where('id', $ticket->created_by)->value('name') ?? 'Utilisateur';

        foreach ($approvers as $approver) {
            $approver->notify(new \App\Notifications\ApprovalRequestedNotification(
                ticketId: (int) $ticket->id,
                ticketPublicId: (string) $ticket->public_id,
                ticketSubject: (string) $ticket->subject,
                requesterName: $creatorName,
            ));
            event(new UserNotificationReceived((int) $approver->id));
        }

        OrganizationAuditService::log(
            'ticket.approval_requested',
            'ticket',
            (int) $ticket->id,
            ['category' => $category->name, 'approver_type' => $category->approval_type],
        );
    }

    /**
     * Approve a ticket.
     */
    public static function approve(Ticket $ticket, User $approver, string $comment): void
    {
        abort_if(! $ticket->isPendingApproval(), 422);
        abort_if(! self::canApprove($ticket, $approver), 403);

        $pending = $ticket->approvals()->where('status', 'pending')->first();

        if ($pending) {
            $pending->update([
                'approver_id' => $approver->id,
                'status' => 'approved',
                'comment' => $comment,
                'decided_at' => now(),
            ]);
        }

        $ticket->update(['approval_status' => 'approved']);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $approver->id,
            'type' => TicketMessageType::System,
            'body' => "{$approver->name} a approuvé ce ticket. Commentaire : {$comment}",
        ]);

        // Notify ticket creator
        if ($ticket->created_by) {
            $creator = User::find($ticket->created_by);
            if ($creator) {
                $creator->notify(new \App\Notifications\ApprovalDecisionNotification(
                    ticketId: (int) $ticket->id,
                    ticketPublicId: (string) $ticket->public_id,
                    ticketSubject: (string) $ticket->subject,
                    approverName: (string) $approver->name,
                    decision: 'approved',
                    comment: $comment,
                ));
                event(new UserNotificationReceived((int) $creator->id));
            }
        }

        OrganizationAuditService::log(
            'ticket.approved',
            'ticket',
            (int) $ticket->id,
            ['approver' => $approver->name, 'comment' => $comment],
        );

        AutomationService::evaluate($ticket->fresh(), 'status_changed');
    }

    /**
     * Reject a ticket.
     */
    public static function reject(Ticket $ticket, User $approver, string $comment): void
    {
        abort_if(! $ticket->isPendingApproval(), 422);
        abort_if(! self::canApprove($ticket, $approver), 403);

        $pending = $ticket->approvals()->where('status', 'pending')->first();

        if ($pending) {
            $pending->update([
                'approver_id' => $approver->id,
                'status' => 'rejected',
                'comment' => $comment,
                'decided_at' => now(),
            ]);
        }

        $ticket->update(['approval_status' => 'rejected']);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $approver->id,
            'type' => TicketMessageType::System,
            'body' => "{$approver->name} a rejeté ce ticket. Commentaire : {$comment}",
        ]);

        // Notify ticket creator
        if ($ticket->created_by) {
            $creator = User::find($ticket->created_by);
            if ($creator) {
                $creator->notify(new \App\Notifications\ApprovalDecisionNotification(
                    ticketId: (int) $ticket->id,
                    ticketPublicId: (string) $ticket->public_id,
                    ticketSubject: (string) $ticket->subject,
                    approverName: (string) $approver->name,
                    decision: 'rejected',
                    comment: $comment,
                ));
                event(new UserNotificationReceived((int) $creator->id));
            }
        }

        OrganizationAuditService::log(
            'ticket.rejected',
            'ticket',
            (int) $ticket->id,
            ['approver' => $approver->name, 'comment' => $comment],
        );
    }

    /**
     * Check if a user can approve this ticket.
     */
    public static function canApprove(Ticket $ticket, User $user): bool
    {
        if (! $ticket->requiresApproval() || ! $ticket->isPendingApproval()) {
            return false;
        }

        $type = $ticket->approval_policy_approver_type;

        // Direct policy match
        if ($type === 'user' && (int) $user->id === (int) $ticket->approval_policy_approver_id) {
            return true;
        }

        if ($type === 'role') {
            $category = TicketCategory::find($ticket->ticket_category_id);
            $requiredRole = $category?->approval_role;

            if ($requiredRole) {
                $membership = DB::table('organization_memberships')
                    ->where('organization_id', $ticket->organization_id)
                    ->where('user_id', $user->id)
                    ->first();

                if ($membership && $membership->role === $requiredRole) {
                    return true;
                }
            }
        }

        if ($type === 'org_admin' && self::isOrgAdminOrOwner($ticket, $user)) {
            return true;
        }

        // Org owners/admins with tickets.approve permission can always approve
        if ($user->hasPermission(Permission::TicketsApprove) && self::isOrgAdminOrOwner($ticket, $user)) {
            return true;
        }

        return false;
    }

    /**
     * Get potential approvers for a ticket.
     */
    public static function getApprovers(Ticket $ticket): Collection
    {
        $type = $ticket->approval_policy_approver_type;
        $orgId = $ticket->organization_id;

        if ($type === 'user' && $ticket->approval_policy_approver_id) {
            $user = User::find($ticket->approval_policy_approver_id);

            return $user ? collect([$user]) : collect();
        }

        if ($type === 'role') {
            $category = TicketCategory::find($ticket->ticket_category_id);
            $role = $category?->approval_role;

            if (! $role) {
                return collect();
            }

            return User::query()
                ->whereHas('organizations', fn ($q) => $q
                    ->where('organization_id', $orgId)
                    ->where('organization_memberships.role', $role))
                ->get();
        }

        if ($type === 'org_admin') {
            return User::query()
                ->whereHas('organizations', fn ($q) => $q
                    ->where('organization_id', $orgId)
                    ->whereIn('organization_memberships.role', ['owner', 'admin']))
                ->get();
        }

        return collect();
    }

    private static function isOrgAdminOrOwner(Ticket $ticket, User $user): bool
    {
        $membership = DB::table('organization_memberships')
            ->where('organization_id', $ticket->organization_id)
            ->where('user_id', $user->id)
            ->first();

        return $membership && in_array($membership->role, ['owner', 'admin'], true);
    }
}
