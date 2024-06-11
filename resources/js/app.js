
import 'bootstrap/dist/js/bootstrap.js';
// import './main.js';
import './bootstrap';
import { createApp } from 'vue/dist/vue.esm-bundler.js';
import { createRouter, createWebHistory } from 'vue-router';
import Routes from './routes.js';
import Echo from 'laravel-echo';
const app = createApp({});
const router = createRouter({
    routes: Routes,
    history: createWebHistory(),
})
app.use(router);
app.mount('#app')
