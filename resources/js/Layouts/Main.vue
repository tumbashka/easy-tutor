<template>
    <v-app>
        <Head>
            <title>{{ actualTitle }}</title>
            <link rel="icon" :href="icon">
        </Head>

        <Menu/>

        <FlashMessages :messages="$page.props.session"/>

        <v-main>
            <div class="container-fluid ">
                <slot/>
            </div>
        </v-main>
    </v-app>
</template>

<script>
import {Head} from "@inertiajs/vue3";
import Menu from "../Components/Navbar/Menu.vue";
import FlashMessages from "../Components/FlashMessages.vue";


export default {
    components: {FlashMessages, Menu, Head},
    props: {
        title: {
            type: [String, null],
        },
        icon: {
            type: [String],
            default: '/images/icons/favicon.ico',
        },
    },
    computed: {
        actualTitle() {
            return this.title ?? this.$page.props.appName;
        },
    }
}
</script>

<style>

</style>