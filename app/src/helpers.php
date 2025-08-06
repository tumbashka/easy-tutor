<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Random\RandomException;

if (!function_exists('activeLink')) {
    /**
     * Выделение активной ссылки в навбаре
     */
    function activeLink(string $route): string
    {
        return Route::is($route) ? 'active' : '';
    }
}

if (!function_exists('isAdminLink')) {
    /**
     * Проверка, что мы в админке
     */
    function isAdminLink(): bool
    {
        return Route::is('admin*');
    }
}

if (!function_exists('getShortDayName')) {
    /**
     * Получение сокращённого названия дня недели с большой буквы
     */
    function getShortDayName(Carbon|int $dayOfWeek): string
    {
        if (is_int($dayOfWeek)) {
            $string = Carbon::now()->startOfWeek()->addDays($dayOfWeek)->isoFormat('dd');
        } else {
            $string = $dayOfWeek->isoFormat('dd');
        }
        $first = mb_substr($string, 0, 1, 'UTF-8');
        $first = mb_strtoupper($first, 'UTF-8');
        $end = mb_substr($string, 1, mb_strlen($string), 'UTF-8');

        return $first . $end;
    }
}

if (!function_exists('getDayName')) {
    /**
     * Получение названия дня недели с большой буквы
     */
    function getDayName(Carbon|int $dayOfWeek): string
    {
        if ($dayOfWeek instanceof Carbon) {
            $string = $dayOfWeek->isoFormat('dddd');
        } else {
            $string = Carbon::now()->startOfWeek()->addDays($dayOfWeek)->isoFormat('dddd');
        }
        $first = mb_substr($string, 0, 1, 'UTF-8');
        $first = mb_strtoupper($first, 'UTF-8');
        $end = mb_substr($string, 1, mb_strlen($string), 'UTF-8');

        return $first . $end;
    }
}

if (!function_exists('getWeekDayIndex')) {
    /**
     * Получение индекса дня недели `0-ПН ... 6-ВСК`
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

if (!function_exists('getWeekOffset')) {
    /**
     * Вычисление разницы в неделях по сравнению с текущим временем
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
     * @param  $start `Начало занятия`
     * @param  $end `Конец занятия`
     * @param int $price_on_hour `Стоимость за час`
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
     * @throws RandomException
     */
    function getRandomRGB(int $count = 1, int $minColor = 70, int $maxColor = 255): array|string
    {
        $arr = [];
        if ($count > 0) {
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
     */
    function getHiFormatTime($time): string
    {
        $carbon = new Carbon($time);

        return $carbon->format('H:i');
    }
}

if (!function_exists('getRGBFromHex')) {
    /**
     * Получение массива цветов по каналам(R,G,B) из HEX цвета
     */
    function getRGBFromHex($hex_color): array
    {
        return sscanf($hex_color, '#%02x%02x%02x');
    }
}

if (!function_exists('getTextContrastColor')) {
    /**
     * Вычисление строки стиля для текста, контрастного на фоне
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

if (!function_exists('pluralRu')) {
    /**
     * Постановка правильного окончания существительного в зависимости от количества для русского языка.
     * Принимает количество и массив форм существительного
     *  ['Куст', 'Куста', 'Кустов']
     */
    function pluralRu(int $number, array $words): string
    {
        $number = $number % 100;
        if ($number > 19) {
            $number = $number % 10;
        }
        return match ($number) {
            1 => $words[0],
            2, 3, 4 => $words[1],
            default => $words[2],
        };
    }
}
