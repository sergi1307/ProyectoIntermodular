import PaginaPrincipal from '../views/principals/paginaDeInicio.vue';
import login from '../views/principals/login.vue';
import products from '../views/products/dashboard.vue';
import mapaGeneral from '../views/maps/mapaGeneral.vue';
import mapaEspecifico from '../views/maps/mapaEspecifico.vue';
import general from '../views/principals/general.vue';
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
//      meta: { comprador: true } Esto lo pondremos en un futuro para asegurar nuestra ruta
        component: general
    },
    {
        path: '/my-products',
        name: 'my-products',
        component: products
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;