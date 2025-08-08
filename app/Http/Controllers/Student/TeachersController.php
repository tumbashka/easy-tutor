<?php

namespace App\Http\Controllers\Student;

use App\Enums\FreeTimeStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\SearchTeacherRequest;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class TeachersController extends Controller
{
    public function index(SearchTeacherRequest $request)
    {
        $query = User::activeAndVerified()
            ->whereRole(Role::Teacher)
            ->with('subjects', 'freeTimes')
            ->withCount([
                'lessons' => function ($query) {
                    $query->where(function ($query) {
                        $query->where('is_canceled', false)
                            ->whereBeforeToday('date');
                    })->orWhere(function ($query) {
                        $query->where('is_canceled', false)
                            ->whereToday('date')
                            ->whereTime('end', '<=', now());
                    });
                },
                'freeTimes'
            ]);

        if ($request->filled('name')) {
            $query->whereLike('name', "%{$request->get('name')}%");
        }

        if ($request->filled('days')) {
            $query->whereHas('freeTimes', function ($query) use ($request) {
                $query->whereIn('week_day', $request->get('days'));
            });
        }

        if ($request->filled('subjects')) {
            $query->whereHas('subjects', function ($query) use ($request) {
                $query->whereIn('subjects.id', $request->get('subjects'));
            });
        }

        if ($request->filled('sort')) {
            switch ($request->get('sort')) {
                case 'lessons_asc':
                    $query->orderBy('lessons_count');
                    break;
                case 'lessons_desc':
                    $query->orderByDesc('lessons_count');
                    break;
                default:
                    $query->orderByDesc('lessons_count');
            }
        }

        $teachers = $query->paginate(10)->appends(request()->query());

        $subjects = Subject::where('is_active', true)->get();

        $title = __('Преподаватели');

        $sorting = ['lessons_desc', 'lessons_asc'];

        return view('student.teacher.index', compact('teachers', 'subjects', 'title', 'sorting'));
    }
}
