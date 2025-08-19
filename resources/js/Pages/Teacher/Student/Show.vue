<template>
    <Head :title="student.name"/>

    <v-container fluid>
        <v-row justify="center">
            <v-col cols="12" style="max-width: 800px">
                <v-card class="mb-4" elevation="4">
                    <v-card-title
                    class="text-white bg-gradient-45 text-center d-flex">
                        {{ `Ученик: ${student.name}` }}
                        <v-spacer />
                        <BackButton
                            variant="tonal"
                            :default-url="route('students.index')"
                            text="Назад"
                            color="white"
                        />
                    </v-card-title>
                    <v-divider />
                    <StudentProfile :student="student" />
                    <v-card-actions class="justify-end">
                        <v-btn
                            min-width="150"
                            variant="tonal"
                            color="primary"
                            @click="editStudent"
                        >
                            Редактировать
                        </v-btn>
                    </v-card-actions>
                </v-card>

                <v-card elevation="4">
                    <LessonTimeList
                        :student="student"
                        :lesson-times="lesson_times"
                    />
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import StudentProfile from '../../../Components/Student/Profile.vue'
import LessonTimeList from '../../../Components/LessonTime/LessonTimeList.vue'
import BackButton from "../../../Components/BackButton.vue";
import {Head, router} from '@inertiajs/vue3';

export default {
    components: {
        Head,
        BackButton,
        StudentProfile,
        LessonTimeList
    },
    props: {
        lesson_times: Array,
        student: Object
    },
    data() {
        return {
            windowWidth: window.innerWidth,
        }
    },
    computed: {
        colsForBreakpoint() {
            if (this.windowWidth < 800) return 12;
            if (this.windowWidth < 1000) return 10;
            if (this.windowWidth < 1300) return 8;
            return 6;
        },
    },
    mounted() {
        window.addEventListener('resize', this.handleResize);
    },
    methods: {
        handleResize() {
            this.windowWidth = window.innerWidth;
        },
        editStudent() {
            router.get(route('students.edit', {student: this.student.id}));
        }
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.handleResize);
    }
}
</script>
