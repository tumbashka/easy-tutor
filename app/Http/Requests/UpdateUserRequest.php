<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Propaganistas\LaravelPhone\Rules\Phone;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->id === $this->user->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,bmp,webp', 'max:25000'],
            'name' => ['required', 'string', 'max:100'],
            'password' => ['nullable', 'string', 'min:7', 'confirmed'],
            'about' => ['nullable', 'string', 'max:16700000'],
            'phone' => ['nullable', 'string', 'max:20', (new Phone)->country('RU')->mobile()],
        ];
    }

    public function messages(): array
    {
        return [
            'phone' => 'Не правильный формат номера телефона',
        ];
    }
}
