<?php

namespace App\Livewire\Tickets;

use App\Enums\TicketMessageType;
use App\Events\TicketMessageSent;
use App\Events\UserNotificationReceived;
use App\Helpers\CacheHelper;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Notifications\TicketMentionNotification;
use App\Notifications\TicketNewMessageNotification;
use App\Services\SlaService;
use App\Services\WebhookService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class TicketComposer extends Component
{
    use WithFileUploads;

    public int $ticketId;

    public string $ticketPublicId = '';

    public bool $canWriteInternalNotes = false;

    public bool $isLocked = false;

    public array $mentionableUsers = [];

    public string $body = '';

    public bool $asInternalNote = false;

    public array $attachments = [];

    /** @var \Illuminate\Http\UploadedFile[] */
    public $attachmentFiles = [];

    public function mount(int $ticketId, string $ticketPublicId, bool $canWriteInternalNotes, bool $isLocked, array $mentionableUsers): void
    {
        $this->ticketId = $ticketId;
        $this->ticketPublicId = $ticketPublicId;
        $this->canWriteInternalNotes = $canWriteInternalNotes;
        $this->isLocked = $isLocked;
        $this->mentionableUsers = $mentionableUsers;
    }

    public function setAsInternalNote(bool $value): void
    {
        $this->asInternalNote = $value;
    }

    public function sendMessage(): void
    {
        $this->validate([
            'body' => ['nullable', 'string', 'max:10000'],
            'attachmentFiles.*' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,csv,txt,zip'],
        ]);

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

        if ($ticket->isLocked() && ! $ticket->canBypassLock($user)) {
            abort(403, __('tickets.locked'));
        }

        $type = $this->asInternalNote ? TicketMessageType::InternalNote : TicketMessageType::Message;

        if ($type === TicketMessageType::InternalNote && ! $this->canWriteInternalNotes) {
            abort(403);
        }

        $body = trim($this->body);
        $mentions = $this->extractMentions($body, (int) $ticket->organization_id);
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

        // SLA: record first response if this is a message from someone other than the creator
        if ($type === TicketMessageType::Message && (int) $user->id !== (int) $ticket->created_by) {
            SlaService::recordFirstResponse($ticket);
        }

        $this->notifyMessageRecipients($ticket, $message, $user, $type);

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

        // Reset form
        $this->body = '';
        $this->asInternalNote = false;
        $this->attachments = [];
        $this->attachmentFiles = [];

        // Notify parent components that a new message was sent
        $this->dispatch('message-sent', messageId: $message->id);
    }

    private function getTicket(): Ticket
    {
        return Ticket::query()
            ->with(['participants:id,name,email', 'creator:id,name,email', 'assignees:id,name,email'])
            ->whereKey($this->ticketId)
            ->where('organization_id', session('current_organization_id'))
            ->firstOrFail();
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

            $byName = (clone $baseQuery)->where('name', 'ilike', '%'.$part.'%')->first();
            if ($byName) {
                $ids[] = (int) $byName->id;
            }
        }

        return array_values(array_unique($ids));
    }

    public function render()
    {
        return view('livewire.tickets.partials.ticket-composer');
    }
}
