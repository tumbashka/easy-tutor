<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\TaskIndexRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(TaskIndexRequest $request)
    {
        $user = $request->user();
        $task_categories = $user->taskCategories;
        $task_category = $request->input('task_category');

        $category = $task_categories
            ->firstWhere('id', $task_category);

        $tasks = $user->tasks()
            ->whereCategory($category)
            ->sortByActuality()
            ->paginate()
            ->appends(compact('task_category'));

        return view('tasks.index', compact('task_categories', 'category', 'tasks'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $task_categories = $user->taskCategories;

        $students_on_classes = $user->studentsOnClasses();

        return view('tasks.create', compact('task_categories', 'students_on_classes'));
    }

    public function store(StoreTaskRequest $request)
    {
        $task = Task::create([
            'user_id' => auth()->user()->id,
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'deadline' => $request->validated('deadline'),
            'reminder_before_deadline' => $request->validated('reminderBeforeDeadline', false),
            'reminder_before_hours' => $request->validated('reminderBeforeHours'),
            'reminder_daily' => $request->validated('reminderDaily', false),
            'reminder_daily_time' => $request->validated('reminderDailyTime'),
        ]);
        if ($task) {
            session(['success' => 'Задача успешно создана!']);
            $task->task_categories()->attach($request['categories']);
            $task->students()->attach($request['students']);
        } else {
            session(['error' => 'Ошибка создания задачи!']);
        }

        return redirect()->route('tasks.index');
    }

    public function show(Task $task)
    {
        $task->load(['task_categories', 'students']);

        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $user = auth()->user();
        $task_categories = $user->taskCategories;
        $students_on_classes = $user->studentsOnClasses();

        $task->load(['task_categories', 'students']);

        return view('tasks.edit', compact('task', 'task_categories', 'students_on_classes'));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->title = $request->input('title');
        $task->description = $request->input('description');
        $task->deadline = $request->input('deadline');
        $task->reminder_before_deadline = $request->input('reminderBeforeDeadline', false);
        $task->reminder_daily = $request->input('reminderDaily', false);
        $task->reminder_before_hours = $request->input('reminderBeforeHours');
        $task->reminder_daily_time = $request->input('reminderDailyTime');

        if ($task->save()) {
            $task->task_categories()->detach();
            $task->students()->detach();

            $task->task_categories()->attach($request['categories']);
            $task->students()->attach($request['students']);
            session(['success' => 'Задача успешно изменена!']);
        } else {
            session(['error' => 'Ошибка изменения задачи!']);
        }

        return redirect()->route('tasks.index');
    }

    public function destroy(Task $task)
    {
        if (auth()->user()->cant('delete', $task)) {
            abort(403);
        }
        if ($task->delete()) {
            session(['success' => 'Задача успешно удалена!']);
        } else {
            session(['error' => 'Ошибка удаления задачи!']);
        }

        return redirect()->route('tasks.index');
    }

    public function change_completed(Task $task)
    {
        if (auth()->user()->cant('update', $task)) {
            abort(403);
        }

        if ($task->completed_at == null) {
            $task->completed_at = now();
        } else {
            $task->completed_at = null;
        }
        $task->save();

        return back();
    }

    public function delete_completed(Request $request)
    {
        $request->user()
            ->tasks()
            ->whereNotNull('completed_at')
            ->delete();

        return back();
    }
}
