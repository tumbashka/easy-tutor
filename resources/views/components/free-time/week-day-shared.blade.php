@props([
    'dayIndex' => null,
    'allLessonSlotsOnDay' => null,
])
<div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xxl-4 mb-3">
    <table class="rounded-table table table-highlighted shadow table-bordered table-hover table-sm mb-0">
        <thead>
        <tr class="text-center table-info ">
            <th colspan="4">
                <h2 class="h5 fw-normal text-center mb-1 text-white">
                    {{ getDayName($dayIndex) }}
                </h2>
            </th>
        </tr>
        </thead>
        <tbody class="text-center align-middle">
        @if(count($allLessonSlotsOnDay))
            @foreach($allLessonSlotsOnDay as $lessonSlot)
                <x-free-time.table-row-shared :lessonSlot="$lessonSlot"/>
            @endforeach
        @endif
        </tbody>
    </table>
</div>
