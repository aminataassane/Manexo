<?php

namespace App\Services\Email;

use App\Enums\OrganizationRole;
use App\Enums\TicketMessageType;
use App\Enums\TicketSource;
use App\Enums\TicketStatus;
use App\Events\TicketMessageSent;
use App\Events\UserNotificationReceived;
use App\Helpers\CacheHelper;
use App\Models\InboundEmailLog;
use App\Models\OrganizationMailbox;
use App\Models\OrganizationMembership;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketNewMessageNotification;
use App\Services\SlaService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InboundEmailService
{
    public function process(ParsedEmail $email, OrganizationMailbox $mailbox): void
    {
        $orgId = (int) $mailbox->organization_id;

        // 1. Loop detection
        $skipReason = EmailLoopDetector::shouldSkip($email->headers, $email->fromEmail);
        if ($skipReason) {
            $this->log($mailbox, $email, 'skipped_loop', metadata: ['reason' => $skipReason]);

            return;
        }

        // 2. Resolve user
        [$user, $isKnown] = $this->resolveUser($email->fromEmail, $email->fromName, $orgId);

        // 3. Rate limit for unknown senders
        if (! $isKnown && EmailRateLimiter::isRateLimited($email->fromEmail, $orgId)) {
            $this->log($mailbox, $email, 'skipped_rate_limit');

            return;
        }

        // 4. Route to existing ticket or create new
        $existingTicket = EmailTicketRouter::matchTicket($email, $orgId);

        try {
            if ($existingTicket) {
                [$ticket, $message] = $this->addReplyToTicket($existingTicket, $user, $email, $mailbox);
            } else {
                [$ticket, $message] = $this->createNewTicket($user, $email, $mailbox, $orgId, $isKnown);

                // Record rate limit for unknown senders
                if (! $isKnown) {
                    EmailRateLimiter::recordTicketCreation($email->fromEmail, $orgId);
                }
            }

            // 5. Add CC recipients as ticket participants
            if (! empty($email->cc)) {
                $this->addCcAsParticipants($ticket, $email->cc, $orgId, $user);
            }

            // 6. Invalidate caches
            CacheHelper::invalidateDashboard($orgId);
            CacheHelper::invalidateReports($orgId);
            CacheHelper::invalidateTicketCounts($orgId);

            // 7. Log success
            $this->log($mailbox, $email, 'processed', $ticket->id, $message->id);

        } catch (\Throwable $e) {
            Log::error('InboundEmail processing failed', [
                'mailbox_id' => $mailbox->id,
                'from' => $email->fromEmail,
                'error' => $e->getMessage(),
            ]);
            $this->log($mailbox, $email, 'failed', metadata: ['error' => Str::limit($e->getMessage(), 500)]);
        }
    }

    /**
     * Resolve the sender to an existing user or create a guest.
     *
     * @return array{0: User, 1: bool} [user, isKnownMember]
     */
    private function resolveUser(string $fromEmail, ?string $fromName, int $orgId): array
    {
        $fromEmail = strtolower(trim($fromEmail));
        $fromName = $fromName ? trim($fromName) : $fromEmail;

        // Check if sender is an org member
        $membership = OrganizationMembership::query()
            ->where('organization_id', $orgId)
            ->whereHas('user', fn ($q) => $q->where('email', $fromEmail))
            ->with('user')
            ->first();

        if ($membership) {
            return [$membership->user, true];
        }

        // Check for existing guest account
        $existing = User::query()
            ->where('email', $fromEmail)
            ->where('status', 'guest')
            ->first();

        if ($existing) {
            // Ensure membership exists
            OrganizationMembership::query()->firstOrCreate([
                'organization_id' => $orgId,
                'user_id' => $existing->id,
            ], [
                'role' => OrganizationRole::Member->value,
            ]);

            return [$existing, false];
        }

        // Check if a verified user exists with this email (don't hijack)
        $verifiedUser = User::query()
            ->where('email', $fromEmail)
            ->whereNotNull('email_verified_at')
            ->exists();

        if ($verifiedUser) {
            $actor = User::query()->create([
                'name' => $fromName,
                'email' => $fromEmail.'.guest.'.Str::random(8).'@unverified',
                'password' => Str::random(32),
            ]);
            $actor->forceFill(['status' => 'guest'])->save();
        } else {
            $unverified = User::query()->where('email', $fromEmail)->first();
            if ($unverified) {
                $actor = $unverified;
            } else {
                $actor = User::query()->create([
                    'name' => $fromName,
                    'email' => $fromEmail,
                    'password' => Str::random(32),
                ]);
            }
            $actor->forceFill(['status' => 'guest'])->save();
        }

        OrganizationMembership::query()->firstOrCreate([
            'organization_id' => $orgId,
            'user_id' => $actor->id,
        ], [
            'role' => OrganizationRole::Member->value,
        ]);

        return [$actor, false];
    }

    /**
     * @return array{0: Ticket, 1: TicketMessage}
     */
    private function addReplyToTicket(Ticket $ticket, User $user, ParsedEmail $email, OrganizationMailbox $mailbox): array
    {
        // Ensure user has access as participant
        if (! $ticket->hasDiscussionAccess((int) $user->id)) {
            $ticket->participants()->syncWithoutDetaching([$user->id => ['added_by' => null]]);
        }

        // Save attachments
        $attachments = $this->saveAttachments($email, (int) $ticket->organization_id, (int) $ticket->id);

        // Create message
        $message = TicketMessage::query()->create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'type' => TicketMessageType::Message,
            'body' => $email->getCleanBody() ?: '(Contenu vide)',
            'attachments' => $attachments ?: null,
            'email_message_id' => $email->messageId,
        ]);

        // SLA: record first response if sender is not the ticket creator
        if ((int) $user->id !== (int) $ticket->created_by) {
            SlaService::recordFirstResponse($ticket);
        }

        // Reopen ticket if resolved/closed
        if (in_array($ticket->status, [TicketStatus::Resolved, TicketStatus::Closed], true)) {
            SlaService::onReopened($ticket);
            $ticket->update(['status' => TicketStatus::Open]);
        }

        // Broadcast + notify
        event(new TicketMessageSent($message));
        $this->notifyParticipants($ticket, $message, $user);

        return [$ticket, $message];
    }

    /**
     * @return array{0: Ticket, 1: TicketMessage}
     */
    private function createNewTicket(User $user, ParsedEmail $email, OrganizationMailbox $mailbox, int $orgId, bool $isKnown): array
    {
        $subject = $email->subject ?: 'Sans objet';
        // Remove [REF-...] / [MANEXO-...] tag from subject if present
        $subject = preg_replace('/\[(?:REF|MANEXO)-[A-Z0-9-]+\]\s*/i', '', $subject);
        $subject = Str::limit(trim($subject), 255);

        $ticket = Ticket::query()->create([
            'organization_id' => $orgId,
            'created_by' => $user->id,
            'ticket_category_id' => $mailbox->default_category_id,
            'ticket_priority_id' => $mailbox->default_priority_id,
            'ticket_group_id' => $mailbox->default_group_id,
            'status' => $isKnown ? TicketStatus::Open : TicketStatus::Pending,
            'source' => TicketSource::Email,
            'subject' => $subject,
            'description' => Str::limit($email->getCleanBody(), 10000) ?: '(Contenu vide)',
        ]);

        SlaService::applyPolicy($ticket);
        \App\Services\ApprovalService::applyPolicy($ticket);

        // Save attachments
        $attachments = $this->saveAttachments($email, $orgId, (int) $ticket->id);
        if ($attachments) {
            $ticket->update(['attachments' => ['files' => $attachments, 'links' => []]]);
        }

        // System message
        $systemMessage = TicketMessage::query()->create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'type' => TicketMessageType::System,
            'body' => "Ticket créé via email depuis {$email->fromEmail}",
            'email_message_id' => $email->messageId,
        ]);

        \App\Services\AutomationService::evaluate($ticket, 'ticket_created');

        $ticket->load(['category', 'priority', 'group', 'creator', 'assignees']);
        \App\Services\WebhookService::dispatch($orgId, 'ticket.created', [
            'ticket' => (new \App\Http\Resources\Api\V1\TicketResource($ticket))->resolve(),
        ]);

        // Auto-reply: send acknowledgement email to the ticket creator
        $org = \App\Models\Organization::find($orgId);
        $orgName = $org?->name ?? config('app.name', 'Support');
        $autoReplyEnabled = is_array($org?->settings) ? ($org->settings['email']['auto_reply_enabled'] ?? true) : true;
        if ($autoReplyEnabled) {
            try {
                $user->notify(new TicketCreatedNotification(
                    ticketId: $ticket->id,
                    ticketPublicId: $ticket->public_id,
                    ticketReference: $ticket->shortReference(),
                    ticketSubject: $ticket->subject,
                    organizationId: $orgId,
                    organizationName: $orgName,
                ));
                event(new UserNotificationReceived((int) $user->id, 'ticket_created'));
            } catch (\Throwable $e) {
                Log::warning('Failed to send auto-reply for email ticket', [
                    'ticket_id' => $ticket->public_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [$ticket, $systemMessage];
    }

    private function saveAttachments(ParsedEmail $email, int $orgId, int $ticketId): array
    {
        $saved = [];

        foreach ($email->attachments as $attachment) {
            $name = $attachment['name'] ?? 'attachment';
            $safeBase = Str::slug(pathinfo($name, PATHINFO_FILENAME)) ?: 'file';
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $filename = $safeBase.'-'.Str::lower(Str::random(10)).($ext ? '.'.$ext : '');

            $dir = "ticket-attachments/org-{$orgId}/ticket-{$ticketId}";
            $path = "{$dir}/{$filename}";

            Storage::disk('local')->put($path, $attachment['content'] ?? '');

            $saved[] = [
                'disk' => 'local',
                'path' => $path,
                'name' => $name,
                'size' => $attachment['size'] ?? 0,
                'mime' => $attachment['mime'] ?? 'application/octet-stream',
            ];
        }

        return $saved;
    }

    /**
     * Add CC email addresses as ticket participants (if they are org members).
     *
     * @param  array<int, array{email: string, name: string|null}>  $ccList
     */
    private function addCcAsParticipants(Ticket $ticket, array $ccList, int $orgId, User $sender): void
    {
        foreach ($ccList as $cc) {
            $ccEmail = strtolower(trim($cc['email'] ?? ''));
            if ($ccEmail === '') {
                continue;
            }

            // Only add CC if they are an org member (don't auto-add external people)
            $member = OrganizationMembership::query()
                ->where('organization_id', $orgId)
                ->whereHas('user', fn ($q) => $q->where('email', $ccEmail))
                ->with('user')
                ->first();

            if (! $member || ! $member->user) {
                continue;
            }

            $userId = (int) $member->user->id;

            // Skip if already creator, assignee, or participant
            if ($userId === (int) $ticket->created_by) {
                continue;
            }
            if ($ticket->assignees()->where('users.id', $userId)->exists()) {
                continue;
            }
            if ($ticket->participants()->where('users.id', $userId)->exists()) {
                continue;
            }

            $ticket->participants()->attach($userId, ['added_by' => $sender->id]);

            Log::info('CC added as participant', [
                'ticket_id' => $ticket->public_id,
                'cc_email' => $ccEmail,
                'user_id' => $userId,
            ]);
        }
    }

    private function notifyParticipants(Ticket $ticket, TicketMessage $message, User $sender): void
    {
        $ticket->loadMissing(['creator', 'assignees', 'participants']);

        $recipientIds = collect();
        if ($ticket->created_by) {
            $recipientIds->push($ticket->created_by);
        }
        foreach ($ticket->assignees as $assignee) {
            $recipientIds->push($assignee->id);
        }
        foreach ($ticket->participants as $participant) {
            $recipientIds->push($participant->id);
        }

        $recipientIds = $recipientIds->unique()->reject(fn ($id) => (int) $id === (int) $sender->id);

        $recipients = User::query()->whereIn('id', $recipientIds)->get();

        /** @var User $recipient */
        foreach ($recipients as $recipient) {
            try {
                $recipient->notify(new TicketNewMessageNotification($message));
            } catch (\Throwable $e) {
                Log::error('Échec envoi notification email (inbound)', [
                    'ticket_id' => $ticket->public_id ?? $ticket->id,
                    'recipient_id' => $recipient->id,
                    'recipient_email' => $recipient->email,
                    'error' => $e->getMessage(),
                ]);
            }
            event(new UserNotificationReceived((int) $recipient->id, 'ticket_new_message'));
        }
    }

    private function log(
        OrganizationMailbox $mailbox,
        ParsedEmail $email,
        string $status,
        ?int $ticketId = null,
        ?int $ticketMessageId = null,
        ?array $metadata = null,
    ): void {
        InboundEmailLog::query()->create([
            'organization_mailbox_id' => $mailbox->id,
            'imap_uid' => $email->uid,
            'message_id' => $email->messageId,
            'from_email' => $email->fromEmail,
            'from_name' => $email->fromName,
            'subject' => Str::limit($email->subject, 500),
            'status' => $status,
            'ticket_id' => $ticketId,
            'ticket_message_id' => $ticketMessageId,
            'metadata' => $metadata,
        ]);
    }
}
