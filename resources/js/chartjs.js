import '../css/chartjs.css';

import jQuery from 'jquery';
import Chart from 'chart.js/auto';
import ChartDataLabels from 'chartjs-plugin-datalabels';

window.$ = jQuery;

window.Chart = Chart;

window.Chart.register(ChartDataLabels);

Chart.defaults.set('plugins.datalabels', {
    // color: '#FE777B',
    anchor: 'end', // Position of the labels (start, end, center, etc.)
    align: 'end', // Alignment of the labels (start, end, center, etc.)
    color: 'white', // Color of the labels

    backgroundColor: '#3264b0',
    borderWidth: '2',
    borderRadius: '2',
    font: {
        weight: 'bold',
    },
    formatter: function (value, context) {
        return value; // Display the actual data value
    }
});
