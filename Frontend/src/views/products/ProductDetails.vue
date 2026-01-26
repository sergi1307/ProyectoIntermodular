<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router'; 
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const producto = ref(null);
const cargando = ref(true);
const error = ref(null);

const volver = () => {
    router.go(-1);
};

const obtenerDetalleProducto = async () => {
    const id = route.params.id; 
    console.log("Buscando producto con ID:", id);

    try {
        const response = await axios.get(`http://localhost:8080/api/products/show/${id}`, {
            withCredentials: true
        });

        producto.value = response.data.data || response.data; 
        console.log("Producto cargado:", producto.value);
        cargando.value = false;

    } catch (err) {
        console.error("Error al cargar:", err);
        error.value = "No se pudo cargar el producto.";
        cargando.value = false;
    }
};

onMounted(() => {
    obtenerDetalleProducto();
});
</script>

<template>
  <div class="detalle-container">
    
    <button @click="volver" class="btn-volver">
        ← Volver al listado
    </button>
    
    <div v-if="cargando" class="loading">Cargando datos del producto...</div>

    <div v-else-if="producto" class="detalle-grid">
        <div class="detalle-imagen">
            <img :src="`http://localhost:8080/storage/${producto.image}`" :alt="producto.nombre">
        </div>

        <div class="detalle-info">
            
            <div class="tags">
                <span v-if="producto.category && producto.category.name" class="tag">
                    {{ producto.category.name }}
                </span>
                <span v-else-if="producto.categoria && producto.categoria.nombre" class="tag">
                    {{ producto.categoria.nombre }}
                </span>
                <span v-else-if="producto.category_name" class="tag">
                    {{ producto.category_name }}
                </span>
            </div>

            <h1>{{ producto.nombre }}</h1>

            <div class="precio-grande">
                {{ producto.price }}€ <span class="unidad">/ {{ producto.type_stock }}</span>
            </div>

            <div class="descripcion-box">
                <h3>Descripción</h3>
                <p>{{ producto.description || 'Sin descripción disponible.' }}</p>
                
                <div class="meta-info">
                    <div>
                        <strong>📍 Origen:</strong><br>
                        {{ producto.delivery_point ? producto.delivery_point.name : 'Ubicación desconocida' }}
                    </div>
                     <div>
                        <strong>📦 Stock:</strong><br>
                        {{ producto.stock }} {{ producto.type_stock }}
                    </div>
                </div>
            </div>

            <div class="acciones-compra">
                <button class="btn-comprar">Comprar Ahora</button>
            </div>
        </div>
    </div>

    <div v-else class="error-msg">
        <h3>Error</h3>
        <p>{{ error }}</p>
    </div>

  </div>
</template>

<style scoped>
.detalle-container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    font-family: 'Segoe UI', sans-serif;
}
.btn-volver {
    background: none;
    border: none;
    font-size: 16px;
    color: #555;
    cursor: pointer;
    margin-bottom: 20px;
}
.btn-volver:hover { text-decoration: underline; }

.loading, .error-msg {
    text-align: center;
    padding: 40px;
    font-size: 1.2em;
    color: #666;
}

.detalle-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.detalle-imagen img {
    width: 100%;
    height: auto;
    border-radius: 15px;
    object-fit: cover;
    max-height: 500px;
}

.detalle-info {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.tags {
    display: flex;
    gap: 10px;
}

.tags .tag {
    background-color: #e8f5e9;
    color: #2e7d32;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85em;
    font-weight: bold;
    text-transform: uppercase;
}

h1 { margin: 10px 0; color: #1a4d2e; font-size: 2.5em; }

.precio-grande { font-size: 2.2em; color: #1a4d2e; font-weight: bold; }
.unidad { font-size: 0.5em; color: #666; font-weight: normal; }

.descripcion-box {
    background-color: #f9f7f2;
    padding: 25px;
    border-radius: 12px;
    color: #555;
    line-height: 1.6;
}

.meta-info {
    display: flex;
    gap: 30px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e0e0e0;
}

.btn-comprar {
    background-color: #1a4d2e;
    color: white;
    border: none;
    padding: 18px 30px;
    font-size: 1.1em;
    border-radius: 8px;
    cursor: pointer;
    width: 100%;
    margin-top: 20px;
    font-weight: bold;
    transition: background-color 0.2s;
}
.btn-comprar:hover { background-color: #143a22; }

@media (max-width: 768px) {
    .detalle-grid { grid-template-columns: 1fr; }
}
</style>