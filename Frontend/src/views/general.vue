<script setup>
    import { ref } from 'vue';

    const productos = ref([
    { id: 1, nombre: 'Tomates Orgánicos', granja: 'Granja del Valle', precio: 4.50, unidad: 'kg', distancia: '2.3 km', img: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTU8W89aGsK2QYB_hVblPGlFpLVbRtb79TtrWQ9DqVUPaKJIEB2hf5o7OiSz1GpvpzEEsJgyn6wf63ISgsLCq26Y88kt-HwKXo49UtU7UiXYA&s=10' },
    { id: 2, nombre: 'Zanahorias Frescas', granja: 'Huerto Sol', precio: 3.20, unidad: 'kg', distancia: '1.8 km', img: 'data:image/jpeg;base64,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' },
    { id: 3, nombre: 'Patatas Locales', granja: 'Raíces de Tierra', precio: 2.10, unidad: 'kg', distancia: '5.0 km', img: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTlGXQ3omSBx3Mj4iNzV0rjHa0yta5GAM_gP0N-8k4YlZTWSFiLeEbUR7XM4qef8KAIA23kZL3Csr4FXNGDSkAITv2oG5GExBi-m-F8Jr0G_A&s=10' },
    { id: 4, nombre: 'Lechuga Romana', granja: 'Granja del Valle', precio: 1.50, unidad: 'ud', distancia: '2.3 km', img: 'data:image/jpeg;base64,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' },
    { id: 5, nombre: 'Miel Artesanal', granja: 'Abeja Feliz', precio: 8.00, unidad: 'tarro', distancia: '10 km', img: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSM9CsmbTlfkAhAvyUJMWbDnGiNpymhuc9xIkKZdd7YYhN9lk_Q2stFW1MO76kr9ZePOz4qeod0TaoUpJuWvbfl_GfdbXLRwCZ3_jFnIlQR&s=10' },
    { id: 6, nombre: 'Huevos Camperos', granja: 'Gallina Turuleca', precio: 5.50, unidad: 'docena', distancia: '3.0 km', img: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQipVCaUptJM_UCIZMqa0J2k8wytd8UxHEzxDhjtCMSco99mX0CskLCBDveprSZwhV5zxioLGgmqIJeXxyQnCOtLL5IXRiFeWyjZjniO_M5Rw&s=10' },
]);

    const vistaActual = ref('grid');
</script>

<template>
    <p>nav</p>
    <div id="cabecera">
        <div id="titulos">
            <h1>Descubre Productos Locales</h1>
            <p>Productos Frescos de Granjas Cercanas a Ti</p>
        </div>
        <div id="selector">
            <div id="borde">
                <button :class="{ 'activo': vistaActual === 'grid' }" @click="vistaActual = 'grid'">Productos</button>
                <button :class="{ 'activo': vistaActual === 'map' }" @click="vistaActual = 'map'">Mapa</button>
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
            <div class="grupo-filtro"><h4>Rango de Precios</h4><input type="range"><div id="rango-precios"><span>$0</span><span>$20</span></div></div>
        </aside>

        <main>
            <div id="resultados">
                <span>{{ productos.length }} Productos encontrados</span>
                <select>
                    <option>Ordenar por: Cercanía</option>
                </select>
            </div>

            <div id="productos">
                <div v-for="producto in productos" :key="producto.id" class="tarjeta-producto">

                    <div id="imagen-producto">
                        <img :src="producto.img" :alt="producto.nombre">
                    </div>

                    <div id="info-producto">
                        <div id="cabecera-info">
                            <h3>{{ producto.nombre }}</h3>
                            <span id="precio">${{ producto.precio }} / {{ producto.unidad }}</span>
                        </div>
                        <p id="granja">{{ producto.granja }}</p>

                        <div id="footer-tarjeta">
                            <span id="distancia">📍 {{ producto.distancia }}</span>
                            <button id="añadir">+</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<style scoped>
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
</style>