<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Message extends Model
{
    // Anulamos los timestamps
    public $timestamps = false;

    // Obtenemos la table messages
    protected $table = 'messages';

    protected $primaryKey = 'id_message';

    // Definimos la tabla que se pueden llenar
    protected $fillable = [
        'id_product',
        'id_transmitter',
        'id_receiver',
        'content',
        'shipping_date'
    ];


    // Definimos los campos que no son textos
    protected $casts = [
        'shipping_date' => 'date'
    ];

    // Relaciones de mensajes

    // Función para obtener un producto
    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product', 'id_product');
    }

    // Función para obtener el emisor
    public function transmitter()
    {
        return $this->belongsTo(User::class, 'id_transmitter');
    }

    // Función para obtener el receptor
    public function receiver()
    {
        return $this->belongsTo(User::class, 'id_receiver');
    }
}