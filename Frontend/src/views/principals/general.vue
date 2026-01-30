<script setup>
    import axios from 'axios';
    import { onMounted, ref, watch, computed } from 'vue';
    import { useRouter } from 'vue-router';

    const router = useRouter();
    const productos = ref([]);
    const categorias = ref([]);
    const vistaActual = ref('grid'); 

    const precioMin = ref(0)
    const precioMax = ref(100)
    const precioMaximo = 100 
    const busqueda = ref('')
    const categoriaSeleccionada = ref(null)
    const ordenSeleccionado = ref('')

    const productosFiltrados = computed(() => {
      const texto = busqueda.value.trim().toLowerCase()

      let resultado = [...productos.value].filter(producto => {
        const pasaBusqueda =
          !texto || producto.name.toLowerCase().includes(texto)

        const pasaCategoria =
          categoriaSeleccionada.value === null ||
          producto.category?.id_category === categoriaSeleccionada.value

        const pasaPrecio =
          producto.price >= precioMin.value &&
          producto.price <= precioMax.value

        return pasaBusqueda && pasaCategoria && pasaPrecio
      })

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
    
    watch(precioMin, (nuevoMin) => {
      if (nuevoMin > precioMax.value) {
        precioMin.value = precioMax.value
      }
    })

    watch(precioMax, (nuevoMax) => {
      if (nuevoMax < precioMin.value) {
        precioMax.value = precioMin.value
      }
    })

    const irAlDetalle = (id) => {
        router.push({ name: 'product-details', params: { id: id } });
    };

    const obtenerProductos = async () => {
        try {
            const resProductos = await axios.get("http://localhost:8080/api/products/", {
                withCredentials: true
            });

            productos.value = resProductos.data.data; 
            console.log("Productos cargados:", productos.value);
        } catch (error) {
            console.error("Error cargando los productos:", error);
        }
    }

    const obtenerCategorias = async () =>{
        try{
            const resCategorias = await axios.get("http://localhost:8080/api/categories/", {
                withCredentials: true
            });

            categorias.value = resCategorias.data;
            console.log("Categorias cargadas")
        } catch(error){
            console.error("Error cargando las categorias")
        }
    }

    onMounted(() =>{
        obtenerProductos();

        obtenerCategorias();
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
        <div id="selector">
            <div id="borde">
                <router-link to="/general"><button :class="{ 'activo': vistaActual === 'grid' }" @click="vistaActual = 'grid'">Productos</button></router-link>
                <router-link to="/mapa"><button :class="{ 'activo': vistaActual === 'map' }" @click="vistaActual = 'map'">Mapa</button></router-link>
            </div>
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
                      :max="precioMaximo"
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
                      min="0"
                      :max="precioMaximo"
                      v-model="precioMax"
                    />
                </div>
                <div id="rango-precios">
                  <span>{{ precioMin }}€</span>
                  <span>{{ precioMax }}€</span>
                </div>
            </div>
        </aside>

        <main>
            <div id="resultados">
                <span>{{ productosFiltrados.length }} Productos encontrados</span>
                <select v-model="ordenSeleccionado">
                  <option value="">Ordenar por</option>
                  <option value="precio-desc">Precio: mayor a menor</option>
                  <option value="precio-asc">Precio: menor a mayor</option>
                  <option value="nombre-asc">Nombre: A -> Z</option>
                  <option value="nombre-desc">Nombre: Z -> A</option>
                </select>
            </div>

            <div id="productos">
                <div 
                    v-for="producto in productosFiltrados" 
                    :key="producto.id_product" 
                    class="tarjeta-producto"
                    @click="irAlDetalle(producto.id_product)">

                    <div id="imagen-producto">
                        <img 
                            :src="`http://localhost:8080/storage/${producto.image}`" 
                            :alt="producto.nombre">
                    </div>

                    <div id="info-producto">
                        <div id="cabecera-info">
                            <h3>{{ producto.name }}</h3>
                            <span id="precio">{{ producto.price }}€ / {{ producto.type_stock }}</span>
                        </div>
                        
                        <p id="granja">{{ producto.delivery_point.name }}</p>

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
        </main>
    </div>
</template>

<style scoped>
* {
    text-decoration: none;
}

#cabecera {
    display: flex;
    justify-content: space-between;
    align-items: center;

    #titulos {
        padding: 2%;

        h1 {
            color: #143b27;
        }
    }

    #busqueda {
        display: flex;
        align-items: center;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 8px 15px;
        width: 300px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: box-shadow 0.3s ease;

        &:focus-within {
            border-color: #1a4d2e;
            box-shadow: 0 4px 8px rgba(26, 77, 46, 0.15);
        }

        input {
            border: none;
            outline: none;
            background: transparent;
            width: 100%;
            font-size: 1rem;
            color: #555;
            margin-left: 10px;
        }

        .search {
            width: 18px;
            height: 18px;
            opacity: 0.5;
        }
    }

    #selector {
        padding: 2%;
        display: flex;
        justify-content: center;

        #borde {
            display: flex;
            background-color: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 4px;
            width: fit-content;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);

            button {
                padding: 8px 20px;
                border: none;
                background-color: transparent;
                color: #555;
                border-radius: 6px;
                cursor: pointer;
                font-weight: 600;
                font-size: 0.95rem;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 8px;

                &:hover {
                    background-color: #f5f5f5;
                }

                &.activo {
                    background-color: #1c5537;
                    color: white;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                }
            }
        }
    }
}

#contenedor-principal {
    display: flex;
    align-items: flex-start;
    padding: 0 2%;
    gap: 2%;
    margin-top: 20px;

    .barraLateral {
        width: 250px;
        padding: 25px;
        background-color: #fff;
        border-radius: 12px;
        border: 1px solid #eee;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
        position: sticky;
        top: 20px;
        height: fit-content;

        #filtro-barraLateral {
            margin-bottom: 25px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;

            h3 {
                color: #1a4d2e;
                font-family: serif;
                font-size: 1.4rem;
                border-bottom: 2px solid #f0f0f0;
                padding-bottom: 10px;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                gap: 10px;
            }
        }

        .grupo-filtro {
            margin-bottom: 30px;

            h4 {
                font-size: 0.95rem;
                color: #333;
                margin-bottom: 15px;
                font-weight: 600;
            }

            p {
                font-size: 0.85rem;
                font-weight: bold;
                color: #1a4d2e;
                margin: 10px 0 5px 0;
            }

            select {
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

                &:hover {
                    border-color: #1a4d2e;
                }

                &:focus {
                    border-color: #1a4d2e;
                    box-shadow: 0 0 0 3px rgba(26, 77, 46, 0.1);
                }
            }

            .sliders_control {
                display: flex;
                flex-direction: column;
                gap: 10px;

                input[type="range"] {
                    width: 100%;
                    height: 6px;
                    background: #e0e0e0;
                    border-radius: 5px;
                    outline: none;
                    margin: 5px 0;

                    &::-webkit-slider-thumb {
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

                        &:hover {
                            transform: scale(1.1);
                        }
                    }

                    &::-moz-range-thumb {
                        width: 18px;
                        height: 18px;
                        border-radius: 50%;
                        background: #1a4d2e;
                        border: 2px solid #fff;
                        cursor: pointer;
                        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
                    }
                }
            }

            #rango-precios {
                display: flex;
                justify-content: space-between;
                margin-top: 5px;

                span {
                    font-size: 0.85rem;
                    color: #666;
                    background: #f5f5f5;
                    padding: 2px 8px;
                    border-radius: 4px;
                    font-weight: 500;
                }
            }
        }
    }

    main {
        flex-grow: 1;

        #resultados {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            color: #666;
            font-size: 0.95rem;
        }

        #productos {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;

            .tarjeta-producto {
                background-color: white;
                border: 1px solid #e0e0e0;
                border-radius: 12px;
                overflow: hidden;
                transition: transform 0.2s ease, box-shadow 0.2s ease;

                &:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
                }

                #imagen-producto {
                    height: 180px;
                    width: 100%;
                    background-color: #eee;

                    img {
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                    }
                }

                #info-producto {
                    padding: 15px;

                    #cabecera-info {
                        display: flex;
                        justify-content: space-between;
                        align-items: flex-start;
                        margin-bottom: 5px;

                        h3 {
                            margin: 0;
                            font-size: 1.1rem;
                            color: #1c5537;
                        }

                        #precio {
                            font-weight: bold;
                            color: #333;
                            font-size: 1rem;
                        }
                    }

                    #granja {
                        color: #888;
                        font-size: 0.9rem;
                        margin-bottom: 15px;
                    }

                    #footer-tarjeta {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-top: 10px;

                        #distancia {
                            font-size: 0.85rem;
                            color: #666;
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

                            &:hover {
                                background-color: #143b27;
                            }
                        }
                    }
                }
            }
        }
    }
}

@media (max-width: 1024px) {
    #contenedor-principal {
        main {
            #productos {
                grid-template-columns: 1fr 1fr !important;
            }
        }

        .barraLateral {
            width: 200px;
        }
    }
}

@media (max-width: 768px) {
    #cabecera {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }

    #contenedor-principal {
        flex-direction: column;
        padding: 0 15px;

        .barraLateral {
            width: 100%;
            position: static;
            margin: 0 0 20px 0;
            box-sizing: border-box;

            .grupo-filtro ul {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }
        }

        main {
            #resultados {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }

            #productos {
                grid-template-columns: 1fr !important;

                .tarjeta-producto {
                    #imagen-producto {
                        height: 200px;
                    }
                }
            }
        }
    }
}
</style>