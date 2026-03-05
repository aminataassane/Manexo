<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Form;
use App\Models\User;
use App\Services\SupportSessionService;

class FormPolicy
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

    public function create(User $user): bool
    {
        return $user->hasPermission(Permission::FormsManage);
    }

    public function update(User $user, Form $form): bool
    {
        return $user->hasPermission(Permission::FormsManage);
    }

    public function delete(User $user, Form $form): bool
    {
        return $user->hasPermission(Permission::FormsManage);
    }

    public function assign(User $user, Form $form): bool
    {
        return $user->hasPermission(Permission::FormsAssign);
    }

    public function viewResponses(User $user, Form $form): bool
    {
        return $user->hasPermission(Permission::FormsViewResponses);
    }
}
