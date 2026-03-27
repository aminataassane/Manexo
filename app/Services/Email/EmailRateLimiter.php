<?php

namespace App\Services\Email;

use Illuminate\Support\Facades\Cache;

class EmailRateLimiter
{
    /** Max tickets per hour per email address (unknown senders only). */
    private const MAX_PER_HOUR = 5;

    /** Max tickets per day per domain (unknown senders only). */
    private const MAX_PER_DAY_DOMAIN = 20;

    public static function isRateLimited(string $email, int $orgId): bool
    {
        $email = strtolower(trim($email));
        $domain = substr($email, strrpos($email, '@') + 1);

        $hourKey = "email_rl:h:{$orgId}:{$email}";
        $dayKey = "email_rl:d:{$orgId}:{$domain}";

        $hourCount = (int) Cache::get($hourKey, 0);
        $dayCount = (int) Cache::get($dayKey, 0);

        return $hourCount >= self::MAX_PER_HOUR || $dayCount >= self::MAX_PER_DAY_DOMAIN;
    }

    public static function recordTicketCreation(string $email, int $orgId): void
    {
        $email = strtolower(trim($email));
        $domain = substr($email, strrpos($email, '@') + 1);

        $hourKey = "email_rl:h:{$orgId}:{$email}";
        $dayKey = "email_rl:d:{$orgId}:{$domain}";

        Cache::increment($hourKey);
        Cache::put($hourKey, (int) Cache::get($hourKey, 1), now()->addHour());

        Cache::increment($dayKey);
        Cache::put($dayKey, (int) Cache::get($dayKey, 1), now()->addDay());
    }
}
