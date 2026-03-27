<?php

namespace App\Livewire\Tickets;

use App\Enums\Permission;
use App\Enums\TicketMessageType;
use App\Enums\TicketStatus;
use App\Events\TicketAssigneeChanged;
use App\Events\TicketMessageSent;
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
use App\Notifications\TicketReopenedNotification;
use App\Notifications\TicketAssigneeNotification;
use App\Notifications\TicketMentionNotification;
use App\Notifications\TicketNewMessageNotification;
use App\Services\ApprovalService;
use App\Services\AutomationService;
use App\Services\OrganizationAuditService;
use App\Services\SlaService;
use App\Services\WebhookService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Discussion')]
class Discussion extends Component
{
    use WithFileUploads;

    public int $ticketId;

    public string $ticketPublicId = '';

    public function getListeners(): array
    {
        return [
            "echo-private:ticket.{$this->ticketPublicId},.message.sent" => 'onNewMessage',
            "echo-private:ticket.staff.{$this->ticketPublicId},.message.sent" => 'onNewMessage',
            "echo-private:ticket.{$this->ticketPublicId},.assignee.changed" => '$refresh',
        ];
    }

    /**
     * Called in real-time when a new message arrives (from platform or email).
     * Refreshes the conversation without full page reload.
     */
    public function onNewMessage(array $payload = []): void
    {
        // Skip if the message was sent by the current user (already visible)
        $currentUserId = Auth::id();
        if (isset($payload['user_id']) && (int) $payload['user_id'] === (int) $currentUserId) {
            return;
        }

        // Dispatch a browser event so the frontend can scroll to the new message
        $this->dispatch('new-message-received');
    }

    /** 1 = shell instantané, 2 = conversation/messages (progressif). */
    public int $loadStage = 1;

    /** Chargement progressif des messages après premier rendu. */
    public function loadMessages(): void
    {
        if ($this->loadStage >= 2) {
            return;
        }
        $this->loadStage = 2;
    }

    /** When true, component is embedded (e.g. in Discussions page) and uses minimal layout. */
    public bool $embedded = false;

    /** Staff roles can see/write internal notes. */
    public bool $canSeeInternalNotes = false;

    public bool $canWriteInternalNotes = false;

    public string $body = '';

    public bool $asInternalNote = false;

    public array $attachments = [];

    /** @var \Illuminate\Http\UploadedFile[] */
    public $attachmentFiles = [];

    public bool $showAddChecklistItem = false;

    public string $newChecklistTitle = '';

    public ?int $newChecklistAssignedTo = null;

    public ?int $newChecklistAssignedToFunction = null;

    public ?string $newChecklistDueDate = null;

    /** Édition du ticket (titre, description, pièces jointes) */
    public string $editSubject = '';

    public string $editDescription = '';

    /** @var \Illuminate\Http\UploadedFile[]|array */
    public $editAttachmentFiles = [];

    public string $editLinkUrl = '';

    /** Approval workflow */
    public string $approvalComment = '';

    public bool $showApprovalModal = false;

    public string $approvalAction = '';

    public function mount(Ticket $ticket): void
    {
        $this->ticketId = (int) $ticket->id;
        $this->ticketPublicId = $ticket->public_id;
        $this->authorizeTicket();

        // Fast entry: load stage 2 directly to avoid a second request on page open.
        $this->loadStage = 2;
    }

    private function getTicket(): Ticket
    {
        return Ticket::query()
            ->with(['participants:id,name,email', 'creator:id,name,email', 'assignees:id,name,email'])
            ->whereKey($this->ticketId)
            ->where('organization_id', session('current_organization_id'))
            ->firstOrFail();
    }

    private function authorizeTicket(): void
    {
        Gate::authorize('view', $this->getTicket());
    }

    private function computeNotePermissions(Ticket $ticket): void
    {
        $this->canSeeInternalNotes = Gate::allows('viewInternalNotes', Ticket::class);
        $this->canWriteInternalNotes = Gate::allows('writeInternalNotes', Ticket::class);

        if (! $this->canSeeInternalNotes) {
            $this->asInternalNote = false;
        }
    }

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
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'type' => TicketMessageType::System,
            'body' => __(':user a été ajouté à la discussion.', ['user' => $orgMember->name]),
            'meta' => ['action' => 'participant_added', 'user_id' => $userId],
        ]);
        $orgMember->notify(new TicketAssigneeNotification($ticket, $user, 'participant_added'));
        event(new TicketAssigneeChanged($ticket->id, $ticket->subject, $userId, 'participant_added', $user->name, $ticket->public_id));
        event(new UserNotificationReceived($userId, 'ticket_assignee'));
    }

    /** Vérifie si l'utilisateur courant peut assigner. */
    private function canAssignTicket(): bool
    {
        /** @var User|null $user */
        return Gate::allows('assign', $this->getTicket());
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

    /** Rôle de l'utilisateur dans l'organisation courante (session). Fiable en requêtes Livewire. */
    private function getCurrentUserRole(): string
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            return 'member';
        }
        $orgId = (int) session('current_organization_id');
        if (! $orgId) {
            return 'member';
        }
        $membership = $user->organizations()->where('organization_id', $orgId)->first();

        return $membership?->pivot?->role ?? 'member';
    }

    /** M'assigner : un clic pour devenir assigné (staff uniquement). */
    public function assignToMe(): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);
        $ticket = $this->getTicket();
        Gate::authorize('assign', $ticket);
        $this->setAssignee((int) $user->id);
    }

    /** Assigner le ticket à une personne comme responsable. Staff uniquement. userId=0 pour désassigner. */
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
        $orgMember = User::whereKey($userId)->whereHas('organizations', function ($q) use ($ticket) {
            $q->where('organization_memberships.organization_id', $ticket->organization_id)
                ->whereIn('organization_memberships.role', ['owner', 'admin', 'agent']);
        })->firstOrFail();
        $previousAssignee = $ticket->assignees()->first();
        // Demote previous responsible to collaborator
        $ticket->assignees()->newPivotQuery()->where('role', 'responsible')->update(['role' => 'collaborator']);
        // Sync the new responsible (attach if not present, update role if present)
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
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'type' => TicketMessageType::System,
            'body' => $previousAssignee && (int) $previousAssignee->id !== $userId
                ? __(':user a été assigné au ticket (par :actor).', ['user' => $orgMember->name, 'actor' => $user->name])
                : __(':user a été assigné au ticket.', ['user' => $orgMember->name]),
            'meta' => ['action' => 'assignee_added', 'user_id' => $userId, 'assigned_by' => $user->id],
        ]);
        $orgMember->notify(new TicketAssigneeNotification($ticket, $user, 'assigned'));
        event(new TicketAssigneeChanged($ticket->id, $ticket->subject, $userId, 'assigned', $user->name, $ticket->public_id));
        event(new UserNotificationReceived($userId, 'ticket_assignee'));
        CacheHelper::invalidateDashboard((int) $ticket->organization_id);
        CacheHelper::invalidateReports((int) $ticket->organization_id);
        CacheHelper::invalidateTicketCounts((int) $ticket->organization_id);
    }

    public function addAssignee(int $userId): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }
        if (! $this->canAssignTicket()) {
            abort(403);
        }
        $ticket = $this->getTicket();
        $this->guardAgainstLock($ticket);
        $orgMember = User::whereKey($userId)->whereHas('organizations', function ($q) use ($ticket) {
            $q->where('organization_memberships.organization_id', $ticket->organization_id)
                ->whereIn('organization_memberships.role', ['owner', 'admin', 'agent']);
        })->firstOrFail();
        if ($ticket->assignees()->where('users.id', $userId)->exists()) {
            return;
        }
        // If no assignees yet, first one becomes responsible; otherwise collaborator
        $hasAssignees = $ticket->assignees()->exists();
        $role = $hasAssignees ? 'collaborator' : 'responsible';
        $ticket->assignees()->attach($userId, ['assigned_by' => $user->id, 'role' => $role]);
        if (! $ticket->assigned_to) {
            $ticket->update([
                'assigned_to' => $userId,
                'assigned_by' => $user->id,
                'assigned_at' => now(),
            ]);
        }
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'type' => TicketMessageType::System,
            'body' => __(':user a été assigné au ticket.', ['user' => $orgMember->name]),
            'meta' => ['action' => 'assignee_added', 'user_id' => $userId],
        ]);
        $orgMember->notify(new TicketAssigneeNotification($ticket, $user, 'assigned'));
        event(new TicketAssigneeChanged($ticket->id, $ticket->subject, $userId, 'assigned', $user->name, $ticket->public_id));
        event(new UserNotificationReceived($userId, 'ticket_assignee'));
        CacheHelper::invalidateDashboard((int) $ticket->organization_id);
        CacheHelper::invalidateReports((int) $ticket->organization_id);
        CacheHelper::invalidateTicketCounts((int) $ticket->organization_id);

        $ticket->load('assignees');
        WebhookService::dispatch((int) $ticket->organization_id, 'ticket.assigned', [
            'ticket_id' => $ticket->public_id,
            'assignees' => $ticket->assignees->map(fn ($a) => [
                'id' => $a->id,
                'name' => $a->name,
                'role' => $a->pivot->role ?? 'collaborator',
            ])->toArray(),
            'changed_by' => $user->id,
        ]);
    }

    public function removeAssignee(int $userId): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }
        if (! $this->canAssignTicket()) {
            abort(403);
        }
        $ticket = $this->getTicket();
        $this->guardAgainstLock($ticket);
        $removedUser = User::find($userId);
        // Check if the removed user was the responsible
        $wasResponsible = $ticket->assignees()->where('users.id', $userId)->wherePivot('role', 'responsible')->exists();
        $ticket->assignees()->detach($userId);
        // If removed user was responsible, promote first remaining collaborator
        if ($wasResponsible) {
            $firstCollaborator = $ticket->assignees()->first();
            if ($firstCollaborator) {
                $ticket->assignees()->updateExistingPivot($firstCollaborator->id, ['role' => 'responsible']);
            }
        }
        $newResponsible = $ticket->assignees()->wherePivot('role', 'responsible')->first();
        $ticket->update([
            'assigned_to' => $newResponsible?->id,
            'assigned_by' => $newResponsible ? $user->id : null,
            'assigned_at' => $newResponsible ? now() : null,
        ]);
        if ($removedUser) {
            TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => null,
                'type' => TicketMessageType::System,
                'body' => __(':user a été retiré des assignés.', ['user' => $removedUser->name]),
                'meta' => ['action' => 'assignee_removed', 'user_id' => $userId],
            ]);
            $removedUser->notify(new TicketAssigneeNotification($ticket, $user, 'unassigned'));
            event(new TicketAssigneeChanged($ticket->id, $ticket->subject, $userId, 'unassigned', $user->name, $ticket->public_id));
            event(new UserNotificationReceived($userId, 'ticket_assignee'));
        }
        CacheHelper::invalidateDashboard((int) $ticket->organization_id);
        CacheHelper::invalidateReports((int) $ticket->organization_id);
        CacheHelper::invalidateTicketCounts((int) $ticket->organization_id);

        $ticket->load('assignees');
        WebhookService::dispatch((int) $ticket->organization_id, 'ticket.assigned', [
            'ticket_id' => $ticket->public_id,
            'assignees' => $ticket->assignees->map(fn ($a) => [
                'id' => $a->id,
                'name' => $a->name,
                'role' => $a->pivot->role ?? 'collaborator',
            ])->toArray(),
            'changed_by' => $user->id,
        ]);
    }

    /** Promouvoir un collaborateur en responsable. */
    public function promoteToResponsible(int $userId): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }
        if (! $this->canAssignTicket()) {
            abort(403);
        }
        $ticket = $this->getTicket();
        $this->guardAgainstLock($ticket);
        if (! $ticket->assignees()->where('users.id', $userId)->exists()) {
            return;
        }
        // Demote current responsible to collaborator
        $ticket->assignees()->newPivotQuery()->where('role', 'responsible')->update(['role' => 'collaborator']);
        // Promote chosen user
        $ticket->assignees()->updateExistingPivot($userId, ['role' => 'responsible']);
        $ticket->update([
            'assigned_to' => $userId,
            'assigned_by' => $user->id,
            'assigned_at' => now(),
        ]);
        CacheHelper::invalidateDashboard((int) $ticket->organization_id);
        CacheHelper::invalidateReports((int) $ticket->organization_id);
        CacheHelper::invalidateTicketCounts((int) $ticket->organization_id);
    }

    /** Guard: abort 403 if ticket is locked and current user cannot bypass lock. */
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

    public function setAsInternalNote(bool $value): void
    {
        $this->asInternalNote = $value;
    }

    public function removeParticipant(int $userId): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }
        $ticket = $this->getTicket();
        if (! $ticket->hasDiscussionAccess((int) $user->id)) {
            abort(403);
        }
        $removedUser = User::find($userId);
        $ticket->participants()->detach($userId);
        if ($removedUser) {
            TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => null,
                'type' => TicketMessageType::System,
                'body' => __(':user a été retiré de la discussion.', ['user' => $removedUser->name]),
                'meta' => ['action' => 'participant_removed', 'user_id' => $userId],
            ]);
            $removedUser->notify(new TicketAssigneeNotification($ticket, $user, 'participant_removed'));
            event(new TicketAssigneeChanged($ticket->id, $ticket->subject, $userId, 'participant_removed', $user->name, $ticket->public_id));
            event(new UserNotificationReceived($userId, 'ticket_assignee'));
        }
    }

    public function sendMessage(): void
    {
        $this->handleSendMessage();
    }

    private function handleSendMessage(): void
    {
        $this->performSendMessage();
    }

    private function performSendMessage(): void
    {
        $this->processSendMessage();
    }

    private function processSendMessage(): void
    {
        $this->validateSendMessageInput();
        if (! $this->canSendMessagePayload()) {
            return;
        }
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }
        $ticket = $this->getTicket();
        if (! $ticket->hasDiscussionAccess((int) $user->id)) {
            abort(403);
        }
        $this->guardAgainstLock($ticket);
        $type = $this->asInternalNote ? TicketMessageType::InternalNote : TicketMessageType::Message;
        $this->computeNotePermissions($ticket);
        if ($type === TicketMessageType::InternalNote && ! $this->canWriteInternalNotes) {
            abort(403);
        }
        $body = trim($this->body);
        $mentions = $this->extractMentions($body, $ticket->organization_id);
        $savedAttachments = $this->storeDiscussionAttachments($ticket);
        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'type' => $type,
            'body' => $body,
            'attachments' => array_merge($this->attachments, $savedAttachments) ?: null,
            'meta' => array_filter(['mentions' => $mentions]),
        ]);
        event(new TicketMessageSent($message));

        // SLA: record first response if this is a message (not internal note) from someone other than the creator
        if ($type === TicketMessageType::Message && (int) $user->id !== (int) $ticket->created_by) {
            SlaService::recordFirstResponse($ticket);
        }

        $this->notifyMessageRecipients($ticket, $message, $user, $type);

        // Send mention-specific notifications
        if (! empty($mentions)) {
            $mentionedUsers = User::whereIn('id', $mentions)
                ->where('id', '!=', $user->id)
                ->get();
            /** @var User $mentionedUser */
            foreach ($mentionedUsers as $mentionedUser) {
                $mentionedUser->notify(new TicketMentionNotification($message, $user));
                event(new UserNotificationReceived((int) $mentionedUser->id, 'ticket_mention'));
            }
        }

        $orgId = (int) $ticket->organization_id;
        CacheHelper::invalidateDashboard($orgId);
        CacheHelper::invalidateReports($orgId);
        CacheHelper::invalidateTicketCounts($orgId);

        if ($type === TicketMessageType::Message) {
            WebhookService::dispatch($orgId, 'ticket.comment_created', [
                'ticket_id' => $ticket->public_id,
                'comment' => (new \App\Http\Resources\Api\V1\TicketCommentResource($message->load('user')))->resolve(),
            ]);
        }

        $this->body = '';
        $this->asInternalNote = false;
        $this->attachments = [];
        $this->attachmentFiles = [];
    }

    private function validateSendMessageInput(): void
    {
        $this->validate([
            'body' => ['nullable', 'string', 'max:10000'],
            'attachmentFiles.*' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,csv,txt,zip'],
        ]);
    }

    private function canSendMessagePayload(): bool
    {
        $hasBody = trim($this->body ?? '') !== '';
        $hasAttachments = is_array($this->attachmentFiles) && count($this->attachmentFiles) > 0;
        if ($hasBody || $hasAttachments) {
            return true;
        }

        $this->addError('body', __('Ajoutez un message ou joignez au moins un fichier.'));

        return false;
    }

    private function storeDiscussionAttachments(Ticket $ticket): array
    {
        $savedAttachments = [];
        foreach ($this->attachmentFiles as $file) {
            $path = $file->store('ticket-messages/'.$ticket->id, 'local');
            $savedAttachments[] = [
                'path' => $path,
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'url' => route('tickets.discussion.file', ['ticket' => $ticket->public_id, 'filename' => basename($path)]),
            ];
        }

        return $savedAttachments;
    }

    private function notifyMessageRecipients(Ticket $ticket, TicketMessage $message, User $user, TicketMessageType $type): void
    {
        $notifyUserIds = collect([$ticket->created_by])
            ->merge($ticket->assignees->pluck('id'))
            ->merge($ticket->participants->pluck('id'))
            ->filter()
            ->unique()
            ->diff([$user->id])
            ->values();

        $recipientsQuery = User::whereIn('id', $notifyUserIds);
        if ($type === TicketMessageType::InternalNote) {
            $recipientsQuery->whereHas('organizations', function ($q) use ($ticket) {
                $q->where('organization_memberships.organization_id', $ticket->organization_id)
                    ->whereIn('organization_memberships.role', ['owner', 'admin', 'agent']);
            });
        }

        /** @var \Illuminate\Database\Eloquent\Collection<int, User> $recipients */
        $recipients = $recipientsQuery->get();
        foreach ($recipients as $recipient) {
            if ($ticket->hasDiscussionAccess((int) $recipient->id)) {
                try {
                    $recipient->notify(new TicketNewMessageNotification($message));
                } catch (\Throwable $e) {
                    Log::error('Échec envoi notification email', [
                        'ticket_id' => $ticket->public_id,
                        'recipient_id' => $recipient->id,
                        'recipient_email' => $recipient->email,
                        'error' => $e->getMessage(),
                    ]);
                }
                event(new UserNotificationReceived((int) $recipient->id, 'ticket_new_message'));
            }
        }
    }

    public function archiveTicket(): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }
        /** @var User $user */
        $ticket = $this->getTicket();
        if (! $ticket->hasDiscussionAccess((int) $user->id)) {
            abort(403);
        }

        $this->guardAgainstLock($ticket);

        // Only users with archive permission or creator/assignee can archive.
        $canArchive = $user->hasPermission(Permission::TicketsArchive);
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
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }
        /** @var User $user */
        $ticket = $this->getTicket();
        if (! $ticket->hasDiscussionAccess((int) $user->id)) {
            abort(403);
        }

        $canArchive = $user->hasPermission(Permission::TicketsArchive);
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

    /** Suppression (soft delete). Autorisée au staff ou au créateur si le ticket n'est pas fermé. */
    public function deleteTicket(): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }
        $ticket = $this->getTicket();
        if (! $ticket->hasDiscussionAccess((int) $user->id)) {
            abort(403);
        }
        $hasDeletePerm = $user instanceof User && $user->hasPermission(Permission::TicketsDelete);
        $isCreator = (int) $ticket->created_by === (int) $user->id;
        $canDelete = $hasDeletePerm || ($isCreator && $ticket->status !== TicketStatus::Closed);
        if (! $canDelete) {
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

    /** Ouvre le modal d'édition en initialisant les champs avec les valeurs du ticket. */
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

    /** Vérifie si l'utilisateur peut modifier les infos de base du ticket (titre, description, pièces jointes). Créateur ou staff. */
    private function canEditTicketBase(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            return false;
        }
        $ticket = $this->getTicket();
        $hasEditPerm = $user instanceof User && $user->hasPermission(Permission::TicketsEdit);
        $isCreator = $ticket->created_by !== null && (int) $ticket->created_by === (int) $user->id;

        return $hasEditPerm || $isCreator;
    }

    /** Modifier le titre du ticket. Autorisé au créateur ou au staff. */
    public function updateSubject(): void
    {
        if (! $this->canEditTicketBase()) {
            abort(403);
        }
        $ticket = $this->getTicket();
        $this->guardAgainstLock($ticket);
        $subject = trim($this->editSubject ?? '');
        if ($subject === '') {
            return;
        }
        if ($ticket->subject === $subject) {
            return;
        }
        $ticket->update(['subject' => $subject]);
        session()->flash('tickets_status', __('Titre mis à jour.'));
    }

    /** Modifier la description du ticket. Autorisé au créateur ou au staff. */
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

    /** Ajouter une pièce jointe (fichier) au ticket. Autorisé au créateur ou au staff. */
    public function addTicketAttachmentFile(): void
    {
        if (! $this->canEditTicketBase()) {
            abort(403);
        }
        $ticket = $this->getTicket();
        $this->guardAgainstLock($ticket);
        $this->validate([
            'editAttachmentFiles.*' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,csv,txt,zip'],
        ]);
        $files = $this->editAttachmentFiles ?? [];
        if (! is_array($files) || count($files) === 0) {
            return;
        }
        $orgId = (int) $ticket->organization_id;
        $attachments = is_array($ticket->attachments) ? $ticket->attachments : ['files' => [], 'links' => []];
        $filesList = $attachments['files'] ?? [];
        /** @var \Illuminate\Http\UploadedFile $file */
        foreach ($files as $file) {
            $original = (string) ($file->getClientOriginalName() ?: 'file');
            $ext = (string) ($file->getClientOriginalExtension() ?: '');
            $safeBase = Str::slug(pathinfo($original, PATHINFO_FILENAME)) ?: 'file';
            $filename = $safeBase.'-'.Str::lower(Str::random(10)).($ext ? '.'.$ext : '');
            $path = $file->storeAs("ticket-attachments/org-{$orgId}/ticket-{$ticket->id}", $filename, 'local');
            $filesList[] = [
                'disk' => 'local',
                'path' => $path,
                'name' => $original,
                'size' => method_exists($file, 'getSize') ? (int) $file->getSize() : null,
                'mime' => method_exists($file, 'getMimeType') ? (string) $file->getMimeType() : null,
                'url' => route('tickets.attachment', ['ticket' => $ticket->public_id, 'filename' => $filename]),
            ];
        }
        $attachments['files'] = $filesList;
        $ticket->update(['attachments' => $attachments]);
        $this->editAttachmentFiles = [];
        session()->flash('tickets_status', __('Pièce jointe ajoutée.'));
    }

    /** Ajouter un lien comme pièce jointe au ticket. Autorisé au créateur ou au staff. */
    public function addTicketAttachmentLink(): void
    {
        if (! $this->canEditTicketBase()) {
            abort(403);
        }
        $ticket = $this->getTicket();
        $this->guardAgainstLock($ticket);
        $this->validate([
            'editLinkUrl' => ['required', 'string', 'url', 'max:2000'],
        ], [], ['editLinkUrl' => __('URL')]);
        $url = trim($this->editLinkUrl);
        $attachments = is_array($ticket->attachments) ? $ticket->attachments : ['files' => [], 'links' => []];
        $linksList = $attachments['links'] ?? [];
        $linksList[] = ['url' => $url];
        $attachments['links'] = $linksList;
        $ticket->update(['attachments' => $attachments]);
        $this->editLinkUrl = '';
        session()->flash('tickets_status', __('Lien ajouté.'));
    }

    /** Supprimer une pièce jointe du ticket (type: 'files'|'links', index 0-based). Autorisé au créateur ou au staff. */
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

    /** @return int[] */
    private function extractMentions(string $body, int $organizationId): array
    {
        if (! preg_match_all('/@([\p{L}\p{N}_]+(?:\s+[\p{L}\p{N}_]+)*)/u', $body, $m)) {
            return [];
        }
        $ids = [];
        $baseQuery = User::query()->whereHas('organizations', fn ($q) => $q->where('organization_id', $organizationId));

        foreach ($m[1] as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }
            // Match by mention_tag (exact, sans espaces)
            if (preg_match('/^[\p{L}\p{N}_]+$/u', $part)) {
                $byTag = (clone $baseQuery)
                    ->whereNotNull('mention_tag')
                    ->whereRaw('LOWER(mention_tag) = ?', [mb_strtolower($part)])
                    ->first();
                if ($byTag) {
                    $ids[] = (int) $byTag->id;

                    continue;
                }
            }
            // Fallback: match by name (comportement existant)
            $byName = (clone $baseQuery)->where('name', 'ilike', '%'.$part.'%')->first();
            if ($byName) {
                $ids[] = (int) $byName->id;
            }
        }

        return array_values(array_unique($ids));
    }

    /** Change le statut du ticket. Réservé au staff. Admin/owner can bypass lock to reopen. */
    public function changeStatus(string $status): void
    {
        $this->handleChangeStatus($status);
    }

    private function handleChangeStatus(string $status): void
    {
        $this->processChangeStatus($status);
    }

    private function processChangeStatus(string $status): void
    {
        $user = Auth::user();
        if (! $user || ! $this->canAssignTicket()) {
            abort(403);
        }
        $newStatus = TicketStatus::tryFrom($status);
        if (! $newStatus) {
            return;
        }
        $ticket = $this->getTicket();
        $oldStatus = $ticket->status;
        if ($oldStatus === $newStatus) {
            return;
        }

        // Lock check: only admin/owner can change status on closed ticket (to reopen)
        if ($ticket->isLocked() && ! $ticket->canBypassLock($user)) {
            abort(403, __('tickets.locked'));
        }

        if (! $this->passesResolutionStrictMode($ticket, $newStatus)) {
            return;
        }

        $isClosed = in_array($newStatus, [TicketStatus::Resolved, TicketStatus::Closed], true);
        $ticket->update([
            'status' => $newStatus,
            'closed_by' => $isClosed ? $user->id : null,
            'closed_at' => $isClosed ? now() : null,
        ]);
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'type' => TicketMessageType::System,
            'body' => __('tickets.status_changed', [
                'actor' => $user->name,
                'old' => __('tickets.status.'.$oldStatus->value),
                'new' => __('tickets.status.'.$newStatus->value),
            ]),
            'meta' => ['action' => 'status_changed', 'old' => $oldStatus->value, 'new' => $newStatus->value],
        ]);
        OrganizationAuditService::log('ticket.status_changed', 'Ticket', $ticket->id, [
            'ticket' => $ticket->subject,
            'old_status' => $oldStatus->value,
            'new_status' => $newStatus->value,
        ]);

        // SLA tracking
        if ($newStatus === TicketStatus::Pending) {
            SlaService::pause($ticket);
        } elseif ($oldStatus === TicketStatus::Pending && $newStatus !== TicketStatus::Pending) {
            SlaService::resume($ticket);
        }
        if (in_array($newStatus, [TicketStatus::Resolved, TicketStatus::Closed], true)) {
            SlaService::recordResolution($ticket);
        }
        if (in_array($oldStatus, [TicketStatus::Resolved, TicketStatus::Closed], true)
            && ! in_array($newStatus, [TicketStatus::Resolved, TicketStatus::Closed], true)) {
            SlaService::onReopened($ticket);
            $this->notifyTicketReopened($ticket, (int) $user->id, $user->name);
        }

        CacheHelper::invalidateDashboard((int) $ticket->organization_id);
        CacheHelper::invalidateReports((int) $ticket->organization_id);
        CacheHelper::invalidateTicketCounts((int) $ticket->organization_id);

        AutomationService::evaluate($ticket, 'status_changed');

        $ticket->load(['category', 'priority', 'group', 'creator', 'assignees']);
        WebhookService::dispatch((int) $ticket->organization_id, 'ticket.status_changed', [
            'ticket_id' => $ticket->public_id,
            'old_status' => $oldStatus->value,
            'new_status' => $newStatus->value,
            'changed_by' => $user->id,
        ]);
    }

    private function passesResolutionStrictMode(Ticket $ticket, TicketStatus $newStatus): bool
    {
        $isResolving = in_array($newStatus, [TicketStatus::Resolved, TicketStatus::Closed], true);
        if (! $isResolving) {
            return true;
        }

        $org = Organization::find($ticket->organization_id);
        $orgSettings = is_array($org?->settings) ? $org->settings : [];
        $resolutionMode = $orgSettings['workflow']['ticket_resolution_mode'] ?? 'flexible';
        if ($resolutionMode !== 'strict') {
            return true;
        }

        $totalItems = $ticket->checklistItems()->count();
        $doneItems = $ticket->checklistItems()->where('is_done', true)->count();
        if ($totalItems > 0 && $doneItems < $totalItems) {
            $this->dispatch('toast', type: 'error', message: __('tickets.ticket_resolution_strict_error'));

            return false;
        }

        return true;
    }

    private function notifyTicketReopened(Ticket $ticket, int $actorId, string $actorName): void
    {
        $notifyUserIds = collect([$ticket->created_by])
            ->merge($ticket->assignees->pluck('id'))
            ->merge($ticket->participants->pluck('id'))
            ->filter()
            ->unique()
            ->diff([$actorId])
            ->values();

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

    /** Change la priorité du ticket. Réservé au staff. */
    public function changePriority(int $priorityId): void
    {
        $user = Auth::user();
        if (! $user || ! $this->canAssignTicket()) {
            abort(403);
        }
        $ticket = $this->getTicket();
        $this->guardAgainstLock($ticket);
        $oldPriority = $ticket->priority;
        $newPriority = TicketPriority::where('organization_id', $ticket->organization_id)
            ->where('is_active', true)
            ->whereKey($priorityId)
            ->firstOrFail();

        if ($ticket->ticket_priority_id === $newPriority->id) {
            return;
        }

        $ticket->update(['ticket_priority_id' => $newPriority->id]);

        SlaService::onPriorityChanged($ticket, $oldPriority?->id);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'type' => TicketMessageType::System,
            'body' => __(':actor a changé la priorité : :old → :new', [
                'actor' => $user->name,
                'old' => $oldPriority?->name ?? '—',
                'new' => $newPriority->name,
            ]),
            'meta' => ['action' => 'priority_changed', 'old_id' => $oldPriority?->id, 'new_id' => $newPriority->id],
        ]);
        CacheHelper::invalidateDashboard((int) $ticket->organization_id);
        CacheHelper::invalidateReports((int) $ticket->organization_id);
        CacheHelper::invalidateTicketCounts((int) $ticket->organization_id);

        AutomationService::evaluate($ticket, 'priority_changed');
    }

    /** Change le groupe du ticket. Réservé au staff. */
    public function changeGroup($groupId): void
    {
        $user = Auth::user();
        if (! $user || ! $this->canAssignTicket()) {
            abort(403);
        }
        $ticket = $this->getTicket();
        $this->guardAgainstLock($ticket);
        $id = $groupId === '' || $groupId === null ? null : (int) $groupId;
        if ($id !== null) {
            TicketGroup::where('organization_id', $ticket->organization_id)
                ->where('is_active', true)
                ->whereKey($id)
                ->firstOrFail();
        }
        $ticket->update(['ticket_group_id' => $id]);
        CacheHelper::invalidateDashboard((int) $ticket->organization_id);
    }

    /** Modifie l'échéance du ticket. Staff, créateur, ou assigné. */
    public function updateDueDate(?string $date): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }
        $ticket = $this->getTicket();
        $this->guardAgainstLock($ticket);

        $hasEditPerm = $user instanceof User && $user->hasPermission(Permission::TicketsEdit);
        $isCreator = (int) $ticket->created_by === (int) $user->id;
        $isAssignee = $ticket->assignees()->where('users.id', $user->id)->exists();

        if (! ($hasEditPerm || $isCreator || $isAssignee)) {
            abort(403);
        }

        /** @var \Carbon\Carbon|null $dueDate */
        $dueDate = $ticket->due_date;
        $oldDate = $dueDate?->format('Y-m-d');
        $newDate = ($date && $date !== '') ? $date : null;

        if ($oldDate === $newDate) {
            return;
        }

        $ticket->update(['due_date' => $newDate]);
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'type' => TicketMessageType::System,
            'body' => __(':actor a modifié l\'échéance : :old → :new', [
                'actor' => $user->name,
                'old' => $oldDate ? \Carbon\Carbon::parse($oldDate)->translatedFormat('d M Y') : '—',
                'new' => $newDate ? \Carbon\Carbon::parse($newDate)->translatedFormat('d M Y') : '—',
            ]),
            'meta' => ['action' => 'due_date_changed', 'old' => $oldDate, 'new' => $newDate],
        ]);
    }

    /** Supprime un élément de la checklist. */
    public function deleteChecklistItem(int $id): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }
        $ticket = $this->getTicket();
        if (! $ticket->hasDiscussionAccess((int) $user->id)) {
            abort(403);
        }
        $this->guardAgainstLock($ticket);

        // Check canEditChecklist logic
        $hasEditPerm = $user instanceof User && $user->hasPermission(Permission::TicketsEdit);
        $isAssignee = $ticket->assignees->contains('id', $user->id);
        $isCreator = (int) $ticket->created_by === (int) $user->id;

        if (! ($hasEditPerm || $isAssignee || $isCreator)) {
            abort(403);
        }

        $ticket->checklistItems()->whereKey($id)->delete();
    }

    /** Met à jour le titre d'un élément de la checklist. */
    public function updateChecklistItemTitle(int $id, string $title): void
    {
        $title = trim($title);
        if ($title === '') {
            return;
        }

        $user = Auth::user();
        if (! $user) {
            abort(403);
        }
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

    // ─── Approval workflow ─────────────────────────────────────────────

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
        $this->validate([
            'approvalComment' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

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

    /** Load ticket with relations used by the discussion view (reduces type complexity in render()). */
    private function loadTicketForRender(): Ticket
    {
        $relations = [
            'creator:id,name,email,mention_tag',
            'assignees:id,name,email,mention_tag',
            'participants:id,name,email,mention_tag',
            'category:id,name',
            'priority:id,name,level',
            'organization:id,name',
            'assignedToFunction:id,name',
            'group:id,name,color',
        ];

        // Heavy relations are deferred to stage 2 for faster first paint.
        if ($this->loadStage >= 2) {
            $relations = array_merge($relations, [
                'checklistItems.assignee:id,name,email',
                'checklistItems.assignees:id,name,email',
                'checklistItems.doneByUser:id,name,email',
                'checklistItems.assignedToFunction:id,name',
                'approvals.approver:id,name',
                'approvals.requester:id,name',
            ]);
        }

        return Ticket::query()
            ->select([
                'id',
                'public_id',
                'organization_id',
                'created_by',
                'ticket_category_id',
                'ticket_priority_id',
                'ticket_group_id',
                'assigned_to',
                'assigned_to_function_id',
                'status',
                'subject',
                'description',
                'custom_fields',
                'attachments',
                'start_date',
                'due_date',
                'closed_by',
                'closed_at',
                'updated_at',
            ])
            ->with($relations)
            ->whereKey($this->ticketId)
            ->where('organization_id', session('current_organization_id'))
            ->firstOrFail();
    }

    /**
     * Build the mentionable users list for the discussion composer.
     *
     * @return array{orgUsers: \Illuminate\Database\Eloquent\Collection, mentionableUsers: array<int, array{id: int, name: string, tag: string}>}
     */
    private function buildUsersData(Ticket $ticket): array
    {
        // Fast first paint: avoid loading all organization members before the page is visible.
        if ($this->loadStage < 2) {
            $orgUsers = collect([$ticket->creator])
                ->merge($ticket->assignees)
                ->merge($ticket->participants)
                ->filter()
                ->unique('id')
                ->values();

            $mentionableUsers = $orgUsers
                ->map(fn ($u) => ['id' => $u->id, 'name' => $u->name, 'tag' => $u->mention_tag])
                ->values()
                ->all();

            return compact('orgUsers', 'mentionableUsers');
        }

        $orgUsers = cache()->remember(
            CacheHelper::membersKey((int) $ticket->organization_id),
            CacheHelper::TTL,
            fn () => User::query()
                ->whereHas('organizations', fn ($q) => $q->where('organization_id', $ticket->organization_id))
                ->orderBy('name')
                ->limit(300)
                ->get(['id', 'name', 'email', 'mention_tag'])
        );

        // Build mentionableUsers from already-loaded relations (no extra query)
        $discussionParticipantIds = collect([$ticket->created_by])
            ->merge($ticket->assignees->pluck('id'))
            ->merge($ticket->participants->pluck('id'))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $mentionableUsers = collect($discussionParticipantIds)
            ->map(fn ($id) => $orgUsers->firstWhere('id', $id))
            ->filter()
            ->map(fn ($u) => ['id' => $u->id, 'name' => $u->name, 'tag' => $u->mention_tag])
            ->values()
            ->all();

        return compact('orgUsers', 'mentionableUsers');
    }

    /**
     * Compute permission flags for the current user on a ticket.
     *
     * @return array{canEditChecklist: bool, canEditTicket: bool, canDeleteTicket: bool, canEditDueDate: bool, canArchive: bool, isStaffOrTicketOwner: bool, authUserId: int}
     */
    private function buildPermissionFlags(Ticket $ticket): array
    {
        /** @var User|null $user */
        $user = Auth::user();
        $userId = (int) ($user?->id ?: 0);

        $canEditChecklist = $this->canSeeInternalNotes;
        if (! $canEditChecklist && $ticket->assignees->contains('id', $userId)) {
            $canEditChecklist = true;
        }
        if (! $canEditChecklist && $ticket->created_by && (int) $ticket->created_by === $userId) {
            $canEditChecklist = true;
        }

        $hasEditPerm = $user && $user->hasPermission(Permission::TicketsEdit);
        $hasDeletePerm = $user && $user->hasPermission(Permission::TicketsDelete);
        $hasArchivePerm = $user && $user->hasPermission(Permission::TicketsArchive);
        $isCreator = $ticket->created_by !== null && (int) $ticket->created_by === $userId;
        $isAssignee = $ticket->assignees->contains('id', $userId);

        // isStaffOrTicketOwner = can toggle any checklist item (staff, ticket creator, or ticket assignee)
        $isStaffOrTicketOwner = $hasEditPerm || $isCreator || $isAssignee;

        return [
            'canEditChecklist' => $canEditChecklist,
            'canEditTicket' => $hasEditPerm || $isCreator,
            'canDeleteTicket' => $hasDeletePerm || ($isCreator && $ticket->status !== TicketStatus::Closed),
            'canEditDueDate' => $hasEditPerm || $isCreator || $isAssignee,
            'canArchive' => $hasArchivePerm || $isCreator || $isAssignee,
            'isStaffOrTicketOwner' => $isStaffOrTicketOwner,
            'authUserId' => $userId,
        ];
    }

    /**
     * Fetch organization-scoped reference data (priorities, member roles, functions).
     *
     * @return array{orgPriorities: \Illuminate\Database\Eloquent\Collection, formResponse: FormResponse|null, orgMemberRoles: array<int, string>, organizationFunctions: \Illuminate\Support\Collection}
     */
    private function buildOrgReferenceData(Ticket $ticket): array
    {
        $orgId = (int) $ticket->organization_id;

        $orgPriorities = cache()->remember(
            CacheHelper::prioritiesKey($orgId, true),
            CacheHelper::TTL,
            fn () => TicketPriority::where('organization_id', $orgId)
                ->where('is_active', true)
                ->orderBy('level')
                ->get(['id', 'name', 'level'])
        );

        // Fast first paint: keep sidebar essentials only.
        if ($this->loadStage < 2) {
            return [
                'orgPriorities' => $orgPriorities,
                'formResponse' => null,
                'orgMemberRoles' => [],
                'organizationFunctions' => collect(),
                'ticketGroups' => collect(),
            ];
        }

        $formResponse = cache()->remember(
            "form_response:ticket:{$ticket->id}",
            60,
            fn () => FormResponse::where('ticket_id', $ticket->id)->first()
        );

        $organizationFunctions = $orgId
            ? cache()->remember(
                CacheHelper::orgFunctionsKey($orgId),
                CacheHelper::TTL,
                fn () => OrganizationFunction::query()
                    ->where('organization_id', $orgId)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get(['id', 'name'])
            )
            : collect();

        $ticketGroups = $orgId
            ? cache()->remember(
                CacheHelper::ticketGroupsKey($orgId, true),
                CacheHelper::TTL,
                fn () => TicketGroup::where('organization_id', $orgId)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get(['id', 'name', 'color'])
            )
            : collect();

        return [
            'orgPriorities' => $orgPriorities,
            'formResponse' => $formResponse,
            'orgMemberRoles' => [],
            'organizationFunctions' => $organizationFunctions,
            'ticketGroups' => $ticketGroups,
        ];
    }

    /** Build view data for the discussion view. */
    private function getDiscussionViewData(Ticket $ticket): array
    {
        $usersData = $this->cachedUsersData ?? $this->buildUsersData($ticket);
        $permissions = $this->buildPermissionFlags($ticket);
        $orgRef = $this->cachedOrgRef ?? $this->buildOrgReferenceData($ticket);

        // Use cached orgMemberRoles to get user's function IDs (no extra query)
        $orgId = (int) session('current_organization_id');
        $userFunctionIds = [];
        if ($permissions['authUserId'] && $orgId) {
            // We need function IDs — get from cached membership data
            $userFunctionIds = cache()->remember(
                "org_member_functions:{$orgId}:{$permissions['authUserId']}",
                CacheHelper::TTL,
                fn () => \Illuminate\Support\Facades\DB::table('organization_memberships')
                    ->where('organization_id', $orgId)
                    ->where('user_id', $permissions['authUserId'])
                    ->whereNotNull('organization_function_id')
                    ->pluck('organization_function_id')
                    ->map(fn ($v) => (int) $v)
                    ->all()
            );
        }

        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        $isLocked = $ticket->isLocked();
        $canBypassLock = $authUser && $ticket->canBypassLock($authUser);
        $canApproveTicket = $authUser && $ticket->isPendingApproval() && ApprovalService::canApprove($ticket, $authUser);

        return [
            'ticket' => $ticket,
            'ticketPublicId' => $this->ticketPublicId,
            'orgUsers' => $usersData['orgUsers'],
            'mentionableUsers' => $usersData['mentionableUsers'],
            'embedded' => $this->embedded,
            'canSeeInternalNotes' => $this->canSeeInternalNotes,
            'canWriteInternalNotes' => $this->canWriteInternalNotes,
            'canEditChecklist' => $permissions['canEditChecklist'],
            'showAddChecklistItem' => $this->showAddChecklistItem,
            'canEditTicket' => $permissions['canEditTicket'],
            'canDeleteTicket' => $permissions['canDeleteTicket'],
            'canAssignTicket' => Gate::allows('assign', $ticket),
            'canEditDueDate' => $permissions['canEditDueDate'],
            'canArchive' => $permissions['canArchive'],
            'isStaffOrTicketOwner' => $permissions['isStaffOrTicketOwner'],
            'authUserId' => $permissions['authUserId'],
            'userFunctionIds' => $userFunctionIds,
            'orgPriorities' => $orgRef['orgPriorities'],
            'formResponse' => $orgRef['formResponse'],
            'orgMemberRoles' => $orgRef['orgMemberRoles'],
            'organizationFunctions' => $orgRef['organizationFunctions'],
            'ticketGroups' => $orgRef['ticketGroups'],
            'isLocked' => $isLocked,
            'canBypassLock' => $canBypassLock,
            'canApproveTicket' => $canApproveTicket,
        ];
    }

    /** Cached view data that rarely changes (org priorities, roles, functions, groups). */
    private ?array $cachedOrgRef = null;

    private ?array $cachedUsersData = null;

    public function render(): \Illuminate\Contracts\View\View
    {
        $layout = $this->embedded ? 'layouts.manexo-embed' : 'layouts.manexo-app';

        $ticket = $this->loadTicketForRender();
        $this->computeNotePermissions($ticket);

        // Stage 2+: load messages (the heavy part, deferred from stage 1)
        if ($this->loadStage >= 2) {
            $ticket->load([
                'messages' => function ($q) {
                    if (! $this->canSeeInternalNotes) {
                        $q->where('type', '!=', TicketMessageType::InternalNote);
                    }
                    // Keep initial payload compact: recent messages only.
                    $q->select([
                        'id',
                        'ticket_id',
                        'user_id',
                        'type',
                        'body',
                        'attachments',
                        'meta',
                        'created_at',
                    ])
                        ->with('user:id,name,email')
                        ->latest('id')
                        ->limit(80);
                },
            ]);
            $ticket->setRelation('messages', $ticket->messages->sortBy('id')->values());
        }

        // Cache org reference data within the request lifecycle (priorities, roles, functions)
        $this->cachedOrgRef ??= $this->buildOrgReferenceData($ticket);
        $this->cachedUsersData ??= $this->buildUsersData($ticket);

        $viewData = $this->getDiscussionViewData($ticket);
        $viewData['loadStage'] = $this->loadStage;

        /** @var \Illuminate\View\View $view */
        $view = view('livewire.tickets.discussion', $viewData);

        return $view->layout($layout);
    }

    /** Assigner le ticket à une fonction métier (pool). Réservé au staff. */
    public function setAssignedToFunction($functionId): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }
        $ticket = $this->getTicket();
        if (! ($user instanceof User) || ! $user->hasPermission(Permission::TicketsAssign)) {
            abort(403);
        }
        $id = $functionId === '' || $functionId === null ? null : (int) $functionId;
        if ($id !== null) {
            OrganizationFunction::query()
                ->where('organization_id', $ticket->organization_id)
                ->whereKey($id)
                ->firstOrFail();
        }
        $ticket->update(['assigned_to_function_id' => $id]);
    }

    public function toggleChecklistItem(int $id): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }
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
        if (! $user) {
            abort(403);
        }
        $ticket = $this->getTicket();
        if (! $ticket->hasDiscussionAccess((int) $user->id)) {
            abort(403);
        }

        /** @var TicketChecklistItem $item */
        $item = $ticket->checklistItems()->whereKey($id)->firstOrFail();

        if (! $item->assigned_to_function_id) {
            abort(403);
        }
        if ($item->assigned_to) {
            return; // Already claimed
        }

        $orgId = (int) session('current_organization_id');
        $belongsToFunction = \Illuminate\Support\Facades\DB::table('organization_memberships')
            ->where('organization_id', $orgId)
            ->where('user_id', $user->id)
            ->where('organization_function_id', $item->assigned_to_function_id)
            ->exists();

        if (! $belongsToFunction || ! $this->isInternalStaffUser((int) $user->id, $orgId)) {
            abort(403);
        }

        $item->claim((int) $user->id);
    }

    public function openAddChecklistForm(): void
    {
        $this->showAddChecklistItem = true;
    }

    public function closeAddChecklistForm(): void
    {
        $this->showAddChecklistItem = false;
    }

    public function addChecklistItemToTicket(): void
    {
        $this->newChecklistAssignedTo = ($this->newChecklistAssignedTo === '' || $this->newChecklistAssignedTo === null)
            ? null
            : (int) $this->newChecklistAssignedTo;
        $this->newChecklistAssignedToFunction = ($this->newChecklistAssignedToFunction === '' || $this->newChecklistAssignedToFunction === null)
            ? null
            : (int) $this->newChecklistAssignedToFunction;
        $this->newChecklistDueDate = ($this->newChecklistDueDate === '' || $this->newChecklistDueDate === null)
            ? null
            : $this->newChecklistDueDate;

        // Mutual exclusivity: function OR responsable, not both
        if ($this->newChecklistAssignedToFunction) {
            $this->newChecklistAssignedTo = null;
        }

        $this->validate([
            'newChecklistTitle' => ['required', 'string', 'max:500'],
            'newChecklistAssignedTo' => ['nullable', 'integer', 'exists:users,id'],
            'newChecklistAssignedToFunction' => ['nullable', 'integer', 'exists:organization_functions,id'],
            'newChecklistDueDate' => ['nullable', 'date'],
        ]);
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }
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
        $item = TicketChecklistItem::create([
            'ticket_id' => $ticket->id,
            'title' => trim($this->newChecklistTitle),
            'assigned_to' => $this->newChecklistAssignedTo,
            'assigned_to_function_id' => $this->newChecklistAssignedToFunction,
            'due_date' => $this->newChecklistDueDate ? \Carbon\Carbon::parse($this->newChecklistDueDate) : null,
            'sort_order' => $maxOrder + 1,
        ]);
        // Insert into pivot table if assigned
        if ($this->newChecklistAssignedTo) {
            $item->assignees()->attach($this->newChecklistAssignedTo, [
                'role' => 'responsible',
                'assigned_by' => $user->id,
            ]);
            // Notify if assigning someone else
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

    /** Add a collaborator to a checklist item. */
    public function addChecklistItemAssignee(int $itemId, int $userId): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }
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
        // Sync assigned_to for backward compatibility if this is the first assignee
        if (! $hasAssignees) {
            $item->update(['assigned_to' => $userId]);
        }
        // Notify
        if ((int) $userId !== (int) $user->id) {
            $assignedUser = User::find($userId);
            if ($assignedUser) {
                $assignedUser->notify(new ChecklistItemAssignedNotification($item, $ticket, $user, 'assigned'));
                event(new UserNotificationReceived($userId, 'checklist_item_assigned'));
            }
        }
    }

    /** Remove an assignee from a checklist item. */
    public function removeChecklistItemAssignee(int $itemId, int $userId): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }
        $ticket = $this->getTicket();
        $this->guardAgainstLock($ticket);
        if (! $ticket->hasDiscussionAccess((int) $user->id)) {
            abort(403);
        }
        /** @var TicketChecklistItem $item */
        $item = $ticket->checklistItems()->whereKey($itemId)->firstOrFail();
        $wasResponsible = $item->assignees()->where('user_id', $userId)->wherePivot('role', 'responsible')->exists();
        $item->assignees()->detach($userId);
        // Promote first remaining if responsible was removed
        if ($wasResponsible) {
            $next = $item->assignees()->first();
            if ($next) {
                $item->assignees()->updateExistingPivot($next->id, ['role' => 'responsible']);
                $item->update(['assigned_to' => $next->id]);
            } else {
                $item->update(['assigned_to' => null]);
            }
        }
        // Sync assigned_to if the removed user was the main assignee
        if ($item->assigned_to && (int) $item->assigned_to === $userId) {
            $next = $item->assignees()->wherePivot('role', 'responsible')->first() ?? $item->assignees()->first();
            $item->update(['assigned_to' => $next?->id]);
        }
        // Notify
        if ((int) $userId !== (int) $user->id) {
            $removedUser = User::find($userId);
            if ($removedUser) {
                $removedUser->notify(new ChecklistItemAssignedNotification($item, $ticket, $user, 'unassigned'));
                event(new UserNotificationReceived($userId, 'checklist_item_assigned'));
            }
        }
    }
}
