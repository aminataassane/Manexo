<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\DiscussionThread;
use App\Models\User;
use App\Services\SupportSessionService;

class DiscussionThreadPolicy
{
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
        return true;
    }

    public function view(User $user, DiscussionThread $thread): bool
    {
        return $thread->participants()->where('user_id', $user->id)->exists()
            || (int) $thread->created_by === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return true; // All org members can create discussions
    }

    public function addParticipant(User $user, DiscussionThread $thread): bool
    {
        // Creator or existing participant can add people
        return (int) $thread->created_by === (int) $user->id
            || $thread->participants()->where('user_id', $user->id)->exists();
    }

    public function viewInternalNotes(User $user): bool
    {
        return $user->hasPermission(Permission::DiscussionsViewInternalNotes);
    }
}
