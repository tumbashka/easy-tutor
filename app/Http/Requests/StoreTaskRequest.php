<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasVerifiedEmail();
    }

    public function prepareForValidation()
    {
        $this->merge([
            'students' => $this->remove_groups_from_students(),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:16700000'],
            'deadline' => ['nullable', 'date_format:Y-m-d H:i'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:App\Models\TaskCategory,id'],
            'students' => ['nullable', 'array'],
            'students.*' => ['exists:App\Models\Student,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'week_day' => 'День недели не выбран',
        ];
    }

    private function remove_groups_from_students()
    {
        if ($this->students) {
            $students = [];
            foreach ($this->students as $student) {
                if (ctype_digit($student)) {
                    $students[] = $student;
                }
            }

            return $students;
        }

        return $this->students;
    }
}
