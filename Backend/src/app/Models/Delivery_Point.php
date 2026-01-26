<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery_Point extends Model
{
    // Obtenemos la tabla delivery_points
    protected $table = 'delivery_points';

    // Marcamos los timestamps como falso
    public $timestamps = false;

    // Definimos la primary key
    protected $primaryKey = 'id_delivery_point';

    // Definimos los campos que se pueden llenar
    protected $fillable = [
        'id_user',
        'name',
        'direction',
        'latitude',
        'length',
    ];

    // Definimos los campos que no se van a ver
    protected $hidden = [
        'id_user',
    ];

    // Función para obtener los productos de los delivery points
    public function products()
    {
        return $this->hasMany(Product::class, 'id_delivery_point', 'id_delivery_point');
    }

    // Función para obtener el usuario al que pertenece el delivery point
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
