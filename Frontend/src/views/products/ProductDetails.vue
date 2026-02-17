<script setup>
import { ref, onMounted, computed, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import starRating from "../../components/ratings/starRating.vue";
import axios from "axios";
import Review from "@/components/reviews/Review.vue";
import { useNotificaciones } from '@/utilidades/useNotificaciones';
import url from '../../config/api';

const notificacion = useNotificaciones();

const route = useRoute();
const router = useRouter();
const producto = ref(null);
const ventasUsuario = ref([]);
const cargando = ref(true);
const error = ref(null);
const ReviewsProducto = ref([]);
const rating = ref(0);
const reviewComment = ref("");
const mediaRating = ref(0);
const totalReviews = ref(0);

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
    const response = await axios.get(
      `${url}/api/products/show/${id}`,
      {
        withCredentials: true,
      },
    );
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
    if (cantidadSeleccionada.value > producto.value.stock)
      cantidadSeleccionada.value = producto.value.stock;
  }
};

const contactarVendedor = async () => {
  const token = localStorage.getItem("token");
  if (!token) {
    notificacion.advertencia("Debes iniciar sesión para chatear.");
    return;
  }

  console.log("Iniciando chat con:", producto.value.user.name);

  const primerMensaje = {
    id_product: producto.value.id_product,
    id_receiver: producto.value.user.id_user,
    content: `Hola, estoy interesado en tu producto: ${producto.value.name}`,
  };

  try {
    await axios.post(`${url}/api/messages/`, primerMensaje, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    console.log("Chat creado con éxito. Redirigiendo...");

    router.push({
      path: "/message",
      query: {
        prodId: producto.value.id_product,
        userId: producto.value.user.id_user,
        prodName: producto.value.name,
        userName: producto.value.user.name,
      },
    });
  } catch (error) {
    console.error("Error al crear chat:", error);
    if (error.response) {
      notificacion.error(`Error del servidor: ${JSON.stringify(error.response.data)}`);
    } else {
      notificacion.error("No se pudo conectar con el servidor.");
    }
  }
};

const realizarCompra = async () => {
  const usuarioStored = localStorage.getItem("user");
  const token = localStorage.getItem("token");

  if (!usuarioStored || !token) {
    notificacion.advertencia("Debes iniciar sesión para realizar una compra.");
    return;
  }
  const usuario = JSON.parse(usuarioStored);

  if (producto.value.stock <= 0) {
    notificacion.advertencia("Lo sentimos, este producto no tiene stock disponible.");
    return;
  }

  const datosVenta = {
    id_product: producto.value.id_product,
    id_buyer: usuario.id_user,
    id_seller: producto.value.user.id_user,
    id_delivery_point:
      producto.value.id_delivery_point ||
      producto.value.delivery_point?.id_delivery_point,
    total: parseFloat(precioTotal.value),
    quantity: cantidadSeleccionada.value,
  };

  try {
    const response = await axios.post(
      `${url}/api/sales/store`,
      datosVenta,
      {
        withCredentials: true,
        headers: {
          Authorization: `Bearer ${token}`,
        },
      },
    );

    if (response.data.status === "true" || response.status === 200) {
      notificacion.exito(
        `¡Compra realizada con éxito! Has comprado ${cantidadSeleccionada.value} unidad(es) por ${precioTotal.value}€`,
      );
      obtenerDetalleProducto();
    }
  } catch (err) {
    console.error(err);
    const mensaje =
      err.response?.data?.message || "Error al procesar la compra";
    notificacion.error(mensaje);
  }
};
const obtenerVentasUsuario = async () => {
  const token = localStorage.getItem("token");

  const response = await axios.get(
    `${url}/api/sales/my-purchases`,
    {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    },
  );

  ventasUsuario.value = response.data;
};

const obtenerVentaDelProducto = () => {
  return ventasUsuario.value.find(
    (sale) =>
      Number(sale.product?.id_product) === Number(producto.value.id_product),
  );
};

const obtenerReviewsProducto = async () => {
  try {
    const response = await axios.get(
      `${url}/api/reviews/producto/${producto.value.id_product}`,
    );
    ReviewsProducto.value = response.data;
    console.log("ReviewsProducto:", ReviewsProducto.value);
  } catch (err) {
    console.error(err);
  }
};

const realizarReview = async () => {
  const usuario = JSON.parse(localStorage.getItem("user"));
  const token = localStorage.getItem("token");

  if (!usuario || !token) {
    notificacion.advertencia("Debes iniciar sesión");
    return;
  }

  const venta = obtenerVentaDelProducto();

  if (!venta) {
    notificacion.advertencia("Debes haber comprado este producto para dejar una review");
    return;
  }

  const datosReview = {
    id_sale: venta.id_sale,
    calification: rating.value,
    comment: reviewComment.value,
  };

  await axios.post(`${url}/api/reviews/store`, datosReview, {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  });

  notificacion.exito("Review enviada correctamente");
  location.reload();
};
const obtenerMediaReviews = async () => {
  try {
    const response = await axios.get(
      `${url}/api/reviews/producto/${producto.value.id_product}/media`,
    );

    mediaRating.value = response.data.average ?? 0;
    totalReviews.value = response.data.total_reviews ?? 0;
  } catch (err) {
    console.log("Error al obtener la media");
  }
};

onMounted(() => {
  obtenerDetalleProducto();
  obtenerVentasUsuario();
  watch(producto, (newVal) => {
    if (newVal) obtenerReviewsProducto();
    if (newVal?.id_product) {
      obtenerMediaReviews();
    }
  });
});
</script>

<template>
  <div class="detalle-container">
    
    <button @click="volver" class="btn-volver">&larr; Volver al listado</button>

    <div v-if="cargando" class="loading">Cargando datos...</div>

    <div v-else-if="producto">
        
        <div class="detalle-grid">
            
            <div class="detalle-imagen">
                <img
                :src="`${url}/storage/${producto.image}`"
                :alt="producto.nombre"
                />
            </div>

            <div class="detalle-info">
                
                <div class="tags">
                    <span v-if="producto.category && producto.category.name" class="tag">
                        {{ producto.category.name }}
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

                <div class="calificacion" v-if="mediaRating">
                    <span class="rating-number">{{ mediaRating.toFixed(2) }}</span>
                    <starRating :modelValue="mediaRating" readonly />
                    <span class="opinions">({{ totalReviews }} opiniones)</span>
                </div>

                <div class="descripcion-box">
                    <h3>Descripción</h3>
                    <p>{{ producto.description || "Sin descripción disponible." }}</p>

                    <div class="meta-info">
                        <div>
                            <strong>📍 Origen:</strong><br />
                            {{ producto.delivery_point ? producto.delivery_point.name : "Ubicación desconocida" }}
                        </div>
                        <div>
                            <strong>📦 Stock:</strong><br />
                            {{ producto.stock }} {{ producto.type_stock }}
                        </div>
                    </div>
                </div>

                <div class="acciones-compra">
                    
                    <div class="selector-cantidad" v-if="producto.stock > 0">
                        <label for="cantidad">Cantidad:</label>
                        <div class="input-wrapper">
                            <input
                                id="cantidad"
                                type="number"
                                v-model.number="cantidadSeleccionada"
                                min="1"
                                :max="producto.stock"
                                @change="validarCantidad"
                                class="input-cantidad"
                            />
                        </div>
                        <small class="stock-hint">Máximo {{ producto.stock }} unidades</small>
                    </div>

                    <div class="botones-grupo">
                        <button @click="contactarVendedor" class="btn-chat">💬 Chat</button>
                        <button
                            @click="realizarCompra"
                            class="btn-comprar"
                            :disabled="producto.stock <= 0"
                            :class="{ agotado: producto.stock <= 0 }"
                        >
                            {{ producto.stock > 0 ? `Comprar por ${precioTotal}€` : "Sin Stock" }}
                        </button>
                    </div>
                </div>
            </div> </div> <div class="reviews-section-full">
            <h3>Deja tu valoración</h3>
            
            <div class="review-form-wrapper">
                
                <div class="rating-input-group">
                    <label>Tu Puntuación:</label>
                    <div class="stars-row">
                        <starRating v-model="rating" />
                        <span class="rating-value">{{ rating }}/5</span>
                    </div>
                </div>

                <form class="form-comentario">
                    <label for="comment" class="label-comentario">Cuéntanos tu experiencia:</label>
                    <textarea 
                        id="comment"
                        v-model="reviewComment"
                        rows="4"
                        class="textarea-estilizado"
                        placeholder="¿Qué te ha parecido la calidad? ¿Y el trato con el vendedor?"  
                    ></textarea>
                    
                    <button type="button" @click="realizarReview" class="btn-review">
                        Enviar Valoración
                    </button>
                </form>
            </div>

            <hr class="divider">

            <Review 
                ref="listaReviewsRef"
                :reviewsData="ReviewsProducto" 
                :productId="producto.id_product" 
                :mediaRating="mediaRating"
                :totalReviews="totalReviews"
            />
        </div>

    </div> <div v-else class="error-msg">
      <h3>Ups, hubo un error</h3>
      <p>{{ error }}</p>
    </div>
  </div>
</template>

<style scoped>
* {
    box-sizing: border-box;
}

.detalle-container {
  max-width: 1200px;
  margin: 20px auto;
  padding: 20px;
  font-family: "Segoe UI", sans-serif;
  color: #333;
}

.loading, .error-msg {
  text-align: center;
  padding: 40px;
  font-size: 1.2em;
  color: #666;
}

.btn-volver {
  background: none;
  border: none;
  font-size: 16px;
  color: #555;
  cursor: pointer;
  margin-bottom: 20px;
  padding: 0;
}
.btn-volver:hover {
  text-decoration: underline;
  color: #1a4d2e;
}

.detalle-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
  background: white;
  padding: 30px;
  border-radius: 15px;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
  margin-bottom: 40px;
}

.detalle-imagen img {
  width: 100%;
  height: auto;
  border-radius: 15px;
  object-fit: cover;
  max-height: 500px;
  border: 1px solid #eee;
}

.detalle-info {
  display: flex;
  flex-direction: column;
  gap: 15px;
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

h1 {
  margin: 5px 0;
  color: #1a4d2e;
  font-size: 2.2em;
  line-height: 1.2;
}

.precio-grande {
  font-size: 2em;
  color: #1a4d2e;
  font-weight: 800;
}
.unidad {
  font-size: 0.5em;
  color: #666;
  font-weight: normal;
  vertical-align: middle;
}

.calificacion {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}
.rating-number {
  font-size: 1.3em;
  color: #1a4d2e;
  font-weight: 600;
}

.descripcion-box {
  background-color: #f9f7f2;
  padding: 20px;
  border-radius: 12px;
  color: #555;
  line-height: 1.6;
}

.meta-info {
  display: flex;
  gap: 30px;
  margin-top: 15px;
  padding-top: 15px;
  border-top: 1px solid #e6e6e6;
  font-size: 0.9em;
}

.acciones-compra {
    margin-top: 20px;
}

.selector-cantidad {
    margin-bottom: 15px;
}

.input-cantidad {
  padding: 10px;
  font-size: 1.1em;
  border: 2px solid #ddd;
  border-radius: 8px;
  width: 80px;
  text-align: center;
}

.stock-hint {
    display: block;
    color: #888;
    font-size: 0.8em;
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
  transition: background-color 0.2s;
}
.btn-comprar:hover { background-color: #143a22; }
.btn-comprar:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

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
  transition: background-color 0.2s;
}
.btn-chat:hover { background-color: #0056b3; }

.reviews-section-full {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    width: 100%;
}

.review-form-wrapper {
    margin-top: 20px;
}

.rating-input-group {
    margin-bottom: 15px;
}
.stars-row {
    display: flex;
    align-items: center;
    gap: 10px;
}
.rating-value {
    background-color: #f3f4f6;
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 0.85rem;
    color: #555;
    font-weight: bold;
}

.label-comentario {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

.textarea-estilizado {
    width: 100%;
    padding: 12px;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    background-color: #fafafa;
    font-family: inherit;
    font-size: 0.95rem;
    resize: vertical;
    box-sizing: border-box;
    transition: all 0.3s ease;
}

.textarea-estilizado:focus {
    outline: none;
    border-color: #1a4d2e;
    background-color: white;
    box-shadow: 0 0 0 3px rgba(26, 77, 46, 0.1);
}

.btn-review {
    width: 100%;
    margin-top: 15px;
    background-color: #1a4d2e;
    color: white;
    border: none;
    padding: 12px;
    font-size: 1.1em;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    box-shadow: 0 4px 6px -1px rgba(26, 77, 46, 0.2);
}
.btn-review:hover {
    background-color: #143a22;
    transform: translateY(-2px);
}

.divider {
    border: 0;
    height: 1px;
    background: #e0e0e0;
    margin: 30px 0;
}

@media (max-width: 768px) {
  .detalle-container {
    padding: 15px;
    margin: 10px auto;
  }

  .detalle-grid { 
    grid-template-columns: 1fr; 
    padding: 20px;
    gap: 25px;
  }

  .detalle-imagen img {
    max-height: 350px;
  }

  h1 {
    font-size: 1.8em;
  }

  .precio-grande {
    font-size: 1.6em;
  }

  .botones-grupo {
    flex-direction: column; 
  }

  .btn-comprar, .btn-chat {
    width: 100%;
    padding: 12px;
  }

  .meta-info {
    flex-direction: column;
    gap: 10px;
  }

  .reviews-section-full {
    padding: 20px;
  }
}
</style>