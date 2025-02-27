<?php

namespace App\Livewire;

use App\Models\Task;
use Livewire\Component;

class TaskCompleteSwitcher extends Component
{
    public $is_completed = false;

    public $task_id;

    public function mount(bool $is_completed = false): void
    {
        $this->is_completed = $is_completed;
    }

    public function render()
    {
        return view('livewire.task-complete-switcher');
    }

    public function switch($task_id): void
    {
        $task = Task::find($task_id);
        if (auth()->user()->cant('update', $task)) {
            abort(403);
        }

        if ($task->completed_at == null) {
            $task->completed_at = now();
        } else {
            $task->completed_at = null;
        }
        $task->save();
    }
}
