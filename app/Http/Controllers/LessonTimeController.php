<?php

namespace App\Http\Controllers;

use App\Events\LessonTime\LessonTimeAdded;
use App\Events\LessonTime\LessonTimeDeleted;
use App\Events\LessonTime\LessonTimeUpdated;
use App\Http\Requests\StoreLessonTimeRequest;
use App\Models\FreeTime;
use App\Models\Lesson;
use App\Models\LessonTime;
use App\Models\Student;
use Illuminate\Http\Request;

class LessonTimeController extends Controller
{
    public function create(Student $student)
    {
        return view('lesson_time.create', compact('student'));
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

        return view('lesson_time.edit', compact('lesson_time', 'student', 'students', 'backUrl'));
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

        $lessons = Lesson::where('date', '>', now())
            ->where('lesson_time_id', $lesson_time->id)
            ->where('user_id', auth()->user()->id)
            ->delete();

        $todayLessons = Lesson::where('date', now()->format('Y-m-d'))
            ->where('start', '>', now()->format('H:i:s'))
            ->where('lesson_time_id', $lesson_time->id)
            ->where('user_id', auth()->user()->id)
            ->delete();

        $free_time = FreeTime::create([
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
        $backUrl = $request->backUrl;
        if ($backUrl) {
            return redirect($backUrl);
        }

        return redirect()->route('students.show', $student);
    }
}
