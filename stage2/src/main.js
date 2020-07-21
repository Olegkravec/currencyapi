import Vue from 'vue'
import router from './router'
import App from './App'
import AuthInterPlugin from '@/plugins/AuthorizationInterceptorPlugin'
import VueEvents from 'vue-events'
import VueLocalStorage from 'vue-localstorage'
const dotenv = require('dotenv')
Vue.config.productionTip = false
Vue.config.devtools = process.env.NODE_ENV === 'development'
dotenv.config()

Vue.use(AuthInterPlugin)
Vue.use(VueEvents)
Vue.use(VueLocalStorage)

new Vue({
    router,
    render: h => h(App),
    el: '#app'
})
