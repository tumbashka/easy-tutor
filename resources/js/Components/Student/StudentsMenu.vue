<template>
    <v-card flat class="px-0 pt-2 pb-1 d-flex justify-center align-center flex-wrap">
        <v-btn
            variant="tonal"
            color="primary"
            elevation="2"
            icon="mdi-magnify"
            class="mx-2 border"
        >
        </v-btn>

        <v-btn
            variant="tonal"
            color="primary"
            elevation="2"
            icon="mdi-plus"
            @click="createStudent"
        />

    </v-card>
</template>

<script>
import dayjs from 'dayjs';
import 'dayjs/locale/ru';
import {router} from "@inertiajs/vue3";

dayjs.locale('ru');

export default {
    methods: {
        createStudent() {
            router.get(route('students.create'));
        },
    },
};
</script>