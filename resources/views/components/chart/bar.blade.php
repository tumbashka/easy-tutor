@props([
    'name' => 'myChart',
    'labels' => [],
    'numbers' => [],
    'label_data' => "",
])

<div class="chart-container ">
    <canvas id="{{ $name }}" class="chart"></canvas>
</div>

@push('js')
    <x-script.bar-chart :name="$name" :labels="$labels" :numbers="$numbers" :label_data="$label_data"/>
@endpush
