<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\Lesson\IndexFilterRequest;
use App\Http\Requests\Teacher\Lesson\StoreLessonRequest;
use App\Http\Requests\Teacher\Lesson\UpdateLessonRequest;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Subject;
use App\Services\LessonService;
use App\Services\StatisticService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Inertia\Inertia;

class LessonController extends Controller
{
    public function index(
        IndexFilterRequest $request,
        LessonService $lessonService,
        StatisticService $statisticService
    ) {
        $weekOffset = (int)$request->input('week', 0);
        $weekDTO = $lessonService->getWeekData($weekOffset);
        $weekLessons = $weekDTO->lessonsOnDays->flatten(1);
        $statistics = $statisticService->getLessonsShortStatistic($weekLessons);

//        dd($weekDTO);
        return Inertia::render(
            'Teacher/Schedule/Index',
            array_merge(
                $weekDTO->toArray(),
                compact('statistics')
            )
        );
    }

    public function show(string $day, LessonService $lessonService)
    {
        $day = Carbon::parse($day);
        $lessons = $lessonService->getActualLessonsOnDate($day);

        $occupiedSlots = auth()->user()->lessons()
            ->whereDate('date', $day)
            ->where('is_canceled', false)
            ->with('student')
            ->get(['start', 'end', 'student_id'])
            ->map(function ($lesson) {
                return [
                    'start' => $lesson->start->format('H:i'),
                    'end' => $lesson->end->format('H:i'),
                    'student_name' => $lesson->student ? $lesson->student->name : 'Без имени',
                ];
            });
        $title = 'Занятия на ' . $day->translatedFormat('d F') . " (" . Str::lower(getShortDayName($day))  . '.)'  ;

        return Inertia::render('Teacher/Schedule/Show', compact('title', 'day', 'lessons', 'occupiedSlots'));
    }

    public function create($day)
    {
        $this->authorize('create', Lesson::class);
        $day = Carbon::parse($day);
        $user = auth()->user();

        $students = $user->students()
            ->orderBy('name')
            ->get();
        $subjects = $user->subjects;

        $occupiedSlots = $user->lessons()
            ->whereDate('date', $day)
            ->where('is_canceled', false)
            ->with('student')
            ->get(['start', 'end', 'student_id'])
            ->map(function ($lesson) {
                return [
                    'start' => $lesson->start->format('H:i'),
                    'end' => $lesson->end->format('H:i'),
                    'student_name' => $lesson->student ? $lesson->student->name : 'Без имени',
                ];
            });

        return view('teacher.lesson.create', compact('day', 'students', 'occupiedSlots', 'subjects'));
    }

    public function store(StoreLessonRequest $request, $day)
    {
        $day = new Carbon($day);
        $student_name = Student::find($request->student)->name;
        $user = auth()->user();
        $subject = $user->subjects()->where('subject_id', $request->subject)->first();

        $lesson = Lesson::create([
            'student_id' => $request->student,
            'student_name' => $student_name,
            'user_id' => auth()->user()->id,
            'date' => $day->format('Y-m-d'),
            'start' => $request->start,
            'end' => $request->end,
            'price' => $request->price,
            'note' => $request->note,
            'subject_id' => $subject?->id,
            'subject_name' => $subject?->name,
        ]);

        if ($lesson) {
            session(['success' => 'Занятие успешно добавлено!']);
        } else {
            session(['error' => 'Ошибка добавления занятия!']);
        }

        return redirect()->route('schedule.show', ['day' => $day->format('Y-m-d')]);
    }

    public function edit(Lesson $lesson)
    {
        $this->authorize('update', $lesson);

//        $day = new Carbon($day);
        $user = auth()->user();

        $students = $user->students()
            ->orderBy('name')
            ->get();
        $subjects = $user->subjects;

        $occupiedSlots = auth()->user()->lessons()
            ->whereDate('date', $lesson->date)
            ->where('is_canceled', false)
            ->whereNot('id', $lesson->id)
            ->with('student')
            ->get(['start', 'end', 'student_id'])
            ->map(function ($lesson) {
                return [
                    'start' => $lesson->start->format('H:i'),
                    'end' => $lesson->end->format('H:i'),
                    'student_name' => $lesson->student ? $lesson->student->name : 'Без имени',
                ];
            });

        return Inertia::render('Teacher/Lesson/Edit', compact('students', 'lesson', 'occupiedSlots', 'subjects'));
    }

    public function update(UpdateLessonRequest $request, Lesson $lesson)
    {
        $student = Student::find($request->student);
        $subject = Subject::find($request->subject);

        $lesson->student_id = $request->student;
        $lesson->student_name = $student->name;
        $lesson->start = $request->start;
        $lesson->end = $request->end;
        $lesson->price = $request->price;
        $lesson->note = $request->note;
        $lesson->subject_id = $subject?->id;
        $lesson->subject_name = $subject?->name;

        if ($lesson->update()) {
            session(['success' => 'Занятие успешно сохранено!']);
        } else {
            session(['error' => 'Ошибка изменения занятия!']);
        }
        $week = getWeekOffset($lesson->date);

        return redirect()->route('schedule.index', compact('week'));
    }

    public function change_status(Lesson $lesson)
    {
        $this->authorize('update', $lesson);
        $lesson->is_canceled = ! $lesson->is_canceled;
        $lesson->save();

        return redirect()->back();
    }

    public function set_payment(Lesson $lesson)
    {
        $this->authorize('update', $lesson);
        $lesson->is_paid = true;
        $lesson->save();

        return redirect()->back();
    }

    public function unset_payment(Lesson $lesson)
    {
        $this->authorize('update', $lesson);
        $lesson->is_paid = false;
        $lesson->save();

        return redirect()->back();
    }
}
