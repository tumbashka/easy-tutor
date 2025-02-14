@props([
    'name' => 'myChart',
    'labels' => [],
    'numbers' => [],
    'label_data' => '',
])
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('{{ $name }}').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: '{{ $label_data }}',
                    data: {!! json_encode($numbers) !!},
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
