<template>
    <div class="space-y-4">
        <!-- Имя -->
        <v-text-field
            prepend-icon="mdi-account"
            v-model="localValue.name"
            label="Имя"
            :error-messages="form.errors.name"
            variant="outlined"
            density="comfortable"
            required
        />

        <!-- Класс -->
        <v-autocomplete
            prepend-icon="mdi-school"
            v-model="localValue.class"
            label="Класс"
            :items="classesData"
            :error-messages="form.errors.class"
            placeholder="Выберите класс"
            variant="outlined"
            density="comfortable"
            required
        />

        <!-- Стоимость -->
        <v-text-field
            prepend-icon="mdi-currency-rub"
            v-model="localValue.price"
            label="Стоимость (руб./ч)"
            type="number"
            :error-messages="form.errors.price"
            variant="outlined"
            density="comfortable"
            required
        />

        <!-- Примечание -->
        <v-textarea
            prepend-icon="mdi-note-edit"
            v-model="localValue.note"
            label="Примечание"
            auto-grow
            :error-messages="form.errors.note"
            variant="outlined"
            density="comfortable"
        />
    </div>
</template>

<script>
export default {
    props: {
        modelValue: { type: Object, required: true },
        form: { type: Object, required: true },
        classesData: { type: Array, required: true },
    },
    emits: ["update:modelValue"],
    computed: {
        localValue: {
            get() {
                return this.modelValue
            },
            set(value) {
                this.$emit("update:modelValue", value)
            },
        },
    },
}
</script>
