<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import productosMock from './productos.json'
import categoriasMock from './categorias.json'

const router = useRouter()

const productos = ref([])
const categorias = ref([])

const obtenerDatos = async () => {
  try {
    /*
    const [resProductos, resCategorias] = await Promise.all([
      axios.get('http://localhost:8080/api/productos'),
      axios.get('http://localhost:8080/api/categorias')
    ])
    productos.value = resProductos.data
    categorias.value = resCategorias.data
    */
    productos.value = productosMock
    categorias.value = categoriasMock
  } catch (error) {
    console.error('Error cargando datos:', error)
  }
}

const productosConCategoria = computed(() => {
  return productos.value.map(producto => {
    const categoria = categorias.value.find(
      c => c.id === producto.category_id
    )

    return {
      ...producto,
      category_name: categoria ? categoria.name : 'Sin categoría'
    }
  })
})

const editarProducto = (producto) => {
  router.push(`/edit-product/${producto.id}`)
}

const eliminarProducto = async (id) => {
  if (!confirm('¿Seguro que quieres eliminar este producto?')) return

  try {
    await axios.delete(`http://localhost:8080/api/productos/${id}`)
    obtenerDatos()
  } catch (error) {
    console.error('Error eliminando producto:', error)
  }
}

onMounted(obtenerDatos)
</script>

<template>
<div id ="productos">
    <div id="contenedor">
        <div id ="menu">
            <div id="search">
                <img class="search" src="./assets/icons/search_icon.png" alt="Buscar"></img>
            </div>
            <div id ="filter">
                <img class="filter" src="./assets/icons/filter_icon" alt="Filtrar"></img>
                <p>Filters</p>
            </div>
            <div id="boton">
                <router-link to="/addProduct">+ Añadir producto</router-link>
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
            <tr v-for="producto in productos" :key="producto.id">
                <td>{{ producto.name }}</td>
                <td>{{ producto.category }}</td>
                <td id="price">{{ producto.price }}</td>
                <td id="stock">{{ producto.stock }}</td>
                <td>{{ producto.type_stock }}</td>
                <td id="status">{{ producto.state }}</td>
                <td>
                  <button @click="editarProducto(producto)"><img class="edit" src="./assets/icons/edit_icon.png" alt="Editar"></img></button>
                  <button @click="eliminarProducto(producto.id)"><img class="delete" src="./assets/icons/delete_icon.png" alt="Borrar"></img></button>
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
        text-decoration: none;
        border-radius: 20px;
        color: white;
    }
    #price{
        
    }
</style>