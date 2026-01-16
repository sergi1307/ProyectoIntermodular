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
  async mounted() {
    await this.cargarTiendas();
  },
  methods: {
    async cargarTiendas() {
      try {
        console.log("Pidiendo datos a Laravel...");
        const response = await axios.get('http://localhost:8080/api/delivery_points');
        
        console.log("Datos crudos recibidos:", response.data);
        
        this.listaTiendas = response.data.map(tienda => {
            return {
                id: tienda.id_delivery_point, 
                name: tienda.name,
                direction: tienda.direction,
                latitude: parseFloat(tienda.latitude), // Aseguramos que sea número en float
                length: parseFloat(tienda.length)     //lo mismo en la longitud
            };
        });

      } catch (error) {
        console.error('Error al cargar tiendas:', error);
        alert("Error de conexión. Asegúrate de que el Backend (Docker) está encendido.");
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