<template>
    <div class="contenedor-mapa">
      <h3>{{ titulo }}</h3>
      
      <div id="mapa-leaflet"></div>
  
      <p v-if="puntos.length === 0" class="error">
          Esperando datos de ubicación...
      </p>
    </div>
  </template>
  
  <script>
  import L from 'leaflet';
  import 'leaflet/dist/leaflet.css';
  
  // Configuración de iconos (Esto estaba bien, lo dejamos igual)
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
          puntos: { type: Array, required: true },
          titulo: { type: String, default: 'Mapa' },
          esSeleccionable: { type: Boolean, default: false }
      },
      data() {
          return {
              map: null,      // Guardamos la instancia del mapa aquí para poder controlarla luego
              layerGroup: null // Grupo de capas para poder borrar marcadores antiguos si actualizamos
          }
      },
      mounted() {
          this.iniciarMapa();
      },
      // IMPORTANTE: Observamos 'puntos'. Si la vista padre actualiza la lista, 
      // el mapa se repinta automáticamente.
      watch: {
          puntos: {
              handler(newVal) {
                  if (this.map && newVal.length > 0) {
                      this.dibujarMarcadores();
                  }
              },
              deep: true
          }
      },
      beforeUnmount() {
          // Limpieza: Si me voy de la página, mato el mapa para que no de errores al volver
          if (this.map) {
              this.map.remove();
          }
      },
      methods: {
          iniciarMapa() {
              // 1. Iniciamos el mapa (centrado neutro, luego se ajustará)
              this.map = L.map('mapa-leaflet').setView([40.4167, -3.70325], 5);
              
              L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                  attribution: '&copy; OpenStreetMap'
              }).addTo(this.map);
  
              // Creamos una capa vacía donde meteremos los pines
              this.layerGroup = L.layerGroup().addTo(this.map);
  
              // Si ya hay puntos al cargar, los dibujamos
              if (this.puntos.length > 0) {
                  this.dibujarMarcadores();
              }
          },
          dibujarMarcadores() {
              // Limpiamos marcadores antiguos para no duplicar
              this.layerGroup.clearLayers();
              
              // Array para calcular el auto-zoom
              const limites = [];
  
              this.puntos.forEach(punto => {
                  // OJO: Asegúrate de si tu BD usa 'length' o 'longitude'. 
                  // Aquí compruebo las dos por si acaso.
                  const lat = punto.latitude;
                  const lng = punto.longitude || punto.length; 
  
                  if (lat && lng) {
                      // Creamos el marcador
                      const marcador = L.marker([lat, lng]);
                      
                      // Lógica del Popup (Tu lógica original estaba bien)
                      if (this.esSeleccionable) {
                          marcador.bindPopup(`<b>${punto.name}</b><br>Click para seleccionar`);
                          marcador.on('click', () => {
                              this.$emit('tienda-elegida', punto.id);
                              marcador.openPopup();
                          });
                      } else {
                          marcador.bindPopup(`<b>${punto.name}</b><br>${punto.direction}`);
                      }
  
                      // Añadimos al grupo de capas
                      this.layerGroup.addLayer(marcador);
                      
                      // Guardamos coordenadas para el auto-zoom
                      limites.push([lat, lng]);
                  }
              });
  
              // MAGIA: Si hay puntos, ajustamos el mapa para que se vean todos
              if (limites.length > 0) {
                  this.map.fitBounds(limites, { padding: [50, 50] });
              }
          }
      }
  }
  </script>
  
  <style scoped>
  /* Asegura que el mapa tiene altura o no se verá */
  #mapa-leaflet { 
      height: 400px; 
      width: 100%; 
      border: 1px solid #ccc; 
      border-radius: 5px; 
      z-index: 1; 
  }
  .error { color: grey; font-style: italic; padding: 10px; }
  </style>