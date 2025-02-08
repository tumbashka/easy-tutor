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
        $start = fake()->time('H:i', '20:00');
        $end = date('H:i', strtotime($start) + rand(1800, 7200));
        return [
            'student_id' => Student::factory(),
            'week_day' => fake()->numberBetween(0,6),
            'start' => $start,
            'end' => $end,
        ];
    }
}
