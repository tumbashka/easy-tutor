<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lesson\IndexFilterRequest;
use App\Http\Requests\Lesson\StoreLessonRequest;
use App\Http\Requests\Lesson\UpdateLessonRequest;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Subject;
use App\Services\LessonService;
use App\Services\StatisticService;
use Illuminate\Support\Carbon;

class BoardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $subjects = $user->subjects;

        return view('teacher.board.index', compact('subjects'));
    }
}
