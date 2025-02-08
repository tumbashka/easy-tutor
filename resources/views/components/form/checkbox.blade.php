@props([
    'icon' => '',
    'name' => '',
    'text' => '',
    ])

<div class="d-flex flex-row align-items-center mb-4">
    <i class="{{ $icon }} text-info "></i>
    <div class="">
        <input class="form-check-input me-2" type="checkbox" value="{{ $name }}" id="{{ $name }}" name="{{ $name }}">
        <label class="form-check-label" for="{{$name}}">{{ $text }}</label>
    </div>
</div>

