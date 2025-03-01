<?php

namespace App\Http\Controllers;

use App\Events\Student\StudentDeleted;
use App\Events\Student\StudentUpdated;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\LessonTime;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $classesUnique = $user->students()
            ->distinct('class')
            ->pluck('class')
            ->toArray();

        sort($classesUnique);

        $studentsOnClasses = [];

        foreach ($classesUnique as $class) {
            $students = $user->students()
                ->where('class', $class)
                ->with('lesson_times')->get()->toArray();
            $studentsOnClasses[$class] = $students;
        }

        return view('student.index', compact('studentsOnClasses'));
    }

    public function create(Request $request)
    {
        return view('student.create', ['free_time' => $request->free_time]);
    }

    public function store(StoreStudentRequest $request)
    {
        $student = Student::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'class' => $request->class,
            'note' => $request->note,
            'price' => $request->price,
        ]);
        if ($student) {
            session(['success' => 'Ученик успешно добавлен!']);
        } else {
            session(['error' => 'Ошибка добавления ученика!']);
        }

        if ($request->free_time) {
            return redirect()->route('free-time.set-student', ['free_time' => $request->free_time]);
        }

        return redirect()->route('students.show', $student);
    }

    public function show(Student $student)
    {
        $lesson_times = $student->lesson_times->sortBy(function ($lesson_time){
            return [$lesson_time->week_day, $lesson_time->start];
        });
        return view('student.show', compact('student', 'lesson_times'));
    }

    public function edit(Student $student)
    {
        return view('student.edit', compact('student'));
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $student->name = $request->name;
        $student->class = $request->class;
        $student->price = $request->price;
        $student->note = $request->note;

        if ($student->save()) {
            StudentUpdated::dispatch($student);
            session(['success' => 'Ученик успешно обновлён!']);
        } else {
            session(['error' => 'Ошибка обновления ученика!']);
        }

        return redirect()->route('students.show', $student);
    }

    public function destroy(Student $student)
    {
        $lessons = $student->lessons()
            ->where('date', '>', now())
            ->where('user_id', auth()->user()->id)
            ->delete();

        $todayLessons = $student->lessons()
            ->where('date', now()->format('Y-m-d'))
            ->where('start', '>', now()->format('H:i:s'))
            ->where('user_id', auth()->user()->id)
            ->delete();

        if ($student->delete()) {
            StudentDeleted::dispatch($student);
            session(['success' => 'Ученик успешно удалён!']);
        } else {
            session(['error' => 'Ошибка удаления ученика!']);
        }

        return redirect()->route('students.index');
    }
}
