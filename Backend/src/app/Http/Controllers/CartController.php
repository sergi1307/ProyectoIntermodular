<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;
class CartController extends Controller
{
    /**
     * Función para obtener el carrito
     *
     * @param Request $request
     * @return json
     */
    public function getCart(Request $request)
    {
        // Obtenemos el id del usuario
        $userId = $request->user()->id_user;
        
        // Obtenemos el carrito con el id del usuario
        $cart = Cart::with(['items.product'])->where('id_user', $userId)->first();
        
        // Comprobamos que exista el carrito para que no de error
        if (!$cart) {
            return response()->json(['items' => [], 'total' => 0]);
        }

        // Iniciamos la variable de los items del carrito
        $total = 0;

        // Para cada carrito buscamos cada item que tiene
        foreach ($cart->items as $item) {
            $total += $item->product->price * $item->quantity;
        }

        // Retornamos la respuesta en json
        return response()->json([
            'id_cart' => $cart->id_cart,
            'items' => $cart->items,
            'total' => $total
        ]);
    }
    
    /**
     * Función para agregar un item al carrito
     *
     * @param Request $request
     * @return json
     */
    public function addItem(Request $request)
    {
        // Obtenemos el id del usuario
        $userId = $request->user()->id_user;

        // Obtenemos la cantidad solicitada a insertar
        $quantity = $request->quantity ?? 1;

        // Obtenemos el id del producto a insertar
        $product = Product::find($request->id_product);
        
        // Comprobamos que el producto existe
        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
        // Comprobamos que el stock no sea menor a la cantidad solicitada
        if ($product->stock < $quantity) {
            return response()->json(['message' => 'Stock insuficiente'], 400);
        }
        // Comprobamos que no intente comprar su propio producto
        if ($product->id_user == $userId) {
            return response()->json(['message' => 'No puedes comprar tu propio producto'], 403);
        }

        // Buscamos el carrito
        $cart = Cart::where('id_user', $userId)->first();

        // Si no existe, lo creamos
        if (!$cart) {
            $cart = Cart::create(['id_user' => $userId]);
        }

        // Comprobamos si el producto ya existe en el carrito
        $item = CartItem::where('id_cart', $cart->id_cart)
            ->where('id_product', $request->id_product)
            ->first();

        // Si existe, sumamos la cantidad que ya teníamos con la nueva solicitada
        if ($item) {
            $item->quantity += $quantity;
            $item->save();

        // Si no existe aún, lo añadimos al carrito
        } else {
            CartItem::create([
                'id_cart' => $cart->id_cart,
                'id_product' => $request->id_product,
                'quantity' => $quantity
            ]);
        }
        
        // Retornamos la respuesta en json 
        return response()->json(['message' => 'Añadido al carrito']);
    }

    /**
     * Función para cambiar la cantidad en el carrito
     *
     * @param Request $request
     * @param number $id
     * @return json
     */
    public function updateItem(Request $request, $id)
    {
        // Obtenemos el id del usuario
        $userId = $request->user()->id_user;
        
        // Buscamos el item en el carrito
        $item = CartItem::find($id);
        
        // Comprobamos que exista el producto en el carrito
        if (!$item) {
            return response()->json(['message' => 'Item no encontrado'], 404);
        }

        // comprobamos que el carrito sea del usuario
        $cart = Cart::find($item->id_cart);
        if ($cart->id_user != $userId) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Actualizamos la cantidad en el carrito
        $item->quantity = $request->quantity;
        $item->save();

        // Retornamos la respuesta en json
        return response()->json(['message' => 'Cantidad actualizada']);
    }
    
    /**
     * Función para eliminar un producto
     *
     * @param Request $request
     * @param number $id
     * @return json
     */
    public function removeItem(Request $request, $id)
    {
        // Obtenemos el id del usuario
        $userId = $request->user()->id_user;
        
        // Obtenemos el item del carrito a eliminar
        $item = CartItem::find($id);
        
        // Comprobamos si existe o no el producto en el carrito
        if (!$item) {
            return response()->json(['message' => 'Item no encontrado'], 404);
        }

        // Comprobamos que el carrito sea del usuario
        $cart = Cart::find($item->id_cart);
        if ($cart->id_user != $userId) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Eliminamos definitivamente el producto del carrito
        $item->delete();

        // Retornamos la respuesta en formato json
        return response()->json(['message' => 'Eliminado del carrito']);
    }

    /**
     * Función para limpiar todo el carrito
     *
     * @param Request $request
     * @return void
     */
    public function clear(Request $request)
    {
        // Obtenemos el id del usuario
        $userId = $request->user()->id_user;
        
        // Obtenemos el carrito que le pertenece al usuario
        $cart = Cart::where('id_user', $userId)->first();
        
        // Si existe el carrito, lo limpiamos
        if ($cart) {
            CartItem::where('id_cart', $cart->id_cart)->delete();
        }

        // Retornamos la respuesta en formato json
        return response()->json(['message' => 'Carrito vaciado']);
    }
    
    /**
     * Función para comprar todo el carrito
     *
     * @param Request $request
     * @return json
     */
    public function checkout(Request $request)
    {
        // Obtenemos el id del usuario
        $userId = $request->user()->id_user;
        
        // Obtenemos el carrito con sus productos cuando pertenezcan al id del usuario
        $cart = Cart::with(['items.product'])->where('id_user', $userId)->first();

        // Comprobamos que el carrito exista y que tenga más de un producto
        if (!$cart || count($cart->items) == 0) {
            return response()->json(['message' => 'Carrito vacio'], 400);
        }

        // Creamos una venta por cada producto
        foreach ($cart->items as $item) {

            // Comprobamos que el stock no sea inferior a la cantidad solicitada por cada producto
            $product = $item->product;
            if ($product->stock < $item->quantity) {
                return response()->json(['message' => 'Stock insuficiente: ' . $product->name], 400);
            }

            // Creamos la venta por producto
            Sale::create([
                'id_product' => $product->id_product,
                'id_buyer' => $userId,
                'id_seller' => $product->id_user,
                'id_delivery_point' => $request->id_delivery_point,
                'sale_date' => Carbon::now()->format('Y-m-d'),
                'total' => $product->price * $item->quantity,
                'collection_date' => Carbon::now()->addDays(3)->format('Y-m-d'),
                'state' => 'Pendiente'
            ]);

            // Restamos el stock total
            $product->stock -= $item->quantity;
            $product->save();
        }

        // Una vez comprado todo, limpiamos el carrito
        CartItem::where('id_cart', $cart->id_cart)->delete();

        // Retornamos la respuesta en formato json
        return response()->json(['message' => 'Compra realizada']);
    }
}
