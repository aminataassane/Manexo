<?php

use App\Models\Ticket;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('ticket.{ticketId}', function ($user, $ticketId) {
    $ticket = Ticket::query()->whereKey($ticketId)->first();
    if (! $ticket) {
        return false;
    }
    if ((int) $ticket->created_by === (int) $user->id) {
        return true;
    }
    if ((int) $ticket->assigned_to === (int) $user->id) {
        return true;
    }
    return $user->organizations()->where('organization_id', $ticket->organization_id)->exists();
});

Broadcast::channel('ticket.staff.{ticketId}', function ($user, $ticketId) {
    $ticket = Ticket::query()->whereKey($ticketId)->first();
    if (! $ticket) {
        return false;
    }

    return $user->organizations()
        ->where('organization_id', $ticket->organization_id)
        ->wherePivotIn('role', ['owner', 'admin', 'agent'])
        ->exists();
});

Broadcast::channel('discussion.{threadId}', function ($user, $threadId) {
    $thread = \App\Models\DiscussionThread::query()->whereKey($threadId)->first();
    if (! $thread) {
        return false;
    }

    return $thread->participants()->where('users.id', $user->id)->exists();
});
