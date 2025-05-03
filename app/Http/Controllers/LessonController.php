<?php

namespace App\Http\Controllers;


use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Models\Lesson;
use App\Models\Student;
use App\src\Schedule\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LessonController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'week' => ['nullable', 'integer'],
        ]);
        $weekOffset = (int)$request->week;
        $weekDays = getWeekDays($weekOffset); // ['0-6' => Carbon obj]
        $previous = getPreviousWeeks($weekOffset, 10);
        $next = getNextWeeks($weekOffset, 10);

        $user = auth()->user();
        $schedule = new Schedule($user);
        $lessonsOnDays = $schedule->getWeekLessonsOnDays($weekDays);

        $allLessons = $lessonsOnDays->flatten(1);
        $now = now();
        $statistics = [
            'conductedLessons' => 0,
            'toConductLessons' => 0,
            'ongoingLessons' => 0,
            'canceledLessons' => 0,
            'earned' => 0,
            'canceledMoneys' => 0,
            'totalPossibleEarnings' => 0,
            'hoursConducted' => 0,
            'hoursToConduct' => 0,
        ];

        foreach ($allLessons as $lesson) {
            // Ensure date is in YYYY-MM-DD format and combine with start/end times
            $lessonDate = \Carbon\Carbon::parse($lesson->date)->format('Y-m-d');
            // Since start/end are cast as datetime:H:i, they are Carbon instances with time only
            $startTime = $lesson->start->format('H:i:s');
            $endTime = $lesson->end->format('H:i:s');

            $lessonStart = \Carbon\Carbon::parse("{$lessonDate} {$startTime}");
            $lessonEnd = \Carbon\Carbon::parse("{$lessonDate} {$endTime}");

            // If end time is before start time, assume it crosses midnight
            if ($lessonEnd < $lessonStart) {
                $lessonEnd->addDay();
            }

            // Calculate duration in hours, ensuring positive value
            $duration = abs($lessonEnd->diffInMinutes($lessonStart)) / 60;

            // Debugging: Log the parsed times and duration
            \Log::debug("Lesson ID: {$lesson->id}, Start: {$lessonStart}, End: {$lessonEnd}, Duration: {$duration} hours");

            if ($lesson->is_canceled) {
                $statistics['canceledLessons']++;
                $statistics['canceledMoneys'] += $lesson->price;
            } else {
                $statistics['totalPossibleEarnings'] += $lesson->price;
                if ($lesson->is_paid) {
                    $statistics['earned'] += $lesson->price;
                }
                if ($now > $lessonEnd) {
                    $statistics['conductedLessons']++;
                    $statistics['hoursConducted'] += $duration;
                } elseif ($now >= $lessonStart && $now < $lessonEnd) {
                    $statistics['ongoingLessons']++;
                } else {
                    $statistics['toConductLessons']++;
                    $statistics['hoursToConduct'] += $duration;
                }
            }
        }

        return view('schedule.index', compact('weekOffset', 'weekDays', 'previous', 'next', 'lessonsOnDays', 'statistics'));
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

    public function create($day)
    {
        $day = new Carbon($day);
        $students = Student::where('user_id', auth()->id())->orderBy('name')->get();

        return view('lesson.create', compact('day', 'students'));
    }

    public function store(StoreLessonRequest $request, $day)
    {
        $day = new Carbon($day);
        $student_name = Student::find($request->student)->name;

        $lesson = Lesson::create([
            'student_id' => $request->student,
            'student_name' => $student_name,
            'user_id' => auth()->user()->id,
            'date' => $day->format('Y-m-d'),
            'start' => $request->start,
            'end' => $request->end,
            'price' => $request->price,
            'note' => $request->note,
        ]);

        if ($lesson) {
            session(['success' => 'Занятие успешно добавлено!']);
        } else {
            session(['error' => 'Ошибка добавления занятия!']);
        }

        return redirect()->route('schedule.show', ['day' => $day->format('Y-m-d')]);
    }

    public function edit($day, $lesson)
    {
        $day = new Carbon($day);
        $lesson = Lesson::with('student')->find($lesson);
        $students = Student::where('user_id', auth()->id())->orderBy('name')->get();

        return view('lesson.edit', compact('day', 'students', 'lesson'));
    }

    public function update(UpdateLessonRequest $request, $day, Lesson $lesson)
    {
        $student_name = Student::find($request->student)->name;

        $lesson->student_id = $request->student;
        $lesson->student_name = $student_name;
        $lesson->start = $request->start;
        $lesson->end = $request->end;
        $lesson->price = $request->price;
        $lesson->note = $request->note;

        if ($lesson->update()) {
            session(['success' => 'Занятие успешно сохранено!']);
        } else {
            session(['error' => 'Ошибка изменения занятия!']);
        }
        $week = getWeekOffset(new Carbon($day));

        return redirect()->route('schedule.index', compact('week'));
    }

    public function change_status($day, $lesson)
    {
        $lesson = Lesson::find($lesson);
        $lesson->is_canceled = !$lesson->is_canceled;
        $lesson->save();
        return redirect()->back();
    }
}
