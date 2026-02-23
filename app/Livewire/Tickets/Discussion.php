<?php

namespace App\Livewire\Tickets;

use App\Enums\TicketMessageType;
use App\Events\TicketAssigneeChanged;
use App\Helpers\CacheHelper;
use App\Events\TicketMessageSent;
use App\Events\UserNotificationReceived;
use App\Enums\TicketStatus;
use App\Models\FormResponse;
use App\Models\OrganizationFunction;
use App\Models\Ticket;
use App\Models\TicketChecklistItem;
use App\Models\TicketMessage;
use App\Models\TicketParticipant;
use App\Models\TicketPriority;
use App\Models\User;
use App\Notifications\TicketAssigneeNotification;
use App\Notifications\TicketMentionNotification;
use App\Notifications\TicketNewMessageNotification;
use Illuminate\Support\Facades\Auth;
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

    public function getListeners(): array
    {
        return [
            "echo-private:ticket.{$this->ticketId},.assignee.changed" => '$refresh',
        ];
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
    public ?string $newChecklistDueDate = null;

    /** Édition du ticket (titre, description, pièces jointes) */
    public string $editSubject = '';
    public string $editDescription = '';
    /** @var \Illuminate\Http\UploadedFile[]|array */
    public $editAttachmentFiles = [];
    public string $editLinkUrl = '';

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

        $isStaff = in_array($this->getCurrentUserRole(), ['owner', 'admin', 'agent'], true);

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
        return in_array($this->getCurrentUserRole(), ['owner', 'admin', 'agent'], true);
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
        CacheHelper::invalidateDashboard((int) $ticket->organization_id);
        CacheHelper::invalidateReports((int) $ticket->organization_id);
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
        CacheHelper::invalidateDashboard((int) $ticket->organization_id);
        CacheHelper::invalidateReports((int) $ticket->organization_id);
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
        CacheHelper::invalidateDashboard((int) $ticket->organization_id);
        CacheHelper::invalidateReports((int) $ticket->organization_id);
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
            /** @var User $mentionedUser */
            foreach ($mentionedUsers as $mentionedUser) {
                $mentionedUser->notify(new TicketMentionNotification($message, $user));
                event(new UserNotificationReceived((int) $mentionedUser->id, 'ticket_mention'));
            }
        }

        $orgId = (int) $ticket->organization_id;
        CacheHelper::invalidateDashboard($orgId);
        CacheHelper::invalidateReports($orgId);

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
        $isStaff = in_array($this->getCurrentUserRole(), ['owner', 'admin', 'agent'], true);
        $isCreator = (int) $ticket->created_by === (int) $user->id;
        $isAssignee = $ticket->assignees()->where('users.id', $user->id)->exists();

        if (! ($isStaff || $isCreator || $isAssignee)) {
            abort(403);
        }

        $ticket->update(['archived_at' => now()]);
        CacheHelper::invalidateDashboard((int) $ticket->organization_id);
        CacheHelper::invalidateReports((int) $ticket->organization_id);
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

        $isStaff = in_array($this->getCurrentUserRole(), ['owner', 'admin', 'agent'], true);
        $isCreator = (int) $ticket->created_by === (int) $user->id;
        $isAssignee = $ticket->assignees()->where('users.id', $user->id)->exists();

        if (! ($isStaff || $isCreator || $isAssignee)) {
            abort(403);
        }

        $ticket->update(['archived_at' => null]);
        CacheHelper::invalidateDashboard((int) $ticket->organization_id);
        CacheHelper::invalidateReports((int) $ticket->organization_id);
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
        $isStaff = in_array($this->getCurrentUserRole(), ['owner', 'admin', 'agent'], true);
        $isCreator = (int) $ticket->created_by === (int) $user->id;
        $canDelete = $isStaff || ($isCreator && $ticket->status !== TicketStatus::Closed);
        if (! $canDelete) {
            abort(403);
        }
        $orgId = (int) $ticket->organization_id;
        $ticket->delete();
        CacheHelper::invalidateDashboard($orgId);
        CacheHelper::invalidateReports($orgId);
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
        // Rôle depuis la relation user->organizations (pivot fiable) plutôt que request()->attributes
        $orgId = (int) $ticket->organization_id;
        $membership = $user->organizations()->where('organization_id', $orgId)->first();
        $role = $membership?->pivot?->role ?? 'member';
        $isStaff = in_array($role, ['owner', 'admin', 'agent'], true);
        $isCreator = $ticket->created_by !== null && (int) $ticket->created_by === (int) $user->id;
        return $isStaff || $isCreator;
    }

    /** Modifier le titre du ticket. Autorisé au créateur ou au staff. */
    public function updateSubject(): void
    {
        if (! $this->canEditTicketBase()) {
            abort(403);
        }
        $subject = trim($this->editSubject ?? '');
        if ($subject === '') {
            return;
        }
        $ticket = $this->getTicket();
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
        $ticket->update(['description' => $this->editDescription ?? '']);
        session()->flash('tickets_status', __('Description mise à jour.'));
    }

    /** Ajouter une pièce jointe (fichier) au ticket. Autorisé au créateur ou au staff. */
    public function addTicketAttachmentFile(): void
    {
        if (! $this->canEditTicketBase()) {
            abort(403);
        }
        $this->validate([
            'editAttachmentFiles.*' => ['nullable', 'file', 'max:10240'],
        ]);
        $files = $this->editAttachmentFiles ?? [];
        if (! is_array($files) || count($files) === 0) {
            return;
        }
        $ticket = $this->getTicket();
        $orgId = (int) $ticket->organization_id;
        $attachments = is_array($ticket->attachments) ? $ticket->attachments : ['files' => [], 'links' => []];
        $filesList = $attachments['files'] ?? [];
        /** @var \Illuminate\Http\UploadedFile $file */
        foreach ($files as $file) {
            $original = (string) ($file->getClientOriginalName() ?: 'file');
            $ext = (string) ($file->getClientOriginalExtension() ?: '');
            $safeBase = Str::slug(pathinfo($original, PATHINFO_FILENAME)) ?: 'file';
            $filename = $safeBase . '-' . Str::lower(Str::random(10)) . ($ext ? '.' . $ext : '');
            $path = $file->storeAs("ticket-attachments/org-{$orgId}/ticket-{$ticket->id}", $filename, 'public');
            $filesList[] = [
                'disk' => 'public',
                'path' => $path,
                'name' => $original,
                'size' => method_exists($file, 'getSize') ? (int) $file->getSize() : null,
                'mime' => method_exists($file, 'getMimeType') ? (string) $file->getMimeType() : null,
                'url' => asset('storage/' . $path),
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
        $this->validate([
            'editLinkUrl' => ['required', 'string', 'url', 'max:2000'],
        ], [], ['editLinkUrl' => __('URL')]);
        $url = trim($this->editLinkUrl);
        $ticket = $this->getTicket();
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
        if (! in_array($type, ['files', 'links'], true)) {
            return;
        }
        $ticket = $this->getTicket();
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

    /** Change le statut du ticket. Réservé au staff. */
    public function changeStatus(string $status): void
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

        $ticket->update(['status' => $newStatus]);
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'type' => TicketMessageType::System,
            'body' => __('tickets.status_changed', [
                'actor' => $user->name,
                'old' => __('tickets.status.' . $oldStatus->value),
                'new' => __('tickets.status.' . $newStatus->value),
            ]),
            'meta' => ['action' => 'status_changed', 'old' => $oldStatus->value, 'new' => $newStatus->value],
        ]);
        CacheHelper::invalidateDashboard((int) $ticket->organization_id);
        CacheHelper::invalidateReports((int) $ticket->organization_id);
    }

    /** Change la priorité du ticket. Réservé au staff. */
    public function changePriority(int $priorityId): void
    {
        $user = Auth::user();
        if (! $user || ! $this->canAssignTicket()) {
            abort(403);
        }
        $ticket = $this->getTicket();
        $oldPriority = $ticket->priority;
        $newPriority = TicketPriority::where('organization_id', $ticket->organization_id)
            ->where('is_active', true)
            ->whereKey($priorityId)
            ->firstOrFail();

        if ($ticket->ticket_priority_id === $newPriority->id) {
            return;
        }

        $ticket->update(['ticket_priority_id' => $newPriority->id]);
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
    }

    /** Modifie l'échéance du ticket. Staff, créateur, ou assigné. */
    public function updateDueDate(?string $date): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }
        $ticket = $this->getTicket();

        $isStaff = in_array($this->getCurrentUserRole(), ['owner', 'admin', 'agent'], true);
        $isCreator = (int) $ticket->created_by === (int) $user->id;
        $isAssignee = $ticket->assignees()->where('users.id', $user->id)->exists();

        if (! ($isStaff || $isCreator || $isAssignee)) {
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

        // Check canEditChecklist logic
        $isStaff = in_array($this->getCurrentUserRole(), ['owner', 'admin', 'agent'], true);
        $isAssignee = $ticket->assignees->contains('id', $user->id);
        $isCreator = (int) $ticket->created_by === (int) $user->id;

        if (! ($isStaff || $isAssignee || $isCreator)) {
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

        $isStaff = in_array($this->getCurrentUserRole(), ['owner', 'admin', 'agent'], true);
        $isAssignee = $ticket->assignees->contains('id', $user->id);
        $isCreator = (int) $ticket->created_by === (int) $user->id;

        if (! ($isStaff || $isAssignee || $isCreator)) {
            abort(403);
        }

        /** @var TicketChecklistItem $item */
        $item = $ticket->checklistItems()->whereKey($id)->firstOrFail();
        $item->update(['title' => $title]);
    }

    /** Load ticket with relations used by the discussion view (reduces type complexity in render()). */
    private function loadTicketForRender(): Ticket
    {
        return Ticket::query()
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
    }

    /**
     * Build the mentionable users list for the discussion composer.
     *
     * @return array{orgUsers: \Illuminate\Database\Eloquent\Collection, mentionableUsers: array<int, array{id: int, name: string, tag: string}>}
     */
    private function buildUsersData(Ticket $ticket): array
    {
        $orgUsers = cache()->remember(
            "org:{$ticket->organization_id}:users",
            now()->addMinutes(5),
            fn () => User::query()
                ->whereHas('organizations', fn ($q) => $q->where('organization_id', $ticket->organization_id))
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'mention_tag'])
        );

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
            ->map(fn ($id) => $discussionParticipants->firstWhere('id', $id))
            ->filter()
            ->map(fn ($u) => ['id' => $u->id, 'name' => $u->name, 'tag' => $u->mention_tag])
            ->values()
            ->all();

        return compact('orgUsers', 'mentionableUsers');
    }

    /**
     * Compute permission flags for the current user on a ticket.
     *
     * @return array{canEditChecklist: bool, canEditTicket: bool, canDeleteTicket: bool, canEditDueDate: bool, canArchive: bool}
     */
    private function buildPermissionFlags(Ticket $ticket): array
    {
        $userId = (int) (Auth::id() ?: 0);

        $canEditChecklist = $this->canSeeInternalNotes;
        if (! $canEditChecklist && $ticket->assignees->contains('id', $userId)) {
            $canEditChecklist = true;
        }
        if (! $canEditChecklist && $ticket->created_by && (int) $ticket->created_by === $userId) {
            $canEditChecklist = true;
        }

        // Même source de rôle que les actions (évite 403 sur priorité / statut / etc.)
        $role = $this->getCurrentUserRole();
        $isStaff = in_array($role, ['owner', 'admin', 'agent'], true);
        $isCreator = $ticket->created_by !== null && (int) $ticket->created_by === $userId;
        $isAssignee = $ticket->assignees->contains('id', $userId);

        return [
            'canEditChecklist' => $canEditChecklist,
            'canEditTicket' => $isStaff || $isCreator,
            'canDeleteTicket' => $isStaff || ($isCreator && $ticket->status !== TicketStatus::Closed),
            'canEditDueDate' => $isStaff || $isCreator || $isAssignee,
            'canArchive' => $isStaff || $isCreator || $isAssignee,
        ];
    }

    /**
     * Fetch organization-scoped reference data (priorities, member roles, functions).
     *
     * @return array{orgPriorities: \Illuminate\Database\Eloquent\Collection, formResponse: FormResponse|null, orgMemberRoles: array<int, string>, organizationFunctions: \Illuminate\Support\Collection}
     */
    private function buildOrgReferenceData(Ticket $ticket): array
    {
        $orgPriorities = TicketPriority::where('organization_id', $ticket->organization_id)
            ->where('is_active', true)
            ->orderBy('level')
            ->get(['id', 'name', 'level']);

        $formResponse = FormResponse::where('ticket_id', $ticket->id)->first();

        $orgMemberRoles = \Illuminate\Support\Facades\DB::table('organization_memberships')
            ->where('organization_id', $ticket->organization_id)
            ->pluck('role', 'user_id')
            ->all();

        $organizationFunctions = $ticket->organization_id
            ? OrganizationFunction::query()
                ->where('organization_id', $ticket->organization_id)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name'])
            : collect();

        return compact('orgPriorities', 'formResponse', 'orgMemberRoles', 'organizationFunctions');
    }

    /** Build view data for the discussion view. */
    private function getDiscussionViewData(Ticket $ticket): array
    {
        $usersData = $this->buildUsersData($ticket);
        $permissions = $this->buildPermissionFlags($ticket);
        $orgRef = $this->buildOrgReferenceData($ticket);

        return [
            'ticket' => $ticket,
            'orgUsers' => $usersData['orgUsers'],
            'mentionableUsers' => $usersData['mentionableUsers'],
            'embedded' => $this->embedded,
            'canSeeInternalNotes' => $this->canSeeInternalNotes,
            'canWriteInternalNotes' => $this->canWriteInternalNotes,
            'canEditChecklist' => $permissions['canEditChecklist'],
            'showAddChecklistItem' => $this->showAddChecklistItem,
            'canEditTicket' => $permissions['canEditTicket'],
            'canDeleteTicket' => $permissions['canDeleteTicket'],
            'canAssignTicket' => $this->canAssignTicket(),
            'canEditDueDate' => $permissions['canEditDueDate'],
            'canArchive' => $permissions['canArchive'],
            'orgPriorities' => $orgRef['orgPriorities'],
            'formResponse' => $orgRef['formResponse'],
            'orgMemberRoles' => $orgRef['orgMemberRoles'],
            'organizationFunctions' => $orgRef['organizationFunctions'],
        ];
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $ticket = $this->loadTicketForRender();
        $this->computeNotePermissions($ticket);

        $ticket->load([
            'messages' => function ($q) {
                if (! $this->canSeeInternalNotes) {
                    $q->where('type', '!=', TicketMessageType::InternalNote);
                }
                $q->with('user:id,name,email');
            },
        ]);

        $layout = $this->embedded ? 'layouts.manexo-embed' : 'layouts.manexo-app';

        /** @var \Illuminate\View\View $view */
        $view = view('livewire.tickets.discussion', $this->getDiscussionViewData($ticket));

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
        if (! in_array($this->getCurrentUserRole(), ['owner', 'admin', 'agent'], true)) {
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
