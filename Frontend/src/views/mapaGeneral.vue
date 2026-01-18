<template>
    <div class="pagina-general">

      <div id="nav">
        <div id="logo">
              <img src="../assets/imgs/logoEmpresa.png">
              <p><router-link to="/">ProxiMarket</router-link></p>
        </div> 
        <div id="navegador">
          <p>navegador</p>
        </div> 
        <div id="nav2">
        <p><router-link to="/products">Cambiar a modo comprador</router-link></p>
        <p><router-link to="#">foto</router-link></p>
        <p><router-link to="#">foto</router-link></p>
        <p><router-link to="#">foto</router-link></p>
        </div>
      </div>
      <h1>Todas nuestras Tiendas</h1>
      <p>Consulta dónde estamos.</p>
      
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
  .pagina-general { 
    padding: 40px 20px; 
    text-align: center; /* Alineamos textos al centro */
    background-color: #f9f9f9; /* Un gris muy clarito de fondo queda bien */
  }
  #nav{
    text-align: left;
    text-decoration: none;
    display:flex;
  }
  ul{
  list-style:none;
  display: flex;
  justify-content: space-evenly;
  }
  li{
    color: black;
  }
  a{
  text-decoration: none;
  color:black
}
  h1 { margin-bottom: 10px; color: #333; }
  p { margin-bottom: 30px; color: #666; }
  
  </style>