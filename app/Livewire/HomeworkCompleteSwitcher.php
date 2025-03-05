<?php

namespace App\Livewire;

use App\Models\Homework;
use App\Models\Task;
use Livewire\Component;

class HomeworkCompleteSwitcher extends Component
{
    public $is_completed = false;

    public $homework_id;

    public function mount(bool $is_completed = false): void
    {
        $this->is_completed = $is_completed;
    }

    public function render()
    {
        return view('livewire.homework-complete-switcher');
    }

    public function switch($homework_id): void
    {
        $homework = Homework::find($homework_id);
        if (auth()->user()->cant('update', $homework)) {
            abort(403);
        }

        if ($homework->completed_at == null) {
            $homework->completed_at = now();
            $this->is_completed = true;
        } else {
            $homework->completed_at = null;
            $this->is_completed = false;
        }
        $homework->save();
    }
}
