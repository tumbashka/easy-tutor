<?php

namespace App\Http\Controllers\Statistic;

use App\Models\Lesson;
use App\src\Statistic\EarningsTimeStatistic;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EarningsController extends StatisticController
{
    public function period()
    {
        $labels = session()->pull('labels');
        $numbers = session()->pull('numbers');
        return view('statistic.earnings.period', compact('labels', 'numbers'));
    }

    public function period_calculate(Request $request)
    {
        $data = $this->getValidatedData($request);
        if(!is_array($data)){
            return $data;
        }

        $res = Lesson::query()
            ->selectRaw('date, SUM(price) as date_price')
            ->where('is_paid', true)
            ->where('date', '>=', $data['start'])
            ->where('date', '<=', $data['end'])
            ->where('user_id', auth()->user()->id)
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('date_price', 'date')
            ->toArray();

        $statistic = new EarningsTimeStatistic($res, $data['type']);
        $statistic->calculate();
        $labels = $statistic->get_labels();
        $numbers = $statistic->get_numbers();

        return redirect()->route('statistic.earnings.period')->with(compact('labels', 'numbers'));
    }

    public function students()
    {
        $labels = session()->pull('labels');
        $numbers = session()->pull('numbers');
        $colors = session()->pull('colors');
        return view('statistic.earnings.students', compact('labels', 'numbers', 'colors'));
    }

    public function students_calculate(Request $request)
    {
        Validator::make($request->all(), [
            'type' => ['required', 'string'],
        ])->validate();
        if ($request->type != 'all' && $request->type != 'month') {
            return redirect()->back()->withErrors(['type_not_found' => 'Ошибка расчета статистики'])->withInput();
        }
        $res = [];
        if ($request->type === 'all') {
            $res = Lesson::query()
                ->selectRaw('student_name, SUM(price) as student_price')
                ->where('user_id', auth()->user()->id)
                ->where('is_paid', true)
                ->groupBy('student_name')
                ->orderBy('student_price')
                ->pluck('student_price', 'student_name')
                ->toArray();
        }
        if ($request->type === 'month') {
            $dates = explode(' — ', $request->range);
            if (count($dates) < 1) {
                return redirect()->back()->withErrors(['required' => 'Заполните диапазон для расчета статистики'])->withInput();
            }

            $start = (new Carbon($dates[0]))->startOfMonth();
            if (count($dates) === 1) {
                $end = (new Carbon($dates[0]))->endOfMonth();

            } else {
                $end = (new Carbon($dates[1]))->endOfMonth();
                if ($end->lt($start)) {
                    return redirect()->back()->withErrors(['invalid' => 'Дата начала, должна быть раньше даты окончания'])->withInput();
                }
            }
            $res = Lesson::query()
                ->selectRaw('student_name, SUM(price) as student_price')
                ->where('user_id', auth()->user()->id)
                ->where('is_paid', true)
                ->where('date', '>=', $start)
                ->where('date', '<=', $end)
                ->groupBy('student_name')
                ->orderBy('student_price')
                ->pluck('student_price', 'student_name')
                ->toArray();
        }
        $labels = array_keys($res);
        $numbers = array_values($res);
        $colors = getRandomRGB(count($labels));

        return redirect()->route('statistic.earnings.students')->with(compact('labels', 'numbers', 'colors'));
    }
}
