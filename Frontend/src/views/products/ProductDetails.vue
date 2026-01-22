<script setup>
    import { useRoute, useRouter } from 'vue-router';
    import { onMounted, ref } from 'vue';
    import axios from 'axios';

    const route = useRoute();
    const router = useRouter();

    const producto = ref(null);
    const cargando = ref(true);

    const obtenerDetalleProducto = async () => {
        try {
            const idProducto = route.params.id;
            const respuesta = await axios.get(`http://localhost:8080/api/products/${idProducto}`, {
                withCredentials: true
            });
            producto.value = respuesta.data; 
            
        } catch (error) {
            console.error("Error cargando el producto:", error);
        } finally {
            cargando.value = false;
        }
    }

    onMounted(obtenerDetalleProducto);
</script>

<template>
    <div id="contenedor-detalle">
        
        <button @click="router.back()" class="btn-volver">
            ← Volver al listado
        </button>

        <div v-if="cargando" class="loading">
            <p>Cargando información del producto...</p>
        </div>

        <div v-else-if="producto" class="detalle-card">
            
            <div class="columna-img">
                <img :src="producto.img" :alt="producto.nombre || 'Producto'">
            </div>

            <div class="columna-info">
                <div class="header-info">
                    <h1>{{ producto.nombre }}</h1> 
                    <span class="precio">${{ producto.price }} / {{ producto.type_stock }}</span>
                </div>

                <div class="granja-info">
                    <p>📍 Producido por: <strong>{{ producto.delivery_point?.name }}</strong></p>
                </div>

                <hr class="separador">

                <div class="descripcion">
                    <h3>Descripción</h3>
                    <p>{{ producto.description || "Este producto es fresco y local, pero el productor no ha añadido una descripción detallada." }}</p>
                </div>

                <div class="acciones">
                    <a v-if="producto.delivery_point"
                       :href="`http://maps.google.com/?q=${producto.delivery_point.latitude},${producto.delivery_point.length}`" 
                       target="_blank"
                       class="btn-mapa">
                        Ver ubicación en Mapa
                    </a>

                    <button class="btn-comprar">
                        Añadir a la cesta
                    </button>
                </div>
            </div>
        </div>

        <div v-else class="error">
            <p>No se ha podido encontrar el producto.</p>
        </div>

    </div>
</template>

<style scoped>
#contenedor-detalle {
    padding: 40px;
    max-width: 1200px;
    margin: 0 auto;
    font-family: sans-serif;
}

.btn-volver {
    background: none;
    border: none;
    color: #666;
    font-size: 1rem;
    cursor: pointer;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 5px;
    font-weight: 600;
}
.btn-volver:hover {
    color: #1c5537;
    text-decoration: underline;
}

.detalle-card {
    display: grid;
    grid-template-columns: 1fr 1fr; 
    gap: 40px;
    background-color: white;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.columna-img {
    width: 100%;
    height: 400px;
    border-radius: 8px;
    overflow: hidden;
    background-color: #f9f9f9;
}

.columna-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}


.columna-info {
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.header-info h1 {
    color: #143b27;
    margin: 0 0 10px 0;
    font-size: 2rem;
}

.precio {
    font-size: 1.5rem;
    color: #1c5537;
    font-weight: bold;
}

.granja-info {
    margin-top: 10px;
    color: #666;
}

.separador {
    border: 0;
    border-top: 1px solid #eee;
    margin: 20px 0;
    width: 100%;
}

.descripcion h3 {
    color: #333;
    margin-bottom: 10px;
}

.descripcion p {
    color: #555;
    line-height: 1.6;
}
.acciones {
    margin-top: 30px;
    display: flex;
    gap: 15px;
    align-items: center;
}

.btn-comprar {
    background-color: #1c5537;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s;
    flex-grow: 1;
    text-align: center;
}

.btn-comprar:hover {
    background-color: #143b27;
}

.btn-mapa {
    padding: 12px 20px;
    border: 1px solid #1c5537;
    color: #1c5537;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
}

.btn-mapa:hover {
    background-color: #f0fdf4;
}

/* Responsive */
@media (max-width: 768px) {
    .detalle-card {
        grid-template-columns: 1fr;
    }
    .columna-img {
        height: 250px;
    }
}
</style>