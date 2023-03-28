import colors from 'vuetify/es5/util/colors'

export default {
  // Disable server-side rendering: https://go.nuxtjs.dev/ssr-mode
  ssr: false,

  // Global page headers: https://go.nuxtjs.dev/config-head
  head: {
    titleTemplate: '%s - client',
    title: 'client',
    htmlAttrs: {
      lang: 'en'
    },
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: '' },
      { name: 'format-detection', content: 'telephone=no' }
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
    ]
  },

  // Global CSS: https://go.nuxtjs.dev/config-css
  css: [
  ],

  // Plugins to run before rendering page: https://go.nuxtjs.dev/config-plugins
  plugins: [
		'~/plugins/vee-validate.js',
    '~/plugins/vuex-persist.js',
    '~/plugins/prototype.js',
  ],

  // Auto import components: https://go.nuxtjs.dev/config-components
  components: true,

  // Modules for dev and build (recommended): https://go.nuxtjs.dev/config-modules
  buildModules: [
    // https://go.nuxtjs.dev/vuetify
    '@nuxtjs/vuetify',
  ],

  // Modules: https://go.nuxtjs.dev/config-modules
  modules: [
    '@nuxtjs/axios',
    '@nuxtjs/auth-next'
  ],

  // Vuetify module configuration: https://go.nuxtjs.dev/config-vuetify
  vuetify: {
    customVariables: ['~/assets/variables.scss'],
    theme: {
      dark: false,
      themes: {
        dark: {
          primary: colors.blue.darken2,
          accent: colors.grey.darken3,
          secondary: colors.amber.darken3,
          info: colors.teal.lighten1,
          warning: colors.amber.base,
          error: colors.deepOrange.accent4,
          success: colors.green.accent3
        }
      }
    }
  },

  // Build Configuration: https://go.nuxtjs.dev/config-build
  build: {
  },
  axios: {
    proxy: true,
    credentials: true
  },
  proxy: {
    '/server': {
      target: 'http://127.0.0.1:8000',
      pathRewrite: { '^/server': '/' }
    }
  },
  proxy: {
		'/server': {
			target: 'http://127.0.0.1:8000',
			pathRewrite: { '^/server': '/' }
		}
	},
  auth: {
    strategies: {
			laravelSanctum: {
				provider: 'laravel/sanctum',
				url: '/server',
				endpoints: {
					login: { url: 'api/auth/login', method: 'post', propertyName: 'access_token' },
					user: { url: 'api/auth/user', method: 'get', propertyName: false },
					logout: { url: 'api/auth/logout', method: 'post' },
				},
				user: false,
				token: {
					property: 'access_token',
					required: true,
					global: true,
					maxAge: 43200,
					type: 'Bearer',
					name: 'Authorization'
				},
			}
		}
  }
}
