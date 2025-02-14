@props([
    'all_lesson_slots_on_days' => [],
])
<div class="row justify-content-center ">
    @foreach($all_lesson_slots_on_days as $day_index => $all_lesson_slots_on_day)
        <x-free-time.week-day-shared
            :$day_index
            :$all_lesson_slots_on_day
        />
    @endforeach
</div>
