@props([
    'name' => 'myChart',
    'labels' => [],
    'first_name' => '',
    'second_name' => '',
    'first_data' => [],
    'second_data' => [],
    'y_name' => '',
])
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('myChart').getContext('2d');
        const labels = {!! json_encode($labels) !!};
        const firstData = {!! json_encode($first_data) !!};
        const secondData = {!!json_encode($second_data) !!};


        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: '{{ $first_name }}',
                        data: firstData,
                        backgroundColor: 'rgba(255, 99, 132, 0.8)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        barThickness: 20,
                    },
                    {
                        label: '{{ $second_name }}',
                        data: secondData,
                        backgroundColor: 'rgba(54, 162, 235, 0.8)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        barThickness: 20,
                    }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        // title: {
                        //     display: true,
                        //     text: 'Дни'
                        // },
                        //
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: '{{ $y_name }}'
                        },
                        beginAtZero: true
                    }
                },
                datasets: {
                    bar: {
                        categoryPercentage: 0.8,
                        barPercentage: 0.9
                    }
                }
            }
        });
    });
</script>
