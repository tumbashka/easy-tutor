@props([
    'lesson' => null,
    'occupiedSlots' => null,
    'students' => null,
])

<x-form.input-error-alert :name="'start'"/>
<x-form.input-error-alert :name="'end'"/>

<div class="input-group">
    <span class="input-group-text">С</span>
    <input name="start" type="time" class="form-control" id="start-time"
           value="{{ old('start', $lesson ? $lesson->start->format('H:i') : '') }}"/>
    <span class="input-group-text">До</span>
    <input name="end" type="time" class="form-control" id="end-time"
           value="{{ old('end', $lesson ? $lesson->end->format('H:i') : '') }}"/>
</div>
<div class="mt-3 text-center">
    <span id="duration-label">1 ч.</span>
    <input type="range" name="duration" id="duration" min="5" max="240" step="5" value="{{ old('duration', $lesson ? min($lesson->end->diffInMinutes($lesson->start), 240) : 60) }}"
           class="form-range"/>
</div>

@pushonce('css')
    <style>
        .form-range::-webkit-slider-thumb {
            background: #0d6efd; /* Bootstrap primary color */
        }
        .form-range::-moz-range-thumb {
            background: #0d6efd;
        }
        .is-invalid ~ .invalid-feedback {
            display: block;
        }
    </style>
@endpushonce

@pushonce('js')
    <script>
        // Инициализация элементов
        const startTimeInput = document.getElementById('start-time');
        const durationInput = document.getElementById('duration');
        const endTimeInput = document.getElementById('end-time');
        const durationLabel = document.getElementById('duration-label');
        const studentSelect = document.getElementById('student');
        const priceInput = document.getElementById('price');
        let isEndTimeEditing = false;
        let isPriceCustom = !!priceInput?.value; // Пользовательская цена при загрузке

        // Проверка доступности элементов
        if (!startTimeInput || !durationInput || !endTimeInput || !durationLabel || !studentSelect) {
            console.error('Required elements not found:', {
                startTimeInput, durationInput, endTimeInput, durationLabel, studentSelect, priceInput
            });
        }
        if (!priceInput) {
            console.warn('Price input not found. Cost calculation will be skipped.');
        }

        // Обработчик для TomSelect
        function addStudentSelectListener() {
            if (studentSelect.tomselect) {
                studentSelect.tomselect.on('change', () => {
                    isPriceCustom = false; // Сбрасываем пользовательскую цену
                    updatePrice();
                    updateTimeline();
                });
            } else {
                studentSelect.addEventListener('change', () => {
                    isPriceCustom = false; // Сбрасываем пользовательскую цену
                    updatePrice();
                    updateTimeline();
                });
            }
        }

        // Добавляем слушатели после загрузки DOM
        document.addEventListener('DOMContentLoaded', () => {
            addStudentSelectListener();
            if (startTimeInput) {
                startTimeInput.addEventListener('change', () => {
                    isPriceCustom = false; // Сбрасываем пользовательскую цену
                    updateEndTime();
                    updatePrice();
                    updateTimeline();
                });
            }
            if (durationInput) {
                durationInput.addEventListener('input', () => {
                    isPriceCustom = false; // Сбрасываем пользовательскую цену
                    updateEndTime();
                    updatePrice();
                    updateTimeline();
                });
            }
            if (endTimeInput) {
                endTimeInput.addEventListener('input', () => {
                    isEndTimeEditing = true;
                    isPriceCustom = false; // Сбрасываем пользовательскую цену
                    updateDuration();
                    updatePrice();
                    updateTimeline();
                });
                endTimeInput.addEventListener('blur', () => {
                    isEndTimeEditing = false;
                });
            }
            if (priceInput) {
                priceInput.addEventListener('input', () => {
                    isPriceCustom = true; // Пользователь вручную изменил цену
                });
            }
        });

        // Слушатель для TomSelect
        document.addEventListener('tomselect:initialized', addStudentSelectListener);

        // Форматирование длительности
        function formatDuration(minutes) {
            const hours = Math.floor(minutes / 60);
            const remainingMinutes = minutes % 60;
            let result = '';
            if (hours > 0) {
                result += `${hours} ч.`;
            }
            if (remainingMinutes > 0) {
                result += (result ? ' ' : '') + `${remainingMinutes} мин`;
            }
            return result || '0 мин';
        }

        // Обновление времени окончания
        function updateEndTime() {
            if (isEndTimeEditing || !startTimeInput || !durationInput || !endTimeInput || !durationLabel) return;
            const start = startTimeInput.value;
            const duration = parseInt(durationInput.value);
            durationLabel.textContent = formatDuration(duration);
            if (start) {
                const [hours, minutes] = start.split(':').map(Number);
                const endDate = new Date();
                endDate.setHours(hours, minutes + duration, 0, 0);
                const endTime = endDate.toTimeString().slice(0, 5);
                endTimeInput.value = endTime;
                checkOccupiedSlots(start, endTime);
            }
        }

        // Обновление длительности
        function updateDuration() {
            if (!startTimeInput || !endTimeInput || !durationInput || !durationLabel) return;
            const start = startTimeInput.value;
            const end = endTimeInput.value;
            if (start && end) {
                const startDate = new Date(`1970-01-01T${start}:00`);
                const endDate = new Date(`1970-01-01T${end}:00`);
                const durationMinutes = (endDate - startDate) / (1000 * 60);

                if (durationMinutes <= 0) {
                    endTimeInput.classList.add('is-invalid');
                    if (!endTimeInput.nextElementSibling?.classList.contains('invalid-feedback')) {
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.textContent = 'Время окончания должно быть позже времени начала!';
                        endTimeInput.parentNode.appendChild(feedback);
                    }
                    durationInput.value = 5;
                    durationLabel.textContent = formatDuration(5);
                    updateEndTime();
                    updatePrice();
                    return;
                }

                const step = 5;
                let adjustedDuration = Math.round(durationMinutes / step) * step;
                adjustedDuration = Math.max(5, Math.min(240, adjustedDuration));

                durationInput.value = adjustedDuration;
                durationLabel.textContent = formatDuration(adjustedDuration);
                if (durationMinutes > 240) {
                    durationLabel.textContent = `${formatDuration(durationMinutes)}`;
                }
                checkOccupiedSlots(start, end);
                updatePrice();
            }
        }

        // Вычисление стоимости
        function updatePrice() {
            if (!studentSelect || !startTimeInput || !endTimeInput || !priceInput) return;
            if (isPriceCustom) return; // Не перезаписываем пользовательскую цену

            const selectedStudent = studentSelect.options[studentSelect.selectedIndex];
            const studentPrice = selectedStudent ? parseFloat(selectedStudent.getAttribute('data-price') || 0) : 0;
            const start = startTimeInput.value;
            const end = endTimeInput.value;

            if (studentPrice && start && end) {
                const startDate = new Date(`1970-01-01T${start}:00`);
                const endDate = new Date(`1970-01-01T${end}:00`);
                const durationMinutes = (endDate - startDate) / (1000 * 60);
                if (durationMinutes > 0) {
                    priceInput.value = (studentPrice * durationMinutes / 60).toFixed(0);
                } else {
                    priceInput.value = '';
                }
            } else {
                priceInput.value = '';
            }
        }

        // Обновление таймлайна
        function updateTimeline() {
            const start = startTimeInput.value;
            const end = endTimeInput.value;
            const selectedStudent = studentSelect.options[studentSelect.selectedIndex];
            const studentName = selectedStudent ? selectedStudent.textContent.trim() : '';

            if (start && end && studentName) {
                document.dispatchEvent(new CustomEvent('new-lesson-updated', {
                    detail: { start, end, student_name: studentName }
                }));
            } else {
                document.dispatchEvent(new CustomEvent('new-lesson-updated', {
                    detail: null
                }));
            }
        }

        // Проверка занятых слотов
        function checkOccupiedSlots(start, end) {
            if (!startTimeInput || !endTimeInput) return;

            // Очищаем предыдущие ошибки
            startTimeInput.classList.remove('is-invalid');
            endTimeInput.classList.remove('is-invalid');
            const existingFeedback = endTimeInput.nextElementSibling;
            if (existingFeedback?.classList.contains('invalid-feedback')) {
                existingFeedback.remove();
            }

            const occupiedSlots = @json($occupiedSlots);
            let isStartOccupied = false;
            let isEndOccupied = false;

            // Проверяем пересечение для start и end
            occupiedSlots.forEach(slot => {
                const slotStart = new Date(`1970-01-01T${slot.start}`).getTime();
                const slotEnd = new Date(`1970-01-01T${slot.end}`).getTime();
                const newStart = new Date(`1970-01-01T${start}`).getTime();
                const newEnd = new Date(`1970-01-01T${end}`).getTime();

                // Проверяем, попадает ли start в занятый слот
                if (newStart >= slotStart && newStart < slotEnd) {
                    isStartOccupied = true;
                }
                // Проверяем, попадает ли end в занятый слот
                if (newEnd > slotStart && newEnd <= slotEnd) {
                    isEndOccupied = true;
                }
                // Проверяем, пересекается ли новый слот с занятым
                if (newStart < slotEnd && newEnd > slotStart) {
                    isEndOccupied = true; // Для пересечения выделяем end
                }
            });

            // Применяем стили и добавляем надпись
            if (isStartOccupied) {
                startTimeInput.classList.add('is-invalid');
            }
            if (isEndOccupied) {
                endTimeInput.classList.add('is-invalid');
            }
            if (isStartOccupied || isEndOccupied) {
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = 'Выбранное время занято!';
                endTimeInput.parentNode.appendChild(feedback);
                // Отправляем событие конфликта
                document.dispatchEvent(new CustomEvent('time-conflict', {
                    detail: { start, end }
                }));
            }
        }

        // Инициализация
        document.addEventListener('DOMContentLoaded', () => {
            if (startTimeInput && endTimeInput && startTimeInput.value && endTimeInput.value) {
                updateDuration();
                updateTimeline();
                // Не вызываем updatePrice, чтобы сохранить пользовательскую цену
            } else if (startTimeInput && startTimeInput.value) {
                updateEndTime();
                updateTimeline();
                // Не вызываем updatePrice, чтобы сохранить пользовательскую цену
            }
        });
    </script>
@endpushonce
