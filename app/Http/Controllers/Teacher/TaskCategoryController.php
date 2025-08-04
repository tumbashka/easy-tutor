<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TaskCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskCategoryController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $task_categories = $user->taskCategories;

        return view('teacher.task_category.index', compact('task_categories'));
    }

    public function create()
    {
        return view('teacher.task_category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:200', 'min:1', 'unique:App\Models\TaskCategory,name'],
            'color' => ['required', 'hex_color'],
        ], ['name.unique' => 'Такая категория уже существует']);
        $task_category = TaskCategory::create([
            'name' => $request->input('name'),
            'color' => $request->input('color'),
            'user_id' => auth()->user()->id,
        ]);

        if ($task_category) {
            session(['success' => 'Категория успешно создана!']);
        } else {
            session(['error' => 'Ошибка создания категории!']);
        }

        return redirect()->route('task_categories.index');
    }

    public function edit(TaskCategory $task_category)
    {

        return view('teacher.task_category.edit', compact('task_category'));
    }

    public function update(Request $request, TaskCategory $task_category)
    {
        if (auth()->user()->cant('update', $task_category)) {
            abort(403);
        }
        $request->validate([
            'name' => ['required', 'string', 'max:200', 'min:1', Rule::unique('task_categories')->ignore($task_category)],
            'color' => ['required', 'hex_color'],
        ], ['name.unique' => 'Такая категория уже существует']);

        $task_category->name = $request->input('name');
        $task_category->color = $request->input('color');

        if ($task_category->save()) {
            session(['success' => 'Категория успешно изменена!']);
        } else {
            session(['error' => 'Ошибка изменения категории!']);
        }

        return redirect()->route('task_categories.index');
    }

    public function destroy(TaskCategory $task_category)
    {
        if (auth()->user()->cant('delete', $task_category)) {
            abort(403);
        }
        if ($task_category->delete()) {
            session(['success' => 'Категория успешно удалена!']);
        } else {
            session(['error' => 'Ошибка удаления категории!']);
        }

        return redirect()->route('task_categories.index');

    }
}
