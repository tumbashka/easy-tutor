@props([
    'form_name' => '',
    'source_var' => null,
    'value' => '',
])
<option {{ (old($form_name) == $value) ? 'selected' :
            (isset($source_var) && $source_var == $value ? 'selected' : '') }} value="{{ $value }}">
    {{ $slot }}
</option>
