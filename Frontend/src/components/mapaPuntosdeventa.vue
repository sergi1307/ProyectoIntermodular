<template>
    <div class="contenedor-mapa">
      <h3>{{ titulo }}</h3>
      
      <div id="mapa-leaflet"></div>
  
      <p v-if="puntos.length === 0" class="error">
          Cargando tiendas o no hay datos...
      </p>
    </div>
  </template>
  
  <script>
  import L from 'leaflet';
  import 'leaflet/dist/leaflet.css';
  
  // FIX: Arreglo para que se vean los iconos en Vite (copiado de internet)
  import icon from 'leaflet/dist/images/marker-icon.png';
  import iconShadow from 'leaflet/dist/images/marker-shadow.png';
  let DefaultIcon = L.icon({
      iconUrl: icon, shadowUrl: iconShadow,
      iconSize: [25, 41], iconAnchor: [12, 41]
  });
  L.Marker.prototype.options.icon = DefaultIcon;
  
  export default {
      name: 'MapaTiendas',
      props: {
          // Los datos me llegarán desde la Vista (Padre)
          puntos: { type: Array, required: true },
          titulo: { type: String, default: 'Mapa' },
          // Interruptor: false = solo ver, true = seleccionar
          esSeleccionable: { type: Boolean, default: false }
      },
      mounted() {
          // Espero a que se monte el componente para cargar el mapa
          this.cargarMapa();
      },
      methods: {
          cargarMapa() {
              // 1. Centro el mapa (Madrid aprox)
              var mapa = L.map('mapa-leaflet').setView([40.4167, -3.70325], 6);
              
              L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                  attribution: '&copy; OpenStreetMap'
              }).addTo(mapa);
  
              // 2. Pinto los marcadores
              this.puntos.forEach(punto => {
                  // Uso 'length' porque así se llama en mi base de datos
                  if (punto.latitude && punto.length) {
                      
                      var marcador = L.marker([punto.latitude, punto.length]).addTo(mapa);
  
                      // LÓGICA DEL INTERRUPTOR
                      if (this.esSeleccionable) {
                          // Modo Vendedor
                          marcador.bindPopup(`<b>${punto.name}</b><br>Click para seleccionar`);
                          marcador.on('click', () => {
                              // Aviso a la vista padre de que han elegido esta tienda
                              this.$emit('tienda-elegida', punto.id);
                              marcador.openPopup();
                          });
                      } else {
                          // Modo Cliente (Solo info)
                          marcador.bindPopup(`<b>${punto.name}</b><br>${punto.direction}`);
                      }
                  }
              });
          }
      }
  }
  </script>
  
  <style scoped>
  #mapa-leaflet { height: 400px; width: 100%; border: 1px solid #ccc; border-radius: 5px; z-index: 1; }
  .error { color: grey; font-style: italic; }
  </style>