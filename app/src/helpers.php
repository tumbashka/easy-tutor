<?php

use Illuminate\Support\Carbon;
use \Illuminate\Support\Facades\Route;

if (!function_exists('activeLink')) {
    /**
     * Выделение активной ссылки в навбаре
     *
     * @param string $route
     * @return string
     */
    function activeLink(string $route): string
    {
        return Route::is($route) ? 'active' : '';
    }
}

if (!function_exists('isAdminLink')) {
    /**
     * Проверка, что мы в админке
     *
     * @return bool
     */
    function isAdminLink(): bool
    {
        return Route::is('admin*');
    }
}

if (!function_exists('getShortDayName')) {
    /**
     * Получение сокращённого названия дня недели с большой буквы
     *
     * @param Carbon|int $dayOfWeek
     * @return string
     */
    function getShortDayName(Carbon|int $dayOfWeek): string
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
    /**
     * Получение названия дня недели с большой буквы
     *
     * @param $dayOfWeek
     * @return string
     */
    function getDayName($dayOfWeek): string
    {
        $string = Carbon::now()->startOfWeek()->addDays($dayOfWeek)->isoFormat('dddd');
        $first = mb_substr($string, 0, 1, "UTF-8");
        $first = mb_strtoupper($first, "UTF-8");
        $end = mb_substr($string, 1, mb_strlen($string), "UTF-8");
        return $first . $end;
    }
}

if (!function_exists('getWeekDays')) {
    /**
     * Получение массива дней недели
     *
     * @param int $weekOffset
     * @return array
     */
    function getWeekDays(int $weekOffset = 0): array
    {
        $startDate = now();
        $currentWeekDay = $startDate->addWeeks($weekOffset)->startOfWeek()->endOfDay();
        $weekDays = [];
        for ($i = 0; $i <= 6; $i++) {
            $weekDays[] = $currentWeekDay->copy()->addDays($i);
        }
        return $weekDays;
    }
}

if (!function_exists('getWeekDayIndex')) {
    /**
     * Получение индекса дня недели `0-ПН ... 6-ВСК`
     *
     * @param Carbon $date
     * @return int
     */
    function getWeekDayIndex(Carbon $date): int
    {
        $index = $date->weekday();
        $index -= 1;
        if ($index < 0) {
            $index = 6;
        }

        return $index;
    }
}

if (!function_exists('getWeekBorders')) {
    /**
     * Получение строки, вида:
     * `{дата начала недели} - {дата конца недели}`
     *
     * @param int $weekOffset
     * @return string
     */
    function getWeekBorders(int $weekOffset = 0): string
    {
        $startDate = now();
        $startDate->addWeeks($weekOffset);
        $mon = $startDate->startOfWeek()->format('d.m.y');
        $sun = $startDate->endOfWeek()->format('d.m.y');

        return "{$mon} - {$sun}";
    }
}

if (!function_exists('getPreviousWeeks')) {
    /**
     * Получение массива строк с границами предыдущих недель
     *
     * @param int $weekOffset
     * @param int $count
     * @return array
     */
    function getPreviousWeeks(int $weekOffset = 0, int $count = 5): array
    {
        $res = [];
        for ($i = -1; -$i <= $count; $i--) {
            $res[$i + $weekOffset] = getWeekBorders($weekOffset + $i);
        }
        return $res;
    }
}

if (!function_exists('getNextWeeks')) {
    /**
     * Получение массива строк с границами будущих недель
     *
     * @param int $weekOffset
     * @param int $count
     * @return array
     */
    function getNextWeeks(int $weekOffset = 0, int $count = 5): array
    {
        $res = [];
        for ($i = 1; $i <= $count; $i++) {
            $res[$i + $weekOffset] = getWeekBorders($weekOffset + $i);
        }
        return $res;
    }
}

if (!function_exists('getWeekOffset')) {
    /**
     * Вычисление разницы в неделях по сравнению с текущим временем
     *
     * @param Carbon $day
     * @return int
     */
    function getWeekOffset(Carbon $day): int
    {
        $now = Carbon::now();

        $startOfCurrentWeek = $now->copy()->startOfWeek();
        $startOfInputWeek = $day->copy()->startOfWeek();

        return $startOfCurrentWeek->diffInWeeks($startOfInputWeek);
    }
}

if (!function_exists('getLessonPrice')) {
    /**
     * Вычисление стоимости занятия
     *
     * @param $start `Начало занятия`
     * @param $end `Конец занятия`
     * @param int $price_on_hour `Стоимость за час`
     * @return int
     */
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

if (!function_exists('getRandomRGB')) {
    /**
     * Получение случайного RBG цвета
     *
     * @param int $count
     * @param int $minColor
     * @param int $maxColor
     * @return array|string
     * @throws \Random\RandomException
     */
    function getRandomRGB(int $count = 1, int $minColor = 70, int $maxColor = 255): array|string
    {
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
        if ($count === 1) {
            return $arr[0];
        } else {
            return $arr;
        }
    }
}


if (!function_exists('getHiFormatTime')) {
    /**
     * Получение строки времени в формате H:i
     *
     * @param $time
     * @return string
     */
    function getHiFormatTime($time): string
    {
        $carbon = new Carbon($time);
        return $carbon->format('H:i');
    }
}

if (!function_exists('getLessonType')) {
    /**
     * Получение названия для типа занятия
     *
     * @param $type
     * @return string
     */
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

if (!function_exists('getLessonStatus')) {
    /**
     * Получение строки статуса занятия
     *
     * @param $status
     * @return string
     */
    function getLessonStatus($status): string
    {
        return match ($status) {
            'free' => 'Время не занято',
            'trial' => 'Назначено пробное занятие',
            default => '',
        };
    }
}

if (!function_exists('getRGBFromHex')) {
    /**
     * Получение массива цветов по каналам(R,G,B) из HEX цвета
     *
     * @param $hex_color
     * @return array
     */
    function getRGBFromHex($hex_color): array
    {
        return sscanf($hex_color, "#%02x%02x%02x");
    }
}


if (!function_exists('getTextContrastColor')) {
    /**
     * Вычисление строки стиля для текста, контрастного на фоне
     *
     * @param $hex_color
     * @return string
     */
    function getTextContrastColor($hex_color): string
    {
        $rgb = getRGBFromHex($hex_color);
        $brightness = ($rgb[0] * 299 + $rgb[1] * 587 + $rgb[2] * 114) / 1000;

        if ($brightness < 170) {
            return 'text-light';
        } else {
            return 'text-dark';
        }
    }
}





