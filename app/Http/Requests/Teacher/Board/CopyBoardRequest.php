<?php

namespace App\Http\Requests\Teacher\Board;

use App\Rules\TimeNotOccupied;
use Illuminate\Foundation\Http\FormRequest;

class CopyBoardRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('copy', $this->board);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255'],
        ];
    }
}
