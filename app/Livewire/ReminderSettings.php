<?php

namespace App\Livewire;

use App\Models\Student;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ReminderSettings extends Component
{
    public $is_enabled;
    public $before_lesson_minutes;
    public $homework_reminder_time;
    public $telegram_reminder;

    // Инициализация свойств значениями текущего пользователя
    public function mount(Student $student): void
    {
        $this->telegram_reminder = $student->telegram_reminder;

        $this->is_enabled = $this->telegram_reminder->is_enabled;
        $this->before_lesson_minutes = $this->telegram_reminder->before_lesson_minutes;
        $this->homework_reminder_time = $this->telegram_reminder->homework_reminder_time
            ? \Carbon\Carbon::parse($this->telegram_reminder->homework_reminder_time)->format('H:i')
            : null;
    }

    public function updatedIsEnabled($value): void
    {
        $this->validateOnly('is_enabled');
        $this->telegram_reminder->update(['is_enabled' => $value]);
        Log::debug("updatedIsEnabledReminder {$value}");
    }

    public function updatedBeforeLessonMinutes($value): void
    {
        $this->validateOnly('before_lesson_minutes');
        $this->telegram_reminder->update(['before_lesson_minutes' => $value]);
        Log::debug("updatedBeforeLessonMinutes {$value}");
    }

    public function updatedHomeworkReminderTime($value): void
    {
        $this->validateOnly('homework_reminder_time');
        $this->telegram_reminder->update(['homework_reminder_time' => $value]);
        Log::debug("updatedHomeworkReminderTime {$value}");
    }

    protected function rules(): array
    {
        return [
            'is_enabled' => 'boolean',
            'before_lesson_minutes' => ['required', 'integer', 'min:1', 'max:1440',], // От 0 до 1440 минут (24 часа)
            'homework_reminder_time' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $minTime = '08:00';
                    $min = Carbon::createFromFormat('H:i', $minTime);
                    $input = Carbon::createFromFormat('H:i', $value);

                    if ($input < $min) {
                        $fail("Время должно быть не раньше {$minTime}.");
                    }
                },
            ],
        ];
    }

    public function render()
    {
        return view('livewire.reminder-settings');
    }
}
