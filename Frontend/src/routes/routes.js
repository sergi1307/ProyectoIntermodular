import PaginaPrincipal from '../views/index.vue';
import login from '../views/login.vue';

import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/',
        name: 'principal',
        component: PaginaPrincipal
    },
    {
        path: '/login',
        name: 'login',
        component: login
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;