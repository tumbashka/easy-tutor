<template>
    <Head :title="'Расписание'"/>

    <WeekNavigator
        :week-days="weekDays"
        :week-offset="weekOffset"
        @change-week="changeWeek"
    />

    <v-fade-transition mode="out-in">
        <div :key="weekOffsetTemp + '-' + loading">
            <WeekSchedule
                v-if="!loading"
                :week-days="weekDays"
                :lessons-on-days="lessonsOnDays"
                :week-offset="weekOffsetTemp"
            />

            <v-container v-else fluid class="relative">
                <v-row justify="center">
                    <v-col
                        v-for="n in 7"
                        :key="'sk-' + n"
                        :cols="colsForBreakpoint"
                    >
                        <v-skeleton-loader type="card" class="mb-3"/>
                    </v-col>
                </v-row>
            </v-container>
        </div>
    </v-fade-transition>

</template>

<script>
import WeekSchedule from '../../../Components/Schedule/WeekSchedule.vue'
import WeekNavigator from '../../../Components/Schedule/WeekNavigator.vue'
import {Head, router} from '@inertiajs/vue3'

export default {
    components: {Head, WeekSchedule, WeekNavigator},
    props: {
        weekDays: Array,
        lessonsOnDays: Object,
        weekOffset: Number
    },
    data() {
        return {
            loading: false,
            transitionName: 'slide-left',
            weekOffsetTemp: this.weekOffset,
            windowWidth: window.innerWidth,
        }
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
        changeWeek(offset) {
            this.transitionName = offset > 0 ? 'slide-left' : 'slide-right';

            this.weekOffsetTemp += offset;
            this.loading = true;

            router.visit(route('schedule.index', {week: this.weekOffsetTemp}), {
                preserveState: false,
                preserveScroll: true,
                onSuccess: () => {
                    this.loading = false;
                },
            });

        },
        afterLeave() {
            if (this.pendingVisit) {
                this.pendingVisit();
                this.pendingVisit = null;
            }
        },
    }
}
</script>

<style>

</style>
