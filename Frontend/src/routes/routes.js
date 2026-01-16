import PaginaPrincipal from '../views/paginaDeInicio.vue';
import login from '../views/login.vue';
import products from '../views/products.vue';
import mapaGeneral from '../views/mapaGeneral.vue';
import mapaEspecifico from '../views/mapaEspecifico.vue';
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

    },
    // --- RUTAS PARA LOS MAPAS ---
    {
        // Esta es la ruta pública para que cualquiera vea las tiendas
        path: '/mapa',
        name: 'mapa_general',
        component: mapaGeneral
    },
    {
        // Esta ruta es para yo probar el selector de tiendas separado del formulario
        path: '/mis-tiendas',
        name: 'mapa_pruebas',
        component: mapaEspecifico
    }

];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;