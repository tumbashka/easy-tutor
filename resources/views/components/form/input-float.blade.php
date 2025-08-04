@props([
    'icon' => '',
    'type' => 'text',
    'name' => '',
    'text' => '',
    'required' => false,
    'value' => null,
    ])
<div class="d-flex flex-row align-items-center ps-5">
    <x-form.input-error-alert :name="$name"/>
</div>
<div class="d-flex flex-row align-items-center mb-4">
    @if($icon)
        <i class="{{ $icon }} text-primary "></i>
    @endif
    <div class="form-floating flex-fill mb-0">
        <input type="{{ $type }}" class="form-control
        {{ ($errors->has($name) || ($name == 'password_confirmation' && $errors->has('password'))) ? 'is-invalid' : '' }}"
               value="{{ (($type != 'password') && old($name)) ? old($name) : $value ?? '' }}"
               placeholder="{{ $text }}" name="{{ $name }}"/>
        <label class="form-label {{  $required ? 'required-input' : '' }}">{{ $text }}</label>
    </div>
</div>
