import { reactive, computed } from 'vue';

// Estado global reactivo para almacenar las notificaciones
const estado = reactive({
    notificaciones: [],
    confirmacion: null
});

let contadorId = 0;

export function useNotificaciones() {

    const agregar = (mensaje, tipo) => {
        const id = contadorId++;
        estado.notificaciones.push({ id, mensaje, tipo });

        // Auto-eliminar a los 3 segundos
        setTimeout(() => {
            eliminar(id);
        }, 3000);
    };

    const eliminar = (id) => {
        const index = estado.notificaciones.findIndex(n => n.id === id);
        if (index !== -1) {
            estado.notificaciones.splice(index, 1);
        }
    };

    const confirmar = (mensaje, accion) => {
        estado.confirmacion = { mensaje, accion };
    };

    const cerrarConfirmacion = () => {
        estado.confirmacion = null;
    };

    return {
        notificaciones: estado.notificaciones,
        confirmacion: computed(() => estado.confirmacion), // Para que sea reactivo al leerlo
        eliminar,
        confirmar,
        cerrarConfirmacion,
        exito: (mensaje) => agregar(mensaje, 'exito'),
        error: (mensaje) => agregar(mensaje, 'error'),
        advertencia: (mensaje) => agregar(mensaje, 'advertencia'),
        info: (mensaje) => agregar(mensaje, 'info')
    };
}
