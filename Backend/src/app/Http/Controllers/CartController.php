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
    // ver carrito
    public function getCart(Request $request)
    {
        $userId = $request->user()->id_user;
        
        $cart = Cart::with(['items.product'])->where('id_user', $userId)->first();
        if (!$cart) {
            return response()->json(['items' => [], 'total' => 0]);
        }
        $total = 0;
        foreach ($cart->items as $item) {
            $total += $item->product->price * $item->quantity;
        }
        return response()->json([
            'id_cart' => $cart->id_cart,
            'items' => $cart->items,
            'total' => $total
        ]);
    }
    // añadir producto
    public function addItem(Request $request)
    {
        $userId = $request->user()->id_user;
        $quantity = $request->quantity ?? 1;
        $product = Product::find($request->id_product);
        
        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
        if ($product->stock < $quantity) {
            return response()->json(['message' => 'Stock insuficiente'], 400);
        }
        if ($product->id_user == $userId) {
            return response()->json(['message' => 'No puedes comprar tu propio producto'], 403);
        }
        // buscar o crear carrito
        $cart = Cart::where('id_user', $userId)->first();
        if (!$cart) {
            $cart = Cart::create(['id_user' => $userId]);
        }
        // ver si ya existe el producto en el carrito
        $item = CartItem::where('id_cart', $cart->id_cart)
            ->where('id_product', $request->id_product)
            ->first();
        if ($item) {
            $item->quantity += $quantity;
            $item->save();
        } else {
            CartItem::create([
                'id_cart' => $cart->id_cart,
                'id_product' => $request->id_product,
                'quantity' => $quantity
            ]);
        }
        return response()->json(['message' => 'Añadido al carrito']);
    }
    // cambiar cantidad
    public function updateItem(Request $request, $id)
    {
        $userId = $request->user()->id_user;
        
        $item = CartItem::find($id);
        
        if (!$item) {
            return response()->json(['message' => 'Item no encontrado'], 404);
        }
        // comprobar que es del usuario
        $cart = Cart::find($item->id_cart);
        if ($cart->id_user != $userId) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        $item->quantity = $request->quantity;
        $item->save();
        return response()->json(['message' => 'Cantidad actualizada']);
    }
    // quitar producto
    public function removeItem(Request $request, $id)
    {
        $userId = $request->user()->id_user;
        
        $item = CartItem::find($id);
        
        if (!$item) {
            return response()->json(['message' => 'Item no encontrado'], 404);
        }
        $cart = Cart::find($item->id_cart);
        if ($cart->id_user != $userId) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        $item->delete();
        return response()->json(['message' => 'Eliminado del carrito']);
    }
    // vaciar carrito
    public function clear(Request $request)
    {
        $userId = $request->user()->id_user;
        
        $cart = Cart::where('id_user', $userId)->first();
        
        if ($cart) {
            CartItem::where('id_cart', $cart->id_cart)->delete();
        }
        return response()->json(['message' => 'Carrito vaciado']);
    }
    // comprar todo el carrito
    public function checkout(Request $request)
    {
        $userId = $request->user()->id_user;
        
        $cart = Cart::with(['items.product'])->where('id_user', $userId)->first();
        if (!$cart || count($cart->items) == 0) {
            return response()->json(['message' => 'Carrito vacio'], 400);
        }
        // crear una venta por cada item
        foreach ($cart->items as $item) {
            $product = $item->product;
            if ($product->stock < $item->quantity) {
                return response()->json(['message' => 'Stock insuficiente: ' . $product->name], 400);
            }
            Sale::create([
                'id_product' => $product->id_product,
                'id_buyer' => $userId,
                'id_seller' => $product->id_user,
                'id_delivery_point' => $request->id_delivery_point,
                'sale_date' => Carbon::now()->format('Y-m-d'),
                'total' => $product->price * $item->quantity
            ]);
            // restar stock
            $product->stock -= $item->quantity;
            $product->save();
        }
        // vaciar carrito
        CartItem::where('id_cart', $cart->id_cart)->delete();
        return response()->json(['message' => 'Compra realizada']);
    }
}
