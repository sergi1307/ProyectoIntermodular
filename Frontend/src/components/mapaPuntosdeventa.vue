<template>
    <div class="contenedor-mapa tarjeta-mapa">
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
  
  // Configuración de iconos estándar de Leaflet
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
              map: null,       // Instancia del mapa
              layerGroup: null // Grupo para gestionar los marcadores
          }
      },
      mounted() {
          this.iniciarMapa();
      },
      // WATCH: Esto es vital. Si los datos tardan en llegar del servidor,
      // este observador actualizará el mapa automáticamente cuando lleguen.
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
          // Limpiamos el mapa al salir de la página para evitar errores de memoria
          if (this.map) {
              this.map.remove();
          }
      },
      methods: {
          iniciarMapa() {
              // Iniciamos el mapa
              this.map = L.map('mapa-leaflet').setView([40.4167, -3.70325], 5);
              
              L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                  attribution: '&copy; OpenStreetMap'
              }).addTo(this.map);
  
              // Creamos la capa para los marcadores
              this.layerGroup = L.layerGroup().addTo(this.map);
  
              // Si ya hay datos, dibujamos
              if (this.puntos.length > 0) {
                  this.dibujarMarcadores();
              }
          },
          dibujarMarcadores() {
              // 1. Borramos marcadores antiguos
              this.layerGroup.clearLayers();
              
              // Array para guardar coordenadas y hacer el auto-zoom luego
              const limites = [];
  
              this.puntos.forEach(punto => {
                  // USAMOS 'length' COMO TU QUIERES
                  const lat = punto.latitude;
                  const lng = punto.length; 
  
                  // Solo pintamos si las coordenadas existen
                  if (lat && lng) {
                      const marcador = L.marker([lat, lng]);
                      
                      // Configuración del Popup
                      if (this.esSeleccionable) {
                          marcador.bindPopup(`<b>${punto.name}</b><br>Click para seleccionar`);
                          marcador.on('click', () => {
                              this.$emit('tienda-elegida', punto.id);
                              marcador.openPopup();
                          });
                      } else {
                          marcador.bindPopup(`<b>${punto.name}</b><br>${punto.direction}`);
                      }
  
                      // Añadimos el marcador a la capa del mapa
                      this.layerGroup.addLayer(marcador);
                      
                      // Guardamos la coordenada para calcular el zoom
                      limites.push([lat, lng]);
                  }
              });
  
              // AUTO-ZOOM: Ajusta el mapa para que se vean todos los puntos
              if (limites.length > 0) {
                  this.map.fitBounds(limites, { padding: [50, 50] });
              }
          }
      }
  }
  </script>
  
  <style scoped>
    /*CORRECION DE ESTILOS PARA QUE EL MAPA SE VEA BIEN (CUADRADO Y NO ESTIRADO)*/ 
    
    .tarjeta-mapa {
    width: 100%;
    max-width: 750px; /* Limite de ancho para que no se estire */
    margin: 0 auto;   /* Centrado */
    background: white;
    padding: 15px;    
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15); 
    text-align: center;
    }
    #mapa-leaflet { 
        width: 100%; 
        height: 550px;
        border-radius: 15px; 
        border: 2px solid #eee;
        z-index: 1; 
    }
    
    .error { color: grey; font-style: italic; padding: 10px; }
    </style>