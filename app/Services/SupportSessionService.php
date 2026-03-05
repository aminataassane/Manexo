<?php

namespace App\Services;

use App\Models\SupportSession;

class SupportSessionService
{
    public function getActive(int $userId): ?SupportSession
    {
        return SupportSession::activeForUser($userId)->first();
    }

    public function start(int $userId, int $orgId, string $reason, int $duration): SupportSession
    {
        // Close any previous active sessions
        SupportSession::where('user_id', $userId)
            ->whereNull('ended_at')
            ->update(['ended_at' => now()]);

        return SupportSession::create([
            'user_id' => $userId,
            'organization_id' => $orgId,
            'reason' => $reason,
            'duration_minutes' => $duration,
            'started_at' => now(),
            'expires_at' => now()->addMinutes($duration),
            'ip_address' => request()->ip() ?? '0.0.0.0',
        ]);
    }

    public function end(SupportSession $session): void
    {
        $session->update(['ended_at' => now()]);
    }

    public function checkExpiry(SupportSession $session): bool
    {
        if ($session->isExpired()) {
            $session->update(['ended_at' => $session->expires_at]);
            return true;
        }

        return false;
    }
}
