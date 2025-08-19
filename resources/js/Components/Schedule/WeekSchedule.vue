<template>
    <v-container fluid class="relative">
        <v-row justify="center">
            <v-col
                v-for="(weekDay, dayIndex) in weekDays"
                :key="weekDay"
                :cols="colsForBreakpoint"
                class="px-2 pt-1 pb-3"
            >
                <v-card class="rounded-lg elevation-4">
                    <v-card-title class="text-white bg-gradient-45 py-1">
                        <div class="d-flex align-center justify-space-between w-100">
                            <div>
                                {{ getShortDayName(dayIndex) }}. {{ formatDate(weekDay) }}
                            </div>
                            <v-btn
                                icon="mdi-pencil"
                                size="small"
                                variant="text"
                                @click="editDay(formatDate2(weekDay))"
                            />
                        </div>
                    </v-card-title>

                    <v-divider/>

                    <v-card-text class="px-3 py-0">
                        <v-table density="compact">
                            <tbody>
                            <template v-if="lessonsOnDays[dayIndex]?.length">
                                <tr
                                    v-for="lesson in lessonsOnDays[dayIndex]"
                                    :key="lesson.id"
                                >
                                    <td class="px-0">
                                        <v-btn
                                            class="px-0 text-subtitle-1"
                                            variant="text"
                                            density="compact"
                                            :to="`/schedule/${lesson.date}/lesson/${lesson.id}/edit`"
                                        >
                                            {{ lesson.start }}-{{ lesson.end }}
                                        </v-btn>
                                    </td>

                                    <td class="text-start px-0">
                                        <v-btn
                                            class="text-capitalize text-subtitle-1"
                                            variant="text"
                                            density="compact"
                                            :to="`/students/${lesson.student_id}`"
                                            @click="editStudent(lesson.student_id)"
                                        >
                                            {{ lesson.student_name }}
                                        </v-btn>
                                    </td>

                                    <td class="px-0">
                                        <v-chip v-if="lesson.subject_name" color="primary" size="small" label>
                                            {{ lesson.subject_name }}
                                        </v-chip>
                                    </td>

                                    <td style="width: 35px;" class="px-0">
                                        <v-tooltip
                                            v-if="lesson.note"
                                            activator="parent"
                                            location="top"
                                        >
                                            {{ lesson.note }}
                                        </v-tooltip>
                                        <v-icon
                                            v-if="lesson.note"
                                            icon="mdi-note-text"
                                            color="primary"
                                        />
                                    </td>

                                    <td class="text-end px-0 text-subtitle-1" style="min-width: 70px;">
                                        <div class="d-flex align-center justify-end gap-2">
                                            <span>{{ lesson.price }}</span>
                                            <v-checkbox
                                                v-model="lesson.is_paid"
                                                color="primary"
                                                density="compact"
                                                hide-details=""
                                                class="ma-0 pa-0 ms-1"
                                                @change="onPaymentChange(lesson)"
                                            />
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <tr v-else>
                                <td colspan="5" class="text-center text-subtitle-1">
                                    Занятий нет
                                </td>
                            </tr>


                            </tbody>
                        </v-table>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import dayjs from 'dayjs';
import 'dayjs/locale/ru';
import {router} from '@inertiajs/vue3';

dayjs.locale('ru');

export default {
    props: {
        weekDays: Array,
        lessonsOnDays: Object,
        weekOffset: Number,
    },
    data() {
        return {
            windowWidth: window.innerWidth,
        };
    },
    computed: {
        colsForBreakpoint() {
            if (this.windowWidth < 1000) return 12;
            if (this.windowWidth < 1300) return 6;
            return 4;
        },
    },
    mounted() {
        window.addEventListener('resize', this.handleResize);
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.handleResize);
    },
    methods: {
        async onPaymentChange(lesson) {
            if (lesson.is_paid) {
                // если галочка поставлена → ставим оплату
                await this.setPayment(lesson.id)
            } else {
                // если снята → отменяем оплату
                await this.unsetPayment(lesson.id)
            }
        },
        handleResize() {
            this.windowWidth = window.innerWidth;
        },
        formatDate(date) {
            return dayjs(date).format('D MMMM');
        },
        formatDate2(date) {
            return dayjs(date).format('YYYY-MM-DD');
        },
        getShortDayName(dayIndex) {
            return ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'][dayIndex] || '';
        },
        editDay(date) {
            router.visit(route('schedule.show', {day: date}));
        },
        editStudent(studentId) {
            if (studentId) {
                router.visit(route('students.show', {student: studentId}));
            }
        },
        setPayment(lessonId) {
            if (lessonId) {
                router.visit(route('schedule.lesson.set_payment', {lesson: lessonId}), {
                    preserveState: true,
                    preserveScroll: true,
                    replace: true,
                });
            }
        },
        unsetPayment(lessonId) {
            if (lessonId) {
                router.visit(route('schedule.lesson.unset_payment', {lesson: lessonId}), {
                    preserveState: true,
                    preserveScroll: true,

                    replace: true,
                });
            }
        },
    },
};
</script>
