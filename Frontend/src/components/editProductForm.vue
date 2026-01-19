<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'
// Importem les dades del JSON (borrar quan anem a fer les peticions al backend)
import productosMock from '../views/productos.json'
import categoriasMock from '../views/categorias.json'

// Llista de les categories
const categorias = ref([])

// Definim els camps del formulari buits
const name = ref('')
const description = ref('')
const category_id = ref('')
const price = ref(null)
const stock = ref(null)
const type_stock = ref('')
const state = ref('')

// Definim el producte com a objecte requerit per al formulari
const props = defineProps({
  producto: {
    type: Object,
    required: true
  }
})
// Emitim la senyal d'actualitzar
const emit = defineEmits(['updated'])

const cargarCategorias = async () => {
  /*
  const res = await axios.get('http://localhost:8080/api/categorias')
  categorias.value = res.data
  */
  categorias.value = categoriasMock
}


const producto = computed(() => ({
  // Mostrem el producte amb el valor que agafem del producte que editarem
  name: name.value,
  description: description.value,
  category_id: category_id.value,
  price: price.value,
  stock: stock.value,
  type_stock: type_stock.value,
  state: state.value
}))
const guardar = async () => {
    /*
  try{
    await axios.put(`/api/products/${props.producto.id}`, productoActualizado)
    emit('updated', productoActualizado)
  } catch (error){
   console.log("Error al actualizar" + error)
  }
    */
  // Emitim la senyal de l'actualitzat amb les dades del formulari i en la vista producte s'actualitzara
  emit('updated', {
  id: props.producto.id,
  name: name.value,
  description: description.value,
  category_id: category_id.value,
  price: price.value,
  stock: stock.value,
  type_stock: type_stock.value,
  state: state.value
})



}
watch(
  () => props.producto,
  (p) => {
    if (!p) return

    name.value = p.name
    description.value = p.description
    category_id.value = p.category_id
    price.value = p.price
    stock.value = p.stock
    type_stock.value = p.type_stock
    state.value = p.state
  },
  { immediate: true }
)
// Carrega les categories per al desplegable
onMounted(cargarCategorias)

</script>


<template>
  <div id="form-container">
    <form @submit.prevent="guardar">
      <!--Nom del producte-->
      <label for="product">Producto</label><br>
      <input type="text" placeholder="Nombre del producto" v-model="name"/><br>

      <!--Descrició del producte-->
      <label>Descripción</label><br>
      <input type="textarea" v-model="description" placeholder="Descripción del producto"/><br>
      
      <!--Preu-->
      <label for="price">Precio</label><br>
      <input type="number" step="0.01" placeholder="Precio" v-model="price"/><br>

      <!--Stock-->
      <label for="stock">Stock</label><br>
      <input type="number" placeholder="Stock" v-model="stock"/><br>

      <!--Tipus de stock-->
      <label for="type_stock">Tipo de stock</label><br>
      <select v-model="type_stock">
        <option disabled value="">Selecciona tipo de stock</option>
        <option value="unidad">Unidad</option>
        <option value="kg">Kg</option>
      </select><br>

      <!--Estat-->
      <label for="state">Estado</label><br>
      <select v-model="state">
        <option disabled value="">Selecciona estado</option>
        <option value="disponible">Disponible</option>
        <option value="agotado">Agotado</option>
        <option value="reservado">Reservado</option>
      </select><br>
      
      <!--Categoria-->
      <label for="category">Categoria</label><br>
      <select v-model="category_id">
      <!--Recorrem un for per a mostrar tots els valors de categoria en el desplegable-->
        <option disabled value="">Selecciona una categoría</option>
        <option
          v-for="categoria in categorias"
          :key="categoria.id"
          :value="categoria.id"
        >
        {{ categoria.name }}
        </option>
      </select><br><br>
      <button id="submit" type="submit">Actualizar producto</button>
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