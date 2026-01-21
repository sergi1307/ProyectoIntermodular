<script setup>
import { ref, defineProps, defineEmits } from 'vue'
import axios from 'axios'

const props = defineProps({
  categorias: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['created'])

const name = ref('')
const description = ref('')
const category_id = ref('')
const price = ref(null)
const stock = ref(null)
const type_stock = ref('')


const enviarDatos = async () => {
  const camposFaltantes = [];

  if (!name.value) camposFaltantes.push("Nombre");
  if (!category_id.value) camposFaltantes.push("Categoría");
  if (price.value === null || price.value === "") camposFaltantes.push("Precio");
  if (stock.value === null || stock.value === "") camposFaltantes.push("Stock");
  if (!type_stock.value) camposFaltantes.push("Tipo de stock");
  if (camposFaltantes.length > 0) {
    alert("Te falta rellenar: " + camposFaltantes.join(", "));
    return;
  }

  const usuarioTexto = localStorage.getItem('user');
  if (!usuarioTexto) {
    alert("No se encontró la sesión del usuario. Por favor, inicia sesión.");
    return;
  }

  const user = JSON.parse(usuarioTexto);
  const id = user.id_user;

  try {

    const estadoCalculado = stock.value > 0 ? 'Disponible' : 'Agotado';

    const payload = {
      id_user: id,        
      id_delivery_point: 1,       
      name: name.value,
      description: description.value,
      price: price.value,
      stock: stock.value,
      type_stock: type_stock.value === 'kg' ? 'Kg' : 'Unidad',
      state: estadoCalculado,
      
      id_category: category_id.value 
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

    emit('created') 
    
    name.value = ''
    description.value = ''
    category_id.value = ''
    price.value = null
    stock.value = null
    type_stock.value = ''
    
    alert('Producto creado correctamente')
    
  } catch (error) {
    console.error('Error al crear producto:', error.response?.data || error)
    alert('Error al guardar. Revisa la consola (F12) para ver el detalle.')
  }
}
</script>

<template>
  <div id="form-container">
    <form @submit.prevent="enviarDatos">

      <label>Nombre del producto</label><br>
      <input type="text" v-model="name" placeholder="Nombre del producto"/><br>

      <label for="description">Descripción</label><br>
      <textarea v-model="description" placeholder="Descripción del producto"></textarea><br>

      <label>Precio</label><br>
      <input type="number" step="0.01" v-model="price" placeholder="Precio"/><br>
      
      <label>Stock</label><br>
      <input type="number" v-model="stock" placeholder="Stock"/><br>
      
      <label>Tipo de stock</label><br>
      <select v-model="type_stock">
        <option disabled value="">Selecciona tipo de stock</option>
        <option value="unidad">Unidad</option>
        <option value="kg">Kg</option>
      </select><br>
      
      <label>Categorías</label><br>
      
      <div class="checkbox-container">
        <div v-if="props.categorias.length === 0" style="color: red; font-size: 12px; margin-bottom: 5px;">
           Cargando categorías...
        </div>

        <select v-model="category_id">
          <option disabled value="">Selecciona categoría</option>
          <option 
            v-for="categoria in props.categorias" 
            :key="categoria.id_category" 
            :value="categoria.id_category"
          >
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
   padding: 12px;
   font-weight: bold;
   cursor: pointer;
 }
 #submit:hover {
   background-color: #15422a;
 }
</style>