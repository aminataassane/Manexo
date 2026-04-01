<?php

namespace App\Livewire\Tickets;

use App\DataTransferObjects\ClientAssignmentClientScenario;
use App\DataTransferObjects\ClientAssignmentContext;
use App\Enums\Permission;
use App\Enums\TicketMessageType;
use App\Enums\TicketStatus;
use App\Events\TicketAssigneeChanged;
use App\Events\UserNotificationReceived;
use App\Helpers\CacheHelper;
use App\Models\FormResponse;
use App\Models\Organization;
use App\Models\OrganizationFunction;
use App\Models\Ticket;
use App\Models\TicketChecklistItem;
use App\Models\TicketGroup;
use App\Models\TicketMessage;
use App\Models\TicketPriority;
use App\Models\User;
use App\Notifications\ChecklistItemAssignedNotification;
use App\Notifications\TicketAssigneeNotification;
use App\Notifications\TicketReopenedNotification;
use App\Notifications\TicketSatisfactionRequestNotification;
use App\Notifications\TicketStatusChangedNotification;
use App\Services\ApprovalService;
use App\Services\AutomationService;
use App\Services\OrganizationAuditService;
use App\Services\SlaService;
use App\Services\WebhookService;
use App\Support\TicketClientRoutingNotifier;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class TicketSidebar extends Component
{
    use WithFileUploads;

    public int $ticketId;

    public string $ticketPublicId = '';

    public bool $canSeeInternalNotes = false;

    public bool $canWriteInternalNotes = false;

    // Checklist form
    public bool $showAddChecklistItem = false;

    public string $newChecklistTitle = '';

    public ?int $newChecklistAssignedTo = null;

    public ?int $newChecklistAssignedToFunction = null;

    public ?string $newChecklistDueDate = null;

    // Edit ticket modal
    public string $editSubject = '';

    public string $editDescription = '';

    /** @var \Illuminate\Http\UploadedFile[]|array */
    public $editAttachmentFiles = [];

    public string $editLinkUrl = '';

    // Approval
    public string $approvalComment = '';

    public bool $showApprovalModal = false;

    public string $approvalAction = '';

    private ?Ticket $ticketCache = null;

    public function getListeners(): array
    {
        return [
            "echo-private:ticket.{$this->ticketPublicId},.assignee.changed" => '$refresh',
        ];
    }

    public function mount(int $ticketId, string $ticketPublicId, bool $canSeeInternalNotes, bool $canWriteInternalNotes): void
    {
        $this->ticketId = $ticketId;
        $this->ticketPublicId = $ticketPublicId;
        $this->canSeeInternalNotes = $canSeeInternalNotes;
        $this->canWriteInternalNotes = $canWriteInternalNotes;
    }

    private function getTicket(): Ticket
    {
        if ($this->ticketCache instanceof Ticket) {
            return $this->ticketCache;
        }

        $this->ticketCache = Ticket::query()
            ->with(['participants:id,name,email', 'creator:id,name,email', 'assignees:id,name,email'])
            ->whereKey($this->ticketId)
            ->where('organization_id', session('current_organization_id'))
            ->firstOrFail();

        return $this->ticketCache;
    }

    private function guardAgainstLock(Ticket $ticket): void
    {
        if (! $ticket->isLocked()) {
            return;
        }
        $user = Auth::user();
        if ($user && $ticket->canBypassLock($user)) {
            return;
        }
        abort(403, __('tickets.locked'));
    }

    private function canAssignTicket(Ticket $ticket): bool
    {
        return Gate::allows('assign', $ticket);
    }

    private function isInternalStaffUser(int $userId, int $organizationId): bool
    {
        return User::query()
            ->whereKey($userId)
            ->whereHas('organizations', function ($q) use ($organizationId) {
                $q->where('organization_memberships.organization_id', $organizationId)
                    ->whereIn('organization_memberships.role', ['owner', 'admin', 'agent']);
            })
            ->exists();
    }

    private function notifyExternalClientsOfTicketRouting(Ticket $ticket, ClientAssignmentContext $context): void
    {
        TicketClientRoutingNotifier::notify($ticket, $context);
    }

    /**
     * Contexte mail client après changement sur les assignés (retrait, etc.).
     */
    private function clientRoutingContextAfterAssigneeChange(Ticket $ticket): ClientAssignmentContext
    {
        $ticket->load('assignees');
        if ($ticket->assignees->isEmpty()) {
            return ClientAssignmentContext::generic();
        }
        if ($ticket->assignees->count() > 1) {
            return new ClientAssignmentContext(ClientAssignmentClientScenario::MultipleMembers);
        }

        return new ClientAssignmentContext(
            ClientAssignmentClientScenario::PersonNamed,
            $ticket->assignees->first()->name,
        );
    }

    // ─── Assignees ───────────────────────────────────────────────────

    public function assignToMe(): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);
        $ticket = $this->getTicket();
        Gate::authorize('assign', $ticket);
        $this->setAssignee((int) $user->id);
    }

    public function setAssignee($userId): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);
        $ticket = $this->getTicket();
        Gate::authorize('assign', $ticket);
        $userId = (int) $userId;
        $this->guardAgainstLock($ticket);
        if ($userId === 0) {
            $current = $ticket->assignees()->first();
            if ($current) {
                $this->removeAssignee((int) $current->id);
            }

            return;
        }
        $orgMember = User::whereKey($userId)->assignableInOrganization((int) $ticket->organization_id)->firstOrFail();
        $previousAssignee = $ticket->assignees()->first();
        $ticket->assignees()->newPivotQuery()->where('role', 'responsible')->update(['role' => 'collaborator']);
        if ($ticket->assignees()->where('users.id', $userId)->exists()) {
            $ticket->assignees()->updateExistingPivot($userId, ['role' => 'responsible', 'assigned_by' => $user->id]);
        } else {
            $ticket->assignees()->attach($userId, ['assigned_by' => $user->id, 'role' => 'responsible']);
        }
        $ticket->update([
            'assigned_to' => $userId,
            'assigned_by' => $user->id,
            'assigned_at' => now(),
        ]);
        $auditBody = $previousAssignee && (int) $previousAssignee->id !== $userId
            ? __('tickets.assignment_audit.reassign_responsible', ['actor' => $user->name, 'target' => $orgMember->name])
            : __('tickets.assignment_audit.assign_user', ['actor' => $user->name, 'target' => $orgMember->name]);
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'type' => TicketMessageType::System,
            'body' => $auditBody,
            'meta' => ['action' => 'assignee_added', 'user_id' => $userId, 'assigned_by' => $user->id],
        ]);
        $orgMember->notify(new TicketAssigneeNotification($ticket, $user, 'assigned'));
        event(new TicketAssigneeChanged($ticket->id, $ticket->subject, $userId, 'assigned', $user->name, $ticket->public_id));
        event(new UserNotificationReceived($userId, 'ticket_assignee'));
        $this->notifyExternalClientsOfTicketRouting(
            $ticket,
            new ClientAssignmentContext(ClientAssignmentClientScenario::PersonNamed, $orgMember->name),
        );
        CacheHelper::invalidateDashboard((int) $ticket->organization_id);
        CacheHelper::invalidateReports((int) $ticket->organization_id);
        CacheHelper::invalidateTicketCounts((int) $ticket->organization_id);
    }

    public function addAssignee(int $userId): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);
        $ticket = $this->getTicket();
        if (! $this->canAssignTicket($ticket)) {
            abort(403);
        }
        $this->guardAgainstLock($ticket);
        $orgMember = User::whereKey($userId)->assignableInOrganization((int) $ticket->organization_id)->firstOrFail();
        if ($ticket->assignees()->where('users.id', $userId)->exists()) {
            return;
        }
        $hasAssignees = $ticket->assignees()->exists();
        $role = $hasAssignees ? 'collaborator' : 'responsible';
        $ticket->assignees()->attach($userId, ['assigned_by' => $user->id, 'role' => $role]);
        if (! $ticket->assigned_to) {
            $ticket->update(['assigned_to' => $userId, 'assigned_by' => $user->id, 'assigned_at' => now()]);
        }
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'type' => TicketMessageType::System,
            'body' => __('tickets.assignment_audit.add_assignee', ['actor' => $user->name, 'target' => $orgMember->name]),
            'meta' => ['action' => 'assignee_added', 'user_id' => $userId],
        ]);
        $orgMember->notify(new TicketAssigneeNotification($ticket, $user, 'assigned'));
        event(new TicketAssigneeChanged($ticket->id, $ticket->subject, $userId, 'assigned', $user->name, $ticket->public_id));
        event(new UserNotificationReceived($userId, 'ticket_assignee'));
        $this->notifyExternalClientsOfTicketRouting(
            $ticket,
            new ClientAssignmentContext(ClientAssignmentClientScenario::PersonNamed, $orgMember->name),
        );
        CacheHelper::invalidateDashboard((int) $ticket->organization_id);
        CacheHelper::invalidateReports((int) $ticket->organization_id);
        CacheHelper::invalidateTicketCounts((int) $ticket->organization_id);

        $ticket->load('assignees');
        WebhookService::dispatch((int) $ticket->organization_id, 'ticket.assigned', [
            'ticket_id' => $ticket->public_id,
            'assignees' => $ticket->assignees->map(fn ($a) => ['id' => $a->id, 'name' => $a->name, 'role' => $a->pivot->role ?? 'collaborator'])->toArray(),
            'changed_by' => $user->id,
        ]);
    }

    public function removeAssignee(int $userId): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);
        $ticket = $this->getTicket();
        if (! $this->canAssignTicket($ticket)) {
            abort(403);
        }
        $this->guardAgainstLock($ticket);
        $removedUser = User::find($userId);
        $wasResponsible = $ticket->assignees()->where('users.id', $userId)->wherePivot('role', 'responsible')->exists();
        $ticket->assignees()->detach($userId);
        if ($wasResponsible) {
            $firstCollaborator = $ticket->assignees()->first();
            if ($firstCollaborator) {
                $ticket->assignees()->updateExistingPivot($firstCollaborator->id, ['role' => 'responsible']);
            }
        }
        $newResponsible = $ticket->assignees()->wherePivot('role', 'responsible')->first();
        $ticket->update(['assigned_to' => $newResponsible?->id, 'assigned_by' => $newResponsible ? $user->id : null, 'assigned_at' => $newResponsible ? now() : null]);
        if ($removedUser) {
            TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => null,
                'type' => TicketMessageType::System,
                'body' => __('tickets.assignment_audit.remove_assignee', ['actor' => $user->name, 'target' => $removedUser->name]),
                'meta' => ['action' => 'assignee_removed', 'user_id' => $userId],
            ]);
            $removedUser->notify(new TicketAssigneeNotification($ticket, $user, 'unassigned'));
            event(new TicketAssigneeChanged($ticket->id, $ticket->subject, $userId, 'unassigned', $user->name, $ticket->public_id));
            event(new UserNotificationReceived($userId, 'ticket_assignee'));
        }
        $this->notifyExternalClientsOfTicketRouting($ticket, $this->clientRoutingContextAfterAssigneeChange($ticket->fresh()));
        CacheHelper::invalidateDashboard((int) $ticket->organization_id);
        CacheHelper::invalidateReports((int) $ticket->organization_id);
        CacheHelper::invalidateTicketCounts((int) $ticket->organization_id);

        $ticket->load('assignees');
        WebhookService::dispatch((int) $ticket->organization_id, 'ticket.assigned', [
            'ticket_id' => $ticket->public_id,
            'assignees' => $ticket->assignees->map(fn ($a) => ['id' => $a->id, 'name' => $a->name, 'role' => $a->pivot->role ?? 'collaborator'])->toArray(),
            'changed_by' => $user->id,
        ]);
    }

    public function promoteToResponsible(int $userId): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);
        $ticket = $this->getTicket();
        if (! $this->canAssignTicket($ticket)) {
            abort(403);
        }
        $this->guardAgainstLock($ticket);
        if (! $ticket->assignees()->where('users.id', $userId)->exists()) {
            return;
        }
        $promoted = User::find($userId);
        $ticket->assignees()->newPivotQuery()->where('role', 'responsible')->update(['role' => 'collaborator']);
        $ticket->assignees()->updateExistingPivot($userId, ['role' => 'responsible']);
        $ticket->update(['assigned_to' => $userId, 'assigned_by' => $user->id, 'assigned_at' => now()]);
        if ($promoted) {
            TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => null,
                'type' => TicketMessageType::System,
                'body' => __('tickets.assignment_audit.set_responsible', ['actor' => $user->name, 'target' => $promoted->name]),
                'meta' => ['action' => 'promote_responsible', 'user_id' => $userId],
            ]);
        }
        $routingCtx = $promoted?->name
            ? new ClientAssignmentContext(ClientAssignmentClientScenario::PersonNamed, $promoted->name)
            : ClientAssignmentContext::generic();
        $this->notifyExternalClientsOfTicketRouting($ticket, $routingCtx);
        CacheHelper::invalidateDashboard((int) $ticket->organization_id);
        CacheHelper::invalidateReports((int) $ticket->organization_id);
        CacheHelper::invalidateTicketCounts((int) $ticket->organization_id);
    }

    // ─── Participants ────────────────────────────────────────────────

    public function addParticipant(int $userId): void
    {
        $user = Auth::user();
        $ticket = $this->getTicket();
        Gate::authorize('view', $ticket);
        $orgMember = User::whereKey($userId)->whereHas('organizations', fn ($q) => $q->where('organization_id', $ticket->organization_id))->firstOrFail();
        if ($ticket->created_by === (int) $userId || $ticket->assignees()->where('users.id', $userId)->exists()) {
            return;
        }
        $ticket->participants()->syncWithoutDetaching([$userId => ['added_by' => $user->id]]);
        TicketMessage::create(['ticket_id' => $ticket->id, 'user_id' => null, 'type' => TicketMessageType::System, 'body' => __(':user a été ajouté à la discussion.', ['user' => $orgMember->name]), 'meta' => ['action' => 'participant_added', 'user_id' => $userId]]);
        $orgMember->notify(new TicketAssigneeNotification($ticket, $user, 'participant_added'));
        event(new TicketAssigneeChanged($ticket->id, $ticket->subject, $userId, 'participant_added', $user->name, $ticket->public_id));
        event(new UserNotificationReceived($userId, 'ticket_assignee'));
    }

    public function removeParticipant(int $userId): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);
        $ticket = $this->getTicket();
        if (! $ticket->hasDiscussionAccess((int) $user->id)) {
            abort(403);
        }
        $removedUser = User::find($userId);
        $ticket->participants()->detach($userId);
        if ($removedUser) {
            TicketMessage::create(['ticket_id' => $ticket->id, 'user_id' => null, 'type' => TicketMessageType::System, 'body' => __(':user a été retiré de la discussion.', ['user' => $removedUser->name]), 'meta' => ['action' => 'participant_removed', 'user_id' => $userId]]);
            $removedUser->notify(new TicketAssigneeNotification($ticket, $user, 'participant_removed'));
            event(new TicketAssigneeChanged($ticket->id, $ticket->subject, $userId, 'participant_removed', $user->name, $ticket->public_id));
            event(new UserNotificationReceived($userId, 'ticket_assignee'));
        }
    }

    // ─── Status / Priority / Group ───────────────────────────────────

    public function changeStatus(string $status): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);
        $newStatus = TicketStatus::tryFrom($status);
        if (! $newStatus) {
            return;
        }
        $ticket = $this->getTicket();
        if (! $this->canAssignTicket($ticket)) {
            abort(403);
        }
        $oldStatus = $ticket->status;
        if ($oldStatus === $newStatus) {
            return;
        }
        if ($ticket->isLocked() && ! $ticket->canBypassLock($user)) {
            abort(403, __('tickets.locked'));
        }
        if (! $this->passesResolutionStrictMode($ticket, $newStatus)) {
            return;
        }

        $isClosed = in_array($newStatus, [TicketStatus::Resolved, TicketStatus::Closed], true);
        $ticket->update(['status' => $newStatus, 'closed_by' => $isClosed ? $user->id : null, 'closed_at' => $isClosed ? now() : null]);
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'type' => TicketMessageType::System,
            'body' => __('tickets.status_changed', ['actor' => $user->name, 'old' => __('tickets.status.'.$oldStatus->value), 'new' => __('tickets.status.'.$newStatus->value)]),
            'meta' => ['action' => 'status_changed', 'old' => $oldStatus->value, 'new' => $newStatus->value],
        ]);
        OrganizationAuditService::log('ticket.status_changed', 'Ticket', $ticket->id, ['ticket' => $ticket->subject, 'old_status' => $oldStatus->value, 'new_status' => $newStatus->value]);

        if ($newStatus === TicketStatus::Pending) {
            SlaService::pause($ticket);
        } elseif ($oldStatus === TicketStatus::Pending && $newStatus !== TicketStatus::Pending) {
            SlaService::resume($ticket);
        }
        if (in_array($newStatus, [TicketStatus::Resolved, TicketStatus::Closed], true)) {
            SlaService::recordResolution($ticket);
        }
        if (in_array($oldStatus, [TicketStatus::Resolved, TicketStatus::Closed], true) && ! in_array($newStatus, [TicketStatus::Resolved, TicketStatus::Closed], true)) {
            SlaService::onReopened($ticket);
            $this->notifyTicketReopened($ticket, (int) $user->id, $user->name);
        }

        CacheHelper::invalidateDashboard((int) $ticket->organization_id);
        CacheHelper::invalidateReports((int) $ticket->organization_id);
        CacheHelper::invalidateTicketCounts((int) $ticket->organization_id);
        AutomationService::evaluate($ticket, 'status_changed');

        $ticket->load(['category', 'priority', 'group', 'creator', 'assignees']);
        WebhookService::dispatch((int) $ticket->organization_id, 'ticket.status_changed', ['ticket_id' => $ticket->public_id, 'old_status' => $oldStatus->value, 'new_status' => $newStatus->value, 'changed_by' => $user->id]);

        $wasReopened = in_array($oldStatus, [TicketStatus::Resolved, TicketStatus::Closed], true) && ! in_array($newStatus, [TicketStatus::Resolved, TicketStatus::Closed], true);
        if (! $wasReopened && $ticket->created_by && (int) $ticket->created_by !== (int) $user->id) {
            $creator = $ticket->creator;
            if ($creator) {
                $orgName = $ticket->organization?->name ?? config('app.name', 'Support');
                $creator->notify(new TicketStatusChangedNotification(
                    ticketId: $ticket->id,
                    ticketPublicId: $ticket->public_id,
                    ticketReference: $ticket->shortReference(),
                    ticketSubject: $ticket->subject,
                    oldStatus: $oldStatus->value,
                    newStatus: $newStatus->value,
                    organizationId: (int) $ticket->organization_id,
                    organizationName: $orgName,
                ));
                event(new UserNotificationReceived((int) $creator->id, 'ticket_status_changed'));

                // Send CSAT request when ticket is resolved
                if ($newStatus === TicketStatus::Resolved) {
                    try {
                        if (! $ticket->satisfactionRating()->exists()) {
                            $creator->notify(new TicketSatisfactionRequestNotification(
                                ticketId: $ticket->id,
                                ticketPublicId: $ticket->public_id,
                                ticketReference: $ticket->shortReference(),
                                ticketSubject: $ticket->subject,
                                organizationId: (int) $ticket->organization_id,
                                organizationName: $orgName,
                            ));
                        }
                    } catch (\Throwable) {
                        // Table may not exist yet if migration hasn't run
                    }
                }
            }
        }

        $this->dispatch('ticket-updated');
    }

    public function changePriority(int $priorityId): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);
        $ticket = $this->getTicket();
        if (! $this->canAssignTicket($ticket)) {
            abort(403);
        }
        $this->guardAgainstLock($ticket);
        $oldPriority = $ticket->priority;
        $newPriority = TicketPriority::where('organization_id', $ticket->organization_id)->where('is_active', true)->whereKey($priorityId)->firstOrFail();
        if ($ticket->ticket_priority_id === $newPriority->id) {
            return;
        }
        $ticket->update(['ticket_priority_id' => $newPriority->id]);
        SlaService::onPriorityChanged($ticket, $oldPriority?->id);
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'type' => TicketMessageType::System,
            'body' => __(':actor a changé la priorité : :old → :new', ['actor' => $user->name, 'old' => $oldPriority?->name ?? '—', 'new' => $newPriority->name]),
            'meta' => ['action' => 'priority_changed', 'old_id' => $oldPriority?->id, 'new_id' => $newPriority->id],
        ]);
        CacheHelper::invalidateDashboard((int) $ticket->organization_id);
        CacheHelper::invalidateReports((int) $ticket->organization_id);
        CacheHelper::invalidateTicketCounts((int) $ticket->organization_id);
        AutomationService::evaluate($ticket, 'priority_changed');

        $this->dispatch('ticket-updated');
    }

    public function changeGroup($groupId): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);
        $ticket = $this->getTicket();
        if (! $this->canAssignTicket($ticket)) {
            abort(403);
        }
        $this->guardAgainstLock($ticket);
        $id = $groupId === '' || $groupId === null ? null : (int) $groupId;
        $group = null;
        if ($id !== null) {
            $group = TicketGroup::where('organization_id', $ticket->organization_id)
                ->where('is_active', true)
                ->whereKey($id)
                ->firstOrFail();
        }
        if ($ticket->ticket_group_id === $id) {
            return;
        }
        $ticket->update(['ticket_group_id' => $id]);
        $fresh = $ticket->fresh();
        $auditBody = $group
            ? __('tickets.assignment_audit.assign_group', ['actor' => $user->name, 'group' => $group->name])
            : __('tickets.assignment_audit.clear_group', ['actor' => $user->name]);
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'type' => TicketMessageType::System,
            'body' => $auditBody,
            'meta' => ['action' => 'group_changed', 'ticket_group_id' => $id],
        ]);
        $clientContext = $group
            ? new ClientAssignmentContext(ClientAssignmentClientScenario::Group, $group->name)
            : ClientAssignmentContext::generic();
        $this->notifyExternalClientsOfTicketRouting($fresh, $clientContext);

        // Notify all group members (in-app) so they know this ticket concerns them
        if ($group) {
            try {
                $groupMembers = $group->members()
                    ->where('users.id', '!=', $user->id)
                    ->get(['users.id', 'users.name', 'users.email']);

                foreach ($groupMembers as $member) {
                    $member->notify(new TicketAssigneeNotification($ticket, $user, 'assigned'));
                    event(new TicketAssigneeChanged($ticket->id, $ticket->subject, (int) $member->id, 'assigned', $user->name, $ticket->public_id));
                    event(new UserNotificationReceived((int) $member->id, 'ticket_assignee'));
                }
            } catch (\Throwable) {
                // Table may not exist yet if migration hasn't run
            }
        }

        CacheHelper::invalidateDashboard((int) $ticket->organization_id);
    }

    public function setAssignedToFunction($functionId): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);
        $ticket = $this->getTicket();
        if (! ($user instanceof User) || ! $user->hasPermission(Permission::TicketsAssign)) {
            abort(403);
        }
        $id = $functionId === '' || $functionId === null ? null : (int) $functionId;
        if ($id !== null) {
            OrganizationFunction::query()->where('organization_id', $ticket->organization_id)->whereKey($id)->firstOrFail();
        }
        if ($ticket->assigned_to_function_id === $id) {
            return;
        }
        $ticket->update(['assigned_to_function_id' => $id]);
        $fresh = $ticket->fresh();
        $fnName = $id !== null
            ? OrganizationFunction::whereKey($id)->value('name')
            : null;
        $auditBody = $id !== null
            ? __('tickets.assignment_audit.assign_function', ['actor' => $user->name, 'name' => $fnName ?? (string) $id])
            : __('tickets.assignment_audit.clear_function', ['actor' => $user->name]);
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'type' => TicketMessageType::System,
            'body' => $auditBody,
            'meta' => ['action' => 'function_changed', 'assigned_to_function_id' => $id],
        ]);
        $clientContext = $id !== null
            ? new ClientAssignmentContext(ClientAssignmentClientScenario::FunctionTeam, $fnName)
            : ClientAssignmentContext::generic();
        $this->notifyExternalClientsOfTicketRouting($fresh, $clientContext);
    }

    public function updateDueDate(?string $date): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);
        $ticket = $this->getTicket();
        $this->guardAgainstLock($ticket);
        $hasEditPerm = $user instanceof User && $user->hasPermission(Permission::TicketsEdit);
        $isCreator = (int) $ticket->created_by === (int) $user->id;
        $isAssignee = $ticket->assignees()->where('users.id', $user->id)->exists();
        if (! ($hasEditPerm || $isCreator || $isAssignee)) {
            abort(403);
        }
        $oldDate = $ticket->due_date !== null
            ? \Carbon\Carbon::parse($ticket->due_date)->format('Y-m-d')
            : null;
        $newDate = ($date && $date !== '') ? $date : null;
        if ($oldDate === $newDate) {
            return;
        }
        $ticket->update(['due_date' => $newDate]);
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'type' => TicketMessageType::System,
            'body' => __(':actor a modifié l\'échéance : :old → :new', ['actor' => $user->name, 'old' => $oldDate ? \Carbon\Carbon::parse($oldDate)->translatedFormat('d M Y') : '—', 'new' => $newDate ? \Carbon\Carbon::parse($newDate)->translatedFormat('d M Y') : '—']),
            'meta' => ['action' => 'due_date_changed', 'old' => $oldDate, 'new' => $newDate],
        ]);
    }

    // ─── Archive / Delete / Edit ─────────────────────────────────────

    public function archiveTicket(): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);
        $ticket = $this->getTicket();
        if (! $ticket->hasDiscussionAccess((int) $user->id)) {
            abort(403);
        }
        $this->guardAgainstLock($ticket);
        $canArchive = $user instanceof User && $user->hasPermission(Permission::TicketsArchive);
        $isCreator = (int) $ticket->created_by === (int) $user->id;
        $isAssignee = $ticket->assignees()->where('users.id', $user->id)->exists();
        if (! ($canArchive || $isCreator || $isAssignee)) {
            abort(403);
        }
        $ticket->update(['archived_at' => now()]);
        CacheHelper::invalidateDashboard((int) $ticket->organization_id);
        CacheHelper::invalidateReports((int) $ticket->organization_id);
        CacheHelper::invalidateTicketCounts((int) $ticket->organization_id);
        session()->flash('tickets_status', __('Ticket archivé.'));
    }

    public function restoreTicket(): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);
        $ticket = $this->getTicket();
        if (! $ticket->hasDiscussionAccess((int) $user->id)) {
            abort(403);
        }
        $canArchive = $user instanceof User && $user->hasPermission(Permission::TicketsArchive);
        $isCreator = (int) $ticket->created_by === (int) $user->id;
        $isAssignee = $ticket->assignees()->where('users.id', $user->id)->exists();
        if (! ($canArchive || $isCreator || $isAssignee)) {
            abort(403);
        }
        $ticket->update(['archived_at' => null]);
        CacheHelper::invalidateDashboard((int) $ticket->organization_id);
        CacheHelper::invalidateReports((int) $ticket->organization_id);
        CacheHelper::invalidateTicketCounts((int) $ticket->organization_id);
        session()->flash('tickets_status', __('Ticket restauré.'));
    }

    public function deleteTicket(): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);
        $ticket = $this->getTicket();
        if (! $ticket->hasDiscussionAccess((int) $user->id)) {
            abort(403);
        }
        $hasDeletePerm = $user instanceof User && $user->hasPermission(Permission::TicketsDelete);
        $isCreator = (int) $ticket->created_by === (int) $user->id;
        if (! ($hasDeletePerm || ($isCreator && $ticket->status !== TicketStatus::Closed))) {
            abort(403);
        }
        $orgId = (int) $ticket->organization_id;
        $ticket->delete();
        CacheHelper::invalidateDashboard($orgId);
        CacheHelper::invalidateReports($orgId);
        CacheHelper::invalidateTicketCounts($orgId);
        session()->flash('tickets_status', __('Ticket supprimé.'));
        $this->redirect(route('tickets.index'));
    }

    public function openEditTicketModal(): void
    {
        if (! $this->canEditTicketBase()) {
            abort(403);
        }
        $ticket = $this->getTicket();
        $this->editSubject = $ticket->subject ?? '';
        $this->editDescription = $ticket->description ?? '';
        $this->editAttachmentFiles = [];
        $this->editLinkUrl = '';
        $this->dispatch('open-modal', 'edit-ticket');
    }

    public function updateSubject(): void
    {
        if (! $this->canEditTicketBase()) {
            abort(403);
        }
        $ticket = $this->getTicket();
        $this->guardAgainstLock($ticket);
        $subject = trim($this->editSubject ?? '');
        if ($subject === '' || $ticket->subject === $subject) {
            return;
        }
        $ticket->update(['subject' => $subject]);
        session()->flash('tickets_status', __('Titre mis à jour.'));
        $this->dispatch('ticket-updated');
    }

    public function updateDescription(): void
    {
        if (! $this->canEditTicketBase()) {
            abort(403);
        }
        $ticket = $this->getTicket();
        $this->guardAgainstLock($ticket);
        $ticket->update(['description' => $this->editDescription ?? '']);
        session()->flash('tickets_status', __('Description mise à jour.'));
    }

    public function addTicketAttachmentFile(): void
    {
        if (! $this->canEditTicketBase()) {
            abort(403);
        }
        $ticket = $this->getTicket();
        $this->guardAgainstLock($ticket);
        $this->validate(['editAttachmentFiles.*' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,csv,txt,zip']]);
        $files = $this->editAttachmentFiles ?? [];
        if (! is_array($files) || count($files) === 0) {
            return;
        }
        $orgId = (int) $ticket->organization_id;
        $attachments = is_array($ticket->attachments) ? $ticket->attachments : ['files' => [], 'links' => []];
        $filesList = $attachments['files'] ?? [];
        foreach ($files as $file) {
            $original = (string) ($file->getClientOriginalName() ?: 'file');
            $ext = (string) ($file->getClientOriginalExtension() ?: '');
            $safeBase = Str::slug(pathinfo($original, PATHINFO_FILENAME)) ?: 'file';
            $filename = $safeBase.'-'.Str::lower(Str::random(10)).($ext ? '.'.$ext : '');
            $path = $file->storeAs("ticket-attachments/org-{$orgId}/ticket-{$ticket->id}", $filename, 'local');
            $filesList[] = ['disk' => 'local', 'path' => $path, 'name' => $original, 'size' => method_exists($file, 'getSize') ? (int) $file->getSize() : null, 'mime' => method_exists($file, 'getMimeType') ? (string) $file->getMimeType() : null, 'url' => route('tickets.attachment', ['ticket' => $ticket->public_id, 'filename' => $filename])];
        }
        $attachments['files'] = $filesList;
        $ticket->update(['attachments' => $attachments]);
        $this->editAttachmentFiles = [];
        session()->flash('tickets_status', __('Pièce jointe ajoutée.'));
    }

    public function addTicketAttachmentLink(): void
    {
        if (! $this->canEditTicketBase()) {
            abort(403);
        }
        $ticket = $this->getTicket();
        $this->guardAgainstLock($ticket);
        $this->validate(['editLinkUrl' => ['required', 'string', 'url', 'max:2000']], [], ['editLinkUrl' => __('URL')]);
        $url = trim($this->editLinkUrl);
        $attachments = is_array($ticket->attachments) ? $ticket->attachments : ['files' => [], 'links' => []];
        $linksList = $attachments['links'] ?? [];
        $linksList[] = ['url' => $url];
        $attachments['links'] = $linksList;
        $ticket->update(['attachments' => $attachments]);
        $this->editLinkUrl = '';
        session()->flash('tickets_status', __('Lien ajouté.'));
    }

    public function removeTicketAttachment(string $type, int $index): void
    {
        if (! $this->canEditTicketBase()) {
            abort(403);
        }
        $ticket = $this->getTicket();
        $this->guardAgainstLock($ticket);
        if (! in_array($type, ['files', 'links'], true)) {
            return;
        }
        $attachments = is_array($ticket->attachments) ? $ticket->attachments : ['files' => [], 'links' => []];
        $list = $attachments[$type] ?? [];
        if (! isset($list[$index])) {
            return;
        }
        if ($type === 'files') {
            $item = $list[$index];
            $path = $item['path'] ?? null;
            if ($path && Storage::disk($item['disk'] ?? 'public')->exists($path)) {
                Storage::disk($item['disk'] ?? 'public')->delete($path);
            }
        }
        array_splice($list, $index, 1);
        $attachments[$type] = $list;
        $ticket->update(['attachments' => $attachments]);
        session()->flash('tickets_status', __('Pièce jointe supprimée.'));
    }

    // ─── Checklist ───────────────────────────────────────────────────

    public function openAddChecklistForm(): void
    {
        $this->showAddChecklistItem = true;
    }

    public function closeAddChecklistForm(): void
    {
        $this->showAddChecklistItem = false;
    }

    public function toggleChecklistItem(int $id): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);
        $ticket = $this->getTicket();
        if (! $ticket->hasDiscussionAccess((int) $user->id)) {
            abort(403);
        }
        $this->guardAgainstLock($ticket);
        /** @var TicketChecklistItem $item */
        $item = $ticket->checklistItems()->whereKey($id)->firstOrFail();
        $userId = (int) $user->id;
        $isItemAssignee = $item->assigned_to && (int) $item->assigned_to === $userId;
        $isItemPivotAssignee = $item->assignees()->where('user_id', $userId)->exists();
        $hasEditPerm = $user instanceof User && $user->hasPermission(Permission::TicketsEdit);
        $isTicketAssignee = $ticket->assignees->contains('id', $userId);
        $isTicketCreator = $ticket->created_by && (int) $ticket->created_by === $userId;
        if (! ($isItemAssignee || $isItemPivotAssignee || $hasEditPerm || $isTicketAssignee || $isTicketCreator)) {
            abort(403);
        }
        if ($item->is_done) {
            $item->markUndone($userId);
        } else {
            $item->markDone($userId);
        }
    }

    public function claimChecklistItem(int $id): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);
        $ticket = $this->getTicket();
        if (! $ticket->hasDiscussionAccess((int) $user->id)) {
            abort(403);
        }
        /** @var TicketChecklistItem $item */
        $item = $ticket->checklistItems()->whereKey($id)->firstOrFail();
        if (! $item->assigned_to_function_id || $item->assigned_to) {
            return;
        }
        $orgId = (int) session('current_organization_id');
        $belongsToFunction = DB::table('organization_memberships')->where('organization_id', $orgId)->where('user_id', $user->id)->where('organization_function_id', $item->assigned_to_function_id)->exists();
        if (! $belongsToFunction || ! $this->isInternalStaffUser((int) $user->id, $orgId)) {
            abort(403);
        }
        $item->claim((int) $user->id);
    }

    public function deleteChecklistItem(int $id): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);
        $ticket = $this->getTicket();
        if (! $ticket->hasDiscussionAccess((int) $user->id)) {
            abort(403);
        }
        $this->guardAgainstLock($ticket);
        $hasEditPerm = $user instanceof User && $user->hasPermission(Permission::TicketsEdit);
        $isAssignee = $ticket->assignees->contains('id', $user->id);
        $isCreator = (int) $ticket->created_by === (int) $user->id;
        if (! ($hasEditPerm || $isAssignee || $isCreator)) {
            abort(403);
        }
        $ticket->checklistItems()->whereKey($id)->delete();
    }

    public function updateChecklistItemTitle(int $id, string $title): void
    {
        $title = trim($title);
        if ($title === '') {
            return;
        }
        $user = Auth::user();
        abort_if(! $user, 403);
        $ticket = $this->getTicket();
        if (! $ticket->hasDiscussionAccess((int) $user->id)) {
            abort(403);
        }
        $this->guardAgainstLock($ticket);
        $hasEditPerm = $user instanceof User && $user->hasPermission(Permission::TicketsEdit);
        $isAssignee = $ticket->assignees->contains('id', $user->id);
        $isCreator = (int) $ticket->created_by === (int) $user->id;
        if (! ($hasEditPerm || $isAssignee || $isCreator)) {
            abort(403);
        }
        /** @var TicketChecklistItem $item */
        $item = $ticket->checklistItems()->whereKey($id)->firstOrFail();
        $item->update(['title' => $title]);
    }

    public function addChecklistItemToTicket(): void
    {
        $this->newChecklistAssignedTo = ($this->newChecklistAssignedTo === '' || $this->newChecklistAssignedTo === null) ? null : (int) $this->newChecklistAssignedTo;
        $this->newChecklistAssignedToFunction = ($this->newChecklistAssignedToFunction === '' || $this->newChecklistAssignedToFunction === null) ? null : (int) $this->newChecklistAssignedToFunction;
        $this->newChecklistDueDate = ($this->newChecklistDueDate === '' || $this->newChecklistDueDate === null) ? null : $this->newChecklistDueDate;
        if ($this->newChecklistAssignedToFunction) {
            $this->newChecklistAssignedTo = null;
        }
        $this->validate(['newChecklistTitle' => ['required', 'string', 'max:500'], 'newChecklistAssignedTo' => ['nullable', 'integer', 'exists:users,id'], 'newChecklistAssignedToFunction' => ['nullable', 'integer', 'exists:organization_functions,id'], 'newChecklistDueDate' => ['nullable', 'date']]);
        $user = Auth::user();
        abort_if(! $user, 403);
        $ticket = $this->getTicket();
        if (! $ticket->hasDiscussionAccess((int) $user->id)) {
            abort(403);
        }
        $this->guardAgainstLock($ticket);
        if ($this->newChecklistAssignedTo && ! $this->isInternalStaffUser((int) $this->newChecklistAssignedTo, (int) $ticket->organization_id)) {
            $this->addError('newChecklistAssignedTo', 'Cette personne ne fait pas partie de l\'équipe interne.');

            return;
        }
        $maxOrder = $ticket->checklistItems()->max('sort_order') ?? -1;
        $item = TicketChecklistItem::create(['ticket_id' => $ticket->id, 'title' => trim($this->newChecklistTitle), 'assigned_to' => $this->newChecklistAssignedTo, 'assigned_to_function_id' => $this->newChecklistAssignedToFunction, 'due_date' => $this->newChecklistDueDate ? \Carbon\Carbon::parse($this->newChecklistDueDate) : null, 'sort_order' => $maxOrder + 1]);
        if ($this->newChecklistAssignedTo) {
            $item->assignees()->attach($this->newChecklistAssignedTo, ['role' => 'responsible', 'assigned_by' => $user->id]);
            if ((int) $this->newChecklistAssignedTo !== (int) $user->id) {
                $assignedUser = User::find($this->newChecklistAssignedTo);
                if ($assignedUser) {
                    $assignedUser->notify(new ChecklistItemAssignedNotification($item, $ticket, $user, 'assigned'));
                    event(new UserNotificationReceived((int) $assignedUser->id, 'checklist_item_assigned'));
                }
            }
        }
        $this->showAddChecklistItem = false;
        $this->newChecklistTitle = '';
        $this->newChecklistAssignedTo = null;
        $this->newChecklistAssignedToFunction = null;
        $this->newChecklistDueDate = null;
    }

    public function addChecklistItemAssignee(int $itemId, int $userId): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);
        $ticket = $this->getTicket();
        $this->guardAgainstLock($ticket);
        if (! $ticket->hasDiscussionAccess((int) $user->id)) {
            abort(403);
        }
        /** @var TicketChecklistItem $item */
        $item = $ticket->checklistItems()->whereKey($itemId)->firstOrFail();
        if (! $this->isInternalStaffUser($userId, (int) $ticket->organization_id)) {
            $this->addError('checklist', 'Cette personne ne fait pas partie de l\'équipe interne.');

            return;
        }
        if ($item->assignees()->where('user_id', $userId)->exists()) {
            return;
        }
        $hasAssignees = $item->assignees()->exists();
        $role = $hasAssignees ? 'collaborator' : 'responsible';
        $item->assignees()->attach($userId, ['role' => $role, 'assigned_by' => $user->id]);
        if (! $hasAssignees) {
            $item->update(['assigned_to' => $userId]);
        }
        if ((int) $userId !== (int) $user->id) {
            $assignedUser = User::find($userId);
            if ($assignedUser) {
                $assignedUser->notify(new ChecklistItemAssignedNotification($item, $ticket, $user, 'assigned'));
                event(new UserNotificationReceived($userId, 'checklist_item_assigned'));
            }
        }
    }

    public function removeChecklistItemAssignee(int $itemId, int $userId): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);
        $ticket = $this->getTicket();
        $this->guardAgainstLock($ticket);
        if (! $ticket->hasDiscussionAccess((int) $user->id)) {
            abort(403);
        }
        /** @var TicketChecklistItem $item */
        $item = $ticket->checklistItems()->whereKey($itemId)->firstOrFail();
        $wasResponsible = $item->assignees()->where('user_id', $userId)->wherePivot('role', 'responsible')->exists();
        $item->assignees()->detach($userId);
        if ($wasResponsible) {
            $next = $item->assignees()->first();
            if ($next) {
                $item->assignees()->updateExistingPivot($next->id, ['role' => 'responsible']);
                $item->update(['assigned_to' => $next->id]);
            } else {
                $item->update(['assigned_to' => null]);
            }
        }
        if ($item->assigned_to && (int) $item->assigned_to === $userId) {
            $next = $item->assignees()->wherePivot('role', 'responsible')->first() ?? $item->assignees()->first();
            $item->update(['assigned_to' => $next?->id]);
        }
        if ((int) $userId !== (int) $user->id) {
            $removedUser = User::find($userId);
            if ($removedUser) {
                $removedUser->notify(new ChecklistItemAssignedNotification($item, $ticket, $user, 'unassigned'));
                event(new UserNotificationReceived($userId, 'checklist_item_assigned'));
            }
        }
    }

    // ─── Approval ────────────────────────────────────────────────────

    public function openApprovalModal(string $action): void
    {
        $this->approvalAction = $action;
        $this->approvalComment = '';
        $this->showApprovalModal = true;
    }

    public function cancelApproval(): void
    {
        $this->showApprovalModal = false;
        $this->approvalAction = '';
        $this->approvalComment = '';
    }

    public function submitApproval(): void
    {
        $this->validate(['approvalComment' => ['required', 'string', 'min:3', 'max:2000']]);
        $user = Auth::user();
        abort_if(! $user, 403);
        $ticket = $this->getTicket();
        if ($this->approvalAction === 'approve') {
            ApprovalService::approve($ticket, $user, $this->approvalComment);
        } elseif ($this->approvalAction === 'reject') {
            ApprovalService::reject($ticket, $user, $this->approvalComment);
        }
        $this->showApprovalModal = false;
        $this->approvalAction = '';
        $this->approvalComment = '';
    }

    // ─── Private helpers ─────────────────────────────────────────────

    private function canEditTicketBase(): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }
        $ticket = $this->getTicket();
        $hasEditPerm = $user instanceof User && $user->hasPermission(Permission::TicketsEdit);
        $isCreator = $ticket->created_by !== null && (int) $ticket->created_by === (int) $user->id;

        return $hasEditPerm || $isCreator;
    }

    private function passesResolutionStrictMode(Ticket $ticket, TicketStatus $newStatus): bool
    {
        if (! in_array($newStatus, [TicketStatus::Resolved, TicketStatus::Closed], true)) {
            return true;
        }
        $org = Organization::find($ticket->organization_id);
        $orgSettings = is_array($org?->settings) ? $org->settings : [];
        $resolutionMode = $orgSettings['workflow']['ticket_resolution_mode'] ?? 'flexible';
        if ($resolutionMode !== 'strict') {
            return true;
        }
        $agg = $ticket->checklistItems()
            ->getQuery()
            ->clone()
            ->reorder()
            ->selectRaw('count(*) as total_count, coalesce(sum(case when is_done then 1 else 0 end), 0) as done_count')
            ->first();
        $totalItems = (int) ($agg->total_count ?? 0);
        $doneItems = (int) ($agg->done_count ?? 0);
        if ($totalItems > 0 && $doneItems < $totalItems) {
            $this->dispatch('toast', type: 'error', message: __('tickets.ticket_resolution_strict_error'));

            return false;
        }

        return true;
    }

    private function notifyTicketReopened(Ticket $ticket, int $actorId, string $actorName): void
    {
        $notifyUserIds = collect([$ticket->created_by])->merge($ticket->assignees->pluck('id'))->merge($ticket->participants->pluck('id'))->filter()->unique()->diff([$actorId])->values();
        if ($notifyUserIds->isEmpty()) {
            return;
        }
        /** @var \Illuminate\Database\Eloquent\Collection<int, User> $recipients */
        $recipients = User::query()->whereIn('id', $notifyUserIds)->get();
        foreach ($recipients as $recipient) {
            if ($ticket->hasDiscussionAccess((int) $recipient->id)) {
                $recipient->notify(new TicketReopenedNotification($ticket, $actorId, $actorName));
                event(new UserNotificationReceived((int) $recipient->id, 'ticket_reopened'));
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function sidebarCachedOrganizationData(int $orgId, Ticket $ticket): array
    {
        $orgUsers = cache()->remember(CacheHelper::membersKey($orgId), CacheHelper::TTL, fn () => User::query()->whereHas('organizations', fn ($q) => $q->where('organization_id', $orgId))->orderBy('name')->limit(300)->get(['id', 'name', 'email', 'mention_tag']));
        $staffUsers = cache()->remember("staff_users:{$orgId}", CacheHelper::TTL, fn () => User::query()->assignableInOrganization($orgId)->orderBy('name')->get(['id', 'name', 'email']));
        $orgPriorities = cache()->remember(CacheHelper::prioritiesKey($orgId, true), CacheHelper::TTL, fn () => TicketPriority::where('organization_id', $orgId)->where('is_active', true)->orderBy('level')->get(['id', 'name', 'level']));
        $ticketGroups = cache()->remember(CacheHelper::ticketGroupsKey($orgId, true), CacheHelper::TTL, fn () => TicketGroup::where('organization_id', $orgId)->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'color']));
        $organizationFunctions = cache()->remember(CacheHelper::orgFunctionsKey($orgId), CacheHelper::TTL, fn () => OrganizationFunction::query()->where('organization_id', $orgId)->orderBy('sort_order')->orderBy('name')->get(['id', 'name']));
        $formResponse = cache()->remember("form_response:ticket:{$ticket->id}", 60, fn () => FormResponse::where('ticket_id', $ticket->id)->first());

        return compact('orgUsers', 'staffUsers', 'orgPriorities', 'ticketGroups', 'organizationFunctions', 'formResponse');
    }

    /**
     * @return array<string, mixed>
     */
    private function sidebarAccessFlags(Ticket $ticket, mixed $user, int $userId, int $orgId): array
    {
        $hasEditPerm = $user instanceof User && $user->hasPermission(Permission::TicketsEdit);
        $hasDeletePerm = $user instanceof User && $user->hasPermission(Permission::TicketsDelete);
        $hasArchivePerm = $user instanceof User && $user->hasPermission(Permission::TicketsArchive);
        $isCreator = $ticket->created_by !== null && (int) $ticket->created_by === $userId;
        $isAssignee = $ticket->assignees->contains('id', $userId);
        $canEditChecklist = $this->canSeeInternalNotes || $isAssignee || $isCreator;
        $userFunctionIds = $userId && $orgId ? cache()->remember("org_member_functions:{$orgId}:{$userId}", CacheHelper::TTL, fn () => DB::table('organization_memberships')->where('organization_id', $orgId)->where('user_id', $userId)->whereNotNull('organization_function_id')->pluck('organization_function_id')->map(fn ($v) => (int) $v)->all()) : [];
        $isLocked = $ticket->isLocked();
        $canBypassLock = $user instanceof User && $ticket->canBypassLock($user);
        $canApproveTicket = $user instanceof User && $ticket->isPendingApproval() && ApprovalService::canApprove($ticket, $user);

        return [
            'canEditChecklist' => $canEditChecklist,
            'canEditTicket' => $hasEditPerm || $isCreator,
            'canDeleteTicket' => $hasDeletePerm || ($isCreator && $ticket->status !== TicketStatus::Closed),
            'canAssignTicket' => $this->canAssignTicket($ticket),
            'canEditDueDate' => $hasEditPerm || $isCreator || $isAssignee,
            'canArchive' => $hasArchivePerm || $isCreator || $isAssignee,
            'isStaffOrTicketOwner' => $hasEditPerm || $isCreator || $isAssignee,
            'authUserId' => $userId,
            'userFunctionIds' => $userFunctionIds,
            'isLocked' => $isLocked,
            'canBypassLock' => $canBypassLock,
            'canApproveTicket' => $canApproveTicket,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function sidebarDiscussionContext(Ticket $ticket, int $orgId): array
    {
        $discussionUsers = collect([$ticket->creator])
            ->merge($ticket->assignees)
            ->merge($ticket->participants)
            ->filter()
            ->unique('id');
        $discussionUserIds = $discussionUsers->pluck('id')->map(fn ($id) => (int) $id)->sort()->values()->all();
        $orgMemberRoles = ! empty($discussionUserIds)
            ? cache()->remember(
                "org_member_roles:{$orgId}:".implode(',', $discussionUserIds),
                60,
                fn () => DB::table('organization_memberships')
                    ->where('organization_id', $orgId)
                    ->whereIn('user_id', $discussionUserIds)
                    ->pluck('role', 'user_id')
                    ->all()
            )
            : [];

        return compact('discussionUsers', 'orgMemberRoles');
    }

    /**
     * @return array<string, mixed>
     */
    private function sidebarAttachmentsPayload(Ticket $ticket): array
    {
        $attachmentRows = TicketMessage::query()
            ->where('ticket_id', $ticket->id)
            ->whereNotNull('attachments')
            ->get(['attachments']);
        $allAttachments = $attachmentRows
            ->pluck('attachments')
            ->flatMap(fn ($a) => is_array($a) ? $a : [])
            ->filter()
            ->values();
        $lastActivityRaw = TicketMessage::query()
            ->where('ticket_id', $ticket->id)
            ->max('created_at');
        $lastActivity = $lastActivityRaw ? \Illuminate\Support\Carbon::parse($lastActivityRaw) : $ticket->updated_at;
        $ticketAttachments = [];
        if (is_array($ticket->attachments)) {
            foreach ($ticket->attachments['files'] ?? [] as $f) {
                $ticketAttachments[] = is_array($f) ? $f : [];
            }
            foreach ($ticket->attachments['links'] ?? [] as $l) {
                $ticketAttachments[] = ['name' => $l['name'] ?? __('Lien'), 'url' => $l['url'] ?? '#', 'path' => null];
            }
        }
        $customFields = is_array($ticket->custom_fields) ? $ticket->custom_fields : [];

        return compact('allAttachments', 'lastActivity', 'ticketAttachments', 'customFields');
    }

    private function sidebarGroupMembersForTicket(Ticket $ticket): Collection
    {
        $groupMembers = collect();
        if (! $ticket->ticket_group_id) {
            return $groupMembers;
        }
        try {
            $group = $ticket->group;
            if ($group) {
                $groupMembers = $group->members()->get(['users.id', 'users.name', 'users.email']);
            }
        } catch (\Throwable) {
            // Table may not exist yet
        }

        return $groupMembers;
    }

    private function sidebarFunctionMembersForTicket(Ticket $ticket): Collection
    {
        if (! $ticket->assigned_to_function_id) {
            return collect();
        }

        return User::query()
            ->where('status', '!=', 'guest')
            ->whereHas('organizations', function ($q) use ($ticket) {
                $q->where('organization_memberships.organization_id', $ticket->organization_id)
                    ->where('organization_memberships.organization_function_id', $ticket->assigned_to_function_id);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    /**
     * @return array<string, mixed>
     */
    private function discussionSidebarViewData(Ticket $ticket): array
    {
        $user = Auth::user();
        $userId = (int) ($user?->id ?: 0);
        $orgId = (int) $ticket->organization_id;

        return [
            'ticket' => $ticket,
            'creator' => $ticket->creator,
            'groupMembers' => $this->sidebarGroupMembersForTicket($ticket),
            'functionMembers' => $this->sidebarFunctionMembersForTicket($ticket),
            'canSeeInternalNotes' => $this->canSeeInternalNotes,
            'showAddChecklistItem' => $this->showAddChecklistItem,
            ...$this->sidebarCachedOrganizationData($orgId, $ticket),
            ...$this->sidebarAccessFlags($ticket, $user, $userId, $orgId),
            ...$this->sidebarDiscussionContext($ticket, $orgId),
            ...$this->sidebarAttachmentsPayload($ticket),
        ];
    }

    public function render()
    {
        $ticket = Ticket::query()
            ->select(['id', 'public_id', 'organization_id', 'created_by', 'ticket_category_id', 'ticket_priority_id', 'ticket_group_id', 'assigned_to', 'assigned_to_function_id', 'status', 'subject', 'description', 'custom_fields', 'attachments', 'start_date', 'due_date', 'closed_by', 'closed_at', 'updated_at', 'requires_approval', 'approval_status'])
            ->with([
                'creator:id,name,email,mention_tag',
                'assignees:id,name,email,mention_tag',
                'participants:id,name,email,mention_tag',
                'category:id,name',
                'priority:id,name,level',
                'organization:id,name',
                'assignedToFunction:id,name',
                'group:id,name,color',
                'checklistItems.assignee:id,name,email',
                'checklistItems.assignees:id,name,email',
                'checklistItems.doneByUser:id,name,email',
                'checklistItems.assignedToFunction:id,name',
                'approvals.approver:id,name',
                'approvals.requester:id,name',
            ])
            ->whereKey($this->ticketId)
            ->where('organization_id', session('current_organization_id'))
            ->firstOrFail();

        return view('livewire.tickets.partials.discussion-sidebar-content', $this->discussionSidebarViewData($ticket));
    }
}
