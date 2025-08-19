<template>
    <Head :title="'Регистрация'"/>

    <FormContainer>
        <v-card elevation="10" rounded="lg" class="overflow-hidden" mx="auto">
            <v-form @submit.prevent="register">

                <v-card-title class="text-white bg-gradient-45 text-center">
                    Регистрация
                </v-card-title>

                <!-- Поля формы -->
                <v-card-text class="pt-4">
                    <v-row class="px-sm-4">

                        <v-col cols="12" class="pb-1">
                            <v-text-field
                                v-model="form.name"
                                label="Имя"
                                type="text"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-account"
                                :error="!!this.form.errors.name"
                                :error-messages="this.form.errors.name"
                            />
                        </v-col>

                        <v-col cols="12" class="py-1">
                            <v-autocomplete
                                v-model="form.role"
                                :items="roles"
                                variant="outlined"
                                density="comfortable"
                                placeholder="Выберите роль"
                                label="Роль"
                                prepend-inner-icon="mdi-human-queue"
                                :error="!!this.form.errors.role"
                                :error-messages="this.form.errors.role"
                            ></v-autocomplete>
                        </v-col>

                        <v-col cols="12" class="py-1">
                            <v-text-field
                                autocomplete="new-email"
                                v-model="form.email"
                                label="Email"
                                type="email"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-email"
                                :error="!!this.form.errors.email"
                                :error-messages="this.form.errors.email"
                            />
                        </v-col>

                        <v-col cols="12" class="py-1">
                            <v-text-field
                                autocomplete="new-password"
                                v-model="form.password"
                                label="Пароль"
                                type="password"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-lock"
                                :error="!!this.form.errors.password_confirmation || !!this.form.errors.password"
                                :error-messages="this.form.errors.password"
                            />
                        </v-col>

                        <v-col cols="12" class="py-1">
                            <v-text-field
                                autocomplete="confirm-password"
                                v-model="form.password_confirmation"
                                label="Подтвердите пароль"
                                type="password"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-lock"
                                :error="!!this.form.errors.password_confirmation || !!this.form.errors.password"
                                :error-messages="this.form.errors.password_confirmation"
                            />
                        </v-col>

                        <v-col cols="12" class="d-flex justify-end py-0">
                            <Link :href="route('login')" class="text-decoration-none text-primary text-body-1 ">
                                Уже зарегистрированы?
                            </Link>
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
                        color="primary"
                        class="px-6 bg-gradient-45"
                        variant="elevated"
                        :loading="form.processing"
                    >
                        Регистрация
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
        roles: Array,
    },
    computed: {
        hasErrors() {
            return !!this.form.errors.email || !!this.form.errors.password;
        },
    },
    data() {
        return {
            form: useForm({
                email: null,
                password: null,
                password_confirmation: null,
                name: null,
                role: null,
            }),
        }
    },
    methods: {
        register() {
            this.form.post(route('register.store'), {
                onFinish: () => this.form.reset('password', 'password_confirmation'),
            });
        },
    },
}
</script>

<style>


</style>
