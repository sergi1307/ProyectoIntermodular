<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import url from '../../config/api';
import { useNotificaciones } from '@/composables/useNotificaciones';

const notificacion = useNotificaciones();
const router = useRouter();

// Ací guardem les dades i el que anem filtrant
const logs = ref([]);
const tipusLog = ref('login-logout'); // Per defecte mirem qui entra/ix
const dataInici = ref('');
const dataFi = ref('');
const idUsuari = ref('');
const carregarDades = ref(false);

// Per a fer la cerca en la taula sense anar al servidor
const cerca = ref('');
const filtreAccio = ref('');

// Funció per a pillar els logs de la base de dades
const obtenirLogs = async () => {
    carregarDades.value = true;
    try {
        // Usem els nous noms de les rutes del back
        const endpoint = tipusLog.value === 'login-logout' ? 'login-logout' : 'registre-modificacio';
        
        // Passem dades de cerca cap al servidor
        const params = {
            created_at: dataInici.value,
            end_at: dataFi.value,
            id: idUsuari.value
        };

        const response = await axios.get(`${url}/api/logs/${endpoint}`, {
            params,
            headers: { Authorization: `Bearer ${localStorage.getItem("token")}` }
        });
        
        logs.value = response.data;
    } catch (error) {
        if (error.response?.status === 403) {
            notificacion.error("No tens permisos");
            router.push('/general');
        } else {
            notificacion.error("Error al carregar dades");
        }
    } finally {
        carregarDades.value = false;
    }
};

// Filtre computat per a que la cerca siga instantània (DWEC)
const logsFiltrats = computed(() => {
    return logs.value.filter(log => {
        // Cerca per text dins dels logs
        const compleixCerca = !cerca.value || 
                             log.action.toLowerCase().includes(cerca.value.toLowerCase()) ||
                             (log.data && log.data.toLowerCase().includes(cerca.value.toLowerCase()));
        
        // Filtre per tipus d'acció (amb els nous noms)
        const compleixAccio = !filtreAccio.value || log.action === filtreAccio.value;

        return compleixCerca && compleixAccio;
    });
});

// Per a que les dates no siguen lletges
const formatearFecha = (fecha) => {
    return fecha ? new Date(fecha).toLocaleString('ca-ES') : '-';
};

// Colors de cada acció: ara usem 'register' i 'modificacio'
const claseAccio = (accio) => {
    const classes = {
        'login': 'estado-aceptado',
        'logout': 'estado-rechazado',
        'register': 'estado-aceptado',
        'modificacio': 'estado-encurso'
    };
    return classes[accio] || '';
};

// Quan obrim la vista comprovem que siga l'admin
onMounted(() => {
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    if (user.name !== 'examen') {
        router.push('/general');
    } else {
        obtenirLogs();
    }
});
</script>

<template>
    <div id="contenedor-admin">
        <div id="capçalera_admin">
            <h1>Panell de Control (Logs)</h1>
            <p>Vista d'administració per a l'usuari: <strong>examen</strong></p>
        </div>

        <div id="panel_filtros">
            <!-- Botonera per a canviar entre taules -->
            <div class="tabs">
                <button :class="{ active: tipusLog === 'login-logout' }" @click="tipusLog = 'login-logout'; obtenirLogs()">Login i Logout</button>
                <button :class="{ active: tipusLog === 'registre-modificacio' }" @click="tipusLog = 'registre-modificacio'; obtenirLogs()">Registre i Modificació</button>
            </div>

            <!-- Filtres per a la cerca -->
            <div class="filtros-grid">
                <div class="filtro-item">
                    <label>Inici</label>
                    <input type="date" v-model="dataInici" @change="obtenirLogs">
                </div>
                <div class="filtro-item">
                    <label>Fi</label>
                    <input type="date" v-model="dataFi" @change="obtenirLogs">
                </div>
                <div class="filtro-item" v-if="tipusLog === 'registre-modificacio'">
                    <label>ID Usuari</label>
                    <input type="number" v-model="idUsuari" @input="obtenirLogs" placeholder="Busca ID">
                </div>
                <div class="filtro-item">
                    <label>Busca ràpida</label>
                    <input type="text" v-model="cerca" placeholder="Busca en la llista">
                </div>
                <div class="filtro-item">
                    <label>Acció</label>
                    <select v-model="filtreAccio">
                        <option value="">Totes</option>
                        <template v-if="tipusLog === 'login-logout'">
                            <option value="login">Login</option>
                            <option value="logout">Logout</option>
                        </template>
                        <template v-else>
                            <option value="register">Registre</option>
                            <option value="modificacio">Modificació</option>
                        </template>
                    </select>
                </div>
            </div>
        </div>

        <div id="listaLogs">
            <div v-if="carregarDades" class="loading">Carregant dades del servidor...</div>
            
            <table v-else>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuari</th>
                        <th>Acció</th>
                        <th>Taula</th>
                        <th>Data i Hora</th>
                        <th>Dades Extra</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="log in logsFiltrats" :key="log.id_log">
                        <td>#{{ log.id_log }}</td>
                        <td>ID: {{ log.id_user }}</td>
                        <td>
                            <span :class="['estado', claseAccio(log.action)]">{{ log.action }}</span>
                        </td>
                        <td>{{ log.table_name }}</td>
                        <td>{{ formatearFecha(log.created_at) }}</td>
                        <td class="detalles-json">{{ log.data || '-' }}</td>
                    </tr>
                    <tr v-if="logsFiltrats.length === 0">
                        <td colspan="6" class="no-data">No hi ha res per ací.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style scoped>
#contenedor-admin { padding: 30px; background-color: #f8fafc; min-height: 100vh; font-family: sans-serif; }
#capçalera_admin { margin-bottom: 25px; }
#capçalera_admin h1 { color: #143b27; margin: 0; }
#capçalera_admin p { color: #64748b; margin-top: 5px; }

#panel_filtros { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 25px; }

.tabs { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px; }
.tabs button { padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; background: #f1f5f9; color: #64748b; font-weight: 600; }
.tabs button.active { background: #143b27; color: white; }

.filtros-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; }
.filtro-item { display: flex; flex-direction: column; gap: 5px; }
.filtro-item label { font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; }
.filtro-item input, .filtro-item select { padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; font-size: 14px; }
.filtro-item input:focus { border-color: #143b27; }

#listaLogs table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; }
#listaLogs th { background: #f8fafc; padding: 12px; text-align: left; color: #64748b; font-size: 12px; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; }
#listaLogs td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #334155; }

.estado { padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
.estado-aceptado { background: #dcfce7; color: #15803d; }
.estado-rechazado { background: #fee2e2; color: #b91c1c; }
.estado-encurso { background: #dbeafe; color: #1d4ed8; }

.detalles-json { font-family: monospace; font-size: 11px; color: #64748b; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.no-data, .loading { padding: 40px; text-align: center; color: #94a3b8; font-style: italic; }
</style>
