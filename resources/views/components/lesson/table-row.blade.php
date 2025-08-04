@props([
    'lesson' => null,
])
<tr>
    <td style="width: 92px;">
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
    <td>
        <span class="badge rounded-pill text-bg-success fw-normal text-white">{{ $lesson->subject_name }}</span>
    </td>
    <td style="width: 35px;">
        @if($lesson->note)
            <a href="{{ route('schedule.lesson.edit', ['day' => $lesson->date, 'lesson' => $lesson->id]) }}"
               class="tooltip-wrapper">
                <i class="fa-solid fa-cat link-primary fa-xl" aria-hidden="true"></i>
                <span class="tooltip-text">{{ $lesson->note }}</span>
            </a>
        @endif
    </td>
    <td style="min-width: 70px;" class="text-end">
        {{ $lesson->price }}
        <livewire:payment-switcher :lesson_id="$lesson->id" :is-paid="$lesson->is_paid"/>
    </td>
</tr>
