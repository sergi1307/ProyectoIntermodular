<template>
  <div class="pagina-general">
    <div id="cabecera">
      <div id="titulos">
        <h1>Todas nuestras Tiendas</h1>
        <p>Consulta dónde estamos.</p>
      </div>
      <div id="selector">
        <div id="borde">
          <router-link to="/general">
            <button :class="{ 'activo': vistaActual === 'grid' }">Productos</button>
          </router-link>
          
          <router-link to="/mapa">
            <button :class="{ 'activo': vistaActual === 'map' }">Mapa</button>
          </router-link>
        </div>
      </div>
    </div>
    <mapa-tiendas :puntos="listaTiendas"></mapa-tiendas>
  </div>
</template>

<script>
import MapaTiendas from '../../components/maps/mapaPuntosdeventa.vue';
import axios from 'axios';

export default {
  name: 'MapaGeneral',
  components: { MapaTiendas },
  data() {
    return {
      vistaActual: 'map',
      listaTiendas: []
    }
  },
  async mounted() {
    await this.cargarTiendas();
  },
  methods: {
    async cargarTiendas() {
      try {
        const response = await axios.get('http://localhost:8080/api/delivery_points');
        
        // Mapeamos los datos para asegurar que latitud y longitud son números
        this.listaTiendas = response.data.map(tienda => {
            return {
                id: tienda.id_delivery_point, 
                name: tienda.name,
                direction: tienda.direction,
                latitude: parseFloat(tienda.latitude),
                length: parseFloat(tienda.length)
            };
        });

      } catch (error) {
        console.error('Error al cargar tiendas:', error);
      }
    }
  }
}
</script>

<style scoped>
.pagina-general { 
  padding: 40px 40px;
  background-color: #f9f9f9;
  min-height: 100vh;
}

#cabecera {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

#titulos {
  text-align: left;
}

#titulos h1 {
  margin: 0;
  font-size: 2rem;
  color: #143b27;
  font-weight: 700;
}

#titulos p {
  margin: 5px 0 0 0;
  color: #666;
  font-size: 1rem;
}

#selector {
  display: flex;
  align-items: center;
}

#borde {
  display: flex;
  background-color: white;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 5px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  gap: 5px;
}

#borde a {
  text-decoration: none;
  display: block;
}

#borde button {
  padding: 10px 24px;
  border: none;
  background-color: transparent;
  color: #555;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  font-size: 0.95rem;
  transition: all 0.3s ease;
}

#borde button:hover {
  background-color: #f0f0f0;
}

#borde button.activo {
  background-color: #1c5537;
  color: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>