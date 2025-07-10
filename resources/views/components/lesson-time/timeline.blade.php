@props([
    'lessonTimes' => [],
    'selectedDay' => 0,
    'newLesson' => null,
    'studentName' => 'Новое занятие',
])

<div class="timeline-container">
    <div class="timeline-title">Расписание</div>
    <svg class="timeline-svg" width="100%" height="600"></svg>
</div>

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
            stroke-width: 1;
            opacity: 1;
            transition: opacity 0.2s;
        }
        .new-slot:hover {
            opacity: 1;
        }
        .conflict-slot {
            fill: #dc3545;
            opacity: 1;
            transition: opacity 0.2s;
            animation: pulse 1s infinite;
        }
        .conflict-slot:hover {
            opacity: 1;
        }
        @keyframes pulse {
            0% { opacity: 0.9; }
            50% { opacity: 0.7; }
            100% { opacity: 0.9; }
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
        .time-label.combined {
        }
    </style>
@endpushonce

@pushonce('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const lessonTimes = @json($lessonTimes);
            let selectedDay = @json($selectedDay);
            let newLesson = @json($newLesson);
            const studentName = @json($studentName);
            const svg = document.querySelector('.timeline-svg');

            function renderTimeline() {
                const dayLessonTimes = lessonTimes.filter(lt => lt.week_day === selectedDay);
                const start = newLesson?.start;
                const end = newLesson?.end;

                const height = svg.getBoundingClientRect().height;
                let earliestHour = Infinity;
                let latestHour = -Infinity;

                // Определение временных границ
                dayLessonTimes.forEach(lt => {
                    const [startH, startM] = lt.start.split(':').map(Number);
                    const [endH, endM] = lt.end.split(':').map(Number);
                    earliestHour = Math.min(earliestHour, startH + startM / 60);
                    latestHour = Math.max(latestHour, endH + endM / 60);
                });
                if (start && end) {
                    const [startH, startM] = start.split(':').map(Number);
                    const [endH, endM] = end.split(':').map(Number);
                    earliestHour = Math.min(earliestHour, startH + startM / 60);
                    latestHour = Math.max(latestHour, endH + endM / 60);
                }
                if (earliestHour === Infinity) earliestHour = 8;
                if (latestHour === -Infinity) latestHour = 24;
                const totalHours = latestHour - earliestHour || 1;
                const hourHeight = height / totalHours;

                svg.innerHTML = '';
                svg.innerHTML += `<line x1="60" y1="0" x2="60" y2="${height}" stroke="#ced4da" stroke-width="2"/>`;

                // Метки времени
                let labelTimes = [];
                for (let hour = Math.floor(earliestHour); hour <= Math.ceil(latestHour); hour++) {
                    if (hour >= earliestHour && hour <= latestHour) {
                        let timeStr = `${hour.toString().padStart(2, '0')}:00`;
                        let time = new Date(`2025-05-26 ${timeStr}`);
                        let y = (hour - earliestHour) * hourHeight;
                        labelTimes.push({ time, timeStr, y, isHour: true });
                    }
                }
                dayLessonTimes.forEach(lt => {
                    let startTime = new Date(`2025-05-26 ${lt.start}`);
                    let endTime = new Date(`2025-05-26 ${lt.end}`);
                    let startY = (startTime.getHours() + startTime.getMinutes() / 60 - earliestHour) * hourHeight;
                    let endY = (endTime.getHours() + endTime.getMinutes() / 60 - earliestHour) * hourHeight;
                    labelTimes.push({ time: startTime, timeStr: lt.start, y: startY, isHour: false });
                    labelTimes.push({ time: endTime, timeStr: lt.end, y: endY, isHour: false });
                });
                if (start && end) {
                    let startTime = new Date(`2025-05-26 ${start}`);
                    let endTime = new Date(`2025-05-26 ${end}`);
                    let startY = (startTime.getHours() + startTime.getMinutes() / 60 - earliestHour) * hourHeight;
                    let endY = (endTime.getHours() + endTime.getMinutes() / 60 - earliestHour) * hourHeight;
                    labelTimes.push({ time: startTime, timeStr: start, y: startY, isHour: false });
                    labelTimes.push({ time: endTime, timeStr: end, y: endY, isHour: false });
                }

                labelTimes.sort((a, b) => a.time - b.time);

                // Фильтрация меток для избежания наложения
                const getTimeDifferenceInMinutes = (time1, time2) => Math.abs((time1 - time2) / 60000);
                let finalLabels = [];
                let lastLabel = null;

                labelTimes.forEach(label => {
                    if (lastLabel) {
                        const diffMinutes = getTimeDifferenceInMinutes(label.time, lastLabel.time);
                        const diffY = Math.abs(label.y - lastLabel.y);
                        const minTimeDistance = 10;
                        const minYDistance = 10;

                        if (diffMinutes < minTimeDistance || diffY < minYDistance) {
                            if (!label.isHour && lastLabel.isHour) {
                                lastLabel = label;
                            } else {
                                lastLabel.timeStr = `${lastLabel.timeStr}`;
                                lastLabel.y = (lastLabel.y + label.y) / 2;
                                lastLabel.isCombined = true;
                            }
                        } else {
                            finalLabels.push(lastLabel);
                            lastLabel = label;
                        }
                    } else {
                        lastLabel = label;
                    }
                });

                if (lastLabel) {
                    finalLabels.push(lastLabel);
                }

                finalLabels = finalLabels.filter((label, index, self) =>
                    index === 0 || label.time.getTime() !== self[index - 1].time.getTime()
                );

                // Проверка, находится ли метка в занятом слоте
                const isLabelInSlot = labelTime => dayLessonTimes.some(lt => {
                    const slotStart = new Date(`2025-05-26 ${lt.start}`).getTime();
                    const slotEnd = new Date(`2025-05-26 ${lt.end}`).getTime();
                    return labelTime.getTime() >= slotStart && labelTime.getTime() <= slotEnd;
                });

                // Отображение меток времени
                finalLabels.forEach(label => {
                    const isOccupied = isLabelInSlot(label.time);
                    const textColor = isOccupied ? 'rgb(161, 47, 74)' : '#495057';
                    const className = label.isCombined ? 'time-label combined' : 'time-label';
                    svg.innerHTML += `<line x1="50" y1="${label.y}" x2="70" y2="${label.y}" stroke="#ced4da" stroke-width="1"/>`;
                    svg.innerHTML += `<text x="5" y="${label.y + 5}" class="${className}" style="fill: ${textColor};">${label.timeStr}</text>`;
                });

                // Занятые слоты
                dayLessonTimes.forEach(lt => {
                    let startTime = new Date(`2025-05-26 ${lt.start}`);
                    let endTime = new Date(`2025-05-26 ${lt.end}`);
                    let startY = (startTime.getHours() + startTime.getMinutes() / 60 - earliestHour) * hourHeight;
                    let endY = (endTime.getHours() + endTime.getMinutes() / 60 - earliestHour) * hourHeight;
                    let minHeight = 12 + 15 + 10;
                    let slotHeight = Math.max(endY - startY, minHeight);

                    svg.innerHTML += `<rect x="80" y="${startY}" width="120" height="${slotHeight}" rx="8" class="slot"/>`;
                    svg.innerHTML += `<text x="90" y="${startY + 12}" class="slot-text">${lt.student.name}</text>`;
                    svg.innerHTML += `<text x="90" y="${startY + slotHeight - 3}" font-size="12" class="slot-text">${lt.start} - ${lt.end}</text>`;
                });

                // Текущий выбор
                if (start && end) {
                    let startTime = new Date(`2025-05-26 ${start}`);
                    let endTime = new Date(`2025-05-26 ${end}`);
                    let startY = (startTime.getHours() + startTime.getMinutes() / 60 - earliestHour) * hourHeight;
                    let endY = (endTime.getHours() + endTime.getMinutes() / 60 - earliestHour) * hourHeight;
                    let minHeight = 12 + 15 + 10;
                    let slotHeight = Math.max(endY - startY, minHeight);

                    const hasConflict = dayLessonTimes.some(lt => start < lt.end && end > lt.start);
                    const slotClass = hasConflict ? 'conflict-slot' : 'new-slot';
                    svg.innerHTML += `<rect x="80" y="${startY}" width="120" height="${slotHeight}" rx="8" class="${slotClass}"/>`;
                    svg.innerHTML += `<text x="90" y="${startY + 12}" class="slot-text">${studentName}</text>`;
                    svg.innerHTML += `<text x="90" y="${startY + slotHeight - 3}" font-size="12" class="slot-text">${start} - ${end}</text>`;
                }
            }

            // Инициализация
            renderTimeline();

            // Обработка события обновления нового занятия
            document.addEventListener('new-lesson-updated', (event) => {
                newLesson = event.detail;
                renderTimeline();
            });

            // Обработка события смены дня
            document.addEventListener('day-changed', (event) => {
                selectedDay = event.detail.day;
                renderTimeline();
            });
        });
    </script>
@endpushonce
