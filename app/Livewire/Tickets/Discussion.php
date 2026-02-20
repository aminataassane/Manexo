<?php

namespace App\Livewire\Tickets;

use App\Enums\TicketMessageType;
use App\Events\TicketAssigneeChanged;
use App\Events\TicketMessageSent;
use App\Events\UserNotificationReceived;
use App\Models\OrganizationFunction;
use App\Models\Ticket;
use App\Models\TicketChecklistItem;
use App\Models\TicketMessage;
use App\Models\TicketParticipant;
use App\Models\User;
use App\Notifications\TicketAssigneeNotification;
use App\Notifications\TicketMentionNotification;
use App\Notifications\TicketNewMessageNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Discussion')]
class Discussion extends Component
{
    use WithFileUploads;

    public int $ticketId;

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
    public ?string $newChecklistDueDate = null;

    public function mount(int|string $ticket): void
    {
        $this->ticketId = (int) $ticket;
        $this->authorizeTicket();
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
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }
        $ticket = $this->getTicket();
        if (! $ticket->hasDiscussionAccess((int) $user->id)) {
            abort(403);
        }
    }

    private function computeNotePermissions(Ticket $ticket): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            $this->canSeeInternalNotes = false;
            $this->canWriteInternalNotes = false;

            return;
        }

        // Reuse middleware org to avoid extra query.
        $org = request()->attributes->get('currentOrganization');
        $isStaff = in_array($org?->pivot?->role, ['owner', 'admin', 'agent'], true);

        $this->canSeeInternalNotes = $isStaff;
        $this->canWriteInternalNotes = $isStaff;

        if (! $this->canSeeInternalNotes) {
            $this->asInternalNote = false;
        }
    }

    public function addParticipant(int $userId): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }
        $ticket = $this->getTicket();
        if (! $ticket->hasDiscussionAccess((int) $user->id)) {
            abort(403);
        }
        $orgMember = User::whereKey($userId)->whereHas('organizations', fn($q) => $q->where('organization_id', $ticket->organization_id))->firstOrFail();
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
        event(new TicketAssigneeChanged($ticket->id, $ticket->subject, $userId, 'participant_added', $user->name));
        event(new UserNotificationReceived($userId, 'ticket_assignee'));
    }

    /** Vérifie si l'utilisateur courant peut assigner (Admin/Owner/Agent uniquement). */
    private function canAssignTicket(): bool
    {
        $org = request()->attributes->get('currentOrganization');
        $role = $org?->pivot?->role ?? 'member';
        return in_array($role, ['owner', 'admin', 'agent'], true);
    }

    /** M'assigner : un clic pour devenir assigné (staff uniquement). */
    public function assignToMe(): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }
        if (! $this->canAssignTicket()) {
            abort(403);
        }
        $this->setAssignee((int) $user->id);
    }

    /** Assigner le ticket à une personne (remplace l'assigné actuel). Staff uniquement. Audit: assigned_by, assigned_at. userId=0 pour désassigner. */
    public function setAssignee($userId): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }
        if (! $this->canAssignTicket()) {
            abort(403);
        }
        $userId = (int) $userId;
        $ticket = $this->getTicket();
        if ($userId === 0) {
            $current = $ticket->assignees()->first();
            if ($current) {
                $this->removeAssignee((int) $current->id);
            }
            return;
        }
        $orgMember = User::whereKey($userId)->whereHas('organizations', fn($q) => $q->where('organization_id', $ticket->organization_id))->firstOrFail();
        $previousAssignee = $ticket->assignees()->first();
        $ticket->assignees()->sync([$userId => ['assigned_by' => $user->id]]);
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
        event(new TicketAssigneeChanged($ticket->id, $ticket->subject, $userId, 'assigned', $user->name));
        event(new UserNotificationReceived($userId, 'ticket_assignee'));
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
        $orgMember = User::whereKey($userId)->whereHas('organizations', fn($q) => $q->where('organization_id', $ticket->organization_id))->firstOrFail();
        if ($ticket->assignees()->where('users.id', $userId)->exists()) {
            return;
        }
        $ticket->assignees()->attach($userId, ['assigned_by' => $user->id]);
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
        event(new TicketAssigneeChanged($ticket->id, $ticket->subject, $userId, 'assigned', $user->name));
        event(new UserNotificationReceived($userId, 'ticket_assignee'));
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
        $removedUser = User::find($userId);
        $ticket->assignees()->detach($userId);
        $firstAssignee = $ticket->assignees()->first();
        $ticket->update([
            'assigned_to' => $firstAssignee?->id,
            'assigned_by' => $firstAssignee ? $user->id : null,
            'assigned_at' => $firstAssignee ? now() : null,
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
            event(new TicketAssigneeChanged($ticket->id, $ticket->subject, $userId, 'unassigned', $user->name));
            event(new UserNotificationReceived($userId, 'ticket_assignee'));
        }
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
            event(new TicketAssigneeChanged($ticket->id, $ticket->subject, $userId, 'participant_removed', $user->name));
            event(new UserNotificationReceived($userId, 'ticket_assignee'));
        }
    }

    public function sendMessage(): void
    {
        $this->validate([
            'body' => ['nullable', 'string', 'max:10000'],
            'attachmentFiles.*' => ['nullable', 'file', 'max:10240'],
        ]);
        $hasBody = trim($this->body ?? '') !== '';
        $hasAttachments = is_array($this->attachmentFiles) && count($this->attachmentFiles) > 0;
        if (! $hasBody && ! $hasAttachments) {
            $this->addError('body', __('Ajoutez un message ou joignez au moins un fichier.'));

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
        $type = $this->asInternalNote ? TicketMessageType::InternalNote : TicketMessageType::Message;
        $this->computeNotePermissions($ticket);
        if ($type === TicketMessageType::InternalNote && ! $this->canWriteInternalNotes) {
            abort(403);
        }
        $body = trim($this->body);
        $mentions = $this->extractMentions($body, $ticket->organization_id);
        $savedAttachments = [];
        /** @var \Illuminate\Filesystem\FilesystemAdapter $publicDisk */
        $publicDisk = Storage::disk('public');
        foreach ($this->attachmentFiles as $file) {
            $path = $file->store('ticket-messages/' . $ticket->id, 'public');
            $savedAttachments[] = [
                'path' => $path,
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'url' => $publicDisk->url($path),
            ];
        }
        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'type' => $type,
            'body' => $body,
            'attachments' => array_merge($this->attachments, $savedAttachments) ?: null,
            'meta' => array_filter(['mentions' => $mentions]),
        ]);
        event(new TicketMessageSent($message));

        $notifyUserIds = collect([$ticket->created_by])
            ->merge($ticket->assignees->pluck('id'))
            ->merge($ticket->participants->pluck('id'))
            ->filter()
            ->unique()
            ->diff([$user->id])
            ->values();

        $recipientsQuery = User::whereIn('id', $notifyUserIds);

        // Never notify non-staff about internal notes.
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
                $recipient->notify(new TicketNewMessageNotification($message));
                event(new UserNotificationReceived((int) $recipient->id, 'ticket_new_message'));
            }
        }

        // Send mention-specific notifications
        if (! empty($mentions)) {
            $mentionedUsers = User::whereIn('id', $mentions)
                ->where('id', '!=', $user->id)
                ->get();
            foreach ($mentionedUsers as $mentionedUser) {
                $mentionedUser->notify(new TicketMentionNotification($message, $user));
                event(new UserNotificationReceived((int) $mentionedUser->id, 'ticket_mention'));
            }
        }

        $this->body = '';
        $this->asInternalNote = false;
        $this->attachments = [];
        $this->attachmentFiles = [];
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

        // Only staff or creator/assignee can archive.
        $org = request()->attributes->get('currentOrganization');
        $role = $org?->pivot?->role ?? 'member';
        $isStaff = in_array($role, ['owner', 'admin', 'agent'], true);
        $isCreator = (int) $ticket->created_by === (int) $user->id;
        $isAssignee = $ticket->assignees()->where('users.id', $user->id)->exists();

        if (! ($isStaff || $isCreator || $isAssignee)) {
            abort(403);
        }

        $ticket->update(['archived_at' => now()]);
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

        $org = request()->attributes->get('currentOrganization');
        $role = $org?->pivot?->role ?? 'member';
        $isStaff = in_array($role, ['owner', 'admin', 'agent'], true);
        $isCreator = (int) $ticket->created_by === (int) $user->id;
        $isAssignee = $ticket->assignees()->where('users.id', $user->id)->exists();

        if (! ($isStaff || $isCreator || $isAssignee)) {
            abort(403);
        }

        $ticket->update(['archived_at' => null]);
        session()->flash('tickets_status', __('Ticket restauré.'));
    }

    /** Suppression (soft delete). Réservée au staff (owner, admin, agent). */
    public function deleteTicket(): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }
        $ticket = $this->getTicket();
        $org = request()->attributes->get('currentOrganization');
        $role = $org?->pivot?->role ?? 'member';
        $isStaff = in_array($role, ['owner', 'admin', 'agent'], true);
        if (! $isStaff) {
            abort(403);
        }
        $ticket->delete();
        session()->flash('tickets_status', __('Ticket supprimé.'));
        $this->redirect(route('tickets.index'), navigate: true);
    }

    /** @return int[] */
    private function extractMentions(string $body, int $organizationId): array
    {
        if (! preg_match_all('/@([\p{L}\p{N}_]+(?:\s+[\p{L}\p{N}_]+)*)/u', $body, $m)) {
            return [];
        }
        $ids = [];
        $baseQuery = User::query()->whereHas('organizations', fn($q) => $q->where('organization_id', $organizationId));

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
            $byName = (clone $baseQuery)->where('name', 'ilike', '%' . $part . '%')->first();
            if ($byName) {
                $ids[] = (int) $byName->id;
            }
        }

        return array_values(array_unique($ids));
    }

    public function render()
    {
        $ticket = Ticket::query()
            ->with([
                'creator:id,name,email',
                'assignees:id,name,email',
                'participants:id,name,email',
                'category:id,name',
                'priority:id,name,level',
                'organization:id,name',
                'assignedToFunction:id,name',
                'checklistItems.assignee:id,name,email',
                'checklistItems.doneByUser:id,name,email',
            ])
            ->whereKey($this->ticketId)
            ->where('organization_id', session('current_organization_id'))
            ->firstOrFail();

        $this->computeNotePermissions($ticket);

        $ticket->load([
            'messages' => function ($q) {
                if (! $this->canSeeInternalNotes) {
                    $q->where('type', '!=', TicketMessageType::InternalNote);
                }
                $q->with('user:id,name,email');
            },
        ]);
        $orgUsers = cache()->remember(
            "org:{$ticket->organization_id}:users",
            now()->addMinutes(5),
            fn() => User::query()
                ->whereHas('organizations', fn($q) => $q->where('organization_id', $ticket->organization_id))
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'mention_tag'])
        );

        // @ = uniquement les participants de la discussion (créateur, assignés, participants ajoutés)
        $discussionParticipantIds = collect([$ticket->created_by])
            ->merge($ticket->assignees->pluck('id'))
            ->merge($ticket->participants->pluck('id'))
            ->filter()
            ->unique()
            ->values()
            ->all();
        $discussionParticipants = User::query()
            ->whereIn('id', $discussionParticipantIds)
            ->get(['id', 'name', 'mention_tag']);
        $order = array_values($discussionParticipantIds);
        $mentionableUsers = collect($order)
            ->map(fn($id) => $discussionParticipants->firstWhere('id', $id))
            ->filter()
            ->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'tag' => $u->mention_tag])
            ->values()
            ->all();

        $layout = $this->embedded ? 'layouts.manexo-embed' : 'layouts.manexo-app';

        $canEditChecklist = $this->canSeeInternalNotes; // staff can edit checklist
        $userId = (int) (Auth::id() ?: 0);
        if (! $canEditChecklist && $ticket->assignees->contains('id', $userId)) {
            $canEditChecklist = true; // assignee can also check / add items
        }
        if (! $canEditChecklist && $ticket->created_by && (int) $ticket->created_by === $userId) {
            $canEditChecklist = true; // creator can add/edit checklist too
        }

        /** @var \Illuminate\View\View $view */
        $view = view('livewire.tickets.discussion', [
            'ticket' => $ticket,
            'orgUsers' => $orgUsers,
            'mentionableUsers' => $mentionableUsers,
            'embedded' => $this->embedded,
            'canSeeInternalNotes' => $this->canSeeInternalNotes,
            'canWriteInternalNotes' => $this->canWriteInternalNotes,
            'canEditChecklist' => $canEditChecklist,
            'showAddChecklistItem' => $this->showAddChecklistItem,
            'canDeleteTicket' => $this->canSeeInternalNotes,
            'canAssignTicket' => $this->canAssignTicket(),
            'organizationFunctions' => $ticket->organization_id
                ? OrganizationFunction::query()
                    ->where('organization_id', $ticket->organization_id)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get(['id', 'name'])
                : collect(),
        ]);
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
        $org = request()->attributes->get('currentOrganization');
        $role = $org?->pivot?->role ?? 'member';
        if (! in_array($role, ['owner', 'admin', 'agent'], true)) {
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
        /** @var TicketChecklistItem $item */
        $item = $ticket->checklistItems()->whereKey($id)->firstOrFail();
        if ($item->is_done) {
            $item->markUndone();
        } else {
            $item->markDone((int) $user->id);
        }
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
        $this->newChecklistDueDate = ($this->newChecklistDueDate === '' || $this->newChecklistDueDate === null)
            ? null
            : $this->newChecklistDueDate;

        $this->validate([
            'newChecklistTitle' => ['required', 'string', 'max:500'],
            'newChecklistAssignedTo' => ['nullable', 'integer', 'exists:users,id'],
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
        $maxOrder = $ticket->checklistItems()->max('sort_order') ?? -1;
        TicketChecklistItem::create([
            'ticket_id' => $ticket->id,
            'title' => trim($this->newChecklistTitle),
            'assigned_to' => $this->newChecklistAssignedTo,
            'due_date' => $this->newChecklistDueDate ? \Carbon\Carbon::parse($this->newChecklistDueDate) : null,
            'sort_order' => $maxOrder + 1,
        ]);
        $this->showAddChecklistItem = false;
        $this->newChecklistTitle = '';
        $this->newChecklistAssignedTo = null;
        $this->newChecklistDueDate = null;
    }
}
