<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Board;
use App\Models\User;

class BoardPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if(!$user->is_active_and_verified){
            return false;
        }
        if ($user->is_admin) {
            return true;
        }

        return null;
    }

    public function create(User $user): bool
    {
        return $user->role == Role::Teacher;
    }

    public function update(User $user, Board $board): bool
    {
        return $board->user_id == $user->id;
    }

    public function delete(User $user, Board $board): bool
    {
        return $board->user_id == $user->id;
    }

    public function copy(User $user, Board $board): bool
    {
        return $board->user_id == $user->id;
    }
}
