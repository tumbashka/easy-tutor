<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LessonPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) {
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lesson $lesson): bool
    {
        return $user->id == $lesson->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lesson $lesson): bool
    {
        return $user->id == $lesson->user_id;
    }

}
