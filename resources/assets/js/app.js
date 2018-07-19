
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import VueRouter from 'vue-router';
import axios from 'axios';
import router from './routes';
import Highcharts from 'highcharts';
import HighchartsVue from 'highcharts-vue'
import loadWordcloud from 'highcharts/modules/wordcloud';
import Store from './Store';


window.Vue = require('vue');
loadWordcloud(Highcharts);
Vue.use(VueRouter);
Vue.use(HighchartsVue)

Vue.component('navbar', require('./components/layout/Navbar.vue'));
Vue.component('sidebar', require('./components/layout/Sidebar.vue'))
Vue.component('layout', require('./components/layout/Layout.vue'));  
Vue.component('modal-score', require('./components/Modal/Score.vue'));
Vue.component('modal-alert', require('./components/Modal/Alert.vue'));
Vue.component('pagination', require('./components/layout/Pagination.vue'));
Vue.component('bar-chart', require('./components/Chart/BarChart.vue'));
Vue.component('pie-chart', require('./components/Chart/PieChart.vue'));
Vue.component('line-chart', require('./components/Chart/LineChart.vue'));
Vue.component('word-cloud', require('./components/Chart/WordCloud.vue'));
Vue.component('text-field', require('./components/TextFieldGroup.vue'));
Vue.component('index', require('./components/layout/index.vue'));

//axios.defaults.baseURL = 'http://localhost:'+window.location.port+'/'; //ganti dengan IP Address
axios.defaults.headers.common["Content-Type"] = 'application/json' 

const app = new Vue({
    el: '#app',
    router,
    store : Store
});
