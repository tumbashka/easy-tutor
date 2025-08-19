<template>
    <v-card-title class="text-white bg-gradient-45 text-center d-flex">
        Занятия
        <v-spacer/>
        <v-btn
            min-width="150"
            variant="tonal"
            color="white"
            @click="addLesson"
        >
            Добавить
        </v-btn>
    </v-card-title>

    <v-divider/>

    <v-card-text class="pa-3">
        <div v-if="!lessonTimes.length" class="text-center text-subtitle-1">
            Список занятий пуст
        </div>
        <div v-else>
            <template v-for="(lesson, index) in lessonTimes" :key="lesson.id" >
                <LessonTime
                    :lesson="lesson"
                    class="py-1"
                    :class="index % 2 === 0 ? 'bg-grey-lighten-4' : ''"
                />
            </template>
        </div>
    </v-card-text>
</template>

<script>
import LessonTime from './LessonTime.vue';
import { router } from '@inertiajs/vue3';

export default {
    name: 'StudentLessonList',
    components: {LessonTime},
    props: {
        student: {
            type: Object,
            required: true
        },
        lessonTimes: {
            type: Array,
            required: true
        }
    },
    methods: {
        addLesson(){
            router.get(route('students.lesson-times.create', {student: this.student.id}));
        }
    }
}
</script>
