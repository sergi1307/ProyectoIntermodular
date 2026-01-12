<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Delivery_Point;

class Delivery_pointController extends Controller
{
    // 1. Crear un punto de entrega
    public function create(Request $request){
        $delivery = Delivery_Point::create([
            'id_user' => $request -> id_user,            
            'name' => $request -> name,
            'direction' => $request -> direction,
            'latitude' => $request -> latitude,
            'length' => $request -> length,
        ]);
        return response() -> json([
            'status' => 'true',
            'message' => 'punto de venta creat correctament',
        ],200);
    }
    // 2. lsitar todos los punto de entrega
    public function index(){
        $delivery_point = Delivery_Point::with('user:id_user:name') -> get();
        return response() ->json($delivery_point,200);
    }
    // 3. Mostrar un punto de entrega
    public function show($id){
        $delivery_point = Delivery_Point::with('user:id_user:name') -> findOrFail($id);
        return response() ->json($delivery_point,200);
    }
    // 4. Actualizar un punto de entrega
    public function update(Request $request, $id){
        $delivery_point  = Delivery_Point::findOrFail($id);
        $delivery_point ->update([
            'name' => $request -> name,
            'direction' => $request -> direcction,
            'latitude' => $request -> latitude,
            'length' => $request -> length,
        ]);
        return response() -> json([
            'message' => 'punto de venta actualizada',
            'Delivery_Point' => $delivery_point
        ],200);
    }
    // 5. Eliminar un punto de entrega
    public function destroy($id){
        $delivery_point = Delivery_Point::findOrFail($id);
        $delivery_point -> delete();
        return response()-> json([
            'message' => 'punto de venta eliminado'
        ],200);
    }
       
}
