<?php

use App\Models\Ticket;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('ticket.{ticketPublicId}', function ($user, $ticketPublicId) {
    $ticket = Ticket::query()->where('public_id', $ticketPublicId)->first();
    if (! $ticket) {
        return false;
    }
    return $ticket->hasDiscussionAccess((int) $user->id);
});

Broadcast::channel('ticket.staff.{ticketPublicId}', function ($user, $ticketPublicId) {
    $ticket = Ticket::query()->where('public_id', $ticketPublicId)->first();
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
