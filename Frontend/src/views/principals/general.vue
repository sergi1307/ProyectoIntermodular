<script setup>
import axios from 'axios';
import { onMounted, ref, computed, watch } from 'vue';
import { useRouter } from 'vue-router';
import starRating from "../../components/ratings/starRating.vue";
import mapaPuntosdeventa from "../../components/maps/mapaPuntosdeventa.vue";

const url = import.meta.env.VITE_API_URL_DEV;

const router = useRouter();
const productos = ref([]);
const categorias = ref([]); 

const precioMin = ref(0)
const precioMax = ref(500)
const precioMaximo = 500 
const busqueda = ref('')
const categoriaSeleccionada = ref(null)
const ordenSeleccionado = ref('')

// Variables para productos del delivery point seleccionado
const puntoSeleccionado = ref(null)
const productosDelPunto = ref([])
const cargandoProductosPunto = ref(false)

// Variables para filtro de proximidad
const ubicacionUsuario = ref(null)
const distanciaMax = ref(500) // km - por defecto 500 para mostrar todo
const distanciaMaxima = 500 // km máximo
const filtroProximidadActivo = ref(false) // Solo se activa cuando usuario mueve el slider

// --- COMPUTED: PRODUCTOS PARA GRID (búsqueda + precio + orden) ---
const productosParaGrid = computed(() => {
  const texto = busqueda.value.trim().toLowerCase()
  
  let resultado = [...productos.value].filter(producto => {
    const pasaBusqueda = !texto || producto.name.toLowerCase().includes(texto)
    const pasaPrecio = producto.price >= precioMin.value && producto.price <= precioMax.value
    const pasaCategoria = categoriaSeleccionada.value === null || producto.category?.id_category === categoriaSeleccionada.value
    
    // Filtro de proximidad - SOLO si está activo Y hay ubicación
    let pasaProximidad = true
    if (filtroProximidadActivo.value && ubicacionUsuario.value && producto.delivery_point) {
      const distancia = calcularDistancia(
        ubicacionUsuario.value.lat,
        ubicacionUsuario.value.lng,
        producto.delivery_point.latitude,
        producto.delivery_point['length']
      )
      pasaProximidad = distancia <= distanciaMax.value
    }

    return pasaBusqueda && pasaPrecio && pasaCategoria && pasaProximidad
  })
  
  // Aplicar ordenación
  switch (ordenSeleccionado.value) {
    case 'precio-asc':
      resultado.sort((a, b) => a.price - b.price)
      break
    case 'precio-desc':
      resultado.sort((a, b) => b.price - a.price)
      break
    case 'nombre-asc':
      resultado.sort((a, b) => a.name.localeCompare(b.name))
      break
    case 'nombre-desc':
      resultado.sort((a, b) => b.name.localeCompare(a.name))
      break
  }
  
  return resultado
})

// --- COMPUTED: PRODUCTOS FILTRADOS (para mapa: búsqueda + categoría + precio + proximidad) ---
const productosFiltrados = computed(() => {
  const texto = busqueda.value.trim().toLowerCase()

  let resultado = [...productos.value].filter(producto => {
    const pasaBusqueda = !texto || producto.name.toLowerCase().includes(texto)
    const pasaCategoria = categoriaSeleccionada.value === null || producto.category?.id_category === categoriaSeleccionada.value
    const pasaPrecio = producto.price >= precioMin.value && producto.price <= precioMax.value
    
    // Filtro de proximidad - SOLO si está activo Y hay ubicación
    let pasaProximidad = true
    if (filtroProximidadActivo.value && ubicacionUsuario.value && producto.delivery_point) {
      const distancia = calcularDistancia(
        ubicacionUsuario.value.lat,
        ubicacionUsuario.value.lng,
        producto.delivery_point.latitude,
        producto.delivery_point['length']
      )
      pasaProximidad = distancia <= distanciaMax.value
    }
    // Si el filtro NO está activo o NO hay ubicación, se muestran todos
    
    return pasaBusqueda && pasaCategoria && pasaPrecio && pasaProximidad
  })

  return resultado
})

// --- COMPUTED: TIENDAS FILTRADAS ---
const tiendasFiltradas = computed(() => {
  const tiendasUnicas = new Map();
  
  productosFiltrados.value.forEach(producto => {
    const dp = producto.delivery_point;
    if (dp && !tiendasUnicas.has(dp.id_delivery_point)) {
      tiendasUnicas.set(dp.id_delivery_point, {
        id: dp.id_delivery_point,
        name: dp.name,
        direction: dp.direction,
        latitude: parseFloat(dp.latitude),
        length: parseFloat(dp.length)
      });
    }
  });
  
  return Array.from(tiendasUnicas.values());
})

// --- WATCHERS DE RANGO ---
watch(precioMin, (nuevoMin) => { if (nuevoMin > precioMax.value) precioMin.value = precioMax.value })
watch(precioMax, (nuevoMax) => { if (nuevoMax < precioMin.value) precioMax.value = precioMin.value })

// Watcer para activar filtro de proximidad cuando usuario mueve el slider
watch(distanciaMax, () => {
  if (ubicacionUsuario.value) {
    filtroProximidadActivo.value = true
  }
})

// --- IR A DETALLE ---
const irAlDetalle = (id) => {
    router.push({ name: 'product-details', params: { id: id } });
};

// --- OBTENER UBICACIÓN DEL USUARIO ---
const obtenerUbicacion = () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                ubicacionUsuario.value = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
            },
            (error) => {
                console.error('Error obteniendo ubicación:', error);
                // Si falla, el filtro de proximidad simplemente no se aplica
            }
        );
    }
};

// --- CALCULAR DISTANCIA (Haversine) ---
const calcularDistancia = (lat1, lon1, lat2, lon2) => {
    const R = 6371; // Radio de la Tierra en km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
};

// --- OBTENER PRODUCTOS ---
const obtenerProductos = async () => {
    try {
        const resProductos = await axios.get(`${url}/api/products/`, {
            withCredentials: true
        });

        // Inicializar mediaRating y totalReviews para cada producto
        productos.value = resProductos.data.data.map(p => ({
            ...p,
            mediaRating: 0,
            totalReviews: 0
        }));

        // Obtener media de reviews por producto
        productos.value.forEach(async (p) => {
            try {
                const res = await axios.get(`${url}/api/reviews/producto/${p.id_product}/media`);
                p.mediaRating = res.data.average ?? 0;
                p.totalReviews = res.data.total_reviews ?? 0;
            } catch (err) {
                console.log(`Error al obtener media de producto ${p.id_product}`);
                p.mediaRating = 0;
                p.totalReviews = 0;
            }
        });

    } catch (error) {
        console.error("Error cargando los productos:", error);
    }
}

// --- OBTENER CATEGORIAS ---
const obtenerCategorias = async () =>{
    try{
        const resCategorias = await axios.get(`${url}/api/categories/`, {
            withCredentials: true
        });
        categorias.value = resCategorias.data;
    } catch(error){
        console.error("Error cargando las categorias")
    }
}

// --- MOSTRAR PRODUCTOS DEL PUNTO SELECCIONADO ---
const mostrarProductosDelPunto = async (punto) => {
    puntoSeleccionado.value = punto;
    cargandoProductosPunto.value = true;
    productosDelPunto.value = [];

    try {
        const response = await axios.get(`${url}/api/delivery_points/${punto.id}/products`);
        productosDelPunto.value = response.data;
    } catch (error) {
        console.error('Error al cargar productos del punto:', error);
        alert('No se pudieron cargar los productos de este punto de venta');
    } finally {
        cargandoProductosPunto.value = false;
    }
}

const cerrarProductosDelPunto = () => {
    puntoSeleccionado.value = null;
    productosDelPunto.value = [];
}

onMounted(() =>{
    obtenerProductos();
    obtenerCategorias();
    obtenerUbicacion();
});
</script>


<template>
    <div id="cabecera">
        <div id="titulos">
            <h1>Descubre Productos Locales</h1> 
            <p>Productos Frescos de Granjas Cercanas a Ti</p>
        </div>
        <div id="busqueda">
                <img
                    class="search"
                    src="../../assets/icons/search_icon.png"
                    alt="Buscar"
                />
                <input v-model="busqueda"
                    type="text"
                    placeholder="Buscar...">
                </input>
            </div>
    </div>

    <div id="contenedor-principal">

        <aside class="barraLateral">
            <div id="filtro-barraLateral"><h3>Filtros</h3></div>
            <div class="grupo-filtro">
                <h4>Categoría</h4>
                <select v-model="categoriaSeleccionada">
            <option :value="null">Todas</option>
            <option 
              v-for="categoria in categorias" 
              :key="categoria.id_category" 
              :value="categoria.id_category"
            >
              {{ categoria.name }}
            </option>
          </select>
            </div>
            <h4>Rango de Precios</h4>
            <p>Mínimo</p>
            <div class="grupo-filtro">
                <div class="sliders_control">
                    <input
                      type="range"
                      min="0"
                      :max="precioMax"
                      v-model="precioMin"
                    />
                    <div id="rango-precios">
                      <span>{{ precioMin }}€</span>
                      <span>{{ precioMax }}€</span>
                    </div>
                    <div class="form_control">
                    </div>
                    <p>Máximo</p>
                    <input
                      type="range"
                      :min="precioMin"
                      :max="precioMaximo"
                      v-model="precioMax"
                    />
                </div>
                <div id="rango-precios">
                  <span>{{ precioMin }}€</span>
                  <span>{{ precioMax }}€</span>
                </div>
            </div>

            <h4>Proximidad</h4>
            <p>Distancia máxima: {{ distanciaMax }} km</p>
            <div class="grupo-filtro">
                <div class="sliders_control">
                    <input
                      type="range"
                      min="2"
                      :max="distanciaMaxima"
                      step="2"
                      v-model="distanciaMax"
                    />
                </div>
                <div id="rango-precios">
                  <span>2 km</span>
                  <span>500 km</span>
                </div>
            </div>
        </aside>

        <main>
            <div id="resultados">
                <span>{{ productosParaGrid.length }} Productos encontrados</span>
                <select id="seleccionar" v-model="ordenSeleccionado">
                  <option value="">Ordenar por</option>
                  <option value="precio-desc">Precio: mayor a menor</option>
                  <option value="precio-asc">Precio: menor a mayor</option>
                  <option value="nombre-asc">Nombre: A -> Z</option>
                  <option value="nombre-desc">Nombre: Z -> A</option>
                </select>
            </div>

            <div id="productos">
                <div 
                    v-for="producto in productosParaGrid" 
                    :key="producto.id_product" 
                    class="tarjeta-producto"
                    @click="irAlDetalle(producto.id_product)">

                    <div id="imagen-producto">
                        <img 
                            :src="`${url}/storage/${producto.image}`" 
                            :alt="producto.nombre">
                    </div>

                    <div id="info-producto">
                        <div id="cabecera-info">
                            <h3>{{ producto.name }}</h3>
                            <span id="precio">{{ producto.price }}€ / {{ producto.type_stock }}</span>
                        </div>
                        
                        <p id="granja">{{ producto.delivery_point.name }}</p>
                        <div class="rating">
                            <span class="rating-number">{{ producto.mediaRating.toFixed(2) }}</span>
                            <starRating :modelValue="producto.mediaRating" readonly />
                        </div>

                        <div id="footer-tarjeta">
                            <span id="distancia" @click.stop>
                                <a :href="`https://maps.google.com/?q=${producto.delivery_point.latitude},${producto.delivery_point.length}`" 
                                    target="_blank">
                                    📍 Ver Mapa
                                </a>
                            </span>

                            <button id="añadir" @click.stop>+</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección del mapa debajo de productos -->
            <div v-if="tiendasFiltradas.length > 0" class="seccion-mapa">
                <h2>Puntos de Venta</h2>
                <p class="descripcion-mapa">Haz click en un marcador para ver sus productos</p>
                <mapa-puntosdeventa 
                    :puntos="tiendasFiltradas"
                    titulo=""
                    map-id="mapa-productos-general"
                    @punto-seleccionado="mostrarProductosDelPunto"
                />
            </div>

            <!-- Sección de productos del punto seleccionado -->
            <div v-if="puntoSeleccionado" class="seccion-productos-punto">
                <div class="cabecera-seccion">
                    <h3>Productos en {{ puntoSeleccionado.name }}</h3>
                    <button @click="cerrarProductosDelPunto" class="btn-cerrar">✕</button>
                </div>

                <div v-if="cargandoProductosPunto" class="cargando">
                    Cargando productos...
                </div>

                <div v-else-if="productosDelPunto.length === 0" class="sin-productos">
                    No hay productos disponibles en este punto de venta.
                </div>

                <div v-else class="grid-productos-punto">
                    <router-link
                        v-for="producto in productosDelPunto"
                        :key="producto.id_product"
                        :to="`/product-details/${producto.id_product}`"
                        class="tarjeta-producto-mini"
                    >
                        <div class="imagen-mini">
                            <img :src="`${url}/storage/${producto.image}`" :alt="producto.name" />
                        </div>
                        <div class="info-mini">
                            <h4>{{ producto.name }}</h4>
                            <p class="precio-mini">{{ producto.price }}€ / {{ producto.type_stock }}</p>
                            <p class="categoria-mini">{{ producto.category?.name }}</p>
                        </div>
                    </router-link>
                </div>
            </div>
        </main>
    </div>
</template>

<style scoped>
* {
    text-decoration: none;
    box-sizing: border-box;
}

#cabecera {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 2%;
    flex-wrap: wrap;
    gap: 15px;
}

#titulos {
    padding: 2%;
}

#titulos h1 {
    color: #143b27;
    margin: 0;
    font-size: 1.8rem;
}

#titulos p {
    margin: 5px 0 0 0;
    color: #666;
}

#busqueda {
    display: flex;
    align-items: center;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 8px 15px;
    width: 300px;
    max-width: 100%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: box-shadow 0.3s ease;
}

#busqueda:focus-within {
    border-color: #1a4d2e;
    box-shadow: 0 4px 8px rgba(26, 77, 46, 0.15);
}

#busqueda input {
    border: none;
    outline: none;
    background: transparent;
    width: 100%;
    font-size: 1rem;
    color: #555;
    margin-left: 10px;
}

#busqueda .search {
    width: 18px;
    height: 18px;
    opacity: 0.5;
}

#contenedor-principal {
    display: flex;
    align-items: flex-start;
    padding: 0 2%;
    gap: 2%;
    margin-top: 20px;
}

.barraLateral {
    width: 280px;
    padding: 25px;
    background-color: #fff;
    border-radius: 12px;
    border: 1px solid #eee;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
    position: sticky;
    top: 20px;
    height: fit-content;
    flex-shrink: 0;
}

#filtro-barraLateral {
    margin-bottom: 25px;
    padding-bottom: 10px;
}

#filtro-barraLateral h3 {
    color: #1a4d2e;
    font-family: serif;
    font-size: 1.4rem;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.grupo-filtro {
    margin-bottom: 30px;
}

.grupo-filtro h4 {
    font-size: 0.95rem;
    color: #333;
    margin-bottom: 15px;
    font-weight: 600;
}

.grupo-filtro p {
    font-size: 0.85rem;
    font-weight: bold;
    color: #1a4d2e;
    margin: 10px 0 5px 0;
}

.grupo-filtro select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    background-color: #fcfcfc;
    color: #333;
    font-size: 0.95rem;
    cursor: pointer;
    outline: none;
    transition: all 0.2s;
}

.grupo-filtro select:hover {
    border-color: #1a4d2e;
}

.grupo-filtro select:focus {
    border-color: #1a4d2e;
    box-shadow: 0 0 0 3px rgba(26, 77, 46, 0.1);
}

.sliders_control {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.sliders_control input[type="range"] {
    width: 100%;
    height: 6px;
    background: #e0e0e0;
    border-radius: 5px;
    outline: none;
    margin: 5px 0;
    -webkit-appearance: none; 
}

.sliders_control input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #1a4d2e;
    border: 2px solid #fff;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    transition: transform 0.1s;
}

.sliders_control input[type="range"]::-webkit-slider-thumb:hover {
    transform: scale(1.1);
}

.sliders_control input[type="range"]::-moz-range-thumb {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #1a4d2e;
    border: 2px solid #fff;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
}

#rango-precios {
    display: flex;
    justify-content: space-between;
    margin-top: 5px;
}

#rango-precios span {
    font-size: 0.85rem;
    color: #666;
    background: #f5f5f5;
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: 500;
}

main {
    flex-grow: 1;
    width: 100%; 
}

#resultados {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
    color: #666;
    font-size: 0.95rem;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

#seleccionar {
    padding: 0.5rem 1rem;
    background-color: rgba(156, 156, 156, 0.1);
    border: 1px solid #ddd;
    border-radius: 500px;
    font-weight: bold;
    font-size: 0.9em;
    cursor: pointer;
}

#productos {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
}

.tarjeta-producto {
    background-color: white;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    cursor: pointer;
    display: flex;
    flex-direction: column;
}

.tarjeta-producto:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
}

#imagen-producto {
    height: 180px;
    width: 100%;
    background-color: #eee;
}

#imagen-producto img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

#info-producto {
    padding: 15px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

#cabecera-info {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 5px;
}

#cabecera-info h3 {
    margin: 0;
    font-size: 1.1rem;
    color: #1c5537;
    line-height: 1.2;
}

#cabecera-info #precio {
    font-weight: bold;
    color: #333;
    font-size: 1rem;
    white-space: nowrap;
    margin-left: 10px;
}

#granja {
    color: #888;
    font-size: 0.9rem;
    margin: 0 0 15px 0;
}

.rating {
    display: flex;
    align-items: center;
    gap: 4px;        
    font-size: 0.95rem;
    margin-bottom: auto; 
}

.rating-number {
    font-size: 1rem;
    color: #555;    
    font-weight: 500;
}

#footer-tarjeta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 15px;
}

#distancia {
    font-size: 0.85rem;
    color: #666;
}

#distancia a {
    color: inherit;
    text-decoration: none;
}

#añadir {
    background-color: #1c5537;
    color: white;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    font-size: 1.2rem;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: background-color 0.2s;
    padding: 0;
}

#añadir:hover {
    background-color: #143b27;
}

.seccion-mapa {
    margin-top: 40px;
    padding: 30px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    width: 100%;
    box-sizing: border-box;
}

.seccion-mapa h2 {
    color: #1a4d2e;
    font-size: 1.8rem;
    margin: 0 0 10px 0;
}

.descripcion-mapa {
    color: #666;
    margin-bottom: 20px;
    font-size: 1rem;
}

.seccion-productos-punto {
    margin-top: 30px;
    padding: 25px;
    background: #f9fafb;
    border-radius: 12px;
    border: 2px solid #1a4d2e;
}

.cabecera-seccion {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.cabecera-seccion h3 {
    color: #1a4d2e;
    font-size: 1.5rem;
    margin: 0;
}

.btn-cerrar {
    background: #dc3545;
    color: white;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    font-size: 1.2rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}

.btn-cerrar:hover {
    background: #c82333;
}

.cargando, .sin-productos {
    text-align: center;
    padding: 40px;
    color: #666;
    font-size: 1.1rem;
}

.grid-productos-punto {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
}

.tarjeta-producto-mini {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
    text-decoration: none;
    color: inherit;
    display: block;
    border: 1px solid #eee;
}

.tarjeta-producto-mini:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
}

.imagen-mini {
    height: 120px;
    width: 100%;
    background: #eee;
}

.imagen-mini img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.info-mini {
    padding: 12px;
}

.info-mini h4 {
    font-size: 1rem;
    color: #1c5537;
    margin: 0 0 5px 0;
}

.precio-mini {
    font-weight: bold;
    color: #333;
    margin: 5px 0;
}

.categoria-mini {
    font-size: 0.85rem;
    color: #888;
    margin: 0;
}


@media (max-width: 1024px) {
    #productos {
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    }

    .barraLateral {
        width: 240px;
    }
}

@media (max-width: 768px) {
    #cabecera {
        flex-direction: column;
        text-align: center;
    }

    #busqueda {
        width: 100%;
    }

    #contenedor-principal {
        flex-direction: column;
        padding: 0 15px;
    }

    .barraLateral {
        width: 100%;
        position: static;
        margin-bottom: 20px;
    }

    #resultados {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }

    #seleccionar {
        width: 100%;
    }

    #productos {
        grid-template-columns: 1fr; 
    }

    .tarjeta-producto {
        flex-direction: row; 
        height: auto;
    }

    .tarjeta-producto #imagen-producto {
        width: 120px;
        height: auto;
        flex-shrink: 0;
    }

    .tarjeta-producto #info-producto {
        padding: 10px;
    }

    .seccion-mapa {
        padding: 15px;
    }

    .cabecera-seccion {
        flex-direction: row;
        align-items: center;
    }

    .cabecera-seccion h3 {
        font-size: 1.2rem;
    }

    .grid-productos-punto {
        grid-template-columns: 1fr 1fr; 
    }
}
</style>