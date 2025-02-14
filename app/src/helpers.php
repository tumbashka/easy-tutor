<?php

use Illuminate\Support\Carbon;
use \Illuminate\Support\Facades\Route;

if (!function_exists('activeLink')) { // выделение активной ссылки в навбаре
    function activeLink(string $route): string
    {
        return Route::is($route) ? 'active' : '';
    }
}

if (!function_exists('getShortDayName')) {
    function getShortDayName(Carbon|int $dayOfWeek): string // получение сокращённого название дня недели с большой буквы
    {
        if (is_integer($dayOfWeek)) {
            $string = Carbon::now()->startOfWeek()->addDays($dayOfWeek)->isoFormat('dd');
        } else {
            $string = $dayOfWeek->isoFormat('dd');
        }
        $first = mb_substr($string, 0, 1, "UTF-8");
        $first = mb_strtoupper($first, "UTF-8");
        $end = mb_substr($string, 1, mb_strlen($string), "UTF-8");
        return $first . $end;
    }
}

if (!function_exists('getDayName')) {
    function getDayName($dayOfWeek): string // получение название дня недели с большой буквы
    {
        $string = Carbon::now()->startOfWeek()->addDays($dayOfWeek)->isoFormat('dddd');
        $first = mb_substr($string, 0, 1, "UTF-8");
        $first = mb_strtoupper($first, "UTF-8");
        $end = mb_substr($string, 1, mb_strlen($string), "UTF-8");
        return $first . $end;
    }
}

if (!function_exists('getWeekDays')) {
    function getWeekDays($weekOffset = 0): array // получение массива дней недели
    {
        $startDate = now();
        $currentWeekStart = $startDate->addWeeks($weekOffset)->startOfWeek()->addHours(23)->addMinutes(59);
        $weekDays = [];
        for ($i = 0; $i <= 6; $i++) {
            $weekDays[] = $currentWeekStart->copy()->addDays($i);
        }
        return $weekDays;
    }
}

if (!function_exists('getWeekBorders')) { // получение строки вида: "{дата понедельника} - {дата воскресенья}"
    function getWeekBorders($weekOffset = 0): string
    {
        $startDate = now();
        $startDate->addWeeks($weekOffset);
        $mon = $startDate->startOfWeek()->format('d.m.y');
        $sun = $startDate->endOfWeek()->format('d.m.y');

        return "{$mon} - {$sun}";
    }
}

if (!function_exists('getPreviousWeeks')) { // получение массива строк с границами предыдущих недель
    function getPreviousWeeks($weekOffset = 0, $count = 5): array
    {
        $res = [];
        for ($i = -1; -$i <= $count; $i--) {
            $res[$i + $weekOffset] = getWeekBorders($weekOffset + $i);
        }
        return $res;
    }
}

if (!function_exists('getNextWeeks')) { // получение массива строк с границами будущих недель
    function getNextWeeks($weekOffset = 0, $count = 5): array
    {
        $res = [];
        for ($i = 1; $i <= $count; $i++) {
            $res[$i + $weekOffset] = getWeekBorders($weekOffset + $i);
        }
        return $res;
    }
}

if (!function_exists('isPast')) { // обёртка для определения прошедшей даты
    function isPast(Carbon $day): bool
    {
        if ($day->lt(now())) {
            return true;
        }
        return false;
    }
}

if (!function_exists('getWeekOffset')) { // вычисление разницы в неделях по сравнению с текущим временем
    function getWeekOffset(Carbon $day): int
    {
        $now = Carbon::now();

        $startOfCurrentWeek = $now->copy()->startOfWeek();
        $startOfInputWeek = $day->copy()->startOfWeek();

        return $startOfCurrentWeek->diffInWeeks($startOfInputWeek, false);
    }
}

if (!function_exists('getLessonPrice')) { // вычисление разницы в неделях по сравнению с текущим временем
    function getLessonPrice($start, $end, int $price_on_hour): int
    {
        $start = new Carbon($start);
        $end = new Carbon($end);
        $lesson_length_minutes = $start->diffInMinutes($end);
        $hours = $lesson_length_minutes / 60;
        $lessonPrice = $hours * $price_on_hour;

        return (int)$lessonPrice;
    }
}

if (!function_exists('getRandomRGB')) { // вычисление разницы в неделях по сравнению с текущим временем
    function getRandomRGB($count = 1, $minColor = 70, $maxColor = 255): array|string
    {
        if ($count === 1) {
            $red = random_int($minColor, $maxColor);
            $green = random_int($minColor, $maxColor);
            $blue = random_int($minColor, $maxColor);
            $str = "rgb({$red}, {$green}, {$blue})";
            return $str;
        }
        $arr = [];
        if ($count > 1) {
            for ($i = 0; $i < $count; $i++) {
                $red = random_int($minColor, $maxColor);
                $green = random_int($minColor, $maxColor);
                $blue = random_int($minColor, $maxColor);
                $str = "rgb({$red}, {$green}, {$blue})";
                $arr[] = $str;
            }
        }
        return $arr;
    }
}


if (!function_exists('getHiFormatTime')) { // вычисление разницы в неделях по сравнению с текущим временем
    function getHiFormatTime($time): string
    {
        $carbon = new Carbon($time);
        return $carbon->format('H:i');
    }
}

if (!function_exists('getLessonType')) { // вычисление разницы в неделях по сравнению с текущим временем
    function getLessonType($type): string
    {
        return match ($type) {
            'online' => 'Онлайн',
            'face-to-face' => 'Очно',
            'all' => 'Онлайн/Очно',
            default => '',
        };
    }
}

if (!function_exists('getLessonStatus')) { // вычисление разницы в неделях по сравнению с текущим временем
    function getLessonStatus($status): string
    {
        return match ($status) {
            'free' => 'Время не занято',
            'trial' => 'Назначено пробное занятие',
            default => '',
        };
    }
}




