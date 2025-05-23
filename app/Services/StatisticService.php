<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class StatisticService
{
    public function getLessonsShortStatistic($lessons): array
    {
        $statistics = [
            'conductedLessons' => 0,
            'toConductLessons' => 0,
            'ongoingLessons' => 0,
            'canceledLessons' => 0,
            'earned' => 0,
            'canceledMoneys' => 0,
            'totalPossibleEarnings' => 0,
            'hoursConducted' => 0,
            'hoursToConduct' => 0,
        ];

        foreach ($lessons as $lesson) {
            $lessonDate = Carbon::parse($lesson->date)->format('Y-m-d');
            $startTime = $lesson->start->format('H:i:s');
            $endTime = $lesson->end->format('H:i:s');

            $lessonStart = Carbon::parse("{$lessonDate} {$startTime}");
            $lessonEnd = Carbon::parse("{$lessonDate} {$endTime}");

            if ($lessonEnd < $lessonStart) {
                $lessonEnd->addDay();
            }

            $duration = abs($lessonEnd->diffInMinutes($lessonStart)) / 60;
            $now = now();

            if ($lesson->is_canceled) {
                $statistics['canceledLessons']++;
                $statistics['canceledMoneys'] += $lesson->price;
            } else {
                $statistics['totalPossibleEarnings'] += $lesson->price;
                if ($lesson->is_paid) {
                    $statistics['earned'] += $lesson->price;
                }
                if ($now > $lessonEnd) {
                    $statistics['conductedLessons']++;
                    $statistics['hoursConducted'] += $duration;
                } elseif ($now >= $lessonStart && $now < $lessonEnd) {
                    $statistics['ongoingLessons']++;
                } else {
                    $statistics['toConductLessons']++;
                    $statistics['hoursToConduct'] += $duration;
                }
            }
        }

        return $statistics;
    }
}
