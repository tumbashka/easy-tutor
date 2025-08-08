<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Board;
use App\Models\User;

class BoardPolicy
{
    public function create(User $user): bool
    {
        return $user->is_active && ($user->is_admin || $user->role == Role::Teacher);
    }

    public function update(User $user, Board $board): bool
    {
        return $user->is_active && ($user->is_admin || $board->user_id == $user->id);
    }

    public function delete(User $user, Board $board): bool
    {
        return $user->is_active && ($user->is_admin || $board->user_id == $user->id);
    }

    public function copy(User $user, Board $board): bool
    {
        return $user->is_active && ($user->is_admin || $board->user_id == $user->id);
    }
}
