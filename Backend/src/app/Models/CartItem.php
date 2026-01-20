<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class CartItem extends Model
{
    protected $table = 'cart_items';
    protected $primaryKey = 'id_cart_item';
    protected $fillable = ['id_cart', 'id_product', 'quantity'];
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'id_cart', 'id_cart');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product', 'id_product');
    }
}
