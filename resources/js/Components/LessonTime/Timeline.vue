<template>
    <v-card flat class="pa-4 pl-0 pr-2 rounded border border-opacity-25">
        <div class="text-subtitle-1 mb-4 text-center">Расписание ({{ weekDays[weekDay].title }})</div>
        <v-alert
            v-if="hasConflict"
            type="error"
            class="ma-1 w-100"
            density="compact"
            variant="tonal"
        >
            Выбранное время пересекается с уже существующим занятием.
        </v-alert>
        <v-timeline
            align="center"
            side="end"
            density="default"
            truncate-line="both"
            fill-dot
            line-thickness="3"
            size="20"
        >
            <v-timeline-item
                v-for="(label, idx) in labels"
                :key="idx"
                :dot-color="label.color"
                :icon="label.icon"
                :hide-dot="label.hideDot"
                class="pa-0 ma-0"
            >
                <template v-slot:opposite>
                    <div v-if="!label.hideDot" class="time-opposite pa-0 ma-0">
                        {{ label.timeStr }}
                    </div>
                </template>
                <div class="d-flex align-center">
                    <v-chip v-if="label.text" variant="flat"
                            density="compact"
                            :color="label.color">
                        {{ label.text }}
                    </v-chip>
                </div>
            </v-timeline-item>
        </v-timeline>
    </v-card>
</template>

<script>
export default {
    props: {
        lessonTimes: {type: Array, default: () => []},
        weekDay: {type: Number, default: 0},
        currentStart: {type: String, default: ''},
        currentEnd: {type: String, default: ''},
        student: {type: Object, default: null},
    },
    data() {
        return {
            weekDays: [
                {title: 'Понедельник', value: 0},
                {title: 'Вторник', value: 1},
                {title: 'Среда', value: 2},
                {title: 'Четверг', value: 3},
                {title: 'Пятница', value: 4},
                {title: 'Суббота', value: 5},
                {title: 'Воскресенье', value: 6},
            ],
        };
    },
    computed: {
        dayLessons() {
            return this.lessonTimes
                .filter(lt => lt.week_day === this.weekDay)
                .sort((a, b) => this.toMinutes(a.start) - this.toMinutes(b.start));
        },
        hasConflict() {
            if (!this.currentStart || !this.currentEnd) return false;
            const s = this.toMinutes(this.currentStart);
            const e = this.toMinutes(this.currentEnd);
            return this.dayLessons.some(lt => {
                const ls = this.toMinutes(lt.start);
                const le = this.toMinutes(lt.end);
                return s < le && e > ls;
            });
        },
        labels() {
            const labels = [];

            // Добавляем существующие занятия
            this.dayLessons.forEach(lt => {
                const startMin = this.toMinutes(lt.start);
                const endMin = this.toMinutes(lt.end);
                const duration = endMin - startMin;
                const durationText = this.formatDuration(duration);
                const studentName = lt.student?.name ?? 'Занято';

                // Начало
                labels.push({
                    minute: startMin,
                    timeStr: lt.start,
                    text: '',
                    color: 'grey-darken-1',
                    hideDot: false,
                    isConflict: false,
                });

                // Середина (скрытый узел)
                labels.push({
                    minute: startMin + duration / 2,
                    timeStr: this.fromMinutes(startMin + duration / 2),
                    text: `${studentName} - занятие (${durationText})`,
                    color: 'grey-darken-1',
                    icon: 'mdi-circle-small',
                    hideDot: true,
                    isConflict: false,
                });

                // Конец
                labels.push({
                    minute: endMin,
                    timeStr: lt.end,
                    text: '',
                    color: 'grey-darken-1',
                    hideDot: false,
                    isConflict: false,
                });
            });

            // Добавляем текущее занятие
            if (this.currentStart && this.currentEnd && this.student) {
                const startMin = this.toMinutes(this.currentStart);
                const endMin = this.toMinutes(this.currentEnd);
                const duration = endMin - startMin;
                const durationText = this.formatDuration(duration);
                const isConflict = this.hasConflict;
                const color = isConflict ? 'error' : 'success';
                const studentName = this.student.name || 'Новое занятие';

                // Начало
                labels.push({
                    minute: startMin,
                    timeStr: this.currentStart,
                    text: `Начало`,
                    color,
                    icon: isConflict ? 'mdi-alert' : 'mdi-check',
                    hideDot: false,
                    isConflict,
                    new: true,
                });

                // Середина (скрытый узел)
                labels.push({
                    minute: startMin + duration / 2,
                    timeStr: this.fromMinutes(startMin + duration / 2),
                    text: `${studentName} - занятие (${durationText})`,
                    color,
                    icon: 'mdi-circle-small',
                    hideDot: true,
                    isConflict,
                    new: true,
                });

                // Конец
                labels.push({
                    minute: endMin - 1,
                    timeStr: this.currentEnd,
                    text: `Конец`,
                    color,
                    icon: isConflict ? 'mdi-alert' : 'mdi-check',
                    hideDot: false,
                    isConflict,
                    class: 'name-pill',
                    new: true,
                });
            }

            // Сортировка по времени
            return labels.sort((a, b) => a.minute - b.minute);
        },
    },
    methods: {
        toMinutes(hhmm) {
            if (!hhmm) return 0;
            const [h, m] = hhmm.split(':').map(Number);
            return h * 60 + m;
        },
        fromMinutes(min) {
            const m = Math.max(0, Math.min(23 * 60 + 59, min));
            const hh = String(Math.floor(m / 60)).padStart(2, '0');
            const mm = String(m % 60).padStart(2, '0');
            return `${hh}:${mm}`;
        },
        formatDuration(minutes) {
            const h = Math.floor(minutes / 60);
            const mm = minutes % 60;
            return (h ? `${h} ч.` : '') + (h && mm ? ' ' : '') + (mm ? `${mm} мин.` : h ? '' : '0 мин.');
        },
    },
};
</script>

<style scoped>

.time-opposite {
    min-width: 64px;
    text-align: right;
    font-variant-numeric: tabular-nums;
}

.name-pill {
    padding: 2px 8px;
    border-radius: 9999px;
    background: rgba(161, 47, 74, 0.08);
}

</style>
