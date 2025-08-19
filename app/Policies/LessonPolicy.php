<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;

class LessonPolicy
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

    public function show(User $user, Lesson $lesson): bool
    {
        return $user->id == $lesson->user_id;
    }

    public function create(User $user): bool
    {
        return $user->is_teacher;
    }

    public function update(User $user, Lesson $lesson): bool
    {
        return $user->id == $lesson->user_id;
    }

    public function delete(User $user, Lesson $lesson): bool
    {
        return $user->id == $lesson->user_id;
    }
}
