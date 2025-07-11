<?php

namespace App\Http\Controllers;

use App\Http\Requests\LessonTime\StoreLessonTimeRequest;
use App\Models\FreeTime;
use App\Models\Lesson;
use App\Models\LessonTime;
use App\Models\Student;
use Illuminate\Http\Request;

class LessonTimeController extends Controller
{
    public function create(Student $student)
    {
        $lessonTimes = auth()->user()->lessonTimes()->with('student')->get();

        return view('lesson_time.create', compact('student', 'lessonTimes'));
    }

    public function store(StoreLessonTimeRequest $request, Student $student)
    {
        $lesson_time = LessonTime::create([
            'student_id' => $student->id,
            'week_day' => $request->week_day,
            'start' => $request->start,
            'end' => $request->end,
        ]);

        if ($lesson_time) {
            session(['success' => 'Занятие успешно добавлено!']);
        } else {
            session(['error' => 'Ошибка добавления занятия!']);
        }

        return redirect()->route('students.show', $student);
    }

    public function edit(Student $student, LessonTime $lesson_time, Request $request)
    {
        $backUrl = $request->backUrl;
        $students = auth()->user()->students()->get();
        $lessonTimes = auth()->user()->lessonTimes()->with('student')->get()->except($lesson_time->id);

        return view('lesson_time.edit', compact('lesson_time', 'student', 'students', 'backUrl', 'lessonTimes'));
    }

    public function update(StoreLessonTimeRequest $request, Student $student, LessonTime $lesson_time)
    {
        $lesson_time->week_day = $request->week_day;
        $lesson_time->start = $request->start;
        $lesson_time->end = $request->end;

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
        $user = auth()->user();
        if (! ($user->can('update', $student) && $lesson_time->student_id == $student->id)) {
            abort(403);
        }

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
