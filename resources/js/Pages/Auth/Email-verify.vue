<template>
    <Head :title="'Подтверждение почты'"/>

    <FormContainer>
        <v-card elevation="10" rounded="lg" class="overflow-hidden" mx="auto">
            <v-card-title class="text-white bg-primary-gradient text-center">
                Подтверждение почты
            </v-card-title>

            <v-card-text class="pt-4">
                <h3 class="mb-3">
                    Спасибо за регистрацию в нашем сервисе для репетиторов!
                </h3>
                <p v-if="!buttonClicked">
                    На почту <b>{{ auth().email }}</b> отправлено письмо с ссылкой для подтверждения аккаунта.
                </p>
                <p v-else>
                    Новое письмо отправлено на почту, указанную при регистрации.
                </p>
            </v-card-text>

            <v-divider class="my-2"></v-divider>

            <v-card-actions class="pa-4 d-flex justify-end">
                <v-btn
                    @click="sendEmail"
                    :disabled="isCooldown"
                    block
                    color="primary"
                    class="px-6"
                    variant="elevated"
                >
                    <template v-if="isCooldown">
                        Отправить повторно через {{ cooldown }}с
                    </template>
                    <template v-else>
                        Отправить новое письмо
                    </template>
                </v-btn>
            </v-card-actions>
        </v-card>
    </FormContainer>
</template>

<script>
import FormContainer from "../../Components/Form/FormContainer.vue";
import {Head, Link, router} from '@inertiajs/vue3';

export default {
    components: {Link, Head, FormContainer},
    data() {
        return {
            buttonClicked: false,
            cooldown: 0,
            timer: null,
        }
    },
    computed: {
        isCooldown() {
            return this.cooldown > 0;
        }
    },
    mounted() {
        const cooldownEnd = localStorage.getItem('cooldownEnd');
        if (cooldownEnd) {
            const remaining = Math.floor((cooldownEnd - Date.now()) / 1000);
            if (remaining > 0) {
                this.startCooldown(remaining);
            } else {
                localStorage.removeItem('cooldownEnd');
            }
        }
    },
    methods: {
        sendEmail() {
            this.buttonClicked = true;
            router.post(route('verification.send'));

            this.startCooldown(20);
            localStorage.setItem('cooldownEnd', Date.now() + 20000);
        },
        startCooldown(seconds) {
            this.cooldown = seconds;
            clearInterval(this.timer);
            this.timer = setInterval(() => {
                if (this.cooldown > 0) {
                    this.cooldown--;
                } else {
                    clearInterval(this.timer);
                    localStorage.removeItem('cooldownEnd');
                }
            }, 1000);
        }
    },
    beforeUnmount() {
        clearInterval(this.timer);
    }
}
</script>

<style>

</style>
