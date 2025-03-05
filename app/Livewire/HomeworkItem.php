<?php

namespace App\Livewire;

use App\Models\Homework;
use Livewire\Component;

class HomeworkItem extends Component
{
    public $homework;
    public $is_completed;

    public function mount(Homework $homework)
    {
        $this->homework = $homework;
        $this->is_completed = (bool) $homework->completed_at;
    }

    public function toggleComplete()
    {
        if (auth()->user()->cant('update', $this->homework)) {
            abort(403);
        }

        $this->homework->completed_at = $this->is_completed ? null : now();
        $this->homework->save();

        $this->is_completed = !$this->is_completed;

        // Отправляем событие для обновления списка
        $this->dispatch('homeworkUpdated');
    }

    public function render()
    {
        return view('livewire.homework-item');
    }
}
