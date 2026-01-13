<script setup>
import { ref, computed } from 'vue'

const name = ref('')
const category_id = ref('')
const price = ref(null)
const stock = ref(null)
const type_stock = ref('')
const state = ref('')

const props = defineProps({
  categorias: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['created'])

const producto = computed(() => ({
  id: Date.now(), // simulamos un ID único
  name: name.value,
  category_id: category_id.value,
  price: price.value,
  stock: stock.value,
  type_stock: type_stock.value,
  state: state.value
}))

const enviarDatos = () => {
  if (!name.value || !category_id.value || !price.value || !stock.value || !type_stock.value || !state.value) {
    alert('Por favor, rellena todos los campos')
    return
  }

  // Emitimos el nuevo producto al padre
  emit('created', producto.value)

  // Limpiamos el formulario
  name.value = ''
  category_id.value = ''
  price.value = null
  stock.value = null
  type_stock.value = ''
  state.value = ''
}
</script>

<template>
  <div id="form-container">
    <form @submit.prevent="enviarDatos">
      <label>Nombre del producto</label><br>
      <input type="text" v-model="name" placeholder="Nombre del producto"/><br>

      <label>Precio</label><br>
      <input type="number" v-model="price" placeholder="Precio"/><br>

      <label>Stock</label><br>
      <input type="number" v-model="stock" placeholder="Stock"/><br>

      <label>Tipo de stock</label><br>
      <select v-model="type_stock">
        <option disabled value="">Selecciona tipo de stock</option>
        <option value="unidad">Unidad</option>
        <option value="kg">Kg</option>
      </select><br>

      <label>Estado</label><br>
      <select v-model="state">
        <option disabled value="">Selecciona estado</option>
        <option value="disponible">Disponible</option>
        <option value="agotado">Agotado</option>
        <option value="reservado">Reservado</option>
      </select><br>

      <label>Categoría</label><br>
      <select v-model="category_id">
        <option disabled value="">Selecciona categoría</option>
        <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
          {{ categoria.name }}
        </option>
      </select><br>

      <button type="submit">Agregar producto</button>
    </form>
  </div>
</template>
