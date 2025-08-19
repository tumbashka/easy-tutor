<?php

namespace App\Policies;

use App\Models\FreeTime;
use App\Models\User;

class FreeTimePolicy
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

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FreeTime $freeTime): bool
    {
        return $user->id == $freeTime->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FreeTime $freeTime): bool
    {
        return $user->id == $freeTime->user_id;
    }
}
