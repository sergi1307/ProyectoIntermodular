<script setup>
import Comprador from './components/NavComprador.vue'
import Vendedor from './components/NavVendedor.vue' 
import { RouterView, useRoute } from 'vue-router'
const route = useRoute() 
</script>

<template>
    <Comprador v-if="route.meta.comprador"></Comprador>
    <Vendedor v-if="route.meta.vendedor"></Vendedor>
    <router-view></router-view>
</template>
