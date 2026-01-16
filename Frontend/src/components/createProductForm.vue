<script setup>
// Importem les funcions de vue
import { ref, computed } from 'vue'

// Definim els camps del formulari buits
const name = ref('')
const description = ref('')
const category_id = ref('')
const price = ref(null)
const stock = ref(null)
const type_stock = ref('')
const state = ref('')

// Definim categoria com un array per al desplegable de les categories
const props = defineProps({
  categorias: {
    type: Array,
    default: () => []
  }
})
// Definim l'emissió per a la creació
const emit = defineEmits(['created'])

const producto = computed(() => ({
  id: Date.now(), 
  name: name.value,
  description: description.value,
  category_id: category_id.value,
  price: price.value,
  stock: stock.value,
  type_stock: type_stock.value,
  state: state.value
}))

const enviarDatos = () => {
  if (!name.value || !description.value || !category_id.value || !price.value || !stock.value || !type_stock.value || !state.value) {
    alert('Por favor, rellena todos los campos')
    return
  }

  // Emitim el producte al pare
  emit('created', producto.value)

  // I procedim a buidar les dades del formulari
  name.value = ''
  description.value = ''
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

      <!--Nom del producte-->
      <label>Nombre del producto</label><br>
      <input type="text" v-model="name" placeholder="Nombre del producto"/><br>

      <!--Descrició del producte-->
      <label>Descripción</label><br>
      <input type="textarea" v-model="description" placeholder="Descripción del producto"/><br>

      <!--Preu-->
      <label>Precio</label><br>
      <input type="number" step="0.01" v-model="price" placeholder="Precio"/><br>
      
      <!--Stock-->
      <label>Stock</label><br>
      <input type="number" v-model="stock" placeholder="Stock"/><br>
      
      <!--Tipus de stock-->
      <label>Tipo de stock</label><br>
      <select v-model="type_stock">
        <option disabled value="">Selecciona tipo de stock</option>
        <option value="unidad">Unidad</option>
        <option value="kg">Kg</option>
      </select><br>
      
      <!--Estat-->
      <label>Estado</label><br>
      <select v-model="state">
        <option disabled value="">Selecciona estado</option>
        <option value="disponible">Disponible</option>
        <option value="agotado">Agotado</option>
        <option value="reservado">Reservado</option>
      </select><br>
      
      <!--Categoria-->
      <label>Categoría</label><br>
      <select v-model="category_id">
        <!--Recorrem la llista de les categories per a mostrar-les en el desplegable-->
        <option disabled value="">Selecciona categoría</option>
        <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
          {{ categoria.name }}
        </option>
      </select><br><br>

      <button id="submit" type="submit">Agregar producto</button>
    </form>
  </div>
</template>
<style>
  #submit{
    background-color: #1c5537;
    border-radius: 20px;
    border: none;
    color: white;
  }
</style>