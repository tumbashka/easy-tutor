<?php

namespace App\Http\Requests\Teacher\Student;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100', 'min:2'],
            'class' => ['nullable', 'integer', 'max:11', 'min:1'],
            'price' => ['required', 'integer', 'max:65000', 'min:0'],
            'note' => ['nullable', 'string', 'max:65000'],
        ];
    }
}
