<?php

return [
    'status' => [
        'open' => 'Open',
        'in_progress' => 'In progress',
        'pending' => 'Pending',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
    ],
    'status_changed' => ':actor changed status: :old → :new',
    'locked' => 'This ticket is locked. Only administrators can modify it.',
    'locked_banner' => 'This ticket is closed and locked. Only administrators can modify it.',
    'ticket_resolution_strict_error' => 'All subtasks must be completed before resolving this ticket.',

    'client_routing' => [
        'email_subject' => 'Update — assignment',
        'email_heading' => 'Update on your request',
        'client_person_named' => 'Your request has been assigned to :name.',
        'client_person_anonymous' => 'Your request has been assigned to a support agent who will handle it.',
        'client_group' => 'Your request has been assigned to the :name team.',
        'client_function' => 'Your request has been routed to the :name team.',
        'client_multiple' => 'Your request has been assigned to several members of our team.',
        'client_generic' => 'The assignment of your request has been updated.',
    ],

    'assignment_audit' => [
        'assign_user' => ':actor assigned the ticket to :target.',
        'add_assignee' => ':actor added :target to the ticket assignees.',
        'set_responsible' => ':actor set :target as the ticket owner.',
        'remove_assignee' => ':actor removed :target from the ticket assignees.',
        'assign_group' => ':actor assigned the ticket to the :group team.',
        'clear_group' => ':actor removed the ticket from its group.',
        'assign_function' => ':actor routed the ticket to the :name team.',
        'clear_function' => ':actor cleared the business-team routing.',
        'reassign_responsible' => ':actor reassigned the ticket to :target (owner).',
    ],

    'discussion_fab_go_top' => 'Back to top of thread',
    'discussion_fab_go_latest' => 'Jump to latest message',

    'composer_send_in_progress' => 'A send is already in progress. Wait a few seconds and try again.',

    'timeline_user_card_open' => 'View participant information',
    'timeline_badge_creator' => 'Ticket creator',
    'timeline_badge_assignee' => 'Assigned to ticket',
    'timeline_badge_participant' => 'Discussion participant',
];
