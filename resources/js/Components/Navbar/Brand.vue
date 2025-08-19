<template>
    <!-- Логотип -->
    <Link :href="url" class="d-flex text-decoration-none text-white align-self-center">
        <div v-html="icon"></div>
        <div class="ms-2 text-h6">{{ actualName }}</div>
    </Link>
</template>

<script>
import {defineComponent} from 'vue'
import {Link} from "@inertiajs/vue3";

export default defineComponent({
    name: "Brand",
    components: {Link},
    props: {
        url: {
            type: String,
            required: true,
            default: ''
        },
        name: {
            type: [String, null],
            required: false,
            default: null,
        },
        icon: {
            type: String,
            required: false,
            default: '<i class="fa-solid fa-tractor fa-xl"></i>'
        },
    },
    computed:{
        actualName(){
            return this.name ?? this.$page.props.appName;
        }
    }

})
</script>

<style scoped>

</style>
