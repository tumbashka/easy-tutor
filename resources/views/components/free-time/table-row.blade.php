@props([
    'lesson_slot' => null,
])
@php
    $highlight = '';
    $edit_url = '';
    if(isset($lesson_slot['status'])){
        $highlight = match ($lesson_slot['status']){
            'free' => 'highlight-element-green',
            'trial' => 'highlight-element-yellow',
            default => '',
        };
        $edit_url = route('free-time.edit', ['free_time' => $lesson_slot['id']]);
    }else{
        $edit_url = route('students.lesson-times.edit', [
            'student' => $lesson_slot['student']['id'],
            'lesson_time' => $lesson_slot['id'],
            'backUrl' => url()->current()
            ]);
    }
@endphp
<tr class="{{ $highlight }}">
    <td style="width: 95px;">
        <a
            {{--            href="{{ route('schedule.lesson_slot.edit', ['day' => $lesson_slot->date, 'lesson_slot' => $lesson_slot->id]) }}"--}}
            class="link-dark link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">
            {{ getHiFormatTime($lesson_slot['start']) }}-{{ getHiFormatTime($lesson_slot['end']) }}
        </a>
    </td>
    @if(isset($lesson_slot['student']))
        <td class="text-start">{{ $lesson_slot['student']['name'] }}</td>
    @else
        <td class="text-start">
            <p class="m-0 p-0">{{ getLessonType($lesson_slot['type']) }}</p>
            <p class="m-0 p-0">{{ getLessonStatus($lesson_slot['status']) }}</p>
        </td>
    @endif
    <td style="width: 30px;" class="text-end">
        <a href="{{ $edit_url }}">
            <i class="fa-solid fa-pen-to-square fa-xl"></i>
        </a>
    </td>
</tr>
