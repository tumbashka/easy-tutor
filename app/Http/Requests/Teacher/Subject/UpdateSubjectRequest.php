<?php

namespace App\Http\Requests\Teacher\Subject;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Propaganistas\LaravelPhone\Rules\Phone;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->id == $this->subject->user_id;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $message = $validator->errors()->first();
        session()->flash('error', $message);

        parent::failedValidation($validator);
    }

}
