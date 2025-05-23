<?php

namespace App\Livewire;

use Livewire\Component;

class ReminderSettings extends Component
{
    public $deadline = '';

    public $reminderBeforeDeadline = false;

    public $reminderBeforeHours = 2;

    public $reminderDaily = false;

    public $reminderDailyTime = '09:00';

    public function mount($task = null)
    {
        if ($task) {
            $this->deadline = $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('Y-m-d H:i') : '';
            $this->reminderBeforeDeadline = (bool) ($task->reminder_before_deadline ?? false);
            $this->reminderBeforeHours = $task->reminder_before_hours ?? 2;
            $this->reminderDaily = (bool) ($task->reminder_daily ?? false);
            $this->reminderDailyTime = $task->reminder_daily_time?->format('H:i') ?? '09:00';
        }
    }

    public function updated($property, $value)
    {
        if ($property === 'deadline') {
            // Форматируем дедлайн при обновлении
            if ($value) {
                try {
                    $this->deadline = \Carbon\Carbon::parse($value)->format('Y-m-d H:i');
                } catch (\Exception $e) {
                    $this->deadline = '';
                }
            } else {
                $this->deadline = '';
                $this->reminderBeforeDeadline = false;
                $this->reminderBeforeHours = 2;
                $this->reminderDaily = false;
                $this->reminderDailyTime = '09:00';
            }
        }
    }

    public function render()
    {
        return view('livewire.reminder-settings');
    }
}
