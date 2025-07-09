<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $user, User $target_user): bool
    {
        return $user->is_admin && $user->id != $target_user->id && $target_user->id != 1;
    }

    public function delete(User $user, User $target_user): bool
    {
        return $user->is_admin && $user->id != $target_user->id && $target_user->id != 1;
    }
}
