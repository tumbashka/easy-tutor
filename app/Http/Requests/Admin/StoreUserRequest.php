<?php

namespace App\Http\Requests\Admin;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\Rules\Phone;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,bmp,webp', 'max:25000'],
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:100', 'unique:'.User::class],
            'password' => ['required', 'string', 'min:7', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
            'is_verify_email' => ['nullable', 'boolean'],
            'about' => ['nullable', 'string', 'max:16700000'],
            'phone' => ['nullable', 'string', 'max:20', (new Phone)->country('RU')->mobile()],
            'telegram_username' => ['nullable', 'string', 'max:50'],
            'telegram_id' => ['nullable', 'string', 'max:64'],
            'role' => ['required', Rule::enum(Role::class)],
        ];
    }
}
