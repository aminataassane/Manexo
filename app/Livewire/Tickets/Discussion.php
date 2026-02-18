<?php

namespace App\Livewire\Tickets;

use App\Enums\TicketMessageType;
use App\Events\TicketMessageSent;
use App\Models\Ticket;
use App\Models\TicketChecklistItem;
use App\Models\TicketMessage;
use App\Models\TicketParticipant;
use App\Models\User;
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
            ->with(['participants:id,name,email', 'creator:id,name,email', 'assignee:id,name,email'])
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

        // Only support team can access internal notes.
        $isStaff = $user->organizations()
            ->where('organizations.id', $ticket->organization_id)
            ->wherePivotIn('role', ['owner', 'admin', 'agent'])
            ->exists();

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
        if ($ticket->created_by === (int) $userId || $ticket->assigned_to === (int) $userId) {
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
        $ticket->participants()->detach($userId);
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

        $notifyUserIds = collect([$ticket->created_by, $ticket->assigned_to])
            ->merge($ticket->participants->pluck('user_id'))
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

        // Only staff or owner/assignee can archive.
        $role = $user->organizations()
            ->where('organizations.id', $ticket->organization_id)
            ->first()?->pivot?->role ?? 'member';
        $isStaff = in_array($role, ['owner', 'admin', 'agent'], true);
        $isCreator = (int) $ticket->created_by === (int) $user->id;
        $isAssignee = (int) ($ticket->assigned_to ?? 0) === (int) $user->id;

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

        $role = $user->organizations()
            ->where('organizations.id', $ticket->organization_id)
            ->first()?->pivot?->role ?? 'member';
        $isStaff = in_array($role, ['owner', 'admin', 'agent'], true);
        $isCreator = (int) $ticket->created_by === (int) $user->id;
        $isAssignee = (int) ($ticket->assigned_to ?? 0) === (int) $user->id;

        if (! ($isStaff || $isCreator || $isAssignee)) {
            abort(403);
        }

        $ticket->update(['archived_at' => null]);
        session()->flash('tickets_status', __('Ticket restauré.'));
    }

    /** @return int[] */
    private function extractMentions(string $body, int $organizationId): array
    {
        if (! preg_match_all('/@(\w+(?:\s+\w+)*)/u', $body, $m)) {
            return [];
        }
        $names = array_map('trim', $m[1]);
        $users = User::query()
            ->whereHas('organizations', fn($q) => $q->where('organization_id', $organizationId))
            ->where(function ($q) use ($names) {
                foreach ($names as $name) {
                    $q->orWhere('name', 'like', '%' . $name . '%');
                }
            })
            ->pluck('id')
            ->unique()
            ->values()
            ->all();
        return $users;
    }

    public function render()
    {
        $ticket = Ticket::query()
            ->with([
                'creator:id,name,email',
                'assignee:id,name,email',
                'participants:id,name,email',
                'category:id,name',
                'priority:id,name,level',
                'organization:id,name',
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
        $orgUsers = User::query()
            ->whereHas('organizations', fn($q) => $q->where('organization_id', $ticket->organization_id))
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $layout = $this->embedded ? 'layouts.manexo-embed' : 'layouts.manexo-app';

        $canEditChecklist = $this->canSeeInternalNotes; // staff can edit checklist
        $userId = (int) (Auth::id() ?: 0);
        if (! $canEditChecklist && $ticket->assigned_to && (int) $ticket->assigned_to === $userId) {
            $canEditChecklist = true; // assignee can also check / add items
        }
        if (! $canEditChecklist && $ticket->created_by && (int) $ticket->created_by === $userId) {
            $canEditChecklist = true; // creator can add/edit checklist too
        }

        /** @var \Illuminate\View\View $view */
        $view = view('livewire.tickets.discussion', [
            'ticket' => $ticket,
            'orgUsers' => $orgUsers,
            'embedded' => $this->embedded,
            'canSeeInternalNotes' => $this->canSeeInternalNotes,
            'canWriteInternalNotes' => $this->canWriteInternalNotes,
            'canEditChecklist' => $canEditChecklist,
            'showAddChecklistItem' => $this->showAddChecklistItem,
        ]);
        return $view->layout($layout);
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
