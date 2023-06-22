import Vue from "vue";
import App from "./App.vue";

import axios from 'axios';
import collect from 'collect.js';

Vue.prototype.$http = axios;
Vue.prototype.$c = collect;

Vue.config.productionTip = false;

import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'

// Import Bootstrap and BootstrapVue CSS files (order is important)
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)

import Notifications from '@voerro/vue-notifications'

Vue.component('notifications', Notifications)

import VueRouter from 'vue-router'

Vue.use(VueRouter)

import Sales from './pages/Sales.vue'
import Products from './pages/Products.vue'
import ProductsTypes from './pages/ProductsTypes.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: Sales
  },
  {
    path: '/products',
    name: 'products',
    component: Products
  },
  {
    path: '/products-types',
    name: 'products-types',
    component: ProductsTypes
  },

]

// Crie uma instância do VueRouter e passe as rotas
const router = new VueRouter({
  routes
})

new Vue({
  router,
  render: h => h(App)
}).$mount('#app')
