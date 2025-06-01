@props([
    'students' => null,
    'old_student_id' => null,
])
<select name="student" id="student" data-tom-select-single placeholder="Выберите ученика" class="w-full form-select">
    <option value="">Выберите ученика</option>
    @foreach($students as $student)
        <option value="{{ $student->id }}"
                data-color="{{ $student->color }}"
                data-price="{{ $student->price }}"
            @selected($old_student_id == $student->id)>
            {{ $student->name }}
        </option>
    @endforeach
</select>
