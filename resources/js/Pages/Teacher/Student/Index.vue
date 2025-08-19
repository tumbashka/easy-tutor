<template>
    <Head :title="'Ученики'"/>

    <StudentsMenu/>
    <StudentsTables :studentsOnClasses/>
</template>

<script>
import StudentsTables from '../../../Components/Student/StudentsTables.vue'
import {Head, router} from '@inertiajs/vue3'
import StudentsMenu from "../../../Components/Student/StudentsMenu.vue";

export default {
    components: {
        Head,
        StudentsMenu,
        StudentsTables,
    },
    props: {
        studentsOnClasses: Object,
    },
}
</script>

<style>

</style>
