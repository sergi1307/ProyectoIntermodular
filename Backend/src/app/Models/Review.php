<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sale;

class Review extends Model
{
    // Obtenemos la tabla reviews
    protected $table = 'reviews';

    // Marcamos los timestamps en falso
    public $timestamps = false;

    // Definimos la primary key
    protected $primaryKey = 'id_review';

    // Definimos los campos que se pueden llenar
    protected $fillable = [
        'id_sale',
        'calification',
        'comment',
        'review_date',
    ];

    // Marcamos los campos ocultos
    protected $hidden = [
        'id_sale',
    ];

    // Convertir calification a float automáticamente
    protected $casts = [
        'calification' => 'float',
    ];

    // Función para obtener la venta de la review
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'id_sale', 'id_sale');
    }
}
