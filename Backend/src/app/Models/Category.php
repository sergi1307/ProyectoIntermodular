<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Obtenemos la tabla categories
    protected $table = 'categories';

    // Definimos la primary key
    protected $primaryKey = 'id_category';

    // Definimos los campos que se pueden llenar
    protected $fillable = [
        'name',
    ];

    // Marcamos los timestamps en falso
    public $timestamps = false;

    // Obtenemos los productos que tienen esa categoría
    public function products()
    {
        return $this->hasMany(Product::class, 'id_category', 'id_category');
    }
}
