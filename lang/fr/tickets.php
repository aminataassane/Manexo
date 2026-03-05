<?php

return [
    'status' => [
        'open' => 'Ouvert',
        'in_progress' => 'En cours',
        'pending' => 'En attente',
        'resolved' => 'Résolu',
        'closed' => 'Fermé',
    ],
    'status_changed' => ':actor a changé le statut : :old → :new',
    'locked' => 'Ce ticket est verrouillé. Seuls les administrateurs peuvent le modifier.',
    'locked_banner' => 'Ce ticket est fermé et verrouillé. Seuls les administrateurs peuvent le modifier.',
    'ticket_resolution_strict_error' => 'Toutes les sous-tâches doivent être terminées avant de résoudre ce ticket.',
];
