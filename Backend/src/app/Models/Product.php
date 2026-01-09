<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Delivery_Point;
use App\Models\Category;

class Product extends Model
{
    protected $table = 'products';
    //HE TENID QUE ÑADIR ESTA LINEA SINO NO PODIA CREAR UN PRODUCTO PARA PROBAR EL MAPA Y NO ME DEJABA
    public $timestamps = false;

    protected $primaryKey = 'id_product';
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'image',
        'type_stock',
        'state',
        'publication_date'
    ];

    protected $hidden = [
        'id_user',
        'id_delivery_point',
        'id_category'
    ];
}
