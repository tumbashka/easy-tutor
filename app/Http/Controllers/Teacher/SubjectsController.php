<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\Subject\DeleteSubjectRequest;
use App\Http\Requests\Teacher\Subject\StoreSubjectRequest;
use App\Http\Requests\Teacher\Subject\UpdateSubjectRequest;
use App\Models\Subject;

class SubjectsController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $subjects = Subject::where('is_active', true)->paginate(15);
        $userSubjects = $user->subjects()->whereIn('name', $subjects->pluck('name'))->get();

        return view('teacher.settings.subjects.index', compact('subjects', 'userSubjects'));
    }

    public function add(Subject $subject)
    {
        $user = auth()->user();
        $user->subjects()->attach($subject);

        return redirect()->back();
    }

    public function remove(Subject $subject)
    {
        $user = auth()->user();
        $user->subjects()->detach($subject);

        return redirect()->back();
    }

    public function default(Subject $subject)
    {
        $user = auth()->user();
        $user->subjects->map(function ($userSubject) use ($subject) {
            if ($subject->id === $userSubject->id) {
                $userSubject->pivot->is_default = true;
            } else {
                $userSubject->pivot->is_default = false;
            }
            $userSubject->pivot->save();
        });

        return redirect()->back();
    }

    public function subjectStore(StoreSubjectRequest $request)
    {
        $user = auth()->user();

        if ($user->subjects()->count()) {
            $params = [
                'name' => $request->input('name'),
                'user_id' => auth()->user()->id,
            ];
        } else {
            $params = [
                'name' => $request->input('name'),
                'user_id' => auth()->user()->id,
                'is_default' => true,
            ];
        }
        Subject::create($params);

        return redirect()->back()->withSuccess('Предмет успешно добавлен');
    }

    public function subjectUpdate(UpdateSubjectRequest $request, Subject $subject)
    {
        $user = auth()->user();
        if ($request->input('is_default')) {
            $user->subjects()->update([
                'is_default' => false,
            ]);
            $subject->update([
                'name' => $request->input('name'),
                'is_default' => $request->input('is_default'),
            ]);
        } else {
            $subject->update([
                'name' => $request->input('name'),
            ]);
        }


        return redirect()->back()->withSuccess('Предмет успешно сохранен');
    }

    public function subjectDelete(DeleteSubjectRequest $request, Subject $subject)
    {
        $subject->deleteOrFail();
        if ($subject->is_default && auth()->user()->subjects()->count()) {
            auth()->user()->subjects()->first()->update([
                'is_default' => true,
            ]);
        }

        return redirect()->back()->withSuccess('Предмет успешно удален');
    }

}
