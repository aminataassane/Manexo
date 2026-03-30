<?php

namespace App\Traits;

use App\Models\User;

/**
 * Resolves notification delivery channels based on user type.
 *
 * RULE: Internal staff (owner, admin, agent) → in-app only (database).
 *       External users (member, guest, client) → in-app + email.
 *
 * This prevents agents from receiving redundant emails for actions
 * they can already see in the platform.
 */
trait ResolvesNotificationChannels
{
    /**
     * Determine the notification channels based on the recipient's role.
     *
     * @param  object  $notifiable  The user receiving the notification
     * @param  int  $organizationId  The organization context
     * @param  bool  $forceEmail  Override: always include email (e.g., SLA breach alerts)
     * @return string[]
     */
    protected function resolveChannels(object $notifiable, int $organizationId, bool $forceEmail = false): array
    {
        $channels = ['database'];

        if ($forceEmail) {
            $channels[] = 'mail';

            return $channels;
        }

        // Internal staff: in-app only — they work in the platform
        if ($notifiable instanceof User && $notifiable->isInternalStaff($organizationId)) {
            return $channels;
        }

        // External users (clients, guests, members): also receive email
        $channels[] = 'mail';

        return $channels;
    }
}
