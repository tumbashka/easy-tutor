<?php

namespace App\Http\Requests\Teacher\Lesson;

use App\Rules\TimeNotOccupied;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLessonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->lesson);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = auth()->user();
        $day = request('day');
        $occupiedSlots = $user->lessons()
            ->whereDate('date', $day)
            ->where('is_canceled', false)
            ->whereNot('id', $this->lesson->id)
            ->with('student')
            ->get();

        return [
            'student' => ['required', 'exists:students,id'],
            'start' => ['required', 'date_format:H:i', new TimeNotOccupied($occupiedSlots)],
            'end' => ['required', 'date_format:H:i', 'after:start'],
            'price' => ['required', 'integer', 'max:200000', 'min:0'],
            'note' => ['nullable', 'string', 'max:65000'],
        ];
    }
}
