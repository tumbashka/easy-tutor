<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLessonTimeRequest;
use App\Http\Requests\UpdateLessonTimeRequest;
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
        $lessonTime = LessonTime::create([
            'student_id' => $student->id,
            'week_day' => $request->week_day,
            'start' => $request->start,
            'end' => $request->end,
        ]);

        if ($lessonTime) {
            Student::updateLessonsPriceOnStudentChanges($student);
            session(['success' => 'Занятие успешно добавлено!']);
        } else {
            session(['error' => 'Ошибка добавления занятия!']);
        }

        return redirect()->route('student.show', $student);
    }

    public function edit(Student $student, LessonTime $lessonTime)
    {
//        dd($student);
        $students = auth()->user()->students()->get();
//        dd($students);
        return view('lesson_time.edit', compact('lessonTime', 'student', 'students'));
    }

    public function update(StoreLessonTimeRequest $request, Student $student, LessonTime $lessonTime)
    {
        $lessonTime->week_day = $request->week_day;
        $lessonTime->start = $request->start;
        $lessonTime->end = $request->end;

        if ($lessonTime->save()) {
            Student::updateLessonTimeOnLessonTimeChanges($student, $lessonTime);
            Student::updateLessonsPriceOnStudentChanges($student);
            session(['success' => 'Обновление успешно!']);
        } else {
            session(['error' => 'Ошибка обновления!']);
        }

        return redirect()->route('student.show', $student);
    }

    public function delete(Student $student, LessonTime $lessonTime)
    {
        $lessons = Lesson::where('date', '>', now())
            ->where('lesson_time_id', $lessonTime->id)
            ->where('user_id', auth()->user()->id)
            ->delete();
        $todayLessons = Lesson::where('date', now()->format('Y-m-d'))
            ->where('start', '>', now()->format('H:i:s'))
            ->where('lesson_time_id', $lessonTime->id)
            ->where('user_id', auth()->user()->id)
            ->delete();
        if ($lessonTime->delete()) {
            session(['success' => 'Удаление успешно!']);
        } else {
            session(['error' => 'Ошибка удаления!']);
        }

        return redirect()->route('student.show', $student);
    }
}
