import PaginaPrincipal from '../views/paginaDeInicio.vue';
import login from '../views/login.vue';
import products from '../views/products.vue';
import mapaGeneral from '../views/mapaGeneral.vue';
import mapaEspecifico from '../views/mapaEspecifico.vue';
import general from '../views/general.vue';
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
        component: login,
        
    },
    {
        path: '/products',
        name: 'products',
        component: products,
        meta: { vendedor: true }
    },
    {
        path: '/mapa',
        name: 'mapa_general',
        component: mapaGeneral,
        meta: { comprador: true }
    },
    {
        path: '/mis-tiendas',
        name: 'mapa_pruebas',
        component: mapaEspecifico,
        meta: { comprador: true }
    },
    {
        path: '/general',
        name: 'general',
        component: general,
        meta: { comprador: true }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;