<?php

namespace App\Http\Requests\Student;

use App\Models\Board;
use App\Rules\TimeNotOccupied;
use Illuminate\Foundation\Http\FormRequest;

class SearchTeacherRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string'],
            'subjects' => ['nullable', 'array'],
            'days' => ['nullable', 'array'],
            'subjects.*' => ['nullable', 'integer', 'exists:subjects,id'],
            'days.*' => ['nullable', 'integer', 'between:0,6'],
            'sort' => ['nullable', 'string'],
        ];
    }
}
