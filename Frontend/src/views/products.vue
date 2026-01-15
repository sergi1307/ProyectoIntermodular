<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import productosMock from './productos.json'
import categoriasMock from './categorias.json'
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
    /*
    // Fem la petició al backend per a ovtindre els productes i les categories
    const [resProductos, resCategorias] = await Promise.all([
      axios.get('http://localhost:8080/api/productos'),
      axios.get('http://localhost:8080/api/categorias')
    ])
    // Li passem les dades del backend als arrays 
    productos.value = resProductos.data
    categorias.value = resCategorias.data
    */
    // De moment passem les dades a partir d'un JSON
    productos.value = productosMock
    categorias.value = categoriasMock
  } catch (error) {
    // Missatge d'error per si no carreguen les dades correctament
    console.error('Error cargando datos:', error)
  }
}

// Procedim a mesclar les dades dels productes i categories
const productosConCategoria = computed(() => {
  return productos.value.map(producto => {
    // Busquem el id de les categories i les agafem
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
  /*
  // Si acceptes anirà al backend i l'eliminara
  try {
    await axios.delete(`http://localhost:8080/api/productos/${id}`)
  } catch (error) {
    // I dona error si falla
    console.error('Error eliminando producto:', error)
  }
  */
  // Si l'accepta busca el producte pel seu id i l'esborra
  productos.value = productos.value.filter(
      producto => producto.id !== id
    )
}
const actualizarProducto = (productoActualizado) => {
  // Busca el producte pel seu id
  const index = productos.value.findIndex(
    p => p.id === productoActualizado.id
  )
  // Si és diferent d'un índex negatiu, li posa el número de l'índex del producte 
  if (index !== -1) {
    productos.value[index] = productoActualizado
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
        <p>Productos</p>
        <p>Ordenes?</p>
        <p>Mapas</p>
      </div>
        <div id ="menu_producto">
            <div id="search">
                <img class="search" src="../assets/icons/search_icon.png" alt="Buscar"></img>
            </div>
            <div id ="filter">
                <img class="filter" src="../assets/icons/filter_icon.png" alt="Filtrar"></img>
                <p>Filters</p>
            </div>
            <div id="boton">
              <!-- Botó d'afegir producte amb el component del formulari per a crear producte-->
              <button @click="openCrear = true">Añadir producto</button>
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
                <th>Categoria</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Tipo de Stock</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
            <!--Fem el bucle del producte mesclat amb les categories per a mostrar-los en la taula HTML-->
            <tr v-for="producto in productosConCategoria" :key="producto.id">
                <td>{{ producto.name }}</td>
                <td>{{ producto.category }}</td>
                <td id="price">{{ producto.price }}</td>
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
                  <button @click="eliminarProducto(producto.id)"><img class="delete" src="../assets/icons/delete_icon.png" alt="Borrar"></img></button>
                </td>
            </tr>
            </table>
        </div>
    </div>
</div>
</template>
<style scoped>
    #boton{
        background-color: #1c5537;
        border-radius: 20px;
    }
    #price{
        
    }
</style>