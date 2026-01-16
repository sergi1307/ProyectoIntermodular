<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import createProduct from '../components/createProductForm.vue';
import editProduct from '../components/editProductForm.vue';
import BaseModal from '../components/Modal.vue'

const router = useRouter()
// Obtenim un array dels objectes
const productos = ref([])
const categorias = ref([])
// Fem objectes per a diferenciar si anem a:
// afegir 
// o
const openCrear = ref(false)
// editar un producte
const openEditar = ref(false)
// Definim l'emissió per a l'actualització
const emit = defineEmits(['updated'])
// Definim el producteSeleccionat a null perquè no hem seleccionat res
const productoSeleccionado = ref(null)

const obtenerDatos = async () => {
  try {
    
    // Fem la petició al backend per a obtindre els productes i les categories
    const [resProductos, resCategorias] = await Promise.all([
      axios.get('http://localhost:8080/api/products'),
      axios.get('http://localhost:8080/api/categories')
    ])
    // Li passem les dades del backend als arrays 
    productos.value = resProductos.data
    categorias.value = resCategorias.data
  } catch (error) {
    // Missatge d'error per si no carreguen les dades correctament
    console.error('Error cargando datos:', error)
  }
}

// Procedim a mesclar les dades dels productes i categories
const productosConCategoria = computed(() => {
  return productos.value.map(producto => {
    const categoria = categorias.value.find(
      c => c.id === producto.category_id
    )
    return {
      /*
      Per últim afegim al producte l'objecte de "categoria":
      producto{
        id
        name
        price
        stock
        type_stock
        state
        category_id{
          name
        }
      }
      */
      ...producto,
      // I ací agafem la categoria, i si no té li posem la etiqueta de "Sin categoría"
      category: categoria ? categoria.name : 'Sin categoría'
    }
  })
})

const agregarProducto = (nuevoProducto) => {
  // Afegim el producte a la llista
  productos.value.push(nuevoProducto)
  // I tanquem la finestra de crear el producte
  openCrear.value = false
}

// Funció per a eliminar els productes
const eliminarProducto = async (id) => {
  // Primerament et mostrara una alerta si estàs segur d'eliminar el producte
  if (!window.confirm('¿Seguro que quieres eliminar este producto?')) return
  
  // Si acceptes anirà al backend i l'eliminara
  try {
    console.log(id) // antes del delete
    await axios.delete(`http://localhost:8080/api/products/destroy/${id}`)
  } catch (error) {
    // I dona error si falla
    console.error('Error eliminando producto:', error)
  }
}
const actualizarProducto = (productoActualizado) => {
  // Busca el producte pel seu id
  const index = productos.value.findIndex(
    p => p.id_product === productoActualizado.id_product
  )

  // Si és diferent d'un índex negatiu, li posa el número de l'índex del producte
  if (index !== -1) {
    // Forçem que detecten els canvis
    productos.value[index] = {
      ...productos.value[index],
      ...productoActualizado,
      category_id: productoActualizado.category_id
    }
  }
  // Si no ho és, no l'edita
  openEditar.value = false
}

// A l'executar farà la funció "obtenerDatos"
onMounted(obtenerDatos)
</script>

<template>
<div id ="productos">
  
    <div id="contenedor">
      <div id="menu">
        <ul>
          <li><a href="#">Productos</a></li>
          <li><a href="#">Ordenes</a></li>
          <li><a href="#">Mapas</a></li>
        </ul>
      </div>
        <div id ="menu_producto">
            <div id="search">
                <img class="search" src="../assets/icons/search_icon.png" alt="Buscar"></img>
                <p>Buscar</p>
            </div>
            <div id ="filter">
                <img class="filter" src="../assets/icons/filter_icon.png" alt="Filtrar"></img>
                <p>Filters</p>
            </div>
            <div id="boton">
              <!-- Botó d'afegir producte amb el component del formulari per a crear producte-->
              <button @click="openCrear = true">+ Añadir producto</button>
              <BaseModal v-model="openCrear">
                <createProduct
                  :categorias="categorias"
                  @created="agregarProducto"
                />
                <!--Ací n'hi ha un botó que referència al footer del modal amb el boto "Cancelar" per a tancar el modal-->
                <template #footer>
                  <button @click="openCrear = false">Cancelar</button>
                </template>
              </BaseModal>
            </div>
        </div>
        <div id="listaProductos">
            <table>
            <tr>
                <th>Producto</th>
                <th>Descripción</th>
                <th>Categoria</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Tipo de Stock</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
            <!--Fem el bucle del producte mesclat amb les categories per a mostrar-los en la taula HTML-->
            <tr v-for="producto in productosConCategoria" :key="id_product">
                <td>{{ producto.name }}</td>
                <td>{{ producto.description }}</td>
                <td>{{ producto.category }}</td>
                <td id="price">{{ producto.price }}€</td>
                <td id="stock">{{ producto.stock }}</td>
                <td>{{ producto.type_stock }}</td>
                <td id="status">{{ producto.state }}</td>
                <td>
                  <!-- Botó d'editar producte amb el component del formulari per a editar producte-->
                  <button @click="productoSeleccionado = producto; openEditar = true">
                    <img class="delete" src="../assets/icons/edit_icon.png" alt="Editar"></img>
                  </button>
                  <BaseModal v-model="openEditar">
                    <editProduct
                      :producto="productoSeleccionado"
                      @updated="actualizarProducto"
                    />
                    <!--Ací n'hi ha un botó que referència al footer del modal amb el boto "Cancelar" per a tancar el modal-->
                    <template #footer>
                      <button @click="openEditar = false">Cancelar</button>
                    </template>
                  </BaseModal>
                  <!--Botó d'eliminar producte-->
                  <button @click="eliminarProducto(producto.id_product)"><img class="delete" src="../assets/icons/delete_icon.png" alt="Borrar"></img></button>
                </td>
            </tr>
            </table>
        </div>
    </div>
</div>
</template>
<style scoped>
  /* CONTENEDOR GENERAL */
#productos {
  background-color: #f6f8f7;
  min-height: 100vh;
  padding: 24px;
  font-family: 'Inter', system-ui, sans-serif;
}

/* CAJA PRINCIPAL */
#contenedor {
  background: white;
  border-radius: 14px;
  padding: 24px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

/* MENU SUPERIOR */
#menu ul {
  display: flex;
  gap: 32px;
  border-bottom: 2px solid #e5e7eb;
  padding-bottom: 12px;
  margin-bottom: 24px;
}

#menu li {
  list-style: none;
}

#menu a {
  text-decoration: none;
  font-weight: 600;
  color: #6b7280;
}

#menu a:hover {
  color: #1c5537;
}

/* BARRA DE ACCIONES */
#menu_producto {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 24px;
}

/* SEARCH */
#search {
  background: #f3f4f6;
  border-radius: 999px;
  padding: 10px 14px;
  display: flex;
  align-items: center;
}

#search img {
  width: 18px;
  opacity: 0.6;
}

/* FILTER */
#filter {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #f3f4f6;
  border-radius: 999px;
  padding: 10px 16px;
  font-weight: 500;
  color: #374151;
  cursor: pointer;
}

#filter img {
  width: 16px;
}

/* BOTON AÑADIR */
#boton button {
  background-color: #1c5537;
  padding: 12px 20px;
  border-radius: 999px;
  font-weight: 600;
  color: white;
  cursor: pointer;
  transition: background 0.2s ease;
}

#boton button:hover {
  background-color: #16432b;
}

/* TABLA */
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

/* PRECIO */
#price {
  font-weight: 700;
  color: #111827;
}

/* STOCK */
#stock {
  font-weight: 500;
}

/* ESTADO */
#status {
  font-weight: 600;
}

#status::before {
  content: '';
  display: inline-block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #22c55e;
  margin-right: 8px;
}

/* ACCIONES */
td button {
  background: transparent;
  border: none;
  cursor: pointer;
  margin-right: 6px;
}

td img {
  width: 18px;
  opacity: 0.7;
  transition: opacity 0.2s ease;
}

td img:hover {
  opacity: 1;
}

/* MODAL FOOTER BUTTON */
template button {
  background: #e5e7eb;
  color: #374151;
  padding: 10px 16px;
  border-radius: 8px;
  font-weight: 500;
}

template button:hover {
  background: #d1d5db;
}

</style>