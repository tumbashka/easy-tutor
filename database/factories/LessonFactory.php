<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'student_id' => User::factory(),
            'date' => Carbon::now(),
            'start' => Carbon::now(),
            'end' => Carbon::now(),
            'price' => $this->faker->randomNumber(),
            'is_paid' => $this->faker->boolean(),
            'note' => $this->faker->word(),
        ];
    }
}
