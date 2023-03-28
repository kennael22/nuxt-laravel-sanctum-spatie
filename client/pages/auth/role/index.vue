<template>
    <div>
        <v-data-table
            :headers="headers"
            :items="roles.data"
            :options.sync="options"
            :server-items-length="totalDocument"
            :footer-props="{
                'items-per-page-options': [5, 10, 25, 50, 100],
                'show-first-last-page': true
            }"
            @update:items-per-page="reloadData()"
            @update:page="reloadData()"
            @update:sort-desc="reloadData()"
            :page.sync="page"
            :items-per-page="10"
            class="elevation-1 ma-4"
            :fixed-header="roles.data.length > 10"
            :height="roles.data.length > 10 ? '75vh' : ''"
        >
            <template v-slot:[`footer.page-text`]="items" v-if="options.list.length">
                Page:&nbsp;
                <select
                    v-if="options.list.length > 1"
                    v-model="options.page"
                    :title="`Jump to page 1-${Math.max(...options.list)}`"
                    class="mr-4 px-2 pagination-select"
                    :style="`border:2px solid #F7F7F7;border-radius:5px;text-align:center;${$vuetify.theme.dark ? 'color:white' : ''}`"
                >
                    <option v-for="(page, i) in options.list" :value="page" :label="page" :key="i" style="color:black" />
                </select>
                <span v-else class="mr-4"> {{ options.list[0] }} </span>
                {{ (items.pageStart).toLocaleString() }} - {{ (items.pageStop).toLocaleString() }} of {{ (items.itemsLength).toLocaleString() }}
            </template>

            <template v-slot:top>
                <v-toolbar flat>
                    <v-text-field v-model="search" label="Search" placeholder="Role" prepend-inner-icon="mdi-magnify" hide-details @keyup.enter="searchFunction()">
                        <template v-slot:append-outer v-if="!!search">
                            <v-btn depressed small @click="searchFunction()">SEARCH</v-btn>
                        </template>
                    </v-text-field>
                    <v-spacer></v-spacer>
                    <v-menu
                        bottom
                        left
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                icon
                                v-bind="attrs"
                                v-on="on"
                            >
                                <v-icon>mdi-dots-vertical</v-icon>
                            </v-btn>
                        </template>

                        <v-list dense>
                            <v-list-item v-if="can.add" @click.stop="addRole()">
                                <v-list-item-icon>
                                    <v-icon>mdi-plus</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title>Add New</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item @click.stop="reloadData()">
                                <v-list-item-icon>
                                    <v-icon>mdi-reload {{ loader ? 'mdi-spin' : ''}}</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title>Reload</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                </v-toolbar>
            </template>

            <template v-slot:[`item.permissions`]="{ item }">
                <v-expansion-panels flat dense title="User Access/Permissions" v-if="item.permissions.length">
                    <v-expansion-panel style="background-color:rgba(0,0,0,0) !important">
                        <v-expansion-panel-header class="py-0 px-2">
                            <template v-slot:default="{ open }">
                                <v-row no-gutters>
                                    <v-col cols="12" class="text--secondary">
                                        <v-fade-transition leave-absolute>
                                            <span v-if="open" key="0"> Permissions </span>
                                            <div v-else key="1">
                                                <v-chip class="ma-1" label small v-for="(access, i) in getCategAccess(item.permissions)" :key="i" :color="getCategColor(access)" dark>{{ access }}</v-chip>
                                            </div>
                                        </v-fade-transition>
                                    </v-col>
                                </v-row>
                            </template>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <v-chip class="ma-1" label small v-for="permission in item.permissions" :key="permission.name" :color="getColor(permission.name)" dark>
                                {{ permission.name }}
                            </v-chip>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
            </template>

            <template v-slot:item.actions="{ item }">
                <div :style="`${can.edit && can.remove ? 'display:flex;' : ''}width:80px;`">
                    <v-icon class="mr-2" @click="viewRoleUser(item)"> mdi-account-group </v-icon>
                    <v-icon v-if="can.edit" class="mr-2" @click="editRole(item)"> mdi-pencil </v-icon>
                    <v-icon v-if="can.remove" @click="confirm_dialog_item=item" :disabled="auth.rolesArray.includes(item.name)"> {{ auth.rolesArray.includes(item.name) ? 'mdi-cancel' : 'mdi-delete' }} </v-icon>
                </div>
            </template>

            <template v-slot:no-data>
                {{ loader ? 'Loading... Please wait' : 'No data available' }}
            </template>

        </v-data-table>

        <v-card  style="border-top-left-radius: 0; border-top-right-radius: 0;">
            <div style="position: absolute; top: -59px; font-size:.8rem" class="ml-8 hidden-xs-only">
                <v-btn text small plain @click="reloadData()"> <v-icon left> {{ loader ? 'mdi-spin mdi-reload' : 'mdi-reload' }} </v-icon> Reload</v-btn>
            </div>
        </v-card>

        <v-card class="mt-1 d-xs-flex d-sm-none d-md-none d-lg-none d-xl-none" flat>
            <v-btn text small plain @click="reloadData()"> <v-icon left> {{ loader ? 'mdi-spin mdi-reload' : 'mdi-reload' }} </v-icon> Reload</v-btn>
        </v-card>

        <v-dialog width="450px" :value="!!confirm_dialog_item" persistent>
            <v-card>
                <v-card-title class="text-h5">
                    <v-icon left color="error">mdi-alert-remove-outline</v-icon>
                    Remove
                </v-card-title>

                <v-card-text>
                    Are you sure you want to remove role "{{ !!confirm_dialog_item ? confirm_dialog_item.name : '' }}"
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text @click="confirmDelete()"> YES </v-btn>
                    <v-btn text @click="confirm_dialog_item=false"> NO </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <RoleAddEditDialog v-if="!!manage_dialog" :manage_dialog="manage_dialog" @close-dialog="manage_dialog=false" @reload="reloadData()" />
        <RoleUserListDialog v-if="!!view_user_dialog" :view_user_dialog="view_user_dialog" @close-dialog="view_user_dialog=false"/>

    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import RoleAddEditDialog from '../../../components/role/RoleAddEditDialog'
import { permissionsCategoryColor } from '../../../plugins/helpers'
export default {
	name: 'RolePage',
	middleware: 'auth',
	layout: 'auth',
    components: {
        RoleAddEditDialog,
        RoleUserListDialog: () => import(/* webpackChunkName: "view_role_user_list" */ "../../../components/role/RoleUserListDialog"),
    },
    data: () => ({
        search: null,
        options: {},
        roles: {data:[]},
        totalDocument: 0,
        page: 1,
        manage_dialog: false,
        confirm_dialog_item: false,
        permissions: [],
        role_user: [],
        view_user_dialog: false,
    }),

    computed: {
        ...mapGetters(['visit_page', 'loader']),
        can() {
            let add = this.$can('add-role')
            let edit = this.$can('edit-role')
            let remove = this.$can('delete-role')

            return { add, edit, remove }
        },
		auth() {
			return this.$auth?.user || {}
		},
        headers() {
            let headers = []
            return headers.concat([
                { align: "start", text: "Role", value: "name", width: "180px" },
                { text: "Description", value: "description", width: "200px" },
                { text: "Access", value: "permissions" },
                { text: "Action", value: "actions", width: "100px", sortable: false, align: 'end' }
            ])
        },
    },

    async created() {
        this.options.itemsPerPage = this.visit_page[this.$route.path]?.per_page ?? 10
        this.options.page = (this.visit_page[this.$route.path]?.current_page ?? 1)
        this.options.list = []
        await this.getDataFromApi()
        await this.getPermissions()
    },

    methods: {
        reloadData() {
            this.getDataFromApi();
        },
        searchFunction() {
            this.options.page == 1 ? this.getDataFromApi() : this.options.page = 1
        },
        async getDataFromApi() {
            this.$store.commit("setLoader", true)
            this.$store.commit('setVisitPage', {
                name: this.$route.path,
                param: {
                    current_page: this.options.page ?? 1,
                    per_page: this.options.itemsPerPage ?? 10,
                }
            })
            var opt_page = Object.values(this.options).length ? this.options.page : 1
            await this.$axios.get(`/server/api/auth/roles` +
                `?page=${opt_page}` +
                `&rows=${this.options.itemsPerPage}` +
                `${!!this.options?.sortBy?.length?`&order_by=${this.options?.sortBy[0]?.match('updated_at')?'updated_at':this.options?.sortBy[0]},${this.options?.sortDesc[0]?'desc':'asc'}`:''}` +
                `${!!this.search?'&search='+this.search:''}` +
                `&with_role_user=true`
            ).then((res)=>{

                this.roles = res.data.meta;
                this.role_user = res.data.role_user

                this.totalDocument = res.data.meta.total
                this.options.list = Array(res.data?.meta?.last_page).fill(0).reduce((value, row, i)=>{ value.push(i+1); return value; }, [])
                this.$store.commit("setLoader", false)
            }).catch(function (thrown) {
                if (thrown?.response?.status != 401) {
                    this.$store.commit("setLoader", false)
                }
            })
        },
        getCategAccess(item_permission) {
            return item_permission.reduce((list, row)=>{
                let p = row?.name?.split('-')[1]
                if (!list.includes(p)) {
                    list.push(p)
                }
                return list
            }, [])
        },
        getColor(permission) {
            let [action, category] = permission.split('-')
            return permissionsCategoryColor(category.toLowerCase().trim())
        },
        getCategColor(category) {
            return permissionsCategoryColor(category.toLowerCase().trim())
        },
        async getPermissions() {
            await this.$axios.get('/server/api/auth/permissions').then(response => {
                var permits = response.data
                let permissions = permits.map(p=>p.name)
                let final_permissions = [];
                permissions.forEach(pmt=>{
                    function capitalizeFirstLetter(str) {
                        return (str.charAt(0).toUpperCase() + str.substring(1)).trim()
                    }
                    let category = pmt.split('-')
                    if (!final_permissions.map(fp=>fp.category).includes(capitalizeFirstLetter(category[1]))) {
                        final_permissions.push({ category: capitalizeFirstLetter(category[1]), metadata: [pmt] })
                    } else {
                        let position = final_permissions.indexOf(final_permissions.find(fp=>fp.category == capitalizeFirstLetter(category[1])))
                        final_permissions[position].metadata.push(pmt)
                    }
                })
                this.permissions = { formatted: final_permissions, array_collection: permissions };
            });
        },
        addRole() {
            var formated_permissions = JSON.parse(JSON.stringify(this.permissions.formatted));
            formated_permissions.forEach(element => {
                let cloneMetaData = JSON.parse(JSON.stringify(element.metadata))
                element.metadata = []
                cloneMetaData.forEach(permit=>{
                    element.metadata.push({name: permit, selected: false})
                })
                element.count = `${element.metadata.filter(prt=>prt.selected==true).length}/${element.metadata.length}`
                element.selected = element.metadata.filter(prt=>prt.selected==true).length == element.metadata.length
            });

            this.manage_dialog={title: 'Add', item: { id: undefined, role: '', data: formated_permissions }}
        },
        editRole(role) {
            let selectedPermissionsArray = role.permissions.map(p=>p.name)
            var formated_permissions = JSON.parse(JSON.stringify(this.permissions.formatted));
            formated_permissions.forEach(element => {
                let cloneMetaData = JSON.parse(JSON.stringify(element.metadata))
                element.metadata = []
                cloneMetaData.forEach(permit=>{
                    element.metadata.push({name: permit, selected: selectedPermissionsArray.includes(permit)?true:false})
                })
                element.count = `${element.metadata.filter(prt=>prt.selected==true).length}/${element.metadata.length}`
                element.selected = element.metadata.filter(prt=>prt.selected==true).length == element.metadata.length
            });

            this.manage_dialog={title: 'Edit', item: { id: role.id, role: role.name, description: role.description, data: formated_permissions }}
        },
        async confirmDelete() {
            let role = JSON.parse(JSON.stringify(this.confirm_dialog_item))
            let form = {id: role.id, type: 'delete', role: role.name, data: [role]}

            await this.$axios.post(`/server/api/auth/roles/remove/${role.id}`, form).then(response => {
				this.$store.commit('unsetSnackbar')
                this.$nextTick(()=>{
                	this.$store.commit('setSnackbar', response.data)
				})

                if (((this.totalDocument - 1) - (this.options.itemsPerPage*this.options.page-this.options.itemsPerPage)) < 1) {
                    this.options.page = this.options.page > 1 ? (this.options.page - 1) : this.options.page
                }

                this.reloadData()
                this.confirm_dialog_item = false
            }).catch((error)=>{
				this.$store.commit('unsetSnackbar')
                this.$nextTick(()=>{
                	this.$store.commit('setSnackbar', error.response.data)
				})
                this.confirm_dialog_item = false
            });
        },
        viewRoleUser(item) {
            this.view_user_dialog = false
            let role_id = item.id
            let role_user_list = this.role_user.reduce((list, row)=>{
                let this_user_role_ids = row.roles.map(r=>r.id)
                if (this_user_role_ids.includes(role_id)) {
                    list.push(row)
                }
                return list
            }, [])

            this.view_user_dialog = {role_user_list: role_user_list, selected_role: item}
        },
    },
};
</script>

<style>
</style>