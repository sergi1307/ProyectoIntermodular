<template>
  <div class="contenedor-especifico">
    <h2>📍 Mis Puntos de Venta</h2>
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
        <p>Aún no has dado de alta ninguna tienda.</p>
    </div>

  </div>
</template>

<script>
import MapaTiendas from '../components/mapaPuntosdeventa.vue';
import axios from 'axios';

export default {
  name: 'MapaEspecifico',
  components: { MapaTiendas },
  data() {
    return {
      idCapturado: null,
      misTiendas: [], // Empezamos vacío
      cargado: false  // Para saber si ya ha terminado de cargar
    }
  },
  async mounted() {
      await this.cargarMisTiendas();
  },
  methods: {
    async cargarMisTiendas() {
        try {
            // 1. RECUPERAR EL TOKEN
            // Cuando hiciste login, debiste guardar el token. 
            // Si lo llamaste de otra forma, cambia 'token' por el nombre correcto.
            const token = localStorage.getItem('token'); 

            if (!token) {
                alert("No estás logueado. Por favor, inicia sesión.");
                this.$router.push('/login'); // Te devuelve al login si no hay token
                return;
            }

            console.log("📡 Pidiendo MIS datos a Laravel...");

            // 2. PETICIÓN CON CABECERA (HEADER)
            // Aquí enviamos el token para que Laravel sepa quiénes somos
            const response = await axios.get('http://localhost:8080/api/users/map', {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            console.log("✅ Mis tiendas recibidas:", response.data);

            // 3. MAPEO DE DATOS (Igual que en el mapa general)
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
            console.error("❌ Error cargando mis tiendas:", error);
            if (error.response && error.response.status === 401) {
                alert("Tu sesión ha caducado. Vuelve a entrar.");
                this.$router.push('/login');
            }
        }
    },
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
.area-mapa { 
    padding: 20px 0; 
    margin-top: 20px; 
}
.caja-debug { 
    margin-top: 20px; 
    padding: 10px; 
    background-color: #d4edda; 
    border-radius: 5px;
    display: inline-block;
}
.aviso-vacio {
    margin-top: 20px;
    color: #856404;
    background-color: #fff3cd;
    padding: 15px;
    border-radius: 5px;
    border: 1px solid #ffeeba;
}
</style>