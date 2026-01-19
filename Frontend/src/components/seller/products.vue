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

const obtenerDatos = async () => {
  try {
    const [resProductos, resCategorias] = await Promise.all([
      axios.get("http://localhost:8080/api/products"),
      axios.get("http://localhost:8080/api/categories"),
    ]);
    productos.value = resProductos.data;
    categorias.value = resCategorias.data;
  } catch (error) {
    console.error("Error cargando datos:", error);
  }
};

const productosConCategoria = computed(() => {
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
    await axios.delete(`http://localhost:8080/api/products/destroy/${id}`);
    await obtenerDatos(); // Recarreguem la llista després d'eliminar
  } catch (error) {
    console.error("Error eliminando producto:", error);
  }
};

const actualizarProducto = (productoActualizado) => {
  const index = productos.value.findIndex(
    (p) => p.id_product === productoActualizado.id_product,
  );
  if (index !== -1) {
    productos.value[index] = {
      ...productos.value[index],
      ...productoActualizado,
      category_id: productoActualizado.category_id,
    };
  }
  openEditar.value = false;
};

onMounted(obtenerDatos);
</script>

<template>
  <div>
    <div id="menu_producto">
      <div id="search">
        <img
          class="search"
          src="../../assets/icons/search_icon.png"
          alt="Buscar"
        />
        <p>Buscar</p>
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
            <td id="price">{{ producto.price }}€</td>
            <td id="stock">{{ producto.stock }}</td>
            <td>{{ producto.type_stock }}</td>
            <td id="status">{{ producto.state }}</td>
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

          <tr v-if="productos.length === 0">
            <td
              colspan="8"
              style="text-align: center; padding: 20px; color: #999"
            >
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
/* Aquí pegas SOLO los estilos de la tabla, botones y buscador */
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
#price {
  font-weight: 700;
  color: #111827;
}
#status {
  font-weight: 600;
}
#status::before {
  content: "";
  display: inline-block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #22c55e;
  margin-right: 8px;
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
</style>
