<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'

const categorias = ref([])

// Camps del formulari
const name = ref('')
const description = ref('')
const price = ref(null)
const stock = ref(null)
const type_stock = ref('')
const state = ref('')
const category_id = ref('')

// Props del producte
const props = defineProps({
  producto: { type: Object, required: true }
})

// Emitim la senyal d'actualitzat
const emit = defineEmits(['updated'])

// Carreguem les categories per al select
const cargarCategorias = async () => {
  try {
    const res = await axios.get('http://localhost:8080/api/categories')
    categorias.value = res.data

  } catch (error) {
    console.error('Error cargando categorías:', error)
  }
}

// Computed per a enviar al backend solo els camps de productes
const productoBackend = computed(() => ({
  name: name.value,
  description: description.value,
  price: price.value,
  stock: stock.value,
  type_stock: type_stock.value,
  state: state.value
}))

// Guardar canvis
const guardar = async () => {
  try {
    const res = await axios.put(
      `http://localhost:8080/api/products/update/${props.producto.id_product}`,
      productoBackend.value
    )

    emit('updated', {
      ...res.data.product,
      category_id: category_id.value
    })

  } catch (error) {
    console.error('Error al actualizar:', error)
  }
}

watch(
  () => props.producto,
  (p) => {
    if (!p) return

    name.value = p.name
    description.value = p.description
    price.value = p.price
    stock.value = p.stock
    type_stock.value = p.type_stock
    state.value = p.state
    category_id.value = p.category_id ?? ''
  },
  { immediate: true }
)

onMounted(cargarCategorias)
</script>

<template>
  <div id="form-container">
    <form @submit.prevent="guardar">

      <!--Nom del producte-->
      <label>Nombre del producto</label><br>
      <input type="text" v-model="name" placeholder="Nombre del producto"/><br>

      <!--Descrició del producte-->
      <label for="description">Descripción</label><br>
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
        <option value="Unidad">Unidad</option>
        <option value="Kg">Kg</option>
      </select><br>
      
      <!--Estat-->
      <label>Estado</label><br>
      <select v-model="state">
        <option disabled value="">Selecciona estado</option>
        <option value="Disponible">Disponible</option>
        <option value="Agotado">Agotado</option>
        <option value="Reservado">Reservado</option>
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

      <button id="submit" type="submit">Actualizar producto</button>
    </form>
  </div>
</template>

<style scoped>
#form-container {
  width: 100%;
  padding: 0;
  margin: 0;
}

form {
  display: grid;
  grid-template-columns: 1fr;
  gap: 14px;
}

label {
  font-size: 13px;
  font-weight: 600;
  color: #374151;
}

input,
select,
textarea {
  width: 100%;
  padding: 12px 14px;
  border-radius: 10px;
  border: 1px solid #e5e7eb;
  font-size: 14px;
}

input:focus,
select:focus,
textarea:focus {
  outline: none;
  border-color: #1c5537;
  box-shadow: 0 0 0 3px rgba(28, 85, 55, 0.15);
}

textarea {
  resize: none;
  min-height: 90px;
}

select {
  appearance: none;
  background-repeat: no-repeat;
  background-position: right 14px center;
  background-size: 16px;
  padding-right: 40px;
}

button {
  background-color: #1c5537;
  border-radius: 20px;
  border: none;
  color: white;
  padding: 5px 15px;
}
</style>