<?php

namespace App\Http\Requests\Teacher\Board;

use App\Models\Board;
use App\Rules\TimeNotOccupied;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBoardRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('update', $this->board);
    }
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
        ];
    }
}
