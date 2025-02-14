@props([
    'day_index' => null,
    'all_lesson_slots_on_day' => null,
])
<div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xxl-4 mb-3">
    <table class="rounded-table table table-highlighted shadow table-bordered table-hover table-sm mb-0">
        <thead>
        <tr class="text-center table-info ">
            <th colspan="4">
                <h2 class="h5 fw-normal text-center mb-1 text-white">
                    {{ getDayName($day_index) }}
                </h2>
            </th>
        </tr>
        </thead>
        <tbody class="text-center align-middle">
        @if(count($all_lesson_slots_on_day))
            @foreach($all_lesson_slots_on_day as $lesson_slot)
                <x-free-time.table-row-shared :lesson_slot="$lesson_slot"/>
            @endforeach
        @endif
        </tbody>
    </table>
</div>
