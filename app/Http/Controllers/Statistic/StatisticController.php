<?php

namespace App\Http\Controllers\Statistic;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

abstract class StatisticController extends Controller
{
    public function getValidatedData(Request $request)
    {
        $dates = explode(' — ', $request->range);
        if (count($dates) < 2) {
            return redirect()->back()->withErrors(['required' => 'Заполните диапазон для расчета статистики'])->withInput();
        }
        $data['type'] = $request->type;
        $data['start'] = $dates[0];
        $data['end'] = $dates[1];
        switch ($data['type']) {
            case 'day':
                Validator::make($data, [
                    'start' => ['required', 'date_format:Y-m-d',],
                    'end' => ['required', 'date_format:Y-m-d', 'after:start'],
                    'type' => ['required', 'string'],
                ])->validate();
                break;
            case 'month':
                Validator::make($data, [
                    'start' => ['required', 'date_format:Y-m',],
                    'end' => ['required', 'date_format:Y-m', 'after:start'],
                    'type' => ['required', 'string'],
                ])->validate();
                break;
            default :
                return redirect()->back()->withErrors(['required' => 'Заполните диапазон для расчета статистики'])->withInput();
        }
        if ($data['type'] == 'month') {
            $data['start'] = (new Carbon($data['start']))->startOfMonth();
            $data['end'] = (new Carbon($data['end']))->endOfMonth();
        }

        return $data;
    }
}
