<template>
    <v-container>
        <v-row justify="center">
            <v-col
                cols="12"
                sm="10"
                md="8"
                lg="6"
                xl="5"
                xxl="4"
                class="px-2"
            >
                <slot />
            </v-col>

            <v-col
                v-if="!!secondCol"
                :class="computedSecondColClasses"
            >
                <slot name="second-col" />
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
    name: 'FormContainer',
    props: {
        secondCol: {
            type: [String, Object, Boolean],
            default: false,
        },
        secondColClasses: {
            type: [String, Array],
            default: () => [
                'col-12',
                'col-sm-10',
                'col-md-8',
                'col-lg-6',
                'col-xl-5',
                'col-xxl-4',
                'px-2',
            ],
        },
    },
    computed: {
        computedSecondColClasses() {
            return Array.isArray(this.secondColClasses)
                ? this.secondColClasses.join(' ')
                : this.secondColClasses;
        },
    },
});
</script>

