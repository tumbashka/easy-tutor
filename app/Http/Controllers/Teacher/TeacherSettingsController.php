<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subject\DeleteSubjectRequest;
use App\Http\Requests\Subject\StoreSubjectRequest;
use App\Http\Requests\Subject\UpdateSubjectRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Subject;
use App\Models\User;
use App\Services\ImageService;

class TeacherSettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $subjects = $user->subjects;

        return view('teacher.settings.index', compact('subjects', 'user'));
    }

    public function subjectStore(StoreSubjectRequest $request)
    {
        $user = auth()->user();

        if ($user->subjects()->count()) {
            $params = [
                'name' => $request->input('name'),
                'user_id' => auth()->user()->id,
            ];
        }else{
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
        if($request->input('is_default')){
            $user->subjects()->update([
                'is_default' => false,
            ]);
            $subject->update([
                'name' => $request->input('name'),
                'is_default' => $request->input('is_default'),
            ]);
        }else{
            $subject->update([
                'name' => $request->input('name'),
            ]);
        }


        return redirect()->back()->withSuccess('Предмет успешно сохранен');
    }

    public function subjectDelete(DeleteSubjectRequest $request, Subject $subject)
    {
        $subject->deleteOrFail();
        if($subject->is_default && auth()->user()->subjects()->count()){
            auth()->user()->subjects()->first()->update([
                'is_default' => true,
            ]);
        }

        return redirect()->back()->withSuccess('Предмет успешно удален');
    }

}
