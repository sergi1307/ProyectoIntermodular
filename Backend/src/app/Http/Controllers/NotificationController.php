<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Psr\Http\Message\ResponseFactoryInterface;

class NotificationController extends Controller
{
    /**
     * Obtenemos la lista de notificaciones
     * 
     * @param Request $request
     * 
     * @return notification
     */
    public function index(Request $request)
    {
        // Devolvemos la lista de notificaciones del usuario
        return $request->user()->notifications()->latest()->paginate(10);
    }

    /**
     * Contamos notificaciones no leídas
     * 
     * @param Request $request
     * 
     * @return json
     */
    public function unreadCount(Request $request)
    {
        // Retornamos la cantidad de notificaciones sin leer en formato json
        return response()->json([
            'count' => $request->user()->unreadNotifications()->count()
        ]);
    }

    /**
     * Marca una notificación como leída
     * 
     * @param Request $request
     * @param numeric $id
     * 
     * @return json
     */
    public function markAsRead(Request $request, $id)
    {
        // Obtenemos la notificación
        $notification = $request->user()->notifications()->where('id', $id)->first();
        
        // Comprobamos que exista la notificación
        if ($notification) {
            // Marcamos como leída la notificación
            $notification->markAsRead();

            // Devolvemos la respuesta en formato json
            return response()->json([
                'message' => 'Notificación leída'
            ]);
        }

        // Retornamos la respuesta en formato json
        return response()->json([
            'message' => 'Notificación no encontrada'
        ]);
    }

    /**
     * Marca todas las notificaciones como leídas
     *
     * @param Request $request
     * @param numeric $id
     * 
     * @return json
     */
    public function markAllAsRead(Request $request, $id)
    {
        // Marcamos como leídas todas las notificaciones
        $request->user()->unreadNotifications->markAsRead();

        // Devolvemos la respuesta en formato json
        return response()->json([
            'message' => 'Todas leídas'
        ]);
    }

    /**
     * Elimina una notificación
     * 
     * @param Request $request
     * @param numeric $id
     * 
     * @return json
     */
    public function destroy(Request $request, $id)
    {
        // Obtenemos la notificación
        $notification = $request->user()->notifications()->where('id', $id)->first();

        // Comprobamos que la notificación existe
        if ($notification) {
            // Eliminamos la notificación
            $notification->delete();
        }

        // Devolvemos la respuesta en formato json
        return response()->json([
            'message' => 'Notificación eliminada'
        ]);
    }
}
