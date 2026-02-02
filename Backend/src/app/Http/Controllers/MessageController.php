<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        // IDs necesarios para hacer las comprobaciones
        $idPersonal = $request->user()->id_user;
        $idProducto = $request->id_product;
        $id_usuario = $request->id_user_chat;

        // Obtenemos los mensajes enviados
        $enviados = Message::where('id_product', $idProducto)
            ->where('id_transmitter', $idPersonal)
            ->where('id_receiver', $id_usuario)
            ->get();
        
        // Obtenemos los mensajes recibidos
        $recibidos = Message::where('id_product', $idProducto)
            ->where('id_transmitter', $id_usuario)
            ->where('id_receiver', $idPersonal)
            ->get();
        
        // Fusionamos para crear el chat
        $mensajes = $enviados->merge($recibidos)->sortBy('shipping_date')->values();

        // Retornamos la respuesta en formato json
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