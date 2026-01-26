<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Delivery_Point;

class Delivery_pointController extends Controller
{
    /**
     * Función para obtener los puntos de venta de un usuario
     *
     * @return json
     */
    public function myPoints(Request $request) {
        // Obtenemos el id del usuario
        $userId = $request->user()->id_user ?? $request->id_user;

        // Buscamos los puntos de venta con los demás campos necesarios
        $delivery_points = Delivery_Point::where('id_user', $userId)->get();

        // Devolvemos la respuesta en formato json
        return response()->json([
            'status' => 'true',
            'total' => count($delivery_points),
            'data' => $delivery_points
        ], 200);
    }

    /**
     * Función para crear un punto de venta
     *
     * @param Request $request
     * @return json
     */
    public function store(Request $request)
    {
        // Validamos los datos antes de insertarlos en la base de datos
        $validated = $request->validate([
            'id_user' => 'required|integer|exists:users,id_user',
            'name' => 'required|string',
            'direction' => 'required|string',
            'latitude' => 'required|numeric',
            'length' => 'required|numeric'
        ]);

        // Creamos definitivamente el punto de venta
        $delivery = Delivery_Point::create([
            'id_user' => $request -> id_user,            
            'name' => $request -> name,
            'direction' => $request -> direction,
            'latitude' => $request -> latitude,
            'length' => $request -> length,
        ]);

        // Devolvemos la respuesta en formato json
        return response() -> json([
            'status' => 'true',
            'message' => 'punto de venta creaedo correctament',
        ], 200);
    }

    /**
     * Función para obtener todos los puntos de venta
     *
     * @return json
     */
    public function index()
    {
        // Obtenemos todos los puntos de venta con sus relaciones
        $delivery_point = Delivery_Point::with('user:id_user:name') -> get();
        
        // Devolvemos la lista de puntos de venta
        return response() ->json($delivery_point, 200);
    }

    /**
     * Función para obtener un punto de venta
     *
     * @param numeric $id
     * @return json
     */
    public function show($id)
    {
        // Obtenemos el punto de venta con sus relaciones
        $delivery_point = Delivery_Point::with('user:id_user:name') -> findOrFail($id);
        
        // Devolvemos la respuesta en formato json
        return response() ->json($delivery_point, 200);
    }

    /**
     * Función para actualizar un punto de venta
     *
     * @param Request $request
     * @param numeric $id
     * @return json
     */
    public function update(Request $request, $id)
    {
        // Obtenemos el punto de venta por medio de su id
        $delivery_point  = Delivery_Point::findOrFail($id);

        // Validamos los datos antes de insertarlos
        $request->validate([
            'name' => 'required|string',
            'direction' => 'required|string',
            'latitude' => 'required|numeric',
            'length' => 'required|numeric'
        ]);

        // Insertamos los datos y actualizamos el punto de venta
        $delivery_point ->update([
            'name' => $request -> name,
            'direction' => $request -> direction,
            'latitude' => $request -> latitude,
            'length' => $request -> length,
        ]);

        // Devolvemos la respuesta en formato json
        return response() -> json([
            'message' => 'punto de venta actualizado',
            'Delivery_Point' => $delivery_point
        ], 200);
    }

    /**
     * Función para eliminar un punto de venta
     *
     * @param numeric $id
     * @return json
     */
    public function destroy($id)
    {
        // Obtenemos el punto de venta por medio de su id
        $delivery_point = Delivery_Point::findOrFail($id);

        // Eliminamos el punto de venta
        $delivery_point -> delete();

        // Devolvemos la respuesta en formato json
        return response()-> json([
            'message' => 'punto de venta eliminado'
        ], 200);
    }
}