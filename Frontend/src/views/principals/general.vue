<script setup>
    import axios from 'axios';
    import { onMounted, ref } from 'vue';
    import { useRouter } from 'vue-router';

    const router = useRouter();
    const productos = ref([]);
    const vistaActual = ref('grid'); 

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

    onMounted(obtenerProductos);
</script>

<template>
    <div id="cabecera">
        <div id="titulos">
            <h1>Descubre Productos Locales</h1>
            <p>Productos Frescos de Granjas Cercanas a Ti</p>
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
                <ul>
                    <li>Todas</li>
                    <li>Verduras</li>
                    <li>Frutas</li>
                </ul>
            </div>
            <div class="grupo-filtro">
                <h4>Rango de Precios</h4>
                <input type="range">
                <div id="rango-precios"><span>$0</span><span>$20</span></div>
            </div>
        </aside>

        <main>
            <div id="resultados">
                <span>{{ productos.length }} Productos encontrados</span>
                <select>
                    <option>Ordenar por: Cercanía</option>
                </select>
            </div>

            <div id="productos">
                <div 
                    v-for="producto in productos" 
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
                            <h3>{{ producto.nombre }}</h3>
                            <span id="precio">${{ producto.price }} / {{ producto.type_stock }}</span>
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
    *{
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
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);

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
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }
            }
        }
    }

    .barraLateral {
        width: 250px;
        padding: 20px;
        background-color: white;
        border-radius: 12px;
        border: 1px solid #e0e0e0;
        
        position: sticky;
        top: 20px;
        height: fit-content;
        margin: 2%;
        
        #filtro-barraLateral {
            margin-bottom: 25px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;

            h3 {
                color: #1c5537;
                font-size: 1.2rem;
                margin: 0;
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

            ul {
                list-style: none;
                padding: 0;
                margin: 0;

                li {
                    padding: 10px 12px;
                    cursor: pointer;
                    color: #666;
                    border-radius: 8px;
                    transition: background-color 0.2s, color 0.2s;
                    font-size: 0.9rem;

                    &:hover {
                        background-color: #f0f0f0;
                        color: #1c5537;
                    }
                }
            }

            input[type="range"] {
                width: 100%;
                cursor: pointer;
                accent-color: #1c5537;
            }

            #rango-precios {
                display: flex;
                justify-content: space-between;
                margin-top: 5px;
                
                span {
                    font-size: 0.85rem;
                    color: #888;
                    font-weight: 500;
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
        }
    }

    .tarjeta-producto {
        background-color: white;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;

        &:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
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

    @media (max-width: 1024px) {
    #productos {
        grid-template-columns: 1fr 1fr !important;
    }

    .barraLateral {
        width: 200px;
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
    }

    .barraLateral {
        width: 100%;
        position: static;
        margin: 0 0 20px 0;
        box-sizing: border-box;
    }

    .grupo-filtro ul {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    #resultados {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }

    #productos {
        grid-template-columns: 1fr !important;
    }

    .tarjeta-producto #imagen-producto {
        height: 200px;
    }
}
</style>