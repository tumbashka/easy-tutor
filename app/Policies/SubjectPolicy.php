<?php

namespace App\Policies;

use App\Models\User;

class SubjectPolicy
{
    public function create(User $user): bool
    {
        return $user->is_active_and_verified && $user->is_admin;
    }

    public function update(User $user): bool
    {
        return $user->is_active_and_verified && $user->is_admin;
    }

    public function delete(User $user): bool
    {
        return $user->is_active_and_verified && $user->is_admin;
    }
}
