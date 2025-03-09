@props([
    'weekDays' => [],
    'lessonsOnDays' => null,
])
<div class="row justify-content-center ">
    @foreach($weekDays as $dayIndex => $weekDay)
        <x-week-day
            :lessons="$lessonsOnDays->get($dayIndex)"
            :dayIndex="$dayIndex"
            :weekDay="$weekDay"
        />
    @endforeach
</div>
