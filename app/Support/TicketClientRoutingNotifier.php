<?php

namespace App\Support;

use App\DataTransferObjects\ClientAssignmentContext;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketClientRoutingNotification;

/**
 * Notifie le créateur et les participants externes lors d’un changement d’affectation.
 */
final class TicketClientRoutingNotifier
{
    public static function notify(Ticket $ticket, ClientAssignmentContext $context): void
    {
        $ticket->loadMissing(['creator', 'participants']);
        $orgId = (int) $ticket->organization_id;
        $seen = [];

        $recipients = collect();
        if ($ticket->creator) {
            $recipients->push($ticket->creator);
        }
        foreach ($ticket->participants as $participant) {
            $recipients->push($participant);
        }

        foreach ($recipients->unique('id') as $user) {
            if (! $user instanceof User) {
                continue;
            }
            if (isset($seen[$user->id])) {
                continue;
            }
            if ($user->isInternalStaff($orgId)) {
                continue;
            }
            $seen[$user->id] = true;
            $user->notify(new TicketClientRoutingNotification((int) $ticket->id, $orgId, $context));
        }
    }
}
