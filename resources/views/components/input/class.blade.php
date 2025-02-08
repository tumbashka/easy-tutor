@props([
    'class' => null,
    'min' => '1',
    'max' => '11',
])
<select name="class" required class="form-select {{ $errors->has('class') ? 'is-invalid' : '' }}">
    <option>Класс не выбран</option>
    @for($i = $min; $i <= $max; $i++)
        <option  {{ (old('class') == $i) ? 'selected' : ($class == $i ? 'selected' : '') }}  value="{{ $i }}">{{ $i }}</option>
    @endfor
</select>
