<script setup>
import axios from 'axios';
import { onMounted, ref, computed } from 'vue';

const logs = ref([]);
const filtroUsuario = ref('');
const filtroFecha = ref('');

const obtenerItem = async () => {
    try {
        const token = localStorage.getItem('token');
        const respuesta = await axios.get('http://localhost:8080/api/logs', {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        console.log('estos son los logs que hay', respuesta.data);
        logs.value = respuesta.data;   
    } catch (error) {
        console.error("Error al obtener los datos:", error);
    }
}

const logsFiltrados = computed(() => {
    return logs.value.filter(log => {
        const coincideUsuario = log.usuario.name.toLowerCase().includes(filtroUsuario.value.toLowerCase());
        const fechaLog = log.create_at ? log.create_at.split(' ')[0] :'no hay logs con esa fecha';
        const coincideFecha = filtroFecha.value ? fechaLog === filtroFecha.value : true;
        return coincideUsuario && coincideFecha;
    });
});

onMounted(() => {
    obtenerItem();
})
</script>

<template>
    <div>
        <div>
            <input type="text" v-model="filtroUsuario" placeholder="Buscar usuario" />

            <input type="date" v-model="filtroFecha"/>
        </div>

        <div v-if="logs">
            <ul v-for="log in logsFiltrados">
                <li>nombre: {{ log.usuario?.name }}</li>
                <li>accion: {{ log.action }}</li>
                <li>tabla afectada: {{ log.table_name }}</li>
                <li>dato: {{ log.data }}</li>
                <li>ip: {{ log.ip_address }}</li>
                <li>fecha: {{ log.create_at }}</li>
            </ul>
        </div>
    </div>
</template>