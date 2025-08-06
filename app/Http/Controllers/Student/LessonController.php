<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

class LessonController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        dump($user->studentProfiles);

        return view('student.lesson.index');
    }
}
