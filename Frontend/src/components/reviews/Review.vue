<script setup>
import { ref, computed, onMounted } from 'vue'

const reviews = ref([])
const sales = ref([])
const users = ref([])

const pagina = ref(1)
const porPagina = 10
const totalPaginas = ref(1)

// Reviews visibles según la página
const reviewsVisibles = computed(() => {
  const inicio = (pagina.value - 1) * porPagina
  const fin = inicio + porPagina
  return reviews.value.slice(inicio, fin)
})

// Navegación
const siguiente = () => {
  if (pagina.value < totalPaginas.value) pagina.value++
}

const anterior = () => {
  if (pagina.value > 1) pagina.value--
}

// Obtener nombre del usuario a partir de la review
const getUserName = (review) => {
  const sale = sales.value.find(
    s => Number(s.id) === Number(review.id_sale)
  )

  if (!sale) return 'Usuario desconocido'

  const user = users.value.find(
    u => Number(u.id) === Number(sale.id_buyer)
  )

  return user ? user.name : 'Usuario desconocido'
}



onMounted(async () => {
  // Cargar datos
  const [reviewsRes, salesRes, usersRes] = await Promise.all([
    fetch('http://localhost:3000/reviews'),
    fetch('http://localhost:3000/sales'),
    fetch('http://localhost:3000/users')
  ])

  reviews.value = await reviewsRes.json()
  sales.value = await salesRes.json()
  users.value = await usersRes.json()

  totalPaginas.value = Math.ceil(reviews.value.length / porPagina)
})
</script>

<template>
  <section>
    <h3>Opiniones</h3>

    <article
      v-for="review in reviewsVisibles"
      :key="review.id"
      class="review"
    >
      <div class="header">
        <strong>{{ getUserName(review) }}</strong><br>
        <span>{{ review.calification }} ⭐</span>
        <small>{{ review.review_date }}</small>
      </div>

      <p>{{ review.comment }}</p>
    </article>

    <div class="paginacion">
      <button @click="anterior" :disabled="pagina === 1" :class="{ 'anterior': pagina === 1 }">
        Anterior
      </button>

      <span>Página {{ pagina }} de {{ totalPaginas }}</span>

      <button @click="siguiente" :disabled="pagina === totalPaginas" :class="{ 'siguiente': pagina === totalPaginas }">
        Siguiente
      </button>
    </div>
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
}
.anterior{
  background-color: #ccc;
  cursor: not-allowed;
}
.siguiente{
  background-color: #ccc;
  cursor: not-allowed;
}
</style>