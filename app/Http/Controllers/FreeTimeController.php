<?php

namespace App\Http\Controllers;

use App\Http\Requests\FreeTime\StoreFreeTimeRequest;
use App\Http\Requests\FreeTime\UpdateFreeTimeRequest;
use App\Models\FreeTime;
use App\Models\LessonTime;
use App\Models\Student;
use App\Models\User;
use App\Services\LessonService;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

class FreeTimeController extends Controller
{
    public function index(Request $request, LessonService $lessonService)
    {
        $url = $request->encrypted_url;

        $week_days = $lessonService->getWeekDays();

        $user = auth()->user();
        $allLessonSlotsOnWeekDays = $user->getAllLessonSlotsOnWeekDays();

        return view('free-time.index', compact('week_days', 'allLessonSlotsOnWeekDays', 'url'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'day' => ['nullable', 'integer', 'min:0', 'max:6'],
        ]);
        $day = $request->day;

        return view('free-time.create', compact('day'));
    }

    public function store(StoreFreeTimeRequest $request)
    {
        $free_time = FreeTime::create([
            'week_day' => $request->input('week_day'),
            'start' => $request->input('start'),
            'end' => $request->input('end'),
            'status' => $request->input('status'),
            'type' => $request->input('type'),
            'note' => $request->input('note'),
            'user_id' => auth()->user()->id,
        ]);
        if ($free_time) {
            session(['success' => 'Окно успешно добавлено!']);
        } else {
            session(['error' => 'Ошибка добавления окна!']);
        }

        return redirect()->route('free-time.index');
    }

    public function edit(FreeTime $freeTime)
    {
        return view('free-time.edit', ['day' => null, 'free_time' => $freeTime]);
    }

    public function update(UpdateFreeTimeRequest $request, FreeTime $freeTime)
    {
        $freeTime->week_day = $request->input('week_day');
        $freeTime->start = $request->input('start');
        $freeTime->end = $request->input('end');
        $freeTime->status = $request->input('status');
        $freeTime->type = $request->input('type');
        $freeTime->note = $request->input('note');

        if ($freeTime->save()) {
            session(['success' => 'Окно успешно обновлено!']);
        } else {
            session(['error' => 'Ошибка обновления окна!']);
        }

        return redirect()->route('free-time.index');
    }

    public function delete(FreeTime $freeTime)
    {
        if (auth()->user()->can('delete', $freeTime)) {
            if ($freeTime->delete()) {
                session(['success' => 'Окно успешно удалено!']);
            } else {
                session(['error' => 'Ошибка удаления!']);
            }

            return redirect()->route('free-time.index');
        }
        abort(403);
    }

    public function setStudent(FreeTime $freeTime)
    {
        $students = auth()->user()->students;

        return view('free-time.set_student', compact('freeTime', 'students'));
    }

    public function setStudentProcess(Request $request, FreeTime $freeTime)
    {
        $validated = $request->validate([
            'student' => ['required', 'exists:App\Models\Student,id'],
        ]);
        $student = Student::find($validated['student']);
        $lesson_time = LessonTime::create([
            'student_id' => $student->id,
            'week_day' => $freeTime->week_day,
            'start' => $freeTime->start,
            'end' => $freeTime->end,
        ]);

        if ($lesson_time) {
            session(['success' => 'Занятие успешно добавлено!']);
        } else {
            session(['error' => 'Ошибка добавления занятия!']);
        }

        $freeTime->delete();

        return redirect()->route('free-time.index');
    }

    public function generateEncryptedUrl(Request $request)
    {
        $validated = $request->validate([
            'expire_time' => ['required', 'integer', 'min:1', 'max:62'],
            'allow_lessons' => ['nullable', 'boolean'],
        ]);

        $encrypted_url = Crypt::encrypt([
            'user_id' => auth()->user()->id,
            'expires' => now()->addDays((int) $validated['expire_time'])->timestamp,
            'allow_lessons' => $validated['allow_lessons'] ?? false,
        ]);
        $encrypted_url = url()->route('free-time.show_shared_page', ['token' => $encrypted_url]);

        return redirect()->route('free-time.index', compact('encrypted_url'));
    }

    public function showSharedPage(#[\SensitiveParameter] $token)
    {
        try {
            $data = Crypt::decrypt($token);
            if ($data['expires'] < now()->timestamp) {
                abort(410, 'Ссылка устарела');
            }

            if (null === $user = User::find($data['user_id'])) {
                abort(404);
            }
            if (! $user->is_active) {
                abort(404);
            }
            $allowLessons = $data['allow_lessons'] ?? false;
            $allLessonSlotsOnWeekDays = $user->getAllLessonSlotsOnWeekDays($allowLessons);

            $expires = (new Carbon($data['expires']))->longAbsoluteDiffForHumans(now());

            return view('free-time.shared-page', compact('allLessonSlotsOnWeekDays', 'user', 'expires'));
        } catch (DecryptException $exception) {
            abort(404);
        }
    }
}
