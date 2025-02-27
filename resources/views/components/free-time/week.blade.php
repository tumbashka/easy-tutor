@props([
    'week_days' => [],
    'all_lesson_slots_on_days' => [],
])
<div class="row justify-content-center ">
    @foreach($week_days as $dayIndex => $weekDay)
        <x-free-time.week-day
            :day-index="$dayIndex"
            :week-day="$weekDay"
            :all_lesson_slots_on_day="array_key_exists($dayIndex, $all_lesson_slots_on_days) ? $all_lesson_slots_on_days[$dayIndex] : null"
        />
    @endforeach
</div>
