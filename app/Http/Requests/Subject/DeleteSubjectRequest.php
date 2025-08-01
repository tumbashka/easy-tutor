<?php

namespace App\Http\Requests\Subject;

use Illuminate\Foundation\Http\FormRequest;
use Propaganistas\LaravelPhone\Rules\Phone;

class DeleteSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->id == $this->subject->user_id;
    }
}
