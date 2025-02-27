import 'flatpickr/dist/flatpickr.css';
import 'flatpickr/dist/plugins/monthSelect/style.css';

import flatpickr from "flatpickr";
import {Russian} from "flatpickr/dist/l10n/ru.js";
import monthSelectPlugin from "flatpickr/dist/plugins/monthSelect";

flatpickr(".datepicker", {
    dateFormat: "Y-m-d",
    disableMobile: "true",
    locale: Russian,
});

flatpickr(".datetime-picker", {
    dateFormat: "Y-m-d H:i",
    disableMobile: "true",
    locale: Russian,
    enableTime: true,
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
