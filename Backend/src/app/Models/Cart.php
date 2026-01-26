<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    // Obtenemos la tabla carts
    protected $table = 'carts';

    // Definimos la primary key
    protected $primaryKey = 'id_cart';

    // Definimos los campos que se pueden llenar
    protected $fillable = ['id_user'];

    // Función para obtener los productos de cada carrito
    public function items()
    {
        return $this->hasMany(CartItem::class, 'id_cart', 'id_cart');
    }

    // Función para obtener el dueño del carrito
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
