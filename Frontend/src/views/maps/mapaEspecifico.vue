<template>
    <div class="contenedor-especifico">
      <h2>Mis Puntos de Venta</h2>
      <p>Gestiona tus ubicaciones activas.</p>
  
      <div class="area-mapa">
        <mapa-tiendas 
            v-if="cargado && misTiendas.length > 0"
            titulo="Mis Tiendas"
            :puntos="misTiendas"
            map-id="mapaEspecifico"
            es-seleccionable 
            @tienda-elegida="seleccionarTienda"
        ></mapa-tiendas>
        
        <div v-else-if="!cargado" class="cargando">Cargando mapa...</div>
      </div>
  
      <div class="caja-debug" v-if="idCapturado">
          <p><strong>Tienda seleccionada:</strong></p>
          <p>ID: <strong>{{ idCapturado }}</strong></p>
      </div>
  
      <div v-if="cargado && misTiendas.length === 0" class="aviso-vacio">
          <p>No tienes tiendas creadas todavía.</p>
      </div>
      
      <div style="background: yellow; padding: 10px; margin: 10px;">
          <p>Cargado: {{ cargado }} | Tiendas: {{ misTiendas.length }}</p>
          <p>{{ debugMsg }}</p>
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
        cargado: false,
        debugMsg: ''
      }
    },
    mounted() {
        this.cargarMisTiendas();
    },
    methods: {
        async cargarMisTiendas() {
          try {
              const token = localStorage.getItem('token');
              this.debugMsg = 'Token: ' + (token ? 'existe' : 'NO existe');
              
              if (!token) {
                   this.$router.push('/login');
                   return;
              }

              const response = await axios.get('http://localhost:8080/api/map', {
                  headers: {
                      'Authorization': 'Bearer ' + token
                  }
              });
              
              this.debugMsg = 'Respuesta: ' + JSON.stringify(response.data);

              this.misTiendas = response.data.map(punto => ({
                  id: punto.id_delivery_point,
                  name: punto.name,
                  direction: punto.direction,
                  latitude: parseFloat(punto.latitude),
                  length: parseFloat(punto.length)
              }));
              
              this.cargado = true;

          } catch (error) {
              this.cargado = true;
              this.debugMsg = 'Error: ' + (error.response ? error.response.status + ' - ' + JSON.stringify(error.response.data) : error.message);
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