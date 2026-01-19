<template>
    <div class="contenedor-especifico">
      <h2>Mis Puntos de Venta</h2>
      <p>Gestiona tus ubicaciones activas.</p>
  
      <div class="area-mapa">
        <mapa-tiendas 
            v-if="cargado"
            titulo="Mis Tiendas"
            :puntos="misTiendas"
            es-seleccionable 
            @tienda-elegida="seleccionarTienda"
        ></mapa-tiendas>
        
        <div v-else class="cargando">Cargando mapa...</div>
      </div>
  
      <div class="caja-debug" v-if="idCapturado">
          <p><strong>Tienda seleccionada:</strong></p>
          <p>ID: <strong>{{ idCapturado }}</strong></p>
      </div>
  
      <div v-if="cargado && misTiendas.length === 0" class="aviso-vacio">
          <p>No tienes tiendas creadas todavía.</p>
      </div>
      
      <div class="debug-info" style="margin-top: 20px; padding: 10px; background: #f0f0f0; border-radius: 5px;">
          <p><strong>Debug Info:</strong></p>
          <p>Cargado: {{ cargado }}</p>
          <p>Número de tiendas: {{ misTiendas.length }}</p>
          <p>Tiendas: {{ misTiendas }}</p>
      </div>
    </div>
  </template>
  
  <script>
  import MapaTiendas from '../../components/maps/mapaPuntosdeventa.vue';
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
        async cargarMisTiendas() {
          try {
              const token = localStorage.getItem('token');
              console.log("Token:", token);
              if (!token) {
                   alert("No hay token guardado. Tienes que hacer login.");
                   this.$router.push('/login');
                   return;
              }
              console.log("Haciendo petición a /api/map...");
              const response = await axios.get('http://localhost:8080/api/map', {
                  headers: {
                      'Authorization': `Bearer ${token}`
                  }
              });

              console.log("Respuesta del servidor:", response.data);
              console.log("Número de tiendas:", response.data.length);

              this.misTiendas = response.data.map(punto => ({
                  id: punto.id_delivery_point,
                  name: punto.name,
                  direction: punto.direction,
                  latitude: parseFloat(punto.latitude),
                  length: parseFloat(punto.length)
              }));
              
              console.log("Tiendas mapeadas:", this.misTiendas);
              
              this.cargado = true;

          } catch (error) {
              console.error("Error completo:", error);
              console.error("Error response:", error.response);
              
              this.cargado = true;
              
              if (error.response && error.response.status === 401) {
                  alert("El token ha caducado. Entra de nuevo.");
                  this.$router.push('/login');
              } else {
                  alert("Error al cargar tus tiendas.");
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
  .area-mapa { padding: 20px 0; margin-top: 20px; min-height: 400px; }
  .caja-debug { 
      margin-top: 20px; padding: 10px; 
      background-color: #d4edda; border-radius: 5px; 
      display: inline-block;
  }
  .aviso-vacio {
      margin-top: 20px; color: #856404; background-color: #fff3cd; 
      padding: 15px; border-radius: 5px;
  }
  .cargando { color: #666; font-style: italic; }
  </style>