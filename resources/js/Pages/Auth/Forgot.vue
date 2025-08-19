<template>
    <Head :title="'Восстановление пароля'"/>

    <FormContainer>
        <v-card elevation="10" rounded="lg" class="overflow-hidden" mx="auto">
            <v-form @submit.prevent="forgotRequest">

                <v-card-title class="text-white bg-gradient-45 text-center">
                    Восстановление пароля
                </v-card-title>

                <v-card-text class="pt-4">
                    <v-row class="px-sm-4">
                        <v-col cols="12">
                            <v-text-field
                                autocomplete="email"
                                v-model="form.email"
                                label="Email"
                                type="email"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-email"
                                :error="hasErrors"
                                :error-messages="this.form.errors.email"
                                required
                            />
                        </v-col>

                        <v-col cols="12" class="d-flex justify-end py-0">
                            <Link :href="route('login')" class="text-decoration-none text-primary text-body-1 ">
                                Вспомнили пароль?
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
                        Отправить письмо на почту
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
    computed: {
        hasErrors() {
            return !!this.form.errors.email || !!this.form.errors.password;
        },
    },
    data() {
        return {
            form: useForm({
                email: null,
            }),
        }
    },
    methods: {
        forgotRequest() {
            this.form.post(route('password.request'));
        },
    },
}
</script>


