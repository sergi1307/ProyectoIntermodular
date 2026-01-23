<script setup>
    import axios from 'axios';
    import { onMounted, ref } from 'vue';
    import { useRouter } from 'vue-router';

    const router = useRouter();
    const productos = ref([]);

    const irAlDetalle = (id) => {
        router.push({ name: 'product-details', params: { id: id } });
    };
    
    const obtenerProductos = async () => {
        try {
            const resProductos = await axios.get("http://localhost:8080/api/products/", {
                withCredentials: true
            });

            productos.value = resProductos.data.data; 
            console.log("Productos cargados:", productos.value);
        } catch (error) {
            console.error("Error cargando los productos:", error);
        }
    }

    onMounted(obtenerProductos);
</script>

<template>
  <div id="productos">
    <div 
      v-for="producto in productos" 
      :key="producto.id_product" 
      class="tarjeta-producto"
      @click="irAlDetalle(producto.id_product)"
      style="cursor: pointer;"
    >

     <div id="imagen-producto">
        <img :src="`http://localhost:8080/storage/${producto.image}`" :alt="producto.nombre">
    </div>

      <div id="info-producto">
        <div id="cabecera-info">
          <h3>{{ producto.nombre }}</h3>
          <span id="precio">${{ producto.price }} / {{ producto.type_stock }}</span>
        </div>

        <p id="granja">{{ producto.delivery_point.name }}</p>

        <div id="footer-tarjeta">
          <span id="distancia" @click.stop>
            <a 
              :href="`https://maps.google.com/?q=${producto.delivery_point.latitude},${producto.delivery_point.length}`" 
              target="_blank"
              style="color: blue; text-decoration: underline; cursor: pointer;"
            >
              📍 Ver Mapa
            </a>
          </span>

          <button id="añadir" @click.stop>+</button>
        </div>
      </div>

    </div>
  </div>
</template>

<style scoped>
    *{ text-decoration: none; }
    
    #contenedor-principal {
        display: flex;
        align-items: flex-start;
        padding: 0 2%;
        gap: 2%;
        margin-top: 20px;
    }
    #productos {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 20px;
    }

    .tarjeta-producto {
        background-color: white;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .tarjeta-producto:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px rgba(0,0,0,0.1);
    }

    #imagen-producto {
        height: 180px;
        width: 100%;
        background-color: #eee;
    }

    #imagen-producto img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    #info-producto {
        padding: 15px;
    }

    #cabecera-info {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 5px;
    }

    #cabecera-info h3 {
        margin: 0;
        font-size: 1.1rem;
        color: #1c5537;
    }

    #precio {
        font-weight: bold;
        color: #333;
        font-size: 1rem;
    }

    #granja {
        color: #888;
        font-size: 0.9rem;
        margin-bottom: 15px;
    }

    #footer-tarjeta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 10px;
    }

    #distancia a {
        font-size: 0.85rem;
        color: #666;
    }

    #añadir {
        background-color: #1c5537;
        color: white;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        font-size: 1.2rem;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: background-color 0.2s;
    }

    #añadir:hover {
        background-color: #143b27;
    }

    @media (max-width: 1024px) {
        #productos { grid-template-columns: 1fr 1fr !important; }
    }

    @media (max-width: 768px) {
        #productos { grid-template-columns: 1fr !important; }
    }
</style>