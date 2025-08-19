<template>
    <v-btn
        :min-width="minWidth"
        :color
        :size
        :variant
        @click="goBack"
    >
        {{ text }}
    </v-btn>
</template>

<script>
import { router } from '@inertiajs/vue3';

export default {
    props: {
        defaultUrl: {
            type: String,
            required: true,
        },
        variant: {
            type: String,
            default: 'tonal',
        },
        text: {
            type: String,
            default: 'Назад',
        },
        size: {
            type: String,
            default: 'default',
        },
        color: {
            type: String,
            default: 'primary',
        },
        minWidth: {
            type: String,
            default: '150',
        },
        skipRoutes: {
            type: Array,
            default: () =>  ['/students/:id/edit', '/students/:id/lesson-times/:id/edit'],
        },
    },
    methods: {
        goBack() {
            const historyStack = JSON.parse(sessionStorage.getItem('navigationHistory') || '[]');
            const skipPatterns = this.skipRoutes.map(
                (route) => new RegExp(route.replace(':id', '\\d+'))
            );
            const currentUrl = window.location.pathname + window.location.search;

            // проверка — пропустить ли url
            const shouldSkip = (url) => {
                return url === currentUrl || skipPatterns.some((pattern) => pattern.test(url));
            };

            // ищем "нормальный" предыдущий url
            let targetUrl = this.defaultUrl;
            for (let i = historyStack.length - 1; i >= 0; i--) {
                const url = historyStack[i];
                if (url && !shouldSkip(url)) {
                    targetUrl = url;
                    break;
                }
            }

            // чтобы не зациклиться — убираем текущий из стека
            if (historyStack[historyStack.length - 1] === currentUrl) {
                historyStack.pop();
                sessionStorage.setItem('navigationHistory', JSON.stringify(historyStack));
            }

            // переход с обновлением данных
            router.visit(targetUrl, {
                preserveState: false, // грузим свежие данные
                replace: true,        // не плодим историю
            });
        },
    },
    mounted() {
        // пишем историю при каждой навигации
        router.on('navigate', () => {
            const currentUrl = window.location.pathname + window.location.search;
            const skipPatterns = this.skipRoutes.map(
                (route) => new RegExp(route.replace(':id', '\\d+'))
            );

            // не пишем пропущенные роуты
            if (!skipPatterns.some((pattern) => pattern.test(currentUrl))) {
                let historyStack = JSON.parse(sessionStorage.getItem('navigationHistory') || '[]');

                if (historyStack[historyStack.length - 1] !== currentUrl) {
                    historyStack.push(currentUrl);
                    if (historyStack.length > 10) {
                        historyStack.shift();
                    }
                    sessionStorage.setItem('navigationHistory', JSON.stringify(historyStack));
                }
            }
        });
    },
};
</script>
