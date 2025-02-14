<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLessonTimeRequest;
use App\Http\Requests\UpdateLessonTimeRequest;
use App\Models\FreeTime;
use App\Models\Lesson;
use App\Models\LessonTime;
use App\Models\Student;
use Illuminate\Auth\Access\Response;
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

    public function edit(Student $student, LessonTime $lessonTime, Request $request)
    {
//        dd($student);
        $backUrl = $request->backUrl;
        $students = auth()->user()->students()->get();
//        dd($students);
        return view('lesson_time.edit', compact('lessonTime', 'student', 'students', 'backUrl'));
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


        if ($backUrl = $request->backUrl){
            return redirect($backUrl);
        }

        return redirect()->route('student.show', $student);
    }

    public function delete(Student $student, LessonTime $lessonTime, Request $request)
    {
        $user = auth()->user();
        if(!($user->can('update', $student) && $lessonTime->student_id == $student->id)){
            abort(403);
        }
        $lessons = Lesson::where('date', '>', now())
            ->where('lesson_time_id', $lessonTime->id)
            ->where('user_id', auth()->user()->id)
            ->delete();
        $todayLessons = Lesson::where('date', now()->format('Y-m-d'))
            ->where('start', '>', now()->format('H:i:s'))
            ->where('lesson_time_id', $lessonTime->id)
            ->where('user_id', auth()->user()->id)
            ->delete();

        $free_time = FreeTime::create([
            'week_day' => $lessonTime->week_day,
            'start' => $lessonTime->start,
            'end' => $lessonTime->end,
            'status' => 'free',
            'type' => 'all',
            'user_id' => auth()->user()->id,
        ]);

        if ($lessonTime->delete()) {
            session(['success' => 'Удаление успешно!']);
        } else {
            session(['error' => 'Ошибка удаления!']);
        }
        $backUrl = $request->backUrl;
        if ($backUrl){
            return redirect($backUrl);
        }

        return redirect()->route('student.show', $student);
    }
}
