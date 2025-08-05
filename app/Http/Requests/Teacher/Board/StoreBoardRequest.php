<?php

namespace App\Http\Requests\Teacher\Board;

use App\Rules\TimeNotOccupied;
use Illuminate\Foundation\Http\FormRequest;

class StoreBoardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
        ];
    }
}
