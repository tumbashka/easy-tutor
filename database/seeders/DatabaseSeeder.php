<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\LessonTime;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('123456789'),
            'email_verified_at' => now(),
            'is_active' => true,
            'is_admin' => true,
        ]);
        $user = User::create([
            'name' => 'Misha',
            'email' => 'tumbashka@gmail.com',
            'password' => Hash::make('123456789'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        Student::factory(15)
            ->create(['user_id' => $user->id])
            ->each(function ($student) {
                LessonTime::factory(fake()->numberBetween(1, 3))
                    ->create(['student_id' => $student->id]);
            });
        $user = User::create([
            'name' => 'Misha2',
            'email' => 'tumbashka@gmail.com2',
            'password' => Hash::make('123456789'),
            'email_verified_at' => now(),
        ]);
        Student::factory(10)
            ->create(['user_id' => $user->id])
            ->each(function ($student) {
                LessonTime::factory(fake()->numberBetween(1, 4))
                    ->create(['student_id' => $student->id]);
            });
    }
}
