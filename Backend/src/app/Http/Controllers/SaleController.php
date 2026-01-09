<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Sale;
use Carbon\Carbon;


class SaleController extends Controller
{
    public function createSale(Request $request){
        $sale = Sale::create([
            'id_product' => $request -> id_product,
            'id_buyer' => $request -> id_buyer,
            'id_seller' => $request -> id_saller,
            'id_delivery_point' => $request -> id_delivery_point,
            'id_review' => $request -> id_review,
            'sale_date' => Carbon::parse($request->sale_date),
            'total' => $request -> total,
            'collection_date' =>  Carbon::parse($request->collection_date),
        ]);
        return response()->json([
            'status' => 'true',
            'message' => 'Venta creat correctament',
        ], 200);
    }
    public function index()
    {
        $sale = Sale::with(['product:id_product,name','user:id_user,name','user:id_user,name','Delivery_Point:id_delivery_point,name','review:id_review,calification']) ->get();
        return response()->json( $sale,200 );
    }
    public function show($id){
        $sale = Sale::with('product:id,name','user:id,name','user:id,name','Delivery_Point:id,calification','review:id_review,calification')->find($id);
        if (!$sale){
        return response() -> json(['message' => 'no se ha encontrado la venta'], 404);
        }
        return response() -> json($sale,200);
    }
    public function update(request $request, $id){
        $sale = Sale::find($id);
        if (!$sale){
        return response() -> json(['message' => 'no se ha encontrado la venta'],404 );
        }
        $datos = [
            'sale_date' => Carbon::parse($request->sale_date),
            'total' => $request -> total,
            'collection_date' =>  Carbon::parse($request->collection_date),
        ];
        $sale->update($datos);
        return response()->json([
            'message' => 'Venta actualizada',
            'sale' => $sale
        ],200);
    }
    public function destroy($id){
    $sale = Sale::findOrFail($id);
    $sale->delete();
        return response()-> json([
            'message' => 'Venta eliminada'
        ],200);
    }

}