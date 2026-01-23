<template>
  <div class="contenedor-especifico">
    <h2>Mis Puntos de Venta</h2>
    <p>Gestiona tus ubicaciones activas.</p>

    <button @click="abrirFormulario" class="boton-nuevo">Añadir Punto de Venta</button>

    <div v-if="mostrarFormulario" class="formulario">
      <h3>Nueva Tienda</h3>
      
      <input v-model="form.name" placeholder="Nombre" class="campo">
      <input v-model="form.direction" placeholder="Dirección" class="campo">
      <input v-model.number="form.latitude" type="number" step="any" placeholder="Latitud (ej: 39.4699)" class="campo">
      <input v-model.number="form.length" type="number" step="any" placeholder="Longitud (ej: -0.3763)" class="campo">
      
      <button @click="guardar" class="boton-guardar">Guardar</button>
      <button @click="cancelar" class="boton-cancelar">Cancelar</button>
    </div>

    <div class="area-mapa">
      <mapa-tiendas 
          v-if="cargado && misTiendas.length > 0"
          titulo="Mis Tiendas"
          :puntos="misTiendas"
          map-id="mapaEspecifico"
      ></mapa-tiendas>
      
      <div v-else-if="!cargado" class="cargando">Cargando mapa...</div>
    </div>

    <div v-if="cargado && misTiendas.length > 0" class="lista">
      <h3>Mis Tiendas</h3>
      <div v-for="tienda in misTiendas" :key="tienda.id" class="item">
        <div>
          <strong>{{ tienda.name }}</strong>
          <span>{{ tienda.direction }}</span>
        </div>
        <div>
          <button @click="editar(tienda)" class="boton-editar">Editar</button>
          <button @click="eliminar(tienda.id)" class="boton-eliminar">Eliminar</button>
        </div>
      </div>
    </div>

    <div v-if="cargado && misTiendas.length === 0" class="aviso-vacio">
        <p>No tienes tiendas creadas todavía.</p>
    </div>
  </div>
</template>

<script>
import MapaTiendas from '../../components/maps/mapaPuntosdeventa.vue';
import axios from 'axios';

export default {
  name: 'MapaEspecifico',
  components: { MapaTiendas },
  data() {
    return {
      misTiendas: [],
      cargado: false,
      mostrarFormulario: false,
      modoEdicion: false,
      form: { id: null, name: '', direction: '', latitude: null, length: null }
    }
  },
  mounted() {
      this.cargarMisTiendas();
  },
  methods: {
      async cargarMisTiendas() {
        try {
            const token = localStorage.getItem('token');
            
            if (!token) {
                 this.$router.push('/login');
                 return;
            }

            const response = await axios.get('http://localhost:8080/api/map', {
                headers: { 'Authorization': 'Bearer ' + token }
            });

            this.misTiendas = response.data.map(punto => ({
                id: punto.id_delivery_point,
                name: punto.name,
                direction: punto.direction,
                latitude: parseFloat(punto.latitude),
                length: parseFloat(punto.length)
            }));
            
            this.cargado = true;

        } catch (error) {
            this.cargado = true;
            console.error('Error cargando tiendas:', error);
        }
    },

    abrirFormulario() {
      this.modoEdicion = false;
      this.form = { id: null, name: '', direction: '', latitude: null, length: null };
      this.mostrarFormulario = true;
    },

    editar(tienda) {
      this.modoEdicion = true;
      this.form.id = tienda.id;
      this.form.name = tienda.name;
      this.form.direction = tienda.direction;
      this.form.latitude = tienda.latitude;
      this.form.length = tienda.length;
      this.mostrarFormulario = true;
    },

    async guardar() {
      try {
        const token = localStorage.getItem('token');
        
        if (this.modoEdicion) {
          await axios.put(`http://localhost:8080/api/delivery_points/update/${this.form.id}`, 
            this.form, 
            { headers: { 'Authorization': 'Bearer ' + token } }
          );
          alert('Tienda actualizada');
        } else {
          const user = JSON.parse(localStorage.getItem('user'));
          const userId = user.id_user;
          await axios.post('http://localhost:8080/api/delivery_points/store', 
            {
              id_user: userId,
              name: this.form.name,
              direction: this.form.direction,
              latitude: this.form.latitude,
              length: this.form.length
            }, 
            { headers: { 'Authorization': 'Bearer ' + token } }
          );
          alert('Tienda creada');
        }
        
        this.cargarMisTiendas();
        this.cancelar();
      } catch (error) {
        let mensaje = error.message;
        if (error.response && error.response.data && error.response.data.message) {
          mensaje = error.response.data.message;
        }
        alert('Error: ' + mensaje);
      }
    },

    async eliminar(id) {
      if (!confirm('¿Seguro que quieres eliminar esta tienda?')) return;
      
      try {
        const token = localStorage.getItem('token');
        
        await axios.delete(`http://localhost:8080/api/delivery_points/destroy/${id}`, {
          headers: { 'Authorization': 'Bearer ' + token }
        });
        
        alert('Tienda eliminada');
        this.cargarMisTiendas();
      } catch (error) {
        let mensaje = error.message;
        if (error.response && error.response.data && error.response.data.message) {
          mensaje = error.response.data.message;
        }
        alert('Error: ' + mensaje);
      }
    },

    cancelar() {
      this.mostrarFormulario = false;
    }
  }
}
</script>

<style scoped>
.contenedor-especifico { 
    padding: 20px; 
    max-width: 900px; 
    margin: 0 auto; 
    text-align: center;
}

.boton-nuevo {
  background: green;
  color: white;
  padding: 10px 20px;
  border: none;
  cursor: pointer;
  margin-bottom: 20px;
}

.formulario {
  background: #f5f5f5;
  padding: 20px;
  margin-bottom: 20px;
}

.campo {
  width: 100%;
  padding: 8px;
  margin-bottom: 10px;
  border: 1px solid #ccc;
}

.boton-guardar {
  background: blue;
  color: white;
  padding: 8px 16px;
  border: none;
  cursor: pointer;
  margin-right: 10px;
}

.boton-cancelar {
  background: gray;
  color: white;
  padding: 8px 16px;
  border: none;
  cursor: pointer;
}

.area-mapa { 
  padding: 20px 0; 
  margin-top: 20px; 
  min-height: 400px; 
}

.lista {
  margin-top: 30px;
  text-align: left;
}

.item {
  display: flex;
  justify-content: space-between;
  padding: 15px;
  background: white;
  border: 1px solid #ddd;
  margin-bottom: 10px;
}

.boton-editar {
  background: orange;
  color: white;
  padding: 8px 12px;
  border: none;
  cursor: pointer;
  margin-right: 5px;
}

.boton-eliminar {
  background: red;
  color: white;
  padding: 8px 12px;
  border: none;
  cursor: pointer;
}

.aviso-vacio {
    margin-top: 20px; 
    color: #856404; 
    background-color: #fff3cd; 
    padding: 15px;
}

.cargando { 
  color: #666; 
  font-style: italic; 
}
</style>