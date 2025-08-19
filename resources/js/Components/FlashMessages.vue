<template>
    <div>
        <!-- Success -->
        <v-snackbar
            v-if="messages.success"
            v-model="showSuccess"
            multi-line
            color="success"
            :timeout="calcTimeout(messages.success)"
            top
            right
        >
            {{ messages.success }}
            <template #actions>
                <v-btn icon @click="showSuccess = false">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </template>
        </v-snackbar>

        <!-- Status / Info -->
        <v-snackbar
            v-if="messages.status"
            v-model="showStatus"
            multi-line
            color="primary"
            :timeout="calcTimeout(messages.status)"
            top
            right
        >
            {{ messages.status }}
            <template #actions>
                <v-btn icon @click="showStatus = false">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </template>
        </v-snackbar>

        <!-- Error -->
        <v-snackbar
            v-if="messages.error"
            v-model="showError"
            multi-line
            color="error"
            :timeout="calcTimeout(messages.error)"
            top
            right
        >
            {{ messages.error }}
            <template #actions>
                <v-btn @click="showError = false">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </template>
        </v-snackbar>
    </div>
</template>

<script>
export default {
    props: {
        messages: {
            type: Object,
            default: () => ({}),
        },
    },
    data() {
        return {
            showSuccess: !!this.messages.success,
            showStatus: !!this.messages.status,
            showError: !!this.messages.error,
        };
    },
    watch: {
        messages: {
            deep: true,
            handler(newVal) {
                this.showSuccess = !!newVal.success;
                this.showStatus = !!newVal.status;
                this.showError = !!newVal.error;
            },
        },
    },
    methods: {
        calcTimeout(message) {
            if (!message) return 4000;
            const time = message.length * 100;
            return Math.min(Math.max(time, 2000), 10000);
        },
    },
};
</script>
