<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'
import productosMock from '../views/productos.json'
import categoriasMock from '../views/categorias.json'


const categorias = ref([])

const name = ref('')
const category_id = ref('')
const price = ref(null)
const stock = ref(null)
const type_stock = ref('')
const state = ref('')

const props = defineProps({
  producto: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['updated'])

const cargarCategorias = async () => {
  /*
  const res = await axios.get('http://localhost:8080/api/categorias')
  categorias.value = res.data
  */
  categorias.value = categoriasMock
}


const producto = computed(() => ({
  name: name.value,
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
  // SIMULAMOS UPDATE
  emit('updated', {
  id: props.producto.id,
  name: name.value,
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
    category_id.value = p.category_id
    price.value = p.price
    stock.value = p.stock
    type_stock.value = p.type_stock
    state.value = p.state
  },
  { immediate: true }
)
onMounted(cargarCategorias)

</script>


<template>
  <div id="form-container">
    <form @submit.prevent="guardar">
      <label for="product">Producto</label><br></br>
      <input type="text" placeholder="Nombre del producto" v-model="name"/><br></br>

      <label for="price">Precio</label><br></br>
      <input type="number" placeholder="Precio" v-model="price"/><br></br>

      <label for="stock">Stock</label><br></br>
      <input type="number" placeholder="Stock" v-model="stock"/><br></br>

      <label for="type_stock">Tipo de stock</label><br></br>
      <select v-model="type_stock">
        <option disabled value="">Selecciona tipo de stock</option>
        <option value="unidad">Unidad</option>
        <option value="kg">Kg</option>
      </select><br></br>

      <label for="state">Estado</label><br></br>
      <select v-model="state">
        <option disabled value="">Selecciona estado</option>
        <option value="disponible">Disponible</option>
        <option value="agotado">Agotado</option>
        <option value="reservado">Reservado</option>
      </select><br></br>

      <label for="category">Categoria</label><br></br>
      <select v-model="category_id">
        <option disabled value="">Selecciona una categoría</option>
        <option
          v-for="categoria in categorias"
          :key="categoria.id"
          :value="categoria.id"
        >
        {{ categoria.name }}
        </option>
      </select><br></br>
      <button type="submit">Actualizar producto</button>
    </form>
  </div>
</template>
