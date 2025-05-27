<?php

namespace App\src\Statistic;

use Illuminate\Support\Carbon;

class EarningsTimeStatistic extends Statistic
{
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

    private function monthCalculate(): void
    {
        $days = array_keys($this->inputData);
        $currentMonth = new Carbon('01-01-2000');
        $numbers = [];
        for ($i = 0; $i < count($days); $i++) {
            $startOfMonth = (new Carbon($days[$i]))->startOfMonth();
            if ($currentMonth != $startOfMonth) {
                $currentMonth = $startOfMonth;
                $numbers[$currentMonth->translatedFormat('F Yг.')] = 0;
            }
            $numbers[$currentMonth->translatedFormat('F Yг.')] += $this->inputData[$days[$i]];
        }
        $this->labels = array_keys($numbers);
        $this->numbers = array_values($numbers);
    }

    private function dayCalculate(): void
    {
        $this->labels = array_keys($this->inputData);
        $this->numbers = array_values($this->inputData);
    }
}
