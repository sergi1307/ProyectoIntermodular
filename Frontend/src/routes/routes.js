import { createRouter, createWebHistory } from "vue-router";

import PaginaPrincipal from '../views/index.vue';

routes: [
    {
        path: '/',
        name: 'principal',
        component: PaginaPrincipal
    }
]