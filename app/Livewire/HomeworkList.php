<?php

namespace App\Livewire;

use App\Models\Homework;
use Livewire\Component;
use Livewire\WithPagination;

class HomeworkList extends Component
{
    use WithPagination;

    public $studentId;

    public function mount($studentId)
    {
        $this->studentId = $studentId;
    }

    public function render()
    {
        $homeworks = Homework::query()
            ->where('student_id', $this->studentId)
            ->orderByCompleted()
            ->paginate(4);

        return view('livewire.homework-list', ['homeworks' => $homeworks]);
    }

    protected $listeners = ['homeworkUpdated' => '$refresh'];
}
