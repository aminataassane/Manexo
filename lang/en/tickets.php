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
];
