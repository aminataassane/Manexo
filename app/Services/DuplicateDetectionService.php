<?php

namespace App\Services;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Detect potential duplicate tickets based on subject similarity and recent activity.
 *
 * Uses PostgreSQL trigram similarity (pg_trgm) when available,
 * falls back to ILIKE matching.
 */
class DuplicateDetectionService
{
    private const MAX_RESULTS = 5;

    private const MIN_SIMILARITY = 0.3;

    /** Age limit: only match tickets created in the last N days. */
    private const RECENT_DAYS = 30;

    /**
     * Find potential duplicates for a ticket subject within an organization.
     *
     * @return Collection<int, object> Collection of {id, public_id, subject, status, similarity}
     */
    public static function findDuplicates(string $subject, int $organizationId, ?int $excludeTicketId = null): Collection
    {
        $subject = trim($subject);
        if (mb_strlen($subject) < 5) {
            return collect();
        }

        // Try trigram similarity first (requires pg_trgm extension)
        if (self::hasTrigramExtension()) {
            return self::findByTrigram($subject, $organizationId, $excludeTicketId);
        }

        return self::findByKeyword($subject, $organizationId, $excludeTicketId);
    }

    /**
     * Find potential duplicates for an existing ticket.
     */
    public static function findDuplicatesForTicket(Ticket $ticket): Collection
    {
        return self::findDuplicates($ticket->subject, (int) $ticket->organization_id, $ticket->id);
    }

    /**
     * Trigram-based similarity search (PostgreSQL pg_trgm).
     */
    private static function findByTrigram(string $subject, int $organizationId, ?int $excludeTicketId): Collection
    {
        $query = "
            SELECT id, public_id, subject, status,
                   similarity(subject, ?) AS sim
            FROM tickets
            WHERE organization_id = ?
              AND deleted_at IS NULL
              AND status NOT IN ('closed')
              AND created_at >= ?
              AND subject % ?
        ";
        $bindings = [
            $subject,
            $organizationId,
            now()->subDays(self::RECENT_DAYS),
            $subject,
        ];

        if ($excludeTicketId) {
            $query .= ' AND id != ?';
            $bindings[] = $excludeTicketId;
        }

        $query .= ' ORDER BY sim DESC LIMIT ?';
        $bindings[] = self::MAX_RESULTS;

        $results = collect(DB::select($query, $bindings));

        return $results->filter(fn ($r) => (float) $r->sim >= self::MIN_SIMILARITY);
    }

    /**
     * Keyword-based fallback search (no pg_trgm).
     */
    private static function findByKeyword(string $subject, int $organizationId, ?int $excludeTicketId): Collection
    {
        // Extract significant words (>= 4 chars) for matching
        $words = array_filter(
            preg_split('/[\s\-_:,;.!?()]+/', mb_strtolower($subject)),
            fn ($w) => mb_strlen($w) >= 4
        );

        if (empty($words)) {
            return collect();
        }

        $query = Ticket::query()
            ->select(['id', 'public_id', 'subject', 'status'])
            ->where('organization_id', $organizationId)
            ->whereNull('deleted_at')
            ->where('status', '!=', TicketStatus::Closed)
            ->where('created_at', '>=', now()->subDays(self::RECENT_DAYS));

        if ($excludeTicketId) {
            $query->where('id', '!=', $excludeTicketId);
        }

        // Match tickets containing any significant word
        $query->where(function ($q) use ($words) {
            foreach ($words as $word) {
                $q->orWhere('subject', 'ilike', '%'.$word.'%');
            }
        });

        $results = $query->orderByDesc('created_at')->limit(self::MAX_RESULTS * 2)->get();

        // Score by counting matching words
        $wordCount = count($words);

        return $results->map(function ($ticket) use ($words, $wordCount) {
            $ticketWords = preg_split('/[\s\-_:,;.!?()]+/', mb_strtolower($ticket->subject));
            $matches = count(array_intersect($words, $ticketWords));
            $ticket->similarity = $wordCount > 0 ? round($matches / $wordCount, 2) : 0;

            return $ticket;
        })
            ->filter(fn ($t) => $t->similarity >= self::MIN_SIMILARITY)
            ->sortByDesc('similarity')
            ->take(self::MAX_RESULTS)
            ->values();
    }

    /**
     * Check if PostgreSQL pg_trgm extension is available.
     */
    private static function hasTrigramExtension(): bool
    {
        static $available = null;

        if ($available === null) {
            try {
                $result = DB::selectOne("SELECT count(*) as cnt FROM pg_extension WHERE extname = 'pg_trgm'");
                $available = $result && (int) $result->cnt > 0;
            } catch (\Throwable) {
                $available = false;
            }
        }

        return $available;
    }
}
