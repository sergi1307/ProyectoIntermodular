<script setup>
import { useNotificaciones } from '@/composables/useNotificaciones';

const { notificaciones, eliminar, confirmacion, cerrarConfirmacion } = useNotificaciones();

const ejecutarAccion = () => {
    if (confirmacion.value && confirmacion.value.accion) {
        confirmacion.value.accion();
    }
    cerrarConfirmacion();
};
</script>

<template>
    <div class="contenedor-toasts">
        <transition-group name="toast">
            <div 
                v-for="notificacion in notificaciones" 
                :key="notificacion.id"
                class="toast"
                :class="notificacion.tipo"
                @click="eliminar(notificacion.id)"
            >
                <span v-if="notificacion.tipo === 'exito'" class="icono">✅</span>
                <span v-else-if="notificacion.tipo === 'error'" class="icono">❌</span>
                <span v-else-if="notificacion.tipo === 'advertencia'" class="icono">⚠️</span>
                <span v-else class="icono">ℹ️</span>

                <span class="mensaje">{{ notificacion.mensaje }}</span>
            </div>
        </transition-group>
    </div>

    <!-- Modal de Confirmación -->
    <div v-if="confirmacion" class="modal-overlay">
        <div class="modal-confirmr">
            <h3>¿Estás seguro?</h3>
            <p>{{ confirmacion.mensaje }}</p>
            <div class="botones">
                <button @click="ejecutarAccion" class="btn-si">Sí, confirmar</button>
                <button @click="cerrarConfirmacion" class="btn-no">Cancelar</button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.contenedor-toasts {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 10px;
    pointer-events: none;
}

.toast {
    pointer-events: auto;
    background: white;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 250px;
    max-width: 400px;
    cursor: pointer;
    font-family: sans-serif;
    font-size: 14px;
    font-weight: 500;
    border-left: 5px solid #ccc;
    background-color: #fff;
    color: #333;
}

/* Tipos */
.toast.exito { border-left-color: #4caf50; }
.toast.error { border-left-color: #f44336; }
.toast.advertencia { border-left-color: #ff9800; }
.toast.info { border-left-color: #2196f3; }

/* Animaciones */
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}
.toast-enter-from {
  opacity: 0;
  transform: translateX(30px);
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

/* Modal Confirmación */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0,0,0,0.5);
    z-index: 10000;
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-confirmr {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    text-align: center;
    max-width: 400px;
    width: 90%;
}

.modal-confirmr h3 {
    margin-top: 0;
    color: #333;
}

.modal-confirmr .botones {
    margin-top: 20px;
    display: flex;
    justify-content: center;
    gap: 15px;
}

.btn-si {
    background: #1c5537;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
}

.btn-no {
    background: #ccc;
    color: #333;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
}
</style>
