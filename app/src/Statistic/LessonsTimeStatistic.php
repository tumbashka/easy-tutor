<?php

namespace App\src\Statistic;

use Illuminate\Support\Carbon;

class LessonsTimeStatistic extends Statistic
{
    private array $allLessons;

    private array $canceledLessons;

    public function __construct(array $allLessons, array $canceledLessons, string $type)
    {
        $this->allLessons = $allLessons;
        $this->canceledLessons = $canceledLessons;
        $this->type = $type;
    }

    public function calculate(): void
    {
        switch ($this->type) {
            case 'day':
                $this->dayCalculate();
                break;
            case 'month':
                $this->monthCalculate();
                break;
        }
    }

    private function dayCalculate(): void
    {
        $this->labels = array_keys($this->allLessons);
        foreach ($this->allLessons as $date => $lessons) {
            if (array_key_exists($date, $this->canceledLessons)) {
                $this->allLessons[$date] -= $this->canceledLessons[$date];
            } else {
                $this->canceledLessons[$date] = 0;
            }
        }
        ksort($this->canceledLessons);
        $this->numbers[] = array_values($this->allLessons);
        $this->numbers[] = array_values($this->canceledLessons);
    }

    private function monthCalculate(): void
    {
        $days = array_keys($this->allLessons);
        $currentMonth = new Carbon('01-01-2000');
        $accepted = [];
        $canceled = [];

        for ($i = 0; $i < count($days); $i++) {
            $startOfMonth = (new Carbon($days[$i]))->startOfMonth();
            if ($currentMonth != $startOfMonth) {
                $currentMonth = $startOfMonth;
                $accepted[$currentMonth->translatedFormat('F Yг.')] = 0;
                $canceled[$currentMonth->translatedFormat('F Yг.')] = 0;
            }
            if (array_key_exists($days[$i], $this->canceledLessons)) {
                $canceled[$currentMonth->translatedFormat('F Yг.')] += $this->canceledLessons[$days[$i]];
            }
            $accepted[$currentMonth->translatedFormat('F Yг.')] += $this->allLessons[$days[$i]];
        }
        foreach ($canceled as $key => $value) {
            $accepted[$key] -= $value;
        }
        $this->labels = array_keys($accepted);
        $this->numbers[] = array_values($accepted);
        $this->numbers[] = array_values($canceled);
    }
}
