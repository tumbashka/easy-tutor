@props([
    'allLessonSlotsOnWeekDays' => [],
])
<div class="row justify-content-center ">
    @foreach($allLessonSlotsOnWeekDays as $dayIndex => $allLessonSlotsOnDay)
        <x-free-time.week-day-shared
            :$dayIndex
            :$allLessonSlotsOnDay
        />
    @endforeach
</div>
