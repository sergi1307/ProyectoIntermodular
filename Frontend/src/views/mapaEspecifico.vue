<template>
    <div class="contenedor-especifico">
      <h2>Prueba de Seleccion de Tienda</h2>
      <p>Esta vista sirve para probar el funcionamiento del mapa antes de integrarlo en el formulario principal.</p>
  
      <div class="area-mapa">
        
        <mapa-tiendas 
            titulo="Selecciona tu tienda"
            :puntos="misTiendas"
            :es-seleccionable="true" 
            @tienda-elegida="probarSeleccion"
        ></mapa-tiendas>
  
      </div>
  
      <div class="caja-debug" v-if="idCapturado">
          <p><strong>Prueba exitosa:</strong></p>
          <p>El componente ha enviado el ID: <strong>{{ idCapturado }}</strong></p>
          <p><i>Este es el valor que se tendra que guardar en la base de datos.</i></p>
      </div>
  
    </div>
  </template>
  
  <script>
  import MapaTiendas from '../components/mapaPuntosdeventa.vue';
  
  export default {
    name: 'MapaEspecifico',
    components: { MapaTiendas },
    data() {
      return {
        idCapturado: null,
        
        // Estos datos son estaticos para poder trabajar sin el Backend.
        // Simulan las tiendas que pertenecen al usuario logueado.
        misTiendas: [
          { id: 50, name: 'Mi Tienda Valencia', latitude: 39.4699, length: -0.3763, direction: 'C/ Colon 1' },
          { id: 51, name: 'Mi Almacen', latitude: 39.48, length: -0.38, direction: 'Poligono Norte' }
        ]
      }
    },
    methods: {
      // Funcion que salta cuando el usuario hace click en un marcador
      probarSeleccion(id) {
          this.idCapturado = id;
          console.log("Comprobando ID recibido:", id);
      }
    }
  }
  </script>
  
  <style scoped>
  .contenedor-especifico { 
    padding: 20px; 
    max-width: 900px; 
    margin: 0 auto; 
    text-align: center; /* Centramos todo */
  }

  /* Quitamos el borde dashed feo y dejamos solo espacio */
  .area-mapa { 
    padding: 20px 0; 
    margin-top: 20px; 
  }

.caja-debug { 
    margin-top: 30px; 
    padding: 15px; 
    background-color: #d4edda; 
    border: 1px solid #c3e6cb; 
    color: #155724; 
    border-radius: 10px;
    display: inline-block; /* Para que la caja ocupe solo lo necesario */
    min-width: 300px;
  }
  </style>