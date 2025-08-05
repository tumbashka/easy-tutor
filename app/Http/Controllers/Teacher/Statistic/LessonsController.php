<?php

namespace App\Http\Controllers\Teacher\Statistic;

use App\Models\Lesson;
use App\src\Statistic\LessonsTimeStatistic;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LessonsController extends StatisticController
{
    public function period()
    {
        $labels = session()->pull('labels');
        $first_data = session()->pull('first_data');
        $second_data = session()->pull('second_data');
        $total = session()->pull('total');

        return view('teacher.statistic.lessons.period', compact('labels', 'first_data', 'second_data', 'total'));
    }

    public function period_calculate(Request $request)
    {
        $data = $this->getValidatedData($request);
        if (! is_array($data)) {
            return $data;
        }
        $all = Lesson::query()
            ->selectRaw('date, count(*) as count_all')
            ->whereBetween('date', [$data['start'], $data['end']])
            ->where('date', '<=', now())
            ->where('user_id', auth()->user()->id)
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count_all', 'date')
            ->toArray();

        $canceled = Lesson::query()
            ->selectRaw('date, count(*) as count_all')
            ->where('is_canceled', true)
            ->whereBetween('date', [$data['start'], $data['end']])
            ->where('date', '<=', now())
            ->where('user_id', auth()->user()->id)
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count_all', 'date')
            ->toArray();

        $statistic = new LessonsTimeStatistic($all, $canceled, $data['type']);
        $statistic->calculate();
        $first_data = $statistic->getNumbers()[0];
        $second_data = $statistic->getNumbers()[1];
        $labels = $statistic->getLabels();

        $total['accepted'] = array_sum($first_data);
        $total['canceled'] = array_sum($second_data);

        return redirect()->route('statistic.lessons.period')->with(compact('labels', 'first_data', 'second_data', 'total'));
    }

    public function students()
    {
        $labels = session()->pull('labels');
        $first_data = session()->pull('first_data');
        $second_data = session()->pull('second_data');

        return view('teacher.statistic.lessons.students', compact('labels', 'first_data', 'second_data'));
    }

    public function students_calculate(Request $request)
    {
        Validator::make($request->all(), [
            'type' => ['required', 'string'],
        ])->validate();
        if ($request->type != 'all' && $request->type != 'month') {
            return redirect()->back()->withErrors(['type_not_found' => 'Ошибка расчета статистики'])->withInput();
        }

        if ($request->type === 'all') {
            $all_lessons = Lesson::query()
                ->select('student_name')
                ->selectRaw('COUNT(*) as total_lessons')
                ->where('date', '<=', now())
                ->where('user_id', auth()->user()->id)
                ->groupBy('student_name')
                ->orderByDesc('total_lessons')
                ->pluck('total_lessons', 'student_name')
                ->toArray();
            $canceled_lessons = Lesson::query()
                ->select('student_name')
                ->selectRaw('COUNT(*) as total_lessons')
                ->where('date', '<=', now())
                ->where('user_id', auth()->user()->id)
                ->where('is_canceled', true)
                ->groupBy('student_name')
                ->orderByDesc('total_lessons')
                ->pluck('total_lessons', 'student_name')
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
            $all_lessons = Lesson::query()
                ->select('student_name')
                ->selectRaw('COUNT(*) as total_lessons')
                ->whereBetween('date', [$start, $end])
                ->where('date', '<=', now())
                ->where('user_id', auth()->user()->id)
                ->groupBy('student_name')
                ->orderByDesc('total_lessons')
                ->pluck('total_lessons', 'student_name')
                ->toArray();
            $canceled_lessons = Lesson::query()
                ->select('student_name')
                ->selectRaw('COUNT(*) as total_lessons')
                ->whereBetween('date', [$start, $end])
                ->where('date', '<=', now())
                ->where('user_id', auth()->user()->id)
                ->where('is_canceled', true)
                ->groupBy('student_name')
                ->orderByDesc('total_lessons')
                ->pluck('total_lessons', 'student_name')
                ->toArray();
        }

        $allowed_lessons = [];
        foreach ($all_lessons as $student_name => $count) {
            $allowed_lessons[$student_name] = $count;
            if (array_key_exists($student_name, $canceled_lessons)) {
                $allowed_lessons[$student_name] -= $canceled_lessons[$student_name];
            } else {
                $canceled_lessons[$student_name] = 0;
            }
        }
        ksort($allowed_lessons);
        ksort($canceled_lessons);

        $first_data = array_values($allowed_lessons);
        $second_data = array_values($canceled_lessons);
        $labels = array_keys($allowed_lessons);

        return redirect()->route('statistic.lessons.students')->with(compact('labels', 'first_data', 'second_data'));
    }
}
