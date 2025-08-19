<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\LessonTime\StoreLessonTimeRequest;
use App\Models\FreeTime;
use App\Models\Lesson;
use App\Models\LessonTime;
use App\Models\Student;
use App\Services\SubjectsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LessonTimeController extends Controller
{
    public function create(Student $student)
    {
        $this->authorize('create', LessonTime::class);
        $user = auth()->user();
        $lessonTimes = $user->lessonTimes()->with('student')->get();
        $subjects = $user->subjects;

        return Inertia::render('Teacher/LessonTime/Create', compact('student', 'lessonTimes', 'subjects'));
    }

    public function store(StoreLessonTimeRequest $request, Student $student)
    {
        $lesson_time = LessonTime::create([
            'student_id' => $student->id,
            'week_day' => $request->week_day,
            'start' => $request->start,
            'end' => $request->end,
            'subject_id' => $request->subject,
        ]);

        if ($lesson_time) {
            session(['success' => 'Занятие успешно добавлено!']);
        } else {
            session(['error' => 'Ошибка добавления занятия!']);
        }

        return redirect()->route('students.show', $student);
    }

    public function edit(Student $student, LessonTime $lessonTime, Request $request)
    {
        $this->authorize('update', $lessonTime);

        $user = auth()->user();
        $students = $user->students;
        $lessonTimes = $user->lessonTimes()->with('student')->get()->except($lessonTime->id);
        $subjects = $user->subjects;
        $defaultSubject = $user->default_subject;
        $title = 'Редактирование времени занятия';

        return Inertia::render('Teacher/LessonTime/Edit', compact('title','lessonTime', 'student', 'students', 'lessonTimes', 'subjects', 'defaultSubject'));
    }

    public function update(StoreLessonTimeRequest $request, Student $student, LessonTime $lesson_time)
    {
        $lesson_time->week_day = $request->week_day;
        $lesson_time->start = $request->start;
        $lesson_time->end = $request->end;
        $lesson_time->subject_id = $request->subject;

        if ($lesson_time->save()) {
            session(['success' => 'Обновление успешно!']);
        } else {
            session(['error' => 'Ошибка обновления!']);
        }

        if ($backUrl = $request->backUrl) {
            return redirect($backUrl);
        }

        return redirect()->route('students.show', $student);
    }

    public function destroy(Student $student, LessonTime $lesson_time, Request $request)
    {
        $this->authorize('delete', $lesson_time);

        Lesson::where('date', '>', now())
            ->where('lesson_time_id', $lesson_time->id)
            ->where('user_id', auth()->user()->id)
            ->delete();

        Lesson::where('date', now()->format('Y-m-d'))
            ->where('start', '>', now()->format('H:i:s'))
            ->where('lesson_time_id', $lesson_time->id)
            ->where('user_id', auth()->user()->id)
            ->delete();

        FreeTime::create([
            'week_day' => $lesson_time->week_day,
            'start' => $lesson_time->start,
            'end' => $lesson_time->end,
            'status' => 'free',
            'type' => 'all',
            'user_id' => auth()->user()->id,
        ]);

        if ($lesson_time->delete()) {
            session(['success' => 'Удаление успешно!']);
        } else {
            session(['error' => 'Ошибка удаления!']);
        }

        if ($backUrl = $request->backUrl) {
            return redirect($backUrl);
        }

        return redirect()->route('students.show', $student);
    }
}
