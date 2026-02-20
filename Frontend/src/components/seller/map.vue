<script>
import mapaEspecifico from "../../views/maps/mapaEspecifico.vue";
import axios from "axios";

import url from '../../config/api';

export default {
  name: "SellerMapWrapper",
  components: {
    mapaEspecifico,
  },
  data() {
    return {
      misTiendas: [],
      cargado: false,
    };
  },
  mounted() {
    this.cargarMisTiendas();
  },
  methods: {
    async cargarMisTiendas() {
      try {
        const token = localStorage.getItem("token");
        if (!token) {
          this.$router.push("/login");
          return;
        }

        const response = await axios.get(`${url}/api/map`, {
          headers: { Authorization: "Bearer " + token },
        });

        this.misTiendas = response.data.map((punto) => ({
          id: punto.id_delivery_point,
          name: punto.name,
          direction: punto.direction,
          latitude: parseFloat(punto.latitude),
          length: parseFloat(punto.length),
        }));

        this.cargado = true;
      } catch (error) {
        this.cargado = true;
        console.error("Error cargando tiendas:", error);
      }
    },
  },
};
</script>

<template>
  <div class="area-mapa">
    <mapa-especifico v-if="cargado" :puntos="misTiendas" />

    <div v-else class="cargando">Cargando mapa...</div>
  </div>
</template>

<style scoped>
.area-mapa {
  padding: 20px;
  margin: 20px auto;
  min-height: 400px;
  max-width: 1200px;
  width: 100%;
  box-sizing: border-box;
}

.cargando {
  color: #666;
  font-style: italic;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 300px;
  background-color: #f9f9f9;
  border-radius: 12px;
}

.contenedor-especifico {
  padding: 20px;
  max-width: 900px;
  margin: 0 auto;
  text-align: center;
}

@media (max-width: 768px) {
  .area-mapa {
    padding: 10px;
    margin: 10px auto;
    min-height: 300px;
  }

  .contenedor-especifico {
    padding: 10px;
    width: 100%;
  }

  .cargando {
    height: 200px;
    font-size: 0.9rem;
  }
}
</style>