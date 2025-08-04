import 'tom-select/dist/css/tom-select.bootstrap5.css';
import '../css/tom-select.css';
import TomSelect from 'tom-select';

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-tom-select]').forEach(function (element) {
        new TomSelect(element, {
            plugins: ['remove_button'],
            create: false,
            maxOptions: null,
            maxItems: null,
            searchField: 'text',
            render: {
                option: function (data, escape) {
                    var color = data.color;
                    var textColor = isDarkColor(color) ? '#fff' : '#000';
                    return '<div class="d-flex align-items-center" style="background-color: ' + escape(color) + '; color: ' + escape(textColor) + ';">' +
                        escape(data.text) + '</div>';
                },
                item: function (data, escape) {
                    var color = data.color;
                    var textColor = isDarkColor(color) ? '#fff' : '#000';
                    return '<div class="task-category-option" style="background-color: ' + escape(color) + '; color: ' + escape(textColor) + ';">' +
                        escape(data.text) + '</div>';
                },
                no_results: function (data, escape) {
                    return '<div class="no-results">Для "' + escape(data.input) + '" ничего не найдено</div>';
                }
            },
            onInitialize: function () {
                this.settings.load = function (query, callback) {
                    var options = [];
                    document.querySelectorAll('[data-tom-select] option').forEach(function (option) {
                        options.push({
                            text: option.innerText,
                            value: option.value,
                            color: option.dataset.color
                        });
                    });
                    callback(options);
                };
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-tom-select-single]').forEach(function (element) {
        new TomSelect(element, {
            plugins: ['remove_button'],
            create: false,
            maxOptions: null,
            maxItems: 1,
            searchField: 'text',
            render: {
                no_results: function (data, escape) {
                    return '<div class="no-results">Для "' + escape(data.input) + '" ничего не найдено</div>';
                }
            },
        });
    });
});

function isDarkColor(color) {
    // Преобразуем цвет в RGB
    var r = parseInt(color.substr(1, 2), 16);
    var g = parseInt(color.substr(3, 2), 16);
    var b = parseInt(color.substr(5, 2), 16);
    // Рассчитываем яркость по формуле
    var brightness = (r * 299 + g * 587 + b * 114) / 1000;
    // Возвращаем true, если яркость ниже порогового значения (128)
    return brightness < 170;
}


document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('[data-tom-select-group]').forEach(function (element) {
        var select = new TomSelect(element, {
            plugins: ['optgroup_select', 'remove_button'],
            optgroup_select: true,
            onChange: function (values) {
                let groups = {};
                this.items.forEach(item => {
                    let option = this.options[item];
                    if (option && option.dataset && option.dataset.group) {
                        groups[option.dataset.group] = groups[option.dataset.group] || [];
                        groups[option.dataset.group].push(option.value);
                    }
                });

                Object.keys(groups).forEach(group => {
                    let allSelected = groups[group].every(value => values.includes(value));
                    let groupOption = this.options['group-' + group];
                    if (groupOption) {
                        if (allSelected) {
                            if (!values.includes('group-' + group)) {
                                this.addItem('group-' + group);
                            }
                        } else {
                            this.removeItem('group-' + group);
                        }
                    }
                });
            }
        });

        select.on('item_add', function (value) {
            let option = select.options[value];
            if (option && option.value.startsWith('group-')) {
                let group = option.value.replace('group-', '');
                document.querySelectorAll('option[data-group="' + group + '"]').forEach(opt => {
                    if (opt && opt.dataset && opt.dataset.group) {
                        select.addItem(opt.value);
                    }
                });
            }
        });

        select.on('item_remove', function (value) {
            const option = select.options[value];
            if (option && option.value.startsWith('group-')) {
                const group = option.value.replace('group-', '');

                const studentsToRemove = [];
                document.querySelectorAll('option[data-group="' + group + '"]').forEach(opt => {
                    if (select.items.includes(opt.value)) {
                        studentsToRemove.push(opt.value);
                    }
                });

                studentsToRemove.forEach(student => {
                    select.removeItem(student);
                });

                const groupOption = select.options['group-' + group];
                if (groupOption) {
                    select.addOption(groupOption);
                }

                select.refreshItems();
            }
        });

        select.on('dropdown_open', function () {
            document.querySelectorAll('.class-option').forEach(opt => {
                select.addOption(opt);
            });
        });

        select.on('optgroup:select', function (optgroup) {
            let optgroupValue = optgroup.getAttribute('data-value');
        });
    });

    TomSelect.define('optgroup_select', function (options) {
        var self = this;
        self.hook('after', 'onOptionSelect', (value, originalEvent) => {
            if (originalEvent && originalEvent.target) {
                var optgroup = originalEvent.target.closest('optgroup');
                if (optgroup) {
                    self.trigger('optgroup:select', optgroup);
                }
            }
        });
    });
});
