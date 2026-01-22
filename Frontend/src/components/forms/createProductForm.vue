<script setup>
import { ref, defineProps, defineEmits, onMounted } from 'vue'
import axios from 'axios'

// Recibimos categorías del padre (como tenías antes)
const props = defineProps({
  categorias: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['created'])

// Variables del formulario
const name = ref('')
const description = ref('')
const category_id = ref('')
const price = ref(null)
const stock = ref(null)
const type_stock = ref('')
const id_delivery_point = ref('')
const imagen = ref(null)

const puntosEntrega = ref([])

// Cargar puntos de entrega del usuario actual
const cargarPuntosEntrega = async () => {
  try {
    const user = JSON.parse(localStorage.getItem('user'));
    if(!user) return;

    const res = await axios.get(`http://localhost:8080/api/delivery_points/myPoints?id_user=${user.id_user}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });

    console.log(res.data);
    puntosEntrega.value = res.data.data;
  } catch (error) {
    console.error('Error cargando puntos de entrega:', error)
  }
}

// Capturar el archivo de imagen
const subirImagen = (event) => {
  imagen.value = event.target.files[0];
}

const enviarDatos = async () => {
  // Validaciones básicas
  const camposFaltantes = [];
  if (!name.value) camposFaltantes.push("Nombre");
  if (!category_id.value) camposFaltantes.push("Categoría");
  if (!price.value) camposFaltantes.push("Precio");
  if (!stock.value) camposFaltantes.push("Stock");
  if (!type_stock.value) camposFaltantes.push("Tipo de stock");
  if (!id_delivery_point.value) camposFaltantes.push("Punto de entrega");

  if (camposFaltantes.length > 0) {
    alert("Te falta rellenar: " + camposFaltantes.join(", "));
    return;
  }

  const user = JSON.parse(localStorage.getItem('user'));
  if (!user) {
    alert("No se encontró la sesión del usuario.");
    return;
  }

  try {
    const estadoCalculado = stock.value > 0 ? 'Disponible' : 'Agotado';

    // --- CAMBIO A FORMDATA (Para poder enviar la imagen) ---
    const formData = new FormData();
    formData.append('id_user', user.id_user);
    formData.append('name', name.value);
    formData.append('description', description.value);
    formData.append('price', price.value);
    formData.append('stock', stock.value);
    formData.append('type_stock', type_stock.value === 'kg' ? 'Kg' : 'Unidad');
    formData.append('state', estadoCalculado);
    formData.append('id_category', category_id.value);
    formData.append('id_delivery_point', id_delivery_point.value);

    // Solo añadimos la imagen si el usuario seleccionó una
    if (imagen.value) {
      formData.append('image', imagen.value);
    }
  
    await axios.post(
      'http://localhost:8080/api/products/store',
      formData, // Enviamos el FormData
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'multipart/form-data' // Obligatorio para subir archivos
        }
      }
    )

    emit('created') 
    
    // Limpiar campos
    name.value = ''; description.value = ''; category_id.value = '';
    price.value = null; stock.value = null; type_stock.value = '';
    id_delivery_point.value = ''; imagen.value = null;
    
    alert('Producto creado correctamente')
    
  } catch (error) {
    console.error('Error al crear producto:', error.response?.data || error)
    alert('Error al guardar. Revisa la consola para ver el detalle.')
  }
}

onMounted(() => {
  cargarPuntosEntrega();
})
</script>

<template>
  <div class="contenedor-formulario">
    <form @submit.prevent="enviarDatos">

      <h3>Nuevo Producto</h3>

      <div class="campo">
        <label>Nombre del producto</label>
        <input type="text" v-model="name" placeholder="Ej. Manzanas Golden"/>
      </div>

      <div class="campo">
        <label>Descripción</label>
        <textarea v-model="description" placeholder="Detalles del producto..."></textarea>
      </div>

      <div class="fila">
        <div class="campo">
          <label>Precio (€)</label>
          <input type="number" step="0.01" v-model="price" placeholder="0.00"/>
        </div>
        
        <div class="campo">
          <label>Stock</label>
          <input type="number" v-model="stock" placeholder="0"/>
        </div>
      </div>

      <div class="fila">
        <div class="campo">
          <label>Unidad de medida</label>
          <select v-model="type_stock">
            <option disabled value="">Selecciona...</option>
            <option value="unidad">Unidad</option>
            <option value="kg">Kg</option>
          </select>
        </div>
        
        <div class="campo">
          <label>
             Categoría 
             <small v-if="props.categorias.length === 0" style="color:orange">(Cargando...)</small>
          </label>
          <select v-model="category_id">
            <option disabled value="">Selecciona categoría</option>
            <option 
              v-for="categoria in props.categorias" 
              :key="categoria.id_category" 
              :value="categoria.id_category"
            >
              {{ categoria.name }}
            </option>
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
          <label>Imagen</label>
          <input 
            type="file" 
            @change="subirImagen"
            accept="image/*"
          />
        </div>
      </div>

      <div class="botones">
        <button type="submit" class="btn-guardar">Agregar producto</button>
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

/* Ajuste para input file */
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
  transition: background-color 0.2s;
}

.btn-guardar:hover {
  background-color: #15422a;
}
</style>