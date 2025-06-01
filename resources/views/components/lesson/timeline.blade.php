@props([
    'occupiedSlots' => [],
    'lesson' => null,
])
<div class="timeline-container">
    <div class="timeline-title">Расписание</div>
    <svg class="timeline-svg" width="100%" height="600"></svg>
</div>

@pushonce('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const occupiedSlots = @json($occupiedSlots);
            const lesson = @json($lesson ? [
                'start' => $lesson->start->format('H:i'),
                'end' => $lesson->end->format('H:i'),
                'student_name' => $lesson->student ? $lesson->student->name : 'Без имени'
            ] : null);
            const defaultStartHour = 8;
            const endHour = 24;
            const svg = document.querySelector('.timeline-svg');
            let newLesson = lesson; // Инициализируем с данными редактируемого занятия
            let conflictingSlots = new Set(); // Храним конфликтующие слоты

            function getStartHour() {
                let earliestHour = defaultStartHour;

                // Проверяем занятые слоты
                occupiedSlots.forEach(slot => {
                    const [hours] = slot.start.split(':').map(Number);
                    earliestHour = Math.min(earliestHour, hours);
                });

                // Проверяем новое занятие
                if (newLesson && newLesson.start) {
                    const [hours] = newLesson.start.split(':').map(Number);
                    earliestHour = Math.min(earliestHour, hours);
                }

                // Округляем вниз до целого часа
                return Math.floor(earliestHour);
            }

            function renderTimeline() {
                const height = svg.getBoundingClientRect().height;
                const startHour = getStartHour();
                const totalHours = endHour - startHour;
                const hourHeight = height / totalHours;
                svg.innerHTML = '';

                // Вертикальная линия
                svg.innerHTML += `<line x1="60" y1="0" x2="60" y2="${height}" stroke="#ced4da" stroke-width="2"/>`;

                // Сортировка слотов по времени начала
                occupiedSlots.sort((a, b) => a.start.localeCompare(b.start));

                // Объединение слотов
                const mergedSlots = [];
                occupiedSlots.forEach(slot => {
                    if (mergedSlots.length === 0) {
                        mergedSlots.push({ start: slot.start, end: slot.end, students: [slot.student_name] });
                    } else {
                        const lastSlot = mergedSlots[mergedSlots.length - 1];
                        const lastEnd = new Date(`2025-05-26 ${lastSlot.end}`).getTime();
                        const currentStart = new Date(`2025-05-26 ${slot.start}`).getTime();
                        const currentEnd = new Date(`2025-05-26 ${slot.end}`).getTime();

                        if (currentStart < lastEnd + 30 * 60000) {
                            const maxEnd = Math.max(lastEnd, currentEnd);
                            lastSlot.end = new Date(maxEnd).toTimeString().slice(0, 5);
                            lastSlot.students.push(slot.student_name);
                        } else {
                            mergedSlots.push({ start: slot.start, end: slot.end, students: [slot.student_name] });
                        }
                    }
                });

                // Подготовка меток времени
                let labelTimes = [];

                // Добавляем целые часы
                for (let hour = startHour; hour <= endHour; hour++) {
                    let timeStr = `${hour.toString().padStart(2, '0')}:00`;
                    let time = new Date(`2025-05-26 ${timeStr}`);
                    let y = (hour - startHour) * hourHeight;
                    labelTimes.push({ time: time, timeStr: timeStr, y: y, isHour: true });
                }

                // Добавляем времена начала и конца слотов
                mergedSlots.forEach(slot => {
                    let startTime = new Date(`2025-05-26 ${slot.start}`);
                    let endTime = new Date(`2025-05-26 ${slot.end}`);
                    let startY = (startTime.getHours() + startTime.getMinutes() / 60 - startHour) * hourHeight;
                    let endY = (endTime.getHours() + endTime.getMinutes() / 60 - startHour) * hourHeight;
                    labelTimes.push({ time: startTime, timeStr: slot.start, y: startY, isHour: false });
                    labelTimes.push({ time: endTime, timeStr: slot.end, y: endY, isHour: false });
                });

                // Добавляем метки для нового занятия
                if (newLesson) {
                    let startTime = new Date(`2025-05-26 ${newLesson.start}`);
                    let endTime = new Date(`2025-05-26 ${newLesson.end}`);
                    let startY = (startTime.getHours() + startTime.getMinutes() / 60 - startHour) * hourHeight;
                    let endY = (endTime.getHours() + endTime.getMinutes() / 60 - startHour) * hourHeight;
                    labelTimes.push({ time: startTime, timeStr: newLesson.start, y: startY, isHour: false });
                    labelTimes.push({ time: endTime, timeStr: newLesson.end, y: endY, isHour: false });
                }

                // Сортируем по времени
                labelTimes.sort((a, b) => a.time - b.time);

                // Функция для вычисления минут между двумя временами
                const getTimeDifferenceInMinutes = (time1, time2) => {
                    return Math.abs((time1 - time2) / 60000);
                };

                // Фильтрация меток
                let finalLabels = [];
                labelTimes.forEach(label => {
                    if (!label.isHour) {
                        finalLabels.push(label);
                    } else {
                        let hasCloseSlot = labelTimes.some(slotLabel => {
                            if (!slotLabel.isHour) {
                                const diff = getTimeDifferenceInMinutes(label.time, slotLabel.time);
                                return diff < 20;
                            }
                            return false;
                        });
                        if (!hasCloseSlot) {
                            finalLabels.push(label);
                        }
                    }
                });

                // Удаляем дубликаты
                finalLabels = finalLabels.filter((label, index, self) =>
                    index === 0 || label.time.getTime() !== self[index - 1].time.getTime()
                );

                // Функция для проверки, находится ли метка в занятом слоте
                const isLabelInSlot = (labelTime) => {
                    return mergedSlots.some(slot => {
                        const slotStart = new Date(`2025-05-26 ${slot.start}`).getTime();
                        const slotEnd = new Date(`2025-05-26 ${slot.end}`).getTime();
                        const labelTimeMs = labelTime.getTime();
                        return labelTimeMs >= slotStart && labelTimeMs <= slotEnd;
                    });
                };

                // Отображение меток времени с учётом занятости
                finalLabels.forEach(label => {
                    const isOccupied = isLabelInSlot(label.time);
                    const textColor = isOccupied ? 'rgb(161, 47, 74)' : '#495057';
                    svg.innerHTML += `<line x1="50" y1="${label.y}" x2="70" y2="${label.y}" stroke="#ced4da" stroke-width="1"/>`;
                    svg.innerHTML += `<text x="5" y="${label.y + 5}" class="time-label" style="fill: ${textColor};">${label.timeStr}</text>`;
                });

                // Отображение объединённых слотов
                mergedSlots.forEach((slot, index) => {
                    try {
                        let startTime = new Date(`2025-05-26 ${slot.start}`);
                        let endTime = new Date(`2025-05-26 ${slot.end}`);
                        let startY = (startTime.getHours() + startTime.getMinutes() / 60 - startHour) * hourHeight;
                        let endY = (endTime.getHours() + endTime.getMinutes() / 60 - startHour) * hourHeight;
                        let minHeight = 15 + slot.students.length * 12 + 12 + 5; // Отступ сверху + имена + время + отступ снизу
                        let slotHeight = Math.max(endY - startY, minHeight);

                        // Подсветка конфликтующего слота
                        const isConflicting = conflictingSlots.has(`${slot.start}-${slot.end}`);
                        const strokeColor = isConflicting ? '#ff0000' : 'rgb(141, 27, 54)';
                        const strokeWidth = isConflicting ? 3 : 1;

                        svg.innerHTML += `<rect x="80" y="${startY}" width="120" height="${slotHeight}" rx="8" class="slot" style="fill: rgb(161, 47, 74); stroke: ${strokeColor}; stroke-width: ${strokeWidth};"/>`;

                        // Позиция для имён учеников (вверху блока)
                        const studentTextY = startY + 15;
                        let studentText = slot.students.map((student, idx) =>
                            `<tspan x="90" dy="${idx === 0 ? 0 : 12}">${student}</tspan>`
                        ).join('');
                        svg.innerHTML += `<text x="90" y="${studentTextY}" class="slot-text">${studentText}</text>`;

                        // Позиция для времени (внизу слота)
                        const timeTextY = startY + slotHeight - 5;
                        const timeText = `${slot.start} - ${slot.end}`;
                        svg.innerHTML += `<text x="90" y="${timeTextY}" font-size="12" class="slot-text">${timeText}</text>`;
                    } catch (e) {
                        console.error('Error rendering slot:', slot, e);
                    }
                });

                // Отображение нового занятия
                if (newLesson) {
                    try {
                        let startTime = new Date(`2025-05-26 ${newLesson.start}`);
                        let endTime = new Date(`2025-05-26 ${newLesson.end}`);
                        let startY = (startTime.getHours() + startTime.getMinutes() / 60 - startHour) * hourHeight;
                        let endY = (endTime.getHours() + endTime.getMinutes() / 60 - startHour) * hourHeight;
                        let minHeight = 15 + 12 + 12 + 5; // Отступ сверху + имя + время + отступ снизу
                        let slotHeight = Math.max(endY - startY, minHeight);

                        // Проверка конфликта для нового слота
                        let hasConflict = false;
                        mergedSlots.forEach(slot => {
                            const slotStart = new Date(`2025-05-26 ${slot.start}`).getTime();
                            const slotEnd = new Date(`2025-05-26 ${slot.end}`).getTime();
                            const newStart = startTime.getTime();
                            const newEnd = endTime.getTime();
                            if (newStart < slotEnd && newEnd > slotStart) {
                                hasConflict = true;
                            }
                        });

                        // Цвет слота в зависимости от конфликта
                        const slotColor = hasConflict ? '#dc3545' : '#28a745';
                        const strokeColor = hasConflict ? '#c82333' : '#1e7e34';
                        const slotClass = hasConflict ? 'conflict-slot' : 'new-slot';

                        svg.innerHTML += `<rect x="80" y="${startY}" width="120" height="${slotHeight}" rx="8" class="${slotClass}" style="fill: ${slotColor}; stroke: ${strokeColor}; stroke-width: 1;"/>`;

                        // Имя ученика
                        const studentTextY = startY + 15;
                        svg.innerHTML += `<text x="90" y="${studentTextY}" class="slot-text">${newLesson.student_name}</text>`;

                        // Время
                        const timeTextY = startY + slotHeight - 5;
                        const timeText = `${newLesson.start} - ${newLesson.end}`;
                        svg.innerHTML += `<text x="90" y="${timeTextY}" font-size="12" class="slot-text">${timeText}</text>`;
                    } catch (e) {
                        console.error('Error rendering new lesson:', newLesson, e);
                    }
                }
            }

            // Обработка события конфликта
            document.addEventListener('time-conflict', (event) => {
                const { start, end } = event.detail;
                conflictingSlots.clear();
                occupiedSlots.forEach(slot => {
                    const slotStart = new Date(`2025-05-26 ${slot.start}`).getTime();
                    const slotEnd = new Date(`2025-05-26 ${slot.end}`).getTime();
                    const newStart = new Date(`2025-05-26 ${start}`).getTime();
                    const newEnd = new Date(`2025-05-26 ${end}`).getTime();

                    if (newStart < slotEnd && newEnd > slotStart) {
                        conflictingSlots.add(`${slot.start}-${slot.end}`);
                    }
                });
                renderTimeline();
            });

            // Обработка события нового занятия
            document.addEventListener('new-lesson-updated', (event) => {
                newLesson = event.detail;
                renderTimeline();
            });

            // Инициализация
            renderTimeline();
        });
    </script>
@endpushonce

@pushonce('css')
    <style>
        .timeline-container {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            height: 100%;
            font-family: 'Arial', sans-serif;
        }
        .timeline-svg {
            display: block;
            overflow: visible;
        }
        .slot {
            fill: rgb(161, 47, 74);
            stroke: rgb(141, 27, 54);
            stroke-width: 1;
            opacity: 0.9;
            transition: opacity 0.2s;
        }
        .slot:hover {
            opacity: 1;
        }
        .new-slot {
            fill: #28a745;
            stroke: #1e7e34;
            stroke-width: 2;
            opacity: 1;
            transition: opacity 0.2s;
        }
        .new-slot:hover {
            opacity: 1;
        }
        .conflict-slot {
            fill: #dc3545;
            stroke: #c82333;
            stroke-width: 2;
            opacity: 1;
            transition: opacity 0.2s;
        }
        .conflict-slot:hover {
            opacity: 1;
        }
        .timeline-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: rgb(161, 47, 74);
            margin-bottom: 15px;
            text-align: center;
        }
        .slot-text {
            pointer-events: none;
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            fill: #ffffff;
        }
        .slot-text[font-size="12"] {
            font-size: 12px;
        }
        .time-label {
            font-size: 14px;
        }
        .conflict-slot {
            animation: pulse 1s infinite;
        }
        @keyframes pulse {
            0% { opacity: 0.9; }
            50% { opacity: 0.7; }
            100% { opacity: 0.9; }
        }
    </style>
@endpushonce
