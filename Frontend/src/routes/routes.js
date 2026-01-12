import PaginaPrincipal from '../views/index.vue';
import login from '../views/login.vue';
import products from '../views/products.vue';

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
    },
    {
        path: '/products',
        name: 'products',
        component: products
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;