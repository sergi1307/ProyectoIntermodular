<script setup>
// Importem les funcions de vue
import { ref, computed } from 'vue'
import axios from 'axios'

// Definim categoria com un array per al desplegable de les categories
const props = defineProps({
  categorias: {
    type: Array,
    default: () => []
  }
})

// Definim l'emissió per a la creació
const emit = defineEmits(['created'])

// Definim els camps del formulari buits
const name = ref('')
const description = ref('')
const category_id = ref('')
const price = ref(null)
const stock = ref(null)
const type_stock = ref('')
const state = ref('')

const enviarDatos = async () => {
  if (
      !name.value || 
      !category_id.value || 
      price.value === null || 
      stock.value === null || 
      !type_stock.value || 
      !state.value
  ) {
    alert('Por favor, rellena todos los campos')
    return
  }

// const producto = computed(() => ({
//   id: Date.now(),
//   name: name.value,
//   description: description.value,
//   category_id: category_id.value,
//   price: price.value,
//   stock: stock.value,
//   type_stock: type_stock.value,
//   state: state.value
// }))

// // Emitim el producte al pare
// emit('created', producto.value)

const usuarioTexto = localStorage.getItem('user');

if (!usuarioTexto) {
    alert("No se encontró la sesión del usuario. Por favor, inicia sesión.");
    return;
}

const user = JSON.parse(usuarioTexto);

const id = user.id_user;

console.log("ID del usuario:", id);
try {
  const payload = {
    id_user: id,        
    id_delivery_point: 1,       
    name: name.value,
    description: description.value,
    price: price.value,
    stock: stock.value,
    type_stock: type_stock.value === 'kg' ? 'Kg' : 'Unidad',
    state:
      state.value === 'disponible'
        ? 'Disponible'
        : state.value === 'agotado'
        ? 'Agotado'
        : 'Reservado',
    categories: category_id.value
    } 
  
  await axios.post(
    'http://localhost:8080/api/products/store',
    payload,
    {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    }
  )

    emit('created') // solo avisamos al padre
    
    // I procedim a buidar les dades del formulari
    name.value = ''
    category_id.value = ''
    price.value = null
    stock.value = null
    type_stock.value = ''
    state.value = ''
    
    alert ('Producto creado correctamente')
    
  } catch (error) {
    console.error('Error al crear producto:', error.response?.data || error)
  }
}
</script>

<template>
  <div id="form-container">
    <form @submit.prevent="enviarDatos">

      <!--Nom del producte-->
      <label>Nombre del producto</label><br>
      <input type="text" v-model="name" placeholder="Nombre del producto"/><br>

      <!--Descrició del producte-->
      <label for="description">Descripción</label><br>
      <textarea v-model="description" placeholder="Descripción del producto"></textarea><br>

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
      <label>Categorías</label><br>
      
      <div class="checkbox-container">
        <div v-if="props.categorias.length === 0">
           No hay categorías cargadas.
        </div>

         <select v-model="category_id">
        <!--Recorrem la llista de les categories per a mostrar-les en el desplegable-->
        <option disabled value="">Selecciona categoría</option>
        <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
          {{ categoria.name }}
        </option>
      </select><br><br>
      </div>
      <br>

      <button id="submit" type="submit">Agregar producto</button>
    </form>
  </div>
</template>

<style scoped>
#form-container {
  width: 100%;
  padding: 0;
  margin: 0;
  max-height: 65vh;
  overflow-y: auto;
  overflow-x: hidden;
  padding-right: 10px;
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

 #submit{
    background-color: #1c5537;
    border-radius: 20px;
    border: none;
    color: white;
 }
</style>