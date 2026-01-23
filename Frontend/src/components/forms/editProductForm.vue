<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
  producto: { type: Object, required: true }
})

const emit = defineEmits(['updated', 'close'])

const categorias = ref([])
const puntosEntrega = ref([]) 

// Campos del formulario
const name = ref('')
const description = ref('')
const price = ref(null)
const stock = ref(null)
const type_stock = ref('')
const category_id = ref('')
const id_delivery_point = ref('') 
const imagen = ref(null)

// 1. Cargar Categorías
const cargarCategorias = async () => {
  try {
    const res = await axios.get('http://localhost:8080/api/categories')
    categorias.value = res.data
  } catch (error) {
    console.error('Error cargando categorías:', error)
  }
}

// 2. Cargar Puntos de Entrega
const cargarPuntosEntrega = async () => {
  try {
    const user = JSON.parse(localStorage.getItem('user'));
    if(!user) return;

    const res = await axios.get(`http://localhost:8080/api/delivery_points?id_user=${user.id_user}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    puntosEntrega.value = res.data;
  } catch (error) {
    console.error('Error cargando puntos de entrega:', error)
  }
}

const subirImagen = (event) => {
  imagen.value = event.target.files[0];
}

// 3. Rellenar formulario al abrir
watch(
  [() => props.producto, categorias],
  ([p, listaCategorias]) => {
    if (!p) return

    name.value = p.name
    description.value = p.description
    price.value = p.price
    stock.value = p.stock
    
    if (p.id_delivery_point) {
        id_delivery_point.value = p.id_delivery_point;
    } else if (p.delivery_point && p.delivery_point.id_delivery_point) {
        id_delivery_point.value = p.delivery_point.id_delivery_point;
    } else {
        id_delivery_point.value = '';
    }

    // Tipo de stock
    if (p.type_stock && p.type_stock.toLowerCase() === 'kg') {
       type_stock.value = 'Kg'
    } else {
       type_stock.value = 'Unidad'
    }

    // Categoría
    if (p.category && listaCategorias.length > 0) {
       const categoriaEncontrada = listaCategorias.find(cat => cat.name === p.category)
       if (categoriaEncontrada) {
          category_id.value = categoriaEncontrada.id_category
       }
    } else if (p.category_id) {
       category_id.value = p.category_id
    }
  },
  { immediate: true }
)

// 4. Guardar cambios
const guardar = async () => {
  if (!category_id.value) {
    alert("Por favor selecciona una categoría.");
    return;
  }
  if (!id_delivery_point.value) {
    alert("Por favor selecciona un punto de entrega.");
    return;
  }

  const estadoCalculado = stock.value > 0 ? 'Disponible' : 'Agotado';

  const formData = new FormData();
  formData.append('name', name.value);
  formData.append('description', description.value);
  formData.append('price', price.value);
  formData.append('stock', stock.value);
  formData.append('type_stock', type_stock.value);
  formData.append('state', estadoCalculado);
  formData.append('id_category', category_id.value);
  formData.append('id_delivery_point', id_delivery_point.value);

  if (imagen.value) {
    formData.append('image', imagen.value);
  }

  try {
    await axios.put(
      `http://localhost:8080/api/products/update/${props.producto.id_product}`,
      formData, 
      {
        headers: { 
            Authorization: `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'multipart/form-data' 
        }
      }
    )

    emit('updated'); 
    alert('Producto actualizado correctamente');

  } catch (error) {
    console.error('Error al actualizar:', error.response?.data || error)
    if (error.response?.status === 403) {
       alert('No tienes permiso para editar este producto.');
    } else {
       alert('Error al guardar. Revisa la consola.');
    }
  }
}

onMounted(() => {
  cargarCategorias();
  cargarPuntosEntrega();
})
</script>

<template>
  <div class="contenedor-formulario">
    <form @submit.prevent="guardar">
      
      <h3>Editar Producto</h3>

      <div class="campo">
        <label>Nombre del producto</label>
        <input 
          type="text" 
          v-model="name" 
          placeholder="Ej. Manzanas"
        />
      </div>

      <div class="campo">
        <label>Descripción</label>
        <textarea 
          v-model="description" 
          placeholder="Detalles del producto"
        ></textarea>
      </div>

      <div class="fila">
        <div class="campo">
          <label>Precio (€)</label>
          <input 
            type="number" 
            step="0.01" 
            v-model="price" 
            placeholder="0.00"
          />
        </div>
        
        <div class="campo">
          <label>Stock</label>
          <input 
            type="number" 
            v-model="stock" 
            placeholder="0"
          />
        </div>
      </div>

      <div class="fila">
        <div class="campo">
          <label>
            Categoría 
            <small v-if="categorias.length === 0" style="color:orange">(Cargando...)</small>
          </label>
          <select v-model="category_id">
            <option disabled value="">Seleccionar...</option>
            <option 
              v-for="cat in categorias" 
              :key="cat.id_category" 
              :value="cat.id_category"
            >
              {{ cat.name }}
            </option>
          </select>
        </div>

        <div class="campo">
          <label>Unidad de medida</label>
          <select v-model="type_stock">
            <option disabled value="">Seleccionar...</option>
            <option value="Unidad">Unidad</option>
            <option value="Kg">Kg</option>
          </select>
        </div>
      </div>

      <div class="fila">
        <div class="campo">
          <label>Punto de Entrega</label>
          <select v-model="id_delivery_point">
            <option disabled value="">Selecciona punto...</option>
            <option 
              v-for="punto in puntosEntrega" 
              :key="punto.id_delivery_point" 
              :value="punto.id_delivery_point"
            >
              {{ punto.name }}
            </option>
          </select>
        </div>

        <div class="campo">
          <label>Imagen del producto</label>
          <input 
            type="file" 
            @change="subirImagen"
            accept="image/*"
          />
        </div>
      </div>

      <div class="botones">
        <button type="submit" class="btn-guardar">
          ✎ Guardar Cambios
        </button>
      </div>

    </form>
  </div>
</template>

<style scoped>
.contenedor-formulario {
  width: 100%;
  padding-right: 5px;
  max-height: 70vh;
  overflow-y: auto;
}

form {
  display: flex;
  flex-direction: column;
  gap: 15px;
  font-family: 'Inter', sans-serif;
}

h3 {
  margin: 0 0 5px 0;
  color: #1c5537;
  font-size: 1.1rem;
  font-weight: 700;
  border-bottom: 2px solid #e5e7eb;
  padding-bottom: 10px;
}

.campo {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.fila {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
}

label {
  font-size: 14px;
  font-weight: 600;
  color: #4b5563;
}

input, select, textarea {
  padding: 10px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  background-color: #fff;
  width: 100%;
  box-sizing: border-box;
  transition: border-color 0.2s;
}

input[type="file"] {
  padding: 7px;
}

input::placeholder, textarea::placeholder {
  color: #9ca3af;
}

input:focus, select:focus, textarea:focus {
  outline: none;
  border-color: #1c5537;
  box-shadow: 0 0 0 3px rgba(28, 85, 55, 0.1);
}

textarea {
  resize: vertical;
  min-height: 80px;
  font-family: inherit;
}

select {
  appearance: none;
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%234b5563' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 10px center;
  background-size: 16px;
  cursor: pointer;
}

.botones {
  margin-top: 10px;
}

.btn-guardar {
  width: 100%;
  background-color: #1c5537;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 12px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
}

.btn-guardar:hover {
  background-color: #15422a;
}
</style>