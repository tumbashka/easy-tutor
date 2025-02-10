@props([
    'name' => 'myChartDoughnut',
    'labels' => [],
    'numbers' => [],
    'colors' => [],
])
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('{{ $name }}').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: {{ \Illuminate\Support\Js::from($labels) }},
                datasets: [{
                    label: 'Доходы',
                    data: {{ \Illuminate\Support\Js::from($numbers) }},
                    backgroundColor: {{ \Illuminate\Support\Js::from($colors) }},
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
            },
        });
    });
</script>
