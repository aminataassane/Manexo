<?php

namespace App\Enums;

enum WebhookEvent: string
{
    case TicketCreated = 'ticket.created';
    case TicketStatusChanged = 'ticket.status_changed';
    case TicketAssigned = 'ticket.assigned';
    case TicketCommentCreated = 'ticket.comment_created';

    public static function labels(): array
    {
        return [
            self::TicketCreated->value => 'Ticket créé',
            self::TicketStatusChanged->value => 'Statut du ticket modifié',
            self::TicketAssigned->value => 'Ticket assigné',
            self::TicketCommentCreated->value => 'Commentaire ajouté',
        ];
    }
}
