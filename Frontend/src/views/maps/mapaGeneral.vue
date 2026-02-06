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
    <mapa-tiendas 
      :puntos="listaTiendas"
      @punto-seleccionado="mostrarProductos"
    ></mapa-tiendas>

    <!-- Sección de productos (Opción C: debajo del mapa) -->
    <div v-if="puntoSeleccionado" class="seccion-productos">
      <div class="cabecera-productos">
        <h2>Productos disponibles en {{ puntoSeleccionado.name }}</h2>
        <button @click="cerrarProductos" class="btn-cerrar">✕</button>
      </div>
      
      <div v-if="cargandoProductos" class="cargando">
        Cargando productos...
      </div>
      
      <div v-else-if="productos.length === 0" class="sin-productos">
        No hay productos disponibles en este punto de venta.
      </div>
      
      <div v-else class="grid-productos">
        <router-link 
          v-for="producto in productos" 
          :key="producto.id_product"
          :to="`/product-details/${producto.id_product}`"
          class="tarjeta-producto"
        >
          <div class="imagen-container">
            <img :src="obtenerUrlImagen(producto.image)" :alt="producto.name" />
          </div>
          <div class="info-producto">
            <h3>{{ producto.name }}</h3>
            <p class="categoria">{{ producto.category?.name || 'Sin categoría' }}</p>
            <p class="precio">{{ producto.price }}€</p>
            <p class="vendedor">Vendedor: {{ producto.user?.name || 'Desconocido' }}</p>
          </div>
        </router-link>
      </div>
    </div>
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
      listaTiendas: [],
      puntoSeleccionado: null,
      productos: [],
      cargandoProductos: false
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
    },

    async mostrarProductos(punto) {
      this.puntoSeleccionado = punto;
      this.cargandoProductos = true;
      this.productos = [];

      try {
        const response = await axios.get(`http://localhost:8080/api/delivery_points/${punto.id}/products`);
        this.productos = response.data;
      } catch (error) {
        console.error('Error al cargar productos:', error);
        alert('No se pudieron cargar los productos de este punto de venta');
      } finally {
        this.cargandoProductos = false;
      }
    },

    cerrarProductos() {
      this.puntoSeleccionado = null;
      this.productos = [];
    },

    obtenerUrlImagen(rutaImagen) {
      if (!rutaImagen) return '/placeholder.jpg';
      return `http://localhost:8080/storage/${rutaImagen}`;
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

.seccion-productos {
  margin-top: 30px;
  background: white;
  border-radius: 12px;
  padding: 25px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  animation: slideDown 0.3s ease;
}

@keyframes slideDown {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}

.cabecera-productos {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  border-bottom: 2px solid #e0e0e0;
  padding-bottom: 15px;
}

.cabecera-productos h2 { color: #143b27; font-size: 1.5rem; margin: 0; }

.btn-cerrar {
  background: #ff4444;
  color: white;
  border: none;
  border-radius: 50%;
  width: 35px;
  height: 35px;
  font-size: 1.2rem;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-cerrar:hover { background: #cc0000; transform: scale(1.1); }

.cargando, .sin-productos { text-align: center; padding: 40px; color: #666; font-style: italic; }

.grid-productos {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
}

.tarjeta-producto {
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 10px;
  overflow: hidden;
  text-decoration: none;
  color: inherit;
  transition: all 0.3s;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.tarjeta-producto:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
  border-color: #1c5537;
}

.imagen-container { width: 100%; height: 200px; overflow: hidden; background: #f5f5f5; }
.imagen-container img { width: 100%; height: 100%; object-fit: cover; }

.info-producto { padding: 15px; }
.info-producto h3 { margin: 0 0 8px 0; font-size: 1.1rem; color: #143b27; }
.info-producto .categoria { font-size: 0.85rem; color: #888; margin: 0 0 10px 0; }
.info-producto .precio { font-size: 1.3rem; font-weight: bold; color: #1c5537; margin: 10px 0; }
.info-producto .vendedor { font-size: 0.9rem; color: #666; margin: 5px 0 0 0; }
</style>