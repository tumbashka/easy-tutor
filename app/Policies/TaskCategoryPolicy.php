<?php

namespace App\Policies;

use App\Models\FreeTime;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\TaskCategory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskCategoryPolicy
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
    public function update(User $user, TaskCategory $task_category): bool
    {
        return $user->id == $task_category->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaskCategory $task_category): bool
    {
        return $user->id == $task_category->user_id;
    }

}
