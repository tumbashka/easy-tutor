<?php

namespace App\Http\Controllers;

use App\Http\Requests\Lesson\IndexFilterRequest;
use App\Http\Requests\Lesson\StoreLessonRequest;
use App\Http\Requests\Lesson\UpdateLessonRequest;
use App\Models\Lesson;
use App\Models\Student;
use App\Services\ScheduleService;
use App\Services\StatisticService;
use Illuminate\Support\Carbon;

class LessonController extends Controller
{
    public function index(
        IndexFilterRequest $request,
        ScheduleService $scheduleService,
        StatisticService $statisticService
    ) {
        $weekOffset = $request->input('week', 0);
        $weekDTO = $scheduleService->getWeekData($weekOffset);

        $weekLessons = $weekDTO->lessonsOnDays->flatten(1);
        $statistics = $statisticService->getLessonsShortStatistic($weekLessons);

        return view('schedule.index', array_merge($weekDTO->toArray(), compact('statistics')));
    }

    public function show(string $day, ScheduleService $scheduleService)
    {
        $dayCarbon = Carbon::parse($day);
        $lessons = $scheduleService->getActualLessonsOnDate($dayCarbon);

        return view('schedule.show', compact('dayCarbon', 'lessons'));
    }

    public function create($day)
    {
        $day = Carbon::parse($day);
        $user = auth()->user();
        $students = $user->students()
            ->orderBy('name')
            ->get();

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

    public function edit($day, Lesson $lesson)
    {
        $day = new Carbon($day);
        $students = auth()
            ->user()
            ->students()
            ->orderBy('name')
            ->get();

        return view('lesson.edit', compact('day', 'students', 'lesson'));
    }

    public function update(UpdateLessonRequest $request, $day, Lesson $lesson)
    {
        $student = Student::find($request->student);

        $lesson->student_id = $request->student;
        $lesson->student_name = $student->name;
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

    public function change_status($day, Lesson $lesson)
    {
        $day = Carbon::parse($day);
        $lesson->is_canceled = ! $lesson->is_canceled;
        $lesson->save();

        return redirect()->route('schedule.show', ['day' => $day->format('Y-m-d')]);
    }
}
