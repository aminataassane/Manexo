<?php

namespace App\Support;

use App\Models\Ticket;

/**
 * Badges affichés sur la fiche auteur d’un message de ticket (timeline).
 */
final class TicketMessageUserCard
{
    /**
     * @param  array<string, string>  $roleLabels
     * @return list<string>
     */
    public static function badgesForTicketUser(Ticket $ticket, int $userId, ?string $orgRole, array $roleLabels): array
    {
        if ($userId <= 0) {
            return [];
        }

        $badges = [];

        if ($userId === (int) $ticket->created_by) {
            $badges[] = __('tickets.timeline_badge_creator');
        } elseif ($ticket->assignees->contains('id', $userId)) {
            $badges[] = __('tickets.timeline_badge_assignee');
        } elseif ($ticket->participants->contains('id', $userId)) {
            $badges[] = __('tickets.timeline_badge_participant');
        }

        if ($orgRole && $orgRole !== 'member') {
            $badges[] = $roleLabels[$orgRole] ?? $orgRole;
        }

        return $badges;
    }
}
