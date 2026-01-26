<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Delivery_Point;
use App\Models\Category;

class Product extends Model
{
    // Obtenemos la tabla products
    protected $table = 'products';

    // Marcamos los timestamps como falso
    public $timestamps = false;

    // Definimos la primary key
    protected $primaryKey = 'id_product';

    // Definimos los campos que se pueden llenar
    protected $fillable = [
        'id_user',
        'id_delivery_point',
        'id_category',
        'name',
        'description',
        'price',
        'stock',
        'image',
        'type_stock',
        'state',
        'publication_date'
    ];

    // Definimos los campos ocultos
    protected $hidden = [
        'id_user',
        'id_delivery_point',
        'id_category',
    ];

    // Definimos los campos que no son texto
    protected $casts = [
        'price' => 'double',
    ];

    // Función para obtener el usuario al que pertenece el producto
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // Función para obtener el delivery point al que pertenece ese producto
    public function delivery_point()
    {
        return $this->belongsTo(Delivery_Point::class, 'id_delivery_point', 'id_delivery_point');
    }

    // Función para obtener la venta si hay alguna de este producto
    public function sale()
    {
        return $this->hasOne(Sale::class, 'id_product', 'id_product');
    }

    // Función para obtener la categoría que tiene el producto
    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category', 'id_category');
    }
}
