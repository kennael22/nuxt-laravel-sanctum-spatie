<template>
    <v-alert
        transition="slide-y-transition"
        v-model="show"
        dismissible
        :color="color"
        border="left"
        prominent
        text
        :icon="icon"
        dense
    >
        {{ message }}
        <div v-if="message_bag!=null">
            <ul>
                <li v-for="(error, index) in message_bag" :key="index">
                    {{error}}
                </li>
            </ul>
        </div>
        <template v-slot:close>
            <v-icon @click="$emit('dismiss_alert')" >mdi-close</v-icon>
        </template>
    </v-alert>
</template>

<script>
export default {
    props: {
        show: Boolean,
        icon: String,
        message: String,
        message_bag: Array|null,
        color: String
    },
}
</script>
