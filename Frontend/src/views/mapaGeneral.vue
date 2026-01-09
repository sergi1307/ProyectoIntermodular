<template>
    <div class="pagina-general">
      <h1>Todas nuestras Tiendas</h1>
      <p>Consulta dónde estamos.</p>
      
      <mapa-tiendas :puntos="listaTiendas"></mapa-tiendas>
    </div>
  </template>
  
  <script>
  import MapaTiendas from '../components/MapaTiendas.vue';
  
  export default {
    name: 'MapaGeneral',
    components: { MapaTiendas },
    data() {
      return {
        // DATOS MOCK: Simulo la respuesta de la API de Laravel
        listaTiendas: [
          { id: 1, name: 'Sede Central', latitude: 40.4167, length: -3.70325, direction: 'Madrid' },
          { id: 2, name: 'Delegación Norte', latitude: 41.3851, length: 2.1734, direction: 'Barcelona' },
          { id: 3, name: 'Delegación Sur', latitude: 37.3891, length: -5.9845, direction: 'Sevilla' }
        ]
      }
    }
  }
  </script>
  
  <style scoped>
  .pagina-general { padding: 20px; }
  </style>