<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Homework\StoreHomeworkRequest;
use App\Http\Requests\Homework\UpdateHomeworkRequest;
use App\Models\Homework;
use App\Models\Student;

class HomeworkController extends Controller
{
    public function create(Student $student)
    {
        return view('teacher.homework.create', compact('student'));
    }

    public function store(StoreHomeworkRequest $request, Student $student)
    {
        $homework = Homework::create([
            'student_id' => $student->id,
            'description' => $request->input('description'),
        ]);

        if ($homework) {
            session(['success' => 'ДЗ успешно добавлено!']);
        } else {
            session(['error' => 'Ошибка добавления ДЗ!']);
        }

        return redirect()->route('students.show', $student);
    }

    public function edit(Student $student, Homework $homework)
    {
        return view('teacher.homework.edit', compact('student', 'homework'));
    }

    public function update(UpdateHomeworkRequest $request, Student $student, Homework $homework)
    {
        $homework->description = $request->input('description');
        if ($homework->update()) {
            session(['success' => 'ДЗ успешно сохранено!']);
        } else {
            session(['error' => 'Ошибка сохранения ДЗ!']);
        }

        return redirect()->route('students.show', $student);
    }

    public function destroy(Student $student, Homework $homework)
    {
        if (auth()->user()->cant('delete', $homework)) {
            abort(403);
        }
        if ($homework->delete()) {
            session(['success' => 'ДЗ успешно удалено!']);
        } else {
            session(['error' => 'Ошибка удаления ДЗ!']);
        }

        return redirect()->back();
    }
}
