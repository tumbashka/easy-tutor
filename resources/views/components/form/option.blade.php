@props([
    'form_name' => '',
    'source_var' => null,
    'value' => '',
])
<option @selected((old($form_name) == $value || $source_var === $value))
        value="{{ $value }}">
    {{ $slot }}
</option>
