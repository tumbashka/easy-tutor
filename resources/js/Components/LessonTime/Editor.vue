<template>
    <v-card elevation="4">
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
                <!-- Форма -->
                <v-col cols="12" lg="7">
                    <LessonTimeForm
                        :student="student"
                        :subjects="subjects"
                        :lesson-times="lessonTimes"
                        :mode="mode"
                        :initial-data="initialDataForForm"
                        :errors="form.errors"
                        @update:subject="form.subject = $event"
                        @update:start="form.start = $event"
                        @update:end="form.end = $event"
                        @update:week_day="form.week_day = $event"
                        @update:error="updateError($event)"
                        @submit="onSubmit"
                    />
                    <div class="d-flex justify-center">
                        <v-btn
                            width="200"
                            color="primary"
                            variant="flat"
                            min-width="150"
                            @click="onSubmit"
                            :loading="form.processing"
                        >
                            Сохранить
                        </v-btn>
                    </div>
                </v-col>
                <!-- Таймлайн -->
                <v-col cols="12" lg="5">
                    <Timeline
                        :lesson-times="lessonTimes"
                        :week-day="form.week_day"
                        :current-start="form.start"
                        :current-end="form.end"
                        :student="student"
                    />
                </v-col>
            </v-row>
        </v-card-text>
    </v-card>
</template>

<script>
import {useForm} from '@inertiajs/vue3';
import LessonTimeForm from '../../Components/LessonTime/LessonTimeForm.vue';
import Timeline from '../../Components/LessonTime/Timeline.vue';
import BackButton from '../../Components/BackButton.vue';

export default {
    components: {BackButton, LessonTimeForm, Timeline},
    props: {
        title: String,
        student: Object,
        subjects: Array,
        lessonTimes: Array,
        mode: {type: String, default: 'create'},
        initialLesson: {type: Object, default: null},
    },
    setup(props) {
        const initial = props.initialLesson
            ? {
                subject: props.initialLesson.subject_id ?? null,
                week_day: props.initialLesson.week_day ?? 0,
                start: props.initialLesson.start ?? '',
                end: props.initialLesson.end ?? '',
                id: props.initialLesson.id,
            }
            : {
                subject: null,
                week_day: 0,
                start: '',
                end: '',
            };

        const form = useForm({...initial});
        return {form};
    },
    computed: {
        initialDataForForm() {
            return {...this.form};
        },
    },
    methods: {
        updateError(error) {
            Object.assign(this.form.errors, error);
        },
        onSubmit() {
            if (this.form.errors.end) return;
            const routeName = this.mode === 'edit' && this.form.id
                ? 'students.lesson-times.update'
                : 'students.lesson-times.store';
            const params = this.mode === 'edit' && this.form.id
                ? [this.student.id, this.form.id]
                : this.student.id;

            this.form[this.mode === 'edit' ? 'put' : 'post'](route(routeName, params), {
                replace: true,
                onError: (errors) => {
                    console.error(errors);
                },
            });
        },
    },
};
</script>

<style scoped>

</style>
