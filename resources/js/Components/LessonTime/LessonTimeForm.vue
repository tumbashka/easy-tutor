<template>
    <v-form @submit.prevent="$emit('submit')">
        <!-- Предмет -->
        <v-select
            v-model="local.subject"
            :items="subjects"
            item-value="id"
            item-title="name"
            label="Предмет"
            variant="outlined"
            clearable
            class="mb-2"
            :error-messages="errors.subject"
        />

        <!-- День недели -->
        <v-select
            v-model="local.week_day"
            :items="weekDays"
            item-title="title"
            item-value="value"
            label="День недели"
            variant="outlined"
            class="mb-2"
            :error-messages="errors.week_day"
        />

        <!-- Время -->
        <v-row >
            <v-col>
                <v-text-field
                    :model-value="local.start"
                    label="Время начала"
                    readonly
                    variant="outlined"
                    :error-messages="errors.start"
                >
                    <v-menu
                        scroll-strategy="close"
                        transition="slide-y-transition"
                        v-model="showStartMenu"
                        :close-on-content-click="false"
                        activator="parent"
                        min-width="0"
                    >
                        <v-time-picker
                            v-model="local.start"
                            :allowed-minutes="allowedMinutes"
                            format="24hr"
                            @update:model-value="onStartChange"
                        />
                    </v-menu>
                </v-text-field>
            </v-col>
            <v-col>
                <v-text-field
                    :model-value="local.end"
                    label="Время окончания"
                    readonly
                    variant="outlined"
                    :error-messages="errors.end"
                >
                    <v-menu
                        scroll-strategy="close"
                        transition="slide-y-transition"
                        v-model="showEndMenu"
                        :close-on-content-click="false"
                        activator="parent"
                        min-width="0"
                    >
                        <v-time-picker
                            v-model="local.end"
                            :allowed-minutes="allowedMinutes"
                            format="24hr"
                            @update:model-value="onEndChange"
                        />
                    </v-menu>
                </v-text-field>
            </v-col>
        </v-row>

        <div>
            <div class="d-flex justify-space-between align-center">
                <span class="text-subtitle-2">Длительность</span>
                <span class="text-subtitle-2">{{ formattedDuration }}</span>
            </div>
            <v-slider
                v-model="local.duration"
                :min="10"
                :max="240"
                :step="1"
                color="primary"
                @update:model-value="onDurationChange"
            />
        </div>
    </v-form>
</template>

<script>
export default {
    props: {
        student: Object,
        subjects: Array,
        lessonTimes: Array,
        mode: { type: String, default: 'create' },
        initialData: { type: Object, default: () => ({}) },
        errors: { type: Object, default: () => ({}) },
    },
    data() {
        // Вычисляем начальную длительность из start и end
        let duration = 60;
        if (this.initialData.start && this.initialData.end) {
            const startMin = this.toMinutes(this.initialData.start);
            const endMin = this.toMinutes(this.initialData.end);
            duration = Math.max(1, endMin - startMin);
        }

        return {
            showStartMenu: false,
            showEndMenu: false,
            local: {
                subject: this.initialData.subject ?? null,
                week_day: this.initialData.week_day ?? 0,
                start: this.initialData.start ?? '',
                end: this.initialData.end ?? '',
                duration,
            },
            weekDays: [
                { title: 'Понедельник', value: 0 },
                { title: 'Вторник', value: 1 },
                { title: 'Среда', value: 2 },
                { title: 'Четверг', value: 3 },
                { title: 'Пятница', value: 4 },
                { title: 'Суббота', value: 5 },
                { title: 'Воскресенье', value: 6 },
            ],
        };
    },
    computed: {
        formattedDuration() {
            const m = this.local.duration;
            const h = Math.floor(m / 60);
            const mm = m % 60;
            return (h ? `${h} ч.` : '') + (mm ? ` ${mm} мин.` : h ? '' : '0 мин.');
        },
    },
    watch: {
        'local.subject'(v) {
            this.$emit('update:subject', v);
        },
        'local.start'(v) {
            this.$emit('update:start', v);
            this.onStartChange(v);
        },
        'local.end'(v) {
            this.$emit('update:end', v);
            this.onEndChange(v);
        },
        'local.week_day'() {
            this.$emit('update:week_day', this.local.week_day);
        },
    },
    methods: {
        allowedMinutes: () => true, // Разрешаем любые минуты
        toMinutes(hhmm) {
            if (!hhmm) return 0;
            const [h, m] = hhmm.split(':').map(Number);
            return h * 60 + m;
        },
        fromMinutes(min) {
            const m = Math.max(0, Math.min(23 * 60 + 59, min));
            const hh = String(Math.floor(m / 60)).padStart(2, '0');
            const mm = String(m % 60).padStart(2, '0');
            return `${hh}:${mm}`;
        },
        onStartChange(val) {
            if (!val || !this.local.end) return;
            const startMin = this.toMinutes(val);
            const endMin = this.toMinutes(this.local.end);
            const diff = endMin - startMin;
            this.local.duration = diff > 0 ? diff : 1;
            if (diff <= 0) {
                this.$emit('update:error', { end: 'Время окончания должно быть позже начала!' });
            } else {
                this.$emit('update:error', { end: '' });
            }
        },
        onEndChange(val) {
            if (!val || !this.local.start) return;
            const startMin = this.toMinutes(this.local.start);
            const endMin = this.toMinutes(val);
            const diff = endMin - startMin;
            this.local.duration = diff > 0 ? diff : 1;
            if (diff <= 0) {
                this.$emit('update:error', { end: 'Время окончания должно быть позже начала!' });
            } else {
                this.$emit('update:error', { end: '' });
            }
        },
        onDurationChange(val) {
            if (!this.local.start) return;
            const startMin = this.toMinutes(this.local.start);
            this.local.end = this.fromMinutes(startMin + val);
            this.$emit('update:end', this.local.end);
            this.$emit('update:error', { end: '' });
        },
    },
};
</script>

<style scoped>
.required-input::after {
    content: '*';
    color: red;
    margin-left: 4px;
}
</style>
