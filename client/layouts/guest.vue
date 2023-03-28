<template>
    <v-app>
        <v-main>
            <v-container bg fill-height grid-list-md text-xs-center>
                <!-- <v-layout row wrap align-center>
                    <v-flex>
                        <Nuxt />
                    </v-flex>
                </v-layout> -->
                <v-row justify="center" align="center">
                    <v-col cols="12" sm="12">
                        <Nuxt />
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </v-app>
</template>
<script>
export default {
    name: 'GuestLayout',
    layout: 'guest',
    middleware: 'auth',
    auth : 'guest',
    data () {
        return {
        }
    }
}
</script>