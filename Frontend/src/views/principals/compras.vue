<script setup>
import { ref, onMounted,computed  } from "vue";
import axios from "axios";
import Modal from "../../components/modals/Modal.vue";

const url = import.meta.env.VITE_API_URL;

const compras = ref([]);
const mostrarModal = ref(false);
const compraSeleccionada = ref(null);
const busqueda = ref('');
const filtroEstado = ref('');
const formatearFecha = (fecha) => {
  if (!fecha) return 'Pendiente';
  return new Date(fecha).toLocaleDateString();
};

const verDetalles = (compra) => {
  compraSeleccionada.value = compra;
  mostrarModal.value = true;
};

const obtenerCompras = async () => {
    try {
        const response = await axios.get(`${url}/api/sales/my-purchases`, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem("token")}`,
            },
        });
        compras.value = response.data;
        console.log("Datos cargados:", compras.value);
    } catch (error) {
        console.error("Error cargando las compras:", error);
    }
}

const rechazarCompra = async (compra) => {
  const url = `${url}/api/sales/update/${compra.id_sale}`;
  console.log(url);
  try {
    const payload = {
      'id_product': compra.product.id_product,
      'state': 'Rechazado',
      'quantity':compra.quantity
    };

    console.log(payload);
    const response = await axios.put(url, payload, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });

    console.log("Producto rechazado exitosamente");
    compra.state = 'Rechazado';
  } catch (error) {
    console.error("Error rechazando la venta:", error);
  }
}
const comprasFiltradas = computed(() => {
  return compras.value.filter(c => {
    const coincideNombre = c.product.name.toLowerCase().includes(busqueda.value.toLowerCase());
    const coincideEstado = !filtroEstado.value || c.state.toLowerCase() === filtroEstado.value.toLowerCase();
    return coincideNombre && coincideEstado;
  });
});

const claseEstado = (estado) => {
  if (!estado) return "";
  const st = estado.toLowerCase();
  
  if (st.includes("rechazado") || st.includes("rechazada")) return "estado-rechazado";
  if (st.includes("en curso")) return "estado-encurso";
  if (st.includes("aceptado") || st.includes("aceptada")) return "estado-aceptado";
  
  return "";
};
onMounted(obtenerCompras);
</script>

<template>
  <div id="contenedor-compras">
    <div id="menu_superior">
      <div id="busqueda">
  <img class="icono-ui" src="../../assets/icons/search_icon.png" alt="Buscar" />
  <input v-model="busqueda" type="text" placeholder="Buscar por producto..." />
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

    <div id="listaCompras">
      <table>
        <thead>
          <tr>
            <th>Producto</th>
            <th>Vendedor</th>
            <th>Fecha Compra</th>
            <th>Fecha Recogida</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="compra in comprasFiltradas" :key="compra.id_sale">
            <td data-label="Producto">{{ compra.product.name }}</td>

            <td data-label="Vendedor">
              {{ compra.seller ? compra.seller.name : "Desconocido" }}
            </td>

            <td data-label="Fecha Compra">
              {{ formatearFecha(compra.sale_date) }}
            </td>

            <td data-label="Fecha Recogida">
              {{ formatearFecha(compra.collection_date) }}
            </td>

            <td data-label="Total" id="precio">{{ compra.total }}€</td>

            <td data-label="Estado">
              <span :class="['estado', claseEstado(compra.state)]">
                {{ compra.state }}
              </span>
            </td>

            <td data-label="Acciones">
              <div class="acciones-wrapper">
                <button title="Ver Detalles" @click="verDetalles(compra)">
                  <img
                    class="action-icon"
                    src="../../assets/icons/verDetalles.png"
                    alt="Ver Detalles"
                  />
                </button>
                <div v-if="compra.state === 'En Curso'">
                  <button title="Rechazar Venta" @click="rechazarCompra(compra)">
                    <img
                      class="action-icon"
                      src="../../assets/icons/rechazar.png"
                      alt="Rechazar Venta"
                    />
                  </button>
                </div>
              </div>
            </td>
          </tr>

          <tr v-if="!comprasFiltradas || comprasFiltradas.length === 0">
            <td
              colspan="7"
              style="text-align: center; padding: 40px; color: #9ca3af"
            >
              No has realizado ninguna compra todavía.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <Modal v-model="mostrarModal">
      <div v-if="compraSeleccionada">
        <h3>Detalle de Compra #{{ compraSeleccionada.id_sale }}</h3>
        <p><strong>Producto:</strong> {{ compraSeleccionada.product.name }}</p>
        <p><strong>Total:</strong> {{ compraSeleccionada.total }}€</p>
      </div>
    </Modal>
  </div>
</template>

<style scoped>
#contenedor-compras {
  padding: 20px;
}

#menu_superior {
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
  border-radius: 500px;
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

.icono-ui {
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
  display: flex;
  align-items: center;
}

#filtro select {
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
  appearance: none;
  background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%231c5537%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
  background-size: 10px;
}

#filtro select:hover {
  background-color: #e5e7eb;
}

#filtro select:focus {
  background-color: #ffffff;
  border-color: #1c5537;
  box-shadow: 0 0 0 3px rgba(28, 85, 55, 0.1);
}

#listaCompras table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

#listaCompras th {
  text-align: left;
  padding: 16px;
  font-size: 13px;
  font-weight: 600;
  color: #6b7280;
  border-bottom: 1px solid #e5e7eb;
  background-color: #f9fafb;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

#listaCompras td {
  padding: 16px;
  border-bottom: 1px solid #f3f4f6;
  font-size: 14px;
  color: #374151;
  vertical-align: middle;
}

#listaCompras tr:last-child td {
  border-bottom: none;
}

.precio-celda {
  font-weight: 700;
  color: #111827;
  font-family: monospace;
  font-size: 1.05rem;
}

.empty-msg {
  text-align: center;
  padding: 40px;
  color: #9ca3af;
  font-style: italic;
}
.chip-estado {
  padding: 4px 12px;
  border-radius: 99px;
  font-size: 12px;
  font-weight: 600;
  display: inline-block;
  text-transform: capitalize;
}

.estado-rechazado {
  background-color: #fff7ed;
  color: #c2410c;
  border: 1px solid #ffedd5;
  border-radius: 10px;
  padding: 0.2em;
}

.estado-encurso {
  background-color: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #dbeafe;
  border-radius: 10px;
  padding: 0.2em;
}

.estado-aceptado {
  background-color: #f0fdf4;
  color: #15803d;
  border: 1px solid #dcfce7;
  border-radius: 10px;
  padding: 0.2em;
}

.acciones-wrapper {
  display: flex;
  gap: 8px;
  align-items: center;
}

td button {
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 6px;
  border-radius: 6px;
  transition: background 0.2s;
}

td button:hover {
  background-color: #f3f4f6;
}

.action-icon {
  width: 20px;
  height: 20px;
  opacity: 0.6;
}

td button:hover .action-icon {
  opacity: 1;
}

.btn-cerrar {
  padding: 8px 20px;
  background-color: #e5e7eb;
  color: #374151;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
}

.btn-cerrar:hover {
  background-color: #d1d5db;
}

@media (max-width: 768px) {
  #menu_superior {
    flex-direction: column;
    align-items: stretch;
  }
  
  #busqueda, #filtro select {
    width: 100%;
  }
  
  #listaCompras thead { display: none; }
  
  #listaCompras tr {
    display: block;
    margin-bottom: 16px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 12px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.02);
  }
  
  #listaCompras td {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #f3f4f6;
    text-align: right;
  }
  
  #listaCompras td::before {
    content: attr(data-label);
    font-weight: 600;
    color: #6b7280;
    font-size: 0.8rem;
    text-transform: uppercase;
  }
  
  .acciones-wrapper {
    justify-content: flex-end;
  }
}
</style>