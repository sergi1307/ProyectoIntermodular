<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";
// Ajusta estas rutas según donde guardes este archivo
import createProduct from "../forms/createProductForm.vue";
import editProduct from "../forms/editProductForm.vue";
import BaseModal from "../modals/Modal.vue";

// --- TOTA LA LÒGICA DE PRODUCTES VE ACÍ ---
const productos = ref([]);
const categorias = ref([]);
const openCrear = ref(false);
const openEditar = ref(false);
const productoSeleccionado = ref(null);
const busqueda = ref('');
const filtroCategoria = ref('');
const filtroEstado = ref('');
const obtenerDatos = async () => {
  // 1. OBTENEMOS EL USUARIO Y EL TOKEN DEL LOCALSTORAGE
  const userStr = localStorage.getItem('user');
  const token = localStorage.getItem('token');

  // Si no hay usuario o token, no podemos cargar sus productos
  if (!userStr || !token) {
    console.error("No hay sesión iniciada.");
    return;
  }

  const user = JSON.parse(userStr);
  const userId = user.id_user; // Cogemos el ID

  try {
    const [resProductos, resCategorias] = await Promise.all([
      // 2. CORRECCIÓN AQUÍ:
      // - Usamos comillas invertidas (`) para meter la variable ${userId}
      // - Añadimos el token en el header por seguridad
      axios.get(`http://localhost:8080/api/products/mine?id_user=${userId}`, {
        headers: { Authorization: `Bearer ${token}` }
      }),
      axios.get("http://localhost:8080/api/categories"),
    ]);

    // Corrección para asegurar que sea un array
    const data = resProductos.data;
    productos.value = Array.isArray(data) ? data : (data.data || []);
    
    categorias.value = resCategorias.data;
  } catch (error) {
    console.error("Error cargando datos:", error);
    productos.value = [];
  }
};

const productosConCategoria = computed(() => {
  // Protección anti-crash
  if (!productos.value || !Array.isArray(productos.value)) {
    return [];
  }

  return productos.value.map((producto) => {
    const categoria = categorias.value.find(
      (c) => c.id === producto.category_id,
    );
    return {
      ...producto,
      category: categoria ? categoria.name : "Sin categoría",
    };
  });
});


const agregarProducto = async () => {
  await obtenerDatos();
  openCrear.value = false;
};

const eliminarProducto = async (id) => {
  if (!window.confirm("¿Seguro que quieres eliminar este producto?")) return;
  try {
    // Añadimos también el token al borrar por si acaso
    await axios.delete(`http://localhost:8080/api/products/destroy/${id}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    await obtenerDatos(); // Recarreguem la llista després d'eliminar
  } catch (error) {
    console.error("Error eliminando producto:", error);
    alert("No se pudo eliminar el producto.");
  }
};

const actualizarProducto = (datosNuevos) => {
  obtenerDatos();

  openEditar.value = false;
};

onMounted(obtenerDatos);
</script>

<template>
  <div>
    <div id="menu_producto">
      <div id="search">
        <img class="search" src="../../assets/icons/search_icon.png" alt="Buscar" />
        <input 
          v-model="busqueda" 
          type="text" 
          placeholder="Buscar por nombre..." 
          class="input-busqueda"
        />
      </div>
      <div id="filter">
        <img
          class="filter"
          src="../../assets/icons/filter_icon.png"
          alt="Filtrar"
        />
        <p>Filters</p>
      </div>
      <div id="boton">
        <button @click="openCrear = true">+ Añadir producto</button>
        <BaseModal v-model="openCrear">
          <createProduct :categorias="categorias" @created="agregarProducto" />
          <template #footer>
            <button @click="openCrear = false">Cancelar</button>
          </template>
        </BaseModal>
      </div>
    </div>

    <div id="listaProductos">
      <table>
        <thead>
          <tr>
            <th>Producto</th>
            <th>Descripción</th>
            <th>Categoria</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Tipo</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="producto in productosConCategoria"
            :key="producto.id_product"
          >
            <td>{{ producto.name }}</td>
            <td>{{ producto.description }}</td>
            <td>{{ producto.category }}</td>
            <td class="price">{{ producto.price }}€</td>
            <td class="stock">{{ producto.stock }}</td>
            <td>{{ producto.type_stock }}</td>

            <td class="status-cell" :class="'status-' + producto.state.toLowerCase()">
              {{ producto.state }}
            </td>

            <td>
              <button
                @click="
                  productoSeleccionado = producto;
                  openEditar = true;
                "
              >
                <img
                  class="delete"
                  src="../../assets/icons/edit_icon.png"
                  alt="Editar"
                />
              </button>
              <button @click="eliminarProducto(producto.id_product)">
                <img
                  class="delete"
                  src="../../assets/icons/delete_icon.png"
                  alt="Borrar"
                />
              </button>
            </td>
          </tr>

          <tr v-if="productosConCategoria.length === 0">
            <td
              colspan="8"
               style="text-align: center; padding: 20px; color: #999">
              No hay productos disponibles
            </td>
          </tr>
        </tbody>
      </table>

      <BaseModal v-model="openEditar">
        <editProduct
          v-if="productoSeleccionado"
          :producto="productoSeleccionado"
          @updated="actualizarProducto"
        />
        <template #footer>
          <button @click="openEditar = false">Cancelar</button>
        </template>
      </BaseModal>
    </div>
  </div>
</template>

<style scoped>
#menu_producto {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 24px;
}
#search,
#filter {
  background: #f3f4f6;
  border-radius: 999px;
  padding: 10px 14px;
  display: flex;
  align-items: center;
}
#search img,
#filter img {
  width: 18px;
  opacity: 0.6;
}
#filter {
  gap: 8px;
  cursor: pointer;
  font-weight: 500;
  color: #374151;
}
#boton button {
  background-color: #1c5537;
  padding: 12px 20px;
  border-radius: 999px;
  font-weight: 600;
  color: white;
  cursor: pointer;
  border: none;
}
#boton button:hover {
  background-color: #16432b;
}

#listaProductos table {
  width: 100%;
  border-collapse: collapse;
}
#listaProductos th {
  text-align: left;
  padding: 14px;
  font-size: 13px;
  color: #6b7280;
  border-bottom: 1px solid #e5e7eb;
}
#listaProductos td {
  padding: 16px 14px;
  border-bottom: 1px solid #f1f5f9;
  font-size: 14px;
  color: #374151;
}

.price {
  font-weight: 700;
  color: #111827;
}

.status-cell {
  font-weight: 600;
  text-transform: capitalize; 
}


.status-cell::before {
  content: "";
  display: inline-block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  margin-right: 8px;
  background: #ccc; 
}

.status-disponible::before {
  background: #22c55e;
}

.status-reservado::before {
  background: #eab308;
}

.status-agotado::before {
  background: #ef4444; 
}

td button {
  background: transparent;
  border: none;
  cursor: pointer;
  margin-right: 6px;
}
td img {
  width: 18px;
  opacity: 0.7;
}

@media (max-width: 1024px) {
  #listaProductos th:nth-child(2), 
  #listaProductos td:nth-child(2) {
    display: none;
  }
}

@media (max-width: 768px) {

  #menu_producto {
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
  }

  #search, #filter, #boton, #boton button {
    width: 100%;
    justify-content: center;
    box-sizing: border-box;
  }

  #listaProductos {
    overflow-x: auto;
  }

  #listaProductos th, 
  #listaProductos td {
    padding: 10px 5px;
    font-size: 13px;
  }

  #listaProductos th:nth-child(2), #listaProductos td:nth-child(2),
  #listaProductos th:nth-child(3), #listaProductos td:nth-child(3),
  #listaProductos th:nth-child(5), #listaProductos td:nth-child(5),
  #listaProductos th:nth-child(6), #listaProductos td:nth-child(6) {
    display: none;
  }

  #listaProductos td:last-child {
    white-space: nowrap;
    text-align: right;
  }
}
</style>