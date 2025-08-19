<template>
    <!-- Верхняя панель -->
    <v-app-bar app color="primary" class="mb-3 bg-primary-gray-gradient-180" elevation="4" height="55">
        <div class="d-flex align-center justify-space-between w-100 px-10">
            <!-- Логотип -->
            <Brand :url="route('schedule.index')"/>

            <v-btn
                icon="mdi-menu"
                class="d-lg-none"
                @click="toggleDrawer"
            />
            <!-- Меню для больших экранов -->
            <div class="d-none d-lg-flex justify-space-around w-100 align-center">
                <div v-if="guest()" class="justify-end w-100">
                    <NavItem name="Вход" :url="route('login')" active-link-pattern="login*"/>
                    <NavItem name="Регистрация" :url="route('register')" active-link-pattern="register*"/>
                </div>

                <div v-if="auth() && emailVerified()">
                    <NavItem name="Главная" :url="route('home')" active-link-pattern="schedule*"/>
                    <NavItem name="Доски" :url="route('boards.index')" active-link-pattern="boards*"/>
                    <NavItem name="Ученики" :url="route('students.index')" active-link-pattern="students*"/>
                    <NavItem name="Окна" :url="route('free-time.index')" active-link-pattern="free-time*"/>
                    <NavItem name="Список дел" :url="route('tasks.index')" active-link-pattern="tasks*"/>
                    <v-badge @click="" location="top right" color="white" :content="5" :offset-y="5" offset-x="-5" class="no-select">
                        <NavItem name="Сообщения" :url="route('chat.index')" active-link-pattern="chat*"/>
                    </v-badge>
                </div>
                <TimeWidget v-if="auth() && emailVerified()" :lessons="($page.props.today_actual_lessons)"/>


                <div v-if="auth()" class="d-flex align-center">
                    <NavItem v-if="can('admin-access')" name="Админ панель" :url="route('admin.dashboard')"/>

                    <!-- Меню статистики -->
                    <v-menu v-if="emailVerified()" offset-y transition="slide-y-transition">
                        <template #activator="{ props }">
                            <v-btn v-bind="props" class="text-white text-subtitle-1 px-2">Статистика</v-btn>
                        </template>
                        <v-list dense>
                            <v-list-subheader>Доходы</v-list-subheader>
                            <v-list-item @click="openUrl(route('statistic.earnings.period'))">
                                <v-list-item-title>По периодам</v-list-item-title>
                            </v-list-item>
                            <v-list-item @click="openUrl(route('statistic.earnings.students'))">
                                <v-list-item-title>По ученикам</v-list-item-title>
                            </v-list-item>
                            <v-divider/>
                            <v-list-subheader>Занятия</v-list-subheader>
                            <v-list-item @click="openUrl(route('statistic.lessons.period'))">
                                <v-list-item-title>По периодам</v-list-item-title>
                            </v-list-item>
                            <v-list-item @click="openUrl(route('statistic.lessons.students'))">
                                <v-list-item-title>По ученикам</v-list-item-title>
                            </v-list-item>
                            <v-divider/>
                            <v-list-subheader>Рабочие часы</v-list-subheader>
                            <v-list-item @click="openUrl(route('statistic.time.period'))">
                                <v-list-item-title>По ученикам</v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>

                    <!-- Меню пользователя -->
                    <v-menu offset-y transition="slide-y-transition">
                        <template #activator="{ props }">
                            <v-btn v-bind="props" class="text-white text-subtitle-1 px-2">
                                {{ $page.props.auth.user?.name || 'Пользователь' }}
                            </v-btn>
                        </template>
                        <v-list dense>
                            <v-list-item v-if="emailVerified()" @click="openUrl(route('user.index'))">
                                <v-list-item-title>Профиль</v-list-item-title>
                            </v-list-item>
                            <v-list-item v-if="emailVerified()" @click="openUrl(route('user.settings.index'))">
                                <v-list-item-title>Настройки</v-list-item-title>
                            </v-list-item>
                            <v-list-item @click="logout" as="button">
                                <v-list-item-title>Выйти</v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                    <v-badge @click="" location="top right" color="white" :content="5" :offset-y="10" class="no-select">
                        <v-btn icon="mdi-bell"/>
                    </v-badge>
                </div>
            </div>
        </div>
    </v-app-bar>

    <!-- Боковое меню для маленьких экранов -->
    <v-navigation-drawer v-model="drawer" app temporary>
        <v-list>
            <template v-if="guest()">
                <v-list-item @click="openUrl(route('login'))">
                    <v-list-item-title>Вход</v-list-item-title>
                </v-list-item>
                <v-list-item @click="openUrl(route('register'))">
                    <v-list-item-title>Регистрация</v-list-item-title>
                </v-list-item>
            </template>

            <template v-if="auth() && emailVerified()">
                <v-list-item @click="openUrl(route('home'))">
                    <v-list-item-title>Главная</v-list-item-title>
                </v-list-item>
                <v-list-item @click="openUrl(route('boards.index'))">
                    <v-list-item-title>Доски</v-list-item-title>
                </v-list-item>
                <v-list-item @click="openUrl(route('students.index'))">
                    <v-list-item-title>Ученики</v-list-item-title>
                </v-list-item>
                <v-list-item @click="openUrl(route('free-time.index'))">
                    <v-list-item-title>Окна</v-list-item-title>
                </v-list-item>
                <v-list-item @click="openUrl(route('tasks.index'))">
                    <v-list-item-title>Список дел</v-list-item-title>
                </v-list-item>
                <v-list-item @click="openUrl(route('chat.index'))">
                    <v-list-item-title>Сообщения</v-list-item-title>
                </v-list-item>
            </template>

            <template v-if="auth()">
                <v-list-item v-if="can('admin-access')" @click="openUrl(route('admin.dashboard'))">
                    <v-list-item-title>Админ панель</v-list-item-title>
                </v-list-item>

                <v-list-group value="statistic">
                    <template #activator="{ props }">
                        <v-list-item v-bind="props" title="Статистика"></v-list-item>
                    </template>
                    <v-list-item @click="openUrl(route('statistic.earnings.period'))">Доходы по периодам</v-list-item>
                    <v-list-item @click="openUrl(route('statistic.earnings.students'))">Доходы по ученикам</v-list-item>
                    <v-list-item @click="openUrl(route('statistic.lessons.period'))">Занятия по периодам</v-list-item>
                    <v-list-item @click="openUrl(route('statistic.lessons.students'))">Занятия по ученикам</v-list-item>
                    <v-list-item @click="openUrl(route('statistic.time.period'))">Рабочие часы</v-list-item>
                </v-list-group>

                <v-list-item @click="openUrl(route('user.index'))">Профиль</v-list-item>
                <v-list-item @click="openUrl(route('user.settings.index'))">Настройки</v-list-item>
                <v-list-item @click="logout">Выйти</v-list-item>
            </template>
        </v-list>
    </v-navigation-drawer>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import {Link, router} from '@inertiajs/vue3';
import NavItem from './NavItem.vue';
import Brand from './Brand.vue';
import {route} from 'ziggy-js';
import TimeWidget from "./TimeWidget.vue";

export default defineComponent({
    name: 'Menu',
    components: {TimeWidget, Brand, NavItem, Link},
    data() {
        return {
            drawer: false,
        };
    },
    methods: {
        route,
        toggleDrawer() {
            this.drawer = !this.drawer;
        },
        logout() {
            router.post(route('logout'));
        },
        openUrl(url: string) {
            this.drawer = false;
            router.visit(url);
        },
    },
});
</script>

<style scoped>
.no-select {
    user-select: none; /* нельзя выделить мышкой */
}

</style>
