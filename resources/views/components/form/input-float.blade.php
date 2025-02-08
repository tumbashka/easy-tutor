@props([
    'icon' => '',
    'type' => 'text',
    'name' => '',
    'text' => '',
    ])
<div class="d-flex flex-row align-items-center ps-5">
    <x-form.input-error-alert :name="$name"/>
</div>
<div class="d-flex flex-row align-items-center mb-4">
    <i class="{{ $icon }} text-info "></i>
    <div class="form-floating flex-fill mb-0">
        <input type="{{ $type }}" class="form-control
        {{ ($errors->has($name) || ($name == 'password_confirmation' && $errors->has('password'))) ? 'is-invalid' : '' }}"
               value="{{ (($type != 'password') && old($name)) ? old($name) : '' }}"
               placeholder="{{ $text }}" name="{{ $name }}"/>
        <label class="form-label">{{ $text }}</label>
    </div>
</div>
