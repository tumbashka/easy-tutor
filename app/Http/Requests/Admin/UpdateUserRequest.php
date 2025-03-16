<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\Rules\Phone;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->user);
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
            'email' => ['required', 'string', 'email:rfc,dns', 'max:100', Rule::unique('users')->ignore($this->user)],
            'password' => ['nullable', 'string', 'min:7', 'confirmed'],
            'is_admin' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'is_verify_email' => ['nullable', 'boolean'],
            'about' => ['nullable', 'string', 'max:16700000'],
            'phone' => ['nullable', 'string', 'max:20', (new Phone)->country('RU')->mobile()],
            'telegram_username' => ['nullable', 'string', 'max:50'],
            'telegram_id' => ['nullable', 'string', 'max:64'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone' => 'Не правильный формат номера телефона',
        ];
    }
}
