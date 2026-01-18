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
  import icon from 'leaflet/dist/images/marker-icon.png';
  import iconShadow from 'leaflet/dist/images/marker-shadow.png';
  
  let DefaultIcon = L.icon({
      iconUrl: icon, shadowUrl: iconShadow,
      iconSize: [25, 41], iconAnchor: [12, 41]
  });
  L.Marker.prototype.options.icon = DefaultIcon;
  
  /**
   * Componente para mostrar el mapa interactivo con marcadores de puntos de venta
   */
  export default {
      name: 'MapaTiendas',
      props: {
          puntos: { type: Array, required: true },
          titulo: { type: String, default: 'Mapa' },
          esSeleccionable: { type: Boolean, default: false }
      },
      data() {
          return {
              map: null,       
              layerGroup: null 
          }
      },
      mounted() {
          this.iniciarMapa();
      },
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
          if (this.map) {
              this.map.remove();
          }
      },
      methods: {
        /**
         * Inicializa el mapa de Leaflet con límites restringidos a España
         */
        iniciarMapa() {
            var surOeste = L.latLng(33.0, -12.0); 
            var norteEste = L.latLng(45.0, 6.0);
            var limitesEspaña = L.latLngBounds(surOeste, norteEste);

            this.map = L.map('mapa-leaflet', {
                center: [40.4167, -3.70325],
                zoom: 6,
                minZoom: 5,
                maxZoom: 9,
                maxBounds: limitesEspaña,
        });
        
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(this.map);

            this.layerGroup = L.layerGroup().addTo(this.map);

            if (this.puntos.length > 0) {
                this.dibujarMarcadores();
            }
        },
          /**
           * Dibuja todos los marcadores en el mapa y ajusta el zoom
           */
          dibujarMarcadores() {
              this.layerGroup.clearLayers();
              const limites = [];
              this.puntos.forEach(punto => {
                  const lat = punto.latitude;
                  const lng = punto.length; 

                  if (lat && lng) {
                      const marcador = L.marker([lat, lng]);
                      
                      if (this.esSeleccionable) {
                          marcador.bindPopup(`<b>${punto.name}</b><br>Click para seleccionar`);
                          marcador.on('click', () => {
                              this.$emit('tienda-elegida', punto.id);
                              marcador.openPopup();
                          });
                      } else {
                          marcador.bindPopup(`<b>${punto.name}</b><br>${punto.direction}`);
                      }
  
                      this.layerGroup.addLayer(marcador);
                      
                      limites.push([lat, lng]);
                  }
              });
              if (limites.length > 0) {
                  this.map.fitBounds(limites, { padding: [50, 50], maxZoom: 8 });
              }
          }
      }
  }
  </script>
  
  <style scoped>
    
    .tarjeta-mapa {
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;  
    background: white;
    padding: 15px;    
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15); 
    text-align: center;
    }
    #mapa-leaflet { 
        width: 100%; 
        height: 800px;
        border-radius: 15px; 
        border: 2px solid #eee;
        z-index: 1; 
    }
    
    .error { color: grey; font-style: italic; padding: 10px; }
    </style>