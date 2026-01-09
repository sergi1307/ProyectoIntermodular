<template>
    <div class="contenedor-mapa">
      <h3>{{ titulo }}</h3>
      
      <div id="mapa-leaflet"></div>
  
      <p v-if="puntos.length === 0" class="mensaje-error">
          No hay tiendas para mostrar.
      </p>
    </div>
  </template>
  
  <script>
  import L from 'leaflet';
  import 'leaflet/dist/leaflet.css';
  
  // --- ARREGLO PARA LOS ICONOS ---
  // Tuve un problema: al usar Vite los marcadores salían invisibles o rotos.
  // Buscando en foros encontré que hay que reasignar las imágenes a mano así:
  import icon from 'leaflet/dist/images/marker-icon.png';
  import iconShadow from 'leaflet/dist/images/marker-shadow.png';
  
  let DefaultIcon = L.icon({
      iconUrl: icon,
      shadowUrl: iconShadow,
      iconSize: [25, 41],
      iconAnchor: [12, 41]
  });
  L.Marker.prototype.options.icon = DefaultIcon;
  // -------------------------------
  
  export default {
      name: 'MapaTiendas',
      props: {
          // Estos son los datos que me pasa la vista de Blade (Laravel)
          puntos: {
              type: Array,
              required: true
          },
          titulo: {
              type: String,
              default: 'Mapa de Tiendas'
          },
          // Esta variable es el "interruptor". 
          // False = Mapa normal (clientes). True = Mapa para seleccionar (vendedor).
          esSeleccionable: {
              type: Boolean,
              default: false
          }
      },
      mounted() {
          // Espero a que el componente esté montado para cargar el mapa, si no da error
          this.cargarMapa();
      },
      methods: {
          cargarMapa() {
              // 1. Centro el mapa en España (aprox) con un zoom de 6
              let mapa = L.map('mapa-leaflet').setView([40.4167, -3.70325], 6);
  
              // 2. Cargo la capa visual (las baldosas) de OpenStreetMap
              L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                  attribution: '&copy; OpenStreetMap'
              }).addTo(mapa);
  
              // 3. Recorro todas las tiendas que me han llegado
              this.puntos.forEach(punto => {
                  
                  // IMPORTANTE: En mi base de datos la columna longitud se llama 'length'.
                  // Compruebo que las coordenadas existan para que no pete el mapa.
                  if (punto.latitude && punto.length) {
                      
                      let marcador = L.marker([punto.latitude, punto.length]).addTo(mapa);
  
                      // --- AQUÍ DECIDO EL COMPORTAMIENTO ---
                      if (this.esSeleccionable) {
                          // CASO VENDEDOR: Quiero que pueda seleccionar la tienda
                          marcador.bindPopup(`<b>${punto.name}</b><br>Haz click para seleccionar`);
                          
                          // Añado un evento click al marcador
                          marcador.on('click', () => {
                              // "Emito" el evento hacia arriba para que Blade sepa qué ID se ha elegido
                              this.$emit('tienda-elegida', punto.id); 
                              marcador.openPopup();
                          });
  
                      } else {
                          // CASO CLIENTE: Solo muestro información, sin clicks raros
                          marcador.bindPopup(`<b>${punto.name}</b><br>${punto.direction}`);
                      }
                  }
              });
          }
      }
  }
  </script>
  
  <style scoped>
  .contenedor-mapa {
      margin-bottom: 20px;
  }
  
  #mapa-leaflet {
      height: 400px;
      width: 100%;
      border: 1px solid #ccc;
      border-radius: 5px;
      /* Le pongo z-index 1 porque si no el mapa se comía los menús desplegables */
      z-index: 1; 
  }
  
  .mensaje-error {
      color: red;
      font-weight: bold;
      margin-top: 10px;
  }
  </style>