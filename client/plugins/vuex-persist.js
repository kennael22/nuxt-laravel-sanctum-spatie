// ~/plugins/vuex-persist.js
// import VuexPersistence from 'vuex-persistedstate'

// export default ({ store }) => {
//   VuexPersistence({
//   /* your options */
//     key: 'vuex', // The key to store the state on in the storage provider.
//     storage: window.localStorage, // or window.sessionStorage or localForage 
//   }).plugin(store);
// }
import createPersistedState from 'vuex-persistedstate'

export default ({store}) => {
  createPersistedState({
      key: 'vuex',
  })(store)
}