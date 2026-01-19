<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const ventas = ref([]);

// Función para obtener las ventas del backend
const obtenerVentas = async () => {
  try {
    const response = await axios.get("http://localhost:8080/api/sales", {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });
    ventas.value = response.data;
  } catch (error) {
    console.error("Error cargando ventas:", error);
  }
};

// Función para dar color según el estado
const claseEstado = (estado) => {
  if (estado === "Pendiente") return "estado-pendiente";
  if (estado === "En Curso") return "estado-encurso";
  if (estado === "Terminado") return "estado-terminado";
  return "";
};

onMounted(obtenerVentas);
</script>

<template>
  <div>
    <div id="menu_producto">
      <div id="busqueda">
        <img
          class="busqueda"
          src="../../assets/icons/search_icon.png"
          alt="Buscar"
        />
        <p>Buscar orden...</p>
      </div>
      <div id="filtro">
        <img
          class="filtro"
          src="../../assets/icons/filter_icon.png"
          alt="Filtrar"
        />
        <p>filtros</p>
      </div>
    </div>

    <div id="listaProductos">
      <table>
        <thead>
          <tr>
            <th>Producto</th>
            <th>Comprador</th>
            <th>Fecha Venta</th>
            <th>Fecha Recogida</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="venta in ventas" :key="venta.id_sale">
            <td>{{ venta.id_product }}</td>

            <td>{{ venta.id_buyer }}</td>

            <td>{{ formatearFecha(venta.sale_date) }}</td>
            <td>{{ formatearFecha(venta.collection_date) }}</td>

            <td id="precio">{{ venta.total }}€</td>

            <td>
              <span :class="['estado', claseEstado(venta.state)]">
                {{ venta.state }}
              </span>
            </td>

            <td>
              <button title="Ver detalles">
                <img
                  class="action-icon"
                  src="../../assets/icons/edit_icon.png"
                  alt="Ver"
                />
              </button>
            </td>
          </tr>

          <tr v-if="ventas.length === 0">
            <td
              colspan="8"
              style="text-align: center; padding: 20px; color: #999"
            >
              No hay órdenes disponibles
            </td>
          </tr>
        </tbody>
      </table>
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
#busqueda,
#filtro {
  background: #f3f4f6;
  border-radius: 999px;
  padding: 10px 14px;
  display: flex;
  align-items: center;
}
#busqueda img,
#filtro img {
  width: 18px;
  opacity: 0.6;
}
#filtro {
  gap: 8px;
  cursor: pointer;
  font-weight: 500;
  color: #374151;
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

#precio {
  font-weight: 700;
  color: #111827;
}
.id-text {
  font-family: monospace;
  color: #6b7280;
}

td button {
  background: transparent;
  border: none;
  cursor: pointer;
  margin-right: 6px;
}
.action-icon {
  width: 18px;
  opacity: 0.7;
}

.estado {
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
  display: inline-block;
}

.estado-pendiente {
  background-color: #fff7ed;
  color: #c2410c;
  border: 1px solid #ffedd5;
}

.estado-encurso {
  background-color: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #dbeafe;
}

.estado-terminado {
  background-color: #f0fdf4;
  color: #15803d;
  border: 1px solid #dcfce7;
}
</style>
