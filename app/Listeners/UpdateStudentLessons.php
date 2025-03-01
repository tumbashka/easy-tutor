<?php

namespace App\Listeners;

use App\Events\Student\StudentUpdated;

class UpdateStudentLessons
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StudentUpdated $event): void
    {
        $student = $event->student;
        $student->updateLessons();
    }
}
