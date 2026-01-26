<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class CartItem extends Model
{
    // Obtenemos la tabla cart_items
    protected $table = 'cart_items';

    // Definimos la primary key
    protected $primaryKey = 'id_cart_item';

    // Definimos los campos que se pueden llenar 
    protected $fillable = ['id_cart', 'id_product', 'quantity'];

    // Función para obtener el carrito al que pertenece ese item
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'id_cart', 'id_cart');
    }

    // Función para obtener el producto al que llama este item
    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product', 'id_product');
    }
}
