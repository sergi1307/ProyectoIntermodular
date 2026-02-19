<template>
  <div class="contenedor-especifico">
    <h2>Mis Puntos de Venta</h2>
    <p>Gestiona tus ubicaciones activas. Haz click en el mapa para añadir un nuevo punto.</p>

    <div v-if="mostrarFormulario" class="formulario">
      <h3>Nueva Tienda</h3>
      
      <input v-model="form.name" placeholder="Nombre" class="campo">
      <input v-model="form.direction" placeholder="Dirección" class="campo">
      <input v-model="form.latitude" type="text" inputmode="decimal" lang="en" placeholder="Latitud (ej: 39.4699)" class="campo">
      <input v-model="form.length" type="text" inputmode="decimal" lang="en" placeholder="Longitud (ej: -0.3763)" class="campo">
      
      <button @click="guardar" class="boton-guardar">Guardar</button>
      <button @click="cancelar" class="boton-cancelar">Cancelar</button>
    </div>

    <div class="area-mapa">
      <mapa-tiendas 
          v-if="cargado"
          titulo="Mis Tiendas - Haz click en el mapa para añadir un punto"
          :puntos="misTiendas"
          :es-editable="true"
          map-id="mapaEspecifico"
          @mapa-clickeado="clickEnMapa"
      ></mapa-tiendas>
      
      <div v-else class="cargando">Cargando mapa...</div>
    </div>

    <div v-if="cargado && misTiendas.length > 0" class="lista">
      <h3>Mis Tiendas</h3>
      <div v-for="tienda in misTiendas" :key="tienda.id" class="item">
        <div>
          <p>
            <strong>{{ tienda.name }}</strong> - <span>{{ tienda.direction }}</span>
          </p>  
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
import { useNotificaciones } from '@/utilidades/useNotificaciones';

import url from '../../config/api';

export default {
  name: 'MapaEspecifico',
  components: { MapaTiendas },
  setup() {
    const notificacion = useNotificaciones();
    return { notificacion };
  },
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

            const response = await axios.get(`${url}/api/map`, {
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

    clickEnMapa(coordenadas) {
        this.modoEdicion = false;
        this.form = {
            id: null,
            name: '',
            direction: '',
            latitude: coordenadas.latitude.toFixed(6),
            length: coordenadas.length.toFixed(6)
        };
        this.mostrarFormulario = true;
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
          await axios.put(`${url}/api/delivery_points/update/${this.form.id}`, 
            this.form, 
            { headers: { 'Authorization': 'Bearer ' + token } }
          );
          this.notificacion.exito('Tienda actualizada');
        } else {
          const user = JSON.parse(localStorage.getItem('user'));
          const userId = user.id_user;
          await axios.post(`${url}/api/delivery_points/store`, 
            {
              id_user: userId,
              name: this.form.name,
              direction: this.form.direction,
              latitude: parseFloat(this.form.latitude),
              length: parseFloat(this.form.length)
            }, 
            { headers: { 'Authorization': 'Bearer ' + token } }
          );
          this.notificacion.exito('Tienda creada');
        }
        
        this.cargarMisTiendas();
        this.cancelar();
      } catch (error) {
        let mensaje = error.message;
        if (error.response && error.response.data && error.response.data.message) {
          mensaje = error.response.data.message;
        }
        this.notificacion.error('Error: ' + mensaje);
      }
    },

    eliminar(id) {
      this.notificacion.confirmar('¿Seguro que quieres eliminar esta tienda?', async () => {
        try {
          const token = localStorage.getItem('token');
          
          await axios.delete(`${url}/api/delivery_points/destroy/${id}`, {
            headers: { 'Authorization': 'Bearer ' + token }
          });
          
          this.notificacion.exito('Tienda eliminada');
          this.cargarMisTiendas();
        } catch (error) {
          let mensaje = error.message;
          if (error.response && error.response.data && error.response.data.message) {
            mensaje = error.response.data.message;
          }
          this.notificacion.error('Error: ' + mensaje);
        }
      });
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
    box-sizing: border-box;
}

h2 {
    color: #1c5537;
    margin-bottom: 10px;
}

.formulario {
  background: #f9fafb;
  padding: 20px;
  margin-bottom: 20px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  border: 1px solid #e5e7eb;
}

.campo {
  width: 100%;
  padding: 12px;
  margin-bottom: 10px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  box-sizing: border-box;
  font-size: 1rem;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.boton-guardar {
  background: #1c5537;
  color: white;
  padding: 10px 20px;
  border: none;
  cursor: pointer;
  border-radius: 6px;
  font-weight: 600;
}

.boton-cancelar {
  background: #6b7280;
  color: white;
  padding: 10px 20px;
  border: none;
  cursor: pointer;
  border-radius: 6px;
  font-weight: 600;
}

.area-mapa { 
  padding: 0; 
  margin-top: 20px; 
  min-height: 400px; 
  border-radius: 12px;
  overflow: hidden;
}

.lista {
  margin-top: 30px;
  text-align: left;
}

.item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  margin-bottom: 10px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.item-info p {
    margin: 0;
    line-height: 1.5;
}

.boton-editar {
  background: #f59e0b;
  color: white;
  padding: 8px 16px;
  border: none;
  cursor: pointer;
  border-radius: 6px;
  margin-right: 5px;
  font-weight: 600;
}

.boton-eliminar {
  background: #ef4444;
  color: white;
  padding: 8px 16px;
  border: none;
  cursor: pointer;
  border-radius: 6px;
  font-weight: 600;
}

.aviso-vacio {
    margin-top: 20px; 
    color: #856404; 
    background-color: #fff3cd; 
    padding: 15px;
    border-radius: 8px;
}

.cargando { 
  color: #666; 
  font-style: italic; 
}

@media (max-width: 768px) {
    .contenedor-especifico {
        padding: 10px;
    }

    .formulario {
        padding: 15px;
    }

    .form-actions {
        flex-direction: column;
        gap: 10px;
    }

    .boton-guardar, .boton-cancelar {
        width: 100%;
        padding: 12px;
    }

    .item {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }

    .item-actions {
        display: flex;
        width: 100%;
        gap: 10px;
    }

    .boton-editar, .boton-eliminar {
        flex: 1;
        margin-right: 0;
        padding: 10px;
        text-align: center;
    }
}
</style>