<?php

namespace App\Http\Requests\Common;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize()
    {
        $chat = $this->route('chat')?->load('users');
        if ($chat->users?->isNotEmpty() && $chat->users->contains($this->user())) {
            return true;
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'text' => ['required', 'string', 'max:2000'],
        ];
    }
}
