<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLessonRequest;
use App\Models\Lesson;
use App\Models\LessonTime;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LessonController extends Controller
{
    public function show(Request $request, $day)
    {
        $day = new Carbon($day);
        $lessons = Lesson::where('date', $day->format('Y-m-d'))->get();

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

    public function update(StoreLessonRequest $request, $day, $lesson)
    {
        $student_name = Student::find($request->student)->name;
        $lesson = Lesson::find($lesson);
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
        return redirect()->route('schedule.show', compact('day'));
    }

    public function change($day, $lesson)
    {
        $lesson = Lesson::find($lesson);
        $lesson->is_canceled = !$lesson->is_canceled;
        $lesson->save();
        return redirect()->back();
    }
}
