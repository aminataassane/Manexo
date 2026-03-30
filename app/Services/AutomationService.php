<?php

namespace App\Services;

use App\Enums\TicketMessageType;
use App\Enums\TicketStatus;
use App\Events\UserNotificationReceived;
use App\Helpers\CacheHelper;
use App\Models\AutomationRule;
use App\Models\Organization;
use App\Models\Ticket;
use App\Models\TicketChecklistItem;
use App\Models\TicketGroup;
use App\Models\TicketMessage;
use App\Models\TicketPriority;
use App\Models\User;
use App\Notifications\AutomationNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AutomationService
{
    /** Track in-progress evaluations to prevent loops within a single request. */
    private static array $processing = [];

    public static function evaluate(Ticket $ticket, string $trigger, array $context = []): void
    {
        $key = "{$ticket->id}:{$trigger}";

        if (isset(self::$processing[$key])) {
            return;
        }

        self::$processing[$key] = true;

        try {
            $orgId = (int) $ticket->organization_id;

            // Check if automations are enabled for this org
            $org = Organization::find($orgId);
            $settings = is_array($org?->settings) ? $org->settings : [];
            if (! ($settings['automations']['enabled'] ?? true)) {
                return;
            }

            $rules = Cache::remember(
                CacheHelper::automationRulesKey($orgId, $trigger),
                CacheHelper::TTL,
                fn () => AutomationRule::withoutOrganizationScope()
                    ->where('organization_id', $orgId)
                    ->where('trigger_type', $trigger)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get()
            );

            foreach ($rules as $rule) {
                if (self::matchesConditions($ticket, $rule->conditions ?? [])) {
                    self::executeActions($ticket, $rule);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('AutomationService error', [
                'ticket_id' => $ticket->id,
                'trigger' => $trigger,
                'error' => $e->getMessage(),
            ]);
        } finally {
            unset(self::$processing[$key]);
        }
    }

    private static function matchesConditions(Ticket $ticket, array $conditions): bool
    {
        if (empty($conditions)) {
            return true;
        }

        if (isset($conditions['category_id']) && (int) $ticket->ticket_category_id !== (int) $conditions['category_id']) {
            return false;
        }

        if (isset($conditions['priority_id']) && (int) $ticket->ticket_priority_id !== (int) $conditions['priority_id']) {
            return false;
        }

        if (isset($conditions['group_id']) && (int) $ticket->ticket_group_id !== (int) $conditions['group_id']) {
            return false;
        }

        if (isset($conditions['source'])) {
            $ticketSource = $ticket->source?->value ?? $ticket->source;
            if ($ticketSource !== $conditions['source']) {
                return false;
            }
        }

        if (isset($conditions['status'])) {
            $ticketStatus = $ticket->status?->value ?? $ticket->status;
            if ($ticketStatus !== $conditions['status']) {
                return false;
            }
        }

        if (! empty($conditions['is_unassigned'])) {
            $ticket->loadMissing('assignees');
            if ($ticket->assignees->isNotEmpty() || $ticket->assigned_to !== null) {
                return false;
            }
        }

        return true;
    }

    private static function executeActions(Ticket $ticket, AutomationRule $rule): void
    {
        $orgId = (int) $ticket->organization_id;
        $actions = $rule->actions ?? [];
        $executedLabels = [];

        foreach ($actions as $action) {
            $type = $action['type'] ?? '';
            $label = self::executeAction($ticket, $action, $orgId);
            if ($label) {
                $executedLabels[] = $label;
            }
        }

        if (! empty($executedLabels)) {
            TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => null,
                'type' => TicketMessageType::System,
                'body' => "Automatisation \"{$rule->name}\" exécutée (".implode(', ', $executedLabels).')',
                'meta' => ['action' => 'automation', 'rule_id' => $rule->id, 'rule_name' => $rule->name],
            ]);
        }
    }

    private static function executeAction(Ticket $ticket, array $action, int $orgId): ?string
    {
        $type = $action['type'] ?? '';

        return match ($type) {
            'assign_responsible' => self::actionAssignResponsible($ticket, $action, $orgId),
            'add_collaborators' => self::actionAddCollaborators($ticket, $action, $orgId),
            'change_priority' => self::actionChangePriority($ticket, $action, $orgId),
            'change_status' => self::actionChangeStatus($ticket, $action),
            'change_group' => self::actionChangeGroup($ticket, $action, $orgId),
            'notify_users' => self::actionNotifyUsers($ticket, $action, $orgId),
            'add_checklist' => self::actionAddChecklist($ticket, $action),
            default => null,
        };
    }

    private static function actionAssignResponsible(Ticket $ticket, array $action, int $orgId): ?string
    {
        $userId = (int) ($action['user_id'] ?? 0);
        if ($userId <= 0) {
            return null;
        }

        $user = User::query()
            ->whereKey($userId)
            ->assignableInOrganization($orgId)
            ->first();

        if (! $user) {
            return null;
        }

        $ticket->loadMissing('assignees');

        // Demote current responsible to collaborator
        $ticket->assignees()->newPivotQuery()->where('role', 'responsible')->update(['role' => 'collaborator']);

        // Attach or update new responsible
        if ($ticket->assignees()->where('users.id', $userId)->exists()) {
            $ticket->assignees()->updateExistingPivot($userId, ['role' => 'responsible']);
        } else {
            $ticket->assignees()->attach($userId, ['role' => 'responsible']);
        }

        $ticket->updateQuietly([
            'assigned_to' => $userId,
            'assigned_at' => now(),
        ]);

        return 'assigné à '.$user->name;
    }

    private static function actionAddCollaborators(Ticket $ticket, array $action, int $orgId): ?string
    {
        $userIds = $action['user_ids'] ?? [];
        if (empty($userIds)) {
            return null;
        }

        $users = User::query()
            ->whereIn('id', $userIds)
            ->assignableInOrganization($orgId)
            ->get();

        if ($users->isEmpty()) {
            return null;
        }

        $ticket->loadMissing('assignees');
        $added = [];

        foreach ($users as $user) {
            if (! $ticket->assignees()->where('users.id', $user->id)->exists()) {
                $ticket->assignees()->attach($user->id, ['role' => 'collaborator']);
                $added[] = $user->name;
            }
        }

        return ! empty($added) ? 'collaborateurs: '.implode(', ', $added) : null;
    }

    private static function actionChangePriority(Ticket $ticket, array $action, int $orgId): ?string
    {
        $priorityId = (int) ($action['priority_id'] ?? 0);
        if ($priorityId <= 0) {
            return null;
        }

        $priority = TicketPriority::withoutOrganizationScope()
            ->where('organization_id', $orgId)
            ->where('is_active', true)
            ->whereKey($priorityId)
            ->first();

        if (! $priority || (int) $ticket->ticket_priority_id === $priorityId) {
            return null;
        }

        $ticket->updateQuietly(['ticket_priority_id' => $priorityId]);

        return 'priorité → '.$priority->name;
    }

    private static function actionChangeStatus(Ticket $ticket, array $action): ?string
    {
        $status = $action['status'] ?? '';
        $newStatus = TicketStatus::tryFrom($status);
        if (! $newStatus) {
            return null;
        }

        $currentStatus = $ticket->status;
        if ($currentStatus === $newStatus) {
            return null;
        }

        $isClosed = in_array($newStatus, [TicketStatus::Resolved, TicketStatus::Closed], true);
        $ticket->updateQuietly([
            'status' => $newStatus,
            'closed_by' => $isClosed ? null : $ticket->closed_by,
            'closed_at' => $isClosed ? now() : null,
        ]);

        return 'statut → '.$newStatus->value;
    }

    private static function actionChangeGroup(Ticket $ticket, array $action, int $orgId): ?string
    {
        $groupId = (int) ($action['group_id'] ?? 0);
        if ($groupId <= 0) {
            return null;
        }

        $group = TicketGroup::withoutOrganizationScope()
            ->where('organization_id', $orgId)
            ->where('is_active', true)
            ->whereKey($groupId)
            ->first();

        if (! $group || (int) $ticket->ticket_group_id === $groupId) {
            return null;
        }

        $ticket->updateQuietly(['ticket_group_id' => $groupId]);

        return 'groupe → '.$group->name;
    }

    private static function actionNotifyUsers(Ticket $ticket, array $action, int $orgId): ?string
    {
        $userIds = $action['user_ids'] ?? [];
        if (empty($userIds)) {
            return null;
        }

        $users = User::query()
            ->whereIn('id', $userIds)
            ->whereHas('organizations', fn ($q) => $q->where('organization_id', $orgId))
            ->get();

        if ($users->isEmpty()) {
            return null;
        }

        /** @var \Illuminate\Database\Eloquent\Collection<int, User> $users */
        foreach ($users as $user) {
            $user->notify(new AutomationNotification(
                ticketId: (int) $ticket->id,
                ticketPublicId: $ticket->public_id,
                ticketSubject: $ticket->subject,
                ruleName: 'Automatisation',
            ));
            event(new UserNotificationReceived((int) $user->id, 'automation'));
        }

        return 'notifié '.$users->count().' utilisateur(s)';
    }

    private static function actionAddChecklist(Ticket $ticket, array $action): ?string
    {
        $items = $action['items'] ?? [];
        if (empty($items)) {
            return null;
        }

        $maxOrder = $ticket->checklistItems()->max('sort_order') ?? -1;
        $count = 0;

        foreach ($items as $item) {
            $title = trim((string) ($item['title'] ?? ''));
            if ($title === '') {
                continue;
            }
            $maxOrder++;
            TicketChecklistItem::create([
                'ticket_id' => $ticket->id,
                'title' => $title,
                'sort_order' => $maxOrder,
            ]);
            $count++;
        }

        return $count > 0 ? $count.' item(s) checklist ajouté(s)' : null;
    }
}
