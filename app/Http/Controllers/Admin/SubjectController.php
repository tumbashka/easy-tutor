<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Subject\StoreSubjectRequest;
use App\Http\Requests\Admin\Subject\UpdateSubjectRequest;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $subjects = Subject::paginate();

        return view('admin.subject.index', compact('subjects'));
    }

    public function store(StoreSubjectRequest $request)
    {
        $this->authorize('create', Subject::class);
        Subject::create($request->validated());

        return redirect()->back()->withSuccess('Предмет успешно добавлен');
    }

    public function update(UpdateSubjectRequest $request, Subject $subject)
    {
        $this->authorize('update', $subject);
        $subject->name = $request->get('name');
        $subject->is_active = (bool)$request->get('is_active');
        $subject->save();

        return redirect()->back()->withSuccess('Предмет успешно сохранен');
    }

    public function destroy(Subject $subject)
    {
        $this->authorize('delete', $subject);

        return redirect()->back()->withSuccess('Предмет успешно удален');
    }
}
