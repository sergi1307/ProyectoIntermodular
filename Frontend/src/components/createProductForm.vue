<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()

const name = ref('')
const category_id = ref('')
const price = ref(null)
const stock = ref(null)
const type_stock = ref('')
const status = ref('')

const producto = computed(() => ({
  name: name.value,
  category_id: category_id.value,
  price: price.value,
  stock: stock.value,
  type_stock: type_stock.value,
  status: status.value
}))

const enviarDatos = async () => {
  if (!name.value || !category_id.value || !price.value || !stock.value || !type_stock.value || !status.value) {
    alert('Por favor, rellena todos los campos')
    return
  }

  try {
    await axios.post('http://localhost:8080/api/products', producto.value)
    alert('Producto creado correctamente')
    router.push('/productos')
  } catch (error) {
    console.error('Error creando producto:', error)
  }
}

const resetForm = () => {
  name.value = ''
  category_id.value = ''
  price.value = null
  stock.value = null
  type_stock.value = ''
  status.value = ''
}
</script>

<template>
  <div id="form-container">
    <form @submit.prevent="enviarDatos">

      <input
        type="text"
        placeholder="Nombre del producto"
        v-model="name"
      />

      <input
        type="number"
        placeholder="Precio"
        v-model="price"
      />

      <input
        type="number"
        placeholder="Stock"
        v-model="stock"
      />

      <select v-model="type_stock">
        <option disabled value="">Selecciona tipo de stock</option>
        <option value="unidad">Unidad</option>
        <option value="kg">Kg</option>
      </select>

      <select v-model="status">
        <option disabled value="">Selecciona estado</option>
        <option value="disponible">Disponible</option>
        <option value="agotado">Agotado</option>
        <option value="reservado">Reservado</option>
      </select>

      <select v-model="category_id">
        <option disabled value="">Selecciona categoría</option>
        <option value="1">Fruta</option>
        <option value="2">Verdura</option>
        <!--
        <option disabled value="">Selecciona una categoría</option>
        <option
          v-for="categoria in categorias"
          :key="categoria.id"
          :value="categoria.id"
        >
        {{ categoria.name }}
        </option>
        -->
        </select>
          <select v-model="type_stock">
          <option disabled value="">Tipo de stock</option>
          <option value="unidad">Unidad</option>
          <option value="kg">Kg</option>
        </select>

        <select v-model="status">
          <option disabled value="">Estado</option>
          <option value="disponible">Disponible</option>
          <option value="agotado">Agotado</option>
          <option value="reservado">Reservado</option>
        </select>
      <button type="submit">Agregar producto</button>
      <button type="button" @click="resetForm">Reset</button>

    </form>
  </div>
</template>
