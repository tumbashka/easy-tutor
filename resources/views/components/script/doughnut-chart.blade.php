@props([
    'name' => 'myChartDoughnut',
    'labels' => [],
    'numbers' => [],
    'colors' => [],
])
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('{{ $name }}').getContext('2d');
        new Chart(ctx,{
            type: 'doughnut',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'Доходы',
                    data: {!! json_encode($numbers) !!},
                    backgroundColor: {!! json_encode($colors) !!},
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
            },
        });
    });
</script>
