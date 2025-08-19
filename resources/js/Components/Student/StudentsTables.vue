<template>
    <v-container fluid class="relative">
        <v-row justify="center">
            <v-col
                v-for="(students, studentClass) in studentsOnClasses"
                :key="studentClass"
                :cols="colsForBreakpoint"
                class="px-2 pt-1 pb-3"
            >
                <v-card class="rounded-lg elevation-4">
                    <v-card-title class="text-white text-center bg-gradient-45 py-1">
                        <template v-if="studentClass">
                            {{ studentClass }} класс
                        </template>
                        <template v-else>
                            Класс не указан
                        </template>
                    </v-card-title>

                    <v-divider/>

                    <v-card-text class="pa-0">
                        <v-table density="compact">
                            <thead>
                            <tr>
                                <th class="text-center font-weight-bold">
                                    Имя
                                </th>
                                <th class="text-center day-column px-1 font-weight-bold" v-for="day in ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс']" :key="day">
                                    {{ day }}
                                </th>
                            </tr>
                            </thead>
                            <tbody v-if="students?.length">
                            <tr v-for="student in students" :key="student.id">
                                <td class="px-0 text-center">
                                    <v-btn
                                        class="px-0 text-subtitle-1"
                                        variant="text"
                                        density="compact"
                                        @click="showStudent(student.id)"
                                    >
                                        {{ student.name }}
                                    </v-btn>
                                    <v-icon v-if="student.account_id" color="primary" icon="mdi-account-check"></v-icon>
                                </td>
                                <td class="px-0 text-center day-column px-1" v-for="dayIndex in 7" :key="dayIndex">
                                    <i v-if="student.days_with_lessons.includes(dayIndex-1)" class="fa-regular fa-check"></i>
                                </td>
                            </tr>
                            </tbody>
                        </v-table>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import {router} from '@inertiajs/vue3';

export default {
    props: {
        studentsOnClasses: Object,
    },
    data() {
        return {
            windowWidth: window.innerWidth,
        };
    },
    computed: {
        colsForBreakpoint() {
            if (this.windowWidth < 1000) return 12;
            if (this.windowWidth < 1300) return 6;
            return 4;
        },
    },
    mounted() {
        window.addEventListener('resize', this.handleResize);
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.handleResize);
    },
    methods: {
        handleResize() {
            this.windowWidth = window.innerWidth;
        },
        showStudent(id) {
            router.visit(route('students.show', { student: id }));
        }
    },
};
</script>

<style scoped>
.day-column {
    width: 36px;
    min-width: 36px;
    max-width: 36px;
}

.v-table table th,
.v-table table td {
    border-left: 1px solid rgba(0, 0, 0, 0.12);
}

.v-table td:nth-child(even),
.v-table th:nth-child(even) {
    background-color: rgba(161, 47, 74, 0.06); /* светло-серый */
}

</style>