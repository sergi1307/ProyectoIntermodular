<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Sale;
use Carbon\Carbon;


class SaleController extends Controller
{
    // 1. Crear una Venta
    public function createSale(Request $request)
    {
        $sale = Sale::create([
            'id_product'        => $request->id_product,
            'id_buyer'          => $request->id_buyer,
            'id_seller'         => $request->id_seller,
            'id_delivery_point' => $request->id_delivery_point,
            'sale_date'         => Carbon::parse($request->sale_date),
            'total'             => $request->total,
            'collection_date'   => Carbon::parse($request->collection_date),
        ]);

        return response()->json([
            'status' => 'true',
            'message' => 'Venta creada correctamente',
        ], 200);
    }

    // 2. Listar Ventas
    public function index()
    {
        $sales = Sale::with([
            'product:id_product,name', 'buyer:id_user,name', 'seller:id_user,name', 'delivery_point:id_delivery_point,name'
        ])->get();

        return response()->json($sales, 200);
    }

    // 3. Mostrar una Venta
    public function show($id)
    {
        $sale = Sale::with([
            'product:id_product,name', 'buyer:id_user,name', 'seller:id_user,name', 'delivery_point:id_delivery_point,name'
        ])->find($id);

        if (!$sale) {
            return response()->json(['message' => 'No se ha encontrado la venta'], 404);
        }

        return response()->json($sale, 200);
    }
    // 4. Actualizar una Venta
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
    // 5. Eliminar una Venta
    public function destroy($id){
    $sale = Sale::findOrFail($id);
    $sale->delete();
        return response()-> json([
            'message' => 'Venta eliminada'
        ],200);
    }

}