<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Notifications\MensajeNotificacion;

class MessageController extends Controller
{
    /**
     * Función para obtener el chat
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        // Obtenemos el id del usuario
        $idPersonal = $request->user()->id_user;

        // Obtenemos todos los mensajes relacionados con ese usuario
        $messages = Message::with(['product', 'transmitter', 'receiver'])
            ->where('id_transmitter', $idPersonal)
            ->orWhere('id_receiver', $idPersonal)
            ->orderBy('shipping_date', 'desc')
            ->get();
        
        // Creamos la bandeja de entrada del usuario
        $bandeja = $messages->unique(function ($mensaje) use ($idPersonal) {
            
            // Función para comprobar si el otro usuario es emisor o receptor
            if ($mensaje->id_transmitter == $idPersonal) {
                $otroUsuario = $mensaje->id_receiver;
            } else {
                $otroUsuario = $mensaje->id_transmitter;
            }

            // Retornamos la combinación producto y usuario único
            return $mensaje->id_product . '-' . $otroUsuario;
        })->values();

        // Retornamos la respuesta en formato json
        return response()->json([
            'status' => 'true',
            'bandeja' => $bandeja
        ], 200);
    }

    /**
     * Función para almacenar mensajes
     *
     * @param Request $request
     * @return json
     */
    public function store(Request $request)
    {
        // Validamos los datos antes de insertarlos
        $request->validate([
            'id_product' => 'required|exists:products,id_product',
            'id_receiver' => 'required|exists:users,id_user',
            'content' => 'required|string'
        ]);

        // ID de emisor
        $id_transmitter = $request->user()->id_user;
        
        // Fecha de envío de mensaje
        $shipping_date = Carbon::now('Europe/Madrid')->format('Y-m-d H:i:s');

        // Insertamos los datos definitivamente
        $message = Message::create([
            'id_product' => $request->id_product,
            'id_transmitter' => $id_transmitter,
            'id_receiver' => $request->id_receiver,
            'content' => $request->content,
            'shipping_date' => $shipping_date
        ]);

        $receiver = User::find($request->id_receiver);

        if ($receiver) {
            $receiver->notify(new MensajeNotificacion(($message)));
        }

        // Retornamos la respuesta en formato json
        return response()->json([
            'status' => 'true',
            'message' => 'Mensaje creado',
            'detalles' => $message
        ], 200);
    }

    /**
     * Función para mostrar un chat
     * 
     * @param Request $request
     * @return json
     */
    public function show(Request $request)
    {
        // Validamos los datos
        $request->validate([
            'id_product' => 'required|exists:products,id_product',
            'id_user_chat' => 'required|exists:users,id_user'
        ]);

        $idPersonal = $request->user()->id_user;
        $idProducto = $request->id_product;
        $idOtroUsuario = $request->id_user_chat;

        // SOLUCIÓN: Una sola consulta agrupada con paréntesis lógicos
        // SELECT * FROM messages WHERE id_product = X AND ( (emisor=YO y receptor=TU) OR (emisor=TU y receptor=YO) )
        $mensajes = Message::where('id_product', $idProducto)
            ->where(function($query) use ($idPersonal, $idOtroUsuario) {
                $query->where(function($q) use ($idPersonal, $idOtroUsuario) {
                    $q->where('id_transmitter', $idPersonal)
                      ->where('id_receiver', $idOtroUsuario);
                })
                ->orWhere(function($q) use ($idPersonal, $idOtroUsuario) {
                    $q->where('id_transmitter', $idOtroUsuario)
                      ->where('id_receiver', $idPersonal);
                });
            })
            ->orderBy('shipping_date', 'asc')
            ->get();

        return response()->json([
            'status' => 'true',
            'messages' => $mensajes
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        // Buscamos el mensaje por id
        $mensaje = Message::findOrFail($id);

        // Comprobamos que el mensaje sea de el
        if ($mensaje->id_transmitter != $request->user()->id_user) {
            return response()->json([
                'status' => 'false',
                'message' => 'No tienes permiso para borrar este mensaje'
            ], 403);
        }

        // Borramos el mensaje
        $mensaje->delete();

        // Retornamos la respuesta en formato json
        return response()->json([
            'status' => 'true',
            'message' => 'Mensaje eliminado correctamente'
        ], 200);
    }
}