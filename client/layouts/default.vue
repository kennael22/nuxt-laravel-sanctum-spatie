<template>
	<v-app dark>
		<v-navigation-drawer
			v-model="drawer"
			:mini-variant="miniVariant"
			:clipped="clipped"
			fixed
			app
		>
		<v-list>
			<v-list-item
				v-for="(item, i) in items"
				:key="i"
				:to="item.to"
				router
				exact
			>
				<v-list-item-action>
					<v-icon>{{ item.icon }}</v-icon>
				</v-list-item-action>
				<v-list-item-content>
					<v-list-item-title v-text="item.title" />
				</v-list-item-content>
				</v-list-item>
				<v-list-group
					v-if="$can([
						'view-role-list',
						'view-user-list',
					])"
					:value="['user', 'role'].includes($route.name)"
					no-action
					title="Access Control"
				>
					<template v-slot:activator>
						<v-list-item-icon>
							<v-icon>mdi-account-cog-outline</v-icon>
						</v-list-item-icon>
						<v-list-item-content>
							<v-list-item-title>Access Control</v-list-item-title>
						</v-list-item-content>
					</template>
					<v-list-item v-if="$can('view-role-list')" :to="{ path: '/auth/role' }" title="Roles">
						<v-list-item-title>Roles</v-list-item-title>
						<v-list-item-icon>
							<v-icon>mdi-account-key</v-icon>
						</v-list-item-icon>
					</v-list-item>
					<v-list-item v-if="$can('view-user-list')" :to="{ path: '/auth/user' }" title="User Management">
						<v-list-item-title>User Management</v-list-item-title>
						<v-list-item-icon>
							<v-icon>mdi-account-multiple-outline</v-icon>
						</v-list-item-icon>
					</v-list-item>
				</v-list-group>
      		</v-list>
			<template v-slot:append>
                <v-dialog v-model="logout_confirm_dialog" max-width="450px">
                    <template v-slot:activator="{ on, attrs }">
                        <v-list nav dense title="Logout">
                            <v-list-item v-bind="attrs" v-on="on">
                                <v-list-item-icon>
                                    <v-icon>mdi-logout</v-icon>
                                </v-list-item-icon>

                                <v-list-item-content>
                                    <v-list-item-title>Logout</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </template>
                    <template v-slot:default="dialog">
                        <v-card>
                            <v-card-title class="headline">
                                <v-icon class="mr-2" size="20px">mdi-logout</v-icon>
                                Logout
                            </v-card-title>
                            <v-card-text>
                                Are you sure you want to logout of the application?
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn text @click.prevent="logout">
                                    Yes
                                </v-btn>
                                <v-btn text @click.prevent="dialog.value = false">
                                    No
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </template>
                </v-dialog>
            </template>
		</v-navigation-drawer>
		<v-app-bar
			:clipped-left="clipped"
			fixed
			app
		>
      	<v-app-bar-nav-icon @click.stop="drawer = !drawer" />
		<v-btn
			icon
			@click.stop="miniVariant = !miniVariant"
		>
        	<v-icon>mdi-{{ `chevron-${miniVariant ? 'right' : 'left'}` }}</v-icon>
      	</v-btn>
		<v-btn
			icon
			@click.stop="clipped = !clipped"
		>
			<v-icon>mdi-application</v-icon>
		</v-btn>
		<v-btn
			icon
			@click.stop="fixed = !fixed"
		>
			<v-icon>mdi-minus</v-icon>
		</v-btn>
		<v-toolbar-title v-text="title" />
      	<v-spacer />
		<!-- <v-btn
			icon
			@click.stop="rightDrawer = !rightDrawer"
		>
			<v-icon>mdi-menu</v-icon>
		</v-btn> -->
    </v-app-bar>
    <v-main>
		<v-container>
			<Nuxt />
		</v-container>
    </v-main>
    <!-- <v-navigation-drawer
		v-model="rightDrawer"
		:right="right"
		temporary
		fixed
    >
		<v-list>
			<v-list-item @click.native="right = !right">
			<v-list-item-action>
				<v-icon light>
				mdi-repeat
				</v-icon>
			</v-list-item-action>
			<v-list-item-title>Switch drawer (click me)</v-list-item-title>
			</v-list-item>
		</v-list>
    </v-navigation-drawer> -->
	<!-- <v-footer
		:absolute="!fixed"
		app
	>
		<span>&copy; {{ new Date().getFullYear() }}</span>
	</v-footer> -->
  </v-app>
</template>

<script>
export default {
	name: 'DefaultLayout',
	layout: 'default',
	middleware: 'auth',
	data () {
		return {
			clipped: false,
			drawer: false,
			fixed: false,
			items: [
				{
					icon: 'mdi-apps',
					title: 'Dashboard',
					to: '/auth'
				},
				{
					icon: 'mdi-chart-bubble',
					title: 'Game',
					to: '/auth/game'
				},
				{
					icon: 'mdi-chart-bubble',
					title: 'Taya',
					to: '/auth/taya'
				}
			],
			miniVariant: false,
			right: true,
			rightDrawer: false,
			title: 'Vuetify.js',
			logout_confirm_dialog: false,
		}
	},
	methods: {
		async logout(){
			await this.$auth.logout()
			this.$router.push({ path: '/login' })
		},
	}
}
</script>
