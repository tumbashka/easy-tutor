<?php

namespace App\Http\Requests\LessonTime;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonTimeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->student && $this->user()->can('update', $this->student);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'week_day' => ['required', 'integer', 'max:6', 'min:0'],
            'start' => ['required', 'date_format:H:i'],
            'end' => ['required', 'date_format:H:i', 'after:start'],
            'subject' => ['nullable', 'exists:subjects,id'],
        ];
    }
}
