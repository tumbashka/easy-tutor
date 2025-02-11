<?php

namespace App\src\Statistic;

use App\src\Statistic\Statistic;
use Illuminate\Support\Carbon;

class LessonsTimeStatistic extends Statistic
{
    private array $all_lessons;
    private array $canceled_lessons;

    public function __construct(array $all_lessons, array $canceled_lessons, string $type)
    {
        $this->all_lessons = $all_lessons;
        $this->canceled_lessons = $canceled_lessons;
        $this->type = $type;
    }

    public function calculate(): void
    {
        switch ($this->type) {
            case 'day':
                $this->day_calculate();
                break;
            case 'month':
                $this->month_calculate();
                break;
        }
    }

    private function day_calculate(): void
    {
        $this->labels = array_keys($this->all_lessons);
        foreach ($this->all_lessons as $date => $lessons) {
            if (array_key_exists($date, $this->canceled_lessons)) {
                $this->all_lessons[$date] -= $this->canceled_lessons[$date];
            } else {
                $this->canceled_lessons[$date] = 0;
            }
        }
        ksort($this->canceled_lessons);
        $this->numbers[] = array_values($this->all_lessons);
        $this->numbers[] = array_values($this->canceled_lessons);
    }

    private function month_calculate(): void
    {
        $days = array_keys($this->all_lessons);
        $current_month = new Carbon('01-01-2000');
        $accepted = [];
        $canceled = [];
//        dd($this->all_lessons, $this->canceled_lessons);

        for ($i = 0; $i < count($days); $i++) {
            $start_of_month = (new Carbon($days[$i]))->startOfMonth();
            if ($current_month != $start_of_month) {
                $current_month = $start_of_month;
                $accepted[$current_month->translatedFormat('F Yг.')] = 0;
                $canceled[$current_month->translatedFormat('F Yг.')] = 0;
            }
            if (array_key_exists($days[$i], $this->canceled_lessons)) {
                $canceled[$current_month->translatedFormat('F Yг.')] += $this->canceled_lessons[$days[$i]];
            }
            $accepted[$current_month->translatedFormat('F Yг.')] += $this->all_lessons[$days[$i]];
        }
        foreach ($canceled as $key => $value) {
            $accepted[$key] -= $value;
        }
        $this->labels = array_keys($accepted);
        $this->numbers[] = array_values($accepted);
        $this->numbers[] = array_values($canceled);
    }
}
