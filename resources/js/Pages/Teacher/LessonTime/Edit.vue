<template>
    <v-container fluid>
        <v-row justify="center">
            <v-col cols="12" style="max-width: 1000px">
                <LessonEditor
                    :title="title"
                    :student="student"
                    :subjects="subjects"
                    :lesson-times="lessonTimes"
                    mode="edit"
                    :initial-lesson="lessonTime"
                />
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import LessonEditor from '../../../Components/LessonTime/Editor.vue'

export default {
    components: { LessonEditor },
    props: {
        title: String,
        student: Object,
        subjects: Array,
        lessonTimes: Array,
        lessonTime: Object,
    },
}
</script>
