<template>
    <!-- Панель навигации -->
    <v-card flat class="px-0 pt-2 pb-1 d-flex justify-center align-center flex-wrap">
        <v-btn
            variant="tonal"
            color="primary"
            elevation="2"
            icon="mdi-home"
            class="mx-2 border"
            @click="goToCurrentWeek"
        >
        </v-btn>

        <v-btn
            variant="tonal"
            color="primary"
            elevation="2"
            icon="mdi-chevron-left"
            @click="changeWeek(-1)"
        />

        <v-btn
            variant="tonal"
            color="primary"
            elevation="2"
            min-width="200"
            size="large"
            @click="pickDate = true"
            class="mx-2 text-capitalize"
        >
            {{ weekRange }}
        </v-btn>

        <v-btn
            variant="tonal"
            color="primary"
            elevation="2"
            icon="mdi-chevron-right"
            @click="changeWeek(1)"
        />

        <v-btn
            variant="tonal"
            color="primary"
            elevation="2"
            icon="mdi-home"
            class="mx-2"
            @click="goToCurrentWeek"
        >
        </v-btn>
    </v-card>

    <!-- Диалог выбора недели -->
    <v-dialog v-model="pickDate" max-width="400">
        <v-card>
            <v-date-picker
                width="400"
                color="primary"
                show-adjacent-months
                locale="ru"
                v-model="selectedDate"
                @update:model-value="goToWeek"
            />
            <v-card-actions>
                <v-btn
                    @click="pickDate = false"
                    variant="outlined" color="primary"
                >
                    Закрыть
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import dayjs from 'dayjs';
import 'dayjs/locale/ru';

dayjs.locale('ru');

export default {
    props: {
        weekDays: Array,
        lessonsOnDays: Object,
        weekOffset: Number,
    },
    data() {
        return {
            pickDate: false,
            selectedDate: null,
        };
    },
    computed: {
        weekRange() {
            if (!this.weekDays?.length) return '';
            const start = dayjs(this.weekDays[0]).format('DD MMM');
            const end = dayjs(this.weekDays[6]).format('DD MMM');
            return `${start} — ${end}`;
        },
    },
    methods: {
        changeWeek(offset) {
            this.$emit('change-week', offset);
        },
        goToCurrentWeek() {
            this.$emit('change-week', -this.weekOffset);
        },
        goToWeek(date) {
            if (date) {
                const diffWeeks = dayjs(date).startOf('week').diff(dayjs().startOf('week'), 'week');
                this.$emit('change-week', diffWeeks - this.weekOffset);
                this.pickDate = false;
            }
        }
    },
    emits: ['change-week'],
};
</script>