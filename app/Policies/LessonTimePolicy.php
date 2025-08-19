<?php

namespace App\Policies;

use App\Models\LessonTime;
use App\Models\User;

class LessonTimePolicy
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

    public function show(User $user, LessonTime $lessonTime): bool
    {
        return $user->id == $lessonTime->student->user_id;
    }

    public function create(User $user): bool
    {
        return $user->is_teacher;
    }

    public function update(User $user, LessonTime $lessonTime): bool
    {
        return $user->id == $lessonTime->student->user_id;
    }

    public function delete(User $user, LessonTime $lessonTime): bool
    {
        return $user->id == $lessonTime->student->user_id;
    }
}
