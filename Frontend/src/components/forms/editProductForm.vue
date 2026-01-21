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
// const state = ref('') // Ya no lo necesitamos manual
const category_id = ref('')

// Props del producte (rebem el producte seleccionat)
const props = defineProps({
  producto: { type: Object, required: true }
})

// Emitim la senyal d'actualitzat al pare
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

// Guardar canvis
const guardar = async () => {
  // Cálculo automático del estado (Igual que en crear)
  const estadoCalculado = stock.value > 0 ? 'Disponible' : 'Agotado';

  // Preparamos los datos
  const payload = {
    name: name.value,
    description: description.value,
    price: price.value,
    stock: stock.value,
    type_stock: type_stock.value,
    state: estadoCalculado,       // Automático
    id_category: category_id.value // Nombre correcto para la BD
  }

  try {
    // 1. Enviem les dades al backend
    // CORRECCIÓN 403: AÑADIMOS EL HEADER CON EL TOKEN
    const res = await axios.put(
      `http://localhost:8080/api/products/update/${props.producto.id_product}`,
      payload,
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`
        }
      }
    )

    // 2. Avisem al component pare
    emit('updated', {
      ...res.data.product, 
      id_product: props.producto.id_product, // Aseguramos mantener el ID
      category_id: category_id.value
    })
    alert('Producto actualizado correctamente');

  } catch (error) {
    console.error('Error al actualizar:', error.response?.data || error)
    // Mostramos mensaje de error más claro si el backend lo envía
    if (error.response?.status === 403) {
       alert('No tienes permiso para editar este producto.');
    } else {
       alert('Error al guardar. Revisa la consola.');
    }
  }
}

// Mirem si canvia el producte seleccionat per omplir el formulari
watch(
  () => props.producto,
  (p) => {
    if (!p) return
    name.value = p.name
    description.value = p.description
    price.value = p.price
    stock.value = p.stock
    type_stock.value = p.type_stock
    // state.value = p.state // Ya no hace falta
    
    // Intentamos coger id_category o category_id, lo que venga
    category_id.value = p.id_category || p.category_id || ''
  },
  { immediate: true }
)

onMounted(cargarCategorias)
</script>

<template>
  <div id="form-container">
    <form @submit.prevent="guardar">

      <label>Nombre del producto</label>
      <input type="text" v-model="name" placeholder="Nombre del producto"/>

      <label>Descripción</label>
      <textarea v-model="description" placeholder="Descripción del producto"></textarea>

      <label>Precio</label>
      <input type="number" step="0.01" v-model="price" placeholder="Precio"/>
      
      <label>Stock</label>
      <input type="number" v-model="stock" placeholder="Stock"/>
      
      <label>Tipo de stock</label>
      <select v-model="type_stock">
        <option disabled value="">Selecciona tipo de stock</option>
        <option value="Unidad">Unidad</option>
        <option value="Kg">Kg</option>
      </select>
      
      <label>Categoría</label>
      <select v-model="category_id">
        <option disabled value="">Selecciona categoría</option>
        <option 
          v-for="categoria in categorias" 
          :key="categoria.id_category" 
          :value="categoria.id_category"
        >
          {{ categoria.name }}
        </option>
      </select>

      <div class="actions">
          <button id="submit" type="submit">Actualizar producto</button>
      </div>
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
  margin-bottom: -8px; 
}

input,
select,
textarea {
  width: 100%;
  padding: 12px 14px;
  border-radius: 10px;
  border: 1px solid #e5e7eb;
  font-size: 14px;
  background-color: #fff;
  box-sizing: border-box; 
}

input:focus,
select:focus,
textarea:focus {
  outline: none;
  border-color: #1c5537;
  box-shadow: 0 0 0 3px rgba(28, 85, 55, 0.15);
}

textarea {
  resize: vertical;
  min-height: 90px;
  font-family: inherit;
}

select {
  appearance: none;
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 14px center;
  background-size: 16px;
  padding-right: 40px;
}

.actions {
    margin-top: 10px;
    display: flex;
    justify-content: flex-end;
}

button {
  background-color: #1c5537;
  border-radius: 99px;
  border: none;
  color: white;
  padding: 12px 24px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

button:hover {
    background-color: #14412a;
}
</style> 