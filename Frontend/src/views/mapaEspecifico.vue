<template>
  <div class="contenedor-especifico">
    <h2>📍 Mis Puntos de Venta 📍</h2>
    <p>Gestiona tus ubicaciones activas.</p>

    <div class="area-mapa">
      <mapa-tiendas 
          titulo="Mis Tiendas"
          :puntos="misTiendas"
          es-seleccionable 
          @tienda-elegida="seleccionarTienda"
      ></mapa-tiendas>
    </div>

    <div class="caja-debug" v-if="idCapturado">
        <p><strong>Tienda seleccionada:</strong></p>
        <p>ID: <strong>{{ idCapturado }}</strong></p>
    </div>

    <div v-if="cargado && misTiendas.length === 0" class="aviso-vacio">
        <p>No tienes tiendas creadas todavía.</p>
    </div>
  </div>
</template>

<script>
import MapaTiendas from '../components/mapaPuntosdeventa.vue';
import axios from 'axios';

/**
 * Vista que muestra las tiendas del usuario autenticado
 */
export default {
  name: 'MapaEspecifico',
  components: { MapaTiendas },
  data() {
    return {
      idCapturado: null,
      misTiendas: [],
      cargado: false
    }
  },
  async mounted() {
      await this.cargarMisTiendas();
  },
  methods: {
    /**
     * Carga las tiendas del usuario desde la API
     */
    async cargarMisTiendas() {
        try {
            console.log("Conectando con Laravel (Modo Sanctum Automático)...");
            // 2. Activamos 'withCredentials: true' para que el navegador la envíe sola.
            const response = await axios.get('http://localhost:8080/api/users/map', {
                withCredentials: true 
            });

            console.log("Mis tiendas recibidas:", response.data);

            this.misTiendas = response.data.map(punto => {
                return {
                    id: punto.id_delivery_point,
                    name: punto.name,
                    direction: punto.direction,
                    latitude: parseFloat(punto.latitude), 
                    length: parseFloat(punto.length)      
                };
            });
            
            this.cargado = true;

        } catch (error) {
            console.error("Error cargando mis tiendas:", error);
            if (error.response && error.response.status === 401) {
                alert("Sesión caducada o no iniciada.");
                this.$router.push('/login');
            }
        }
    },
    /**
     * Maneja la selección de una tienda en el mapa
     * @param {number} id - ID de la tienda seleccionada
     */
    seleccionarTienda(id) {
        this.idCapturado = id;
    }
  }
}
</script>

<style scoped>
.contenedor-especifico { 
    padding: 20px; 
    max-width: 900px; 
    margin: 0 auto; 
    text-align: center;
}
.area-mapa { padding: 20px 0; margin-top: 20px; }
.caja-debug { 
    margin-top: 20px; padding: 10px; 
    background-color: #d4edda; border-radius: 5px; 
    display: inline-block;
}
.aviso-vacio {
    margin-top: 20px; color: #856404; background-color: #fff3cd; 
    padding: 15px; border-radius: 5px;
}
</style>