<template>
    <v-card class="mb-4 rounded border border-opacity-25" flat>
        <v-card-title class="text-subtitle-1 font-weight-bold">
            {{ title }} ({{ lessons.length }})
        </v-card-title>
        <v-divider/>
        <v-table density="compact" class="px-3">
            <tbody>
            <template v-if="lessons.length">
                <tr
                    v-for="lesson in lessons"
                    :key="lesson.id"
                >
                    <!-- Время -->
                    <td class="px-0">
                        <v-btn
                            class="px-0 text-subtitle-1"
                            variant="text"
                            density="compact"
                            @click="$emit('edit-lesson', lesson)"
                        >
                            {{ lesson.start }} - {{ lesson.end }}
                        </v-btn>
                    </td>

                    <!-- Ученик -->
                    <td class="text-start px-0">
                        <v-btn
                            class="text-capitalize text-subtitle-1"
                            variant="text"
                            density="compact"
                            @click="$emit('show-student', lesson.student_id)"
                        >
                            {{ lesson.student_name }}
                        </v-btn>
                    </td>

                    <!-- Предмет -->
                    <td class="px-0">
                        <v-chip v-if="lesson.subject_name" color="primary" size="small" label>
                            {{ lesson.subject_name }}
                        </v-chip>
                    </td>

                    <!-- Заметка -->
                    <td style="width: 35px;" class="px-0">
                        <v-tooltip v-if="lesson.note" activator="parent" location="top">
                            {{ lesson.note }}
                        </v-tooltip>
                        <v-icon
                            v-if="lesson.note"
                            icon="mdi-note-text"
                            color="primary"
                        />
                    </td>

                    <!-- Оплата -->
                    <td class="text-end px-0 text-subtitle-1" style="min-width: 90px;">
                        <div class="d-flex align-center justify-end gap-2">
                            <span>{{ lesson.price }}</span>
                            <v-checkbox
                                v-model="lesson.is_paid"
                                color="primary"
                                density="compact"
                                hide-details
                                class="ma-0 pa-0 ms-1"
                                @change="$emit('payment-change', lesson)"
                            />
                        </div>
                    </td>

                    <td style="width: 35px;" class="px-1">
                        <v-btn
                            :icon="lesson.is_canceled ? 'mdi-arrow-up-bold' : 'mdi-arrow-down-bold'"
                            variant="tonal"
                            color="primary"
                            density="comfortable"
                            :loading="!!loadingStatus[lesson.id]"
                            :disabled="!!loadingStatus[lesson.id]"
                            @click="onStatusClick(lesson)"
                        />
                    </td>
                    <td style="width: 35px;" class="px-1">
                        <v-btn
                            icon="mdi-pencil"
                            variant="tonal"
                            color="primary"
                            density="comfortable"
                            @click="$emit('edit-lesson', lesson)"
                        />
                    </td>
                </tr>
            </template>
            <tr v-else>
                <td colspan="6" class="text-center text-subtitle-1">Занятий нет</td>
            </tr>
            </tbody>
        </v-table>
    </v-card>
</template>

<script>
export default {
    props: {
        lessons: { type: Array, default: () => [] },
        title: { type: String, default: "" },
        // Родитель передаёт функцию, которая возвращает Promise
        changeStatusFn: { type: Function, required: true },
    },
    data() {
        return {
            loadingStatus: {}, // { [lessonId]: boolean }
        }
    },
    methods: {
        async onStatusClick(lesson) {
            this.loadingStatus[lesson.id] = true
            try {
                await this.changeStatusFn(lesson.id) // ждём завершения родителя
            } finally {
                this.loadingStatus[lesson.id] = false
            }
        },
    },
    mounted(){
        console.log(this.title);
        console.log(this.lessons);
    }
}
</script>