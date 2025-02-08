@props([
    'class' => '',
    'students' => null,
])
<div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xxl-4 mb-3">
    <table class="rounded-table shadow table table-bordered table-hover table-sm mb-0">
        <thead>
        <tr class="text-center table-info">
            <th colspan="9">
                <h2 class="h5 fw-normal text-center mb-1 text-white">
                    {{ $class }} класс - {{ count($students) }} чел.
                </h2>
            </th>
        </tr>
        </thead>
        <tbody class="text-center align-middle">
        <tr class="text-center fw-bolder">
            <td>Имя</td>
            <td>пн</td>
            <td>вт</td>
            <td>ср</td>
            <td>чт</td>
            <td>пт</td>
            <td>сб</td>
            <td>вс</td>
            <td><i class="fa-duotone fa-solid fa-ruble-sign"></i></td>
        </tr>
        @foreach($students as $student)
            <tr>
                <td>
                    <x-table.link
                        href="{{ route('student.show', $student['id']) }}">{{ $student['name'] }}</x-table.link>
                </td>
                @php
                    $lesson_days = [];
                    foreach($student['lesson_times'] as $key => $lesson){
                        $lesson_days[] = $lesson['week_day'];
                    }
                @endphp
                @for($i = 0; $i <= 6; $i++)
                    @if(in_array($i, $lesson_days))
                        <x-table.day :check="true"/>
                    @else
                        <x-table.day/>
                    @endif
                @endfor
                <td>
                    {{ $student['price'] }}
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>
</div>
