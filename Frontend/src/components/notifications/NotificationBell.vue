<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const notificaciones = ref([]);
const sinLeer = ref(0);
const mostrarMenu = ref(false);
const token = localStorage.getItem('token');
import url from '../../config/api';

const cargarDatos = async () => {
    try {
        const response = await axios.get(`${url}/api/notifications`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        
        notificaciones.value = response.data.data;
        
        sinLeer.value = notificaciones.value.filter(n => n.read_at === null).length;

    } catch (error) {
        console.log("Error cargando notificaciones:", error);
    }
};

onMounted(() => {
    cargarDatos();
});
</script>

<template>
    <div class="contenedor-campana" @mouseenter="mostrarMenu = true" @mouseleave="mostrarMenu = false">
        
        <button class="btn-campana">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            
            <span v-if="sinLeer > 0" class="contador">{{ sinLeer }}</span>
        </button>

        <div v-if="mostrarMenu" class="menu-desplegable">
            
            <div class="titulo-menu">Últimas notificaciones</div>

            <ul class="lista-noti">
                <li v-for="notificacion in notificaciones.slice(0, 5)" :key="notificacion.id" class="item-noti">
                    <p class="texto">{{ notificacion.data.mensaje }}</p>
                    <span class="fecha">{{ new Date(notificacion.created_at).toLocaleDateString() }}</span>
                </li>
                
                <li v-if="notificaciones.length === 0" class="sin-datos">
                    No tienes notificaciones
                </li>
            </ul>

            <router-link to="/mis-notificaciones" class="btn-ver-todas">
                Ver todas las notificaciones
            </router-link>
        </div>

    </div>
</template>

<style scoped>
.contenedor-campana {
    position: relative;
    display: inline-block;
}

.btn-campana {
    background: none;
    border: none;
    cursor: pointer;
    color: white;
    position: relative;
    padding: 10px;
}

.contador {
    position: absolute;
    top: 5px;
    right: 5px;
    background-color: red;
    color: white;
    font-size: 10px;
    border-radius: 50%;
    padding: 2px 5px;
    font-weight: bold;
}

.menu-desplegable {
    position: absolute;
    top: 100%;
    right: 0;
    width: 300px;
    background-color: white;
    border: 1px solid #ccc;
    box-shadow: 0px 5px 10px rgba(0,0,0,0.2);
    border-radius: 8px;
    z-index: 1000;
    overflow: hidden;
}

.titulo-menu {
    background-color: #f5f5f5;
    padding: 10px;
    font-weight: bold;
    font-size: 14px;
    color: #333;
    border-bottom: 1px solid #eee;
}

.lista-noti {
    list-style: none;
    margin: 0;
    padding: 0;
}

.item-noti {
    padding: 10px;
    border-bottom: 1px solid #f0f0f0;
    cursor: default;
}

.item-noti:hover {
    background-color: #f9f9f9;
}

.texto {
    margin: 0;
    font-size: 13px;
    color: #333;
}

.fecha {
    font-size: 10px;
    color: #888;
}

.sin-datos {
    padding: 20px;
    text-align: center;
    color: #888;
    font-size: 12px;
}

.btn-ver-todas {
    display: block;
    text-align: center;
    background-color: #143b27;
    color: white;
    text-decoration: none;
    padding: 10px;
    font-size: 13px;
    font-weight: bold;
}

.btn-ver-todas:hover {
    background-color: #0f2e1e;
}
</style>