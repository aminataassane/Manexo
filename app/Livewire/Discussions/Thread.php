<?php

namespace App\Livewire\Discussions;

use App\Events\DiscussionMessageSent;
use App\Events\DiscussionParticipantChanged;
use App\Events\UserNotificationReceived;
use App\Helpers\CacheHelper;
use App\Models\DiscussionMessage;
use App\Models\DiscussionThread;
use App\Models\User;
use App\Notifications\DiscussionRemovedNotification;
use App\Notifications\DiscussionInviteNotification;
use App\Notifications\DiscussionNewMessageNotification;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
    public int $messagesLimit = 120;

    public string $body = '';
    /** @var \Illuminate\Http\UploadedFile[] */
    public $attachmentFiles = [];

    public string $addParticipantSearch = '';

    private ?DiscussionThread $threadCache = null;

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
        $this->markThreadNotificationsAsRead($this->threadId);
    }

    private function getThread(): DiscussionThread
    {
        if ($this->threadCache instanceof DiscussionThread && (int) $this->threadCache->id === $this->threadId) {
            return $this->threadCache;
        }

        $orgId = (int) session('current_organization_id');

        $this->threadCache = DiscussionThread::query()
            ->with(['participants:id,name,email'])
            ->whereKey($this->threadId)
            ->where('organization_id', $orgId)
            ->firstOrFail();

        return $this->threadCache;
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
        $this->broadcastSafe(fn () => event(new UserNotificationReceived((int) $invitee->id, 'discussion_invite')));

        // Broadcast to all current participants
        $this->broadcastSafe(fn () => event(new DiscussionParticipantChanged(
            threadId: $thread->id,
            userId: $userId,
            action: 'added',
            actorName: $user->name,
            userName: $invitee->name,
        )));

        $this->addParticipantSearch = '';
        $this->threadCache = null;
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
        $threadName = $thread->name ?: __('Groupe de discussion');
        $removedUser->notify(new DiscussionRemovedNotification(
            threadId: $thread->id,
            threadName: $threadName,
            actorName: $user->name,
        ));
        $this->broadcastSafe(fn () => event(new UserNotificationReceived((int) $removedUser->id, 'discussion_removed')));

        // Broadcast to remaining participants
        $this->broadcastSafe(fn () => event(new DiscussionParticipantChanged(
            threadId: $thread->id,
            userId: $userId,
            action: 'removed',
            actorName: $user->name,
            userName: $removedUser->name,
        )));
        $this->threadCache = null;
    }

    public function loadMoreMessages(): void
    {
        $this->messagesLimit = min(600, $this->messagesLimit + 120);
    }

    public function sendMessage(): void
    {
        $this->validate([
            'body' => ['nullable', 'string', 'max:10000'],
            'attachmentFiles.*' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,csv,txt,zip'],
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

        $savedAttachments = [];
        foreach ($this->attachmentFiles as $file) {
            $path = $file->store('discussion-messages/'.$thread->id, 'local');
            $filename = basename($path);
            $savedAttachments[] = [
                'disk' => 'local',
                'path' => $path,
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'url' => route('discussions.file', ['thread' => $thread->id, 'filename' => $filename]),
            ];
        }

        $message = DiscussionMessage::create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'body' => trim((string) $this->body),
            'attachments' => $savedAttachments ?: null,
        ]);

        // Marquer comme lues les notifs de ce fil pour l'utilisateur (il est dans la conversation)
        $this->markThreadNotificationsAsRead($thread->id);

        // Broadcast en après-réponse pour ne pas bloquer l'envoi (Pusher peut être lent ou injoignable)
        $createdAt = $message->created_at->toIso8601String();
        $messageId = $message->id;
        $threadId = $thread->id;
        $userId = $user->id;
        $userName = $user->name;
        $body = $message->body;
        $attachments = $message->attachments;
        $meta = $message->meta ?? null;
        dispatch(function () use ($messageId, $threadId, $userId, $userName, $body, $attachments, $meta, $createdAt) {
            try {
                event(new DiscussionMessageSent(
                    messageId: $messageId,
                    threadId: $threadId,
                    userId: $userId,
                    userName: $userName,
                    body: $body,
                    attachments: $attachments,
                    meta: $meta,
                    createdAt: $createdAt,
                ));
            } catch (BroadcastException $e) {
                Log::warning('Broadcast failed (Pusher/WebSocket may be down).', ['exception' => $e->getMessage()]);
            }
        })->afterResponse();

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
            $participantId = (int) $participant->id;
            dispatch(function () use ($participantId) {
                try {
                    event(new UserNotificationReceived($participantId, 'discussion_new_message'));
                } catch (BroadcastException $e) {
                    Log::warning('Broadcast notification failed.', ['exception' => $e->getMessage()]);
                }
            })->afterResponse();
        }

        $this->body = '';
        $this->attachmentFiles = [];
    }

    /**
     * Run a broadcast (event) in a try-catch so that if Pusher/WebSocket is unavailable,
     * the request still succeeds (message is saved, notifications are sent).
     */
    private function broadcastSafe(callable $fn): void
    {
        try {
            $fn();
        } catch (BroadcastException $e) {
            Log::warning('Broadcast failed (Pusher/WebSocket may be down). Message and notifications were still saved.', [
                'exception' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Marquer comme lues toutes les notifications de discussion liées à ce fil
     * (ouverture du fil ou envoi d'un message = considéré comme lu).
     * Une seule requête UPDATE avec filtre JSON pour éviter de charger toutes les notifs.
     */
    private function markThreadNotificationsAsRead(int $threadId): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $driver = DB::connection()->getDriverName();
        $types = [
            DiscussionNewMessageNotification::class,
            DiscussionInviteNotification::class,
        ];

        $query = DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->whereIn('type', $types);

        if ($driver === 'pgsql') {
            // data is text; cast to jsonb so ->> works
            $query->whereRaw("((data::jsonb)->>'thread_id')::bigint = ?", [$threadId]);
        } else {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.thread_id')) = ?", [(string) $threadId]);
        }

        $query->update(['read_at' => now()]);
    }

    public function render()
    {
        $thread = $this->getThread();
        $messages = DiscussionMessage::query()
            ->where('thread_id', $thread->id)
            ->select(['id', 'thread_id', 'user_id', 'body', 'attachments', 'created_at'])
            ->with('user:id,name')
            ->latest('id')
            ->limit($this->messagesLimit)
            ->get()
            ->reverse()
            ->values();
        $thread->setRelation('messages', $messages);

        $totalMessages = cache()->remember(
            "disc:thread_total_messages:{$thread->id}",
            60,
            fn () => (int) DiscussionMessage::query()
                ->where('thread_id', $thread->id)
                ->count()
        );
        $hasMoreMessages = $totalMessages > $messages->count();

        $orgId = (int) session('current_organization_id');
        $canManageParticipants = $this->canManageParticipants;
        $orgUsers = ($thread->is_group && $canManageParticipants)
            ? cache()->remember(
                CacheHelper::membersKey($orgId),
                CacheHelper::TTL,
                fn () => User::query()
                    ->whereHas('organizations', fn ($q) => $q->where('organization_id', $orgId))
                    ->orderBy('name')
                    ->limit(200)
                    ->get(['id', 'name', 'email'])
            )
            : collect();

        $layout = $this->embedded ? 'layouts.manexo-embed' : 'layouts.manexo-app';

        /** @var \Illuminate\View\View $view */
        $view = view('livewire.discussions.thread', [
            'thread' => $thread,
            'orgUsers' => $orgUsers,
            'embedded' => $this->embedded,
            'canManageParticipants' => $canManageParticipants,
            'hasMoreMessages' => $hasMoreMessages,
        ]);
        return $view->layout($layout);
    }
}
