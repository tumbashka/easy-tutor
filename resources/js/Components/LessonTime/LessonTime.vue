<template>
    <v-row align="center" class="py-0 px-2">
        <v-col class="text-center text-sm-start py-0">
            <p class="text-subtitle-1">{{ getDayName(lesson.week_day) }}</p>

        </v-col>
        <v-col class="py-0">
            <v-chip v-if="lesson.subject?.name" color="primary">
                {{ lesson.subject?.name }}
            </v-chip>
        </v-col>
        <!-- Время -->
        <v-col class="py-0">
                <span class="text-body-1 font-weight-medium mr-2">С</span>
                <span class="text-body-1 font-weight-bold primary--text">{{ lesson.start }}</span>
                <span class="text-body-1 font-weight-medium mx-2">до</span>
                <span class="text-body-1 font-weight-bold primary--text">{{ lesson.end }}</span>
        </v-col>

        <!-- Действия -->
        <v-col  class="d-flex justify-center justify-sm-end py-0">
            <v-btn
                icon="mdi-pencil"
                variant="tonal"
                color="primary"
                @click="openEdit"
                title="Редактировать"
                class="mr-2"
            />
            <v-btn
                icon="mdi-delete"
                variant="tonal"
                color="error"
                @click="showDeleteModal = true"
                title="Удалить"
            />
        </v-col>

        <!-- Модальное окно для подтверждения удаления -->
        <v-dialog v-model="showDeleteModal" max-width="400">
            <v-card>
                <v-card-title class="text-h6">Подтвердите удаление</v-card-title>
                <v-card-text class="text-center">
                    {{getDayName(lesson.week_day)}} с <b>{{lesson.start}}</b> до <b>{{lesson.end}}</b>
                    <br>
                    <template v-if="lesson.subject?.name">Предмет: {{ lesson.subject?.name }}</template>
                </v-card-text>
                <v-card-actions>
                    <v-btn text="Отмена" variant="tonal" @click="showDeleteModal = false"/>
                    <v-btn
                        color="error"
                        variant="flat"
                        @click="deleteLesson"
                        text="Удалить"
                    />
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>

<script>
import {router} from '@inertiajs/vue3';

export default {
    props: {
        lesson: {
            type: Object,
            required: true,
            default: () => ({
                id: null,
                student: null,
                week_day: null,
                start: '',
                end: '',
                subject: {name: ''},
            }),
        },
    },
    data() {
        return {
            showDeleteModal: false,
        };
    },
    methods: {
        getDayName(num) {
            const days = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
            return days[num] || '—';
        },
        openEdit() {
            router.get(
                route('students.lesson-times.edit', {student: this.lesson.student_id, lesson_time: this.lesson.id})
            );
        },
        deleteLesson() {
            router.delete(
                route('students.lesson-times.destroy', {student: this.lesson.student_id, lesson_time: this.lesson.id}),
                {
                    onSuccess: () => {
                        this.showDeleteModal = false;
                    },
                }
            );
        },
    },
};
</script>

<style scoped>

.time-field :deep(.v-field__input) {
    padding: 4px 8px;
    font-size: 0.875rem;
}
</style>