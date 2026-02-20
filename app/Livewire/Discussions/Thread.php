<?php

namespace App\Livewire\Discussions;

use App\Events\DiscussionMessageSent;
use App\Events\DiscussionParticipantChanged;
use App\Events\UserNotificationReceived;
use App\Models\DiscussionMessage;
use App\Models\DiscussionThread;
use App\Models\User;
use App\Notifications\DiscussionInviteNotification;
use App\Notifications\DiscussionNewMessageNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.manexo-app')]
#[Title('Discussion')]
class Thread extends Component
{
    use WithFileUploads;

    public int $threadId;
    public bool $embedded = false;

    public string $body = '';
    /** @var \Illuminate\Http\UploadedFile[] */
    public $attachmentFiles = [];

    public string $addParticipantSearch = '';

    public function getListeners(): array
    {
        return [
            "echo-private:discussion.{$this->threadId},.discussion.participant.changed" => '$refresh',
        ];
    }

    public function mount(int|string $thread): void
    {
        $this->threadId = (int) $thread;
        $this->authorizeThread();
    }

    private function getThread(): DiscussionThread
    {
        $orgId = (int) session('current_organization_id');

        return DiscussionThread::query()
            ->with(['participants:id,name,email'])
            ->whereKey($this->threadId)
            ->where('organization_id', $orgId)
            ->firstOrFail();
    }

    private function authorizeThread(): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        $thread = $this->getThread();
        if (! $thread->participants()->where('users.id', $user->id)->exists()) {
            abort(403);
        }
    }

    #[Computed]
    public function canManageParticipants(): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        $thread = $this->getThread();
        if (! $thread->is_group) {
            return false;
        }

        // Creator can always manage
        if ((int) $thread->created_by === (int) $user->id) {
            return true;
        }

        // Staff (owner/admin/agent) can also manage
        $org = request()->attributes->get('currentOrganization');
        $role = $org?->pivot?->role ?? 'member';

        return in_array($role, ['owner', 'admin', 'agent'], true);
    }

    public function addParticipant(int $userId): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        $thread = $this->getThread();
        if (! $thread->is_group) {
            return;
        }

        // Auth check
        $isCreator = (int) $thread->created_by === (int) $user->id;
        $org = request()->attributes->get('currentOrganization');
        $role = $org?->pivot?->role ?? 'member';
        $isStaff = in_array($role, ['owner', 'admin', 'agent'], true);

        if (! $isCreator && ! $isStaff) {
            abort(403);
        }

        $orgId = (int) session('current_organization_id');
        $invitee = User::find($userId);
        if (! $invitee || ! $invitee->organizations()->where('organization_id', $orgId)->exists()) {
            return;
        }

        // Already a participant?
        if ($thread->participants()->where('users.id', $userId)->exists()) {
            return;
        }

        $thread->participants()->attach($userId, ['added_by' => $user->id]);

        $threadName = $thread->name ?: __('Groupe de discussion');

        // Notify the invitee
        $invitee->notify(new DiscussionInviteNotification(
            threadId: $thread->id,
            threadName: $threadName,
            isGroup: true,
            inviterId: $user->id,
            inviterName: $user->name,
        ));
        event(new UserNotificationReceived((int) $invitee->id, 'discussion_invite'));

        // Broadcast to all current participants
        event(new DiscussionParticipantChanged(
            threadId: $thread->id,
            userId: $userId,
            action: 'added',
            actorName: $user->name,
            userName: $invitee->name,
        ));

        $this->addParticipantSearch = '';
    }

    public function removeParticipant(int $userId): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        $thread = $this->getThread();
        if (! $thread->is_group) {
            return;
        }

        // Cannot remove the creator
        if ((int) $thread->created_by === $userId) {
            return;
        }

        // Auth check
        $isCreator = (int) $thread->created_by === (int) $user->id;
        $org = request()->attributes->get('currentOrganization');
        $role = $org?->pivot?->role ?? 'member';
        $isStaff = in_array($role, ['owner', 'admin', 'agent'], true);

        if (! $isCreator && ! $isStaff) {
            abort(403);
        }

        $removedUser = User::find($userId);
        if (! $removedUser) {
            return;
        }

        $thread->participants()->detach($userId);

        // Broadcast to remaining participants
        event(new DiscussionParticipantChanged(
            threadId: $thread->id,
            userId: $userId,
            action: 'removed',
            actorName: $user->name,
            userName: $removedUser->name,
        ));
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

        $thread = $this->getThread();
        if (! $thread->participants()->where('users.id', $user->id)->exists()) {
            abort(403);
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $publicDisk */
        $publicDisk = Storage::disk('public');
        $savedAttachments = [];
        foreach ($this->attachmentFiles as $file) {
            $path = $file->store('discussion-messages/'.$thread->id, 'public');
            $savedAttachments[] = [
                'path' => $path,
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'url' => $publicDisk->url($path),
            ];
        }

        $message = DiscussionMessage::create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'body' => trim((string) $this->body),
            'attachments' => $savedAttachments ?: null,
        ]);

        // Broadcast real-time message (primitives)
        event(new DiscussionMessageSent(
            messageId: $message->id,
            threadId: $thread->id,
            userId: $user->id,
            userName: $user->name,
            body: $message->body,
            attachments: $message->attachments,
            meta: $message->meta ?? null,
            createdAt: $message->created_at->toIso8601String(),
        ));

        // Notify other participants
        $threadName = $thread->is_group
            ? ($thread->name ?: __('Groupe de discussion'))
            : $user->name;
        $bodyExcerpt = mb_substr(trim((string) $message->body), 0, 100);

        foreach ($thread->participants as $participant) {
            if ((int) $participant->id === (int) $user->id) {
                continue;
            }
            $participant->notify(new DiscussionNewMessageNotification(
                threadId: $thread->id,
                threadName: $threadName,
                isGroup: $thread->is_group,
                messageId: $message->id,
                senderId: $user->id,
                senderName: $user->name,
                bodyExcerpt: $bodyExcerpt,
            ));
            event(new UserNotificationReceived((int) $participant->id, 'discussion_new_message'));
        }

        $this->body = '';
        $this->attachmentFiles = [];
    }

    public function render()
    {
        $thread = $this->getThread();
        $thread->load([
            'messages.user:id,name,email',
        ]);

        $orgId = (int) session('current_organization_id');
        $orgUsers = User::query()
            ->whereHas('organizations', fn ($q) => $q->where('organization_id', $orgId))
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $layout = $this->embedded ? 'layouts.manexo-embed' : 'layouts.manexo-app';

        /** @var \Illuminate\View\View $view */
        $view = view('livewire.discussions.thread', [
            'thread' => $thread,
            'orgUsers' => $orgUsers,
            'embedded' => $this->embedded,
            'canManageParticipants' => $this->canManageParticipants,
        ]);
        return $view->layout($layout);
    }
}
