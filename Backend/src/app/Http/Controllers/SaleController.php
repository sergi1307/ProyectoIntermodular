<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use function Symfony\Component\Clock\now;

class SaleController extends Controller
{
    /**
     * Función para crear una venta
     *
     * @param Request $request
     * @return json
     */
    public function store(Request $request)
    {
        // 1. Validamos los datos (Añadí 'quantity' a la validación para mayor seguridad)
        $validated = $request->validate([
            'id_product' => 'required|integer|exists:products,id_product',
            'id_buyer' => 'required|integer|exists:users,id_user',
            'id_seller' => 'required|integer|exists:users,id_user',
            'id_delivery_point' => 'required|integer|exists:delivery_points,id_delivery_point',
            'total' => 'required|numeric',
            'quantity' => 'required|integer|min:1' // Campo añadido
        ]);

        // Obtenemos el producto por su id
        $product = Product::findOrFail($request->id_product);

        // Comprobamos que el stock sea superior a la cantidad solicitada
        if ($product->stock < $request->quantity) { // Corregido a $request->quantity
            return response()->json([
                'status' => 'false',
                'message' => 'Stock insuficiente'
            ], 400);
        }

        // Obtenemos el id del comprador
        $compradorId = $request->user()->id_user ?? $request->id_buyer;

        // Comprobamos que el usuario no intente comprar su propio producto
        if ($product->id_user == $compradorId) {
            return response()->json([
                'status' => 'false',
                'message' => 'No puedes comprar tus propios productos'
            ], 403);
        }
        
        // Restamos la cantidad comprada al stock actual
        $product->stock = $product->stock - $request->quantity;

        if ($product->stock <= 0) {
            $product->state = 'Agotado'; 
        }

        $product->save();

        // Insertamos los datos de la venta definitivamente
        $sale = Sale::create([
            'id_product' => $request->id_product,
            'id_buyer' => $request->id_buyer,
            'id_seller' => $request->id_seller,
            'id_delivery_point' => $request->id_delivery_point,
            'sale_date' => Carbon::now('Europe/Madrid')->format('Y-m-d'),
            'total' => $request->total,
            'quantity' => $request->quantity, // Campo añadido
            'state' => 'En Curso'
        ]);

        // Devolvemos los datos en formato json
        return response()->json([
            'status' => 'true',
            'message' => 'Venta creada y stock actualizado correctamente',
        ], 200);
    }

    /**
     * Función para obtener todas las ventas de un usuario
     *
     * @return json
     */
    public function myOrders(Request $request)
    {
        // Obtenemos el id del usuario
        $userId = $request->user()->id_user;

        // Obtenemos la venta cuando el usuario sea el vendedor
        $sales = Sale::where('id_seller', $userId)
            ->with([
                'product:id_product,name,image,price', 
                'buyer:id_user,name', 
                'delivery_point:id_delivery_point,name,direction'
            ])
            ->get();
        
        // Devolvemos la respuesta en formato json
        return response()->json($sales, 200);
    }

    /**
     * Función para obtener todas las compras de un usuario
     * * @return json
     */
    public function myPurchase(Request $request)
    {
        // Obtenemos el id del usuario
        $userId = $request->user()->id_user;

        // Obtenemos las compras del usuario
        $purchases = Sale::where('id_buyer', $userId)
            ->with([
                'product:id_product,name,image,price',
                'seller:id_user,name',
                'delivery_point:id_delivery_point,name,direction'
            ])
            ->get();
        
        // Devolvemos la respuesta en json
        return response()->json($purchases, 200);
    }

    /**
     * Función para obtener una venta
     *
     * @param numeric $id
     * @return json
     */
    public function show($id)
    {
        // Obtenemos la venta con sus relaciones
        $sale = Sale::with([
            'product:id_product,name', 'buyer:id_user,name', 'seller:id_user,name', 'delivery_point:id_delivery_point,name'
        ])->find($id);

        // Comprobamos que la venta existe
        if (!$sale) {
            return response()->json(['message' => 'No se ha encontrado la venta'], 404);
        }

        // Devolvemos la respuesta en formato json
        return response()->json($sale, 200);
    }

    /**
     * Función para actualizar una venta
     *
     * @param request $request
     * @param numeric $id
     * @return json
     */
    public function update(Request $request, $id_venta)
    {
        // Comprobamos que la venta existe y la obtenemos
        $sale = Sale::findOrFail($id_venta);

        // Obtenemos el producto asociado a la venta
        $product = $sale->product; 

        // Validamos los datos antes de introducirlos en la base de datos
        $validated = $request->validate([
            'collection_date' => 'nullable|date',
            'state' => 'required|string|in:Rechazado,Aceptado'
        ]);

        // Cambiamos el estado de la venta al recibido por el usuario
        $sale->state = $request->state;
        
        // Comprobamos que si es aceptado asigne ya la fecha de recogida
        if($request->state === 'Aceptado') {
            $sale->collection_date = now();
        } else {
            // Devolvemos al stock la cantidad original de la venta
            $product->stock = $product->stock + $sale->quantity;
            $sale->collection_date = null;
            
            // Si el producto estaba agotado, lo reactivamos si ahora hay stock
            if ($product->stock > 0 && $product->state === 'Agotado') {
                $product->state = 'Disponible'; // O el estado por defecto que uses
            }
        }

        // Guardamos los cambios
        $sale->save();
        $product->save();

        // Devolvemos la respuesta en formato json
        return response()->json(['message' => 'Venta actualizada', 'sale' => $sale], 200);
    }

    /**
     * Función para eliminar una venta
     *
     * @param numeric $id
     * @return json
     */
    public function destroy($id)
    {
        // Obtenemos una venta por id
        $sale = Sale::findOrFail($id);

        // Eliminamos la venta
        $sale->delete();

        // Devolvemos la respuesta en formato json
        return response()-> json([
            'message' => 'Venta eliminada'
        ], 200);
    }
}