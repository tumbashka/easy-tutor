<template>
    <Head :title="'Добавление ученика'"/>

    <v-container fluid>
        <v-row justify="center">
            <v-col cols="12" style="max-width: 800px">
                <VForm @submit.prevent="submit">
                    <v-card>
                        <v-card-title
                            class="text-white bg-gradient-45 mb-5 d-flex">
                            Добавление ученика
                            <v-spacer/>
                            <BackButton
                                variant="tonal"
                                :default-url="route('students.index')"
                                text="Назад"
                                color="white"
                            />
                        </v-card-title>

                        <v-card-text class="px-sm-7">
                            <StudentForm v-model="form" :form :classesData/>
                        </v-card-text>

                        <v-card-actions class="pa-4 pt-0">
                            <v-btn
                                block
                                type="submit"
                                class="px-6 bg-gradient-45"
                                variant="elevated"
                                :loading="form.processing">
                                Сохранить
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </VForm>
            </v-col>
        </v-row>
    </v-container>

</template>

<script>
import {Head, useForm} from "@inertiajs/vue3"
import StudentForm from "../../../Components/Student/StudentForm.vue"
import LessonTimeList from "../../../Components/LessonTime/LessonTimeList.vue";
import BackButton from "../../../Components/BackButton.vue";
import StudentProfile from "../../../Components/Student/Profile.vue";

export default {
    components: {Head, StudentProfile, BackButton, LessonTimeList, StudentForm},
    props: {
        free_time: String,
        classesData: Array,
    },
    data() {
        return {
            form: useForm({
                name: null,
                class: null,
                price: null,
                note: null,
            }),
        }
    },
    methods: {
        submit() {
            this.form.post(route("students.store", {free_time: this.free_time}))
        },
    },
}
</script>
