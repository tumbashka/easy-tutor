<template>
    <Head :title="title"/>
    <v-container fluid>
        <v-row justify="center">
            <v-col cols="12" style="max-width: 1200px">
                <v-card elevation="4">
                    <!-- Заголовок -->
                    <v-card-title class="text-white bg-gradient-45 text-center d-flex">
                        {{ title }}
                        <v-spacer/>
                        <BackButton
                            variant="tonal"
                            :default-url="route('students.index')"
                            text="Назад"
                            color="white"
                        />
                    </v-card-title>

                    <v-card-text class="pa-4">
                        <v-row>
                            <!-- Левая колонка -->
                            <v-col cols="12" lg="8" class="pa-0 pa-sm-1">
                                <LessonsTable
                                    title="Актуальные занятия"
                                    :lessons="activeLessons"
                                    :change-status-fn="changeStatus"
                                    @payment-change="onPaymentChange"
                                    @show-student="onShowStudent"
                                    @edit-lesson="onEditLesson"
                                />

                                <LessonsTable
                                    title="Отменённые занятия"
                                    :lessons="canceledLessons"
                                    :change-status-fn="changeStatus"
                                    @payment-change="onPaymentChange"
                                    @show-student="onShowStudent"
                                    @edit-lesson="onEditLesson"
                                />
                            </v-col>

                            <!-- Правая колонка -->
                            <v-col cols="12" lg="4">
                                <Timeline :lessons="lessons"/>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import {Head, router} from '@inertiajs/vue3'
import BackButton from "../../../Components/BackButton.vue"
import Timeline from "../../../Components/LessonTime/Timeline.vue"
import LessonsTable from "../../../Components/Lesson/LessonsTable.vue"

export default {
    components: { Timeline, BackButton, Head, LessonsTable },
    props: {
        title: String,
        day: String,
        lessons: Array,
        occupiedSlots: Array,
    },
    computed:{
        activeLessons () {
            return this.lessons.filter(l => !l.is_canceled)
        },
        canceledLessons () {
            return this.lessons.filter(l => l.is_canceled)
        },
    },
    methods: {
        onPaymentChange(lesson) {
            // логика оплаты
        },
        onEditLesson(id) {
            router.visit(route('lessons.edit', id));
        },
        onShowStudent(id) {
            router.visit(route('students.show', id));
        },
        changeStatus(id) {
            return new Promise((resolve, reject) => {
                router.get(route('lessons.change_status', id), {
                    preserveScroll: true,
                    onFinish: () => resolve(),
                    onError: () => reject(),
                })
            })
        },
    }
}
</script>
