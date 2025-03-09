<?php

namespace App\Http\Controllers;

use App\Events\Lesson\LessonAdded;
use App\Events\Lesson\LessonUpdated;
use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Models\Lesson;
use App\Models\Student;
use App\Repositories\LessonRepository;
use App\src\Schedule\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LessonController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'week' => ['nullable', 'integer'],
        ]);
        $weekOffset = (int)$request->week;
        $weekDays = getWeekDays($weekOffset); // ['0-6' => Carbon obj]
        $previous = getPreviousWeeks($weekOffset, 10);
        $next = getNextWeeks($weekOffset, 10);

        $user = auth()->user();
        $schedule = new Schedule($user);
        $lessonsOnDays = $schedule->getWeekLessonsOnDays($weekDays);

        return view('schedule.index', compact('weekOffset', 'weekDays', 'previous', 'next', 'lessonsOnDays'));
    }

    public function show(Request $request, $day)
    {
        $day = new Carbon($day);
        $lessons = Lesson::where('date', $day->format('Y-m-d'))
            ->where('user_id', auth()->user()->id)
            ->get();
        $arr = [];
        foreach ($lessons as $lesson) {
            $arr[] = $lesson;
        }
        $lessons = $arr;
        usort($lessons, function ($a, $b) {
            return $a['start'] <=> $b['start']; // Сортировка по времени
        });

        return view('schedule.show', compact('day', 'lessons'));
    }

    public function create($day)
    {
        $day = new Carbon($day);
        $students = Student::where('user_id', auth()->id())->get();

        return view('lesson.create', compact('day', 'students'));
    }

    public function store(StoreLessonRequest $request, $day)
    {
        $day = new Carbon($day);
        $student_name = Student::find($request->student)->name;

        $lesson = Lesson::create([
            'student_id' => $request->student,
            'student_name' => $student_name,
            'user_id' => auth()->user()->id,
            'date' => $day->format('Y-m-d'),
            'start' => $request->start,
            'end' => $request->end,
            'price' => $request->price,
            'note' => $request->note,
        ]);

        if ($lesson) {
            session(['success' => 'Занятие успешно добавлено!']);
        } else {
            session(['error' => 'Ошибка добавления занятия!']);
        }

        return redirect()->route('schedule.show', ['day' => $day->format('Y-m-d')]);
    }

    public function edit($day, $lesson)
    {
        $day = new Carbon($day);
        $lesson = Lesson::with('student')->find($lesson);
        $students = Student::where('user_id', auth()->id())->get();

        return view('lesson.edit', compact('day', 'students', 'lesson'));
    }

    public function update(UpdateLessonRequest $request, $day, Lesson $lesson)
    {
        $student_name = Student::find($request->student)->name;

        $lesson->student_id = $request->student;
        $lesson->student_name = $student_name;
        $lesson->start = $request->start;
        $lesson->end = $request->end;
        $lesson->price = $request->price;
        $lesson->note = $request->note;

        if ($lesson->update()) {
            session(['success' => 'Занятие успешно сохранено!']);
        } else {
            session(['error' => 'Ошибка изменения занятия!']);
        }
        $week = getWeekOffset(new Carbon($day));

        return redirect()->route('schedule.index', compact('week'));
    }

    public function change_status($day, $lesson)
    {
        $lesson = Lesson::find($lesson);
        $lesson->is_canceled = !$lesson->is_canceled;
        $lesson->save();
        return redirect()->back();
    }
}
