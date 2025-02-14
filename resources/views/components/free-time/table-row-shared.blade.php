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
    }
@endphp
<tr class="{{ $highlight }}">
    <td style="width: 95px;">
        <a class="link-dark link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">
            {{ getHiFormatTime($lesson_slot['start']) }}-{{ getHiFormatTime($lesson_slot['end']) }}
        </a>
    </td>
    @if(isset($lesson_slot['student']))
        <td class="text-start">Занято</td>
    @else
        <td class="text-start">
            <p class="m-0 p-0">{{ getLessonType($lesson_slot['type']) }}</p>
            <p class="m-0 p-0">{{ getLessonStatus($lesson_slot['status']) }}</p>
        </td>
    @endif
</tr>
