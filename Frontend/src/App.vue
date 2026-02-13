<script setup>
import Comprador from './components/nav/NavComprador.vue'
import Vendedor from './components/nav/NavVendedor.vue' 
import { RouterView, useRoute } from 'vue-router'
import ToastContainer from './components/ToastContainer.vue' // Importamos nuestro componente
const route = useRoute() 
</script>

<template>
    <ToastContainer /> <!-- Añadimos el contenedor aquí -->
    <Comprador v-if="route.meta.comprador"></Comprador>
    <Vendedor v-if="route.meta.vendedor"></Vendedor>
    <router-view></router-view>
</template>
