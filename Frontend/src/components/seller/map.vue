<script>
import mapaEspecifico from '../../views/maps/mapaEspecifico.vue';
import axios from 'axios';

export default {
  name: 'SellerMapWrapper',
  components: { 
    mapaEspecifico
  },
  data() {
    return {
      misTiendas: [],
      cargado: false
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
    }
  }
}
</script>

<template>

    <div class="area-mapa">
      <mapa-especifico 
        v-if="cargado" 
        :puntos="misTiendas" 
      />
      
      <div v-else class="cargando">Cargando mapa...</div>
    </div>

    <div v-if="cargado && misTiendas.length === 0" class="aviso-vacio">
        <p>No tienes tiendas creadas todavía.</p>
    </div>
</template>

<style scoped>
.contenedor-especifico { 
    padding: 20px; 
    max-width: 900px; 
    margin: 0 auto; 
    text-align: center;
}
.area-mapa { padding: 20px 0; margin-top: 20px; min-height: 300px; }
.aviso-vacio {
    margin-top: 20px; color: #856404; background-color: #fff3cd; 
    padding: 15px; border-radius: 5px;
}
.cargando { color: #666; font-style: italic; }
</style>