<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;

class StudentService
{
    private User $user;

    public function __construct(?User $user = null)
    {
        $this->user = $user ?? auth()->user();
    }

    public function getStudentsDataForView()
    {
        return $this->user->students()
            ->select(['id', 'name', 'class', 'account_id'])
            ->orderBy('name')
            ->with([
                'lesson_times' => function ($query) {
                    $query->select('student_id', 'week_day')
                        ->distinct('week_day');
                }
            ])
            ->get()
            ->map(function (Student $student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'class' => $student->class,
                    'days_with_lessons' => $student->lesson_times
                        ->pluck('week_day')
                        ->unique()
                        ->sort()
                        ->values()
                        ->toArray(),
                ];
            })
            ->groupBy('class')
            ->sortKeys()
            ->toArray();
    }

    public function getClassesDataForVue(): array
    {
        $data = [];
        $data[0] = [
            'title' => 'Не указан',
            'value' => null,
        ];
        foreach (range(1, 11) as $number) {
            $data[] = [
                'title' => $number,
                'value' => $number,
            ];
        }

        return $data;
    }

}