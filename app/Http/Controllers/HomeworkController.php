<?php

namespace App\Http\Controllers;

use App\Models\Homework;
use App\Models\Student;
use Illuminate\Http\Request;

class HomeworkController extends Controller
{
    public function create(Student $student)
    {
        return view('homework.create', compact('student'));
    }

    public function store(Request $request, Student $student)
    {
        \Validator::validate($request->all(),[
                'description' => ['required', 'string', 'max:250'],
            ],[
                'description.required' => 'Краткое описание обязательно для заполнения',
                'description.max' => 'Краткое описание не должно быть длиннее 250 символов',
            ]
        );
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
        return view('homework.edit', compact('student', 'homework'));
    }

    public function update(Request $request, Student $student, Homework $homework)
    {
        \Validator::validate($request->all(),[
            'description' => ['required', 'string', 'max:250'],
        ],[
                'description.required' => 'Краткое описание обязательно для заполнения',
                'description.max' => 'Краткое описание не должно быть длиннее 250 символов',
            ]
        );

        $homework->description = $request->get('description');
        if ($homework->save()) {
            session(['success' => 'ДЗ успешно сохранено!']);
        } else {
            session(['error' => 'Ошибка сохранения ДЗ!']);
        }
        return redirect()->route('students.show', $student);
    }

    public function destroy(Student $student, Homework $homework)
    {
        if (auth()->user()->cant('delete', $homework)){
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
