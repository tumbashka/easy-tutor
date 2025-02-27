<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFreeTimeRequest extends FormRequest
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
            'week_day' => ['required', 'integer', 'min:0', 'max:6'],
            'start' => ['required', 'date_format:H:i'],
            'end' => ['required', 'date_format:H:i', 'after:start'],
            'status' => ['required', 'string', 'in:free,trial'],
            'type' => ['required', 'string', 'in:online,face-to-face,all'],
            'note' => ['nullable', 'string', 'max:65000'],
        ];
    }
    public function messages(): array
    {
        return [
            'week_day' => 'День недели не выбран',
        ];
    }
}
