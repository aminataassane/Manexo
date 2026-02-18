<?php

namespace App\Livewire\Discussions;

use App\Events\DiscussionMessageSent;
use App\Models\DiscussionMessage;
use App\Models\DiscussionThread;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

        event(new DiscussionMessageSent($message));

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
        ]);
        return $view->layout($layout);
    }
}

