import './bootstrap';

import jQuery from 'jquery';
window.$ = jQuery;

import Chart from 'chart.js/auto';
window.Chart = Chart;
import ChartDataLabels from 'chartjs-plugin-datalabels';
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

import flatpickr from "flatpickr";
import { Russian } from "flatpickr/dist/l10n/ru.js";
import monthSelectPlugin from "flatpickr/dist/plugins/monthSelect";

flatpickr(".datepicker", {
    dateFormat: "Y-m-d",
    disableMobile: "true",
    locale: Russian,
});

flatpickr(".month-picker", {
    locale: Russian,
    disableMobile: "true",
    plugins: [
        new monthSelectPlugin({
            shorthand: true,
            dateFormat: "Y-m",
            altFormat: "F Y",
        })
    ]
});

flatpickr(".range-picker", {
    locale: Russian,
    mode: "range",
    dateFormat: "Y-m-d",
});

flatpickr(".month-range-picker", {
    locale: Russian,
    mode: "range",
    dateFormat: "Y-m",
    plugins: [new monthSelectPlugin({
        dateFormat: "Y-m",
    })]
});







