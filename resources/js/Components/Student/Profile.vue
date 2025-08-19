<template>
    <v-container>
        <v-row justify="center">
            <v-row align="center">
                <!-- Аватар и имя -->
                <v-col cols="12" md="4" class="text-center">
                    <v-avatar color="primary" size="100" class="mb-3">
                        <v-icon dark size="60">mdi-school</v-icon>
                    </v-avatar>
                    <h2 class="text-h5 font-weight-bold">{{ student.name }}</h2>
                </v-col>

                <!-- Информация об ученике -->
                <v-col cols="12" md="8">
                    <v-card-text>
                        <v-row dense>
                            <v-col cols="12" sm="6">
                                <v-sheet color="transparent">
                                    <h3 class="text-subtitle-1 font-weight-bold">Класс</h3>
                                    <p class="text-body-1">{{ student.class || '—' }}</p>
                                </v-sheet>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-sheet color="transparent">
                                    <h3 class="text-subtitle-1 font-weight-bold">Стоимость</h3>
                                    <p class="text-body-1">{{ student.price || '—' }}</p>
                                </v-sheet>
                            </v-col>
                        </v-row>

                        <v-divider class="my-4"/>
                        <h3 class="text-subtitle-1 font-weight-bold">Примечание</h3>
                        <p class="text-body-2">{{ student.note || '—' }}</p>
                    </v-card-text>
                </v-col>
            </v-row>
        </v-row>
    </v-container>
</template>

<script>
export default {
    name: 'StudentProfile',
    props: {
        student: {
            type: Object,
            required: true
        }
    }
}
</script>
