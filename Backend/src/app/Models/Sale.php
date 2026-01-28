<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sale extends Model
{
    // Obtenemos la tabla sales
    protected $table = 'sales';

    // Definimos la primary key
    protected $primaryKey = 'id_sale';

    // Marcamos los timestamps en falso
    public $timestamps = false;

    // Definimos los campos que se pueden llenar
    protected $fillable = [
        'id_product',
        'id_buyer',
        'id_seller',
        'id_delivery_point',
        'sale_date',
        'quantity',
        'total',
        'collection_date',
        'state'
    ];

    // Marcamos los campos ocultos
    protected $hidden = [
        'id_product',
        'id_buyer',
        'id_seller',
        'id_delivery_point',
        'id_review',
    ];

    // Definimos los campos que no son texto
    protected $casts = [
        'sale_date' => 'date',
        'collection_date' => 'date',
    ];

    // Función para obtener el producto de la venta
    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product', 'id_product');
    }

    // Función para obtener el comprador
    public function buyer()
    {
        return $this->belongsTo(User::class, 'id_buyer', 'id_user');
    }

    // Función para obtener el vendedor
    public function seller()
    {
        return $this->belongsTo(User::class, 'id_seller', 'id_user');
    }

    // Función para obtener el delivery point de la venta
    public function delivery_point()
    {
        return $this->belongsTo(Delivery_Point::class, 'id_delivery_point', 'id_delivery_point');
    }

    // Función para obtener las reviews
    public function reviews()
    {
        return $this->hasMany(Review::class, 'id_sale', 'id_sale');
    }
}
