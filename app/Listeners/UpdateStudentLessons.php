<?php

namespace App\Listeners;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class UpdateStudentLessons
{
    public function handle(Model $model): void
    {
        Log::info('Update Student Lessons');
        if ($model instanceof \App\Models\Student) {
            $student = $model;
        } else {
            return;
        }

        Log::info("обновление занятий у ученика с id: {$student->id}");
        $student->updateLessons();
    }
}
