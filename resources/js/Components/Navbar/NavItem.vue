<template>
        <v-btn
            @mousedown="openUrl"
            class="text-decoration-none text-white normal-text text-subtitle-1 px-2"
            :class="activeClasses">
            {{ name }}
        </v-btn>
</template>

<script>
import {Link, router} from "@inertiajs/vue3";

export default {
    components: {
        Link
    },
    props: {
        url: {
            type: String,
            required: true,
            default: ''
        },
        name: {
            type: String,
            required: true,
            default: ''
        },
        activeLinkPattern: {
            type: String,
            required: false,
            default: ''
        },
    },
    computed: {
        activeClasses() {
            if (this.activeLink(this.activeLinkPattern)) {
                return 'border-0 border-b-md border-white';
            } else {
                return '';
            }
        },
    },
    methods:{
        openUrl(){
            router.visit(this.url);
        },
    }
};
</script>

<style scoped>
.normal-text{
    text-transform: none !important;
}

.border-white {
    border-bottom: 2px solid #fdfdfd !important;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 0;
}
</style>
