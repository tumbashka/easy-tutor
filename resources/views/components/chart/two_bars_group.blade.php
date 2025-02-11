@props([
    'name' => 'myChart',
    'labels' => [],
    'first_name' => '',
    'second_name' => '',
    'first_data' => [],
    'second_data' => [],
    'y_name' => '',
])

<div class="chart-container ">
    <canvas id="{{ $name }}" class="chart"></canvas>
</div>

@push('js')
    <x-script.two_bars_group
        :name="$name"
        :labels="$labels"
        :first_name="$first_name"
        :second_name="$second_name"
        :first_data="$first_data"
        :second_data="$second_data"
        :y_name="$y_name"
    />
@endpush
