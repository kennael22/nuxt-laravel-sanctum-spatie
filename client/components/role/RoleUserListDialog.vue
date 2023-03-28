<template>
    <div>
        <v-row justify="center">
            <v-dialog
                v-model="dialog"
                transition="scale-transition"
                max-width="600px"
                scrollable
            >
                <v-card flat>
                    <v-toolbar>
                        <v-toolbar-title>View Role User</v-toolbar-title>
                        <v-spacer />
                        <v-btn icon @click="dialog=false">
                            <v-icon>mdi-close</v-icon>
                        </v-btn>
                    </v-toolbar>

                    <v-card-text class="pa-0">
                        <v-data-table
                            :headers="headers"
                            :items="view_user_dialog.role_user_list"
                            :search="search"
                            :footer-props="{
                                'show-first-last-page': true,
                                'show-current-page': true
                            }"
                            :items-per-page="-1"
                            :hide-default-footer="true"
                        >
                            <template v-slot:top>
                                <v-toolbar flat>
                                    <v-text-field
                                        v-model="search"
                                        placeholder="Search User"
                                        :label="view_user_dialog.selected_role.name+ ' (' + view_user_dialog.role_user_list.length + ')'"
                                        prepend-inner-icon="mdi-magnify"
                                        class="mt-8"
                                        clearable
                                    >
                                    </v-text-field>
                                </v-toolbar>
                            </template>
                            <template v-slot:[`item.username`]="{ item }">
                                <tr>
                                    <th>
                                        <v-avatar size="32px">
                                            <img
                                                alt="Profile"
                                                :src="item.avatar ? '/server/' + item.avatar : require('~/assets/images/no_image.jpg')"
                                            >
                                        </v-avatar>
                                    </th>
                                    <td class="pl-2">
                                        {{ item.username }}
                                    </td>
                                </tr>
                            </template>
                            <template v-slot:[`item.is_active`]="{ item }">
                                <v-chip
                                    :color="item.hasOwnProperty('is_active') ? (item.is_active ? 'success' : 'error') : ''"
                                    label
                                    small
                                >
                                    {{ item.hasOwnProperty('is_active') ? (item.is_active?'Activated':'Deactivated') : 'N/A' }}
                                </v-chip>
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-card>
            </v-dialog>
        </v-row>
    </div>
</template>

<script>
export default {
    props: ['view_user_dialog'],
    data() {
        return {
            dialog: true,
            search: "",
            headers: [
                { text: "Username", value: "username", width: "10px", sortable: false },
                { text: "Fullname", value: "name" },
                { text: "Status", value: "is_active" },
            ],
        }
    },
    watch: {
        'dialog'(val) {
            if (!val) {
                this.$emit('close-dialog')
            }
        }
    },
}
</script>

<style scoped>
</style>
