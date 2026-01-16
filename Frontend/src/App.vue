<script setup></script>

<template>
  <nav>
    <router-link to="/">Home</router-link>
    <router-link to="/login">Login</router-link>
    <router-link to="/products">Productos</router-link>
    <router-link to="/mapaGeneral">Mapa General</router-link>
    <router-link to="/mapaEspecifico">Mapa Especifico</router-link>
  </nav>
  <router-view></router-view>
</template>

<style scoped></style>
