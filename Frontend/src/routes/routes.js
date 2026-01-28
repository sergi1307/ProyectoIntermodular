import PaginaPrincipal from '../views/principals/paginaDeInicio.vue';
import login from '../views/principals/login.vue';
import products from '../views/products/dashboard.vue';
import mapaGeneral from '../views/maps/mapaGeneral.vue';
import general from '../views/principals/general.vue';
import compras from '../views/principals/compras.vue';
import ProductDetails from '../views/products/ProductDetails.vue';
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
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;