<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router'; 
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const producto = ref(null);
const cargando = ref(true);
const error = ref(null);

const cantidadSeleccionada = ref(1);

const volver = () => {
    router.go(-1);
};

const precioTotal = computed(() => {
    if (!producto.value) return 0;
    return (producto.value.price * cantidadSeleccionada.value).toFixed(2);
});

const obtenerDetalleProducto = async () => {
    const id = route.params.id; 
    try {
        const response = await axios.get(`http://localhost:8080/api/products/show/${id}`, {
            withCredentials: true
        });
        producto.value = response.data; 
        cargando.value = false;
        
        cantidadSeleccionada.value = 1;
    } catch (err) {
        console.error(err);
        error.value = "No se pudo cargar el producto.";
        cargando.value = false;
    }
};

const validarCantidad = () => {
    if (producto.value) {
        if (cantidadSeleccionada.value < 1) cantidadSeleccionada.value = 1;
        if (cantidadSeleccionada.value > producto.value.stock) cantidadSeleccionada.value = producto.value.stock;
    }
};

const contactarVendedor = async () => {
    const token = localStorage.getItem('token');
    if (!token) {
        alert("Debes iniciar sesión para chatear.");
        return;
    }

    console.log("Iniciando chat con:", producto.value.user.name);

    const primerMensaje = {
        id_product: producto.value.id_product,
        id_receiver: producto.value.user.id_user,
        content: `Hola, estoy interesado en tu producto: ${producto.value.name}`
    };

    try {
        await axios.post("http://localhost:8080/api/messages/", primerMensaje, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        console.log("Chat creado con éxito. Redirigiendo...");

        router.push({
            path: '/message', 
            query: {
                prodId: producto.value.id_product,
                userId: producto.value.user.id_user,
                prodName: producto.value.name,
                userName: producto.value.user.name
            }
        });

    } catch (error) {
        console.error("Error al crear chat:", error);
        if (error.response) {
             alert(`Error del servidor: ${JSON.stringify(error.response.data)}`);
        } else {
             alert("No se pudo conectar con el servidor.");
        }
    }
};

const realizarCompra = async () => {
    const usuarioStored = localStorage.getItem('user');
    const token = localStorage.getItem('token'); 

    if (!usuarioStored || !token) {
        alert("Debes iniciar sesión para realizar una compra.");
        return;
    }
    const usuario = JSON.parse(usuarioStored);

    if (producto.value.stock <= 0) {
        alert("Lo sentimos, este producto no tiene stock disponible.");
        return;
    }

    const datosVenta = {
        id_product: producto.value.id_product,
        id_buyer: usuario.id_user,
        id_seller: producto.value.user.id_user, 
        id_delivery_point: producto.value.id_delivery_point || producto.value.delivery_point?.id_delivery_point,
        total: parseFloat(precioTotal.value), 
        quantity: cantidadSeleccionada.value,
    };

    try {
        const response = await axios.post("http://localhost:8080/api/sales/store", datosVenta, {
            withCredentials: true,
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        if (response.data.status === 'true' || response.status === 200) {
            alert(`¡Compra realizada con éxito! Has comprado ${cantidadSeleccionada.value} unidad(es) por $${precioTotal.value}`);
            obtenerDetalleProducto(); 
        }
    } catch (err) {
        console.error(err);
        const mensaje = err.response?.data?.message || "Error al procesar la compra";
        alert(mensaje);
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

            <h1>{{ producto.name }}</h1>

            <div class="precio-grande">
                {{ precioTotal }}€ 
                <span class="unidad">
                    ({{ cantidadSeleccionada }} x {{ producto.price }}€)
                </span>
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
                        <strong>📦 Stock Disponible:</strong><br>
                        {{ producto.stock }} {{ producto.type_stock }}
                    </div>
                </div>
            </div>

            <div class="acciones-compra">
                
                <div class="selector-cantidad" v-if="producto.stock > 0">
                    <label for="cantidad">Cantidad a comprar:</label>
                    <div class="input-wrapper">
                        <input 
                            id="cantidad"
                            type="number" 
                            v-model.number="cantidadSeleccionada" 
                            min="1" 
                            :max="producto.stock"
                            @change="validarCantidad"
                            class="input-cantidad"
                        >
                    </div>
                    <small class="stock-hint">Máximo {{ producto.stock }} unidades</small>
                </div>

                <div class="botones-grupo">
                    <button 
                        @click="contactarVendedor" 
                        class="btn-chat"
                        title="Contactar con el vendedor"
                    >
                        💬 Chat
                    </button>

                    <button 
                        @click="realizarCompra" 
                        class="btn-comprar"
                        :disabled="producto.stock <= 0"
                        :class="{ 'agotado': producto.stock <= 0 }"
                    >
                        {{ producto.stock > 0 ? `Comprar` : 'Sin Stock' }}
                    </button>
                </div>

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

.selector-cantidad {
    margin-bottom: 15px;
}

.selector-cantidad label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

.input-cantidad {
    padding: 10px;
    font-size: 1.1em;
    border: 2px solid #ddd;
    border-radius: 8px;
    width: 80px;
    text-align: center;
}

.input-cantidad:focus {
    border-color: #1a4d2e;
    outline: none;
}

.stock-hint {
    display: block;
    color: #888;
    font-size: 0.85em;
    margin-top: 5px;
}

.botones-grupo {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.btn-comprar {
    background-color: #1a4d2e;
    color: white;
    border: none;
    padding: 15px 20px;
    font-size: 1.1em;
    border-radius: 8px;
    cursor: pointer;
    flex-grow: 2;
    font-weight: bold;
    transition: all 0.2s;
}
.btn-comprar:hover:not(:disabled) { background-color: #143a22; }

.btn-chat {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 15px 20px;
    font-size: 1.1em;
    border-radius: 8px;
    cursor: pointer;
    flex-grow: 1;
    font-weight: bold;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}
.btn-chat:hover { background-color: #0056b3; }

.btn-comprar.agotado {
    background-color: #ccc;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .detalle-grid { grid-template-columns: 1fr; }
}
</style>