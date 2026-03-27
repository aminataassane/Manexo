<?php

namespace App\Services\Email;

use App\Models\Ticket;
use App\Models\TicketMessage;

class EmailTicketRouter
{
    /**
     * Try to match an inbound email to an existing ticket.
     *
     * @return Ticket|null The matched ticket, or null for a new ticket.
     */
    public static function matchTicket(ParsedEmail $email, int $orgId): ?Ticket
    {
        // 1. Parse reply+{public_id}@domain from To/Delivered-To headers
        $ticket = self::matchByReplyAddress($email, $orgId);
        if ($ticket) {
            return $ticket;
        }

        // 2. In-Reply-To / References → match email_message_id in ticket_messages
        $ticket = self::matchByReferences($email, $orgId);
        if ($ticket) {
            return $ticket;
        }

        // 3. Subject tag [REF-{public_id}] (legacy [MANEXO-{public_id}])
        $ticket = self::matchBySubjectTag($email, $orgId);
        if ($ticket) {
            return $ticket;
        }

        return null;
    }

    private static function matchByReplyAddress(ParsedEmail $email, int $orgId): ?Ticket
    {
        // Check To, Delivered-To, Cc headers for plus-addressing: localpart+PUBLIC_ID@domain
        // Supports both new format (support+TCK-XXX@domain) and legacy (reply+TCK-XXX@domain)
        $toHeaders = [];
        foreach (['To', 'Delivered-To', 'Cc'] as $headerName) {
            $val = $email->headers[self::findHeaderKey($email->headers, $headerName)] ?? null;
            if ($val) {
                $toHeaders[] = is_array($val) ? implode(',', $val) : $val;
            }
        }

        $allTo = implode(',', $toHeaders);
        if (preg_match('/\+(TCK-[A-Z0-9]+)@/i', $allTo, $m)) {
            $publicId = strtoupper($m[1]);

            return Ticket::query()
                ->where('organization_id', $orgId)
                ->where('public_id', $publicId)
                ->first();
        }

        return null;
    }

    private static function matchByReferences(ParsedEmail $email, int $orgId): ?Ticket
    {
        $messageIds = [];

        if ($email->inReplyTo) {
            $messageIds[] = $email->inReplyTo;
        }

        $messageIds = array_merge($messageIds, $email->references);
        $messageIds = array_unique(array_filter($messageIds));

        if (empty($messageIds)) {
            return null;
        }

        // RFC 5322 : les clients envoient parfois <id@domain> ou id@domain — la DB peut stocker l’un ou l’autre
        $candidates = [];
        foreach ($messageIds as $raw) {
            $n = self::normalizeMessageId($raw);
            if ($n === '') {
                continue;
            }
            $candidates[] = $n;
            $candidates[] = '<'.$n.'>';
        }
        $candidates = array_values(array_unique($candidates));

        $ticketMessage = TicketMessage::query()
            ->whereIn('email_message_id', $candidates)
            ->whereHas('ticket', fn ($q) => $q->where('organization_id', $orgId))
            ->with('ticket')
            ->first();

        return $ticketMessage?->ticket;
    }

    /**
     * Normalise un Message-ID pour comparaison (supprime espaces et chevrons).
     */
    public static function normalizeMessageId(?string $id): string
    {
        if ($id === null || trim($id) === '') {
            return '';
        }

        return trim(trim($id), "<> \t\r\n\0\x0B");
    }

    private static function matchBySubjectTag(ParsedEmail $email, int $orgId): ?Ticket
    {
        $subject = $email->subject ?? '';

        if (preg_match('/\[(?:REF|MANEXO)-([A-Z0-9-]+)\]/i', $subject, $m)) {
            $publicId = strtoupper($m[1]);

            return Ticket::query()
                ->where('organization_id', $orgId)
                ->where('public_id', $publicId)
                ->first();
        }

        return null;
    }

    private static function findHeaderKey(array $headers, string $name): string
    {
        $nameLower = strtolower($name);
        foreach ($headers as $key => $value) {
            if (strtolower($key) === $nameLower) {
                return $key;
            }
        }

        return $name;
    }
}
