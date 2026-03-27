<?php

namespace App\Enums;

enum ApiTokenScope: string
{
    case TicketsRead = 'tickets:read';
    case TicketsWrite = 'tickets:write';
    case CommentsRead = 'comments:read';
    case CommentsWrite = 'comments:write';
    case WebhooksManage = 'webhooks:manage';

    public static function labels(): array
    {
        return [
            self::TicketsRead->value => 'Lire les tickets',
            self::TicketsWrite->value => 'Créer/modifier les tickets',
            self::CommentsRead->value => 'Lire les commentaires',
            self::CommentsWrite->value => 'Écrire des commentaires',
            self::WebhooksManage->value => 'Gérer les webhooks',
        ];
    }
}
