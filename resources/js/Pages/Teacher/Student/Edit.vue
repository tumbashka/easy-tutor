<template>
    <Head :title="'Редактирование: ' + student.name"/>

    <v-container fluid>
        <v-row justify="center">
            <v-col cols="12" style="max-width: 800px">
                <VForm @submit.prevent="submit">
                    <v-card>
                        <v-card-title
                            class="text-white bg-gradient-45 mb-5 d-flex">
                            Редактирование: {{student.name}}
                            <v-spacer />
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
                <v-btn
                    color="error"
                    variant="outlined"
                    class="mt-4"
                    @click="showDeleteModal = true"
                >
                    Удалить
                </v-btn>
            </v-col>
        </v-row>
    </v-container>
    <v-dialog v-model="showDeleteModal" max-width="400">
        <v-card>
            <v-card-title class="text-h6">Подтвердите удаление</v-card-title>
            <v-card-text class="text-center">
                {{ student.name }}<template v-if="student.class"> ({{student.class}} класс)</template>
            </v-card-text>
            <v-card-actions>
                <v-btn text="Отмена" variant="tonal" @click="showDeleteModal = false"/>
                <v-btn
                    color="error"
                    variant="flat"
                    @click="deleteStudent"
                    text="Удалить"
                />
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import {useForm, router, Head} from "@inertiajs/vue3"
import StudentForm from "../../../Components/Student/StudentForm.vue"
import BackButton from "../../../Components/BackButton.vue";

export default {
    components: {Head, BackButton, StudentForm },
    props: {
        student: Object,
        classesData: Array,
    },
    data() {
        return {
            form: useForm({
                name: this.student.name,
                class: this.student.class,
                price: this.student.price,
                note: this.student.note,
            }),
            showDeleteModal: false,
        }
    },
    methods: {
        submit() {
            this.form.put(route("students.update", this.student.id), { replace: true })
        },
        deleteStudent() {
            router.delete(
                route('students.destroy', this.student.id),
                {
                    onSuccess: () => {
                        this.showDeleteModal = false;
                    },
                }
            );
        },
    },
}
</script>
