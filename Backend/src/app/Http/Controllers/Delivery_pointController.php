<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Delivery_Point;

class Delivery_pointController extends Controller
{
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
    public function index(){
        $delivery_point = Delivery_Point::with('user:id_user:name') -> get();
        return response() ->json($delivery_point,200);
    }
    public function show($id){
        $delivery_point = Delivery_Point::with('user:id_user:name') -> findOrFail($id);
        return response() ->json($delivery_point,200);
    }
    public function update(Request $request, $id){
        $delivery_point  = Delivery_Point::findOrFail($id);
        $delivery_point = Delivery_Point::update([
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
    public function destroy($id){
        $delivery_point = Delivery_Point::findOrFail($id);
        $delivery_point -> delete();
        return response()-> json([
            'message' => 'punto de venta eliminado'
        ],200);
    }
       
}
