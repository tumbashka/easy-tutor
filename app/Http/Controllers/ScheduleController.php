<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonTime;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $weekOffset = (int)$request->week;
        $weekDays = getWeekDays($weekOffset);// ['0-6' => Carbon]
        $previous = getPreviousWeeks($weekOffset, 10);
        $next = getNextWeeks($weekOffset, 10);

        $lessonsOnDays = [];
        $user = auth()->user();
        foreach ($weekDays as $weekDay_id => $day) {// Для каждого дня недели

            $lessonTimes = $user->lessonTimes()
                ->where('week_day', $weekDay_id)
                ->orderBy('start')
                ->get();
            $lessons = $user->lessons()
                ->where('date', $day->format('Y-m-d'))
                ->orderBy('start')
                ->get();

            if ($lessons->count()) {// Занятия этого дня уже запрашивались
                if (!isPast($day)) {// Этот день еще не прошёл (код ниже не выполнится для старых записей)
                    $lessons_lesTimeIds = $lessons->pluck('lesson_time_id')->toArray();

                    foreach ($lessonTimes as $lessonTime) { // если мы добавили ученику новое время занятия
                        if (!in_array($lessonTime->id, $lessons_lesTimeIds)) {// добавляем его в занятия
                            $student_name = Student::find($lessonTime->student_id)->name;

                            $lessons[] = Lesson::create([
                                'student_id' => $lessonTime->student_id,
                                'user_id' => $user->id,
                                'student_name' => $student_name,
                                'date' => $day,
                                'start' => $lessonTime->start,
                                'end' => $lessonTime->end,
                                'price' => getLessonPrice($lessonTime->start, $lessonTime->end, $lessonTime->student->price),
                                'lesson_time_id' => $lessonTime->id,
                            ]);
                        }
                    }
                }
            } else {// Первый запрос занятий этого дня
                if (!isPast($day)) {
                    foreach ($lessonTimes as $lessonTime) {
                        $student_name = Student::find($lessonTime->student_id)->name;

                        $lessons[] = Lesson::create([
                            'student_id' => $lessonTime->student_id,
                            'user_id' => $user->id,
                            'student_name' => $student_name,
                            'date' => $day,
                            'start' => $lessonTime->start,
                            'end' => $lessonTime->end,
                            'is_paid' => false,
                            'price' => getLessonPrice($lessonTime->start, $lessonTime->end, $lessonTime->student->price),
                            'lesson_time_id' => $lessonTime->id,
                        ]);
                    }
                }
            }
            $arr = [];
            foreach ($lessons as $lesson) {
                $arr[] = $lesson;
            }
            $lessons = $arr;
            usort($lessons, function ($a, $b) {
                return $a['start'] <=> $b['start']; // Сортировка по времени занятия
            });

            $lessonsOnDays[$weekDay_id] = $lessons;
        }

        return view('schedule.index', compact('weekOffset', 'weekDays', 'previous', 'next', 'lessonsOnDays'));
    }

    public function show(Request $request, $day)
    {
        $day = new Carbon($day);
        $lessons = Lesson::where('date', $day->format('Y-m-d'))
            ->where('user_id', auth()->user()->id)
            ->get();
        $arr = [];
        foreach ($lessons as $lesson) {
            $arr[] = $lesson;
        }
        $lessons = $arr;
        usort($lessons, function ($a, $b) {
            return $a['start'] <=> $b['start']; // Сортировка по времени
        });

        return view('schedule.show', compact('day', 'lessons'));
    }

}
