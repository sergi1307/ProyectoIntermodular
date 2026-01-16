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
    //funcion para obtener cookies
    obtenerCookie(nombre) {
        const valor = `; ${document.cookie}`;
        const partes = valor.split(`; ${nombre}=`);
        if (partes.length === 2) return partes.pop().split(';').shift();
        return null;
    },
    //Funcion para cargar las tiendas
    async cargarMisTiendas() {
        try {
            const token = this.obtenerCookie('auth_token'); 

            if (!token) {
                console.warn("No se encontró la cookie del token");
                alert("No estás logueado.");
                this.$router.push('/login');
                return;
            }

            console.log("Token recuperado de cookie:", token);

            const response = await axios.get('http://localhost:8080/api/users/map', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
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
            console.error(" Error cargando mis tiendas:", error);
            if (error.response && error.response.status === 401) {
                alert("Sesión caducada.");
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