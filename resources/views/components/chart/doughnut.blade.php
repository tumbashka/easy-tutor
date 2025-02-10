@props([
    'name' => 'myChartDoughnut',
    'labels' => [],
    'numbers' => [],
    'colors' => [],

])

<div class="chart-container">
    <canvas id="{{ $name }}" class="chart"></canvas>
</div>

@push('js')
    <x-script.doughnut-chart :name="$name" :labels="$labels" :numbers="$numbers" :colors="$colors"/>
@endpush
