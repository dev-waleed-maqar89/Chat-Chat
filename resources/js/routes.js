import home_page from './components/main/homePage.vue';
import Register from "./components/auth/register.vue";
import Login from "./components/auth/login.vue";

export default [
    {
        name: 'home',
        path: '/',
        component: home_page
    },
    {
        path: '/register',
        name: 'register',
        component: Register
    },
    {
        path: '/login',
        name: 'login',
        component: Login
    },
]
