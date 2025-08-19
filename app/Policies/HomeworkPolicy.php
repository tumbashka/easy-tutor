<?php

namespace App\Policies;

use App\Models\Homework;
use App\Models\User;

class HomeworkPolicy
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
    public function update(User $user, Homework $homework): bool
    {
        $user_id = $homework->student->user->id;

        return $user->id == $user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Homework $homework): bool
    {
        $user_id = $homework->student->user->id;

        return $user->id == $user_id;
    }
}
