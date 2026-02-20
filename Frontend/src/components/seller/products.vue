<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import { useNotificaciones } from '@/utilidades/useNotificaciones';

const notificacion = useNotificaciones();

import url from '../../config/api';

import createProduct from "../forms/createProductForm.vue";
import editProduct from "../forms/editProductForm.vue";
import BaseModal from "../modals/Modal.vue";

const productos = ref([]);
const categorias = ref([]);
const openCrear = ref(false);
const openEditar = ref(false);
const productoSeleccionado = ref(null);
const busqueda = ref("");
const filtroCategoria = ref("");
const filtroEstado = ref("");
const obtenerDatos = async () => {
  const userStr = localStorage.getItem("user");
  const token = localStorage.getItem("token");

  if (!userStr || !token) {
    console.error("No hay sesión iniciada.");
    return;
  }

  const user = JSON.parse(userStr);
  const userId = user.id_user;

  try {
    const [resProductos, resCategorias] = await Promise.all([
      axios.get(`${url}/api/products/mine?id_user=${userId}`, {
        headers: { Authorization: `Bearer ${token}` },
      }),
      axios.get(`${url}/api/categories`),
    ]);

    const data = resProductos.data;
    productos.value = Array.isArray(data) ? data : data.data || [];

    categorias.value = resCategorias.data;
  } catch (error) {
    console.error("Error cargando datos:", error);
    productos.value = [];
  }
};

const productosConCategoria = computed(() => {
  if (!productos.value || !Array.isArray(productos.value)) {
    return [];
  }

  return productos.value.map((producto) => {
    return {
      ...producto,
      category: producto.category?.name || "Sin categoría",
    };
  });
});
const productosFiltrados = computed(() => {
  return productosConCategoria.value.filter((p) => {
    const coincideNombre = p.name
      .toLowerCase()
      .includes(busqueda.value.toLowerCase());
    const coincideCategoria =
      !filtroCategoria.value || p.category === filtroCategoria.value;
    const coincideEstado =
      !filtroEstado.value ||
      p.state.toLowerCase() === filtroEstado.value.toLowerCase();
    return coincideNombre && coincideCategoria && coincideEstado;
  });
});

const agregarProducto = async () => {
  await obtenerDatos();
  openCrear.value = false;
};

const eliminarProducto = (id) => {
  notificacion.confirmar("¿Seguro que quieres eliminar este producto?", async () => {
    try {
      await axios.delete(`${url}/api/products/destroy/${id}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
      });
      await obtenerDatos();
      notificacion.exito("Producto eliminado correctamente");
    } catch (error) {
      console.error("Error eliminando producto:", error);
      notificacion.error("No se pudo eliminar el producto.");
    }
  });
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
        <img
          class="search"
          src="../../assets/icons/search_icon.png"
          alt="Buscar"
        />
        <input
          v-model="busqueda"
          type="text"
          placeholder="Buscar por nombre..."
          class="input-busqueda"
        />
      </div>
      <div id="filter">
        <select v-model="filtroCategoria">
          <option value="">Todas las categorías</option>
          <option v-for="cat in categorias" :key="cat.id" :value="cat.name">
            {{ cat.name }}
          </option>
        </select>

        <select v-model="filtroEstado">
          <option value="">Todos los estados</option>
          <option value="disponible">Disponible</option>
          <option value="agotado">Agotado</option>
        </select>
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
          <tr v-for="producto in productosFiltrados" :key="producto.id_product">
            <td>{{ producto.name }}</td>
            <td>{{ producto.description }}</td>
            <td>{{ producto.category }}</td>
            <td class="price">{{ producto.price }}€</td>
            <td class="stock">{{ producto.stock }}</td>
            <td>{{ producto.type_stock }}</td>

            <td
              class="status-cell"
              :class="'status-' + producto.state.toLowerCase()"
            >
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
#menu_producto {
  background-color: #ffffff;
  padding: 12px 16px;
  border-radius: 16px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 24px;
  flex-wrap: wrap;
}

#search {
  flex: 1;
  min-width: 250px;
  background-color: #f3f4f6;
  border-radius: 500px;
  padding: 8px 18px;
  display: flex;
  align-items: center;
  border: 1px solid transparent;
  transition: all 0.3s ease;
}

#search:focus-within {
  background-color: #ffffff;
  border-color: #1c5537;
  box-shadow: 0 0 0 3px rgba(28, 85, 55, 0.1);
}

#search img.search {
  width: 18px;
  opacity: 0.5;
  margin-right: 10px;
}

#search input {
  border: none;
  background: transparent;
  width: 100%;
  outline: none;
  font-size: 14px;
  color: #374151;
}

#filter {
  background: transparent;
  padding: 0;
  border: none;
  display: flex;
  gap: 10px;
  align-items: center;
}

#filter select {
  background-color: #f3f4f6;
  border: 1px solid transparent;
  padding: 10px 34px 10px 18px;
  border-radius: 999px;
  font-size: 14px;
  color: #374151;
  font-weight: 500;
  cursor: pointer;
  outline: none;
  transition: all 0.2s ease;
}

#filter select:hover {
  background-color: #e5e7eb;
}

#filter select:focus {
  background-color: #ffffff;
  border-color: #1c5537;
  box-shadow: 0 0 0 3px rgba(28, 85, 55, 0.1);
}

#boton button {
  background-color: #1c5537;
  padding: 12px 20px;
  border-radius: 999px;
  font-weight: 600;
  color: white;
  cursor: pointer;
  border: none;
  white-space: nowrap;
}

#boton button:hover {
  background-color: #16432b;
}

#listaProductos {
  width: 100%;
  background-color: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  overflow-x: auto; 
}

#listaProductos table {
  width: 100%;
  border-collapse: collapse;
  min-width: 600px; 
}

#listaProductos th {
  text-align: left;
  padding: 14px;
  font-size: 13px;
  color: #6b7280;
  border-bottom: 1px solid #e5e7eb;
  background-color: #f9fafb;
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

.status-agotado::before {
  background: #ef4444;
}

td button {
  background: transparent;
  border: none;
  cursor: pointer;
  margin-right: 6px;
  padding: 4px;
  border-radius: 4px;
  transition: background 0.2s;
}

td button:hover {
  background-color: #f3f4f6;
}

td img {
  width: 18px;
  opacity: 0.7;
}

@media (max-width: 768px) {
  #menu_producto {
    flex-direction: column;
    align-items: stretch;
    padding: 16px;
  }

  #search {
    width: 100%;
  }

  #filter {
    display: grid;
    grid-template-columns: 1fr 1fr;
    width: 100%;
    gap: 10px;
  }

  #filter select {
    width: 100%;
    margin: 0;
  }

  #boton button {
    width: 100%;
  }
  #listaProductos th:nth-child(2), 
  #listaProductos td:nth-child(2),
  #listaProductos th:nth-child(6),
  #listaProductos td:nth-child(6) {
    display: none;
  }

  #listaProductos th, 
  #listaProductos td {
    padding: 12px 8px;
  }
}
</style>
