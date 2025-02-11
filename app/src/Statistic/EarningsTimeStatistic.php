<?php

namespace App\src\Statistic;

use Illuminate\Support\Carbon;

class EarningsTimeStatistic extends Statistic
{

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

    private function month_calculate(): void
    {
        $days = array_keys($this->input_data);
        $current_month = new Carbon('01-01-2000');
        $numbers = [];
        for ($i = 0; $i < count($days); $i++) {
            $start_of_month = (new Carbon($days[$i]))->startOfMonth();
            if ($current_month != $start_of_month) {
                $current_month = $start_of_month;
                $numbers[$current_month->translatedFormat('F Yг.')] = 0;
            }
            $numbers[$current_month->translatedFormat('F Yг.')] += $this->input_data[$days[$i]];
        }
        $this->labels = array_keys($numbers);
        $this->numbers = array_values($numbers);
    }

    private function day_calculate(): void
    {
        $this->labels = array_keys($this->input_data);
        $this->numbers = array_values($this->input_data);
    }
}
