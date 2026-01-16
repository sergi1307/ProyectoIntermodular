<template>
    <div class="pagina-general">
      <h1>Todas nuestras Tiendas</h1>
      <p>Consulta dónde estamos.</p>
      
      <mapa-tiendas :puntos="listaTiendas"></mapa-tiendas>
    </div>
  </template>
  
  <script>
  import MapaTiendas from '../components/mapaPuntosdeventa.vue';
  import axios from 'axios';
  export default {
    name: 'MapaGeneral',
    components: { MapaTiendas },
    data() {
      return {

        listaTiendas: []
      }
    },
    async mounted(){
      await this.cargarTiendas();
    },
    methods: {
      async cargarTiendas(){
        try {
          const response = await axios.get('http://localhost:8080/api/tiendas');
          this.listaTiendas = response.data;
        } catch (error) {
          console.error('Error al cargar tiendas:', error);
        }
      }
    }
  }
  </script>
  
  <style scoped>
  .pagina-general { 
    padding: 40px 20px; 
    text-align: center; /* Alineamos textos al centro */
    background-color: #f9f9f9; /* Un gris muy clarito de fondo queda bien */
  }

  h1 { margin-bottom: 10px; color: #333; }
  p { margin-bottom: 30px; color: #666; }
  </style>