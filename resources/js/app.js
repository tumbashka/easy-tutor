import './bootstrap';

import jQuery from 'jquery';
import Chart from 'chart.js/auto';
import ChartDataLabels from 'chartjs-plugin-datalabels';
import flatpickr from "flatpickr";
import {Russian} from "flatpickr/dist/l10n/ru.js";
import monthSelectPlugin from "flatpickr/dist/plugins/monthSelect";
import ClipboardJS from 'clipboard';

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
    disableMobile: "true",
    locale: Russian,
    mode: "range",
    dateFormat: "Y-m-d",
});

flatpickr(".month-range-picker", {
    disableMobile: "true",
    locale: Russian,
    mode: "range",
    dateFormat: "Y-m",
    plugins: [new monthSelectPlugin({
        dateFormat: "Y-m",
    })]
});


// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    const clipboard = new ClipboardJS('.btn-copy', {
        text: function(trigger) {
            return trigger.previousElementSibling.value;
        }
    });

    clipboard.on('success', function(e) {
        showCopyFeedback(e.trigger, true);
        e.clearSelection();
    });

    clipboard.on('error', function(e) {
        showCopyFeedback(e.trigger, false);
    });
});

// Функция для отображения обратной связи
function showCopyFeedback(element, success) {
    const originalHtml = element.innerHTML;
    element.innerHTML = success ?
        '<i class="fas fa-check"></i> Скопировано!' :
        '<i class="fas fa-times"></i> Ошибка!';
    element.classList.add(success ? 'copied-success' : 'copied-error');

    setTimeout(() => {
        element.innerHTML = originalHtml;
        element.classList.remove('copied-success', 'copied-error');
    }, 2000);
}


