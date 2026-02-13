<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const notificaciones = ref([]);
const token = localStorage.getItem('token');

const cargarTodo = async () => {
    try {
        const response = await axios.get('http://localhost:8080/api/notifications', {
            headers: { Authorization: `Bearer ${token}` }
        });
        notificaciones.value = response.data.data;
        console.log(notificaciones.value);
    } catch (error) {
        console.error("Error al cargar");
    }
};

const leerNotificacion = async (noti) => {
    if(noti.read_at) return;

    try {
        await axios.patch(`http://localhost:8080/api/notifications/${noti.id}/read`, {}, {
            headers: { Authorization: `Bearer ${token}` }
        });
        noti.read_at = new Date().toISOString();
    } catch (e) {
        console.log("Error al marcar como leída");
    }
};

onMounted(() => {
    cargarTodo();
});
</script>

<template>
    <div class="contenedor-pagina">
        <h1>Todas mis notificaciones</h1>

        <div class="lista-completa">
            
            <div 
                v-for="noti in notificaciones" 
                :key="noti.id" 
                class="tarjeta" 
                :class="{ 'sin-leer': !noti.read_at }"
                @click="leerNotificacion(noti)"
            >
                <div class="icono-estado">
                    <span v-if="!noti.read_at">●</span>
                    <span v-else>✔</span>
                </div>

                <div class="contenido" v-if="noti.data.tipo == 'pedido'">
                    <h3>{{ noti.data.titulo }}</h3>
                    <small>{{ new Date(noti.created_at).toLocaleString() }}</small>
                </div>

                <div class="contenido" v-else-if="noti.data.tipo == 'venta'">
                    <h3>{{ noti.data.titulo }}</h3>
                    <small>{{ new Date(noti.created_at).toLocaleString() }}</small>
                </div>

                <div v-else>
                    <h3>{{ noti.data.titulo }}</h3>
                    <small>{{ new Date(noti.created_at).toLocaleString() }}</small>
                </div>
            </div>

            <div v-if="notificaciones.length === 0" class="vacio">
                <p>No tienes notificaciones en el historial.</p>
            </div>

        </div>
    </div>
</template>

<style scoped>
.contenedor-pagina {
    padding: 40px;
    max-width: 800px;
    margin: 0 auto;
}

h1 {
    color: #143b27;
    border-bottom: 2px solid #eee;
    padding-bottom: 10px;
    margin-bottom: 30px;
}

.lista-completa {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.tarjeta {
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    cursor: pointer;
    transition: transform 0.2s;
}

.tarjeta:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.tarjeta.sin-leer {
    background-color: #f0fdf4;
    border-left: 5px solid #143b27;
}

.icono-estado {
    font-size: 20px;
    color: #143b27;
    width: 30px;
    text-align: center;
}

.contenido h3 {
    margin: 0 0 5px 0;
    font-size: 16px;
    color: #333;
}

.contenido small {
    color: #888;
}

.vacio {
    text-align: center;
    color: #777;
    padding: 40px;
    border: 2px dashed #ccc;
    border-radius: 10px;
}
</style>