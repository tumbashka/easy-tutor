@props([
    'lesson' => null,
])
<tr>
    <td style="width: 95px;">
        <a href="{{ route('schedule.lesson.edit', ['day' => $lesson->date, 'lesson' => $lesson->id]) }}"
           class="link-dark link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">
            {{ $lesson->start->format('H:i') }}-{{ $lesson->end->format('H:i') }}
        </a>
    </td>
    <td class="text-start">
        {{ $lesson->student_name }}
    </td>
    <td style="width: 35px;">
        <a href="{{ route('schedule.lesson.edit', ['day' => $lesson->date, 'lesson' => $lesson->id]) }}"
           title="Редактировать">
            <i class="fa-solid link-primary fa-pen-to-square fa-xl"></i>
        </a>
    </td>
    <td style="width: 35px;">
        <a href="{{ route('schedule.lesson.change_status', ['day' => $lesson->date, 'lesson' => $lesson->id]) }}"
           title="Отмена">
            @if($lesson->is_canceled)
                <i class="fa-solid fa-up-from-bracket link-primary fa-xl"></i>
            @else
                <i class="fa-solid fa-down-to-bracket link-primary fa-xl"></i>
            @endif
        </a>
    </td>
    <td style="width: 35px;">
        @if($lesson->note)
            <a href="{{ route('schedule.lesson.edit', ['day' => $lesson->date, 'lesson' => $lesson->id]) }}">
                <i class="fa-duotone fa-cat link-primary fa-xl"></i>
            </a>
        @endif
    </td>
    <td style="width: 74px;" class="text-end align-self-center">
        {{ $lesson->price }}
        <livewire:payment-switcher :lesson_id="$lesson->id" :is-paid="$lesson->is_paid"/>
    </td>
</tr>
