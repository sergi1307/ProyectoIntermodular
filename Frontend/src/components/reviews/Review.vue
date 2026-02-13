<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'

const url = import.meta.env.VITE_API_URL_DEV;

const props = defineProps({
  reviewsData: Object,
  productId: Number
});

// Refs reactivos
const reviewsVisibles = ref([])
const pagina = ref(1)
const totalPaginas = ref(1)

const initReviews = () => {
  const data = props.reviewsData?.data || props.reviewsData || []
  if (data.length > 0) {
    reviewsVisibles.value = data
    pagina.value = props.reviewsData?.current_page || 1
    totalPaginas.value = props.reviewsData?.last_page || 1
    console.log('Datos inicializados:', {
      reviewsVisibles: reviewsVisibles.value,
      pagina: pagina.value,
      totalPaginas: totalPaginas.value,
      productId: props.productId
    });
  } else {
    console.log('No hay reviews en la prop')
  }
}

watch(
  () => props.reviewsData,
  () => initReviews(),
  { immediate: true }
)


const siguiente = () => {
  console.log('Botón Siguiente clickeado. pagina:', pagina.value, 'totalPaginas:', totalPaginas.value)
  if (pagina.value < totalPaginas.value) fetchPage(pagina.value + 1)
}

const anterior = () => {
  console.log('Botón Anterior clickeado. pagina:', pagina.value)
  if (pagina.value > 1) fetchPage(pagina.value - 1)
}

// Función para pedir otra página
const fetchPage = async (page) => {
  if (!props.productId) {
    console.log('No hay productId, no se puede hacer fetch');
    return;
  }

  try {
    const { data } = await axios.get(
      `${url}/api/reviews/producto/${props.productId}?page=${page}`
    );
    reviewsVisibles.value = data.data || []
    pagina.value = data.current_page || 1
    totalPaginas.value = data.last_page || 1
    console.log('Datos actualizados tras fetchPage:', { pagina: pagina.value, totalPaginas: totalPaginas.value, reviewsVisibles: reviewsVisibles.value })
  } catch (err) {
    console.error('Error en fetchPage:', err)
  }
}
</script>


<template>
  <section>
    <h3>Opiniones</h3>

    <article
      v-for="review in reviewsVisibles"
      :key="review.id_review"
      class="review"
    >
      <div class="header">
        <strong>{{ review.sale?.buyer?.name || 'Usuario desconocido' }}</strong><br>
        <span>{{ review.calification }} ⭐</span>
        <small>{{ review.review_date }}</small>
      </div>

      <p>{{ review.comment }}</p>
    </article>

    <div class="paginacion" v-if="totalPaginas > 1">
      <button @click="anterior" :disabled="pagina === 1" class="anterior">
        Anterior
      </button>

      <span>Página {{ pagina }} de {{ totalPaginas }}</span>

      <button @click="siguiente" :disabled="pagina === totalPaginas" class="siguiente">
        Siguiente
      </button>
    </div>

    <p v-if="reviewsVisibles.length === 0">No hay opiniones aún</p>
  </section>
</template>

<style scoped>
section {
  background-color: #f9fafb;
  border-radius: 16px;
  padding: 24px;
  margin-top: 30px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

h3 {
  color: #1c5537;
  font-size: 1.5rem;
  margin-bottom: 20px;
  border-bottom: 2px solid #e5e7eb;
  padding-bottom: 10px;
  font-weight: 700;
}

.review {
  background-color: white;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 16px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.review:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  border-color: #d1d5db;
}

.header {
  margin-bottom: 12px;
  padding-bottom: 8px;
  border-bottom: 1px dashed #f3f4f6;
  line-height: 1.6;
}

.header strong {
  color: #111827;
  font-size: 1.05rem;
}

.header span {
  color: #fbbf24;
  margin-left: 8px;
}

.header small {
  display: block;
  color: #9ca3af;
  font-size: 0.85rem;
  margin-top: 2px;
}

.review p {
  color: #4b5563;
  line-height: 1.6;
  font-size: 0.95rem;
  margin: 0;
}

.paginacion {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px;
  margin-top: 30px;
  font-size: 0.9rem;
  color: #555;
  font-weight: 500;
}

button {
  background-color: #1c5537;
  color: white;
  font-weight: 600;
  border: none;
  cursor: pointer;
  border-radius: 8px;
  padding: 10px 20px;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(28, 85, 55, 0.2);
}

button:hover:not(:disabled) {
  background-color: #14402a;
  transform: translateY(-1px);
}

button:active:not(:disabled) {
  transform: translateY(0);
}

button:disabled {
  background-color: #e5e7eb;
  color: #9ca3af;
  cursor: not-allowed;
  box-shadow: none;
}

section > p:last-child {
  text-align: center;
  color: #6b7280;
  font-style: italic;
  padding: 20px;
  background: white;
  border-radius: 8px;
  border: 1px dashed #e5e7eb;
}

@media (max-width: 600px) {
  section {
    padding: 15px;
    margin-top: 20px;
    border-radius: 12px;
  }

  h3 {
    font-size: 1.3rem;
    margin-bottom: 15px;
  }

  .review {
    padding: 15px;
  }

  .header strong {
    font-size: 1rem;
  }
  
  .paginacion {
    gap: 10px;
    flex-wrap: wrap;
  }

  button {
    flex: 1; /* Los botones ocupan el mismo ancho */
    padding: 10px;
    font-size: 0.9rem;
  }

  .paginacion span {
    width: 100%;
    text-align: center;
    order: -1; /* Pone el texto "Página X" arriba en pantallas muy pequeñas */
    margin-bottom: 5px;
  }
}
</style>
