<?php

namespace App\Http\Controllers\Statistic;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\src\Statistic\LessonsTimeStatistic;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class TimeController extends StatisticController
{
    public function period()
    {
        $labels = session()->pull('labels');
        $numbers = session()->pull('numbers');
        $label = session()->pull('label');
        $total = session()->pull('total');
        return view('statistic.time.period', compact('labels', 'numbers', 'label', 'total'));
    }

    public function period_calculate(Request $request)
    {
        $data = $this->getValidatedData($request);
        if (!is_array($data)) {
            return $data;
        }

        $res = Lesson::query()
            ->select('student_name')
            ->selectRaw('FLOOR(SUM(TIME_TO_SEC(TIMEDIFF(`end`, `start`))) / 3600)   as `time`')
            ->whereBetween('date', [$data['start'], $data['end']])
            ->where('date', '<=', now())
            ->where('is_canceled', false)
            ->where('user_id', auth()->user()->id)
            ->groupBy('student_name')
            ->pluck('time', 'student_name')
            ->toArray();

        $labels = array_keys($res);
        $numbers = array_values($res);
        $data['start'] = (new Carbon($data['start']))->format('Y-m-d');
        $data['end'] = (new Carbon($data['end']))->format('Y-m-d');
        $label = "Отработанные часы с {$data['start']} по {$data['end']}";

        $total = array_sum($numbers);

        return redirect()->route('statistic.time.period')->with(compact('labels', 'numbers', 'label', 'total'));
    }
}
