import PaginaPrincipal from '../views/principals/paginaDeInicio.vue';
import login from '../views/principals/login.vue';
import products from '../views/products/dashboard.vue';
import mapaEspecifico from '../views/maps/mapaEspecifico.vue';
import general from '../views/principals/general.vue';
import compras from '../views/principals/compras.vue';
import ProductDetails from '../views/products/ProductDetails.vue';
import message from '../views/message/message.vue';
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
        path: '/mis-tiendas',
        name: 'mis-tiendas',
        component: mapaEspecifico,
        meta: { vendedor: true }
    },
    {
        path: '/general',
        name: 'general',
        component: general,
        meta: { comprador: true }
    },
    {
        path: '/my-products',
        name: 'my-products',
        component: products
    },
    {
        path: '/product-details/:id',
        name: 'product-details',
        component: ProductDetails,
        props: true
    },
    {
        path: '/mis-compras',
        name: 'mis-compras',
        component: compras,
        meta: { comprador: true }
    },
    {
        path: '/message',
        name: 'message',
        component: message,
        meta: { vendedor: true }
    },
    {
        path: 'panel-admin',
        name: 'panel-admin',
        component: () => import('../views/principals/panelAdmin.vue'),
        meta: { admin: true }
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;