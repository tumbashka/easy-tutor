<template>
    <Head :title="'Сброс пароля'"/>

    <FormContainer>
        <v-card elevation="10" rounded="lg" class="overflow-hidden" mx="auto">
            <v-form @submit.prevent="passwordUpdate">

                <v-card-title class="text-white bg-gradient-45 text-center">
                    Сброс пароля
                </v-card-title>

                <v-card-text class="pt-4">
                    <v-row class="px-sm-4">
                        <v-col cols="12" class="pb-1">
                            <v-text-field
                                autocomplete="new-password"
                                v-model="form.password"
                                label="Пароль"
                                :type="showPassword ? 'text' : 'password'"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-lock"
                                :error="!!this.form.errors.password_confirmation || !!this.form.errors.password"
                                :error-messages="this.form.errors.password"
                            />
                        </v-col>

                        <v-col cols="12" class="py-1">
                            <v-text-field
                                autocomplete="new-password"
                                v-model="form.password_confirmation"
                                label="Подтвердите пароль"
                                :type="showPassword ? 'text' : 'password'"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-lock"
                                :error="!!this.form.errors.password_confirmation || !!this.form.errors.password"
                                :error-messages="this.form.errors.password_confirmation"
                            />
                        </v-col>
                        <v-col cols="12" class="py-1">
                            <v-btn
                                class="px-5"
                                size="small"
                                @click="showPassword = !showPassword"
                                :title="showPassword ? 'Скрыть пароль' : 'Показать пароль'"
                            >
                                {{ showPassword ? 'Скрыть пароль' : 'Показать пароль' }}
                            </v-btn>
                        </v-col>


                    </v-row>
                </v-card-text>

                <!-- Разделитель -->
                <v-divider class="my-2"></v-divider>

                <!-- Кнопка отправки -->
                <v-card-actions class="pa-4 d-flex justify-end">
                    <v-btn
                        block
                        type="submit"
                        color="primary bg-gradient-45"
                        class="px-6"
                        variant="elevated"
                        :loading="form.processing"
                    >
                        Сохранить
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </FormContainer>
</template>

<script>
import FormContainer from "../../Components/Form/FormContainer.vue";
import {Head, Link, useForm} from '@inertiajs/vue3';

export default {
    components: {Link, Head, FormContainer},
    props: {
        email: String,
        token: String,
    },
    data() {
        return {
            showPassword: false,
            form: useForm({
                token: this.token,
                email: this.email,
                password: null,
                password_confirmation: null,
            }),
        }
    },
    computed: {
        hasErrors() {
            return !!this.form.errors.email || !!this.form.errors.password;
        },
    },
    methods: {
        passwordUpdate() {
            this.form.post(route('password.update'), {
                onFinish: () => this.form.reset('password', 'password_confirmation'),
            });
        },
    },
}
</script>

<style>


</style>
