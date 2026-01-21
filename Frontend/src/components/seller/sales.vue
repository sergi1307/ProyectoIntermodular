<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import Modal from "../modals/Modal.vue";

const ventas = ref([]);
const mostrarModal = ref(false);
const ventaSeleccionada = ref(null);

// Función para obtener las ventas del backend
const obtenerVentas = async () => {
  console.log(localStorage.getItem("token"));
  try {
    const response = await axios.get("http://localhost:8080/api/sales/my-orders", {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });
    console.log(response);
    ventas.value = response.data;
  } catch (error) {
    console.error("Error cargando ventas:", error);
  }
};

const rechazarVenta = async (ventas) => {
  const url = `http://localhost:8080/api/sales/update/${ventas.id_sale}`;

  try {
    const userString = localStorage.getItem('user');
    const user = userString ? JSON.parse(userString) : null;

    const idUser = user?.id_user;

    const payload = {
      'state': 'Rechazada'
    };

    const response = await axios.put(url, payload, {
      headers : {
        Authorization : `Bearer ${localStorage.getItem("token")}`,
      },
    });

    console.log("Producto Rechazado Éxitosamente:", response.data);

    ventas.state='Rechazada';
  } catch (error) {
    console.error("Error al rechazar la venta:", error);
  }
}

const aceptarVenta = async (venta) => {
  const url = `http://localhost:8080/api/sales/update/${venta.id_sale}`;

  try {
    const userString = localStorage.getItem("user");
    const user = userString ? JSON.parse(userString) : null;

    const idUser = user?.id_user;

    const payload = {
      'state': 'Aceptada'
    };

    const response = await axios.put(url, payload, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });

    console.log("Producto Aceptado Éxitosament:", response.data);

    venta.state = 'Aceptada';
    venta.collection_date = response.data.sale.collection_date;
  } catch (error) {
    console.error("Error al rechazar la venta:", error);
  }
}

const formatearFecha = (fecha) => {
  if (!fecha) return 'Pendiente';
  return new Date(fecha).toLocaleDateString();
}

// Función para dar color según el estado
const claseEstado = (estado) => {
  if (estado === "Rechazada") return "estado-rechazado";
  if (estado === "En Curso") return "estado-encurso";
  if (estado === "Aceptada") return "estado-aceptado";
  return "";
};

const verDetalles = (venta) => {
  ventaSeleccionada.value = venta;
  mostrarModal.value = true;
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
            <td>{{ venta.product.name }}</td>

            <td>{{ venta.buyer.name }}</td>

            <td>{{ formatearFecha(venta.sale_date) }}</td>
            <td>{{ formatearFecha(venta.collection_date) }}</td>

            <td id="precio">{{ venta.total }}€</td>

            <td>
              <span :class="['estado', claseEstado(venta.state)]">
                {{ venta.state }}
              </span>
            </td>

            <td>
              <div  v-if="venta.state === 'En Curso'">
                <button title="Rechazar Venta" @click="rechazarVenta(venta)">
                  <img
                    class="action-icon"
                    src="../../assets/icons/rechazar.png"
                    alt="Rechazar"
                  />
                </button>
                <button title="Aceptar Venta" @click="aceptarVenta(venta)">
                  <img
                    class="action-icon"
                    src="../../assets/icons/aceptar.png"
                    alt="Aceptar"
                  />
                </button>
              </div>
              <div v-else>
                <button title="Ver Detalles" @click="verDetalles(venta)">
                  <img
                    class="action-icon"
                    src="../../assets/icons/verDetalles.png"
                    alt="Ver Detalles"
                  />
                </button>
              </div>
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
    
    <Modal v-model="mostrarModal">
      
      <div v-if="ventaSeleccionada">
        <h3>Detalles de la Orden #{{ ventaSeleccionada.id_sale }}</h3>
        
        <div class="info-grid">
          <p><strong>Producto:</strong> {{ ventaSeleccionada.product.name }}</p>
          <p><strong>Comprador:</strong> {{ ventaSeleccionada.buyer.name }}</p>
          <p><strong>Fecha Venta:</strong> {{ formatearFecha(ventaSeleccionada.sale_date) }}</p>
          <p><strong>Fecha Recogida:</strong> {{ formatearFecha(ventaSeleccionada.collection_date) }}</p>
          <p><strong>Lugar Recogida:</strong> {{ ventaSeleccionada.delivery_point.direction }}</p>
          <p><strong>Precio Unidad:</strong> {{ ventaSeleccionada.product.price }}</p>
          <p><strong>Total:</strong> {{ ventaSeleccionada.total }}€</p>
          <p><strong>Estado actual:</strong> {{ ventaSeleccionada.state }}</p>
        </div>
      </div>

      <template #footer>
        <div style="display: flex; justify-content: flex-end; gap: 10px; padding: 10px;">
           <button 
             @click="mostrarModal = false"
             style="padding: 8px 16px; cursor: pointer;"
           >
             Cerrar
           </button>
        </div>
      </template>
    </Modal>
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
  text-align: center;
  padding: 14px;
  font-size: 13px;
  color: #6b7280;
  border-bottom: 1px solid #e5e7eb;
}
#listaProductos td {
  text-align: center;
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
  width: 26px;
  opacity: 0.7;
}

.estado {
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
  display: inline-block;
}

.estado-rechazado {
  background-color: #fff7ed;
  color: #c2410c;
  border: 1px solid #ffedd5;
}

.estado-encurso {
  background-color: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #dbeafe;
}

.estado-aceptado {
  background-color: #f0fdf4;
  color: #15803d;
  border: 1px solid #dcfce7;
}
</style>
