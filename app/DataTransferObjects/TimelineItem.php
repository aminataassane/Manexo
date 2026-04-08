<?php

namespace App\DataTransferObjects;

use App\Enums\TicketMessageType;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Support\TicketMessageUserCard;
use Illuminate\Support\Str;

final class TimelineItem
{
    public function __construct(
        public readonly int $id,
        public readonly string $type,
        public readonly string $channel,
        public readonly ?int $authorId,
        public readonly ?string $authorName,
        public readonly ?string $authorEmail,
        public readonly string $roleLabel,
        public readonly string $body,
        public readonly string $bodyRaw,
        public readonly string $timeHuman,
        public readonly string $timeShort,
        public readonly string $date,
        public readonly bool $isOwn,
        public readonly bool $isCreator,
        public readonly bool $isInternal,
        public readonly bool $isSystem,
        public readonly bool $hasEmailOrigin,
        public readonly ?string $emailFrom,
        public readonly bool $hasAttachments,
        public readonly int $attachmentCount,
        public readonly array $attachments,
        public readonly string $badgePrimary,
        public readonly ?string $badgeSecondary,
        public readonly string $avatarUrl,
        public readonly string $createdAt,
        public readonly string $initials,
        public readonly ?string $mentionTag,
        /** @var list<string> */
        public readonly array $popoverBadges,
        public readonly bool $showAvatarPopover,
    ) {}

    /**
     * Build a TimelineItem from a TicketMessage model.
     *
     * All display logic previously in timeline-item.blade.php is centralised here.
     */
    /**
     * @param  array<int|string, string>  $orgRolesByUserId
     * @param  array<string, string>  $roleLabels
     */
    public static function fromMessage(
        TicketMessage $msg,
        Ticket $ticket,
        int $ticketCreatorId,
        int $authUserId,
        array $orgRolesByUserId,
        array $roleLabels,
    ): self {
        $isSystem = $msg->type === TicketMessageType::System;
        $isInternal = $msg->type === TicketMessageType::InternalNote;
        $isCreator = (int) $msg->user_id === $ticketCreatorId;
        $isOwn = (int) $msg->user_id === $authUserId;

        $channel = self::resolveChannel($msg, $ticketCreatorId);
        $hasEmailOrigin = in_array($channel, ['email_inbound', 'email_outbound'], true);
        $isApiOrigin = $channel === 'api';

        // Role label: Client / Support / Système
        $roleLabel = match (true) {
            $isSystem => __('Système'),
            $isInternal => __('Interne'),
            $isCreator => __('Client'),
            default => __('Support'),
        };

        // Badge primary — one badge per message, never two
        $badgePrimary = match ($channel) {
            'email_inbound' => __('Reçu par email'),
            'email_outbound' => __('Envoyé par email'),
            'api' => __('API'),
            default => $roleLabel,
        };

        // Email origin address (for inbound emails)
        $emailFrom = null;
        if ($hasEmailOrigin) {
            $meta = is_array($msg->meta) ? $msg->meta : [];
            $emailFrom = $meta['from_email'] ?? $msg->user?->email ?? null;
        }

        // Author info
        $authorName = $msg->user?->name ?? null;
        $authorEmail = $msg->user?->email ?? null;
        $mentionTag = $msg->user?->mention_tag ? trim((string) $msg->user->mention_tag) : null;
        if ($mentionTag === '') {
            $mentionTag = null;
        }

        $initials = Str::of($authorName ?? 'U')
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn ($p) => Str::upper(Str::substr($p, 0, 1)))
            ->implode('') ?: 'U';

        $uid = (int) ($msg->user_id ?? 0);
        $orgRole = $uid ? ($orgRolesByUserId[$uid] ?? $orgRolesByUserId[(string) $uid] ?? null) : null;
        $popoverBadges = (! $isSystem && ! $isInternal && $uid)
            ? TicketMessageUserCard::badgesForTicketUser($ticket, $uid, $orgRole ? (string) $orgRole : null, $roleLabels)
            : [];
        $showAvatarPopover = ! $isSystem && ! $isInternal && $uid > 0;

        // Avatar URL
        $avatarUrl = $authorName
            ? 'https://ui-avatars.com/api/?name='.urlencode($authorName).'&size=40&background=e2e8f0&color=475569'
            : 'https://ui-avatars.com/api/?name=U&size=40&background=e2e8f0&color=475569';

        // Body formatting: escape, mentions, nl2br
        $bodyRaw = $msg->body ?? '';
        $bodyEscaped = e($bodyRaw);
        $body = self::formatBody($bodyEscaped, $isInternal, $isOwn);

        // Attachments
        $attachments = is_array($msg->attachments) ? $msg->attachments : [];
        $attachmentCount = count($attachments);

        // Time
        $createdAt = $msg->created_at;
        $timeHuman = $createdAt?->diffForHumans() ?? '';
        $timeShort = $createdAt?->format('H:i') ?? '';
        $date = $createdAt?->format('Y-m-d') ?? '';

        return new self(
            id: $msg->id,
            type: $msg->type->value,
            channel: $channel,
            authorId: $msg->user_id ? (int) $msg->user_id : null,
            authorName: $authorName,
            authorEmail: $authorEmail,
            roleLabel: $roleLabel,
            body: $body,
            bodyRaw: $bodyRaw,
            timeHuman: $timeHuman,
            timeShort: $timeShort,
            date: $date,
            isOwn: $isOwn,
            isCreator: $isCreator,
            isInternal: $isInternal,
            isSystem: $isSystem,
            hasEmailOrigin: $hasEmailOrigin,
            emailFrom: $emailFrom,
            hasAttachments: $attachmentCount > 0,
            attachmentCount: $attachmentCount,
            attachments: $attachments,
            badgePrimary: $badgePrimary,
            badgeSecondary: null,
            avatarUrl: $avatarUrl,
            createdAt: $createdAt?->toIso8601String() ?? '',
            initials: $initials,
            mentionTag: $mentionTag,
            popoverBadges: $popoverBadges,
            showAvatarPopover: $showAvatarPopover,
        );
    }

    /**
     * Resolve the communication channel from message data.
     */
    private static function resolveChannel(TicketMessage $msg, int $ticketCreatorId): string
    {
        if ($msg->type === TicketMessageType::System) {
            return 'system';
        }

        if ($msg->type === TicketMessageType::InternalNote) {
            return 'internal';
        }

        // Message with email_message_id = came from or sent via email
        if ($msg->email_message_id) {
            // If author is the ticket creator → inbound email (client replied by email)
            if ((int) $msg->user_id === $ticketCreatorId) {
                return 'email_inbound';
            }

            // Otherwise → outbound email (agent reply sent by email)
            return 'email_outbound';
        }

        $meta = is_array($msg->meta) ? $msg->meta : [];

        // Message with meta flag indicating email was sent
        if (! empty($meta['email_sent'])) {
            return 'email_outbound';
        }

        // Message created via API
        if (($meta['source'] ?? null) === 'api') {
            return 'api';
        }

        return 'platform';
    }

    /**
     * Format message body: escape HTML, highlight @mentions, convert newlines.
     */
    private static function formatBody(string $bodyEscaped, bool $isInternal, bool $isOwn): string
    {
        $mentionPattern = '/@([\p{L}\p{N}_]+(?:\s+[\p{L}\p{N}_]+)*)/u';

        if ($isInternal) {
            $bodyWithMentions = preg_replace(
                $mentionPattern,
                '<span class="mention font-medium" style="color: color-mix(in srgb, var(--accent) 55%, black);">@$1</span>',
                $bodyEscaped
            );
        } elseif ($isOwn) {
            $bodyWithMentions = preg_replace(
                $mentionPattern,
                '<span class="mention font-medium opacity-90">@$1</span>',
                $bodyEscaped
            );
        } else {
            $bodyWithMentions = preg_replace(
                $mentionPattern,
                '<span class="mention font-medium text-slate-700">@$1</span>',
                $bodyEscaped
            );
        }

        return nl2br($bodyWithMentions);
    }
}
