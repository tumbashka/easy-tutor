<?php

namespace App\Http\Requests\Teacher\FreeTime;

use App\Enums\FreeTimeStatus;
use App\Enums\FreeTimeType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'status' => ['required', 'string', Rule::enum(FreeTimeStatus::class)],
            'type' => ['required', 'string', Rule::enum(FreeTimeType::class)],
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
