<script setup>
import { ref, onMounted,computed  } from "vue";
import axios from "axios";
// Asegúrate de que la ruta al Modal sea correcta
import Modal from "../../components/modals/Modal.vue";

const compras = ref([]);
const mostrarModal = ref(false);
const compraSeleccionada = ref(null); // Corregido nombre variable singular
const busqueda = ref('');
const filtroEstado = ref('');
// 1. Funciones auxiliares necesarias para que el template no falle
const formatearFecha = (fecha) => {
  if (!fecha) return 'Pendiente';
  return new Date(fecha).toLocaleDateString();
};

const claseEstado = (estado) => {
  if (estado === "Rechazado") return "estado-rechazado";
  if (estado === "En Curso") return "estado-encurso";
  if (estado === "Aceptado") return "estado-aceptado";
  return "";
};

const verDetalles = (compra) => {
  compraSeleccionada.value = compra;
  mostrarModal.value = true;
};

const obtenerCompras = async () => {
    try {
        const response = await axios.get("http://localhost:8080/api/sales/my-purchases", {
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
  const url = `http://localhost:8080/api/sales/update/${compra.id_sale}`;
  console.log(url);
  try {
    const payload = {
      'state': 'Rechazado'
    };

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
/* ... (Tus estilos anteriores) ... */
</style>

<style scoped>
#contenedor-compras {
  padding: 2%;
}

#menu_superior {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 24px;
}

#busqueda,
#filtro {
  background: #f3f4f6;
  border-radius: 999px;
  padding: 10px 18px;
  display: flex;
  align-items: center;
  transition: background 0.2s;
}

#busqueda:hover,
#filtro:hover {
  background: #e5e7eb;
}

.icono-ui {
  width: 18px;
  opacity: 0.6;
  margin-right: 8px;
}

#filtro {
  cursor: pointer;
  font-weight: 600;
  color: #374151;
  font-size: 14px;
}

#busqueda p {
  margin: 0;
  color: #6b7280;
  font-size: 14px;
}

#filtro p {
  margin: 0;
}

/* =========================================
   Tabla de Datos
   ========================================= */
#listaCompras table {
  width: 100%;
  border-collapse: collapse;
}

#listaCompras th {
  text-align: center;
  padding: 16px 14px;
  font-size: 13px;
  font-weight: 600;
  color: #6b7280;
  border-bottom: 1px solid #e5e7eb;
  letter-spacing: 0.03em;
  text-transform: uppercase;
}

#listaCompras td {
  text-align: center;
  padding: 20px 14px;
  border-bottom: 1px solid #f1f5f9;
  font-size: 14px;
  color: #374151;
  vertical-align: middle;
}

/* Hover suave en las filas para desktop */
#listaCompras tbody tr:hover {
  background-color: #f9fafb;
}

#precio {
  font-weight: 700;
  color: #111827;
  font-size: 15px;
}

/* =========================================
   Botones y Acciones
   ========================================= */
td button {
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 4px;
  border-radius: 8px;
  transition: background 0.2s;
}

td button:hover {
  background-color: #f3f4f6;
}

.action-icon {
  width: 24px;
  height: 24px;
  opacity: 0.7;
  display: block;
}

/* =========================================
   Badges de Estado (Chips)
   ========================================= */
.estado {
  padding: 6px 12px;
  border-radius: 99px; /* Pill shape completo */
  font-size: 12px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 80px;
}

.estado-rechazado,
.estado-Rechazado {
  background-color: #fff7ed;
  color: #c2410c;
  border: 1px solid #ffedd5;
}

.estado-encurso,
.estado-EnCurso {
  background-color: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #dbeafe;
}

.estado-aceptado,
.estado-Aceptado {
  background-color: #f0fdf4;
  color: #15803d;
  border: 1px solid #dcfce7;
}

/* =========================================
   Media Queries (Responsividad)
   ========================================= */

/* Tablet: Ocultar columnas menos relevantes */
@media (max-width: 1024px) {
  #listaCompras th:nth-child(2), #listaCompras td:nth-child(2), /* Vendedor */
  #listaCompras th:nth-child(3), #listaCompras td:nth-child(3)  /* Fecha Compra */ {
    display: none;
  }
}

/* Mobile: Transformación a Tarjetas */
@media (max-width: 768px) {
  #menu_superior {
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
  }

  #busqueda,
  #filtro {
    width: 100%;
    justify-content: center;
    box-sizing: border-box;
  }

  /* Romper la estructura de tabla */
  #listaCompras table,
  #listaCompras thead,
  #listaCompras tbody,
  #listaCompras th,
  #listaCompras td,
  #listaCompras tr {
    display: block;
  }

  /* Ocultar cabeceras de tabla visualmente */
  #listaCompras thead tr {
    position: absolute;
    top: -9999px;
    left: -9999px;
  }

  /* Estilo de la Tarjeta */
  #listaCompras tr {
    margin-bottom: 20px;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    background: white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    padding: 16px;
  }

  /* Estilo de las filas dentro de la tarjeta */
  #listaCompras td {
    border: none;
    border-bottom: 1px solid #f3f4f6;
    position: relative;
    padding-left: 0;
    padding-right: 0;
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: right;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  /* Última fila sin borde */
  #listaCompras td:last-child {
    border-bottom: none;
    padding-top: 16px;
    padding-bottom: 0;
    justify-content: flex-end;
  }

  /* Uso de data-label para los títulos */
  #listaCompras td::before {
    content: attr(data-label);
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
    font-size: 11px;
    text-align: left;
    margin-right: auto; /* Empuja el contenido a la derecha */
  }

  /* Ajustes específicos para elementos dentro de tarjetas */
  .acciones-wrapper {
    display: flex;
    gap: 15px;
    width: 100%;
    justify-content: flex-end;
  }

  #precio {
    font-size: 1.25em;
    color: #1c5537; /* Color principal de la marca para destacar */
  }

  .action-icon {
    width: 28px;
    height: 28px;
  }
}
</style>
