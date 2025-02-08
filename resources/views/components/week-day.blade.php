@props([
    'weekDay' => now(),
    'dayIndex' => null,
    'lessons' => null,
])
<div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xxl-4 mb-3">
    <table class="rounded-table table table-highlighted shadow table-bordered table-hover table-sm mb-0">
        <thead>
        <tr class="text-center table-info ">
            <th colspan="4">
                <h2 class="h5 fw-normal text-center mb-1 text-white">
                    {{ getShortDayName($dayIndex) }}. {{ $weekDay->translatedFormat('d F') }}
                    <a href="{{ route('schedule.show',$weekDay->format('Y-m-d')) }}">
                        <i class="link-light fa-light fa-pen-to-square "></i>
                    </a>
                </h2>
            </th>
        </tr>
        </thead>
        <tbody class="text-center align-middle">
        @php $empty = true; @endphp
        @if(count($lessons))
            @foreach($lessons as $lesson)
                @if(!$lesson->is_canceled)
                    @php $empty = false; @endphp
                    <x-lesson.table-row :lesson="$lesson"/>
                @endif
            @endforeach
        @endif
        @if($empty)
            <tr style="height: 10px;">
                <td colspan="2">
                </td>
                <td colspan="2">
                </td>
            </tr>
            <tr style="height: 60px;">
                <td colspan="4" class="text-center align-content-center">
                    <p class="m-0">Занятий нет</p>
                </td>
            </tr>
            <tr style="height: 10px;">
                <td colspan="2">
                </td>
                <td colspan="2">
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>
