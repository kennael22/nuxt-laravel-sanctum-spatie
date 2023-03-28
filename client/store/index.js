export const state = () => ({
    user:{},
    visit_page: {},
    loader: false,
    snackbar: {
        show: false,
        type: "",
        message: "",
    },
})

export const getters = {
    user (state) {
        return state.user
    },
    visit_page (state) {
        return state.visit_page
    },
    loader (state) {
        return state.loader
    },
    snackbar (state) {
        return state.snackbar
    },
}

export const actions = {
    async getUser ({ commit } ,  data ) {
        await commit('setUser', data)
    },
}

export const mutations = {
    setUser (state, data) {
        state.user = data
    },
    setVisitPage (state, { name, param }) {
        state.visit_page[name] = param
    },
    setLoader (state, bool) {
        state.loader = bool
    },
    setSnackbar (state, param = null) {
        // state.snackbar.show = false
        // setTimeout(function() {
            state.snackbar.show = true
            state.snackbar.type = param?.type || "error"
            state.snackbar.message = param?.message || "Failed"
        // }.bind(state), 50);
    },
    unsetSnackbar (state) {
        state.snackbar.show = false
    },

}