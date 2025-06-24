@props([
    'lessonSlot' => null,
])
@php
    $highlight = match ($lessonSlot['status']){
        'free' => 'highlight-element-green',
        'trial' => 'highlight-element-yellow',
        default => '',
    };
@endphp
<tr class="{{ $highlight }}">
    <td style="width: 95px;">
        <a class="link-dark link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">
            {{ getHiFormatTime($lessonSlot['start']) }}-{{ getHiFormatTime($lessonSlot['end']) }}
        </a>
    </td>
    @if(isset($lessonSlot['student']))
        <td class="text-start">Занято</td>
    @else
        <td class="text-start">
            <p class="m-0 p-0">{{ getLessonType($lessonSlot['type']) }}</p>
            <p class="m-0 p-0">{{ getLessonStatus($lessonSlot['status']) }}</p>
        </td>
    @endif
</tr>
