<?php

namespace App\Http\Controllers\Teacher\Statistic;

use App\Http\Requests\Teacher\Statistic\EarningsStudentsRequest;
use App\Models\Lesson;
use App\src\Statistic\EarningsTimeStatistic;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;

class EarningsController extends StatisticController
{
    public function period()
    {
        $labels = session()->pull('labels');
        $numbers = session()->pull('numbers');
        $label = session()->pull('label');
        $total = session()->pull('total');

        return view('teacher.statistic.earnings.period', compact('labels', 'numbers', 'label', 'total'));
    }

    public function periodCalculate(Request $request)
    {
        $data = $this->getValidatedData($request);
        if (! is_array($data)) {
            return $data;
        }
        $res = Lesson::query()
            ->selectRaw('date, SUM(price) as date_price')
            ->where('is_paid', true)
            ->whereBetween('date', [$data['start'], $data['end']])
            ->where('user_id', auth()->user()->id)
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('date_price', 'date')
            ->toArray();

        $statistic = new EarningsTimeStatistic($res, $data['type']);
        $statistic->calculate();
        $labels = $statistic->getLabels();
        $numbers = $statistic->getNumbers();

        $total = array_sum($numbers);
        $total = Number::format($total, 2, locale: 'ru');

        $data['start'] = (new Carbon($data['start']))->format('Y-m-d');
        $data['end'] = (new Carbon($data['end']))->format('Y-m-d');
        $label = "Доходы за период: с {$data['start']} по {$data['end']}";

        return redirect()->route('statistic.earnings.period')->with(compact('labels', 'numbers', 'label', 'total'));
    }

    public function students()
    {
        $labels = session()->pull('labels');
        $numbers = session()->pull('numbers');
        $colors = session()->pull('colors');

        return view('teacher.statistic.earnings.students', compact('labels', 'numbers', 'colors'));
    }

    public function studentsCalculate(EarningsStudentsRequest $request)
    {
      $type = $request->input('type');
        if ($type != 'all' && $type != 'month') {
            return redirect()->back()->withErrors(['type_not_found' => 'Указан неправильный тип расчета'])->withInput();
        }
        $res = [];
        if ($type === 'all') {
            $res = Lesson::query()
                ->selectRaw('student_name, SUM(price) as student_price')
                ->where('user_id', auth()->user()->id)
                ->where('is_paid', true)
                ->groupBy('student_name')
                ->orderBy('student_price')
                ->pluck('student_price', 'student_name')
                ->toArray();
        }
        if ($type === 'month') {
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
                ->whereBetween('date', [$start, $end])
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
