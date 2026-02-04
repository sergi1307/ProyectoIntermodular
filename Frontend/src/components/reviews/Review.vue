<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'

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
      `http://localhost:8080/api/reviews/producto/${props.productId}?page=${page}`
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

<style>
.header{
  padding: 5px;
}
button{
  background-color: #1a4d2e;
  color: white;
  font-weight: bold;
  border: none;
  cursor: pointer;
  border-radius: 8px;
  padding: 5px;
}
.paginacion{
  display: flex;
  justify-content: space-between;
  margin-top: 15px;
}
.anterior[disabled],
.siguiente[disabled]{
  background-color: #ccc;
  cursor: not-allowed;
}
</style>
