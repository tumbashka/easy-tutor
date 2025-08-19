<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\Student\StoreStudentRequest;
use App\Http\Requests\Teacher\Student\UpdateStudentRequest;
use App\Models\Homework;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function index(Request $request, StudentService $service)
    {
        $this->authorize('view-any', Student::class);
        $studentsOnClasses = $service->getStudentsDataForView();

        return Inertia::render('Teacher/Student/Index', compact('studentsOnClasses'));
    }

    public function create(Request $request, StudentService $service)
    {
        $this->authorize('create', Student::class);
        $classesData = $service->getClassesDataForVue();

        return Inertia::render('Teacher/Student/Create', ['classesData' => $classesData, 'free_time' => $request->free_time]);
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
        $this->authorize('view', $student);
        $lesson_times = $student->lesson_times()
            ->orderBy('week_day')
            ->orderBy('start')
            ->get();

//        $reminder = $student->telegram_reminder;
//
//        $homeworks = Homework::query()
//            ->where('student_id', $student->id)
//            ->orderByCompleted()
//            ->paginate(4);

        return Inertia::render('Teacher/Student/Show', compact('student', 'lesson_times'));
    }

    public function edit(Student $student, StudentService $service)
    {
        $this->authorize('update', $student);
        $classesData = $service->getClassesDataForVue();

        return Inertia::render('Teacher/Student/Edit', compact('classesData', 'student'));
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $student->name = $request->name;
        $student->class = $request->class;
        $student->price = $request->price;
        $student->note = $request->note;

        if ($student->update()) {
            session(['success' => 'Ученик успешно обновлён!']);
        } else {
            session(['error' => 'Ошибка обновления ученика!']);
        }

        return redirect()->route('students.show', $student);
    }

    public function destroy(Student $student)
    {
        $this->authorize('delete', $student);
        $student->lessons()
            ->where('date', '>', now())
            ->where('user_id', auth()->user()->id)
            ->delete();

        $student->lessons()
            ->where('date', now()->format('Y-m-d'))
            ->where('start', '>', now()->format('H:i:s'))
            ->where('user_id', auth()->user()->id)
            ->delete();

        if ($student->delete()) {
            session(['success' => 'Ученик успешно удалён!']);
        } else {
            session(['error' => 'Ошибка удаления ученика!']);
        }

        return redirect()->route('students.index');
    }
}
