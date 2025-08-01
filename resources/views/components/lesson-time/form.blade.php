@props([
    'student' => null,
    'lessonTime' => null,
    'lessonTimes' => null,
    'subjects' => null,
])

<div class="row">
    <div class="col-sm-8">
        <div class="row align-items-center">
            <div class="col-sm-3">
                <p class="mb-0">Предмет</p>
            </div>
            <div class="col-sm-9">
                <x-form.input-error-alert :name="'subject'"/>
                <select name="subject" class="form-select">
                    <option value="">
                        Не указан
                    </option>
                   @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}"
                            @selected(old('subject') === $subject->id || (!$lessonTime && $subject->is_default) || ($lessonTime?->subject?->id == $subject->id ))
                        >
                            {{ $subject->name }}
                        </option>
                   @endforeach
                </select>
            </div>
        </div>
        <hr>
        <div class="row align-items-center">
            <div class="col-sm-3">
                <p class="mb-0 required-input">День недели</p>
            </div>
            <div class="col-sm-9">
                <x-form.input-error-alert :name="'week_day'"/>
                <select name="week_day" class="form-select" id="week-day-select">
                    @for($i = 0; $i <= 6; $i++)
                        <option
                            {{ (old('week_day') == $i) ? 'selected' : (isset($lessonTime->week_day) && $i == $lessonTime->week_day ? 'selected' : '') }} value="{{ $i }}">{{ getDayName($i) }}
                        </option>
                    @endfor
                </select>
            </div>
        </div>
        <hr>
        <div class="row align-items-center">
            <div class="col-sm-3">
                <p class="mb-0 required-input">Время</p>
            </div>
            <div class="col-sm-9">
                <x-form.input-error-alert :name="'start'"/>
                <x-form.input-error-alert :name="'end'"/>
                <div class="input-group">
                    <span class="input-group-text">С</span>
                    <input name="start" type="time" class="form-control" id="start-time"
                           value="{{ old('start') ?? ($lessonTime != null ? $lessonTime->start->format('H:i') : '' )}}"/>
                </div>
                <div class="mt-3">
                    <label for="duration">Длительность: <span id="duration-label">1 ч.</span></label>
                    <input type="range" name="duration" id="duration" min="5" max="240" step="5"
                           value="{{ old('duration', $lessonTime ? min($lessonTime->end->diffInMinutes($lessonTime->start), 240) : 60) }}"
                           class="form-range"/>
                </div>
                <div class="input-group mt-3">
                    <span class="input-group-text">До</span>
                    <input name="end" type="time" class="form-control" id="end-time"
                           value="{{ old('end') ?? ($lessonTime != null ? $lessonTime->end->format('H:i') : '' ) }}"/>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <x-lesson-time.timeline
            :lessonTimes="$lessonTimes"
            :selectedDay="old('week_day', $lessonTime ? $lessonTime->week_day : 0)"
            :newLesson="$lessonTime ? ['start' => $lessonTime->start->format('H:i'), 'end' => $lessonTime->end->format('H:i')] : null"
            :studentName="$student ? $student->name : 'Новое занятие'"
        />
    </div>
</div>

@pushonce('css')
    <style>
        .form-range::-webkit-slider-thumb {
            background: #0d6efd;
        }
        .form-range::-moz-range-thumb {
            background: #0d6efd;
        }
        .is-invalid ~ .invalid-feedback {
            display: block;
        }
        input[type="time"] {
            height: calc(1.5em + 0.75rem + 2px);
        }
    </style>
@endpushonce

@pushonce('js')
    <script>
        const startTimeInput = document.getElementById('start-time');
        const durationInput = document.getElementById('duration');
        const endTimeInput = document.getElementById('end-time');
        const durationLabel = document.getElementById('duration-label');
        const weekDaySelect = document.getElementById('week-day-select');
        const lessonTimes = @json($lessonTimes);

        // Форматирование длительности
        function formatDuration(minutes) {
            const hours = Math.floor(minutes / 60);
            const remainingMinutes = minutes % 60;
            let result = '';
            if (hours > 0) result += `${hours} ч.`;
            if (remainingMinutes > 0) result += (result ? ' ' : '') + `${remainingMinutes} мин`;
            return result || '0 мин';
        }

        // Обновление времени окончания
        function updateEndTime() {
            const start = startTimeInput.value;
            const duration = parseInt(durationInput.value);
            durationLabel.textContent = formatDuration(duration);
            if (start) {
                const [hours, minutes] = start.split(':').map(Number);
                const endDate = new Date();
                endDate.setHours(hours, minutes + duration, 0, 0);
                endTimeInput.value = endDate.toTimeString().slice(0, 5);

                // Отправка события для обновления таймлайна
                document.dispatchEvent(new CustomEvent('new-lesson-updated', {
                    detail: { start: startTimeInput.value, end: endTimeInput.value }
                }));

                // Проверка конфликтов
                const selectedDay = parseInt(weekDaySelect.value);
                const dayLessonTimes = lessonTimes.filter(lt => lt.week_day === selectedDay);
                const hasConflict = dayLessonTimes.some(lt => start < lt.end && endTimeInput.value > lt.start);
                if (hasConflict) {
                    endTimeInput.classList.add('is-invalid');
                    const feedback = endTimeInput.nextElementSibling?.classList.contains('invalid-feedback') ?
                        endTimeInput.nextElementSibling :
                        document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'Выбранное время пересекается с другими занятиями!';
                    endTimeInput.parentNode.appendChild(feedback);
                } else {
                    endTimeInput.classList.remove('is-invalid');
                    if (endTimeInput.nextElementSibling?.classList.contains('invalid-feedback')) {
                        endTimeInput.nextElementSibling.remove();
                    }
                }
            }
        }

        // Обновление длительности
        function updateDuration() {
            const start = startTimeInput.value;
            const end = endTimeInput.value;
            if (start && end) {
                const startDate = new Date(`1970-01-01T${start}:00`);
                const endDate = new Date(`1970-01-01T${end}:00`);
                let durationMinutes = (endDate - startDate) / (1000 * 60);
                if (durationMinutes <= 0) {
                    endTimeInput.classList.add('is-invalid');
                    const feedback = endTimeInput.nextElementSibling?.classList.contains('invalid-feedback') ?
                        endTimeInput.nextElementSibling :
                        document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'Время окончания должно быть позже времени начала!';
                    endTimeInput.parentNode.appendChild(feedback);
                    durationInput.value = 5;
                    durationLabel.textContent = formatDuration(5);
                    updateEndTime();
                    return;
                }
                durationInput.value = Math.max(5, Math.min(240, Math.round(durationMinutes / 5) * 5));
                durationLabel.textContent = formatDuration(durationInput.value);

                // Отправка события для обновления таймлайна
                document.dispatchEvent(new CustomEvent('new-lesson-updated', {
                    detail: { start: startTimeInput.value, end: endTimeInput.value }
                }));

                // Проверка конфликтов
                const selectedDay = parseInt(weekDaySelect.value);
                const dayLessonTimes = lessonTimes.filter(lt => lt.week_day === selectedDay);
                const hasConflict = dayLessonTimes.some(lt => start < lt.end && end > lt.start);
                if (hasConflict) {
                    endTimeInput.classList.add('is-invalid');
                    const feedback = endTimeInput.nextElementSibling?.classList.contains('invalid-feedback') ?
                        endTimeInput.nextElementSibling :
                        document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'Выбранное время пересекается с другими занятиями!';
                    endTimeInput.parentNode.appendChild(feedback);
                } else {
                    endTimeInput.classList.remove('is-invalid');
                    if (endTimeInput.nextElementSibling?.classList.contains('invalid-feedback')) {
                        endTimeInput.nextElementSibling.remove();
                    }
                }
            }
        }

        // Слушатели событий
        document.addEventListener('DOMContentLoaded', () => {
            startTimeInput.addEventListener('change', updateEndTime);
            durationInput.addEventListener('input', updateEndTime);
            endTimeInput.addEventListener('input', updateDuration);
            weekDaySelect.addEventListener('change', () => {
                // Отправка события смены дня
                document.dispatchEvent(new CustomEvent('day-changed', {
                    detail: { day: parseInt(weekDaySelect.value) }
                }));
                // Отправка события для обновления таймлайна с текущими значениями времени
                document.dispatchEvent(new CustomEvent('new-lesson-updated', {
                    detail: { start: startTimeInput.value, end: endTimeInput.value }
                }));
            });
            if (startTimeInput.value && endTimeInput.value) updateDuration();
            else if (startTimeInput.value) updateEndTime();
        });
    </script>
@endpushonce
