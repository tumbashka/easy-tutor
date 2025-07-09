<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BackupDownloadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'string'],
            'dir' => ['required', 'string'],
        ];
    }

    /**
     * Get all input data, including route parameters.
     *
     * @return array
     */
    public function all($keys = null)
    {
        $data = parent::all($keys);
        $data['dir'] = $this->route('dir');
        $data['file'] = $this->route('file');
        return $data;
    }
}
