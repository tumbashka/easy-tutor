@props([
    'lesson' => null,
])
<tr >
    <td style="width: 95px;">
        <a href="{{ route('schedule.lesson.edit', ['day' => $lesson->date, 'lesson' => $lesson->id]) }}"
           class="link-dark link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">
            {{ $lesson->start->format('H:i') }}-{{ $lesson->end->format('H:i') }}
        </a>
    </td>

    <td class="text-start">
        <a href="{{ $lesson->student_id != null ? route('students.show', ['student' => $lesson->student_id]) : '#' }}"
        class="link-dark link-offset-1 link-underline-opacity-0 link-underline-opacity-75-hover ">
            {{ $lesson->student_name }}
        </a>
    </td>
    <td style="width: 35px;">
        @if($lesson->note)
            <a href="{{ route('schedule.lesson.edit', ['day' => $lesson->date, 'lesson' => $lesson->id]) }}">
                <i class="fa-duotone fa-cat link-info fa-xl" aria-hidden="true"></i>
            </a>
        @endif
    </td>
    <td style="min-width: 70px;" class="text-end">
        {{ $lesson->price }}
        <livewire:payment-switcher :lesson_id="$lesson->id" :is-paid="$lesson->is_paid"  />
    </td>
</tr>
