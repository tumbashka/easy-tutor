@props([
    'name' => 'myChart',
    'labels' => [],
    'numbers' => [],
])
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('{{ $name }}').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {{ \Illuminate\Support\Js::from($labels) }},
                datasets: [{
                    label: 'Доходы',
                    data: {{ \Illuminate\Support\Js::from($numbers) }},
                    backgroundColor: 'rgb(255,151,54)',
                    // borderColor: 'rgb(189,117,63)',
                    // borderWidth: 2
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            },
        });
    });
</script>
