<?php

namespace App\Livewire;

use App\Models\Lesson;
use Livewire\Component;

class PaymentSwitcher extends Component
{
    public $isPaid = false;

    public $lesson_id;

    public function mount(bool $isPaid = false)
    {
        $this->isPaid = $isPaid;
    }

    public function render()
    {
        return view('livewire.payment-switcher');
    }

    public function switch($lesson_id)
    {
        $lesson = Lesson::find($lesson_id);
        $lesson->is_paid = ! $lesson->is_paid;
        $lesson->save();
    }
}
