<?php

namespace App\Http\Requests\Teacher\Board;

use App\Models\Board;
use App\Rules\TimeNotOccupied;
use Illuminate\Foundation\Http\FormRequest;

class SearchBoardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string'],
            'subject_id' => ['nullable', 'integer'],
        ];
    }
}
