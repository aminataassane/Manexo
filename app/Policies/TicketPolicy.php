<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Ticket;
use App\Models\User;
use App\Services\SupportSessionService;

class TicketPolicy
{
    /**
     * Platform admins with an active support session can do anything.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->canPlatformManage() && session('support_session_id')) {
            $session = (new SupportSessionService)->getActive($user->id);
            if ($session) {
                return true;
            }
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true; // All org members can see the ticket list
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return $ticket->hasDiscussionAccess((int) $user->id);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(Permission::TicketsCreate);
    }

    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->hasPermission(Permission::TicketsEdit)) {
            return true;
        }

        // Creator can edit their own ticket
        return (int) $ticket->created_by === (int) $user->id;
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->hasPermission(Permission::TicketsDelete);
    }

    public function changeStatus(User $user, Ticket $ticket): bool
    {
        if ($user->hasPermission(Permission::TicketsChangeStatus)) {
            return true;
        }

        // Creator can change status of their own ticket
        return (int) $ticket->created_by === (int) $user->id;
    }

    public function assign(User $user, Ticket $ticket): bool
    {
        return $user->hasPermission(Permission::TicketsAssign);
    }

    public function archive(User $user, Ticket $ticket): bool
    {
        return $user->hasPermission(Permission::TicketsArchive);
    }

    public function viewTrash(User $user): bool
    {
        return $user->hasPermission(Permission::TicketsViewTrash);
    }

    public function restore(User $user, Ticket $ticket): bool
    {
        return $user->hasPermission(Permission::TicketsDelete);
    }

    public function forceDelete(User $user, Ticket $ticket): bool
    {
        return $user->hasPermission(Permission::TicketsDelete);
    }

    public function viewInternalNotes(User $user): bool
    {
        return $user->hasPermission(Permission::DiscussionsViewInternalNotes);
    }

    public function writeInternalNotes(User $user): bool
    {
        return $user->hasPermission(Permission::DiscussionsWriteInternalNotes);
    }
}
