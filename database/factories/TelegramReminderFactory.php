<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\TelegramReminder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TelegramReminderFactory extends Factory
{
    protected $model = TelegramReminder::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'chat_id' => $this->faker->randomNumber(),
            'is_enabled' => $this->faker->boolean(),
            'before_lesson_minutes' => $this->faker->randomNumber(),
            'homework_reminder_time' => Carbon::now(),

            'student_id' => Student::factory(),
        ];
    }
}
