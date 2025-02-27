<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function index()
    {
        if (Gate::denies('admin-access')) {
            abort(403);
        }
        $users_count = User::count();
        $students_count = Student::count();
        $lessons_count = Lesson::where('date', '<', now())->count();
        $last_registered = User::orderBy('created_at', 'desc')->first();
        $not_active_users_count = User::where('is_active', false)->count();

        return view('admin.dashboard.index', compact('users_count', 'students_count', 'lessons_count', 'last_registered', 'not_active_users_count'));
    }
}
