<template>
    <div>
        <v-row dense>
            <!-- <v-col justify="center" align="center" class="mb-2">
                <v-img
                    :src="require('~/assets/images/provincial_logo.png')"
                    :height="image_height"
                    contain
                />
            </v-col> -->
        </v-row>
        <v-row dense>
            <v-col justify="center" align="center">
                <v-card
                    max-width="650px"
                    elevation="2"
                >
                    <v-card-text>
                        <alert
                            class="text-left"
                            :show="login_failed"
                            :color="'red'"
                            :icon="'mdi-alert'"
                            :message="message"
                            :message_bag="error_bag"
                            @dismiss_alert="dismissAlert"
                            dense
                        />
                        <validation-observer
                            ref="observer"
                            v-slot="{ invalid }"
                        >
                            <v-form @submit.prevent="login">
                                <v-row>
                                    <v-col class="py-0 mt-5">
                                        <validation-provider
                                            rules = 'required'
                                            name='Email'
                                            v-slot="{ errors }"
                                        >
                                            <v-text-field
                                                ref="Email"
                                                v-model.lazy="email"
                                                label="Email"
                                                outlined
                                                required
                                                prepend-inner-icon="mdi-account-circle"
                                                :error-messages="errors"
                                                @keydown="autoRemoveErrorAlert"
                                            />
                                        </validation-provider>
                                    </v-col>
                                </v-row>
                                <v-row
                                    align="center"
                                    justify="space-around"
                                >
                                    <v-col class="pb-0">
                                        <validation-provider
                                            rules = 'required'
                                            name='Password'
                                            v-slot="{ errors }"
                                        >
                                            <v-text-field
                                                v-model="password"
                                                label="Password"
                                                required
                                                outlined
                                                prepend-inner-icon="mdi-shield-key"
                                                :error-messages="errors"
                                                :append-icon="show_password ? 'mdi-eye' : 'mdi-eye-off'"
                                                :type="show_password ? 'text' : 'password'"
                                                @click:append="show_password = !show_password"
                                                @keydown="autoRemoveErrorAlert"
                                            />
                                        </validation-provider>
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col
                                        align="center"
                                        justify="space-around"
                                    >
                                        <v-btn
                                            depressed
                                            color="primary"
                                            type="submit"
                                            :disabled="invalid || !email || !password"
                                            large
                                        >
                                            Login
                                        </v-btn>
                                    </v-col>
                                </v-row>
                            </v-form>
                        </validation-observer>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>
<script>
import { ValidationObserver, ValidationProvider } from 'vee-validate'
const Alert = () => import('../components/Alert.vue');
export default {
	name: 'LoginPage',
	layout: 'guest',
    components: {
		ValidationProvider,
		ValidationObserver,
        Alert
	},
    data() {
        return {
            email: null,
            password: null,
            login_failed: false,
            error_bag: null,
            message: null,
            show_password: false,
            submitted: false,
        }
    },
    computed: {
        // ...mapGetters(['isAuthenticated']),
        image_height() {
            switch (this.$vuetify.breakpoint.name) {
                case 'xs': return '300px'
                case 'xl': return '450px'
                default : return '380px'
            }
        },
    },
	methods: {
        focus() {
            this.$refs.email.focus()
        },
        autoRemoveErrorAlert() {
            if (this.login_failed) this.login_failed = false
        },
        dismissAlert() {
            this.login_failed = false;
        },
        async login() {
            this.$store.commit("setLoader", true);
            this.submitted = true;
            this.login_failed = false, this.error_bag = this.message =null;
            await this.$auth.loginWith('laravelSanctum', {
				data: { email: this.email, password: this.password }
			})
            .then((response) =>{
                var auth = JSON.parse(JSON.stringify(response.data.user))
                auth.permissionsArray = []
                auth.rolesArray = auth.roles.reduce((roles, role) => {
                    role.permissions.forEach(permission => {
                        if (!auth.permissionsArray?.includes(permission?.name?.split('-')[1])) {
                            auth.permissionsArray?.push(permission?.name?.split('-')[1])
                        }
                        auth.permissionsArray.push(permission.name)
                    })
                    roles.push(role.name)
                    return roles
                }, [])
                
                this.$auth.setUser(auth)
                this.$nextTick(() => {
                    this.$router.push({path: '/auth'})
                })
                // this.$store.commit("setLoader", false);
            })
            .catch(e => {
                this.login_failed = true;
                this.message = 'Authentication Failed!'
                this.error_bag = Object.values(e.response.data.errors).flat();
                // this.$store.commit("setLoader", false);
            })
        },
	},
    mounted() {
        this.$store.commit("setLoader", false);
    }
}
</script>

<style scoped>
.center {
  margin: auto;
  width: 60%;
  border: 3px solid #73AD21;
  padding: 10px;
}
</style>