<?php

namespace Database\Factories;

use App\Models\Homework;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class HomeworkFactory extends Factory
{
    protected $model = Homework::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'description' => $this->faker->text(),
            'completed_at' => Carbon::now(),

            'student_id' => Student::factory(),
        ];
    }
}
