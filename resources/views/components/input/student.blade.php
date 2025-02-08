@props([
    'students' => null,
    'old_student_id' => null,
])
<select name="student" required class="form-select {{ $errors->has('student') ? 'is-invalid' : '' }}">
    <option>Ученик не выбран</option>
    @foreach($students as $student)
        <option
            {{ (old('student') == $student->id) ? 'selected' : ($old_student_id == $student->id ? 'selected' : '') }}
            value="{{ $student->id }}">
            {{ $student->name }}
        </option>
    @endforeach
</select>
