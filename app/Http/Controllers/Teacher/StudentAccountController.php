<?php

namespace App\Http\Controllers\Teacher;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\Student\StoreStudentAccountRequest;
use App\Models\Student;
use App\Models\User;
use App\Notifications\MyVerifyMail;
use App\Notifications\StudentAccountRegistered;
use Illuminate\Support\Str;

class StudentAccountController extends Controller
{
    public function create(Student $student)
    {
        return view('teacher.student_account.create', compact('student'));
    }

    public function store(StoreStudentAccountRequest $request, Student $student)
    {
        $password = Str::password(12);
        $account = User::forceCreate([
            'role' => Role::Student,
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($password),
            'is_active' => true,
        ]);

        $student->account_id = $account->id;
        $student->save();

        $account->notify(new MyVerifyMail());
        $account->notify(new StudentAccountRegistered($password));
    }
}
