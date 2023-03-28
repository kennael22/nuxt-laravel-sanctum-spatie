<template>
    <div>
        this is taya
        <v-btn @click="logout">text</v-btn>
    </div>
</template>

<script>
    export default {
        name: 'TayaPage',
        middleware: 'auth',
        methods: {
            async logout() {
                await this.$auth.logout()
                this.$router.push({path:'/login'})
            }
        }
    }
</script>

<style scoped>

</style>