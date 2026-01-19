<template>
    <div class="pagina-general">
       <div id="cabecera">
        <div id="titulos">
            <h1>Todas nuestras Tiendas</h1>
            <p>Consulta dónde estamos.</p>
        </div>
        <div id="selector">
            <div id="borde">
                <router-link to="/general"><button :class="{ 'activo': vistaActual === 'grid' }" @click="vistaActual = 'grid'">Productos</button></router-link>
                <router-link to="/mapa"><button :class="{ 'activo': vistaActual === 'map' }" @click="vistaActual = 'map'">Mapa</button></router-link>
            </div>
        </div>
    </div>
      <mapa-tiendas :puntos="listaTiendas"></mapa-tiendas>
    </div>
  </template>
  
  <script>
  import MapaTiendas from '../components/mapaPuntosdeventa.vue';
  
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
/* Contenedor principal */
.pagina-general { 
  padding: 40px 40px; /* Un poco más de margen lateral para que respire */
  background-color: #f9f9f9;
  min-height: 100vh; /* Asegura que ocupe al menos toda la altura */
}

/* Cabecera: Flexbox para alinear texto (izq) y botones (der) */
#cabecera {
  display: flex;
  justify-content: space-between; /* Separa los elementos a los extremos */
  align-items: center; /* Alineación vertical perfecta */
  margin-bottom: 30px; /* Espacio antes del mapa */
}

/* Títulos: Quitamos márgenes extraños para que centre bien */
#titulos {
  text-align: left;
}

#titulos h1 {
  margin: 0; /* Clave para la alineación vertical */
  font-size: 2rem;
  color: #143b27;
  font-weight: 700;
}

#titulos p {
  margin: 5px 0 0 0; /* Pequeño espacio solo arriba */
  color: #666;
  font-size: 1rem;
}

/* Selector (El borde blanco que contiene los botones) */
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
  gap: 5px; /* Pequeña separación entre botones */
}

/* Enlaces del router (a veces rompen el flex, esto lo arregla) */
#borde a {
  text-decoration: none;
  display: block;
}

/* Estilo base de los botones */
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

/* ESTADO ACTIVO (MAPA VERDE) */
/* Esto se aplica cuando el botón tiene la clase .activo */
#borde button.activo {
  background-color: #1c5537; /* Verde corporativo */
  color: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>