@props([
    'icon' => '',
    'name' => '',
    'text' => '',
    'required' => false,
    'values' => [],
    'names' => [],
    ])
<div class="d-flex flex-row align-items-center ps-5">
    <x-form.input-error-alert :name="$name"/>
</div>
<div class="d-flex flex-row align-items-center mb-4">
    <i class="{{$icon}} text-primary"></i>
    <div class="form-floating flex-fill mb-0">
        <select name="{{ $name }}" id="{{ $name }}" data-tom-select-single placeholder="{{$text}}"
                class="w-full form-select">
            <option value="">{{ $text }}</option>
            @foreach($values as $value)
                <option value="{{ $value }}">
                    {{ __($names[$loop->index]) }}
                </option>
            @endforeach
        </select>
    </div>
</div>
