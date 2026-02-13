<script setup>
import { ref } from 'vue'

import products from '../../components/seller/products.vue'
import sales from '../../components/seller/sales.vue'
import maps from '../../components/seller/map.vue'

const pestanyaActiva = ref('products')
</script>

<template>
<div id="dashboard">
    <div id="contenedor">
      
      <div id="menu">
        <ul>
          <li>
            <a href="#" 
               @click.prevent="pestanyaActiva = 'products'"
               :class="{ active: pestanyaActiva === 'products' }">
               Productos
            </a>
          </li>
          <li>
            <a href="#" 
               @click.prevent="pestanyaActiva = 'sales'"
               :class="{ active: pestanyaActiva === 'sales' }">
               Ordenes
            </a>
          </li>
          <li>
            <a href="#" 
               @click.prevent="pestanyaActiva = 'map'"
               :class="{ active: pestanyaActiva === 'map' }">
               Mapas
            </a>
          </li>
        </ul>
      </div>

      <div class="contenido">
        <products v-if="pestanyaActiva === 'products'" />
        <sales v-else-if="pestanyaActiva === 'sales'" />
        <maps v-else-if="pestanyaActiva === 'map'" />
      </div>

    </div>
</div>
</template>

<style scoped>
#dashboard {
  background-color: #f6f8f7;
  min-height: 100vh;
  padding: 24px;
  font-family: 'Inter', system-ui, sans-serif;
  box-sizing: border-box;
}

#contenedor {
  background: white;
  border-radius: 14px;
  padding: 24px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.05);
  max-width: 1400px;
  margin: 0 auto;
}

#menu ul {
  display: flex;
  gap: 32px;
  border-bottom: 2px solid #e5e7eb;
  padding-bottom: 12px;
  margin-bottom: 24px;
  padding-left: 0;
  margin-top: 0;
}

#menu li {
  list-style: none;
}

#menu a {
  text-decoration: none;
  font-weight: 600;
  color: #6b7280;
  padding-bottom: 12px;
  transition: all 0.2s;
  display: inline-block;
  border-bottom: 3px solid transparent;
}

#menu a:hover {
  color: #1c5537;
}

#menu a.active {
  color: #1c5537;
  border-bottom: 3px solid #1c5537;
}

@media (max-width: 768px) {
  #dashboard {
    padding: 10px;
  }

  #contenedor {
    padding: 15px;
    border-radius: 8px;
  }

  #menu ul {
    gap: 10px;
    justify-content: space-between;
  }

  #menu li {
    flex: 1;
    text-align: center;
  }

  #menu a {
    width: 100%;
    font-size: 0.95rem;
    padding-bottom: 8px;
  }
}
</style>