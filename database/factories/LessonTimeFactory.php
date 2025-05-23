<?php

namespace Database\Factories;

use App\Models\LessonTime;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class LessonTimeFactory extends Factory
{
    protected $model = LessonTime::class;

    public function definition(): array
    {
        $startHour = fake()->numberBetween(8, 21);
        $startMinute = fake()->numberBetween(0, 59);
        $start = Carbon::createFromTime($startHour, $startMinute);
        $durationMinutes = fake()->numberBetween(30, 120);
        $end = $start->copy()->addMinutes($durationMinutes);

        $startTime = $start->format('H:i');
        $endTime = $end->format('H:i');

        return [
            'student_id' => Student::factory(),
            'week_day' => fake()->numberBetween(0, 6),
            'start' => $startTime,
            'end' => $endTime,
        ];
    }
}
