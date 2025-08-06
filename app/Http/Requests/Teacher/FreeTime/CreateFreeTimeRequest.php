<?php

namespace App\Http\Requests\Teacher\FreeTime;

use Illuminate\Foundation\Http\FormRequest;

class CreateFreeTimeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasVerifiedEmail();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'day' => ['nullable', 'integer', 'min:0', 'max:6'],
        ];
    }
}
