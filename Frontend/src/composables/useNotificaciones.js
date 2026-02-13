import { useToast } from 'vue-toastification'

export function useNotificaciones() {
    const notificacion = useToast()

    return {
        exito: (mensaje) => notificacion.success(mensaje),
        error: (mensaje) => notificacion.error(mensaje),
        advertencia: (mensaje) => notificacion.warning(mensaje),
        info: (mensaje) => notificacion.info(mensaje)
    }
}
