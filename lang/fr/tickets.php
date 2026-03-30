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

    'client_routing' => [
        'email_subject' => 'Mise à jour — affectation',
        'email_heading' => 'Mise à jour sur votre demande',
        'client_person_named' => 'Votre demande a été assignée à :name.',
        'client_person_anonymous' => 'Votre demande a été assignée à un agent en charge du traitement.',
        'client_group' => 'Votre demande a été assignée à l\'équipe :name.',
        'client_function' => 'Votre demande a été orientée vers l\'équipe :name.',
        'client_multiple' => 'Votre demande a été assignée à plusieurs membres de notre équipe.',
        'client_generic' => 'L\'affectation de votre demande a été mise à jour.',
    ],

    'assignment_audit' => [
        'assign_user' => ':actor a assigné le ticket à :target.',
        'add_assignee' => ':actor a ajouté :target aux personnes assignées au ticket.',
        'set_responsible' => ':actor a désigné :target comme responsable du ticket.',
        'remove_assignee' => ':actor a retiré :target des assignés du ticket.',
        'assign_group' => ':actor a assigné le ticket à l\'équipe :group.',
        'clear_group' => ':actor a retiré le ticket de son équipe (groupe).',
        'assign_function' => ':actor a orienté le ticket vers l\'équipe :name.',
        'clear_function' => ':actor a retiré l\'orientation par équipe métier.',
        'reassign_responsible' => ':actor a réassigné le ticket à :target (responsable).',
    ],

    'discussion_fab_go_top' => 'Remonter en haut du fil',
    'discussion_fab_go_latest' => 'Aller au dernier message',
];
