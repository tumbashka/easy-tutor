@props([
    'weekDays' => [],
    'lessonsOnDays' => [],
])
<div class="row justify-content-center ">
    @foreach($weekDays as $dayIndex => $weekDay)
        <x-week-day
            :lessons="$lessonsOnDays[$dayIndex]"
            :dayIndex="$dayIndex"
            :weekDay="$weekDay"
        />
    @endforeach
</div>
