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
    <td class="text-start">{{ $lesson->student_name }}</td>
    <td style="width: 35px;">
        <a href="{{ route('schedule.lesson.edit', ['day' => $lesson->date, 'lesson' => $lesson->id]) }}">
            <i class="fa-solid link-info fa-pen-to-square fa-xl"></i>
        </a>
    </td>
    <td style="width: 35px;">
        <a href="{{ route('schedule.lesson.change', ['day' => $lesson->date, 'lesson' => $lesson->id]) }}">
            @if($lesson->is_canceled)
                <i class="fa-solid fa-trash-arrow-up link-info fa-xl"></i>
            @else
                <i class="fa-solid fa-ban link-info fa-xl"></i>
            @endif
        </a>
    </td>
    <td style="width: 35px;">
        @if($lesson->note)
            <a href="{{ route('schedule.lesson.edit', ['day' => $lesson->date, 'lesson' => $lesson->id]) }}">
                <i class="fa-duotone fa-cat link-info fa-xl"></i>
            </a>
        @endif
    </td>
    <td style="width: 74px;" class="text-end align-self-center">
        {{ $lesson->price }}
        <input class="form-check-input m-0 " style="width: 24px; height: 24px;" type="checkbox" value="">
    </td>
</tr>
