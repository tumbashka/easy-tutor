<template>
    <div class="time-widget d-flex align-center text-white">
        <!-- Левая колонка: текущее время -->
        <div class="current-time me-3">
            {{ formattedNow }}
        </div>

        <!-- Правая колонка: информация о занятиях -->
        <div class="lessons-info d-flex flex-column">
            <div v-if="currentLesson" class="lesson-info">
                {{ currentLesson.start }} - {{ currentLesson.student_name }} (осталось {{ remainingCurrent }})
            </div>
            <div v-if="nextLesson" class="lesson-info">
                {{ nextLesson.start }} - {{ nextLesson.student_name }} (через {{ untilNext }})
            </div>
            <div v-if="!currentLesson && !nextLesson" class="lesson-info">
                Уроков нет
            </div>
        </div>
    </div>
</template>

<script>
import dayjs from 'dayjs';

export default {
    name: 'TimeWidget',
    props: {
        lessons: {
            type: [Array, Object],
            required: true,
        },
    },
    data() {
        return {
            now: dayjs(),
        };
    },
    computed: {
        lessonsArray() {
            return Array.isArray(this.lessons) ? this.lessons : Object.values(this.lessons);
        },
        currentLesson() {
            return this.lessonsArray.find(lesson => {
                if (lesson.is_canceled) return false;

                // Приводим дату урока к формату YYYY-MM-DD
                const lessonDate = dayjs(lesson.date).format('YYYY-MM-DD');
                const start = dayjs(`${lessonDate}T${lesson.start}`);
                const end = dayjs(`${lessonDate}T${lesson.end}`);

                return this.now.isAfter(start) && this.now.isBefore(end);
            });
        },
        nextLesson() {
            const upcoming = this.lessonsArray
                .filter(lesson => {
                    if (lesson.is_canceled) return false;

                    const lessonDate = dayjs(lesson.date).format('YYYY-MM-DD');
                    const start = dayjs(`${lessonDate}T${lesson.start}`);

                    return start.isAfter(this.now);
                })
                .sort((a, b) => {
                    const aStart = dayjs(`${dayjs(a.date).format('YYYY-MM-DD')}T${a.start}`).valueOf();
                    const bStart = dayjs(`${dayjs(b.date).format('YYYY-MM-DD')}T${b.start}`).valueOf();
                    return aStart - bStart;
                });

            return upcoming[0] || null;
        },
        remainingCurrent() {
            if (!this.currentLesson) return '';
            const lessonDate = dayjs(this.currentLesson.date).format('YYYY-MM-DD');
            const end = dayjs(`${lessonDate}T${this.currentLesson.end}`);
            const diff = end.diff(this.now, 'minute');
            const hours = Math.floor(diff / 60);
            const minutes = diff % 60;
            return hours > 0 ? `${hours} ч. ${minutes} мин.` : `${minutes} мин.`;
        },
        untilNext() {
            if (!this.nextLesson) return '';
            const lessonDate = dayjs(this.nextLesson.date).format('YYYY-MM-DD');
            const start = dayjs(`${lessonDate}T${this.nextLesson.start}`);
            const diff = start.diff(this.now, 'minute');
            const hours = Math.floor(diff / 60);
            const minutes = diff % 60;
            return hours > 0 ? `${hours} ч. ${minutes} мин.` : `${minutes} мин.`;
        },
        formattedNow() {
            return this.now.format('HH:mm:ss');
        },
    },
    methods: {
        updateNow() {
            this.now = dayjs();
        },
        scheduleMidnightUpdate() {
            const msUntilMidnight = dayjs().endOf('day').diff(this.now, 'millisecond') + 1000;
            setTimeout(() => {
                this.updateNow();
                this.scheduleMidnightUpdate();
            }, msUntilMidnight);
        },
    },
    mounted() {
        setInterval(this.updateNow, 1000);
        this.scheduleMidnightUpdate();
    },
};
</script>

<style scoped>
.time-widget {
    min-width: 250px; /* можно регулировать */
    display: flex;
    align-items: center;
}

.current-time {
    font-weight: bold;
    font-size: 1.4rem;
    min-width: 80px; /* фиксированная ширина для времени */
    text-align: center;
}

.lessons-info {
    display: flex;
    flex-direction: column;
    justify-content: center;
    flex-grow: 1; /* занимает оставшееся место */
}

.lesson-info {
    font-size: 0.85rem;
    line-height: 1.2rem;
}
</style>
