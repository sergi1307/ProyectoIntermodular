<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import Modal from "../modals/Modal.vue";

const ventas = ref([]);
const mostrarModal = ref(false);
const ventaSeleccionada = ref(null);
const busqueda = ref('');
const filtroEstado = ref('');
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
    const payload = {
      'state': 'Rechazado',
      
    };

    const response = await axios.put(url, payload, {
      headers : {
        Authorization : `Bearer ${localStorage.getItem("token")}`,
      },
    });

    console.log("Producto Rechazado Éxitosamente:", response.data);

    ventas.state='Rechazado';
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
      'state': 'Aceptado'
    };

    const response = await axios.put(url, payload, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });

    console.log("Producto Aceptado Éxitosament:", response.data);

    venta.state = 'Aceptado';
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
const ventasFiltradas = computed(() => {
  return ventas.value.filter(v => {
    const coincideNombre = v.product.name.toLowerCase().includes(busqueda.value.toLowerCase());
    const coincideEstado = !filtroEstado.value || v.state.toLowerCase() === filtroEstado.value.toLowerCase();
    return coincideNombre && coincideEstado;
  });
});
onMounted(obtenerVentas);
</script>

<template>
  <div>
    <div id="menu_producto">
      <div id="busqueda">
        <img class="busqueda" src="../../assets/icons/search_icon.png" alt="Buscar" />
        <input 
          v-model="busqueda" 
          type="text" 
          placeholder="Buscar por producto..." 
        />
      </div>
      <div id="filtro">
        <select v-model="filtroEstado">
          <option value="">Todos los estados</option>
          <option value="en curso">En Curso</option>
          <option value="aceptado">Aceptado</option>
          <option value="rechazado">Rechazado</option>
        </select>
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
          <tr v-for="venta in ventasFiltradas" :key="venta.id_sale">
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

#busqueda {
  flex: 1;
  min-width: 250px;
  background-color: #f3f4f6;
  border-radius: 999px;
  padding: 8px 18px;
  display: flex;
  align-items: center;
  border: 1px solid transparent;
  transition: all 0.3s ease;
}

#busqueda:focus-within {
  background-color: #ffffff;
  border-color: #1c5537;
  box-shadow: 0 0 0 3px rgba(28, 85, 55, 0.1);
}

#busqueda img.busqueda {
  width: 18px;
  opacity: 0.5;
  margin-right: 10px;
}

#busqueda input {
  border: none;
  background: transparent;
  width: 100%;
  outline: none;
  font-size: 14px;
  color: #374151;
}

#filtro {
  background: transparent; 
  padding: 0;
  border: none;
  display: flex;
  align-items: center;
}

#filtro select {
  appearance: none;
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

#filtro select:hover {
  background-color: #e5e7eb;
}

#filtro select:focus {
  background-color: #ffffff;
  border-color: #1c5537;
  box-shadow: 0 0 0 3px rgba(28, 85, 55, 0.1);
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

@media (max-width: 1024px) {
  #listaProductos th:nth-child(2), #listaProductos td:nth-child(2),
  #listaProductos th:nth-child(3), #listaProductos td:nth-child(3)
  {
    display: none;
  }
}

@media (max-width: 768px) {
  
  #menu_producto {
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
  }

  #busqueda, #filtro {
    width: 100%;
    justify-content: center;
    box-sizing: border-box;
  }

  #listaProductos table, 
  #listaProductos thead, 
  #listaProductos tbody, 
  #listaProductos th, 
  #listaProductos td, 
  #listaProductos tr {
    display: block;
  }

  #listaProductos thead tr {
    position: absolute;
    top: -9999px;
    left: -9999px;
  }

  #listaProductos tr {
    margin-bottom: 20px;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    background: white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    padding: 15px;
  }

  #listaProductos td {
    border: none;
    border-bottom: 1px solid #f3f4f6;
    position: relative;
    padding-left: 50%;
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: right;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  #listaProductos td:last-child {
    border-bottom: none;
    padding-top: 20px;
    justify-content: flex-end;
  }

  #listaProductos td::before {
    content: attr(data-label);
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
    font-size: 11px;
    text-align: left;
  }

  .action-icon {
    width: 32px;
    height: 32px;
  }

  .acciones-wrapper {
    display: flex;
    gap: 20px;
  }

  #precio {
    font-size: 1.2em;
    color: #1c5537;
  }
}
</style>
