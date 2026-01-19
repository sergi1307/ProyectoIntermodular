<script setup>
import Comprador from './components/nav/NavComprador.vue'
import Vendedor from './components/nav/NavVendedor.vue' 
import { RouterView, useRoute } from 'vue-router'
const route = useRoute() 
</script>

<template>
    <Comprador v-if="route.meta.comprador"></Comprador>
    <Vendedor v-if="route.meta.vendedor"></Vendedor>
    <router-view></router-view>
</template>
