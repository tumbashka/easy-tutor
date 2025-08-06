<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

class MessagesController extends Controller
{
    public function index()
    {
        return view('student.lesson.index');
    }
}
